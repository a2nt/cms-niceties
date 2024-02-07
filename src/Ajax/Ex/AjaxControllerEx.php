<?php

namespace A2nt\CMSNiceties\Ajax\Ex;

use SilverStripe\Control\Controller;
use SilverStripe\Control\Director;
use SilverStripe\Core\Config\Config;
use SilverStripe\Core\Extension;
use SilverStripe\Core\Injector\Injector;
use SilverStripe\Forms\Form;
use SilverStripe\ORM\FieldType\DBHTMLText;
use SilverStripe\Security\MemberAuthenticator\MemberAuthenticator;
use SilverStripe\Security\Security;
use SilverStripe\View\SSViewer;

/**
 * Class \A2nt\CMSNiceties\Ajax\Ex\AjaxControllerEx
 *
 * @property \A2nt\CMSNiceties\Ajax\Ex\AjaxControllerEx $owner
 */
class AjaxControllerEx extends Extension
{
    private static $no_placeholders = false;
    private static $show_labels = false;

    private static $allowed_actions = [
        'LoginFormEx',
        'LostPasswordForm',
        'passwordsent',
    ];

    private static function _processFields(Form $form)
    {
        $cfg = Config::inst()->get(__CLASS__);

        $fields = $form->Fields();
        foreach ($fields as $field) {
            $name = $field->getName();
            if ($name === 'Remember') {
                continue;
            }

            $field
                ->setAttribute('required', 'required')
                ->addExtraClass('required');

            /*
             *  A2nt\CMSNiceties\Ajax\Ex\AjaxControllerEx:
             *      show_labels: false
             *      no_placeholders: false
             */
            if (!$cfg['no_placeholders']) {
                $placeholder = $field->Title();
                $field->setAttribute(
                    'placeholder',
                    $placeholder
                );
            }

            if (!$cfg['show_labels']) {
                $field->setTitle('');
            }
        }
    }

    public function LoginFormEx()
    {
        $ctrl = $this->owner;

        /* @var Form $form */
        $form = $ctrl->LoginForm();
        self::_processFields($form);

        //$form->addExtraClass('ajax-form');
        $form->setLegend('Log in to your service account');

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

        self::_processFields($form);
        $form->addExtraClass('ajax-form');
        $form->setLegend('Restore your password');

        if ($form->get_protector()) {
            $form->enableSpamProtection();
        }

        return $form;
    }

    public static function isFormRequest()
    {
        $ctrl = Controller::curr();
        /* @var $req SilverStripe\Control\HTTPRequest */
        $req = $ctrl->getRequest();

        return $req->getHeader('x-requested-form') || $req->requestVar('formid');
    }

    public function passwordsent()
    {
        $ctrl = $this->owner;

        if (self::isFormRequest() && Director::is_ajax()) {
            $message = _t(
                'SilverStripe\\Security\\Security.PASSWORDRESETSENTTEXT',
                "Thank you. A reset link has been sent, provided an account exists for this email address."
            );

            $json = json_encode([
                'status' => 'success',
                'message' => '<div class="alert alert-success">'.$message.'</div>',
            ]);

            return $json;
        }

        return Injector::inst()->get(MemberAuthenticator::class)
            ->getLostPasswordHandler($ctrl->Link())
            ->passwordsent();
    }


    public static function processAJAX($tpls)
    {
        foreach ($tpls as $tpl) {
            if (is_array($tpl)) {
                continue;
            }

            $a_tpl = explode('\\', $tpl);
            $last_name = array_pop($a_tpl);
            $a_tpl[] = 'Layout';
            $a_tpl[] = $last_name;
            $a_tpl = implode('\\', $a_tpl);

            if (SSViewer::hasTemplate($a_tpl)) {
                $tpl = $a_tpl;
                break;
            }
        }
        //

        $tpl = is_array($tpl) ? 'Page' : $tpl;
        $tpl = ($tpl !== 'Page') ? $tpl : 'Layout/Page';

        return SSViewer::create($tpl);
    }

    public function prepareAjaxResponse($response)
    {
        $ctrl = $this->owner;

        $record = $ctrl->dataRecord;

        $req = $ctrl->getRequest();
        $url = $req->getURL();
        $url = $url === 'home' ? '/' : $url;

        $resources = array_merge(
            $ctrl->config()->get('graphql_resources'),
            $ctrl->config()->get('ajax_resources')
        );

        $response->setBody(json_encode([
            'ID' => $record->ID,
            'Title' => $record->Title,
            'Link' => $ctrl->Link(),
            'CSSClass' => $ctrl->CSSClass(),
            'Resources' => $resources,
            'RequestLink' => $url,
            'MainContent' => $ctrl->customise([
                'Layout' => DBHTMLText::create()->setValue($response->getBody()),
            ])->renderWith('Includes/MainContent')->RAW(),
        ]));
    }
}
