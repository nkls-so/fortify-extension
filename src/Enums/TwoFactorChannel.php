<?php

declare(strict_types=1);

namespace Nkls\FortifyExtension\Enums;

enum TwoFactorChannel: string
{
    case TOTP_APP = 'TOTP_APP';

    case TOTP_SMS = 'TOTP_SMS';

    case TOTP_EMAIL = 'TOTP_EMAIL';

    case WEBAUTHN = 'WEBAUTHN';
}
