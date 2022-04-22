<?php

namespace Modules\Builder\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\Builder\Entities\Node;
use Nwidart\Modules\Facades\Module;
use Storage;

class NodeController extends Controller
{

    protected $nodeModel;
    /**
     * @var Node
     */
    private $nodeRepo;

    /**
     * NodeController constructor.
     * @param  Node  $nodeRepo
     */
    public function __construct(Node $nodeRepo)
    {

        $this->nodeRepo = $nodeRepo;
    }

    public function index(Request $request)
    {
        return redirect()->route('admin.node.show', ['node' => 0, 'root_id' => 0]);
    }


    public function create(Request $request)
    {
        return redirect()->route('admin.node.show', ['node' => 0, 'root_id' => 0]);
    }

    public function store(Request $request)
    {
        if ($request->get('form_action') == 'create') {
            $currentNode = $this->nodeRepo->createNode($request->all());
        } else {
            $currentNode = $this->nodeRepo->findNode($request->get('node_id'));
            $currentNode = $this->nodeRepo->updateNode($currentNode->id, $request->all());
        }

        if ((int) $currentNode->parent_id === 0) {
            return redirect()->route('admin.node.index');
        }
        return redirect()->to(route('admin.node.index').'?root_id='.$currentNode->parent_id);
    }

    public function show($nodeId, Request $request)
    {
        $tableArr = $this->getAllTables();

        $rootObj = [
            'id' => 0,
            'name' => 'Project',
            'type' => 'object',
            'key' => 'project',
            'binding_table' => '',
        ];

        if ($request->has('root_id') && $request->get('root_id')) {
            $rootNode = Node::find($request->root_id)->toArray();
            $currentNode = $this->nodeRepo->findNode($nodeId);
        } else {
            $rootNode = $rootObj;
            $currentNode = (object) $rootObj;
        }

        $allNode = Node::where('parent_id', $rootNode['id'])->get()->toArray();

        $rootNode['children'] = $allNode;
        $nodeChartData = $rootNode;

        $allRootChild = Node::where('type', 'object')->get()->toArray();

        $allTypes = [
            'number' => 'Number',
            'textarea' => 'Textarea',
            'object' => 'Object',
            'collection' => 'Collection',
        ];

        array_unshift($allRootChild, $rootObj);
        return view(
            'builder::node.create',
            compact('nodeChartData', 'allRootChild', 'tableArr', 'currentNode', 'allTypes')
        );
    }


    public function curdBuilder($nodeId, $action = '')
    {
        $modules = Module::all();

        $currentNode = Node::find($nodeId);
        $allFields = Node::where('parent_id', $currentNode->id)->get();
        return view('builder::node.build', compact('currentNode', 'allFields', 'modules'));
    }

    public function getFetchTable($nodeId)
    {
        $currentNode = Node::find($nodeId);
        if (!$currentNode || !$currentNode->binding_table) {
            return redirect()->back();
        }
        $allColumns = \DB::getSchemaBuilder()->getColumnListing($currentNode->binding_table);
        $ignoreColumns = [
            'id', 'created_at', 'updated_at', 'deleted_at'
        ];

        foreach ($allColumns as $key => $column) {
            if (in_array($column, $ignoreColumns)) {
                unset($allColumns[$key]);
            }
        }

        $allTypes = [
            'text' => 'Text',
            'number' => 'Number',
            'textarea' => 'Textarea',
            'object' => 'Object',
            'collection' => 'Collection',
        ];

        return view(
            'builder::node.fetch_table',
            compact('currentNode', 'allColumns', 'allTypes')
        );
    }

    public function postFetchTable($nodeId, Request $request)
    {
        $currentNode = $this->nodeRepo->findNode($nodeId);
        //remove all old children
        $this->nodeRepo->removeAllChildren($currentNode->id);
        $allColumns = $request->get('columns');
        foreach ($allColumns as $key => $column) {
            if (isset($column['select'])) {
                $newNodeData = [
                    'parent_id' => $currentNode->id,
                    'name' => $column['name'],
                    'key' => $key,
                    'type' => $column['type'],
                ];
                dump($newNodeData);
                $this->nodeRepo->createNode($newNodeData);
            }
        }
        return redirect()->route(
            'admin.node.show',
            ['node' => $currentNode->id, 'root_id' => $currentNode->parent_id]
        );
    }


    public function curdCreate($nodeId, Request $request)
    {
        $module = Module::find($request->module);
        $currentNode = Node::find($nodeId);
        $action = $request->action.'Create';
        $this->$action($currentNode, $request->fields, $module);

        return redirect()->back()->withInput();
    }

