<?php
/**
 * Created by PhpStorm.
 * User: tony
 * Date: 6/30/18
 * Time: 11:37 PM
 */

namespace A2nt\CMSNiceties\Extensions;

use Sheadawson\Linkable\Forms\LinkField;
use Sheadawson\Linkable\Models\Link;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\TextField;
use SilverStripe\ORM\DataExtension;
use SilverStripe\Security\Member;

/**
 * Class \A2nt\CMSNiceties\Extensions\SocialExtension
 *
 * @property \A2nt\ElementalBasics\Models\TeamMember|\A2nt\CMSNiceties\Extensions\SocialExtension $owner
 * @property int $FacebookID
 * @property int $LinkedInID
 * @property int $PinterestID
 * @property int $InstagramID
 * @property int $TwitterID
 * @property int $YouTubeID
 * @property int $PublicEmailID
 * @property int $PhoneNumberID
 * @method \Sheadawson\Linkable\Models\Link Facebook()
 * @method \Sheadawson\Linkable\Models\Link LinkedIn()
 * @method \Sheadawson\Linkable\Models\Link Pinterest()
 * @method \Sheadawson\Linkable\Models\Link Instagram()
 * @method \Sheadawson\Linkable\Models\Link Twitter()
 * @method \Sheadawson\Linkable\Models\Link YouTube()
 * @method \Sheadawson\Linkable\Models\Link PublicEmail()
 * @method \Sheadawson\Linkable\Models\Link PhoneNumber()
 */
class SocialExtension extends DataExtension
{
    private static $db = [
        //'PhoneNumber' => 'Varchar(255)',
    ];

    private static $has_one = [
        'Facebook' => Link::class,
        'LinkedIn' => Link::class,
        'Pinterest' => Link::class,
        'Instagram' => Link::class,
        'Twitter' => Link::class,
        'YouTube' => Link::class,
        'PublicEmail' => Link::class,
        'PhoneNumber' => Link::class,
    ];

    public function updateCMSFields(FieldList $fields)
    {
        parent::updateCMSFields($fields);

        $linkFields = [
            LinkField::create('FacebookID', 'Facebook'),
            LinkField::create('LinkedInID', 'LinkedIn'),
            LinkField::create('PinterestID', 'Pinterest'),
            LinkField::create('InstagramID', 'Instagram'),
            LinkField::create('TwitterID', 'Twitter'),
            LinkField::create('YouTubeID', 'YouTube'),
        ];

        foreach ($linkFields as $field) {
            $field->setAllowedTypes(['URL']);
        }

        $fields->findOrMakeTab('Root.Social');

        $fields->addFieldsToTab('Root.Social', [
            LinkField::create('PublicEmailID', 'Public Email')
                ->setAllowedTypes(['Email']),
            LinkField::create('PhoneNumberID', 'Phone Number')
                ->setAllowedTypes(['Phone']),
        ]);

        $fields->addFieldsToTab('Root.Social', $linkFields);
    }

    public static function byPhone($phone)
    {
        $links = Link::get()->filter('Phone', $phone);

        if ($links->exists()) {
            return Member::get()->filter(
                'PhoneNumberID',
                array_keys($links->map('ID', 'Title')->toArray())
            )->first();
        }

        return null;
    }
}
