<?php

namespace A2nt\CMSNiceties\Dashboard;

use SilverStripe\Control\Controller;
use SilverStripe\ORM\DataObject;
use SilverStripe\View\ViewableData;

class Changelog extends ViewableData
{
    private $object;
    public function __construct(DataObject $object)
    {
        $this->object = $object;
    }

    public function CMSEditLink($version = null)
    {
        if(!$version) {
            return $this->object->CMSEditLink();
        }

        return Controller::join_links(
            $this->object->CMSEditLink(),
            '/ItemEditForm/field/History/item/28/view?VersionID='.$version
        );
    }

    public function Created()
    {
        return $this->object->dbObject('Created');
    }

    public function Versions()
    {
        $versions = $this->object->allVersions();
        return $this->object->allVersions();
    }

    public function render()
    {
        return $this->renderWith(__CLASS__);
    }
}