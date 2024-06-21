<?php

/** @noinspection PhpUnusedPrivateFieldInspection */

namespace A2nt\CMSNiceties\Templates;

use SilverStripe\Control\Controller;
use SilverStripe\View\TemplateGlobalProvider;
use SilverStripe\View\Requirements;
use SilverStripe\Core\Config\Config;
use SilverStripe\Control\Director;
use SilverStripe\Core\Path;
use SilverStripe\FontAwesome\FontAwesomeField;

class DeferredRequirements implements TemplateGlobalProvider
{
    private static $preloadFont = [];
    private static $preloadJS = [];
    private static $preloadCSS = [];
    private static $blocked = [];
    private static $css = [];
    private static $js = [];
    private static $deferred = false;
    private static $static_domain;
    private static $version;
    private static $nojquery = false;
    private static $jquery_version = '3.4.1';
    private static $nofontawesome = false;
    private static $fontawesome_svg = false;
    private static $custom_requirements = [];

    /**
     * @return array
     */
    public static function get_template_global_variables(): array
    {
        return [
            'AutoRequirements' => 'Auto',
            'DeferedCSS' => 'loadCSS',
            'DeferedJS' => 'loadJS',
            'WebpackActive' => 'webpackActive',
            'EmptyImgSrc' => 'emptyImageSrc',
            'HttpMethod' => 'httpMethod',
            'Preloads' => 'Preloads',
        ];
    }

    public static function httpMethod(): string
    {
        $ctl = Controller::curr();
        if (!$ctl) {
            return null;
        }

        $req = $ctl->getRequest();
        return ($req) ? $req->httpMethod() : null;
    }

    public static function setupAuto($class = false): void
    {
        $config = Config::inst()->get(self::class);
        $projectName = WebpackTemplateProvider::projectName();
        $mainTheme = WebpackTemplateProvider::mainTheme();
        $mainTheme = $mainTheme ?: $projectName;

        $dir = Path::join(
            Director::publicFolder(),
            RESOURCES_DIR,
            $projectName,
            'client',
            'dist'
        );
        $cssPath = Path::join($dir, 'css');
        $jsPath = Path::join($dir, 'js');

        // Initialization
        Requirements::block(THIRDPARTY_DIR.'/jquery/jquery.js');
        /*if (defined('FONT_AWESOME_DIR')) {
            Requirements::block(FONT_AWESOME_DIR.'/css/lib/font-awesome.min.css');
        }*/
        Requirements::set_force_js_to_bottom(true);

        // Main libs
        if (!$config['nojquery']) {
            self::loadJS(
                'https://ajax.googleapis.com/ajax/libs/jquery/'
                .$config['jquery_version'].'/jquery.min.js'
            );
        }

        if (!$config['noreact']) {
            if (!Director::isDev()) {
                self::loadJS('https://unpkg.com/react@17/umd/react.production.min.js');
                self::loadJS('https://unpkg.com/react-dom@17/umd/react-dom.production.min.js');
            } else {
                self::loadJS('https://unpkg.com/react@17/umd/react.development.js');
                self::loadJS('https://unpkg.com/react-dom@17/umd/react-dom.development.js');
            }
        }

        self::loadCSS($mainTheme.'.css');

        // hot reloading
        /*if (self::webpackActive()) {
            self::loadJS('hot.js');
        }*/

        self::loadJS($mainTheme.'.js');

        // Custom controller requirements
        $loadRequirement = static function ($file) {
            if (strpos($file, '.css')) {
                self::loadCSS($file);
            }
            if (strpos($file, '.js')) {
                self::loadJS($file);
            }
        };

        $curr_class = $class ?: get_class(Controller::curr());
        if (isset($config['custom_requirements'][$curr_class])) {
            foreach ($config['custom_requirements'][$curr_class] as $file) {
                if (is_array($file)) {
                    foreach ($file as $f) {
                        $loadRequirement($f);
                    }
                } else {
                    $loadRequirement($file);
                }
            }
        }

        $curr_class = str_replace('\\', '.', $curr_class);

        // Controller requirements
        $themePath = Path::join($cssPath, $mainTheme.'_'.$curr_class . '.css');
        $projectPath = Path::join($cssPath, $projectName.'_'.$curr_class . '.css');
        if ($mainTheme && file_exists($themePath)) {
            self::loadCSS($mainTheme.'_'.$curr_class . '.css');
        } elseif (file_exists($projectPath)) {
            self::loadCSS($projectName.'_'.$curr_class . '.css');
        }

        $themePath = Path::join($jsPath, $mainTheme.'_'.$curr_class . '.js');
        $projectPath = Path::join($jsPath, $projectName.'_'.$curr_class . '.js');
        if ($mainTheme && file_exists($themePath)) {
            self::loadJS($mainTheme.'_'.$curr_class . '.js');
        } elseif (file_exists($projectPath)) {
            self::loadJS($projectName.'_'.$curr_class . '.js');
        }

        // App libs
        if (!$config['nofontawesome']) {
            $v = !isset($config['fontawesome_version']) || !$config['fontawesome_version']
                ? Config::inst()->get(FontAwesomeField::class, 'version')
                : $config['fontawesome_version'];

            if ($config['fontawesome_svg']) {
                Requirements::customScript('FontAwesomeConfig={searchPseudoElements:true}');
                self::loadJS('https://use.fontawesome.com/releases/v'.$v.'/js/all.js');
            } else {
                self::loadCSS('https://use.fontawesome.com/releases/v'.$v.'/css/all.css');
            }
        }
    }

    public static function Auto(string | bool $class = false): string
    {
        self::setupAuto($class);
        return self::forTemplate();
    }

    public static function block(array | string $path): void
    {
        if (!is_array($path)) {
            $path = [$path];
        }
        self::$blocked = array_merge(self::$blocked, $path);
    }

