<?php

declare(strict_types=1);

namespace Nkls\FortifyExtension;

use Illuminate\Support\Facades\Auth;
use Laravel\Fortify\TwoFactorAuthenticationProvider as FortifyTwoFactorAuthenticationProvider;
use Nkls\FortifyExtension\Enums\TwoFactorChannel;

class TwoFactorAuthenticationProvider extends FortifyTwoFactorAuthenticationProvider
{
    /**
     * Get the current one time password for a key.
     *
     * @param string $secret
     *
     * @return string
     */
    public function getCurrentOtp($secret)
    {
        return $this->engine->getCurrentOtp($secret);
    }

    /**
     * Verify the given code.
     *
     * @param  string  $secret
     * @param  string  $code
     * @return bool
     */
    public function verify($secret, $code)
    {
        $window = null;

        if (Auth::user()->two_factor_channel != TwoFactorChannel::TOTP_APP) {
            $window = config('fortify-extension.verification_window');
        }

        $timestamp = $this->engine->verifyKeyNewer(
            $secret, $code, optional($this->cache)->get($key = 'fortify.2fa_codes.'.md5($code), $window)
        );

        if ($timestamp !== false) {
            optional($this->cache)->put($key, $timestamp, ($this->engine->getWindow() ?: 1) * 60);

            return true;
        }

        return false;
    }
}
