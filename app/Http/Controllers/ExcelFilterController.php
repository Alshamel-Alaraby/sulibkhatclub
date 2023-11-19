<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ExcelFilter;

class ExcelFilterController extends Controller
{
    public function __construct(private ExcelFilter $model)
    {
        $this->model = $model;
    }

    public function getNumericFilterTypes()
    {
        $numericModels = $this->model->data()->where('type', 'LIKE', '%numeric%')->get();

        return responseJson(200, 'success', $numericModels);
    }

    public function getTextFilterTypes()
    {
        $textModels = $this->model->data()->where('type', 'LIKE','%text%')->get();

        return responseJson(200, 'success', $textModels);
    }

    public function getDateFilterTypes()
    {
        $dateModels = $this->model->data()->where('type', 'LIKE', '%date%')->get();

        return responseJson(200, 'success', $dateModels);
    }

}
