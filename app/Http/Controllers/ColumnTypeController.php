<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ColumnTypeController extends Controller
{
    public function getColumnType(Request $request)
    {
        $currentTable = cacheGet('current_table');

        return responseJson(200, 'success', ColumnType($currentTable, $request->column_name));
    }
}
