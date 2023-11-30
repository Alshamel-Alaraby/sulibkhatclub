<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CopyDataController extends Controller
{
    public function getSampleData(Request $request)
    {
        $currentTable = cacheGet('current_table');

        $sampleData = DB::table($currentTable)->select('id','name','name_e')->get();

        return responseJson(200, 'success', $sampleData);
    }

    public function getFullData(Request $request)
    {
        $currentTable = cacheGet('current_table');

        $fullData = DB::table($currentTable)->where('id', $request->id)->get();

        return responseJson(200, 'success', $fullData);
    }
}
