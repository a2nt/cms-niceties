<?php

namespace A2nt\CMSNiceties\Extensions;

use Sheadawson\Linkable\Forms\EmbeddedObjectField;
use SilverStripe\Forms\CheckboxField;
use SilverStripe\Forms\CompositeField;
use SilverStripe\Forms\LiteralField;
use SilverStripe\ORM\FieldType\DBHTMLText;

class EmbedObjectField extends EmbeddedObjectField
{
    /**
     * List the allowed included embed types.  If null all are allowed.
     * @var array
     */
    private static $allowed_embed_types = [
        'video',
        'photo'
    ];

    /**
     * Defines tab to insert the embed fields into.
     * @var string
     */
    private static $embed_tab = 'Main';

    /**
     * @param array $properties
     * @return mixed|DBHTMLText
     */
    public function FieldHolder($properties = [])
    {
        $name = $this->getName();
        $fields = [
            CheckboxField::create(
                $name . '[autoplay]',
                _t(self::class.'AUTOPLAY', 'Autoplay video?')
            )->setValue($this->object->getField('Autoplay')),

            CheckboxField::create(
                $name . '[loop]',
                _t(self::class.'LOOP', 'Loop video?')
            )->setValue($this->object->getField('Loop')),

            CheckboxField::create(
                $name.'[controls]',
                _t(self::class.'CONTROLS', 'Show player controls?')
            )->setValue($this->object->getField('Controls'))
        ];

        return CompositeField::create(array_merge([
            LiteralField::create(
                $name.'Options',
                parent::FieldHolder($properties)
            )
        ], $fields));
    }
}
