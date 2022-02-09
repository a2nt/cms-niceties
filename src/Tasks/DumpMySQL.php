<?php

namespace A2nt\CMSNiceties\Tasks;

use SilverStripe\Assets\File;
use SilverStripe\Dev\BuildTask;
use SilverStripe\ORM\DB;

class DumpMySQL extends BuildTask
{
    protected $title = 'Dump MySQL Task';
    protected $description = 'Create MySQL dump';
    protected $enabled = true;

    public function run($request)
    {
        $fileName = 'backup-'.date('d-m-Y').'.sql';
        $cfg = DB::getConfig();

        try {
            ob_clean();
        } catch (Exception $e) {
        }

        // check if gzip is on
        try {
            if (count(array_intersect(['mod_deflate', 'mod_gzip'], apache_get_modules())) > 0) {
                $fileName .= '.gz';
            }
        } catch (Exception $e) {
        }

        header('Content-Disposition: attachment; filename="'.$fileName.'"');
        passthru('mysqldump -u '.$cfg['username'].' --password="'.$cfg['password'].'" '.$cfg['database']);
        exit(0);
    }
}
