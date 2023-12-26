<?php

namespace App\Http\Controllers\AccountStatement;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;

class AccountStatementController extends Controller
{


    public function index(Request $request)
    {

        $balance = 0;
       $query_settlements=DB::table('general_break_settlements')
       ->join('rp_break_downs','general_break_settlements.break_id','rp_break_downs.id')
       ->join('general_voucher_headers','general_break_settlements.voucher_header_id','general_voucher_headers.id')
       ->join('general_documents','general_documents.id','general_voucher_headers.document_id')
       ->where('rp_break_downs.client_type_id',$request->client_type_id)->where('rp_break_downs.customer_id',$request->customer_id)
       ->whereNull('rp_break_downs.deleted_at')
       ->whereNull('general_break_settlements.deleted_at')
       ->selectRaw('general_documents.name,general_documents.name_e,"" as debit,general_break_settlements.amount as paid_amount,"" as credit,general_break_settlements.created_at as date,general_voucher_headers.prefix,general_voucher_headers.notes');


       $invoices_amount=DB::table('rp_break_downs')->join('general_document_headers','rp_break_downs.break_id','general_document_headers.id')
       ->join('general_documents','general_documents.id','rp_break_downs.document_id')
       ->where('rp_break_downs.client_type_id',$request->client_type_id)->where('rp_break_downs.customer_id',$request->customer_id)
       ->whereNull('rp_break_downs.deleted_at')
    //    ->where('document_id',45)
       ->selectRaw('general_documents.name,general_documents.name_e,debit,"" as paid_amount,credit,rp_break_downs.created_at as date,general_document_headers.prefix,"" as notes')
       ->union($query_settlements)->orderBy('date')
       ->get()->map(function($item,$index) use(&$balance){
            $balance += $item->debit > 0 ?$item->debit : ($item->credit > 0 ? -$item->credit : -$item->paid_amount);
            $item->balance = $balance;
            return $item;
        });




        return responseJson(200, 'success', $invoices_amount, null);
    }


}
