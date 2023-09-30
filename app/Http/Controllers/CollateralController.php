<?php

namespace App\Http\Controllers;

use Auth;
use App\Constants\FormType;
use App\Constants\Message;
use App\Models\Collateral;
use App\Traits\FileHandling;
use App\Constants\ExtendedProperty as EPropertyType;
use App\Models\ExtendedProperty as CollateralType;
use Illuminate\Http\Request;

class CollateralController extends Controller
{
    use FileHandling;
    /** @var string Folder name to store sale document */
    private $documentFolder = 'collateral';
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($loan_id)
    {
        if(!Auth::user()->can('collateral_type.add')){
            return back()->with([Message::ERROR_KEY=>trans('message.no_permission'), 'alert-type' => 'warning'], 403);
        }
        $title  = __("app.create");
        $collateral = new  Collateral(); 
        return view('collateral.form',compact('title','loan_id','collateral'));
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function save(Request $request,$loan_id)
    {
        if(!Auth::user()->can('collateral.edit') && !Auth::user()->can('collateral.add')){
            return back()->with([Message::ERROR_KEY=>trans('message.no_permission'), 'alert-type' => 'warning'], 403);
        }
        $collateral = Collateral::find($request->id) ?? new  Collateral();
        $collateral->name  = $request->name;
        $collateral->type_id  = $request->type_id;
        $collateral->loan_id  = $loan_id;
        $collateral->value  = $request->value;
        if (!empty($request->file('files'))){
            $collateral->files = $this->uploadFile($this->documentFolder, $request->file('files'));
        }
        if ($collateral->save()) {
            session()->flash(Message::SUCCESS_KEY, trans('message.item_saved_success'));
        } else {
            session()->flash(Message::ERROR_KEY, trans('message.item_saved_fail'));
        }

        return redirect()->route('loan-cash.show',[$loan_id,'get'=>'collaterals']);
        //
    }
    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Collateral  $collateral
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if(!Auth::user()->can('collateral.edit')){
            return back()->with([Message::ERROR_KEY=>trans('message.no_permission'), 'alert-type' => 'warning'], 403);
        }
        $title  = __("app.edit");
        $collateral=Collateral::find($id);
        $loan_id=$collateral->loan_id;
        return view('collateral.form',compact('title','loan_id','collateral'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Collateral  $collateral
     * @return \Illuminate\Http\Response
     */
    public function destroy(Collateral $collateral)
    {
        if(!Auth::user()->can('collateral.delete')) {
            session()->flash(Message::SUCCESS_KEY, trans('message.no_permission'));
        }
        $collateral->delete();
        session()->flash(Message::SUCCESS_KEY, trans('message.item_deleted_success'));
        //
    }



    public function typeindex(Request $request)
    {
        if(!Auth::user()->can('collateral_type.browse')){
            return back()->with([Message::ERROR_KEY=>trans('message.no_permission'), 'alert-type' => 'warning'], 403);
        }
        $positions = CollateralType::collateralType();
        if (!empty($request->search)) {
            $positions = $positions->where('value', 'like', '%' . $request->search . '%');
        }

        $itemCount = $positions->count();
        $positions = $positions->sortable()->orderBy('value')->paginate(paginationCount());
        $offset = offset($request->page);

        return view('collateral_type.index', compact('itemCount', 'offset', 'positions'));
    }
    public function typeCreate()
    {
        if(!Auth::user()->can('collateral_type.add')){
            return back()->with([Message::ERROR_KEY=>trans('message.no_permission'), 'alert-type' => 'warning'], 403);
        }
        $title = trans('app.create');
        return view('collateral_type.form', compact('title'));
    }
    public function typeEdit($id)
    {
        if(!Auth::user()->can('collateral_type.edit')){
            return back()->with([Message::ERROR_KEY=>trans('message.no_permission'), 'alert-type' => 'warning'], 403);
        }
        $collateral_type= CollateralType::find($id);
        $title = trans('app.edit');
        return view('collateral_type.form', compact('title','collateral_type'));
    }
    public function typeSave(Request $request)
    {
        if(!Auth::user()->can('collateral_type.add') && !Auth::user()->can('collateral_type.edit')){
            return back()->with([Message::ERROR_KEY=>trans('message.no_permission'), 'alert-type' => 'warning'], 403);
        }
        $this->validate($request, [
            'title' => 'required|max:255',
        ]);

        $collateral_type = CollateralType::find($request->id) ?? new CollateralType();
        $collateral_type->property_name = EPropertyType::COLLATERAL_TYPE;
        $collateral_type->group_name = EPropertyType::COLLATERAL_TYPE;
        $collateral_type->value = $request->title;

        if ($collateral_type->save()) {
            session()->flash(Message::SUCCESS_KEY, trans('message.item_saved_success'));
        } else {
            session()->flash(Message::ERROR_KEY, trans('message.item_saved_fail'));
        }

        return redirect()->route('collateral-type.index');
    }
}
