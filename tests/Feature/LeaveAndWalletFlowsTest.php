<?php

namespace Tests\Feature;

use App\Models\Cart;
use App\Models\LeaveRequest;
use App\Models\Product;
use App\Models\User;
use App\Models\Wallet;
use App\Notifications\InsufficientWalletFundsNotification;
use App\Notifications\LeaveRequestSubmittedNotification;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class LeaveAndWalletFlowsTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        if (config('database.default') !== 'sqlite' || config('database.connections.sqlite.database') !== ':memory:') {
            $this->markTestSkipped('Run with DB_CONNECTION=sqlite and DB_DATABASE=:memory: to avoid touching local data.');
        }

        config(['app.url' => 'http://localhost']);

        $this->createMinimalSchema();
    }

    protected function tearDown(): void
    {
        foreach (['notifications', 'messages', 'events', 'site_settings', 'academic_settings', 'student_years', 'wallet_transactions', 'wallets', 'order_items', 'orders', 'carts', 'products', 'leave_requests', 'permission_role', 'permissions', 'role_user', 'roles', 'users'] as $table) {
            Schema::dropIfExists($table);
        }

        parent::tearDown();
    }

    public function test_teacher_can_submit_leave_request_with_private_document(): void
    {
        Notification::fake();
        Storage::fake();

        $teacher = User::factory()->create(['role' => 'Teacher']);
        $admin = User::factory()->create(['role' => 'Admin']);

        $response = $this->actingAs($teacher)->post('/leave-requests', [
            'leave_type' => 'Medical',
            'start_date' => now()->addDay()->toDateString(),
            'end_date' => now()->addDays(2)->toDateString(),
            'reason' => 'Medical appointment',
            'document' => UploadedFile::fake()->create('doctor-note.pdf', 100, 'application/pdf'),
        ]);

        $response->assertRedirect('/leave-requests');

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

        $response = $this->actingAs($attendant)->post('/shop/orders/store', [
            'payment_method' => 'student_wallet',
            'student_identifier' => 'STD-001',
        ]);

        $response->assertSessionHas('alert-type', 'error');

        $this->assertDatabaseMissing('orders', ['payment_method' => 'student_wallet']);
        $this->assertEquals(500.00, (float) Wallet::where('user_id', $student->id)->first()->balance);
        Notification::assertSentTo($shopManager, InsufficientWalletFundsNotification::class);
    }

    private function createMinimalSchema(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('email')->nullable();
            $table->string('password')->nullable();
            $table->string('role')->nullable();
            $table->string('usertype')->nullable();
            $table->string('id_no')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });

        Schema::create('role_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('role_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
        });

        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('slug')->nullable();
            $table->timestamps();
        });

        Schema::create('permission_role', function (Blueprint $table) {
            $table->id();
            $table->foreignId('permission_id')->constrained()->cascadeOnDelete();
            $table->foreignId('role_id')->constrained()->cascadeOnDelete();
        });

        Schema::create('leave_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('teacher_id')->constrained('users')->cascadeOnDelete();
            $table->string('leave_type');
            $table->date('start_date');
            $table->date('end_date');
            $table->text('reason');
            $table->string('document_path')->nullable();
            $table->string('status')->default('pending');
            $table->text('admin_comment')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();
        });

        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2);
            $table->integer('stock_quantity')->default(0);
            $table->string('image')->nullable();
            $table->string('category')->nullable();
            $table->string('status')->default('active');
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
        });

        Schema::create('carts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->integer('quantity')->default(1);
            $table->timestamps();
        });

        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('student_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('role_type');
            $table->decimal('total_amount', 10, 2);
            $table->string('status')->default('pending');
            $table->string('payment_reference')->nullable()->unique();
            $table->string('payment_transaction_id')->nullable();
            $table->string('payment_method')->nullable();
            $table->string('payment_provider')->nullable();
            $table->string('payment_status')->default('pending');
            $table->timestamp('paid_at')->nullable();
            $table->string('transfer_reference')->nullable();
            $table->string('transfer_receipt')->nullable();
            $table->timestamp('receipt_submitted_at')->nullable();
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('verified_at')->nullable();
            $table->text('verification_note')->nullable();
            $table->timestamps();
        });

        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->integer('quantity');
            $table->decimal('price', 10, 2);
            $table->timestamps();
        });

        Schema::create('wallets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->decimal('balance', 15, 2)->default(0);
            $table->string('role')->nullable();
            $table->string('status')->default('active');
            $table->timestamps();
        });

        Schema::create('wallet_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('wallet_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->decimal('amount', 15, 2);
            $table->decimal('balance_after', 15, 2)->nullable();
            $table->string('type');
            $table->string('description');
            $table->foreignId('performed_by')->constrained('users');
            $table->json('metadata')->nullable();
            $table->string('reference_type')->nullable();
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->timestamps();
        });

        Schema::create('site_settings', function (Blueprint $table) {
            $table->id();
            $table->string('school_name')->nullable();
            $table->string('logo')->nullable();
            $table->timestamps();
        });

        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable();
            $table->date('event_date')->nullable();
            $table->unsignedBigInteger('section_id')->nullable();
            $table->timestamps();
        });

        Schema::create('student_years', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->boolean('is_active')->default(false);
            $table->timestamps();
        });

        Schema::create('academic_settings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('current_session_id')->nullable();
            $table->timestamps();
        });

        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('receiver_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('seen_at')->nullable();
            $table->timestamps();
        });

        Schema::create('notifications', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('type');
            $table->morphs('notifiable');
            $table->text('data');
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
        });
    }
}
