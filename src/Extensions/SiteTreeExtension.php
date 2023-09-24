<?php

namespace A2nt\CMSNiceties\Extensions;

use A2nt\ElementalBasics\Elements\SidebarElement;
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

    public function ShowSidebar()
    {
        $obj = $this->owner;

        if ($obj->ElementalArea()->Elements()->find('ClassName', SidebarElement::class)->first()) {
            return false;
        }

        if ($obj->SideBarContent) {
            return true;
        }
        if (method_exists($obj, 'SideBarView')) {
            $view = $obj->SideBarView();

            if ($view && $view->Widgets()->count()) {
                return true;
            }
        }

        return false;
    }

    public function onBeforeWrite()
    {
        parent::onBeforeWrite();

        // h1 page title and navigation label should be equal for SEO
        $obj = $this->owner;
        $obj->setField('MenuTitle', $obj->getField('Title'));
    }
}
