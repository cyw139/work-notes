<?php

namespace App\Models\Kspx;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Kalnoy\Nestedset\NodeTrait;

class TreeBaseModel extends KspxBaseModel
{
    use HasFactory, SoftDeletes, NodeTrait;

    const POSITIONS = ['after', 'before'];

    public function getParentIdName()
    {
        return 'pid';
    }
    /**
     * 获取节点深度
     * @param $id
     * @return int
     */
    public static function nodeDepth($id)
    {
        $node = self::withDepth()->find($id);
        $node_depth = $node ? $node->depth : 0;

        return $node_depth;
    }

    /**
     * 某节点A是否是某节点B的直接子节点
     * @param $node 节点ID
     * @param $parent_node 父节点ID
     */
    public static function isChild($node, $parent_node)
    {
        if (!$node) {
            return false;
        }

        if (!$parent_node) {
            return $node->isRoot();
        }

        return $node->ischildOf($parent_node);
    }

}
