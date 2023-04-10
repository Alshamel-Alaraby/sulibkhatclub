<?php

namespace Modules\RecievablePayable\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\RecievablePayable\Entities\RpBreakDown;
use Modules\RecievablePayable\Http\Requests\CreateBreakDownRequest;
use Modules\RecievablePayable\Transformers\BreakDownResource;

class RpBreakDownController extends Controller
{
    public function __construct(private RpBreakDown $model)
    {
        $this->model = $model;
    }

    public function show($id)
    {
        $model = $this->model->find($id);
        if (!$model) {
            return responseJson(404, 'not found');
        }

        return responseJson(200, 'success', new BreakDownResource($model));
    }

    public function index(Request $request)
    {
        $models = $this->model->filter($request)->orderBy('instalment_date', 'ASC');
        if ($request->break_type){
            $models->where('break_type',$request->break_type);
        }
        if ($request->break_id && $request->break_type == "openingBalance"){
            $models->whereHas('openingBalance',function ($q) use ($request){
                $q->where('id',$request->break_id);
            })->where('parent_id',null);
        }
        if ($request->break_id && $request->break_type == "invoice"){
            $models->whereHas('invoice',function ($q) use ($request){
                $q->where('id',$request->break_id);
            })->where('parent_id',null);
        }
        if ($request->break_id && $request->break_type == "contract"){
            $models->whereHas('contract',function ($q) use ($request) {
                $q->where('id', $request->break_id);
            })->where('parent_id',null);
        }
        if ($request->break_id && $request->break_type == "reservation"){
            $models->whereHas('reservation',function ($q) use ($request) {
                $q->where('id', $request->break_id);
            })->where('parent_id',null);
        }

        if ($request->parent_id){
            $models->where('parent_id',$request->parent_id);
        }
        if ($request->customer_id){
            $models->where('customer_id',$request->customer_id)
            ->where('invoice_id',null)->orderBy('instalment_date', 'DESC');
        }
        if ($request->per_page) {
            $models = ['data' => $models->paginate($request->per_page), 'paginate' => true];
        } else {
            $models = ['data' => $models->get(), 'paginate' => false];
        }

        return responseJson(200, 'success', BreakDownResource::collection($models['data']), $models['paginate'] ? getPaginates($models['data']) : null);
    }

    public function store(CreateBreakDownRequest $request)
    {
        $data = [];
        foreach ($request->validated()['break_downs'] as $break_downs ):
                $model = $this->model->create($break_downs);
            $data[] = $model;
        endforeach;
        return $data;
    }


    public function updateBreak(CreateBreakDownRequest $request)
    {
        $data = [];
        foreach ($request->validated()['break_downs'] as $break_downs ):
            $model = $this->model->where('id',$break_downs['id'])->first();
            if ($model)
            {
                $model->update($break_downs);
            }else{
                $model = $this->model->create($break_downs);
            }
            $data[] = $model;
        endforeach;
        return $data;
    }

    public function destroy($id)
    {
        $model = $this->model->find($id);
        if ($model->hisChildren()){
            return responseJson(400, 'some items has relation cant delete');
        }
        $model->delete();
        return responseJson(200, 'deleted');
    }


}
