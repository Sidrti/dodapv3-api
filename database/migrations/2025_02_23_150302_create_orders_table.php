<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null'); // User who placed the order
            $table->foreignId('service_id')->constrained('services')->onDelete('cascade'); // Ordered service
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email');
            $table->string('mobile_number');
            $table->text('street_address');
            $table->text('unit_address');
            $table->decimal('total_amount', 10, 2);
            $table->decimal('fixed_charge', 10, 2)->default(0);
            $table->string('payment_status')->default('pending'); // pending, completed, failed
            $table->string('transaction_id')->nullable();
            $table->date('appointment_date'); 
            $table->string('time_slot');
            // $table->string('referral_code')->nullable(); // If used
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('orders');
    }
};
