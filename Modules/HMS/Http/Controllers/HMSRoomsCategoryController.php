<?php

namespace Modules\HMS\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\HMS\Entities\HMSRoomsCategory;
use Modules\HMS\Http\Requests\HMSRoomsCategoryRequest;
use Modules\HMS\Transformers\HMSRoomsCategoryResource;

class HMSRoomsCategoryController extends Controller
{
    public function __construct(private HMSRoomsCategory $model)
    {
        $this->model = $model;
    }

    public function index(Request $request)
    {
        $models = $this->model->filter($request)->orderBy($request->order ? $request->order : 'updated_at', $request->sort ? $request->sort : 'DESC');

        if($request->type){
            $models->where('parent_id' , $request->type =='parent' ? '=' : '!=' ,null);
        }

        if ($request->per_page) {
            $models = ['data' => $models->paginate($request->per_page), 'paginate' => true];
        } else {
            $models = ['data' => $models->get(), 'paginate' => false];
        }

        return responseJson(200, 'success', HMSRoomsCategoryResource::collection($models['data']), $models['paginate'] ? getPaginates($models['data']) : null);
    }


    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(HMSRoomsCategoryRequest $request)
    {
        $this->model->create($request->validated());
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(HMSRoomsCategoryRequest $request, HMSRoomsCategory $rooms_category)
    {
        $rooms_category->update($request->validated());
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy(HMSRoomsCategory $rooms_category)
    {
        if($rooms_category->children()->count())
            $rooms_category->children()->delete();

        $rooms_category->delete();
    }


    public function opreations_rooms_categories(Request $request){
        $models = $this->model->where('does_it_have_schedule',1)->get();
        return responseJson(200, 'success', HMSRoomsCategoryResource::collection($models),null);
    }
    public function bulkDelete()
    {

        $ids = request()->ids;
        if (!$ids) {
            return responseJson(400, 'ids is required');
        }
        $models = $this->model->whereIn('id', $ids)->get();

        $models->each(function ($model) {
//            if (!$model->hasChildren()) {
            $model->delete();
//            }

        });
        return responseJson(200, 'deleted');
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

}
