<?php

namespace A2nt\CMSNiceties\Extensions;

use A2nt\ElementalBasics\Elements\SidebarElement;
use DNADesign\Elemental\Models\ElementContent;
use SilverStripe\CMS\Model\SiteTree;
use SilverStripe\Forms\TextareaField;
use SilverStripe\ORM\DataExtension;
use SilverStripe\Forms\FieldList;

use TractorCow\Fluent\Extension\FluentSiteTreeExtension;

/**
 * Class \A2nt\CMSNiceties\Extensions\SiteTreeExtension
 *
 * @property \A2nt\CMSNiceties\Extensions\SiteTreeExtension $owner
 * @property string $ExtraCode
 */
class SiteTreeExtension extends DataExtension
{
    protected $_cached = [];
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

        if($obj->DisableSidebar) {
            return false;
        }

        $area = $obj->ElementalArea();
        if (!$area) {
            return true;
        }
        $els = $area->Elements();
        if (!$els) {
            return true;
        }
        $els = $els->find('ClassName', SidebarElement::class);
        if (!$els) {
            return true;
        }

        if ($els->first()) {
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

    public static function DefaultContainer()
    {
        return SiteTree::config()->get('default_container_class');
    }

    /*
     * Shows custom summary of the post, otherwise
     * Displays summary of the first content element
     */
    public function Summary($wordsToDisplay = 30)
    {
        $obj = $this->owner;
        if (isset($this->_cached['summary' . $wordsToDisplay])) {
            return $this->_cached['summary' . $wordsToDisplay];
        }

        $summary = $obj->getField('Summary');
        if ($summary) {
            $this->_cached['summary' . $wordsToDisplay] = $summary;

            return $this->_cached['summary' . $wordsToDisplay];
        }

        if(!method_exists($obj, 'ElementalArea')) {
            return;
        }

        $element = ElementContent::get()->filter([
            'ParentID' => $obj->ElementalArea()->ID,
            'HTML:not' => [null],
        ])->first();

        if ($element) {
            $this->_cached['summary' . $wordsToDisplay] = $element->dbObject('HTML')->Summary($wordsToDisplay);

            return $this->_cached['summary' . $wordsToDisplay];
        }

        $content = $obj->getField('Content');
        if ($content) {
            $this->_cached['summary' . $wordsToDisplay] = $obj->dbObject('Content')->Summary($wordsToDisplay);

            return $this->_cached['summary' . $wordsToDisplay];
        }

        $this->_cached['summary' . $wordsToDisplay] = false;

        return $this->_cached['summary' . $wordsToDisplay];
    }

    public function CSSClass()
    {
        $obj = $this->owner;
        return str_replace(['\\'], '-', $obj->getField('ClassName'));
    }

    public function onBeforeWrite()
    {
        parent::onBeforeWrite();

        // h1 page title and navigation label should be equal for SEO
        $obj = $this->owner;
        $obj->setField('MenuTitle', $obj->getField('Title'));

        if (class_exists(FluentSiteTreeExtension::class) && ! $obj->isDraftedInLocale() && $obj->isInDB()) {
            $elementalArea = $obj->ElementalArea();

            $elementalAreaNew = $elementalArea->duplicate();
            $obj->setField('ElementalAreaID', $elementalAreaNew->ID);
        }
    }
}
