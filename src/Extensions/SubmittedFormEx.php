<?php

namespace A2nt\CMSNiceties\Extensions;

use SilverStripe\ORM\DataExtension;

class SubmittedFormEx extends DataExtension
{
    public function Title()
    {
        $obj = $this->owner;
        $parent = $obj->Parent();

        $title = '#' . $obj->ID;

        if(!$parent) {
            return $title;
        }

        $cols = $parent->SubmissionColumns();
        foreach ($cols as $col) {
            $name = $col->getField('Name');
            $title .= ' '.$obj->relField($name);
        }

        return $title;
    }
}