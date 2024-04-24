<?php

namespace A2nt\CMSNiceties\Tasks;

use SilverStripe\Assets\File;
use SilverStripe\Dev\BuildTask;

class PublishAllFiles extends BuildTask
{
    protected $title = 'Publish All Files Task';

    protected $description = 'Publish All Files';

    protected $enabled = true;

    public function run($request)
    {
        $files = File::get();
        $i = 0;
        foreach ($files as $file) {
            if ($file->exists()) {
                $file->publishFile();
            }
            $i++;
        }

        die('Done!');
    }
}
