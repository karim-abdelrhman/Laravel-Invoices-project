<?php

namespace App\Http\Controllers;

use App\Models\invoice_attachments;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class InvoiceAttachmentsController extends Controller
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
        $this->validate($request , [
            'file_name' => 'mimes:pdf,jpeg,png,jpg'
        ],[
           'file_name.mimes' => 'pdf,jpeg,png,jpg يجب ان تكون صيغة الملف '
        ]);

        //dd($request->file('file_name'));
        $file = $request->file('file_name');
        $file_name = $file->getClientOriginalName();

        invoice_attachments::create([
           'file_name' => $file_name,
            'invoice_number' => $request->invoice_number,
            'Created_by' => Auth::user()->name,
            'invoice_id' => $request->invoice_id
        ]);
        $fileName = $request->file_name->getClientOriginalName();
        $request->file_name->move(public_path('Attachments/' . $request->invoice_number), $fileName);

        session()->flash('Add', 'تم اضافة المرفق بنجاح');
        return back();
    }

    /**
     * Display the specified resource.
     */
    public function show(invoice_attachments $invoice_attachments)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(invoice_attachments $invoice_attachments)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, invoice_attachments $invoice_attachments)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        $deleted_attachment = invoice_attachments::where('id' , $request->id_file)->delete();
        Storage::disk('Attachments')->delete($request->invoice_number.'/'.$request->file_name);
        session()->flash('delete' , 'تم حذف المرفق بنجاح');
        return back();
    }
}
