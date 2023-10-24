<?php

namespace A2nt\CMSNiceties\GraphQL;

use SilverStripe\Control\Controller;
use SilverStripe\GraphQL\Controller as GraphQLController;
use App\GraphQL\URLLinkablePlugin;
use SilverStripe\ORM\DataExtension;

/**
 * Class \A2nt\CMSNiceties\Extensions\SubmittedFormEx
 *  AJAX/GraphQL helpers
 * @property \A2nt\CMSNiceties\Extensions\SubmittedFormEx $owner
 */
class ObjectGraphQlEx extends DataExtension
{
    // Get rendered template
    public function MainContent()
    {
        $object = $this->owner;
        return isset($object->GraphQLContent) ? $object->GraphQLContent : null;
    }

    public function RequestLink()
    {
        $curr = Controller::curr();
        //$var = URLLinkablePlugin::config()->get('single_field_name');
        $var = 'url';
        if ($curr::class === GraphQLController::class) {
            $vars = json_decode($curr->getRequest()->getBody(), true)['variables'];
            if (isset($vars[$var])) {
                return $vars[$var];
            }
        }

        return null;
    }

    public function isFormResponse()
    {
        $curr = Controller::curr();
        $req = $curr->getRequest();

        return $req->requestVar('SecurityID') || $req->httpMethod() === 'POST';
    }
}
