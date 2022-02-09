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
        $cfg = DB::getConfig();

        /*try {
            ob_clean();
        } catch (Exception $e) {
        }*/

        //header('Content-Disposition: attachment; filename="backup-'.date('d-m-Y').'.sql"');
        $dump = passthru('mysqldump -u '.$cfg['username'].' --password="'.$cfg['password'].'" '.$cfg['database'], true);
        var_dump($dump);

        exit(0);
    }
}
