<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('wallet_from')->index();
            $table->unsignedBigInteger('wallet_to')->index();
            $table->string('value', 30);
            $table->unsignedBigInteger('type_id');
            $table->unsignedBigInteger('status_id');
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable();

            $table->foreign('wallet_from')->references('id')->on('wallets');
            $table->foreign('wallet_to')->references('id')->on('wallets');
            $table->foreign('type_id')->references('id')->on('transaction_types');
            $table->foreign('status_id')->references('id')->on('transaction_statuses');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropForeign('transactions_wallet_from_foreign');
            $table->dropForeign('transactions_wallet_to_foreign');
            $table->dropForeign('transactions_type_id_foreign');
            $table->dropForeign('transactions_status_id_foreign');
            $table->dropIfExists();
        });
    }
}
