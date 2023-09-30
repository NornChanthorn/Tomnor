<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePurchasesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchases', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('creator_id')->comment('User who inserted data');
            $table->unsignedInteger('warehouse_id');
            $table->string('reference_no')->nullable();
            $table->date('purchase_date')->nullable();
            $table->integer('total_quantity')->nullable();
            $table->double('total_cost')->nullable();
            $table->double('total_price')->nullable();
            $table->double('total_discount')->nullable();
            $table->double('total_tax')->nullable();
            $table->double('shipping_cost')->nullable();
            $table->double('grand_total')->nullable();
            $table->double('paid_amount')->nullable();
            $table->string('document')->nullable();
            $table->text('note')->nullable();
            $table->string('purchase_status')->nullable();
            $table->string('payment_status')->nullable();
            $table->timestamps();

            $table->foreign('creator_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('purchases');
    }
}
