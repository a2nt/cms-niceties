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
}
