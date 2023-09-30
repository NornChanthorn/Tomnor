<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStockHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stock_histories', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('transaction')->comment('E.g. purchase, sale, transfer, return, adjustment, etc.');
            $table->unsignedBigInteger('transaction_id')->nullable();
            $table->date('transaction_date')->nullable();
            $table->unsignedInteger('warehouse_id');
            $table->unsignedInteger('product_id');
            $table->string('stock_type')->comment('Stock-in or stock-out');
            $table->integer('quantity');
            $table->text('note')->nullable();
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
        Schema::dropIfExists('stock_histories');
    }
}
