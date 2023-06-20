<?php

namespace A2nt\CMSNiceties\Extensions;

use SilverStripe\Forms\TextareaField;
use SilverStripe\ORM\DataExtension;
use SilverStripe\Forms\FieldList;

/**
 * Class \A2nt\CMSNiceties\Extensions\SiteTreeExtension
 *
 * @property \A2nt\CMSNiceties\Extensions\SiteTreeExtension $owner
 * @property string $ExtraCode
 */
class SiteTreeExtension extends DataExtension
{
    private static $db = [
        'ExtraCode' => 'Text',
    ];

    public function updateSettingsFields(FieldList $fields)
    {
        $fields->addFieldsToTab('Root.Settings', [
            TextareaField::create(
                'ExtraCode',
                'Extra page specific HTML code'
            ),
        ]);
    }

    public function updateCMSFields(FieldList $fields)
    {
        $f = $fields->dataFieldByName('MenuTitle');
        // Elements has own Title field to be used at content (h1 can be hidden),
        // while Menu Title (h1 page title) and Navigation label should be equal for SEO
        if ($f) {
            // name page name as navigation label to be more clear for CMS admin
            $fields->dataFieldByName('Title')->setTitle($f->Title());
            $fields->removeByName('MenuTitle');
        }
    }

    public function onBeforeWrite()
    {
        parent::onBeforeWrite();

        // h1 page title and navigation label should be equal for SEO
        $obj = $this->owner;
        $obj->setField('MenuTitle', $obj->getField('Title'));
    }
}
