<?php

namespace Modules\Builder\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Modules\Builder\Entities\Node;

class NodeTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        $allNodeData = [
            [
                'name' => 'OptionTemplate',
                'key' => 'option_template',
                'type' => 'object',
                'parent_id' => 0,
                'binding_table' => 'option_templates',
            ],
            [
                'name' => 'ProductModel',
                'key' => 'product_model',
                'type' => 'object',
                'parent_id' => 0,
                'binding_table' => 'product_models',
            ],
            [
                'name' => 'Product',
                'key' => 'product',
                'type' => 'object',
                'parent_id' => 0,
                'binding_table' => 'products',
            ],
            [
                'name' => 'Category',
                'key' => 'category',
                'type' => 'object',
                'parent_id' => 0,
                'binding_table' => 'categories',
            ],
        ];

        foreach($allNodeData as $node) {
            if(Node::where('key','!=',$node['key'])) {
                Node::create($node);
            }
        }
    }
}
