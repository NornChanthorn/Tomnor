<?php

namespace App\Http\Controllers;

use App\Models\ExtendedProperty;
use App\Constants\FormType;
use App\Constants\Message;
use Illuminate\Http\Request;
use Auth;

class MethodPaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if(!Auth::user()->can('payment_method.browse')){
            return back()->with([Message::ERROR_KEY=>trans('message.no_permission'), 'alert-type' => 'warning'], 403);
        }
        $searchString = $request->get('search');
        $methodPayments=ExtendedProperty::where('group_name','payment_methods');
        if(!empty($searchString)){
            $methodPayments->where(function ($query) use ($searchString){
                $query->where('value', 'like', '%'.$searchString.'%');
            });
        }
        $itemCount = $methodPayments->count();
        $methodPayments = $methodPayments->paginate(paginationCount());
        $offset = offset($request->page);
        return view('method_payment.index',compact('methodPayments', 'itemCount', 'offset'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(ExtendedProperty $methodPayment)
    {
        if(!Auth::user()->can('payment_method.add')){
            return back()->with([Message::ERROR_KEY=>trans('message.no_permission'), 'alert-type' => 'warning'], 403);
        }
        //
        $title = trans('app.create');
        $formType = FormType::CREATE_TYPE;
        return view('method_payment.form', compact('formType', 'title', 'methodPayment'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, ExtendedProperty $methodPayment)
    {
        if(!Auth::user()->can('payment_method.add') && !Auth::user()->can('payment_method.edit')){
            return back()->with([Message::ERROR_KEY=>trans('message.no_permission'), 'alert-type' => 'warning'], 403);
        }
        if(empty($methodPayment->id)){
            $methodPayment->property_name = $request->value;
        }
     
        $methodPayment->value = $request->name;
        $methodPayment->group_name='payment_methods';
        if($methodPayment->save()){
            session()->flash(Message::SUCCESS_KEY, trans('message.item_saved_success'));
        }
        else {
            session()->flash(Message::ERROR_KEY, trans('message.item_saved_fail'));
        }
        return redirect()->back();
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\ContactGroup  $contactGroup
     * @return \Illuminate\Http\Response
     */
    public function show(ExtendedProperty $methodPayment)
    {
        if(!Auth::user()->can('payment_method.browse')){
            return back()->with([Message::ERROR_KEY=>trans('message.no_permission'), 'alert-type' => 'warning'], 403);
        }
        $title = trans('app.create');
        $formType = FormType::SHOW_TYPE;
        return view('method_payment.form', compact('formType', 'title', 'methodPayment'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\ContactGroup  $contactGroup
     * @return \Illuminate\Http\Response
     */
    public function edit(ExtendedProperty $methodPayment)
    {
        if(!Auth::user()->can('payment_method.edit')){
            return back()->with([Message::ERROR_KEY=>trans('message.no_permission'), 'alert-type' => 'warning'], 403);
        }
        $title = trans('app.create');
        $formType = FormType::EDIT_TYPE;
        return view('method_payment.form', compact('formType', 'title', 'methodPayment'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\ContactGroup  $contactGroup
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ExtendedProperty $methodPayment)
    {
        dd($request);
        $methodPayment->property_name = $request->value;
        $methodPayment->value = $request->name;
        if($methodPayment->value()){
            session()->flash(Message::SUCCESS_KEY, trans('message.item_saved_success'));
        }
        else {
            session()->flash(Message::ERROR_KEY, trans('message.item_saved_fail'));
        }
        return redirect()->back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\ContactGroup  $contactGroup
     * @return \Illuminate\Http\Response
     */
    public function destroy(ExtendedProperty $methodPayment)
    {   
        if(!Auth::user()->can('payment_method.delete')){
            return back()->with([Message::ERROR_KEY=>trans('message.no_permission'), 'alert-type' => 'warning'], 403);
        }
        if(count($methodPayment->invoices)>0){
            session()->flash(Message::ERROR_KEY, trans('message.data_in_used'));
        }else{
            $methodPayment->delete();
            session()->flash(Message::SUCCESS_KEY, trans('message.item_deleted_success'));
        }
           
        // return redirect()->back();
    }
}