    public function baseRepositoryCreate(Node $node, $fields, $module)
    {
        $moduleName = $module->getName();
        $namespace = 'Modules\\'.$moduleName;
        $moduleFolder = $module->getLowerName();
        $modulePath = $module->getPath();
        $templatePath = module_path('Builder', 'Resources/views/templates/repository/base');

        //create RepositoryInterface.php
        $repositoryInterfaceTemp = $templatePath.'/repository_interface.temp';
        $repositoryInterfaceContent = $this->replaceTemplate([], [], $repositoryInterfaceTemp);

        Storage::disk('base')
            ->put(
                'Modules/Admin/Repositories/Base/RepositoryInterface.php',
                $repositoryInterfaceContent
            );

        //create BaseRepository.php
        $baseRepositoryInterfaceTemp = $templatePath.'/base_repository.temp';
        $baseRepositoryInterfaceContent = $this->replaceTemplate([], [], $baseRepositoryInterfaceTemp);
        Storage::disk('base')
            ->put(
                'Modules/Admin/Repositories/Base/BaseRepository.php',
                $baseRepositoryInterfaceContent
            );

        //create InputDataInterface.php
        $inputDataInterfaceTemp = $templatePath.'/input/input_data_interface.temp';
        $inputDataInterfaceContent = $this->replaceTemplate([], [], $inputDataInterfaceTemp);
        Storage::disk('base')
            ->put(
                'Modules/Admin/Repositories/Base/Input/InputDataInterface.php',
                $inputDataInterfaceContent
            );

        //create Base/Output
        $outputInterfaceTemp = $templatePath.'/out_put/output_interface.temp';
        $outputInterfaceContent = $this->replaceTemplate([], [], $outputInterfaceTemp);
        Storage::disk('base')
            ->put(
                'Modules/Admin/Repositories/Base/Output/OutputInterface.php',
                $outputInterfaceContent
            );

        $paginateCollectionOutputTemp = $templatePath.'/out_put/paginate_collection_output.temp';
        $paginateCollectionOutputContent = $this->replaceTemplate([], [], $paginateCollectionOutputTemp);
        Storage::disk('base')
            ->put(
                'Modules/Admin/Repositories/Base/Output/PaginateCollectionOutput.php',
                $paginateCollectionOutputContent
            );

        //create Base/Query
        $queryInterfaceTemp = $templatePath.'/query/query_interface.temp';
        $queryInterfaceContent = $this->replaceTemplate([], [], $queryInterfaceTemp);
        Storage::disk('base')
            ->put(
                'Modules/Admin/Repositories/Base/Query/QueryInterface.php',
                $queryInterfaceContent
            );

        $baseQueryTemp = $templatePath.'/query/base_query.temp';
        $baseQueryContent = $this->replaceTemplate([], [], $baseQueryTemp);
        Storage::disk('base')
            ->put(
                'Modules/Admin/Repositories/Base/Query/BaseQuery.php',
                $baseQueryContent
            );

        return true;
    }

    public function controllerCreate(Node $node, $fields, $module)
    {
        $moduleName = $module->getName();
        $namespace = 'Modules\\'.$moduleName;
        $moduleFolder = $module->getLowerName();
        $modulePath = $module->getPath();

        //create controller file
        $templatePath = module_path('Builder', 'Resources/views/templates/controller');
        $node->objName = lcfirst($node->name);
        $controllerTemp = $templatePath.'/controller.temp';
        $controllerContent = $this->replaceTemplate(
            ['$TYPE$', '$NAME$', '$KEY$', '$SLUG$', '$NAMESPACE$', '$FOLDER$', '$OBJ_NAME$'],
            [
                $node->type, $node->name, $node->key, \Str::of($node->key)->slug(), $namespace, $moduleFolder,
                $node->objName
            ],
            $controllerTemp
        );

        return Storage::disk('base')
            ->put(
                'Modules/'.$moduleName.'/Http/Controllers/'.$node->name.'Controller.php',
                $controllerContent
            );
    }

