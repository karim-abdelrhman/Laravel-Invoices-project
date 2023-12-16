<?php

namespace App\Http\Controllers;

use App\Models\product;
use App\Models\section;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $products = product::all();
        $sections = section::all();

        return view('products.products' , compact('products' , 'sections'));
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

        $validated = $request->validate([
            'product_name' => 'required|unique:products|max:255',
            'description' => 'required',
            'section_id' => 'required'
        ],[
            'product_name.unique' => 'هذا المنتج موجود مسبقا',
            'product_name.required' => 'يرجي ادخال اسم المنتج اولا.',
            'section_id.required' => 'هذا الحقل مطلوب',

        ]);

        $product = new product();

        $product->product_name = $request->product_name;
        $product->description = $request->description;
        $product->section_id = $request->section_id;

        $product->save();
        session()->flash('Add', 'تم اضافة المنتج بنجاح ');
        return redirect('/products');
    }

    /**
     * Display the specified resource.
     */
    public function show(product $product)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(product $product)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        $product = product::where('id' , $request->pro_id)->first();
        $section_name = section::where('section_name' , $request->section_name)->first();
        $product->update([
            'product_name' => $request->product_name,
            'section_id' => $section_name->id,
            'description' => $request->description,
        ]);
        session()->flash('Edit', 'تم تعديل المنتج بنجاح ');
        return redirect('/products');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        product::where('id' , $request->pro_id)->delete();
        session()->flash('delete', 'تم حذف المنتج بنجاح ');
        return redirect('/products');
    }
}
