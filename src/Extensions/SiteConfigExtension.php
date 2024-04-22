<?php

namespace A2nt\CMSNiceties\Extensions;

use A2nt\SilverStripeMapboxField\MapboxField;
use Innoweb\Sitemap\Pages\SitemapPage;
use SilverStripe\AssetAdmin\Forms\UploadField;
use SilverStripe\Assets\Image;
use SilverStripe\Blog\Model\BlogPost;
use SilverStripe\Forms\LiteralField;
use SilverStripe\Forms\TextareaField;
use SilverStripe\Forms\TextField;
use SilverStripe\ORM\DataExtension;
use SilverStripe\CMS\Model\SiteTree;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\TreeMultiselectField;
use SilverStripe\Forms\DropdownField;
use Symbiote\Addressable\Addressable;
use SilverStripe\SiteConfig\SiteConfig;
use SilverStripe\Core\Config\Config;

//use BetterBrief\GoogleMapField;

/**
 * Class \A2nt\CMSNiceties\Extensions\SiteConfigExtension
 *
 * @property \A2nt\CMSNiceties\Extensions\SiteConfigExtension $owner
 * @property string $ExtraCode
 * @property float $Longitude
 * @property float $Latitude
 * @property int $MapZoom
 * @property string $Description
 * @property string $Address
 * @property string $Suburb
 * @property string $State
 * @property string $ZipCode
 * @property int $PrivacyPolicyID
 * @property int $SitemapID
 * @method \SilverStripe\CMS\Model\SiteTree PrivacyPolicy()
 * @method \SilverStripe\CMS\Model\SiteTree Sitemap()
 * @method \SilverStripe\ORM\ManyManyList|\SilverStripe\CMS\Model\SiteTree[] Navigation()
 */
class SiteConfigExtension extends DataExtension
{
    private static $db = [
        'ExtraCode' => 'Text',
        'Lng' => 'Decimal(10, 8)',
        'Lat' => 'Decimal(11, 8)',
        'MapZoom' => 'Int',
        'Description' => 'Varchar(255)',
        'Address' => 'Varchar(255)',
        'Suburb' => 'Varchar(255)',
        'State' => 'Varchar(255)',
        'Country' => 'Varchar(255)',
        'ZipCode' => 'Varchar(6)',
        'AddressExtra' => 'Text',
    ];

    private static $has_one = [
        'PrivacyPolicy' => SiteTree::class,
        'Sitemap' => SiteTree::class,
    ];

    private static $many_many = [
        'Navigation' => SiteTree::class,
    ];

    public function updateCMSFields(FieldList $fields)
    {
        $img = Image::get()->filter([
            'ParentID' => 0,
            'FileFilename' => 'qrcode.png',
        ])->first();
        if ($img) {
            $fields->addFieldsToTab('Root.Main', [
                LiteralField::create('QRCode', '<img src="'.$img->Link().'" alt="QR code" width="200" style="float:left" />'),
            ]);
        }

        $fields->addFieldsToTab('Root.Main', [
            TreeMultiselectField::create(
                'Navigation',
                'Navigation',
                SiteTree::class
            )->setDisableFunction(static function ($el) {
                return $el->getField('ParentID') !== 0;
            }),
            TextareaField::create('Description', 'Website Description'),
            TextareaField::create('ExtraCode', 'Extra site-wide HTML code'),
            DropdownField::create(
                'PrivacyPolicyID',
                'Privacy Policy Page',
                SiteTree::get()->map()->toArray()
            )->setEmptyString('(Select one)'),
            DropdownField::create(
                'SitemapID',
                'Sitemap Page',
                SitemapPage::get()->map()->toArray()
            )->setEmptyString('(Select one)'),
        ]);

        $mapTab = $fields->findOrMakeTab('Root.Maps');
        $mapTab->setTitle('Address / Map');


        $addrFields = [
            TextField::create('Address'),
            TextField::create('ZipCode'),
            TextField::create('Suburb', 'City'),
        ];

        if (\class_exists(Addressable::class)) {
            $stateLabel = _t('Addressable.STATE', 'State');
            $allowedStates = Config::inst()->get(SiteConfig::class, 'allowed_states');
            if ($allowedStates && count($allowedStates) >= 1) {
                // If allowed states are restricted, only allow those
                $addrFields[] = DropdownField::create('State', $stateLabel, $allowedStates);
            } elseif (!$allowedStates) {
                // If no allowed states defined, allow the user to type anything
                $addrFields[] = TextField::create('State', $stateLabel);
            }

            // Get country field
            $countryLabel = _t('Addressable.COUNTRY', 'Country');
            $allowedCountries = Config::inst()->get(SiteConfig::class, 'allowed_countries');
            if ($allowedCountries && count($allowedCountries) >= 1) {
                $addrFields[] = DropdownField::create(
                    'Country',
                    $countryLabel,
                    $allowedCountries
                );
            } else {
                $addrFields[] = TextField::create('Country', $countryLabel);
            }
        } else {
            $addrFields[] = TextField::create('State');
            $addrFields[] = TextField::create('Country');
        }

        $addrFields[] = TextareaField::create('AddressExtra', 'Address Extra Lines');


        $fields->addFieldsToTab('Root.Maps', $addrFields);

        if (\class_exists(MapboxField::class)) {
            if (MapboxField::getAccessToken()) {
                $fields->addFieldsToTab('Root.Maps', [
                    //TextField::create('MapAPIKey'),
                    TextField::create('MapZoom'),
                    MapboxField::create('Map', 'Choose a location', 'Latitude', 'Longitude'),
                ]);
            } else {
                $fields->addFieldsToTab('Root.Maps', [
                    LiteralField::create('MapNotice', '<p class="alert alert-info">No Map API keys specified.</p>')
                ]);
            }
        }

        /*GoogleMapField::create(
            $this->owner,
            'Location',
            [
                'show_search_box' => true,
            ]
        )*/
    }

    public function MapStyle()
    {
        return MapboxField::config()->get('map_style');
    }

    public function getGeoJSON()
    {
        return \json_encode([
            'type' => 'MarkerCollection',
            'features' => [
                [
                    'id' => 'SiteConfig' . $this->owner->ID,
                    'type' => 'Feature',
                    'icon' => '<i class="fa-icon fas fa-map-marker-alt"></i>',
                    'properties' => [
                        'content' => $this->owner->renderWith('A2nt/ElementalBasics/Models/MapPin'),
                    ],
                    'geometry' => [
                        'type' => 'Point',
                        'coordinates' => [
                            $this->owner->Lng,
                            $this->owner->Lat,
                        ],
                    ],
                ]
            ]
        ]);
    }

    public function DirectionsLinkURL()
    {
        return 'https://www.google.com/maps/dir/Current+Location/'
        .$this->owner->Lat.','
        .$this->owner->Lng;
    }

    public function DirectionsLink()
    {
        return '<a href="'.$this->DirectionsLinkURL().'" class="btn btn-primary btn-directions" target="_blank">'
            .'<i class="fas fa-road"></i> Get Directions</a>';
    }

    public function getLatestBlogPosts()
    {
        return BlogPost::get()->sort('PublishDate DESC');
    }
}
