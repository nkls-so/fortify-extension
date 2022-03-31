<?php

namespace Nkls\FortifyExtension;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Laravel\Fortify\Contracts\TwoFactorAuthenticationProvider;
use Laravel\Fortify\Features;
use Nkls\FortifyExtension\Enums\TwoFactorChannel;
use Nkls\FortifyExtension\Notifications\TOTPCode;

class FortifyExtension
{
    /**
     * Indicates if Fortify routes will be registered.
     *
     * @var bool
     */
    public static $registersRoutes = true;

    /**
     * Determine if Fortify is using additional two factor authentication channels.
     */
    public static function useAdditionalTwoFactorChannels(): bool
    {
        return Features::enabled(Features::twoFactorAuthentication())
               && Features::optionEnabled(Features::twoFactorAuthentication(), 'useAdditionalChannels');
    }

    /**
     * Determine if Fortify is using webauthn.
     */
    public static function useWebAuthN(): bool
    {
        return Features::enabled(Features::twoFactorAuthentication())
               && Features::optionEnabled(Features::twoFactorAuthentication(), 'useWebAuthN');
    }

    /**
     * Send a new totp notification.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function sendTOTPNotification(User $user)
    {
        if (Auth::check()) {
            return new JsonResponse('', Response::HTTP_NO_CONTENT);
        }

        if (TwoFactorChannel::TOTP_EMAIL === $user->two_factor_channel
            || TwoFactorChannel::TOTP_SMS === $user->two_factor_channel)
        {
            $totp = app(TwoFactorAuthenticationProvider::class)->getCurrentOtp(decrypt($user->two_factor_secret));
            $user->notify(new TOTPCode($totp));
        }

        return new JsonResponse('', Response::HTTP_ACCEPTED);
    }

    /**
     * Configure FortifyExtension to not register its routes.
     *
     * @return static
     */
    public static function ignoreRoutes()
    {
        static::$registersRoutes = false;

        return new static;
    }
}
