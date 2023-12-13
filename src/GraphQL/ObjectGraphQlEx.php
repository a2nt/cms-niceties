<?php

namespace A2nt\CMSNiceties\GraphQL;

use A2nt\CMSNiceties\Templates\DeferredRequirements;
use SilverStripe\Control\Controller;
use SilverStripe\GraphQL\Controller as GraphQLController;
use SilverStripe\Core\Extension;
use SilverStripe\View\Requirements;

/**
 * Class \A2nt\CMSNiceties\GraphQL\ObjectGraphQlEx
 *  AJAX/GraphQL helpers
 */
class ObjectGraphQlEx extends Extension
{
    // Get rendered template
    public function MainContent()
    {
        $object = $this->owner;
        return isset($object->GraphQLContent) ? $object->GraphQLContent : null;
    }

    public function Resources()
    {
        $object = $this->owner;
        $res = $object->config()->get('graphql_resources');
        return $res ? json_encode($res) : null;
    }

    public function RequestLink()
    {
        $curr = Controller::curr();
        $req = $curr->getRequest();

        //$var = URLLinkablePlugin::config()->get('single_field_name');
        $var = 'url';
        if ($curr::class === GraphQLController::class) {
            $vars = json_decode($curr->getRequest()->getBody(), true)['variables'];
            if (isset($vars[$var])) {
                $link = $vars[$var];

                if ($req->requestVar('SecurityID')) {
                    $urlArray = explode('/', $link);
                    $urlArray = array_filter($urlArray);

                    // remove last element
                    array_pop($urlArray);

                    $link = '/'.implode('/', $urlArray).'/';
                }

                return $link;
            }
        }

        return null;
    }

    public function isFormResponse()
    {
        $curr = Controller::curr();
        $req = $curr->getRequest();

        // TODO: GraphQL form response /element/*id*/action
        return $req->requestVar('SecurityID') || $req->httpMethod() === 'POST' || preg_match('!element/([0-9]+)/([A-z]+)!', $req->getURL());
    }

    public function isLegacy()
    {
        $object = $this->owner;

        return $object->config()->get('legacy') || in_array($object->ClassName, [
            RedirectorPage::class,
            ErrorPage::class,
        ]);
    }
}
