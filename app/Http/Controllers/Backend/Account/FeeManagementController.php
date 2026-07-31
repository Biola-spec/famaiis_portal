<?php

namespace App\Http\Controllers\Backend\Account;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\FeeType;
use App\Models\FeeStructure;
use App\Models\FeeItem;
use App\Models\StudentFee;
use App\Models\FeePayment;
use App\Models\SchoolSection;
use App\Models\StudentClass;
use App\Models\StudentYear;
use App\Models\Term;
use App\Models\User;

class FeeManagementController extends Controller
{
    // ─── FEE TYPES ──────────────────────────────────────────────────────────────

    public function feeTypes()
    {
        $data['feeTypes'] = FeeType::withCount('feeItems')->get();
        return view('backend.fees.fee_types', $data);
    }

    public function storeFeeType(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:100|unique:fee_types,name',
            'category' => 'required|in:mandatory,optional,one-time,recurring',
        ]);

        FeeType::create($request->only('name', 'category'));

        return back()->with('success', 'Fee type created successfully.');
    }

    public function updateFeeType(Request $request, $id)
    {
        $feeType = FeeType::findOrFail($id);
        $request->validate([
            'name'     => 'required|string|max:100|unique:fee_types,name,' . $id,
            'category' => 'required|in:mandatory,optional,one-time,recurring',
        ]);

        $feeType->update($request->only('name', 'category'));

        return back()->with('success', 'Fee type updated successfully.');
    }

    public function deleteFeeType($id)
    {
        $feeType = FeeType::findOrFail($id);
        if ($feeType->feeItems()->exists()) {
            return back()->with('error', 'Cannot delete: fee type is in use by existing fee structures.');
        }
        $feeType->delete();
        return back()->with('success', 'Fee type deleted.');
    }

    // ─── FEE STRUCTURES ─────────────────────────────────────────────────────────

    public function feeStructures()
    {
        $data['structures'] = FeeStructure::with(['section', 'studentClass', 'term', 'year', 'feeItems.feeType'])->get();
        $data['sections']   = SchoolSection::all();
        $data['classes']    = StudentClass::all();
        $data['years']      = StudentYear::all();
        $data['terms']      = Term::all();
        $data['feeTypes']   = FeeType::all();
        return view('backend.fees.fee_structures', $data);
    }

    public function storeFeeStructure(Request $request)
    {
        $request->validate([
            'section_id'  => 'required|exists:school_sections,id',
            'class_id'    => 'nullable|exists:student_classes,id',
            'term_id'     => 'nullable|exists:terms,id',
            'year_id'     => 'required|exists:student_years,id',
            'fee_type_ids'   => 'required|array|min:1',
            'fee_type_ids.*' => 'exists:fee_types,id',
            'amounts'        => 'required|array|min:1',
            'amounts.*'      => 'required|numeric|min:0',
        ]);

        $structure = FeeStructure::create([
            'section_id' => $request->section_id,
            'class_id'   => $request->class_id,
            'term_id'    => $request->term_id,
            'year_id'    => $request->year_id,
            'total_amount' => 0,
        ]);

        $total = 0;
        foreach ($request->fee_type_ids as $i => $typeId) {
            $amount = $request->amounts[$i] ?? 0;
            FeeItem::create([
                'fee_structure_id' => $structure->id,
                'fee_type_id'      => $typeId,
                'amount'           => $amount,
            ]);
            $total += $amount;
        }

        $structure->update(['total_amount' => $total]);

        return redirect()->route('fee.structures')->with('success', 'Fee structure created successfully.');
    }

    public function editFeeStructure($id)
    {
        $data['structure'] = FeeStructure::with('feeItems.feeType')->findOrFail($id);
        $data['sections']  = SchoolSection::all();
        $data['classes']   = StudentClass::all();
        $data['years']     = StudentYear::all();
        $data['terms']     = Term::all();
        $data['feeTypes']  = FeeType::all();
        return view('backend.fees.edit_fee_structure', $data);
    }

    public function updateFeeStructure(Request $request, $id)
    {
        $structure = FeeStructure::findOrFail($id);

        $request->validate([
            'section_id'     => 'required|exists:school_sections,id',
            'class_id'       => 'nullable|exists:student_classes,id',
            'term_id'        => 'nullable|exists:terms,id',
            'year_id'        => 'required|exists:student_years,id',
            'fee_type_ids'   => 'required|array|min:1',
            'fee_type_ids.*' => 'exists:fee_types,id',
            'amounts'        => 'required|array|min:1',
            'amounts.*'      => 'required|numeric|min:0',
        ]);

        $structure->update([
            'section_id' => $request->section_id,
            'class_id'   => $request->class_id,
            'term_id'    => $request->term_id,
            'year_id'    => $request->year_id,
        ]);

        // Replace fee items
        $structure->feeItems()->delete();
        $total = 0;
        foreach ($request->fee_type_ids as $i => $typeId) {
            $amount = $request->amounts[$i] ?? 0;
            FeeItem::create([
                'fee_structure_id' => $structure->id,
                'fee_type_id'      => $typeId,
                'amount'           => $amount,
            ]);
            $total += $amount;
        }
        $structure->update(['total_amount' => $total]);

        return redirect()->route('fee.structures')->with('success', 'Fee structure updated.');
    }

    public function deleteFeeStructure($id)
    {
        $structure = FeeStructure::findOrFail($id);
        if ($structure->studentFees()->exists()) {
            return back()->with('error', 'Cannot delete: students are already assigned to this structure.');
        }
        $structure->delete();
        return back()->with('success', 'Fee structure deleted.');
    }

    // ─── ASSIGN FEES TO STUDENT ─────────────────────────────────────────────────

    public function assignFees()
    {
        $data['sections']    = SchoolSection::with('feeStructures')->get();
        $data['students']    = User::where('usertype', 'Student')->orWhere('role', 'Student')->get();
        $data['studentFees'] = StudentFee::with(['student', 'section', 'feeStructure'])->latest()->paginate(20);
        return view('backend.fees.assign_fees', $data);
    }

    public function storeAssignFee(Request $request)
    {
        $request->validate([
            'student_id'       => 'required|exists:users,id',
            'fee_structure_id' => 'required|exists:fee_structures,id',
        ]);

        $structure = FeeStructure::findOrFail($request->fee_structure_id);

        // Avoid duplicate assignments
        $existing = StudentFee::where('student_id', $request->student_id)
            ->where('section_id', $structure->section_id)
            ->where('fee_structure_id', $structure->id)
            ->first();

        if ($existing) {
            return back()->with('error', 'Student is already assigned this fee structure.');
        }

        StudentFee::create([
            'student_id'       => $request->student_id,
            'section_id'       => $structure->section_id,
            'fee_structure_id' => $structure->id,
            'total_due'        => $structure->total_amount,
            'total_paid'       => 0,
            'balance'          => $structure->total_amount,
        ]);

        return back()->with('success', 'Fee assigned to student successfully.');
    }

    // ─── PAYMENTS ────────────────────────────────────────────────────────────────

    public function payments()
    {
        $data['payments']  = FeePayment::with(['student', 'section', 'feeStructure.term', 'feeStructure.year'])->latest()->paginate(25);
        $data['sections']  = SchoolSection::all();
        $data['students']  = User::where('usertype', 'Student')->orWhere('role', 'Student')->get();
        $data['studentFees'] = StudentFee::with(['student', 'section', 'feeStructure'])->get();
        return view('backend.fees.payments', $data);
    }

    public function recordPayment(Request $request)
    {
        $request->validate([
            'student_fee_id'  => 'required|exists:student_fees,id',
            'amount_paid'     => 'required|numeric|min:0.01',
            'payment_date'    => 'required|date',
            'payment_method'  => 'nullable|string|max:50',
        ]);

        $studentFee = StudentFee::findOrFail($request->student_fee_id);

        if ($request->amount_paid > $studentFee->balance) {
            return back()->with('error', 'Payment amount exceeds outstanding balance of ₦' . number_format($studentFee->balance, 2));
        }

        $receiptNo = FeePayment::generateReceiptNo();
        $newBalance = $studentFee->balance - $request->amount_paid;

        FeePayment::create([
            'student_id'       => $studentFee->student_id,
            'section_id'       => $studentFee->section_id,
            'fee_structure_id' => $studentFee->fee_structure_id,
            'amount_paid'      => $request->amount_paid,
            'balance'          => $newBalance,
            'receipt_no'       => $receiptNo,
            'payment_date'     => $request->payment_date,
            'payment_method'   => $request->payment_method,
        ]);

        // Update student fee record
        $studentFee->recalculateBalance();

        return back()->with('success', 'Payment recorded. Receipt: ' . $receiptNo);
    }

    public function paymentReceipt($id)
    {
        $payment = FeePayment::with(['student', 'section', 'feeStructure.feeItems.feeType', 'feeStructure.term', 'feeStructure.year'])->findOrFail($id);
        return view('backend.fees.receipt', compact('payment'));
    }

    // ─── REPORTS ─────────────────────────────────────────────────────────────────

    public function feeReport(Request $request)
    {
        $query = StudentFee::with(['student', 'section', 'feeStructure.term', 'feeStructure.year']);

        if ($request->section_id) {
            $query->where('section_id', $request->section_id);
        }

        $data['studentFees'] = $query->paginate(25);
        $data['sections']    = SchoolSection::all();
        $data['totalDue']    = $query->sum('total_due');
        $data['totalPaid']   = $query->sum('total_paid');
        $data['totalBalance']= $query->sum('balance');

        return view('backend.fees.fee_report', $data);
    }
}
