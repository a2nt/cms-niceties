<?php

namespace A2nt\CMSNiceties\Tasks;

use Page;
use SilverStripe\Dev\BuildTask;
use SilverStripe\Widgets\Model\WidgetArea;

class BrokenWidgetsTask extends BuildTask
{
    protected $title = 'Broken Widgets Task';
    protected $description = 'Broken widgets reset';
    protected $enabled = true;

    public function run($request)
    {
        $pages = Page::get();
        $wIDs = array_keys($pages->map('ID', 'SideBarID')->toArray());

        // delete orphaned widgets areas
        $items = WidgetArea::get()->exclude('ID', $wIDs);
        foreach ($items as $i) {
            $i->delete();
        }

        // reset empty widget areas
        foreach ($pages as $p) {
            $w = $p->Sidebar();
            if (!$w->ID) {
                $p->setField('SideBarID', '0');
                $p->write();

                continue;
            }

            $widgets = $w->Widgets();

            if (!$widgets->count()) {
                $w->delete();
                $p->setField('SideBarID', '0');
                $p->write();
            }
        }

        die('Done!');
    }
}
