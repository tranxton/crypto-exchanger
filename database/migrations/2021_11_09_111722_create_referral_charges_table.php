<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReferralChargesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('referral_charges', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('bill_id')->index();
            $table->unsignedBigInteger('currency_id')->index();
            $table->unsignedBigInteger('referral_id')->index();
            $table->unsignedBigInteger('user_id')->index();
            $table->unsignedBigInteger('level_id')->index();
            $table->unsignedBigInteger('status_id');
            $table->string('value', 20);
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable();

            $table->foreign('bill_id')->references('id')->on('bills');
            $table->foreign('currency_id')->references('id')->on('currencies');
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('referral_id')->references('id')->on('users');
            $table->foreign('level_id')->references('id')->on('referral_levels');
            $table->foreign('status_id')->references('id')->on('referral_charge_statuses');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('referral_charges', function (Blueprint $table) {
            $table->dropForeign('referral_charges_bill_id_foreign');
            $table->dropForeign('referral_charges_currency_id_foreign');
            $table->dropForeign('referral_charges_referral_id_foreign');
            $table->dropForeign('referral_charges_user_id_foreign');
            $table->dropForeign('referral_charges_level_id_foreign');
            $table->dropForeign('referral_charges_status_id_foreign');
            $table->dropIfExists();
        });
    }
}
