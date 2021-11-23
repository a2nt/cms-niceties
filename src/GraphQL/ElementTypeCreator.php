<?php

namespace A2nt\CMSNiceties\GraphQL;

if (!class_exists('SilverStripe\GraphQL\TypeCreator', true)) {
    return;
}

use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use SilverStripe\GraphQL\TypeCreator;

class ElementTypeCreator extends TypeCreator
{
    public function attributes()
    {
        return [
            'name' => 'element'
        ];
    }

    public function fields()
    {
        return [
            '_id' => ['type' => Type::nonNull(Type::id()),'resolve' => static function ($object) {
                return $object->ID;
            }],
            'ID' => ['type' => Type::nonNull(Type::id())],
            'Title' => ['type' => Type::string()],
            'ParentID' => ['type' => Type::id()],
            'Render' => [
                'type' => Type::string(),
                'resolve' => static function ($object, array $args, $context, ResolveInfo $info) {
                    return $object->getController()->forTemplate()->HTML();
                }
            ],
        ];
    }
}
