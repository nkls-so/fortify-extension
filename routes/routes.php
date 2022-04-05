<?php

use Laravel\Fortify\Features;
use Nkls\FortifyExtension\FortifyExtension;

Route::group(['middleware' => config('fortify.middleware', ['web'])], function () {

    $twoFactorLimiter = config('fortify.limiters.two-factor');

    if (Features::enabled(Features::twoFactorAuthentication())) {
        Route::post('/user/two-factor-code', [FortifyExtension::class, 'sendTOTPNotification'])
            ->middleware(array_filter([
                'guest:'.config('fortify.guard'),
                $twoFactorLimiter ? 'throttle:'.$twoFactorLimiter : null,
            ]))
            ->name('two-factor.send')
        ;
    }
});
