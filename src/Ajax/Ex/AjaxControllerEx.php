<?php

namespace A2nt\CMSNiceties\Ajax\Ex;

use SilverStripe\CMS\Model\RedirectorPage;
use SilverStripe\Control\Controller;
use SilverStripe\Control\Director;
use SilverStripe\Control\HTTPResponse;
use SilverStripe\Core\Config\Config;
use SilverStripe\Core\Extension;
use SilverStripe\Core\Injector\Injector;
use SilverStripe\ErrorPage\ErrorPage;
use SilverStripe\Forms\Form;
use SilverStripe\Forms\HiddenField;
use SilverStripe\ORM\FieldType\DBHTMLText;
use SilverStripe\ORM\ValidationResult;
use SilverStripe\Security\MemberAuthenticator\MemberAuthenticator;
use SilverStripe\Security\Security;
use SilverStripe\UserForms\Control\UserDefinedFormController;
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
    private static $add_asteriks = false;

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
             *      add_asteriks: false
             */
            if (!$cfg['no_placeholders']) {
                $placeholder = $field->Title();
                $field->setAttribute(
                    'placeholder',
                    $placeholder.($cfg['add_asteriks'] ? ' *' : '')
                );
            }

            if (!$cfg['show_labels']) {
                $field->setTitle('');
            }
        }
    }

    public function LoginFormEx()
    {
        $ctrl = Security::singleton();

        $auth = $ctrl->getApplicableAuthenticators()['default'];
        $loginHandler = $auth->getLoginHandler('login');

        /* @var Form $form */
        $form = $loginHandler->LoginForm();
        self::_processFields($form);

        //$form->addExtraClass('ajax-form');
        $back = $this->owner->Link();
        $form->setFormAction('/Security/login/default/LoginForm/?BackURL='.$back);
        $form->Fields()
            ->push(HiddenField::create('BackURL')->setValue($back));

        $form->setLegend(
            _t(
                'SilverStripe\\Security\\Security.LOGINFORMLEGEND',
                'Log in'
            )
        );

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
        $form->Actions()->first()->setTitle(
            _t(
                'SilverStripe\\Security\\Security.SUBMITTITLE',
                'Submit'
            )
        );

        if (Director::isLive() && $form->get_protector()) {
            $form->enableSpamProtection();
        }

        $form->setLegend(
            _t(
                'SilverStripe\\Security\\Security.LOSTPASSWORDFORMLEGEND',
                'I\'ve lost my password'
            )
        );

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

    private static function isJson($str)
    {
        $json = json_decode($str);
        return $json && $str != $json;
    }

    public function prepareAjaxResponse(HTTPResponse $response)
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

        $body = $response->getBody();
        if (self::isFormRequest()) {
            /* @var $req \SilverStripe\Control\HTTPRequest */
            $req = $ctrl->getRequest();
            $sess = $req->getSession();

            $formName = $req->requestVar('formid');
            $data = $sess->get('FormInfo');

            // form validation response
            if ($formName && $data && isset($data[$formName]['result'])) {
                $sess->clear('FormInfo.'.$formName);

                /* @var $valid \SilverStripe\ORM\ValidationResult */
                $valid = unserialize($data[$formName]['result']);
                $msgs = $valid->getMessages();

                $body = json_encode([
                    'status' => $valid->isValid()
                        ? ValidationResult::TYPE_GOOD
                        : ValidationResult::TYPE_ERROR,
                    'msgs' => $msgs,
                ]);

                $response->removeHeader('Location');
                $response->setStatusCode(200);
                $response->setBody($body);

                return $response;
            }
        }

        // ajax redirect
        if ($response->isRedirect()) {
            $body = json_encode([
                'location' => $response->getHeader('location'),
                'loadAjax' => true,
            ]);

            $response->removeHeader('Location');
            $response->setStatusCode(200);
            $response->setBody($body);

            return $response;
        }

        // render page
        if ($body && !self::isJson($body)) {
            $body = json_encode([
                'ID' => $record->ID,
                'Title' => $record->Title,
                'Link' => $ctrl->Link(),
                'CSSClass' => $ctrl->CSSClass(),
                'Resources' => $resources,
                'RequestLink' => $url,
                'MainContent' => $ctrl->customise([
                    'Layout' => DBHTMLText::create()->setValue($response->getBody()),
                ])->renderWith('Includes/MainContent')->RAW(),
            ]);
        }

        $response->setBody($body);
    }


    public function isLegacy()
    {
        $object = $this->owner;

        if (
            is_a($object, UserDefinedFormController::class)
            && $object->getAction() === 'finished'
        ) {
            return true;
        }

        return $object->config()->get('legacy') || in_array(
            $object->ClassName,
            [
                RedirectorPage::class,
                ErrorPage::class,
            ]
        );
    }
}
