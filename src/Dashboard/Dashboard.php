<?php

namespace A2nt\CMSNiceties\Dashboard;

use SilverStripe\Admin\LeftAndMain;
use SilverStripe\Assets\File;
use SilverStripe\CMS\Model\SiteTree;
use SilverStripe\ORM\ArrayList;
use SilverStripe\UserForms\Model\Submission\SubmittedForm;

class Dashboard extends LeftAndMain
{
    private static $menu_title = "Dashboard";
    private static $url_segment = "dashboard";
    private static $menu_priority = 100;
    private static $url_priority = 30;

    private static $menu_icon_class = 'font-icon-dashboard';
    private static $managed_models = [
        SubmittedForm::class,
    ];

    protected static function getRecentObjects($class, $limit = 10)
    {
        return $class::get()
            ->sort('LastEdited DESC')
            ->limit($limit);
    }

    public function RecentPages()
    {
        return self::getRecentObjects(SiteTree::class);
    }

    public function RecentFiles()
    {
        return self::getRecentObjects(File::class);
    }

    public function RecentObjects()
    {
        $models = self::config()->get('managed_models');
        if (!count($models)) {
            return null;
        }

        $objects = [];
        foreach ($models as $model) {
            $objects[] = [
                'Title' => singleton($model)->plural_name(),
                'Objects' => self::getRecentObjects($model),
            ];
        }

        return ArrayList::create($objects);
    }
}
