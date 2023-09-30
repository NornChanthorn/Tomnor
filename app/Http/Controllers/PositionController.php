<?php

namespace App\Http\Controllers;

use App\Constants\UserRole;
use Illuminate\Http\Request;
use App\Constants\ExtendedProperty as EPropertyType;
use App\Constants\Message;
use App\Models\ExtendedProperty as Position;
use Auth;

class PositionController extends Controller
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
     * @return Response
     */
    public function index(Request $request)
    {
        if(!Auth::user()->can('position.browse')){
            return back()->with([Message::ERROR_KEY=>trans('message.no_permission'), 'alert-type' => 'warning'], 403);
        }

        $positions = Position::position();
        if (!empty($request->search)) {
            $positions = $positions->where('value', 'like', '%' . $request->search . '%');
        }

        $itemCount = $positions->count();
        $positions = $positions->sortable()->orderBy('value')->paginate(paginationCount());
        $offset = offset($request->page);
        
        return view('position/index', compact('itemCount', 'offset', 'positions'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if(!Auth::user()->can('position.add')){
            return back()->with([Message::ERROR_KEY=>trans('message.no_permission'), 'alert-type' => 'warning'], 403);
        }

        $title = trans('app.create');
        
        return view('position/form', compact('title'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Position $position
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(Position $position)
    {
        if(!Auth::user()->can('position.edit')){
            return back()->with([Message::ERROR_KEY=>trans('message.no_permission'), 'alert-type' => 'warning'], 403);
        }

        $title = trans('app.edit');
        
        return view('position/form', compact('title', 'position'));
    }

    /**
     * Save new or existing position.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function save(Request $request)
    {
        if(!Auth::user()->can('position.add') && !Auth::user()->can('position.edit')){
            return back()->with([Message::ERROR_KEY=>trans('message.no_permission'), 'alert-type' => 'warning'], 403);
        }

        $this->validate($request, [
            'title' => 'required|max:255',
        ]);
        
        $position = Position::find($request->id) ?? new Position();
        $position->property_name = EPropertyType::POSITION;
        $position->group_name = EPropertyType::POSITION;
        $position->value = $request->title;

        if ($position->save()) {
            session()->flash(Message::SUCCESS_KEY, trans('message.item_saved_success'));
        } else {
            session()->flash(Message::ERROR_KEY, trans('message.item_saved_fail'));
        }

        return redirect()->route('position.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if(!Auth::user()->can('position.delete')){
            return back()->with([Message::ERROR_KEY=>trans('message.no_permission'), 'alert-type' => 'warning'], 403);
        }
        
    }
}
