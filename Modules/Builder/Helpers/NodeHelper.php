<?php

namespace Modules\Builder\Helpers;

use Modules\Builder\Entities\Node;

class NodeHelper
{
    public static function getAllObject()
    {
        return Node::where('type', 'object')->orderBy('name')->get();
    }
}
