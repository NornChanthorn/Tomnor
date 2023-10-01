<?php

namespace App\Http\Controllers;

use App\DownPayment;
use Doctrine\Deprecations\Deprecation;
use Illuminate\Http\Request;

class DepreciationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $data = Deprecation::all();
        return view('loan/contract',compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\DownPayment  $downPayment
     * @return \Illuminate\Http\Response
     */
    public function show(DownPayment $downPayment)
    {
        //
        
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\DownPayment  $downPayment
     * @return \Illuminate\Http\Response
     */
    public function edit(DownPayment $downPayment)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\DownPayment  $downPayment
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, DownPayment $downPayment)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\DownPayment  $downPayment
     * @return \Illuminate\Http\Response
     */
    public function destroy(DownPayment $downPayment)
    {
        //
    }
    
    // public function down_payment()
    // {
    //     return $this->hasOne(DownPayment::class);
    // }
}