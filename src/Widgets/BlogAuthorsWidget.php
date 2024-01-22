<?php

namespace A2nt\CMSNiceties\Widgets;

use SilverStripe\Blog\Model\Blog;
use SilverStripe\Blog\Model\BlogPost;
use SilverStripe\Core\Convert;
use SilverStripe\Forms\DropdownField;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\NumericField;
use SilverStripe\ORM\ArrayList;
use SilverStripe\Security\Member;
use SilverStripe\Widgets\Model\Widget;

if (!class_exists(Widget::class)) {
    return;
}

/**
 * Class \A2nt\CMSNiceties\Widgets\BannerWidget
 *
 * @property int $ImageID
 * @property int $LinkID
 * @method \SilverStripe\Assets\Image Image()
 * @method \Sheadawson\Linkable\Models\Link Link()
 */
class BlogAuthorsWidget extends Widget
{
    private static $title = 'Blog Authors';
    private static $cmsTitle = 'Authors';
    private static $description = 'Shows banner Blog Authors.';
    private static $table_name = 'BlogAuthorsWidget';

    private static $db = [
        'Limit' => 'Int',
        'Order' => 'Varchar',
        'Direction' => 'Varchar',
    ];

    private static $has_one = [
        'Blog' => Blog::class
    ];

    public function getCMSFields()
    {
        $this->beforeUpdateCMSFields(function (FieldList $fields) {
            $fields[] = DropdownField::create(
                'BlogID',
                _t(__CLASS__ . '.Blog', 'Blog'),
                Blog::get()->map()
            );

            $fields[] = NumericField::create(
                'Limit',
                _t(__CLASS__ . '.Limit', 'Limit'),
                0
            )
                ->setDescription(
                    _t(
                        __CLASS__ . '.Limit_Description',
                        'Limit the number of tags shown by this widget (set to 0 to show all tags).'
                    )
                )
                ->setMaxLength(3);

            $fields[] = DropdownField::create(
                'Order',
                _t(__CLASS__ . '.Sort', 'Sort'),
                [
                    'FirstName' => 'First Name',
                    'Surname' => 'Last Name',
                    'LastEdited' => 'Updated',
                ],
            )
                ->setDescription(
                    _t(__CLASS__ . '.Sort_Description', 'Change the order of tags shown by this widget.')
                );

            $fields[] = DropdownField::create(
                'Direction',
                _t(__CLASS__ . '.Direction', 'Direction'),
                ['ASC' => 'Ascending', 'DESC' => 'Descending']
            )
                ->setDescription(
                    _t(
                        __CLASS__ . '.Direction_Description',
                        'Change the direction of ordering of tags shown by this widget.'
                    )
                );
        });

        return parent::getCMSFields();
    }

    /**
     * @return DataList
     */
    public function getAuthors()
    {
        $parent = $this->Blog();

        if (!$parent) {
            return [];
        }

        $blogID = $parent->ID;

        $posts = BlogPost::get()
            ->filter('ParentID', $blogID);

        $authorIDs = [];
        foreach ($posts as $post) {
            $authorIDs = array_merge(
                $authorIDs,
                array_keys(
                    $post->Authors()
                        ->map()
                        ->toArray()
                )
            );
        }

        $authorIDs = array_unique($authorIDs);
        $query = Member::get()->filter(['ID' => $authorIDs]);

        if ($this->Limit) {
            $query = $query->limit(Convert::raw2sql($this->Limit));
        }

        if ($this->Order && $this->Direction) {
            $query = $query->sort(
                Convert::raw2sql($this->Order),
                Convert::raw2sql($this->Direction)
            );
        }

        // Update all authors
        $items = ArrayList::create();
        foreach ($query as $author) {
            // Add link for each author
            $author = $author->customise([
                'URL' => $parent->ProfileLink($author->URLSegment),
            ]);
            $items->push($author);
        }

        return $items;
    }
}
