<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use Illuminate\Http\Request;

class InvoiceController extends Controller
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

    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request,Invoice $invoice)
    {
        if ($request->hasFile('img') && $request->file('img')->isValid()) {
            $avatar = $request->file('img');
            $img = upload($avatar,public_path('uploads/invoice'));
        } else {
            $img = null;
        }

        $invoice->title = $request->title;
        $invoice->mobile = $request->mobile;
        $invoice->fax = $request->fax;
        $invoice->tax_number = $request->tax_number;
        $invoice->address = $request->address;
        $invoice->phone = $request->phone;
        $invoice->email = $request->email;
        $invoice->CRN = $request->CRN;
        $invoice->img = $img;
        $invoice->save();
        return response()->json(['isSuccess' => true], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
