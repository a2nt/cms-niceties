<?php

namespace A2nt\CMSNiceties\Ajax;

use A2nt\CMSNiceties\Ajax\Ex\AjaxControllerEx;
use SilverStripe\Forms\FormRequestHandler;
use SilverStripe\ORM\ValidationResult;

class AjaxFormRequestHandler extends FormRequestHandler
{
    private static $allowed_actions = [
        'httpSubmission',
    ];

    /**
     * Handle a form submission.  GET and POST requests behave identically.
     * Populates the form with {@link loadDataFrom()}, calls {@link validate()},
     * and only triggers the requested form action/method
     * if the form is valid.
     *
     * @param HTTPRequest $request
     * @return HTTPResponse
     * @throws HTTPResponse_Exception
     */
    public function httpSubmission($request)
    {
        $resp = parent::httpSubmission($request);

        if (!AjaxControllerEx::isFormRequest()) {
            return $resp;
        }

        $validation = $this->form->validationResult();
        if (!$validation->isValid()) {
            $messages = $validation->getMessages();
            return json_encode([
                'status' => ValidationResult::TYPE_ERROR,
                'msgs' => $messages,
            ]);
        }

        return $resp;
    }
}
