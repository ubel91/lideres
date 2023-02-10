<?php


namespace App\Service;


class TranslationSecurityService
{
    const INVALIDRESETPASSWORD = 'The reset password link is invalid. Please try to reset your password again.';
    const FAKEREPOSITORY = 'Please update the request_password_repository configuration in config/packages/reset_password.yaml to point to your "request password repository` service.';
    const EXPIREDRESETPASS = 'The link in your email is expired. Please try to reset your password again.';
    const TOOMANYPASSREQUEST = 'You have already requested a reset password email. Please check your email or try again soon.';

    public function translateMessage(string $message): string
    {
        switch ($message){
            case self::INVALIDRESETPASSWORD:
                return 'El enlace para recuperar su contraseña no es válido';
                break;
            case self::EXPIREDRESETPASS:
                return 'El enlace enviado a su buzón de correo ha expirado. Por favor intente reiniciar su contraseña otra vez solicitando un nuevo correo.';
                break;
            case self::TOOMANYPASSREQUEST:
                return 'Usted ha enviado una solictud para recuperar su constraseña anteriormente. Por favor revise su buzón de correo o intente de nuevo en unos instantes';
                break;
            case self::FAKEREPOSITORY:
                return 'Please update the request_password_repository configuration in config/packages/reset_password.yaml to point to your "request password repository` service.';
            default:
                return '';
        }
    }

}