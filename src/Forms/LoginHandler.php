<?php

namespace A2nt\CMSNiceties\Forms;

use SilverStripe\Control\Director;
use SilverStripe\Control\HTTPRequest;
use SilverStripe\Security\MemberAuthenticator\LoginHandler as MemberAuthenticatorLoginHandler;
use SilverStripe\Security\MemberAuthenticator\MemberLoginForm;

class LoginHandler extends MemberAuthenticatorLoginHandler
{
    private static $allowed_actions = [
        'LoginForm',
    ];

    public function doLogin($data, MemberLoginForm $form, HTTPRequest $request)
    {
        return parent::doLogin($data, $form, $request);
    }

    public function loginForm()
    {
        $form = parent::loginForm();

        if (self::config()->get('enable_captcha') && Director::isLive() && $form->get_protector()) {
            $form->enableSpamProtection();
        }
        $form->addExtraClass('legacy');

        return $form;
    }
}
