<?php

namespace App\Http\Controllers;

use App\Models\ContactGroup;
use App\Constants\FormType;
use App\Constants\Message;
use Illuminate\Http\Request;
use Auth;

class ContactGroupController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if(!Auth::user()->can('contact_group.browse')) {
            return back()->with([
              Message::ERROR_KEY => trans('message.no_permission'),
              'alert-type' => 'warning'
            ], 403);
        }
        $searchString = $request->get('query');
        $contactGroups=ContactGroup::query();
        if(!empty($searchString)){
            $contactGroups->where(function ($query) use ($searchString){
                $query->where('name', 'like', '%'.$searchString.'%');
            });
        }
        $itemCount = $contactGroups->count();
        $contactGroups = $contactGroups->paginate(paginationCount());
        $offset = offset($request->page);
        return view('contact_group.index',compact('contactGroups', 'itemCount', 'offset'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(ContactGroup $group)
    {
        if(!Auth::user()->can('contact_group.add')) {
            return back()->with([
              Message::ERROR_KEY => trans('message.no_permission'),
              'alert-type' => 'warning'
            ], 403);
        }
        //
        $title = trans('app.create');
        $formType = FormType::CREATE_TYPE;
        return view('contact_group.form', compact('formType', 'title', 'group'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function save(Request $request, ContactGroup $group)
    {
        if(!Auth::user()->can('contact_group.add') && !Auth::user()->can('contact_group.edit')) {
            return back()->with([
              Message::ERROR_KEY => trans('message.no_permission'),
              'alert-type' => 'warning'
            ], 403);
        }
        $group->name = $request->name;
        $group->type = $request->type;
        if($group->save()){
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
    public function show(ContactGroup $group)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\ContactGroup  $contactGroup
     * @return \Illuminate\Http\Response
     */
    public function edit(ContactGroup $group)
    {
        if(!Auth::user()->can('contact_group.edit')) {
            return back()->with([
              Message::ERROR_KEY => trans('message.no_permission'),
              'alert-type' => 'warning'
            ], 403);
        }
        $title = trans('app.edit');
        $formType = FormType::EDIT_TYPE;
        return view('contact_group.form', compact('formType', 'title', 'group'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\ContactGroup  $contactGroup
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ContactGroup $group)
    {
        //
        $group->name = $request->name;

        if($group->save()){
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
    public function destroy(ContactGroup $group)
    {
        if(!Auth::user()->can('contact_group.delete')) {
            return back()->with([
              Message::ERROR_KEY => trans('message.no_permission'),
              'alert-type' => 'warning'
            ], 403);
        }
        if(count($group->contact)>0){
            session()->flash(Message::ERROR_KEY, trans('message.item_saved_fail'));
        }else{
            $group->delete();
            session()->flash(Message::SUCCESS_KEY, trans('message.item_saved_success'));
        }
        // return redirect()->back();
    }
}
