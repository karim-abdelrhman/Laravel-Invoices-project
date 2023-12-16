<?php

namespace App\Http\Controllers;

use App\Models\invoice;
use App\Models\invoice_attachments;
use App\Models\invoice_details;
use App\Models\section;
use App\Models\User;
use App\Notifications\Add_Invoice;
use App\Notifications\AddInvoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Notifications\Notifiable;

class InvoiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $invoices = invoice::all();
        return view('invoices.invoices' , compact('invoices'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $sections = section::all();
        return view('invoices.add_invoice' , compact('sections'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        invoice::create([
            'invoice_number' => $request->invoice_number,
            'invoice_Date' => $request->invoice_Date,
            'Due_date' => $request->Due_date,
            'product' => $request->product,
            'section_id' => $request->Section,
            'Amount_collection' => $request->Amount_collection,
            'Amount_Commission' => $request->Amount_Commission,
            'Discount' => $request->Discount,
            'Value_VAT' => $request->Value_VAT,
            'Rate_VAT' => $request->Rate_VAT,
            'Total' => $request->Total,
            'Status' => 'غير مدفوعة',
            'Value_Status' => 2,
            'note' => $request->note,
        ]);

        $invoice_id = invoice::latest()->first()->id;
        invoice_details::create([
            'id_Invoice' => $invoice_id,
            'invoice_number' => $request->invoice_number,
            'product' => $request->product,
            'Section' => $request->Section,
            'Status' => 'غير مدفوعة',
            'Value_Status' => 2,
            'note' => $request->note,
            'user' => (Auth::user()->name),
        ]);

        if($request->hasFile('pic')){
            $invoice_id = invoice::latest()->first()->id;
            $image = $request->file('pic');
            $file_name = $image->getClientOriginalName();
            $invoice_number = $request->invoice_number;

            $attachments = new invoice_attachments();
            $attachments->file_name = $file_name;
            $attachments->invoice_number = $invoice_number;
            $attachments->created_by = Auth::user()->name;
            $attachments->invoice_id = $invoice_id;
            $attachments->save();

            //move picture
            $imageName = $request->pic->getClientOriginalName();
            $request->pic->move(public_path('Attachments/' . $invoice_number), $imageName);


        }

//        $user = User::first();
//        Notification::send($user , new AddInvoice($invoice_id));

        $user = User::where('id' , '!=' , Auth::user()->id)->get();
        $invoice = invoice::latest()->first();
        Notification::send($user , new Add_Invoice($invoice));

//        $user->notify(new \App\Notifications\Add_Invoice($invoice));

        session()->flash('Add', 'تم اضافة الفاتورة بنجاح');
        return redirect('/invoices');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $invoice = invoice::findOrFail($id);


        return view('invoices.invoice_status' , compact('invoice'));

    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
       $invoice = invoice::findOrFail($id);
       $sections = section::all();
       return view('invoices.edit' , compact('invoice' , 'sections') );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request , $id)
    {
//        dd($request);
        $invoice = invoice::where('id' , $id)->firstOrFail();

        $invoice->invoice_number = $request->invoice_number;
        $invoice->invoice_Date = $request->invoice_Date;
        $invoice->Due_date = $request->Due_date;
        $invoice->product = $request->product;
        $invoice->section_id = $request->section_id;
        $invoice->Amount_collection = $request->Amount_collection;
        $invoice->Amount_Commission = $request->Amount_Commission;
        $invoice->Discount = $request->Discount;
        $invoice->Value_VAT = $request->Value_VAT;
        $invoice->Rate_VAT = $request->Rate_VAT;
        $invoice->Total = $request->Total;
        $invoice->note = $request->note;

//        $invoice_details = new invoice_details();
//        $invoice_details->create([
//            'id_Invoice' => $id,
//            'invoice_number' => $request->invoice_number,
//            'product' => $request->product,
//            'Section' => $request->section_id,
//            'Status' => 'غير مدفوعة',
//            'Value_Status' => 2,
//            'note' => $request->note,
//            'user' => (Auth::user()->name),
//        ]);
//
//        $invoice_details->save();

        $invoice->save();

        session()->flash('edit', 'تم تعديل الفاتورة بنجاح');
        return redirect('/invoices');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        $deleted_invoice = invoice::where('id' , $request->invoice_id)->first();
        $invoice_details = invoice_attachments::where('invoice_id' , $deleted_invoice->id)->first();

        if($request->page_id == 1)
        {
            if(!empty($invoice_details->invoice_number)){
                Storage::disk('Attachments')->deleteDirectory($invoice_details->invoice_number);
            }
            $deleted_invoice->forceDelete();
        }

        if($request->page_id == 2)
        {
            if(!empty($invoice_details->invoice_number)){
                Storage::disk('Attachments')->deleteDirectory($invoice_details->invoice_number);
            }
            $deleted_invoice->delete();
        }


        session()->flash('delete_invoice');
        return redirect('/invoices');
    }
    public function getProducts($id){
        $products = DB::table('products')->where('section_id' , $id)->pluck('product_name' , 'id');
        return json_encode($products);
    }

    public function updateStatus(Request $request , $id)
    {
//        dd($request , $id);
        $invoice = invoice::findOrFail($id);
//        $invoice_details = new invoice_details();

        //this's in case the invoice is paid.
        if($request->Status === 'مدفوعة'){

            $invoice->Status = $request->Status;
            $invoice->Value_Status = 1;
            $invoice->Payment_Date = $request->Payment_Date;

            invoice_details::create([
                'id_Invoice' => $invoice->id,
                'invoice_number' => $request->invoice_number,
                'product' => $request->product,
                'Section' => $request->Section,
                'Status' => 'مدفوعة',
                'Value_Status' => 1,
                'Payment_Date' => $request->Payment_Date,
                'note' => $request->note,
                'user' => (Auth::user()->name),
            ]);

            $invoice->save();
//            $invoice_details->save();
        }
        else{
            $invoice->Status = 'مدفوعة جزئيا';
            $invoice->Value_Status = 3;
            $invoice->Payment_Date = $request->Payment_Date;

            invoice_details::create([
                'id_Invoice' => $invoice->id,
                'invoice_number' => $request->invoice_number,
                'product' => $request->product,
                'Section' => $request->Section,
                'Status' => 'مدفوعة جزئيا',
                'Value_Status' => 3,
                'Payment_Date' => $request->Payment_Date,
                'note' => $request->note,
                'user' => (Auth::user()->name),
            ]);

            $invoice->save();
        }

        Session()->flash('status_update' , 'تم تحديث حالة الدفع بنجاح');
        return redirect('/invoices');

    }

    public function invoicesArchive()
    {
        $invoices = invoice::all()->where('deleted_at' , '!=' , 'NULL');
        return view('invoices.invoices_archive' , compact('invoices'));
    }

    public function invoicesPaid(){
        $invoices_paid = invoice::where('Value_Status' , '1')->get();
        return view('invoices.invoices_paid' , compact('invoices_paid'));
    }

    public function invoicesPartial(){
        $invoices_partial = invoice::where('Value_Status' , '3')->get();
        return view('invoices.invoices_partial' , compact('invoices_partial'));
    }

    public function invoicesNotPaid(){
        $invoices_not_paid = invoice::where('Value_Status' , '2')->get();
        return view('invoices.invoices_not_paid' , compact('invoices_not_paid'));
    }

    public function printInvoice($id){
        $invoice = invoice::where('id' , $id)->first();
        return view('invoices.print_invoice', compact('invoice'));
    }

    public function markAllAsRead()
    {
        $user = Auth::user();
        foreach ($user->unreadNotifications as $notification) {
            $notification->markAsRead();
        }
        return back();
    }
}
