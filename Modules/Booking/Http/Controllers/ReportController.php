<?php

namespace Modules\Booking\Http\Controllers;

use App\Models\DocumentHeader;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Booking\Entities\Unit;

class ReportController extends Controller
{
    public function houseKeeping(Request $request)
    {

        $models = Unit::with(['documentHeaderDetails' => function ($query) use ($request) {
            $query->where('unit_status_id', 16)
                ->whereDate('date_from', '<=', now())
                ->whereDate('date_to', '>=', now())
                ->whereHas('documentHeader', function ($query) use ($request) {
                    $query->where('branch_id', $request->branch_id);
                })
                ->select('unit_id', 'document_header_id', 'category_booking')
                ->with(['documentHeader' => function ($query) {
                    $query->select('id', 'customer_id')
                        ->with(['customer' => function ($query) {
                            $query->select('id', 'nationality');
                        }]);
                }]);
        }])->select('id', 'code')->get();

        $response = [
            'message' => 'success',
            'data' => $models,
        ];

        return response()->json($response);
    }

    public function checkIn(Request $request)
    {

        $models = DocumentHeader::where('branch_id', $request->branch_id)
            ->where('document_id', 33)->whereDate('date', now())
            ->with(['documentHeaderDetails' => function ($query) {
                $query->select('id', 'document_header_id', 'date_from', 'date_to', 'unit_id', 'category_booking', 'rent_days','price_per_uint')
                    ->with(['unit' => function ($query) {
                        $query->select('id', 'code');
                    }]);
            }])->with(['customer' => function ($qq) {
            $qq->select('id', 'name', 'name_e', 'nationality')->with(['voucherHeaders' => function ($q) {
                $q->select('id', 'customer_id', 'amount')->whereNull('break_settlement_id');
            }]);
        }])
            ->select('id', 'branch_id', 'document_id', 'customer_id', 'attendans_num', 'invoice_discount', 'net_invoice')
            ->get();

        $response = [
            'message' => 'success',
            'data' => $models,
        ];

        return response()->json($response);

    }

    public function checkOut(Request $request)
    {

        $models = DocumentHeader::where('branch_id', $request->branch_id)
            ->where('document_id', 34)->whereDate('date', now())
            ->with('documentHeaderDetails', function ($query) {
                $query->select('id', 'document_header_id', 'date_from', 'date_to', 'unit_id', 'item_id', 'category_booking', 'rent_days', 'price_per_uint', 'rent_days')
                    ->with(['unit' => function ($query) {
                        $query->select('id', 'code');
                    }])
                    ->with(['item' => function ($query) {
                        $query->select('id', 'name', 'name_e', 'price');
                    }]);
            })->with(['customer' => function ($qq) {
            $qq->select('id', 'name', 'name_e', 'nationality');
        }])->with(['documentNumber' => function ($query) {
            $query->select('id', 'branch_id', 'document_id', 'customer_id', 'attendans_num', 'invoice_discount', 'net_invoice', 'related_document_number')
                ->with(['documentHeaderDetails' => function ($q) {
                    $q->select('id', 'document_header_id', 'date_from', 'date_to', 'unit_id', 'category_booking', 'rent_days','price_per_uint','discount');
                }]);
        }])->select('id', 'branch_id', 'document_id', 'customer_id', 'attendans_num', 'invoice_discount', 'net_invoice', 'related_document_number')
            ->get();

        // // Calculate "accommodation_total_income"
        // $models->each(function ($model) {
        //     $model->documentHeaderDetails->each(function ($detail) {
        //         $detail->accommodation_total_income = $detail->price_per_uint * $detail->rent_days;
        //     });
        // });

        $response = [
            'message' => 'success',
            'data' => $models,
        ];

        return response()->json($response);
    }

    public function occupiedRoomsStatus(Request $request)
    {

        $models = DocumentHeader::where('branch_id', $request->branch_id)
            ->where('document_id', 33)
            ->whereHas('documentHeaderDetails',function ($qu){
                $qu->whereDate('date_from', '<=', now())
                    ->whereDate('date_to', '>=', now());
            })->with(['documentHeaderDetails' => function ($query) {
                $query->whereDate('date_from', '<=', now())
                    ->whereDate('date_to', '>=', now())
                    ->select('id', 'document_header_id', 'date_from', 'date_to', 'unit_id', 'category_booking', 'price_per_uint','discount','rent_days')
                    ->with(['unit' => function ($query) {
                        $query->select('id', 'code');
                    }]);

            }])->with(['customer' => function ($qq) use ($request) {
            $qq->select('id', 'name', 'name_e', 'nationality')
                ->with(['voucherHeaders' => function ($q) {
                    $q->select('id', 'customer_id', 'amount')->whereNull('break_settlement_id');
                }])
                ->with(['documentHeader' => function ($q) use ($request) {
                    $q->where('document_id', 36)
                    ->where('branch_id', $request->branch_id)
                    ->whereDoesntHave('documentNumber')
                    ->select('id', 'customer_id','net_invoice');
                }]);
        }])
            ->select('id', 'branch_id', 'document_id', 'customer_id', 'attendans_num', 'invoice_discount', 'net_invoice', 'serial_number')
            ->get();

        $response = [
            'message' => 'success',
            'data' => $models,
        ];

        return response()->json($response);
    }

}
