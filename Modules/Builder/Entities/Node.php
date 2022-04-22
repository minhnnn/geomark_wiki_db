<?php

namespace Modules\Builder\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Node extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'key',
        'type',
        'parent_id',
        'binding_table',

    ];

    public function children()
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    public function createNode(array $nodeData)
    {
        return self::create($nodeData);
    }

    public function findNode($nodeId)
    {
        return self::find($nodeId);
    }

    public function updateNode($id, array $updateData)
    {
        $node = self::find($id);
        if (!$node) {
            return false;
        }
        $node->update($updateData);
        return $node;
    }

    public function removeAllChildren($id)
    {
        $node = self::find($id);
        if ($node) {
            return $node->children()->delete();
        }
        return false;
    }
}
