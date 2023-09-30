<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAdjustmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('adjustments', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('creator_id')->comment('User who inserted data');
            $table->unsignedInteger('warehouse_id');
            $table->unsignedInteger('product_id');
            $table->string('action')->nullable();
            $table->date('adjustment_date')->nullable();
            $table->integer('quantity')->nullable();
            $table->string('reference_no')->nullable();
            $table->string('document')->nullable();
            $table->text('reason')->nullable();
            $table->timestamps();
            $table->foreign('product_id')->references('id')->on('products');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('adjustments');
    }
}
