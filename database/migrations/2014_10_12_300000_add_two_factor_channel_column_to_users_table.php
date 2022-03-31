<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Nkls\FortifyExtension\FortifyExtension;

return new class () extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            if (FortifyExtension::useAdditionalTwoFactorChannels()) {
                $table->string('two_factor_channel')
                    ->after('password')
                    ->nullable()
                ;

                if (!Schema::hasColumn('users', 'phone')) {
                    $table->integer('phone')
                        ->after('password')
                        ->nullable()
                    ;
                }
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(FortifyExtension::useAdditionalTwoFactorChannels() ? [
                'phone',
                'two_factor_channel',
            ] : []);
        });
    }
};
