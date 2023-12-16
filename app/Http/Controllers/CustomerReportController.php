<?php

namespace App\Http\Controllers;

use App\Models\invoice;
use App\Models\product;
use App\Models\section;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CustomerReportController extends Controller
{
    public function index()
    {
        $sections = section::all();
        return view('reports.customers_reports' , compact('sections'));
    }

    public function customerSearch(Request $request)
    {
        $sections = section::all();
        $product = product::where('product_name' , $request->product)->first();
        $section = section::where('id' , $request->section)->first();
        $start_at = date($request->start_at);
        $end_at = date($request->end_at);
        $invoices = DB::table('invoices')
            ->where('section_id', '=', $request->section)
            ->where('product', '=', $request->product)
            ->whereBetween('invoice_Date',[$start_at,$end_at])
            ->get();
        return view('reports.customers_reports' , compact('invoices','sections' , 'section'))->withDetails('invoices');
    }
}
