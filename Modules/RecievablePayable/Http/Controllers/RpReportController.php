<?php

namespace Modules\RecievablePayable\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Modules\RecievablePayable\Entities\RpBreakDown;
use Modules\RecievablePayable\Transformers\FilterBreakResource;

class RpReportController extends Controller
{
    public function __construct(private RpBreakDown $model)
    {
        $this->model = $model;
    }

    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function filterVourcher(Request $request)
    {
        $models = $this->model->filter($request)->filterbreak($request)->
        doesnthave('children')->orderBy('instalment_date', 'ASC')
            ->where('amount_status', "!=", 'Unpaid');
        if ($request->per_page) {
            $models = ['data' => $models->paginate($request->per_page), 'paginate' => true];
        } else {
            $models = ['data' => $models->get(), 'paginate' => false];
        }
        return responseJson(200, 'success', FilterBreakResource::collection($models['data']), $models['paginate'] ? getPaginates($models['data']) : null);
    }

    public function filterBreakClient(Request $request)
    {
        $request->validate([
            'year' => 'required',
            'client_type_id' => 'required',
        ]);

        // Get the table name based on client_type_id
        $clientType = DB::table('general_client_types')->where('id', $request->client_type_id)->first();
        $tableName = $clientType->db_table;


        $results = DB::table('rp_break_downs as r')
            ->select('r.customer_id', 'c.name', 'c.name_e',
                DB::raw("SUM(CASE WHEN YEAR(r.instalment_date) < $request->year THEN r.debit ELSE 0 END) AS 'Before'"),
                DB::raw("SUM(CASE WHEN YEAR(r.instalment_date) = $request->year THEN r.debit ELSE 0 END) AS 'During'"),
                DB::raw("SUM(CASE WHEN YEAR(r.instalment_date) > $request->year THEN r.debit ELSE 0 END) AS 'After'"),
                DB::raw("SUM(CASE WHEN YEAR(r.instalment_date) < $request->year THEN IFNULL(s.amount, 0) ELSE 0 END) AS 'paid_before'"),
                DB::raw("SUM(CASE WHEN YEAR(r.instalment_date) = $request->year THEN IFNULL(s.amount, 0) ELSE 0 END) AS 'paid_during'"),
                DB::raw("SUM(CASE WHEN YEAR(r.instalment_date) > $request->year THEN IFNULL(s.amount, 0) ELSE 0 END) AS 'paid_after'")
            )
            ->join($tableName . ' as c', 'r.customer_id', '=', 'c.id')
            ->leftJoin('general_break_settlements as s', 'r.id', '=', 's.break_id')
            ->where('r.client_type_id', $request->client_type_id)
            ->groupBy('r.customer_id', 'c.name')
            ->get();

        return $results;
    }

    public function filterFollowSettlements(Request $request)
    {
        $request->validate([
            'year' => 'required',
            'client_type_id' => 'required|exists:general_client_types,id',
        ]);

        // Get the table name based on client_type_id
        $clientType = DB::table('general_client_types')->where('id', $request->client_type_id)->first();
        $tableName = $clientType->db_table;

        $data = DB::table('rp_break_downs')
            ->join('general_break_settlements', 'rp_break_downs.id', '=', 'general_break_settlements.break_id')
            ->join($tableName . ' as c', 'rp_break_downs.customer_id', '=', 'c.id')
            ->select('c.name', 'c.name_e', 'rp_break_downs.instalment_date',
                'rp_break_downs.customer_id', 'rp_break_downs.debit', DB::raw('SUM(general_break_settlements.amount) as total_amount'),
                DB::raw('(rp_break_downs.debit - SUM(general_break_settlements.amount)) as remaining_amount'),
                DB::raw('CASE
            WHEN YEAR(rp_break_downs.instalment_date) < ? THEN "previous"
            WHEN YEAR(rp_break_downs.instalment_date) = ? THEN "current"
            WHEN YEAR(rp_break_downs.instalment_date) = ? + 1 THEN "next"
            ELSE "future"
            END AS time_frame'))
            ->where('rp_break_downs.debit', '>', 0)
            ->groupBy('rp_break_downs.id')
            ->orderBy('rp_break_downs.customer_id')
            ->orderBy('rp_break_downs.instalment_date');

        $data->addBinding($request->year, 'select');
        $data->addBinding($request->year, 'select');
        $data->addBinding($request->year, 'select');

        $results = $data->get();
        return $results;
    }

    public function filterFollowSPayments(Request $request)
    {
        $request->validate([
            'client_type_id' => 'required|exists:general_client_types,id',
        ]);

        // Get the table name based on client_type_id
        $clientType = DB::table('general_client_types')->where('id', $request->client_type_id)->first();
        $tableName = $clientType->db_table;

        $models = DB::table('rp_break_downs')
            ->where('debit', '>', 0)
            ->leftJoin('general_break_settlements', 'rp_break_downs.id', '=', 'general_break_settlements.break_id')
            ->leftJoin('rp_installment_payment_types as ipt', 'rp_break_downs.instalment_type_id', '=', 'ipt.id')
            ->leftJoin($tableName . ' as c', 'rp_break_downs.customer_id', '=', 'c.id')
            ->select('ipt.name as ipt_name', 'ipt.name_e as ipt_name_en',
                'c.name', 'c.name_e',
                'rp_break_downs.debit', 'rp_break_downs.instalment_date', 'rp_break_downs.id',

                DB::raw('COALESCE(SUM(general_break_settlements.amount),0) as total_amount'),
                DB::raw('CASE
                WHEN (SUM(general_break_settlements.amount) >= rp_break_downs.debit) THEN "paid"
                WHEN (SUM(general_break_settlements.amount) < rp_break_downs.debit) THEN "partial_paid"
                ELSE "unpaid"
                END AS "payment_status"
                '),
                // payment_timing 'before' 'at_time' 'after'
                DB::raw('CASE

                    WHEN (MAX(general_break_settlements.created_at) < rp_break_downs.instalment_date) THEN "early_payment"
                    WHEN (MAX(general_break_settlements.created_at) = rp_break_downs.instalment_date) THEN "on_time_payment"
                    WHEN (MAX(general_break_settlements.created_at) > rp_break_downs.instalment_date) THEN "late_payment"
                    ELSE "--"
                    END AS "payment_timing"
                '),
                DB::raw('DATE(MAX(general_break_settlements.created_at)) as last_instalment_date')
            )
            ->groupBy('rp_break_downs.id')
            ->orderBy('rp_break_downs.customer_id');
        if ($request->client_type_id) {
            $models->where('client_type_id', $request->client_type_id);
        }
        if ($request->instalment_type_id) {
            $models->where('instalment_type_id', $request->instalment_type_id);
        }
        if ($request->clients_ids) {
            $models->whereIn('customer_id', $request->clients_ids);
        }
        if ($request->start_date && $request->end_date) {
            $models->whereDate('rp_break_downs.created_at', '>=', $request->start_date)
                ->whereDate('rp_break_downs.created_at', '<=', $request->end_date);
        }

        if ($request->start_date_instalment && $request->end_date_instalment) {
            $models->where('instalment_date', '>=', $request->start_date_instalment)
                ->where('instalment_date', '<=', $request->end_date_instalment);
        }

        return $models->get();
    }


}
