<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWalletsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('wallets', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->index();
            $table->unsignedBigInteger('currency_id');
            $table->unsignedBigInteger('type_id')->default(2);
            $table->string('address', 50)->unique();
            $table->string('value', 30);
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable();

            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('currency_id')->references('id')->on('currencies');
            $table->foreign('type_id')->references('id')->on('wallet_types');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('wallets', function (Blueprint $table) {
            $table->dropForeign('wallets_user_id_foreign');
            $table->dropForeign('wallets_currency_id_foreign');
            $table->dropForeign('wallets_type_id_foreign');
            $table->dropIfExists();
        });
    }
}
