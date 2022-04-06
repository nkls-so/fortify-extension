<?php

namespace Nkls\FortifyExtension\Actions;

use Illuminate\Support\Collection;
use Laravel\Fortify\Actions\EnableTwoFactorAuthentication as FortifyEnableTwoFactorAuthentication;
use Laravel\Fortify\Events\TwoFactorAuthenticationEnabled;
use Laravel\Fortify\RecoveryCode;
use Nkls\FortifyExtension\Http\Requests\EnableTwoFactorAuthenticationRequest;

class EnableTwoFactorAuthentication extends FortifyEnableTwoFactorAuthentication
{
    /**
     * Enable two factor authentication for the user.
     *
     * @param EnableTwoFactorAuthenticationRequest $request
     *
     * @return void
     */
    public function __invoke(EnableTwoFactorAuthenticationRequest $request = null)
    {
        $validated = $request->validated();
        $user = $request->user();

        $user->forceFill([
            'two_factor_phone' => $validated['phone'] ?? null,
            'two_factor_channel' => $validated['channel'],
            'two_factor_secret' => encrypt($this->provider->generateSecretKey()),
            'two_factor_recovery_codes' => encrypt(json_encode(Collection::times(8, function () {
                return RecoveryCode::generate();
            })->all())),
        ])->save();

        TwoFactorAuthenticationEnabled::dispatch($user);
    }
}