    public function viewCreate(Node $node, $fields, $module)
    {
        $moduleName = $module->getName();
        $namespace = 'Modules\\'.$moduleName;
        $moduleFolder = $module->getLowerName();
        $node->objName = lcfirst($node->name);

        $modulePath = $module->getPath();
        $templatePath = module_path('Builder', 'Resources/views/templates/view');

        //create index.blade.php
        $indexTemplate = $templatePath.'/Index.temp';

        $allFields = Node::whereParentId($node->id)->where('type', '!=', 'object')->get();
        $tHeadContent = '';

        $tBodyContent = '';

        foreach ($allFields as $field) {
            $tHeadContent .= "<th>".$field->name."</th>\n                        ";
            $tBodyContent .= '<td>{{'.$node->key.'.'.$field->key.'}}</td>'."\n";
        }
        $tHeadContent .= '<th></th>';
        $tBodyContent .=
            '<td>
                <a
                    class="btn btn-primary btn-ms"
                    :href="$backendRoute(\''.$moduleFolder.'.'.\Str::of($node->key)->slug().'.edit\','.$node->key.'.id)"
                >
                Edit</a>
            </td>';

        $indexContent = '';
        $indexContent .= $this->replaceTemplate(
            ['$TYPE$', '$NAME$', '$KEY$', '$SLUG$', '$THEADCONTENT$', '$TBODYCONTENT$', '$FOLDER$'],
            [
                $node->type, $node->name, $node->key, \Str::of($node->key)->slug(), $tHeadContent, $tBodyContent,
                $moduleFolder
            ],
            $indexTemplate
        );

        $result = Storage::disk('base')->put(
            'Modules/'.$moduleName.'/Resources/assets/js/Pages/'.ucwords(\Str::of($node->key)->slug()).'/Index.vue',
            $indexContent
        );

        //create create.blade.php
        $createViewTemplate = $templatePath.'/Create.temp';

        $submitName = 'create';
        $submitRouteName = $moduleFolder.'.'.\Str::of($node->key)->slug().'.store';

        $createViewContent = $this->replaceTemplate(
            ['$SUBMITNAME$', '$SUBMIT_ROUTE_NAME$'],
            [$submitName, $submitRouteName],
            $createViewTemplate
        );

        $result = Storage::disk('base')->put(
            'Modules/'.$moduleName.'/Resources/assets/js/Pages/'.ucwords(\Str::of($node->key)->slug()).'/Create.vue',
            $createViewContent
        );
//
//        //edit edit.blade.php
//        $editViewTemplate = $templatePath.'/edit.blade.php';
//        $editViewContent = $this->replaceTemplate(
//            ['$TYPE$', '$NAME$', '$KEY$', '$SLUG$', '$FOLDER$', '$OBJECT_NAME$'],
//            [$node->type, $node->name, $node->key, \Str::of($node->key)->slug(), $moduleFolder, $node->objName],
//            $editViewTemplate
//        );
//
//        Storage::disk('base')->put(
//            'Modules/'.$moduleName.'/Resources/views/'.\Str::of($node->key)->slug().'/edit.blade.php',
//            $editViewContent
//        );
        return true;
    }

    public function formCreate(Node $node, $fields, $module)
    {
        $moduleName = $module->getName();
        $namespace = 'Modules\\'.$moduleName;
        $moduleFolder = $module->getLowerName();
        $node->objName = lcfirst($node->name);

        $modulePath = $module->getPath();
        $templatePath = module_path('Builder', 'Resources/views/templates/view');

        //create form.blade.php
        $formTemplate = $templatePath.'/form/DefaultForm.temp';

        $allPros = Node::where('parent_id', $node->id)->where('type', '!=', 'object')->get();

        $templatePath = module_path('Builder', 'Resources/views/templates');
        $inputTemplate = $templatePath.'/view/form';
        $inputContent = '';
        $formData = '';

        foreach ($allPros as $pro) {
            $formData .= $pro->key . ':null,' . "\n";
            $inputContent .= $this->replaceTemplate(
                ['$TYPE$', '$NAME$', '$KEY$', '$OBJECT$', '$FOLDER$'],
                [$pro->type, $pro->name, $pro->key, $node->objName, $moduleFolder],
                $inputTemplate.'/'.ucwords($pro->type).'Input.temp'
            );
        }

        $formContent = '';
        $submitName = 'create';

        $formContent .= $this->replaceTemplate(
            ['$INPUTCONTENT$', '$FORMDATA$', '$SUBMITNAME$'],
            [$inputContent, $formData, $submitName],
            $formTemplate
        );

        return Storage::disk('base')->put(
            'Modules/'.$moduleName.'/Resources/assets/js/Pages/'.ucwords(\Str::of($node->key)->slug()).'/Form/DefaultForm.vue',
            $formContent
        );
    }

