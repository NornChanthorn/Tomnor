<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTransfersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transfers', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('creator_id')->comment('User who inserted data');
            $table->unsignedInteger('from_warehouse_id');
            $table->unsignedInteger('to_warehouse_id');
            $table->string('reference_no')->nullable();
            $table->date('transfer_date')->nullable();
            $table->integer('total_quantity')->nullable();
            $table->double('total_cost')->nullable();
            $table->double('total_discount')->nullable();
            $table->double('total_tax')->nullable();
            $table->double('shipping_cost')->nullable();
            $table->double('grand_total')->nullable();
            $table->string('document')->nullable();
            $table->text('note')->nullable();
            $table->string('status')->nullable();
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
        Schema::dropIfExists('transfers');
    }
}
