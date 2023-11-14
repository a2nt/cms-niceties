<?php

namespace A2nt\CMSNiceties\Extensions;

use SilverStripe\Core\Config\Config;
use SilverStripe\Core\Extension;
use SilverStripe\Forms\CompositeField;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\TextField;
use SilverStripe\Forms\TextareaField;
use SilverStripe\Forms\FormField;

/**
 * Class \A2nt\CMSNiceties\Extensions\PlaceholderFormExtension
 *
 * @property \A2nt\CMSNiceties\Extensions\PlaceholderFormExtension $owner
 */
class PlaceholderFormExtension extends Extension
{
    public function updateFormFields(FieldList $fields)
    {
        foreach ($fields as $field) {
            $this->setPlaceholder($field);
        }
    }

    private function setPlaceholder($field)
    {
        if (is_a($field, TextField::class) || is_a($field, TextareaField::class)) {
            if (!$field->getAttribute('placeholder')) {
                $placeholder = $field->Title();

                if (!Config::inst()->get(\get_class($this->owner), 'no_placeholders')) {
                    $field->setAttribute(
                        'placeholder',
                        $placeholder
                    );
                }

                /*
                 *  SilverStripe\UserForms\Form\UserForm:
                 *      show_labels: false
                 */
                if (!Config::inst()->get(\get_class($this->owner), 'show_labels')) {
                    $field->setTitle('');
                }
            }
        }

        if (is_a($field, CompositeField::class)) {
            $children = $field->getChildren();
            foreach ($children as $child) {
                $this->setPlaceholder($child);
            }
        }
    }
}
