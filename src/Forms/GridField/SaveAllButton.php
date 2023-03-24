<?php

namespace A2nt\CMSNiceties\Forms\GridField;

use SilverStripe\Forms\GridField\GridField;
use SilverStripe\Forms\GridField\GridField_HTMLProvider;
use SilverStripe\Forms\GridField\GridField_ActionProvider;
use SilverStripe\Forms\GridField\GridField_FormAction;
use SilverStripe\Forms\GridField\GridField_SaveHandler;
use SilverStripe\Control\Controller;
use Symbiote\GridFieldExtensions\GridFieldEditableColumns;

class SaveAllButton implements GridField_HTMLProvider, GridField_ActionProvider
{
    protected $targetFragment;
    protected $actionName = 'saveallrecords';

    public $buttonName;

    public $publish = true;

    public $completeMessage;

    public $removeChangeFlagOnFormOnSave = false;

    public function setButtonName($name)
    {
        $this->buttonName = $name;
        return $this;
    }

    public function setRemoveChangeFlagOnFormOnSave($flag)
    {
        $this->removeChangeFlagOnFormOnSave = $flag;
        return $this;
    }

    public function __construct($targetFragment = 'before', $publish = true, $action = 'saveallrecords')
    {
        $this->targetFragment = $targetFragment;
        $this->publish = $publish;
        $this->actionName = $action;
    }

    public function getHTMLFragments($gridField)
    {
        $class = $gridField->getModelClass();
        $singleton = singleton($class);

        if (!$singleton->canEdit() && !$singleton->canCreate()) {
            return [];
        }

        if (!$this->buttonName) {
            if ($this->publish && $singleton->hasExtension(Versioned::class)) {
                $this->buttonName = _t('GridField.SAVE_ALL_AND_PUBLISH', 'Save all and Publish');
            } else {
                $this->buttonName = _t('GridField.SAVE_ALL', 'Save all');
            }
        }

        $button = GridField_FormAction::create(
            $gridField,
            $this->actionName,
            $this->buttonName,
            $this->actionName,
            null
        );

        $button
            ->setAttribute('style', 'float:right')
            ->addExtraClass('action action-detail btn btn-primary font-icon-disk new new-link font-icon-check-mark');

        if ($this->removeChangeFlagOnFormOnSave) {
            $button->addExtraClass('js-mwm-gridfield--saveall');
        }

        return [
            $this->targetFragment => $button->Field(),
        ];
    }

    public function getActions($gridField)
    {
        return [$this->actionName];
    }

    public function handleAction(GridField $gridField, $actionName, $arguments, $data)
    {
        if ($actionName == $this->actionName) {
            return $this->saveAllRecords($gridField, $arguments, $data);
        }
    }

    protected function saveAllRecords(GridField $grid, $arguments, $data)
    {
        if (!isset($data[$grid->Name])
            || !isset($data[$grid->Name]['GridFieldEditableColumns'])
        ) {
            return;
        }

        $currValue = $grid->Value();
        $grid->setValue($data[$grid->Name]);
        $model = singleton($grid->List->dataClass());
        $cfg = $grid->getConfig();

        foreach ($cfg->getComponents() as $component) {
            if ($component instanceof GridField_SaveHandler) {
                $component->handleSave($grid, $model);
            }
        }

        // Only use the viewable list items, since bulk publishing can take a toll on the system
        $paginator = $cfg->getComponentByType(GridFieldPaginator::class);
        $list = $paginator
            ? $paginator->getManipulatedData($grid, $grid->List)
            : $grid->List;

        // add missing checkbox fields
        $cols = $cfg->getComponentByType(GridFieldEditableColumns::class);
        if ($cols) {
            $fields = $cols->getFields($grid, $model);
            $colsData = $data[$grid->Name]['GridFieldEditableColumns'];

            if (isset($colsData)) {
                $list->each(function ($item) use ($colsData, $grid, $fields) {
                    /* @var $item \SilverStripe\ORM\DataObject */
                    if (!isset($colsData[$item->ID])) {
                        foreach ($fields as $field) {
                            /* @var $field \SilverStripe\Forms\FormField */
                            $fieldName = $field->getName();
                            $item->setField($fieldName, '');
                        }

                        $item->write();
                    }
                });
            }
        }

        if ($this->publish) {
            $list->each(function ($item) {
                if ($item->hasExtension(Versioned::class)) {
                    $item->writeToStage('Stage');

                    if (!$item->stagesDiffer()) {
                        $item->copyVersionToStage('Stage', 'Live');
                    }
                }
            });
        }

        if ($model->exists()) {
            $model->delete();
            $model->destroy();
        }

        $grid->setValue($currValue);

        $curr = Controller::curr();
        $response = $curr->Response;

        if ($curr && $response) {
            if (!$this->completeMessage) {
                $this->completeMessage = _t('GridField.DONE', 'Done.');
            }

            $response->addHeader('X-Status', rawurlencode($this->completeMessage));
        }
    }
}
