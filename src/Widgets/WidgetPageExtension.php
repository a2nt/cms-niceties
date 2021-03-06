<?php


namespace A2nt\CMSNiceties\Widgets;

use DNADesign\Elemental\Forms\ElementalAreaField;
use SilverStripe\Forms\FieldList;
use SilverStripe\Widgets\Forms\WidgetAreaEditor;
use SilverStripe\Widgets\Model\Widget;
use SilverStripe\Widgets\Model\WidgetArea;

/**
 * Class \A2nt\CMSNiceties\Widgets\WidgetPageExtension
 *
 * @property \A2nt\CMSNiceties\Widgets\WidgetPageExtension $owner
 */
class WidgetPageExtension extends \SilverStripe\Widgets\Extensions\WidgetPageExtension
{
    public function updateCMSFields(FieldList $fields)
    {
        parent::updateCMSFields($fields);

        $tab = $fields->findOrMakeTab('Root.Widgets');

        $tab->setTitle('Sidebar');

        $tab->removeByName('SideBar');

        $widgetTypes =  WidgetAreaEditor::create('Sidebar')->AvailableWidgets();
        $available = [];
        /** @var Widget $type */
        foreach ($widgetTypes as $type) {
            $available[get_class($type)] = $type->getCMSTitle();
        }

        $tab->push(WidgetAreaField::create(
            'SideBar',
            $this->owner->Sidebar(),
            $available
        ));
    }

    public function onBeforeWrite()
    {
        parent::onBeforeWrite();

        if (!$this->owner->getField('SideBarID')) {
            $area = WidgetArea::create();
            $area->write();

            $this->owner->setField('SideBarID', $area->ID);
        }
    }
}
