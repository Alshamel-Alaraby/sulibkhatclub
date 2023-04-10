<?php

namespace Modules\RealEstate\Http\Controllers;

use Illuminate\Routing\Controller;
use Modules\RealEstate\Repositories\RlstCategoryItem\RlstCategoryItemInterface;
use Modules\RealEstate\Transformers\RlstCategoryItemResource;
use Illuminate\Http\Request;
use Modules\RealEstate\Http\Requests\CategoryItemRequest;

class RlstCategoryItemController extends Controller
{

    public function __construct(private RlstCategoryItemInterface $modelInterface)
    {
        $this->modelInterface = $modelInterface;
    }

    public function all(Request $request)
    {
        $models = $this->modelInterface->all($request);

        return responseJson(200, 'success', RlstCategoryItemResource::collection($models['data']), $models['paginate'] ? getPaginates($models['data']) : null);
    }//

    public function getRootNodes()
    {
        return $this->modelInterface->getRootNodes();
    }
    public function getChildNodes($parentId)
    {
        return $this->modelInterface->getChildNodes($parentId);
    }

    public function find($id)
    {

        $model = $this->modelInterface->find($id);
        if (!$model) {
            return responseJson(404, __('message.data not found'));
        }
        return responseJson(200, 'success', new RlstCategoryItemResource($model));
    }


    public function create(CategoryItemRequest $request)
    {
        $model = $this->modelInterface->create($request);
        return responseJson(200, 'success', new RlstCategoryItemResource($model));
    }

    public function update(CategoryItemRequest $request, $id)
    {
        $model = $this->modelInterface->find($id);
        if (!$model) {
            return responseJson(404, __('message.data not found'));
        }
        $model = $this->modelInterface->update($request, $id);

        return responseJson(200, 'success', new RlstCategoryItemResource($model));
    }

    public function delete($id)
    {
        $model = $this->modelInterface->find($id);
        if (!$model) {
            return responseJson(404, __('message.data not found'));
        }
        if ($model->haveChildren) {
            return responseJson(400, __('message.parent have children'));
        }
        $this->modelInterface->delete($id);

        return responseJson(200, 'success');
    }

    public function logs($id)
    {
        $model = $this->modelInterface->find($id);
        if (!$model) {
            return responseJson(404, __('message.data not found'));
        }
        $logs = $this->modelInterface->logs($id);
        return responseJson(200, 'success', $logs);
    }
    public function bulkDelete(Request $request)
    {
        foreach ($request->ids as $id) {
            $model = $this->modelInterface->find($id);
            $arr = [];
            if ($model->have_children) {
                $arr[] = $id;
                continue;
            }
            $this->modelInterface->delete($id);
        }
        if (count($arr) > 0) {
            return responseJson(400, __('some items has relation cant delete'));
        }
        return responseJson(200, __('Done'));
    }

}
