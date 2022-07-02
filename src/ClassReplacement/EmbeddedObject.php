<?php

/*
 * Replace outdated class for Dynamic\FlexSlider\Model\SlideImage
 */
namespace Sheadawson\Linkable\Models;

use gorriecoe\Embed\Models\Embed;

class EmbeddedObject extends Embed
{
    private static $table_name = 'EmbeddedObject';
}
