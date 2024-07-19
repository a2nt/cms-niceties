<?php

namespace A2nt\CMSNiceties\Forms\GridField;

use SilverStripe\Forms\GridField\GridField_ActionMenu;
use SilverStripe\Forms\GridField\GridFieldConfig_RelationEditor as GridFieldGridFieldConfig_RelationEditor;
use SilverStripe\Forms\GridField\GridFieldEditButton;

class GridFieldConfig_RelationEditor extends GridFieldGridFieldConfig_RelationEditor
{
    public function __construct($itemsPerPage = null, $showPagination = null, $showAdd = null)
    {
        parent::__construct($itemsPerPage, $showPagination, $showAdd);

        $this->removeComponentsByType([
            GridField_ActionMenu::class,
        ]);

        $btn = $this->getComponentByType(GridFieldEditButton::class);
        if ($btn) {
            $btn->removeExtraClass('grid-field__icon-action--hidden-on-hover');
        }
    }
}
