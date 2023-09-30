<?php

namespace App\Http\Controllers;

use App\Constants\UserRole;
use App\Constants\Message;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Role;
use App\Models\Permission;
use App\Http\Requests\RoleRequest;

use Auth;

class RoleController extends Controller
{
    /**
     * Display a listing of users.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function index(Request $request)
    {
        if(!Auth::user()->can('role.browse')){
            return back()->with(['message'=>trans('message.no_permission')], 403);
        }

        $roles = Role::orderBy('name');
        if (!empty($request->search)) {
            $roles = $roles->where('name', 'like', '%' . $request->search . '%')
                    ->orWhere('display_name', 'like', '%' . $request->search . '%');
        }

        $itemCount = $roles->count();
        $roles = $roles->sortable()->orderBy('name')->paginate(paginationCount());
        $offset = offset($request->page);

        return view('role/index', compact('itemCount', 'offset', 'roles'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        $this->user = Auth::user();
        if(!$this->user->can('role.add')){
            return back()->with(['message'=>trans('message.no_permission')], 403);
        }

        $permission_arr = [];
        $permissions = Permission::orderBy('display_name')->get();
        foreach ($permissions as $key => $pms) {
            if(!empty($pms->group))
                $permission_arr[$pms->group][] = $pms;
            else
                $permission_arr['Misc.'][] = $pms;
        }
        $data = [
            'permissions' => $permission_arr,
            'rolePermissions' => [],
            'title' => trans('app.create')
        ];
        
        $role = new Role();
        return view('role.form', $data)->with(compact('role'));
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id)
    {
        $this->user = Auth::user();
        if(!$this->user->can('role.edit')){
            if(request()->ajax()){
                return Response::json(['message'=>trans('message.no_permission'), 'alert-type' => 'warning'], 403);
            }
            return back()->with(['message'=>trans('message.no_permission'), 'alert-type' => 'warning'], 403);
        }

        $role = Role::find($id);
        $permission_arr = [];
        $permissions = Permission::orderBy('display_name')->get();
        foreach ($permissions as $key => $pms) {
            if(!empty($pms->group))
                $permission_arr[$pms->group][] = $pms;
            else
                $permission_arr['Misc.'][] = $pms;
        }

        $data = [
            'permissions' => $permission_arr,
            'rolePermissions' => $role->permissions()->pluck('id')->all(),
            'title' => trans('app.edit')
        ];

        return view('role.form', $data)->with('role', $role);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store(RoleRequest $request)
    {
        $this->user = Auth::user();
        if(!$this->user->can('role.add')){
            if($request->ajax()){
                return Response::json(['message'=>trans('message.no_permission'), 'alert-type' => 'warning'], 403);
            }
            session()->flash(Message::ERROR_KEY, trans('message.no_permission'));
            return back()->with(['message'=>trans('message.no_permission'), 'alert-type' => 'warning'], 403);
        }

        $role =  new Role();
        $input = $request->all();

        $role->fill($input);
        $role->name = str_slug($input['display_name']);
        $role->user_id = Auth::id();
        $role->save();

        //Store role permission
        //---------------------
        $role->permissions()->sync($request->input('permissions'));

        $msg = [
                    'message'    => 'New role has been added',
                    'alert-type' => 'success',
                ];
        session()->flash(Message::SUCCESS_KEY, trans('message.item_saved_success'));
        if(!empty($request->back_url))
            return redirect($request->back_url)->with($msg);

        return redirect()->route('role.index')->with($msg);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function update(RoleRequest $request, $id)
    {
        $this->user = Auth::user();
        if(!$this->user->can('role.edit')){
            if($request->ajax()){
                return Response::json(['message'=>trans('message.no_permission'), 'alert-type' => 'warning'], 403);
            }
            return back()->with(['message'=>trans('message.no_permission'), 'alert-type' => 'warning'], 403);
        }

        //Find the role and update its details
        $role = Role::find($id);
        $input = $request->all();
        $role->fill($input);
        $role->name = str_slug($input['display_name']);
        $role->save();

        //attach the new permissions to the role
        $role->permissions()->sync($request->input('permissions'));

        $msg = [
                    'message'    => 'Role updated successfully',
                    'alert-type' => 'success',
                ];

        session()->flash(Message::SUCCESS_KEY, trans('message.item_saved_success'));
        return redirect()->route('role.index')
            ->with($msg);
    }
}
