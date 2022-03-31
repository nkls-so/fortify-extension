<?php

use Nkls\FortifyExtension\FortifyExtension;

Route::group(['middleware' => config('fortify.middleware', ['web'])], function () {
    Route::post('/user/two-factor-code', [FortifyExtension::class, 'sendTOTPNotification'])
        ->middleware([config('fortify.auth_middleware', 'auth').':'.config('fortify.guard'), 'throttle:api'])
        ->name('totp.send');
});
