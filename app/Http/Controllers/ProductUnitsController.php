<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Constants\Message;
use App\Constants\UserRole;
use App\Models\Unit;
use Auth;

class ProductUnitsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if(!Auth::user()->can('product-type.browse')){
            return back()->with([Message::ERROR_KEY=>trans('message.no_permission'), 'alert-type' => 'warning'], 403);
        }

        if (!empty($request->search)) {
            $units = Unit::where('short_name', 'like', '%' . $request->search . '%');
        }else{
            $units = Unit::where('short_name', '!=', '');
        }

        $itemCount = $units->count();
        $units = $units->sortable()->orderBy('short_name')->paginate(paginationCount());
        $offset = offset($request->page);

        return view('units/index', compact('itemCount', 'offset', 'units'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Unit $unit)
    {
        if(!Auth::user()->can('product-type.add')){
            return back()->with([Message::ERROR_KEY=>trans('message.no_permission'), 'alert-type' => 'warning'], 403);
        }

        $title = trans('app.create');

        return view('units/form', compact('unit', 'title'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function save(Request $request, Unit $unit)
    {
        if(!Auth::user()->can('product-type.add') && !Auth::user()->can('product-type.edit')){
            return back()->with([Message::ERROR_KEY=>trans('message.no_permission'), 'alert-type' => 'warning'], 403);
        }

        $this->validate($request, [
            'name' => 'required|max:255',
        ]);

        $unit->actual_name = $request->name;
        $unit->short_name = $request->name;
        $unit->created_by = Auth::user()->id;
        $unit->save();

        session()->flash(Message::SUCCESS_KEY, trans('message.item_saved_success'));
        return redirect()->route('product-units.index');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if(!Auth::user()->can('product-type.edit')){
            return back()->with([Message::ERROR_KEY=>trans('message.no_permission'), 'alert-type' => 'warning'], 403);
        }

        $title = trans('app.edit');
        $unit = Unit::findOrFail($id);
        return view('units/form', compact('unit', 'title'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Unit $unit)
    {
        if(!Auth::user()->can('product-type.delete')){
            return back()->with([Message::ERROR_KEY=>trans('message.no_permission'), 'alert-type' => 'warning'], 403);
        }

        abort(404);
    }
}
