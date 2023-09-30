<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLoansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('loans', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('client_id');
            $table->unsignedInteger('staff_id');
            $table->unsignedInteger('branch_id')->nullable();
            $table->unsignedInteger('product_id');
            $table->double('product_price')->nullable();
            $table->string('product_ime')->nullable();
            $table->string('account_number')->nullable();
            $table->string('account_number_append')->nullable();
            $table->double('loan_amount');
            $table->double('depreciation_amount')->nullable();
            $table->double('down_payment_amount')->nullable();
            $table->double('extra_fee')->nullable();
            $table->float('interest_rate')->nullable();
            $table->integer('installment')->nullable();
            $table->integer('frequency')->nullable();
            $table->date('loan_start_date')->nullable();
            $table->integer('payment_per_month')->nullable();
            $table->date('first_payment_date')->nullable();
            $table->date('second_payment_date')->nullable();
            $table->string('wing_code')->nullable();
            $table->string('status');
            $table->tinyInteger('changed_by')->nullable();
            $table->text('note')->nullable();
            $table->date('approved_date')->nullable();
            $table->date('disbursed_date')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->foreign('user_id')->references('id')->on('users')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('client_id')->references('id')->on('clients')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('staff_id')->references('id')->on('staff')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('products')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('loans');
    }
}
