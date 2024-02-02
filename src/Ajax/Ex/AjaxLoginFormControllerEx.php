<?php

namespace A2nt\CMSNiceties\Ajax\Ex;

use SilverStripe\Control\Director;
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

        //$form->addExtraClass('ajax-form');
        $form->setLegend('Sign in to your service account');

        if ($form->get_protector()) {
            $form->enableSpamProtection();
        }

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

        $form->addExtraClass('ajax-form');
        $form->setLegend('Restore your password');

        if ($form->get_protector()) {
            $form->enableSpamProtection();
        }

        return $form;
    }

    public function passwordsent()
    {
        $ctrl = $this->owner;

        if (Director::is_ajax()) {
            $message = _t(
                'SilverStripe\\Security\\Security.PASSWORDRESETSENTTEXT',
                "Thank you. A reset link has been sent, provided an account exists for this email address."
            );

            $json = json_encode([
                'status' => 'success',
                'message' => '<div class="alert alert-success">'.$message.'</div>',
            ]);

            return $json;
            /*$response = $ctrl->getResponse();
            $response->setBody($json);
            die($response->output());*/
        }

        return Injector::inst()->get(MemberAuthenticator::class)
            ->getLostPasswordHandler($ctrl->Link())
            ->passwordsent();
    }
}
