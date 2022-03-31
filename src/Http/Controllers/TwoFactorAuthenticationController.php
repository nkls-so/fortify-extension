<?php

declare(strict_types=1);

namespace Nkls\FortifyExtension\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Laravel\Fortify\Actions\EnableTwoFactorAuthentication;
use Laravel\Fortify\Http\Controllers\TwoFactorAuthenticationController as FortifyTwoFactorAuthenticationController;

class TwoFactorAuthenticationController extends FortifyTwoFactorAuthenticationController
{
    /**
     * Enable two factor authentication for the user.
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function store(Request $request, EnableTwoFactorAuthentication $enable)
    {
        $enable($request);

        return $request->wantsJson()
                    ? new JsonResponse('', 200)
                    : back()->with('status', 'two-factor-authentication-enabled');
    }
}
