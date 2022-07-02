<?php
/**
 * Created by PhpStorm.
 * User: tony
 * Date: 9/12/18
 * Time: 2:55 AM
 */

namespace A2nt\CMSNiceties\Models;

use gorriecoe\Link\Models\Link;
use gorriecoe\LinkField\LinkField;
use SilverStripe\ORM\DataObject;
use SilverStripe\ORM\ValidationResult;
use SilverStripe\SiteConfig\SiteConfig;

/**
 * Class \A2nt\CMSNiceties\Models\Notification
 *
 * @property string $Title
 * @property string $Content
 * @property string $DateOn
 * @property string $DateOff
 * @property string $Area
 * @property int $ParentID
 * @property int $TargetLinkID
 * @method \SilverStripe\SiteConfig\SiteConfig Parent()
 * @method \Sheadawson\Linkable\Models\Link TargetLink()
 */
class Notification extends DataObject
{
    private static $table_name = 'Notification';

    private static $db = [
        'Title' => 'Varchar(255)',
        'Content' => 'Text',
        'DateOn' => 'Date',
        'DateOff' => 'Date',
        'Area' => 'Enum("Site","Site")',
    ];

    private static $has_one = [
        'Parent' => SiteConfig::class,
        'TargetLink' => Link::class,
    ];

    private static $defaults = [
        'Area' => 'Site',
    ];


    private static $summary_fields = [
        'Title' => 'Title',
        'Content' => 'Text',
        'DateOn' => 'Turn on date',
        'DateOff' => 'Turn off date',
    ];

    private static $default_sort = 'DateOn DESC, DateOff DESC, Title ASC';

    public function getCMSFields()
    {
        $fields = parent::getCMSFields();

        $fields->addFieldsToTab('Root.Main', [
            LinkField::create('TargetLink', 'Link', $this),
        ]);

        return $fields;
    }

    public function validate()
    {
        $result = parent::validate();

        if (!$this->getField('DateOn') || !$this->getField('DateOff')) {
            return $result->addError(
                'Turn on and turn off dates are required.',
                ValidationResult::TYPE_ERROR
            );
        }

        if (!$this->getField('Content')) {
            return $result->addError(
                'Text field required.',
                ValidationResult::TYPE_ERROR
            );
        }

        return $result;
    }
}
