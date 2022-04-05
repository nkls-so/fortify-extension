<?php

namespace Nkls\FortifyExtension\Http\Requests;

use Laravel\Fortify\Contracts\TwoFactorAuthenticationProvider;
use Laravel\Fortify\Http\Requests\TwoFactorLoginRequest as FortifyTwoFactorLoginRequest;
use Nkls\FortifyExtension\Enums\TwoFactorChannel;

class TwoFactorLoginRequest extends FortifyTwoFactorLoginRequest
{
    /**
     * Determine if the request has a valid two factor code.
     *
     * @return bool
     */
    public function hasValidCode()
    {
        $window = null;
        if ($this->challengedUser()->two_factor_channel != TwoFactorChannel::TOTP_APP) {
            $window = config('fortify-extension.verification_window');
        }

        return $this->code && tap(app(TwoFactorAuthenticationProvider::class)->verify(
            decrypt($this->challengedUser()->two_factor_secret), $this->code, $window
        ), function ($result) {
            if ($result) {
                $this->session()->forget('login.id');
            }
        });
    }
}
