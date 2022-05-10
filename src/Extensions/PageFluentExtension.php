<?php


namespace A2nt\CMSNiceties\Extensions;

use SilverStripe\Blog\Forms\GridField\GridFieldConfigBlogPost;
use SilverStripe\Forms\FieldList;
use SilverStripe\ORM\DataExtension;
use TractorCow\Fluent\Model\Locale;

/**
 * Class \A2nt\CMSNiceties\Extensions\PageFluentExtension
 *
 * @property \A2nt\CMSNiceties\Extensions\PageFluentExtension $owner
 */
class PageFluentExtension extends DataExtension
{
    /**
     * Override default Fluent fallback
     *
     * @param string $query
     * @param string $table
     * @param string $field
     * @param Locale $locale
     */
    public function updateLocaliseSelect(&$query, $table, $field, Locale $locale)
    {
        // disallow elemental data inheritance in the case that published localised page instance already exists
        if ($field == 'ElementalAreaID' && $this->owner->isPublishedInLocale()) {
            $query = '"' . $table . '_Localised_' . $locale->getLocale() . '"."' . $field . '"';
        }
    }
}
