<?php


namespace A2nt\CMSNiceties\Widgets;

use SilverStripe\AssetAdmin\Forms\UploadField;
use SilverStripe\Assets\Image;
use SilverStripe\Forms\HTMLEditor\HTMLEditorField;
use SilverStripe\Widgets\Model\Widget;

if (!class_exists(Widget::class)) {
    return;
}

/**
 * Class \A2nt\CMSNiceties\Widgets\ContentWidget
 *
 * @property string $Text
 */
class ContentWidget extends Widget
{
    private static $title = 'Content';
    private static $cmsTitle = 'Content';
    private static $description = 'Shows text content.';
    private static $icon = '<i class="icon font-icon-block-content"></i>';
    private static $table_name = 'ContentWidget';

    private static $db = [
        'Text' => 'HTMLText',
    ];

    public function getCMSFields()
    {
        $fields = parent::getCMSFields();

        $fields->push(HTMLEditorField::create('Text'));

        return $fields;
    }
}
