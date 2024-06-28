<?php

namespace A2nt\CMSNiceties\Widgets;

use DNADesign\Elemental\Forms\ElementalAreaField;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\LiteralField;
use SilverStripe\Widgets\Forms\WidgetAreaEditor;
use SilverStripe\Widgets\Model\Widget;
use SilverStripe\Widgets\Model\WidgetArea;
use SilverStripe\Forms\CheckboxField;

/**
 * Class \A2nt\CMSNiceties\Widgets\WidgetPageExtension
 *
 * @property \A2nt\CMSNiceties\Widgets\WidgetPageExtension $owner
 */
class WidgetPageExtension extends \SilverStripe\Widgets\Extensions\WidgetPageExtension
{
    private static $db = [
        'DisableSidebar' => 'Boolean(0)',
    ];

    public function updateCMSFields(FieldList $fields)
    {
        parent::updateCMSFields($fields);

        $tab = $fields->findOrMakeTab('Root.Widgets');

        $tab->setTitle('Sidebar');
        $tab->removeByName('SideBar');
        $tab->push(CheckboxField::create('DisableSidebar'));

        $widgetTypes =  WidgetAreaEditor::create('Sidebar')->AvailableWidgets();
        $available = [];
        /** @var Widget $type */
        foreach ($widgetTypes as $type) {
            $available[get_class($type)] = $type->getCMSTitle();
        }

        $w = $this->owner->Sidebar();
        $tab->push(WidgetAreaField::create(
            'SideBar',
            $w,
            $available
        ));
    }

    public function onBeforeWrite()
    {
        parent::onBeforeWrite();

        $obj = $this->owner;
        $w = $obj->SideBar();

        if (!$w->ID || !$obj->getField('SideBarID')) {
            $area = WidgetArea::create();
            $area->write();

            $obj->setField('SideBarID', $area->ID);
        }
    }
}
