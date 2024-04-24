<?php

namespace A2nt\CMSNiceties\Tasks;

use SilverStripe\Assets\File;
use SilverStripe\Dev\BuildTask;

class BrokenFilesTask extends BuildTask
{
    protected $title = 'Broken Files Task';

    protected $description = 'Broken files report';

    protected $enabled = true;

    public function run($request)
    {
        $files = File::get();
        $i = 0;
        foreach ($files as $file) {
            if ($file->exists()) {
                echo '<b>'.$file->getField('Name').'</b><br/>';
                $file->publishRecursive();
            }

            $i++;
        }

        die('Done!');
    }
}
