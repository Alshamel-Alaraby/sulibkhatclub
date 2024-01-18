<?php

namespace App\Http\Controllers\WorkNature;

use App\Http\Requests\WorkNature\WorkNatureRequest;
use App\Http\Resources\WorkNature\WorkNatureResource;
use App\Models\WorkNature;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class WorkNatureController extends Controller
{
    public function __construct(private WorkNature $model)
    {
        $this->model = $model;
    }

    public function index(Request $request)
    {
        if($request->drop_down)
            return $this->model->select('id','name','name_e')->latest()->get();

        $models = $this->model->filter($request)->orderBy($request->order ? $request->order : 'updated_at', $request->sort ? $request->sort : 'DESC');

        if ($request->per_page) {
            $models = ['data' => $models->paginate($request->per_page), 'paginate' => true];
        } else {
            $models = ['data' => $models->get(), 'paginate' => false];
        }

        return responseJson(200, 'success', WorkNatureResource::collection($models['data']), $models['paginate'] ? getPaginates($models['data']) : null);
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(WorkNatureRequest $request)
    {
        $this->model->create($request->validated());
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(WorkNatureRequest $request, WorkNature $work_nature)
    {
        $work_nature->update($request->validated());
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy(WorkNature $work_nature)
    {
        $work_nature->delete();
    }
}
