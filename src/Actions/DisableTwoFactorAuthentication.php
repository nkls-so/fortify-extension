<?php

declare(strict_types=1);

namespace Nkls\FortifyExtension\Actions;

use Laravel\Fortify\Actions\DisableTwoFactorAuthentication as FortifyDisableTwoFactorAuthentication;
use Laravel\Fortify\Events\TwoFactorAuthenticationDisabled;
use Laravel\Fortify\Fortify;

class DisableTwoFactorAuthentication extends FortifyDisableTwoFactorAuthentication
{
    /**
     * Disable two factor authentication for the user.
     *
     * @param mixed $user
     *
     * @return void
     */
    public function __invoke($user)
    {
        $user->forceFill([
            'two_factor_channel' => null,
            'two_factor_secret' => null,
            'two_factor_recovery_codes' => null,
        ] + (Fortify::confirmsTwoFactorAuthentication() ? [
            'two_factor_confirmed_at' => null,
        ] : []))->save();

        TwoFactorAuthenticationDisabled::dispatch($user);
    }
}
