<?php

namespace App\Http\Controllers;

use App\Models\invoice;
use App\Models\invoice_attachments;
use App\Models\invoice_details;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class InvoiceDetailsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $invoice_info = invoice::findOrFail($id);
        $invoice_details = invoice_details::where('id_Invoice' , $id)->get();
        $invoice_attachments = invoice_attachments::where('invoice_id' , $id)->get();


//        $notify_id = DB::table('notifications')->where('data->id' , '=' , $id)->pluck('id');
//        dd($notify_id);
//        DB::table('notifications')->where('id' , $notify_id)->update(['read_at' => now()]);

        return view('invoices.invoice_details' , compact('invoice_details' , 'invoice_info' , 'invoice_attachments'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, invoice_details $invoice_details)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(invoice_details $invoice_details)
    {
        //
    }

    public function open_file($invoice_number , $file_name)
    {
        $files = Storage::disk('Attachments')->getDriver()->getAdapter()->applyPathPrefix($invoice_number.'/'.$file_name);
        return response()->file($files);
    }
    public function get_file($invoice_number,$file_name)
    {
        $contents= Storage::disk('Attachments')->getDriver()->getAdapter()->applyPathPrefix($invoice_number.'/'.$file_name);
        return response()->download( $contents);
    }
}
