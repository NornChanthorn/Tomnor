<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ModifyTransactionPaymentsTableForContactPayments extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // DB::statement("ALTER TABLE invoices MODIFY COLUMN transaction_id INT(11) UNSIGNED DEFAULT NULL");

        Schema::table('invoices', function (Blueprint $table) {
            $table->integer('payment_for')->after('client_id')->nullable();
            $table->integer('parent_id')->after('payment_for')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('invoices', function (Blueprint $table) {
            //
        });
    }
}
