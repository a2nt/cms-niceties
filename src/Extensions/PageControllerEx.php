<?php

namespace A2nt\CMSNiceties\Extensions;

use A2nt\ElementalBasics\Models\TeamMember;
use DNADesign\Elemental\Models\ElementalArea;
use DNADesign\Elemental\Models\ElementContent;
use DNADesign\ElementalUserForms\Control\ElementFormController;
use Page;
use SilverStripe\CMS\Model\SiteTree;
use SilverStripe\Control\Controller;
use SilverStripe\Control\Director;
use SilverStripe\Control\HTTPRequest;
use SilverStripe\Core\Config\Config;
use SilverStripe\Core\Extension;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\Form;
use SilverStripe\Forms\FormAction;
use SilverStripe\Forms\RequiredFields;
use SilverStripe\Forms\TextField;
use SilverStripe\ORM\ArrayList;
use SilverStripe\ORM\FieldType\DBDatetime;
use SilverStripe\ORM\PaginatedList;
use SilverStripe\View\ArrayData;

class PageControllerEx extends Extension
{
    private static $allowed_actions = [
        'SearchForm',
    ];

    private $site_message;

    public static function DefaultContainer()
    {
        return SiteTreeExtension::DefaultContainer();
    }

    public function CurrentTime()
    {
        return DBDatetime::now();
    }

    public function isDev()
    {
        return Director::isDev();
    }

    public function getSiteWideMessage()
    {
        $obj = $this->owner;

        $request = $obj->getRequest();

        if ($request->isGET() && ! $this->site_message) {
            $session = $request->getSession();
            $this->site_message = $session->get('SiteWideMessage');
            $session->clear('SiteWideMessage');
        }

        return $this->site_message;
    }

    public function getParentRecursively()
    {
        $obj = $this->owner;
        return $obj->Level(1);
    }

    public static function setSiteWideMessage($message, $type, $request = null)
    {
        $request = $request ? $request : Controller::curr()->getRequest();
        $request->getSession()->set(
            'SiteWideMessage',
            ArrayData::create([
                'Message' => $message,
                'Type' => $type,
            ])
        );

        return true;
    }

    private static $searchable_elements = [
        ElementContent::class,
    ];

    private static $searchable_objects = [
        TeamMember::class,
    ];

    private $search_term;

    public function index(HTTPRequest $request)
    {
        $obj = $this->owner;
        $search = $request->getVar('q');
        if ($search) {
            return $this->doSearch($search);
        }

        return $obj->render();
    }

    public function setAction($action)
    {
        $obj = $this->owner;
        $obj->action = $action;
    }

    public function ElementalArea()
    {
        $obj = $this->owner;
        if (!$obj->getAction() || 'index' === $obj->getAction()) {
            return ElementalArea::get()->byID($obj->getField('ElementalAreaID'));
        }

        return false;
    }

    public function CurrentElement()
    {
        $controller_curr = Controller::curr();

        if (is_a($controller_curr, ElementFormController::class)) {
            return $controller_curr;
        }

        return false;
    }

    public function SearchForm(): Form
    {
        $obj = $this->owner;
        $config = $obj->SiteConfig();

        $form = Form::create(
            $obj,
            __FUNCTION__,
            FieldList::create(
                TextField::create('q', 'Search ...')
                    ->setAttribute('placeholder', 'What are you looking for?')
            ),
            FieldList::create(
                FormAction::create(
                    'doSearch',
                    'Find it!'
                )
                    ->setUseButtonTag(true)
                    ->addExtraClass('btn-secondary')
                    ->setButtonContent(
                        '<i class="fas fa-search"></i>'
                        . '<span class="sr-only">Search</span>'
                    )
            ),
            RequiredFields::create(['q'])
        )->setFormMethod('GET');

        $homePage = SiteTree::get()->filter('URLSegment', 'home')->first();
        if ($homePage) {
            $link = $homePage->Link();
            $link = ($link === '/') ? '/home/' : $link;
            $form->setFormAction($link);
        }
        $form->setLegend('Search at ' . $config->getField('Title'));

        if (Page::config()->get('search_disable_security_token')) {
            $form->disableSecurityToken();
        }

        return $form;
    }

    public function doSearch($data)
    {
        $obj = $this->owner;
        $this->search_term = is_array($data) ? $data['q'] : $data;

        return $obj->renderWith(['PageController_search', 'Page']);
    }

    public function SearchResults()
    {
        $term = $this->search_term;
        if (! $term) {
            return false;
        }

        $results = ArrayList::create();

        // get pages by title and content
        $pages = SiteTree::get()->filterAny([
            'Title:PartialMatch' => $term,
            'Content:PartialMatch' => $term,
        ])->exclude([
            'ClassName' => ErrorPage::class,
        ])->sort('Created DESC');

        $results->merge($pages);

        // get pages by elements
        $elements = self::getSearchObjects(
            Page::config()->get('searchable_elements'),
            $term
        );

        foreach ($elements as $element) {
            if (!is_a($element, \DNADesign\Elemental\Models\BaseElement::class)
                && !$element->hasMethod('getPage')) {
                continue;
            }

            $page = $element->getPage();
            if (! $page) {
                continue;
            }

            $results->push($page);
        }

        // get pages by objects
        $elements = self::getSearchObjects(
            Page::config()->get('searchable_objects'),
            $term
        );

        foreach ($elements as $element) {
            $page = $element->getPage();

            if (!$element->hasMethod('getPage')) {
                continue;
            }

            if (! $page) {
                continue;
            }

            $results->push($page);
        }

        $results->removeDuplicates();

        return ArrayData::create([
            'Title' => 'You searched for: "' . $term . '"',
            'Results' => PaginatedList::create($results),
        ]);
    }

    private static function getSearchObjects($classNames, $term): ArrayList
    {
        $elements = ArrayList::create();
        foreach ($classNames as $class) {
            $fields = Config::inst()->get($class, 'frontend_searchable_fields');
            if (!$fields) {
                continue;
            }

            $find = array_combine($fields, $fields);
            $find = array_map(static function () use ($term) {
                return $term;
            }, $find);

            $elements->merge($class::get()->filterAny($find)->sort('Created DESC'));
        }

        return $elements;
    }
}
