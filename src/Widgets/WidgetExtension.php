<?php

namespace A2nt\CMSNiceties\Widgets;

use DNADesign\Elemental\Forms\TextCheckboxGroupField;
use SilverStripe\CMS\Model\SiteTree;
use SilverStripe\Control\Director;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\TreeDropdownField;
use SilverStripe\ORM\DataExtension;
use SilverStripe\Forms\LiteralField;

/**
 * Class \A2nt\CMSNiceties\Widgets\WidgetExtension
 *
 * @property \A2nt\CMSNiceties\Widgets\WidgetExtension $owner
 * @property boolean $ShowTitle
 */
class WidgetExtension extends DataExtension
{
    private static $db = [
        'ShowTitle' => 'Boolean(1)',
    ];

    public function updateCMSFields(FieldList $fields)
    {
        $obj = $this->owner;
        parent::updateCMSFields($fields);
        // Add a combined field for "Title" and "Displayed" checkbox in a Bootstrap input group
        $fields->removeByName('ShowTitle');
        $fields->replaceField(
            'Title',
            TextCheckboxGroupField::create()
                ->setName('Title')
        );

        if ($obj->ID) {
            $fields->push(TreeDropdownField::create(
                'MovePageID',
                'Move widget to page',
                SiteTree::class
            ));
        }

        $fields->push(LiteralField::create(
            'Type',
            '<div class="form-group field text">'
            .'<div class="form__field-label">Type</div>'
            .'<div class="form__field-holder">'.(!Director::isLive() ? $obj->getField('ClassName') : $obj->i18n_singular_name()).'</div>'
            .'</div>'
        ));
    }

    public function onBeforeWrite()
    {
        $obj = $this->owner;
        $moveID = $obj->MovePageID;
        if ($moveID) {
            $page = \Page::get()->byID($moveID);
            if ($page) {
                $sidebarID = $page->getField('SideBarID');
                if ($sidebarID) {
                    $obj->setField('ParentID', $sidebarID);
                }
            }
        }

        parent::onBeforeWrite();
    }
}
