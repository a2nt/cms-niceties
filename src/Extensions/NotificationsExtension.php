<?php

namespace A2nt\CMSNiceties\Extensions;

use SilverStripe\Forms\CheckboxField;
use SilverStripe\Forms\GridField\GridField;
use SilverStripe\Forms\GridField\GridFieldConfig;
use SilverStripe\Forms\GridField\GridFieldDeleteAction;
use SilverStripe\Forms\GridField\GridFieldDetailForm;
use SilverStripe\Forms\GridField\GridFieldEditButton;
use SilverStripe\Forms\GridField\GridFieldToolbarHeader;
use SilverStripe\Forms\HeaderField;
use SilverStripe\Forms\LiteralField;
use SilverStripe\ORM\DataExtension;
use SilverStripe\Forms\FieldList;
use A2nt\CMSNiceties\Models\Notification;
use Symbiote\GridFieldExtensions\GridFieldAddNewInlineButton;
use Symbiote\GridFieldExtensions\GridFieldEditableColumns;
use Symbiote\GridFieldExtensions\GridFieldTitleHeader;

/**
 * Class \A2nt\CMSNiceties\Extensions\NotificationsExtension
 *
 * @property \A2nt\CMSNiceties\Extensions\NotificationsExtension $owner
 * @property boolean $ShowNotifications
 * @method \SilverStripe\ORM\DataList|\A2nt\CMSNiceties\Models\Notification[] Notifications()
 */
class NotificationsExtension extends DataExtension
{
    private static $db = [
        'ShowNotifications' => 'Boolean(1)',
    ];

    private static $has_many = [
        'Notifications' => Notification::class,
    ];

    public function updateCMSFields(FieldList $fields)
    {
        $tab = $fields->findOrMakeTab('Root.Notifications');

        if (!$this->owner->exists()) {
            $tab->push(LiteralField::create(
                'NotificationsNotice',
                '<p class="message notice">The object must be saved before notifications can be added</p>'
            ));

            return null;
        }

        $items = $this->owner->Notifications();

        $config = GridFieldConfig::create();
        $config->addComponents([
            new GridFieldToolbarHeader(),
            new GridFieldTitleHeader(),
            new GridFieldEditableColumns(),
            new GridFieldAddNewInlineButton('toolbar-header-right'),
            new GridFieldDetailForm(),
            new GridFieldEditButton(),
            new GridFieldDeleteAction(),
        ]);

        $tab->setChildren(FieldList::create(
            HeaderField::create('NotificationsHeader', 'Notifications'),
            LiteralField::create(
                'CurrentNotifications',
                '<b>Current:</b>'
                .$this->owner->renderWith('App\\Objects\\NotificationsList')
            ),
            CheckboxField::create('ShowNotifications'),
            GridField::create(
                'Notifications',
                '',
                $items,
                $config
            )
        ));
    }

    public function NotificationsToday()
    {
        $items = $this->owner->Notifications();
        $time = time();

        return $items->where("AlwaysOn='1' OR (DateOn <= '".date('Y-m-d', $time)."' AND DateOff >= '".date('Y-m-d', $time)."')");
    }
}
