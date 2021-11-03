<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersReferralsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(
            'users_referrals',
            function (Blueprint $table) {
                $table->unsignedBigInteger('user_id');
                $table->unsignedBigInteger('referral_id');
                $table->unsignedBigInteger('level_id');
                $table->timestamp('created_at')->useCurrent();
                $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable();

                $table->foreign('user_id')->references('id')->on('users');
                $table->foreign('referral_id')->references('id')->on('users');
                $table->foreign('level_id')->references('id')->on('referral_levels');

                $table->unique(['user_id', 'referral_id']);
            }
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users_referrals', function (Blueprint $table) {
            $table->dropForeign('users_referrals_user_id_foreign');
            $table->dropForeign('users_referrals_referral_id_foreign');
            $table->dropForeign('users_referrals_level_id_foreign');
            $table->dropIfExists();
        });
    }
}
