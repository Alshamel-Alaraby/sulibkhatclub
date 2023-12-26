<?php

namespace Modules\HMS\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\HMS\Entities\HMSRoom;
use Modules\HMS\Http\Requests\HMSRoomRequest;
use Modules\HMS\Transformers\HMSRoomResource;

class HMSRoomController extends Controller
{
    public function __construct(private HMSRoom $model)
    {
        $this->model = $model;
    }

    public function index(Request $request)
    {
        $models = $this->model->filter($request)->orderBy($request->order ? $request->order : 'updated_at', $request->sort ? $request->sort : 'DESC');

        if($request->branch_id)
            $models->where('branch_id',$request->branch_id);

        if($request->parent_rooms_category_id)
            $models->whereRelation('rooms_category','parent_id',$request->parent_rooms_category_id);

        if($request->doctor_id)
            $models->whereRelation('doctor_work_times','doctor_id',$request->doctor_id);

        if ($request->per_page) {
            $models = ['data' => $models->paginate($request->per_page), 'paginate' => true];
        } else {
            $models = ['data' => $models->get(), 'paginate' => false];
        }

        return responseJson(200, 'success', HMSRoomResource::collection($models['data']), $models['paginate'] ? getPaginates($models['data']) : null);
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(HMSRoomRequest $request)
    {
        $room = $this->model->create($request->validated());

        $this->room_schedule($request->schedule_times,$room);
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(HMSRoomRequest $request, HMSRoom $room)
    {
        $room->update($request->validated());
        $room->schedule()->forceDelete();
        $this->room_schedule($request->schedule_times,$room);
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy(HMSRoom $room)
    {
        $room->delete();
    }

    protected function room_schedule($schedule,$room){
        if($room->rooms_category->parent && $room->rooms_category->parent->does_it_have_schedule)
        foreach($schedule as $time)
            $room->schedule()->create($time);
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
