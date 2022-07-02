<?php
/**
 * Created by PhpStorm.
 * User: tony
 * Date: 6/30/18
 * Time: 11:37 PM
 */

namespace A2nt\CMSNiceties\Extensions;

use SilverStripe\Control\HTTPResponse;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\TextField;
use SilverStripe\ORM\DataExtension;
use SilverStripe\ORM\FieldType\DBField;
use SilverStripe\Security\MemberAuthenticator\LostPasswordHandler;

class LostPasswordHandlerExtension extends LostPasswordHandler
{
    private static $url_handlers = [
        'passwordsent' => 'passwordsent',
    ];

    private static $allowed_actions = [
        'passwordsent',
    ];

    /**
     * Show the "password sent" page, after a user has requested
     * to reset their password.
     *
     * @return array
     */
    public function passwordsent()
    {
        $message = _t(
            'SilverStripe\\Security\\Security.PASSWORDRESETSENTTEXT',
            "Thank you. A reset link has been sent, provided an account exists for this email address."
        );

        $email = $this->getRequest()->getVar('email');
        $message = $email
            ? 'Thank you! A reset link has been sent to \''.$email.'\', provided an account exists for this email address.'
            : $message;

        $fragment = DBField::create_field('HTMLFragment', "<p>$message</p>");

        return [
            'Title' => _t(
                'SilverStripe\\Security\\Security.PASSWORDRESETSENTHEADER',
                "Password reset link sent".($email ? ' to \''.$email.'\'' : '')
            ),
            'ElementalArea' => $fragment,
            'Content' => $fragment,
        ];
    }

    /**
     * Avoid information disclosure by displaying the same status, regardless wether the email address actually exists
     *
     * @param array $data
     * @return HTTPResponse
     */
    protected function redirectToSuccess(array $data)
    {
        $link = $this->link('passwordsent').'?email='.$data['Email'];

        return $this->redirect($this->addBackURLParam($link));
    }
}
