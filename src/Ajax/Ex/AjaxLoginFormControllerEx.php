<?php

namespace A2nt\CMSNiceties\Ajax\Ex;

use SilverStripe\Core\Extension;
use SilverStripe\Core\Injector\Injector;
use SilverStripe\Security\MemberAuthenticator\MemberAuthenticator;
use SilverStripe\Security\Security;

/**
 * Class \App\Service\Ex\ServiceAreaController
 *
 * @property \A2nt\CMSNiceties\Ajax\Ex\AjaxLoginFormControllerEx $owner
 */
class AjaxLoginFormControllerEx extends Extension
{
    private static $allowed_actions = [
        'LoginFormEx',
        'LostPasswordForm',
        'passwordsent',
    ];

    public function LoginFormEx()
    {
        $ctrl = $this->owner;

        /* @var Form $form */
        $form = $ctrl->LoginForm();
        $form->setLegend('Sign in to your service account');
        //$form->enableSpamProtection();

        return $form;
    }

    public function LostPasswordForm()
    {
        if (Security::getCurrentUser()) {
            return;
        }

        $ctrl = $this->owner;
        $form = Injector::inst()->get(MemberAuthenticator::class)
            ->getLostPasswordHandler($ctrl->Link())
            ->lostPasswordForm();

        $form->setLegend('Restore your password');
        //$form->enableSpamProtection();

        return $form;
    }

    public function passwordsent()
    {
        $ctrl = $this->owner;

        return Injector::inst()->get(MemberAuthenticator::class)
            ->getLostPasswordHandler($ctrl->Link())
            ->passwordsent();
    }
}
