<?php
/**
 * Created by PhpStorm.
 * User: tony
 * Date: 9/12/18
 * Time: 2:55 AM
 */

namespace A2nt\CMSNiceties\Models;

use Dynamic\FlexSlider\Model\SlideImage;
use SilverStripe\ORM\DataObject;
use SilverStripe\ORM\ValidationResult;
use SilverStripe\SiteConfig\SiteConfig;

/**
 * Class \A2nt\CMSNiceties\Models\Holiday
 *
 * @property string $Title
 * @property string $Date
 * @property int $ParentID
 * @method \SilverStripe\SiteConfig\SiteConfig Parent()
 */
class Holiday extends DataObject
{
    private static $table_name = 'Holiday';

    private static $db = [
        'Title' => 'Varchar(255)',
        'Date' => 'Date',
    ];

    private static $has_one = [
        'Parent' => SiteConfig::class,
    ];


    private static $summary_fields = [
        'Title' => 'Title',
        'Date' => 'Date',
    ];

    private static $default_sort = 'Date ASC, Title ASC';

    public function validate()
    {
        $result = parent::validate();

        $exists = self::get()->filter([
            'ID:not' => $this->ID,
            'Date' => $this->getField('Date'),
        ])->exists();

        if($exists) {
            return $result->addError(
                'Holiday was defined already.',
                ValidationResult::TYPE_ERROR
            );
        }

        return $result;
    }
}
