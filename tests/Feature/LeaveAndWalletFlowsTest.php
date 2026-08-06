<?php

namespace Tests\Feature;

use App\Models\Cart;
use App\Models\LeaveRequest;
use App\Models\Product;
use App\Models\User;
use App\Models\Wallet;
use App\Notifications\InsufficientWalletFundsNotification;
use App\Notifications\LeaveRequestSubmittedNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class LeaveAndWalletFlowsTest extends TestCase
{
    use RefreshDatabase;

    public function test_teacher_can_submit_leave_request_with_private_document(): void
    {
        Notification::fake();
        Storage::fake();

        $teacher = User::factory()->create(['role' => 'Teacher']);
        $admin = User::factory()->create(['role' => 'Admin']);

        $response = $this->actingAs($teacher)->post(route('leave.requests.store'), [
            'leave_type' => 'Medical',
            'start_date' => now()->addDay()->toDateString(),
            'end_date' => now()->addDays(2)->toDateString(),
            'reason' => 'Medical appointment',
            'document' => UploadedFile::fake()->create('doctor-note.pdf', 100, 'application/pdf'),
        ]);

        $response->assertRedirect(route('leave.requests.index'));

        $leaveRequest = LeaveRequest::firstOrFail();

        $this->assertSame($teacher->id, $leaveRequest->teacher_id);
        $this->assertSame('pending', $leaveRequest->status);
        $this->assertNotNull($leaveRequest->document_path);
        Storage::assertExists($leaveRequest->document_path);
        Notification::assertSentTo($admin, LeaveRequestSubmittedNotification::class);
    }

    public function test_shop_wallet_sale_does_not_complete_when_student_balance_is_insufficient(): void
    {
        Notification::fake();

        $attendant = User::factory()->create(['role' => 'Admin']);
        $student = User::factory()->create(['role' => 'Student', 'id_no' => 'STD-001']);
        $shopManager = User::factory()->create(['role' => 'Shop Manager']);

        $product = Product::create([
            'name' => 'Notebook',
            'price' => 1000,
            'stock_quantity' => 10,
            'created_by' => $attendant->id,
            'status' => 'active',
        ]);

        Cart::create([
            'user_id' => $attendant->id,
            'product_id' => $product->id,
            'quantity' => 2,
        ]);

        Wallet::create([
            'user_id' => $student->id,
            'balance' => 500,
            'role' => 'student',
            'status' => 'active',
        ]);

        $response = $this->actingAs($attendant)->post(route('orders.store'), [
            'payment_method' => 'student_wallet',
            'student_identifier' => 'STD-001',
        ]);

        $response->assertSessionHas('alert-type', 'error');

        $this->assertDatabaseMissing('orders', ['payment_method' => 'student_wallet']);
        $this->assertSame('500.00', Wallet::where('user_id', $student->id)->first()->balance);
        Notification::assertSentTo($shopManager, InsufficientWalletFundsNotification::class);
    }
}
