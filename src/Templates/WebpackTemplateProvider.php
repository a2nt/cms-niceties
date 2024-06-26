<?php

/** @noinspection PhpUnusedPrivateFieldInspection */

/**
 * Directs assets requests to Webpack server or to static files
 */

namespace A2nt\CMSNiceties\Templates;

use SilverStripe\Core\Manifest\ModuleManifest;
use SilverStripe\View\SSViewer;
use SilverStripe\View\TemplateGlobalProvider;
use SilverStripe\View\Requirements;
use SilverStripe\Control\Director;
use SilverStripe\Control\Controller;
use SilverStripe\Core\Config\Config;

class WebpackTemplateProvider implements TemplateGlobalProvider
{
    /**
     * @var int port number
     */
    private static $port = 3000;

    /**
     * @var string host name
     */
    private static $hostname = 'localhost';

    /**
     * @var string assets static files directory
     */
    private static $dist = 'client/dist';
    private static $webp = false;
    private static $absolute_path = false;

    /**
     * @return array
     */
    public static function get_template_global_variables(): array
    {
        return [
            'WebpackDevServer' => 'isActive',
            'WebpackCSS' => 'loadCSS',
            'WebpackJS' => 'loadJS',
            'ResourcesURL' => 'resourcesURL',
            'ProjectName' => 'themeName',
        ];
    }

    /**
     * Load CSS file
     * @param $path
     */
    public static function loadCSS($path): void
    {
        /*if (self::isActive()) {
            return;
        }*/

        Requirements::css(self::_getPath($path));
    }

    /**
     * Load JS file
     * @param $path
     */
    public static function loadJS($path): void
    {
        Requirements::javascript(self::_getPath($path));
    }

    public static function projectName(): string
    {
        return Config::inst()->get(ModuleManifest::class, 'project');
    }

    public static function mainTheme()
    {
        $themes = Config::inst()->get(SSViewer::class, 'themes');
        if (!is_array($themes)) {
            return;
        }

        $theme = null;
        foreach ($themes as $t) {
            if ($t  !== '$public' && $t !== '$default') {
                $theme = $t;
                break;
            }
        }

        return $theme;
    }

    public static function resourcesURL($link = null): string
    {
        $cfg = self::config();

        if ($cfg['webp'] && !self::isActive()) {
            $link = str_replace(['.png','.jpg','.jpeg'], '.webp', $link);
        }

        return Controller::join_links(
            Director::baseURL(),
            RESOURCES_DIR,
            self::projectName(),
            $cfg['dist'],
            'img',
            $link
        );
    }


    /**
     * Checks if dev mode is enabled and if webpack server is online
     * @return bool
     */
    public static function isActive(): bool
    {
        $cfg = self::config();
        return Director::isDev() && @fsockopen(
            $cfg['HOSTNAME'],
            $cfg['PORT']
        );
    }

    protected static function _getPath($path): string
    {
        return self::isActive() && strpos($path, '//') === false ?
            self::_toDevServerPath($path) :
            self::toPublicPath($path);
    }

    protected static function _toDevServerPath($path): string
    {
        $cfg = self::config();
        return sprintf(
            '%s%s:%s/%s',
            ($cfg['HTTPS'] ? 'https://' : 'http://'),
            $cfg['HOSTNAME'],
            $cfg['PORT'],
            basename($path)
            //Controller::join_links($cfg['APPDIR'], $cfg['SRC'], basename($path))
        );
    }

    public static function toPublicPath($path): string
    {
        $cfg = self::config();
        if (strpos($path, '//') || strpos($path, '/')) {
            return $path;
        }

        $link = Controller::join_links(
            RESOURCES_DIR,
            self::projectName(),
            $cfg['dist'],
            (strpos($path, '.css') ? 'css' : 'js'),
            $path
        );

        if ($cfg['absolute_path']) {
            $link = Director::absoluteURL($link);
        }

        return $link;
    }

    public static function config(): array
    {
        return Config::inst()->get(__CLASS__);
    }
}
