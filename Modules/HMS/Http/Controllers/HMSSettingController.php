<?php

namespace Modules\HMS\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\HMS\Entities\HMSSetting;
use Modules\HMS\Http\Requests\HMSSettingRequest;
use Modules\HMS\Transformers\HMSSettingResource;

class HMSSettingController extends Controller
{
    public function __construct(private HMSSetting $model)
    {
        $this->model = $model;
    }

    public function index(Request $request)
    {

        return responseJson(200, 'success',HMSSettingResource::collection($this->model->get()),null);
    }



    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(HMSSettingRequest $request, HMSSetting $setting)
    {
        $setting->update($request->validated());
    }


}
