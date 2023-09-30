<?php

namespace App\Http\Controllers;

use App\Constants\UserRole;
use Illuminate\Http\Request;
use App\Constants\ExtendedProperty as EPropertyType;
use App\Constants\Message;
use App\Models\ExtendedProperty as Brand;

use Auth;

class BrandController extends Controller
{
    public function __construct()
    {
        //$this->middleware('role:'. UserRole::ADMIN);
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * 
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if(!Auth::user()->can('brand.browse')){
            return back()->with([Message::ERROR_KEY=>trans('message.no_permission'), 'alert-type' => 'warning'], 403);
        }

        $brands = Brand::brand();
        if (!empty($request->search)) {
            $brands = $brands->where('value', 'like', '%' . $request->search . '%');
        }

        $itemCount = $brands->count();
        $brands = $brands->sortable()->orderBy('value')->paginate(paginationCount());
        $offset = offset($request->page);
        
        return view('brand/index', compact('brands', 'itemCount', 'offset'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if(!Auth::user()->can('brand.add')){
            return back()->with([Message::ERROR_KEY=>trans('message.no_permission'), 'alert-type' => 'warning'], 403);
        }

        $title = trans('app.create');
        
        return view('brand/form', compact('title'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Brand $brand
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(Brand $brand)
    {
        if(!Auth::user()->can('brand.edit')){
            return back()->with([Message::ERROR_KEY=>trans('message.no_permission'), 'alert-type' => 'warning'], 403);
        }

        $title = trans('app.edit');

        return view('brand/form', compact('title', 'brand'));
    }

    /**
     * Save new or existing brand.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function save(Request $request)
    {
        if(!Auth::user()->can('brand.add') && !Auth::user()->can('brand.edit')){
            return back()->with([Message::ERROR_KEY=>trans('message.no_permission'), 'alert-type' => 'warning'], 403);
        }

        $this->validate($request, [
            'name' => 'required|max:255',
        ]);
        
        $brand = Brand::find($request->id) ?? new Brand();
        $brand->property_name = EPropertyType::BRAND;
        $brand->value = $request->name;
        $brand->group_name = EPropertyType::BRAND;
        
        if ($brand->save()) {
            session()->flash(Message::SUCCESS_KEY, trans('message.item_saved_success'));
        } else {
            session()->flash(Message::ERROR_KEY, trans('message.item_saved_fail'));
        }
        
        return redirect()->route('brand.index');
    }
    
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if(!Auth::user()->can('brand.delete')){
            return back()->with([Message::ERROR_KEY=>trans('message.no_permission'), 'alert-type' => 'warning'], 403);
        }
    }
}