    public static function addPreloadCSS(array | string $path): void
    {
        if (!is_array($path)) {
            $path = [$path];
        }
        self::$preloadCSS = array_merge(self::$preloadCSS, $path);
    }

    public static function addPreloadJS(array | string $path): void
    {
        if (!is_array($path)) {
            $path = [$path];
        }
        self::$preloadJS = array_merge(self::$preloadJS, $path);
    }

    public static function addPreloadFont(array | string $path): void
    {
        self::$preloadFont = array_merge(self::$preloadFont, $path);
    }

    private static function getPreloadLine(string $url, string | null $as = null, string | null $type = null)
    {
        $crossorigin = strpos('//', $url) ? ' crossorigin ' : '';
        $type = $type ?: ' type="'.$type.'" ';

        return '<link rel="preload" href="'.$url.'" as="'.$as.'"'.$type.$crossorigin.'/>';
    }

    public static function Preloads(): string
    {
        self::setupAuto();

        self::$css = array_unique(self::$css);
        $html = '';

        $csses = array_merge(self::$css, self::$preloadCSS);
        foreach ($csses as $css) {
            $url = self::get_url($css);
            $html .= self::getPreloadLine($url, 'style');
        }
        unset($csses, $css);

        $jss = array_merge(self::$js, self::$preloadJS);
        foreach ($jss as $js) {
            $url = self::get_url($js);
            $html .= self::getPreloadLine($url, 'script');
        }
        unset($jss, $js);

        $fonts = self::$preloadFont;
        foreach ($fonts as $font) {
            $type = 'font/woff2';

            if (str_contains($font, 'ttf')) {
                $type = 'font/ttf';
            }

            $url = self::get_url($font);
            $html .= self::getPreloadLine($url, 'font', $type);
        }
        unset($fonts, $font);

        return $html;
    }

    public static function loadCSS($css): void
    {
        $external = (mb_strpos($css, '//') === 0 || mb_strpos($css, 'http') === 0);
        //if (self::getDeferred() && !self::webpackActive()) {
        if ((self::getDeferred() && !self::webpackActive()) || $external) {
            self::$css[] = $css;
        } else {
            WebpackTemplateProvider::loadCSS(self::get_url($css));
        }
    }

    public static function loadJS($js): void
    {
        /*$external = (mb_substr($js, 0, 2) === '//' || mb_substr($js, 0, 4) === 'http');
        if ($external || (self::getDeferred() && !self::_webpackActive())) {*/
        // webpack supposed to load external JS
        if (self::getDeferred() && !self::webpackActive()) {
            self::$js[] = $js;
        } else {
            WebpackTemplateProvider::loadJS(self::get_url($js));
        }
    }

    public static function webpackActive(): bool
    {
        return WebpackTemplateProvider::isActive();
    }

    public static function setDeferred($bool): void
    {
        Config::inst()->set(__CLASS__, 'deferred', $bool);
    }

    public static function getDeferred(): bool
    {
        return self::config()['deferred'];
    }

    public static function forTemplate(): string
    {
        $result = '';
        self::$css = array_unique(self::$css);
        foreach (self::$css as $css) {
            $url = self::get_url($css);
            $result .= '<i class="defer-cs" data-load="' . self::get_url($css) . '"></i>';
            //$result .= '<link rel="preload" href="'.$url.'" as="style" onload="this.rel=\'stylesheet\'">';
            $result .= '<noscript><link rel="stylesheet" href="'.$url.'"></noscript>';
        }

        self::$js = array_unique(self::$js);
        foreach (self::$js as $js) {
            $result .= '<i class="defer-sc" data-load="' . self::get_url($js) . '"></i>';
        }

        $result .=
            '<script>function lsc(a,b){var c=document.createElement("script");c.readyState'
            .'?c.onreadystatechange=function(){"loaded"!=c.readyState&&"complete"!=c.readyState||(c.onreadystatechange=null,b())}'
            .':c.onload=function(){b()},c.src=a,document.getElementsByTagName("body")[0].appendChild(c)}'
            .'function lscd(a){a<s.length-1&&(a++,lsc(s.item(a).getAttribute("data-load"),function(){lscd(a)}))}'
            .'for(var s=document.getElementsByClassName("defer-cs"),i=0;i<s.length;i++){var b=document.createElement("link");b.rel="stylesheet",'
            .'b.type="text/css",b.href=s.item(i).getAttribute("data-load"),b.media="all";var c=document.getElementsByTagName("body")[0];'
            .'c.appendChild(b)}var s=document.getElementsByClassName("defer-sc"),i=0;if(s.item(i)!==null)lsc(s.item(i).getAttribute("data-load"),function(){lscd(i)});'
            .'</script>';

        return $result;
    }

    private static function get_url($url): string
    {
        $config = self::config();

        // external URL
        if (strpos($url, '//') !== false) {
            return $url;
        }

        $projectName = WebpackTemplateProvider::projectName();
        $path = Path::join(
            Director::publicFolder(),
            RESOURCES_DIR,
            $projectName,
            'client',
            'dist',
            (strpos($url, '.js') ? 'js' : 'css'),
            $url
        );

        $absolutePath = Director::getAbsFile($path);
        $hash = sha1_file($absolutePath);

        $version = $config['version'] ? '&v='.$config['version'] : '';
        //$static_domain = $config['static_domain'];
        //$static_domain = $static_domain ?: '';

        return Controller::join_links(WebpackTemplateProvider::toPublicPath($url), '?m='.$hash.$version);
    }

    public static function emptyImageSrc(): string
    {
        return 'data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7';
    }

    public static function config(): array
    {
        return Config::inst()->get(__CLASS__);
    }
}
