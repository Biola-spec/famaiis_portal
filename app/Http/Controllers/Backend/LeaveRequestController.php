<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\LeaveRequest;
use App\Models\User;
use App\Notifications\LeaveRequestStatusNotification;
use App\Notifications\LeaveRequestSubmittedNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;

class LeaveRequestController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $leaveRequests = LeaveRequest::with(['teacher', 'reviewer'])
            ->when(!$user->hasRole('Admin'), fn ($query) => $query->where('teacher_id', $user->id))
            ->latest()
            ->paginate(20);

        return view('backend.leave_requests.index', compact('leaveRequests'));
    }

    public function create()
    {
        abort_unless(Auth::user()->hasRole('Teacher', 'Staff'), 403);

        return view('backend.leave_requests.create');
    }

    public function store(Request $request)
    {
        abort_unless(Auth::user()->hasRole('Teacher', 'Staff'), 403);

        $validated = $request->validate([
            'leave_type' => ['required', 'string', 'max:100'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'reason' => ['required', 'string', 'max:5000'],
            'document' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png,webp', 'max:5120'],
        ]);

        $documentPath = $request->file('document')?->store('leave_documents');

        $leaveRequest = LeaveRequest::create([
            'teacher_id' => Auth::id(),
            'leave_type' => $validated['leave_type'],
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'reason' => $validated['reason'],
            'document_path' => $documentPath,
        ]);

        $admins = User::query()
            ->whereHas('roles', fn ($query) => $query->whereIn('name', ['Admin', 'Super Admin']))
            ->orWhereIn('role', ['Admin', 'Super Admin'])
            ->get();

        Notification::send($admins, new LeaveRequestSubmittedNotification($leaveRequest->load('teacher')));

        return redirect()->route('leave.requests.index')->with([
            'message' => 'Leave request submitted successfully.',
            'alert-type' => 'success',
        ]);
    }

    public function updateStatus(Request $request, LeaveRequest $leaveRequest)
    {
        abort_unless(Auth::user()->hasRole('Admin'), 403);

        $validated = $request->validate([
            'status' => ['required', 'in:approved,rejected'],
            'admin_comment' => ['nullable', 'string', 'max:2000'],
        ]);

        if ($validated['status'] === 'rejected' && empty($validated['admin_comment'])) {
            return back()->with([
                'message' => 'Please add a rejection comment.',
                'alert-type' => 'error',
            ]);
        }

        $leaveRequest->update([
            'status' => $validated['status'],
            'admin_comment' => $validated['admin_comment'] ?? null,
            'reviewed_by' => Auth::id(),
            'reviewed_at' => now(),
        ]);

        $leaveRequest->teacher->notify(new LeaveRequestStatusNotification($leaveRequest));

        return back()->with([
            'message' => 'Leave request ' . $validated['status'] . ' successfully.',
            'alert-type' => 'success',
        ]);
    }

    public function cancel(LeaveRequest $leaveRequest)
    {
        abort_unless((int) $leaveRequest->teacher_id === (int) Auth::id(), 403);

        if ($leaveRequest->status !== 'pending') {
            return back()->with([
                'message' => 'Only pending leave requests can be cancelled.',
                'alert-type' => 'error',
            ]);
        }

        $leaveRequest->update(['status' => 'cancelled']);

        return back()->with([
            'message' => 'Leave request cancelled successfully.',
            'alert-type' => 'success',
        ]);
    }

    public function download(LeaveRequest $leaveRequest)
    {
        $user = Auth::user();

        abort_unless($user->hasRole('Admin') || (int) $leaveRequest->teacher_id === (int) $user->id, 403);
        abort_unless($leaveRequest->document_path && Storage::exists($leaveRequest->document_path), 404);

        return Storage::download($leaveRequest->document_path);
    }
}
