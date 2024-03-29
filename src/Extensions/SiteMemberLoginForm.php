<?php

namespace A2nt\CMSNiceties\Extensions;

use SilverStripe\Control\Director;
use SilverStripe\Security\MemberAuthenticator\MemberLoginForm;
use SilverStripe\View\Requirements;

class SiteMemberLoginForm extends MemberLoginForm
{
    private static $enable_captcha = true;

    public function __construct(
        $controller,
        $authenticatorClass,
        $name,
        $fields = null,
        $actions = null,
        $checkCurrentUser = true
    ) {
        parent::__construct($controller, $authenticatorClass, $name, $fields, $actions, $checkCurrentUser);

        $fields = $this->Fields();
        $actions = $this->Actions();

        Requirements::customScript('const pwd = document.querySelector(".field-password__show-password");if(pwd){pwd.addEventListener("click",function(e){e.preventDefault();var p = document.querySelector(\'[name="Password"]\');if(p.getAttribute("type")==="password"){var attr="text";}else{var attr="password"}p.setAttribute("type", attr);});}');
        $email = $fields->fieldByName('Email');
        if ($email) {
            $email
                ->setAttribute('placeholder', 'your@email.com')
                ->setAttribute('autocomplete', 'email')
                ->setAttribute('type', 'email');
        }

        $pass = $fields->fieldByName('Password');
        if ($pass) {
            //$pass->setAttribute('autocomplete', 'current-password');
            $pass->setAttribute('placeholder', '**********');
            $pass->setAutofocus(true);
        }

        $btn = $actions->fieldByName('action_doLogin');
        if ($btn) {
            $btn->setUseButtonTag(true);
            $btn->setButtonContent('<i class="fas fa-check"></i> '.$btn->Title());
            $btn->addExtraClass('btn-lg');
        }

        if (self::config()->get('enable_captcha') && Director::isLive()) {
            $this->enableSpamProtection();
        }
    }
}
