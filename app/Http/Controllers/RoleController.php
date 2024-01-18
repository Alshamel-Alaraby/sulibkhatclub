<?php

namespace App\Http\Controllers;

use App\Models\GeneralCustomTable;
use App\Models\HotkeyRole;
use App\Models\SpatieRole;
use Illuminate\Http\Request;
use App\Http\Requests\AllRequest;
use App\Http\Requests\RoleRequest;
use App\Http\Resources\AllDropListResource;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Http\Resources\RoleResource;
use App\Http\Resources\PermissionsResource;

class RoleController extends Controller
{

    public function __construct(private Role $model)
    {
        $this->model = $model;
    }

    public function find($id)
    {
        $model = $this->model->find($id);
        if (!$model) {
            return responseJson(404, 'not found');
        }

        return responseJson(200, 'success', new RoleResource($model));
    }

    public function all(Request $request)
    {
        $models = $this->model->orderBy($request->order ? $request->order : 'updated_at', $request->sort ? $request->sort : 'DESC');

        if ($request->per_page) {
            $models = ['data' => $models->paginate($request->per_page), 'paginate' => true];
        } else {
            $models = ['data' => $models->get(), 'paginate' => false];
        }

        return responseJson(200, 'success', RoleResource::collection($models['data']), $models['paginate'] ? getPaginates($models['data']) : null);
    }

    public function create(RoleRequest $request)
    {
        DB::beginTransaction();
        $model = $this->model->create($request->validated());
        $model->permissions()->sync($request->permissions);
        foreach ($request->added_hotkeys as $table => $columns)
        {
            foreach ($columns as $column_name => $options)
            {
                $hotkey_role = new HotkeyRole([
                    'column_name' => $column_name,
                    'table' => $table,
                    'can_edit'  =>  $options['can_edit'],
                    'can_see'  =>  $options['can_see'],
                    'can_plus'  =>  $options['can_plus'],
                    'percentage'  =>  $options['percentage'],
                    'role_id' => $model->id
                ]);
                $hotkey_role->save();
            }
        }
        $model->refresh();
        DB::commit();
        return responseJson(200, 'created', new RoleResource($model));
    }

    public function update($id, RoleRequest $request)
    {
        $model = $this->model->find($id);
        if (!$model) {
            return responseJson(404, 'not found');
        }

        $model->update($request->validated());
        $model->permissions()->whereIn('id',$request->permissions_range)->detach();
        $model->permissions()->attach($request->permissions);

        /* delete role hotkeys */
        $spatie_role = SpatieRole::query()->findOrFail($id);
        $spatie_role->hotkeys()->delete();

        /* add new role hotkeys */
        foreach ($request->added_hotkeys as $table => $columns)
        {
            foreach ($columns as $column_name => $options)
            {
                $hotkey_role = new HotkeyRole([
                    'column_name' => $column_name,
                    'table' => $table,
                    'can_edit'  =>  $options['can_edit'],
                    'can_see'  =>  $options['can_see'],
                    'can_plus'  =>  $options['can_plus'],
                    'percentage'  =>  $options['percentage'],
                    'role_id' => $model->id
                ]);
                $hotkey_role->save();
            }
        }

        $model->refresh();

        return responseJson(200, 'updated', new RoleResource($model));
    }

    public function logs($id)
    {
        $model = $this->model->find($id);
        if (!$model) {
            return responseJson(404, 'not found');
        }

        $logs = $model->activities()->orderBy('created_at', 'DESC')->get();
        return responseJson(200, 'success', \App\Http\Resources\Log\LogResource::collection($logs));
    }
    public function delete($id)
    {
        $model = $this->model->find($id);
        if (!$model) {
            return responseJson(404, __('message.data not found'));
        }

//        $relationsWithChildren = $model->hasChildren();
//
//        if (!empty($relationsWithChildren)) {
//            $errorMessages = [];
//            foreach ($relationsWithChildren as $relation) {
//                $relationName = $this->getRelationDisplayName($relation['relation']);
//                $childCount = $relation['count'];
//                $childIds = implode(', ', $relation['ids']);
//                $errorMessages[] = [
//                    "message" => "This item has {$childCount} {$relationName} (Names: {$childIds}) and can't be deleted. Remove its {$relationName} first."
//                ];
//            }
//            return response()->json([
//                "message" => $errorMessages,
//                "data" => null,
//                "pagination" => null
//            ], 400);
//        }

        $model->delete();
        return responseJson(200, 'success');
    }

    public function bulkDelete(Request $request)
    {
        $itemsWithRelations = [];

        foreach ($request->ids as $id) {
            $model = $this->model->find($id);

            $relationsWithChildren = $model->hasChildren();
            if (!empty($relationsWithChildren)) {
                $itemsWithRelations[] = [
                    'id' => $id,
                    'relations' => $relationsWithChildren,
                ];
                continue;
            }

            $model->delete();
        }

        if (count($itemsWithRelations) > 0) {
            $errorMessages = [];
            foreach ($itemsWithRelations as $item) {
                $itemId = $item['id'];
                $relations = $item['relations'];

                $relationErrorMessages = [];
                foreach ($relations as $relation) {
                    $relationName = $this->getRelationDisplayName($relation['relation']);
                    $childCount = $relation['count'];
                    $childIds = implode(', ', $relation['ids']);
                    $relationErrorMessages[] = [
                        'message' => "Item with ID {$itemId} has {$childCount} {$relationName} (IDs: {$childIds}) and can't be deleted. Remove its {$relationName} first."
                    ];
                }

                $errorMessages = array_merge($errorMessages, $relationErrorMessages);
            }

            return response()->json([
                "message" => $errorMessages,
                "data" => null,
                "pagination" => null
            ], 400);
        }

        return responseJson(200, 'success');
    }

    private function getRelationDisplayName($relation)
    {
        $displayableName = str_replace('_', ' ', $relation);
        return ucwords($displayableName);
    }
    public function permissions(Request $request)
    {
        $permissions = \Spatie\Permission\Models\Permission::where(function ($q) use ($request) {
            if ($request->workflows) {
                $q->whereIn('crud_name',$request->workflows);
            }
        })->get();
        return responseJson(200, 'success', PermissionsResource::collection($permissions));
    }


    public function getDropDown(Request $request)
    {
        $models = $this->model->select('id', 'name', 'name_e');

        if ($request->per_page) {
            $models = ['data' => $models->paginate($request->per_page), 'paginate' => true];
        } else {
            $models = ['data' => $models->get(), 'paginate' => false];
        }

        return responseJson(200, 'success', AllDropListResource::collection($models['data']), $models['paginate'] ? getPaginates($models['data']) : null);
    }

    public function getHotkeys(Request $request)
    {
        $request->validate([
            'table' => 'required|string|exists:permissions,crud_name',
            'company_id' => 'required|integer',
        ]);
        // get table name from permission table
        $table_name = Permission::query()->where('crud_name', $request->table)->first()->table;
        $columns = GeneralCustomTable::query()->where('table_name', $table_name)->where('company_id', $request->company_id)->first();
        if(!null == $columns){
            $columns = $columns->columns;
            // first columns get only is_hotkey = 1
            $columns = array_filter($columns, function ($column) {
                return $column['is_hotkey'] == 1;
            });
            $columns = array_values($columns);
        }
        return responseJson(200, 'success', $columns??[]);


    }
}
