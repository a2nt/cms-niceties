<?php


namespace A2nt\CMSNiceties\Extensions;

use SilverStripe\Blog\Forms\GridField\GridFieldConfigBlogPost;
use SilverStripe\Forms\FieldList;
use SilverStripe\ORM\DataExtension;

/**
 * Class \A2nt\CMSNiceties\Extensions\BlogExtension
 *
 * @property \A2nt\CMSNiceties\Extensions\BlogExtension $owner
 */
class BlogExtension extends DataExtension
{
    public function updateCMSFields(FieldList $fields)
    {
        $f = $fields->dataFieldByName('ChildPages');
        if ($f) {
            $f->setConfig(GridFieldConfigBlogPost::create(75));
        }
    }
}
