<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBillsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bills', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('type_id');
            $table->unsignedBigInteger('status_id');
            $table->unsignedBigInteger('sender_wallet_id')->index();
            $table->unsignedBigInteger('recipient_wallet_id')->index();
            $table->string('value', 20);
            $table->timestamp('expires_at');
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable();

            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('type_id')->references('id')->on('bill_types');
            $table->foreign('status_id')->references('id')->on('bill_statuses');
            $table->foreign('sender_wallet_id')->references('id')->on('wallets');
            $table->foreign('recipient_wallet_id')->references('id')->on('wallets');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('bills', function (Blueprint $table){
            $table->dropForeign('transactions_sender_wallet_id_foreign');
            $table->dropForeign('transactions_recipient_wallet_id_foreign');
            $table->dropForeign('transactions_user_id_foreign');
            $table->dropForeign('transactions_type_id_foreign');
            $table->dropForeign('transactions_status_id_foreign');
            $table->dropIfExists();
        });
    }
}