    public function modelCreate(Node $node, $fields, $module)
    {

        $moduleName = $module->getName();
        $namespace = 'Modules\\'.$moduleName;
        $modulePath = $module->getPath();

        $templatePath = module_path('Builder', 'Resources/views/templates');
        $modelTemplate = $templatePath.'/model.temp';
        $repositoryTemplate = $templatePath.'/repository/repository.temp';
        $repositoryEloquentTemplate = $templatePath.'/repository/repository_eloquent.temp';
        $fillableString = '';
        foreach ($fields as $field) {
            $fillableString .= "'".$field."',";
        }
        $modelContent = $this->replaceTemplate(
            ['$NAME$', '$KEY$', '$FILLABLE$', '$NAMESPACE$'],
            [$node->name, $node->key, $fillableString, $namespace],
            $modelTemplate
        );
        Storage::disk('base')->put('Modules/'.$moduleName.'/Entities/'.ucfirst($node->name).'.php', $modelContent);

        $repositoryContent = $this->replaceTemplate(
            ['$NAME$', '$KEY$', '$FILLABLE$', '$NAMESPACE$'],
            [$node->name, $node->key, $fillableString, $namespace],
            $repositoryTemplate
        );
        Storage::disk('base')->put(
            'Modules/'.$moduleName.'/Repositories/'.$node->name.'/'.ucfirst($node->name).'Repository.php',
            $repositoryContent
        );

        $repositoryEloquentContent = $this->replaceTemplate(
            ['$NAME$', '$KEY$', '$FILLABLE$', '$NAMESPACE$'],
            [$node->name, $node->key, $fillableString, $namespace],
            $repositoryEloquentTemplate
        );
        Storage::disk('base')->put(
            'Modules/'.$moduleName.'/Repositories/'.$node->name.'/'.ucfirst($node->name).'RepositoryEloquent.php',
            $repositoryEloquentContent
        );

        //create RepositoryServiceProvider.php
        if(!Storage::disk('base')->exists('Modules/Admin/Providers/RepositoryServiceProvider.php')) {
            $repositoryServiceProviderTemp = $templatePath.'/repository/base/repository_service_provider.temp';
            $repositoryServiceProviderContent = $this->replaceTemplate(
                ['$NAMESPACE$'],
                [$namespace],
                $repositoryServiceProviderTemp
            );

            Storage::disk('base')
                ->put(
                    'Modules/Admin/Providers/RepositoryServiceProvider.php',
                    $repositoryServiceProviderContent
                );
        }

        //Insert Dependency Injection binding
        $injectionStr = '$this->app->bind(Modules\\'
            .$moduleName.'\\Repositories\\'.$node->name.'\\'.ucfirst($node->name).'Repository::class,
            '.
            'Modules\\'.$moduleName.'\\Repositories\\'.$node->name.'\\'.ucfirst(
                $node->name
            ).'RepositoryEloquent::class);';
//        Check existed content

        $repositoriesStr = Storage::disk('base')->get('Modules/'.$moduleName.'/Includes/repositories.php');

        if (!str_contains($repositoriesStr, $injectionStr)) {
            Storage::disk('base')->append('Modules/'.$moduleName.'/Includes/repositories.php', $injectionStr);
        }
        //Insert route define
        $routeLine = 'Route::resource("'.\Str::slug($node->key).'", "'.$node->name.'Controller");';
        $routesTr = Storage::disk('base')->get('Modules/'.$moduleName.'/Includes/curd_routes.php');
        if (!str_contains($routesTr, $routeLine)) {
            Storage::disk('base')->append('Modules/'.$moduleName.'/Includes/curd_routes.php', $routeLine);
        }
    }

    public function requestCreate(Node $node, $fields, $module)
    {

        $moduleName = $module->getName();
        $namespace = 'Modules\\'.$moduleName;
        $modulePath = $module->getPath();


        $templatePath = module_path('Builder', 'Resources/views/templates');
        $createRequestTemplate = $templatePath.'/createRequest.temp';

        $rulestring = '';
        foreach ($fields as $field) {
//            'name'=>'required',
            $rulestring .= "'".$field."'=>'required',\n            ";
        }

        $createRequestContent = $this->replaceTemplate(
            ['$NAME$', '$RULES$', '$NAMESPACE$'],
            [$node->name, $rulestring, $namespace],
            $createRequestTemplate
        );

        return Storage::disk('base')->put(
            'Modules/'.$moduleName.'/Http/Requests/'.ucfirst($node->name).'/CreateRequest.php',
            $createRequestContent
        );
    }

    public function replaceTemplate($find, $replace, $template)
    {
        try {
            $template = file_get_contents($template);
        } catch (\Exception $e) {
            dd($e);
        }
        return str_replace($find, $replace, $template);
    }

    private function getAllTables()
    {
        $allTables = DB::getSchemaBuilder()->getAllTables();

        $tableArr = [];
        foreach ($allTables as $table) {
            $table = (array) $table;
            $tableArr[] = $table[array_key_first($table)];
        }

        return $tableArr;
    }
}
