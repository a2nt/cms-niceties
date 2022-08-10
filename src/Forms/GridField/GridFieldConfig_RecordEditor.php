<?php

namespace A2nt\CMSNiceties\Forms\GridField;

use SilverStripe\Forms\GridField\GridField_ActionMenu;
use SilverStripe\Forms\GridField\GridFieldConfig_RecordEditor as GridFieldGridFieldConfig_RecordEditor;
use SilverStripe\Forms\GridField\GridFieldEditButton;

class GridFieldConfig_RecordEditor extends GridFieldGridFieldConfig_RecordEditor
{
    public function __construct($itemsPerPage = null)
    {
        parent::__construct();

        $this->removeComponentsByType([
            GridField_ActionMenu::class,
        ]);

        $btn = $this->getComponentByType(GridFieldEditButton::class);
        if ($btn) {
            $btn->removeExtraClass('grid-field__icon-action--hidden-on-hover');
        }
    }
}
