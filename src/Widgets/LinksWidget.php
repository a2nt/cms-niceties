<?php

namespace A2nt\CMSNiceties\Widgets;

use gorriecoe\Link\Models\Link;
use gorriecoe\LinkField\LinkField;
use SilverStripe\AssetAdmin\Forms\UploadField;
use SilverStripe\Assets\Image;
use SilverStripe\Forms\GridField\GridField;
use SilverStripe\Forms\GridField\GridFieldConfig_RecordEditor;
use SilverStripe\Forms\LiteralField;
use SilverStripe\Widgets\Model\Widget;

if (!class_exists(Widget::class)) {
    return;
}

/**
 * Class \A2nt\CMSNiceties\Widgets\LinksWidget
 *
 * @method \SilverStripe\ORM\ManyManyList|\Sheadawson\Linkable\Models\Link[] Links()
 */
class LinksWidget extends Widget
{
    private static $title = 'Links';
    private static $cmsTitle = 'Links';
    private static $description = 'Shows listing of links.';
    private static $icon = '<i class="icon font-icon-list"></i>';
    private static $table_name = 'LinksWidget';

    private static $many_many = [
        'Links' => Link::class,
    ];

    private static $many_many_extraFields = [
        'Links' => [
            'Sort' => 'Int',
        ],
    ];

    private static $owns = [
        'Links',
    ];

    public function getCMSFields()
    {
        //die('aaa');
        $fields = parent::getCMSFields();

        if($this->ID) {
            $fields->push(LinkField::create(
                'Links',
                'Links',
                $this
            ));
        } else {
            $fields->push(
                LiteralField::create(
                    'Note',
                    '<p class="alert alert-warning"><b>Note:</b> The widget needs to be saved before adding a link.'
                    .' Enter the Title and click "+ Create" button at the bottom left corner of the screen</p>'
                )
            );
        }

        return $fields;
    }
}
