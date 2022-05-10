<?php


namespace A2nt\CMSNiceties\Extensions;

use SilverStripe\Forms\CheckboxField;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\NumericField;
use SilverStripe\Forms\TextareaField;
use SilverStripe\Forms\TextField;
use SilverStripe\ORM\DataExtension;

/**
 * Class \A2nt\CMSNiceties\Extensions\ImageExtension
 *
 * @property \A2nt\CMSNiceties\Extensions\ImageExtension $owner
 */
class ImageExtension extends DataExtension
{
    public function updateCMSFields(FieldList $fields)
    {
        parent::updateCMSFields($fields);

        /*$fields->removeByName([
            'Filename',
        ]);*/
    }
}
