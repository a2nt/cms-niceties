<?php


namespace A2nt\CMSNiceties\Forms\GridField;


use SilverStripe\Forms\GridField\GridFieldButtonRow;
use SilverStripe\Forms\GridField\GridFieldConfig;
use SilverStripe\Forms\GridField\GridFieldDeleteAction;
use SilverStripe\Forms\GridField\GridFieldDetailForm;
use SilverStripe\Forms\GridField\GridFieldEditButton;
use SilverStripe\Forms\GridField\GridFieldFilterHeader;
use SilverStripe\Forms\GridField\GridFieldPageCount;
use SilverStripe\Forms\GridField\GridFieldPaginator;
use SilverStripe\Forms\GridField\GridFieldToolbarHeader;
use Symbiote\GridFieldExtensions\GridFieldAddNewInlineButton;
use Symbiote\GridFieldExtensions\GridFieldEditableColumns;
use Symbiote\GridFieldExtensions\GridFieldTitleHeader;

class GridFieldConfig_Inline extends GridFieldConfig
{
	/**
     *
	 * @param bool $showDetails Whether the `Details form` should display or not, leave as null to use default
	 * @param bool $showAdd Whether the `Add` button should display or not, leave as null to use default
     * @param int $itemsPerPage - How many items per page should show up
     */
    public function __construct($showDetails = false, $showAdd = true, $itemsPerPage = 100)
    {
        parent::__construct();

        $this
            ->addComponent(new GridFieldTitleHeader())
			->addComponent(new GridFieldEditableColumns());

        if($showDetails) {
	        $this
		        ->addComponent(new GridFieldDetailForm(null, true, $showAdd))
		        ->addComponent(new GridFieldEditButton());
        }

        $this
	        ->addComponent($pagination = new GridFieldPaginator($itemsPerPage))
	        ->addComponent($filter = new GridFieldFilterHeader())
	        ->addComponent(new GridFieldPageCount('toolbar-header-right'))
	        ->addComponent(new GridFieldButtonRow('before'))
			->addComponent(new GridFieldToolbarHeader())
			->addComponent(new GridFieldDeleteAction());

        if($showAdd) {
        	$this->addComponent(new GridFieldAddNewInlineButton());
        }

        $pagination->setThrowExceptionOnBadDataType(false);
        $filter->setThrowExceptionOnBadDataType(false);

        $this->extend('updateConfig');
	}
}