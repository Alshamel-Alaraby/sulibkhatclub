<?php

namespace Modules\HMS\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\HMS\Entities\HMSDay;
use Modules\HMS\Transformers\HMSDayResource;

class HMSDayController extends Controller
{
    public function __construct(private HMSDay $model)
    {
        $this->model = $model;
    }

    public function index(Request $request)
    {
        $models = $this->model->get();

        return responseJson(200, 'success', HMSDayResource::collection($models),null);
    }


}
