<?php

namespace A2nt\CMSNiceties\Extensions;

use SilverStripe\Forms\CheckboxField;
use SilverStripe\Forms\FieldList;
use SilverStripe\ORM\DataExtension;

class RedirectorPageEx extends DataExtension
{
    private static $db = [
        'OpenInNewTab' => 'Boolean(0)',
    ];
    private static $defaults = [
        'OpenInNewTab' => 0,
    ];

    public function updateCMSFields(FieldList $fields)
    {
        $MainTab = $fields->findOrMakeTab('Root.Main');
        $MainTab->push(
            CheckboxField::create(
                'OpenInNewTab',
                _t('RedirectorPage.OpenInNewTab', 'Open in new tab')
            )
        );
    }
}
