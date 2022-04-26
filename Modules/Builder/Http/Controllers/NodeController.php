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

    public function index()
    {
        return redirect()->route('admin.node.show', ['node' => 0, 'root_id' => 0]);
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

    public function baseFileBuilder($nodeId, $action = '')
    {
        $modules = Module::all();
        $currentNode = Node::find($nodeId);
        $allFields = Node::where('parent_id', $currentNode->id)->get();
        return view(
            'builder::node.build_base',
            compact('currentNode', 'allFields', 'modules')
        );
    }


    public function curdBuilder($nodeId, $action = '')
    {
        $modules = Module::all();
        $currentNode = Node::find($nodeId);
        $allFields = Node::where('parent_id', $currentNode->id)->get();
        return view(
            'builder::node.build',
            compact('currentNode', 'allFields', 'modules')
        );
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

    public function baseIntertiaCreate(Node $node, $fields, $module)
    {
        $moduleName = $module->getName();
        $templatePath = module_path('Builder', 'Resources/views/templates/base/innertia');
        $filePath = 'Modules/'.$moduleName.'/Resources/assets/js/Shared';
        $filesCreate = [];

        $paginationTemp = $templatePath.'/pagination.temp';
        $filesCreate[] = [
            'filePath' => $filePath.'/Pagination.vue',
            'fileContent' => $this->replaceTemplate([], [], $paginationTemp)
        ];

        $textInputTemp = $templatePath.'/text_input.temp';
        $filesCreate[] = [
            'filePath' => $filePath.'/TextInput.vue',
            'fileContent' => $this->replaceTemplate([], [], $textInputTemp)
        ];

        $textareaInputTemp = $templatePath.'/textarea_input.temp';
        $filesCreate[] = [
            'filePath' => $filePath.'/TextareaInput.vue',
            'fileContent' => $this->replaceTemplate([], [], $textareaInputTemp)
        ];

        $vueTemp = module_path('Builder', 'Resources/views/templates/vue_intertia').'/intertia.temp';
        $filesCreate[] = [
            'filePath' => 'Modules/'.$moduleName.'/Resources/assets/js/intertia.js',
            'fileContent' => $this->replaceTemplate([], [], $vueTemp)
        ];

        foreach($filesCreate as $fileCreate)
        {
            $this->createFile(
                $fileCreate['filePath'],
                $fileCreate['fileContent']
            );
        }

        return true;
    }

    public function baseRepositoryCreate(Node $node, $fields, $module)
    {
        $moduleName = $module->getName();
        $filePath = 'Modules/Admin/Repositories/Base';
        $templatePath = module_path('Builder', 'Resources/views/templates/base/repository');
        $filesCreate = [];

        //create RepositoryInterface.php
        $repositoryInterfaceTemp = $templatePath.'/repository_interface.temp';
        $filesCreate[] = [
            'filePath' => $filePath.'/RepositoryInterface.php',
            'fileContent' => $this->replaceTemplate([], [], $repositoryInterfaceTemp)
        ];

        //create BaseRepository.php
        $baseRepositoryInterfaceTemp = $templatePath.'/base_repository.temp';
        $filesCreate[] = [
            'filePath' => $filePath.'/BaseRepository.php',
            'fileContent' => $this->replaceTemplate([], [], $baseRepositoryInterfaceTemp)
        ];

        //create Base/Input
        $inputDataInterfaceTemp = $templatePath.'/input/input_data_interface.temp';
        $filesCreate[] = [
            'filePath' => $filePath.'/Input/InputDataInterface.php',
            'fileContent' => $this->replaceTemplate([], [], $inputDataInterfaceTemp)
        ];

        $inputDataFromRequestTemp = $templatePath.'/input/input_data_from_request.temp';
        $filesCreate[] = [
            'filePath' => $filePath.'/Input/InputDataFromRequest.php',
            'fileContent' => $this->replaceTemplate([], [], $inputDataFromRequestTemp)
        ];

        //create Base/Query
        $queryInterfaceTemp = $templatePath.'/query/query_interface.temp';
        $filesCreate[] = [
            'filePath' => $filePath.'/Query/QueryInterface.php',
            'fileContent' => $this->replaceTemplate([], [], $queryInterfaceTemp)
        ];

        $baseQueryTemp = $templatePath.'/query/base_query.temp';
        $filesCreate[] = [
            'filePath' => $filePath.'/Query/BaseQuery.php',
            'fileContent' => $this->replaceTemplate([], [], $baseQueryTemp)
        ];

        $getByIdTemp = $templatePath.'/query/get_by_id.temp';
        $filesCreate[] = [
            'filePath' => $filePath.'/Query/GetById.php',
            'fileContent' => $this->replaceTemplate([], [], $getByIdTemp)
        ];

        //create Base/Output
        $outputInterfaceTemp = $templatePath.'/output/output_interface.temp';
        $filesCreate[] = [
            'filePath' => $filePath.'/Output/OutputInterface.php',
            'fileContent' => $this->replaceTemplate([], [], $outputInterfaceTemp)
        ];

        $objectOutputInterfaceTemp = $templatePath.'/output/object_output_interface.temp';
        $filesCreate[] = [
            'filePath' => $filePath.'/Output/ObjectOutputInterface.php',
            'fileContent' => $this->replaceTemplate([], [], $objectOutputInterfaceTemp)
        ];

        $paginateCollectionOutputTemp = $templatePath.'/output/paginate_collection_output.temp';
        $filesCreate[] = [
            'filePath' => $filePath.'/Output/PaginateCollectionOutput.php',
            'fileContent' => $this->replaceTemplate([], [], $paginateCollectionOutputTemp)
        ];

        $objectOutputTemp = $templatePath.'/output/object_output.temp';
        $filesCreate[] = [
            'filePath' => $filePath.'/Output/ObjectOutput.php',
            'fileContent' => $this->replaceTemplate([], [], $objectOutputTemp)
        ];

        foreach($filesCreate as $fileCreate)
        {
            $this->createFile(
                $fileCreate['filePath'],
                $fileCreate['fileContent']
            );
        }

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
        $filePath = 'Modules/'.$moduleName.'/Resources/assets/js/Pages/'.ucwords(\Str::of($node->key)->slug());
        $filesCreate = [];

        //create Index.vue
        $indexTemplate = $templatePath.'/index.temp';

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
                <Link :href="$backendRoute(\''.$moduleFolder.'.'.\Str::of($node->key)->slug().'.edit\','.$node->key.'.id)" class="btn btn-primary">Edit</Link>
                <button type="button" @click="delete'.$node->name.'('.$node->key.')" class="btn btn-danger">Delete</button>
            </td>
            ';

        $indexContent = '';
        $indexContent .= $this->replaceTemplate(
            ['$TYPE$', '$NAME$', '$KEY$', '$SLUG$', '$THEADCONTENT$', '$TBODYCONTENT$', '$FOLDER$'],
            [
                $node->type, $node->name, $node->key, \Str::of($node->key)->slug(), $tHeadContent, $tBodyContent,
                $moduleFolder
            ],
            $indexTemplate
        );

        $filesCreate[] = [
            'filePath' => $filePath.'/Index.vue',
            'fileContent' => $indexContent
        ];

        //create form
        $allPros = Node::where('parent_id', $node->id)->where('type', '!=', 'object')->get();

        $inputContent = '';
        $createFormData = '';
        $updateFormData = '';

        foreach ($allPros as $pro) {

            $createFormData .= $pro->key . ':null,' . "\n";
            $updateFormData .= $pro->key . ': this.' . \Str::of($node->key)->slug(). '.' . $pro->key . ',' . "\n";

            $vueInputData = 'v-model="form.'.$pro->key.'"
                            :error="form.errors.'.$pro->key.'"
                            label="'.$pro->key.'"';

            switch ($pro->type) {
                case 'text':
                    $inputContent .= '
                         <text-input
                            '.$vueInputData.'
                        ></text-input>
                    ';
                    break;
                case 'number':
                    $inputContent .= '
                         <text-input
                            '.$vueInputData.'
                            :type="\'number\'"
                        ></text-input>
                    ';
                    break;
                case 'textarea':
                    $inputContent .= '
                         <textarea-input
                            '.$vueInputData.'
                        ></textarea-input>
                    ';
                    break;
            }
        }

        //create create.blade.php
        $createViewTemplate = $templatePath.'/create.temp';

        $createViewContent = $this->replaceTemplate(
            ['$INPUTCONTENT$', '$SLUG$', '$FOLDER$', '$FORMDATA$'],
            [$inputContent, \Str::of($node->key)->slug(), $moduleFolder, $createFormData],
            $createViewTemplate
        );

        $filesCreate[] = [
            'filePath' => $filePath.'/Create.vue',
            'fileContent' => $createViewContent
        ];

        //edit edit.blade.php
        $editViewTemplate = $templatePath.'/edit.temp';

        $editViewContent = $this->replaceTemplate(
            ['$INPUTCONTENT$', '$SLUG$', '$FOLDER$', '$FORMDATA$'],
            [$inputContent, \Str::of($node->key)->slug(), $moduleFolder, $updateFormData],
            $editViewTemplate
        );

        $filesCreate[] = [
            'filePath' => $filePath.'/Edit.vue',
            'fileContent' => $editViewContent
        ];

        foreach($filesCreate as $fileCreate)
        {
            $this->createFile(
                $fileCreate['filePath'],
                $fileCreate['fileContent']
            );
        }

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
        $templatePath = module_path('Builder', 'Resources/views/templates/request');
        $filePath = 'Modules/'.$moduleName.'/Http/Requests/'.ucfirst($node->name);
        $filesCreate = [];

        $rulestring = '';
        foreach ($fields as $field) {
            $rulestring .= "'".$field."'=>'required',\n            ";
        }

        $createRequestTemplate = $templatePath.'/create_request.temp';
        $filesCreate[] = [
            'filePath' => $filePath.'/CreateRequest.php',
            'fileContent' => $this->replaceTemplate(
                ['$NAME$', '$RULES$', '$NAMESPACE$'],
                [$node->name, $rulestring, $namespace],
                $createRequestTemplate
            )
        ];

        $editRequestTemplate = $templatePath.'/update_request.temp';
        $filesCreate[] = [
            'filePath' => $filePath.'/UpdateRequest.php',
            'fileContent' => $this->replaceTemplate(
                ['$NAME$', '$RULES$', '$NAMESPACE$'],
                [$node->name, $rulestring, $namespace],
                $editRequestTemplate
            )
        ];

        foreach($filesCreate as $fileCreate)
        {
            $this->createFile(
                $fileCreate['filePath'],
                $fileCreate['fileContent']
            );
        }

        return true;
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

    private function createFile($filePath, $fileContent)
    {
        if(!Storage::disk('base')->exists($filePath)) {
            Storage::disk('base')->put($filePath, $fileContent);
        }
    }
}
