<?php

namespace App\Http\Controllers;

use App\Constants\UserRole;
use Illuminate\Http\Response;
use Illuminate\Http\Request;

use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;

use App\Constants\FormType;
use App\Constants\Message;

use App\Models\User;
use App\Models\Role;
use App\Models\Staff;

use App\Http\Requests\UserRequest;

use Auth;

class UserController extends Controller
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
    if(!Auth::user()->can('user.browse')){
      return back()->with([Message::ERROR_KEY=>trans('message.no_permission'), 'alert-type' => 'warning'], 403);
    }

    $users = new User;
    if (!empty($request->search)) {
      $users = $users->where('name', 'like', '%' . $request->search . '%')
      ->orWhere('username', 'like', '%' . $request->search . '%');
    }

    $itemCount = $users->count();
    $users = $users->sortable()->orderBy('name')->paginate(paginationCount());
    $offset = offset($request->page);

    return view('user/index', compact('itemCount', 'offset', 'users'));
  }

  public function create(User $user)
  {
    if(!Auth::user()->can('user.edit')) {
      return back()->with([Message::ERROR_KEY=>trans('message.no_permission'), 'alert-type' => 'warning'], 403);
    }

    $title = trans('app.create');
    $formType = FormType::CREATE_TYPE;
    $roles = Role::getAll();
    $staffs = Staff::orderBy('id', 'desc')->get();

    return view('user/form', compact('formType', 'title', 'user', 'roles', 'staffs'));
  }

  /**
   * Show form to edit existing user.
   *
   * @param User $user
   *
   * @return Response
   */
  public function edit(User $user)
  {
    if(!Auth::user()->can('user.edit')) {
      return back()->with([Message::ERROR_KEY=>trans('message.no_permission'), 'alert-type' => 'warning'], 403);
    }

    // dd(auth()->user()->staff);

    $title = trans('app.edit');
    $formType = FormType::EDIT_TYPE;
    $roles = Role::getAll();
    $staffs = Staff::orderBy('id', 'desc')->get();

    return view('user/form', compact('formType', 'title', 'user', 'roles', 'staffs'));
  }

  /**
   * Save new or existing user.
   *
   * @param UserRequest $request
   * @param User $user
   *
   * @return Response
   */
  public function save(UserRequest $request, User $user)
  {
    if(!Auth::user()->can('user.add') && !Auth::user()->can('user.edit')){
      return back()->with([Message::ERROR_KEY=>trans('message.no_permission'), 'alert-type' => 'warning'], 403);
    }

    if($request->form_type == FormType::EDIT_TYPE) {
      $this->validate($request, [
        'username' => ['required', 'min:6', 'max:50'],
      ]);
    }
    else {
      $this->validate($request, [
        'username' => ['required', 'min:6', 'max:50', Rule::unique('users')->ignore(request('user'))],
      ]);
    }

    $user->name     = strtolower($request->username);
    $user->username = strtolower($request->username);
    $user->role     = $request->role;
    $user->active   = $request->status;

    if ($request->password != null) {
      $user->password = bcrypt($request->password);
    }

    $user->save();

    //Attach the selected Roles
    //-------------------------
    if($request->has('role')) {
      $user->roles()->sync([$request->input('role')]);
    }

    if($request->has('staff') && !empty($request->staff)) {
      $staff = Staff::find($request->staff);
      $staff->user_id = $user->id;
      $staff->save();
    }

    session()->flash(Message::SUCCESS_KEY, trans('message.item_saved_success'));
    return redirect(route('user.index'));
  }

  /**
   * Delete user.
   *
   * @param User $user
   *
   * @return Response
   */
  public function destroy($id)
  {
    if(!Auth::user()->can('user.delete')) {
      return back()->with([Message::ERROR_KEY=>trans('message.no_permission'), 'alert-type' => 'warning'], 403);
    }
    if($id != Auth::id()) {
      $user = User::where('id',$id)->first();
      if($user->delete()) {
        session()->flash(Message::SUCCESS_KEY, trans('message.item_saved_success'));
        // return redirect(route('user.index'));
      }
    } 
    else {
      return back()->with([Message::ERROR_KEY=>trans("You can't delete your self"),"alert-type"=>'warning'],403);
    }
  }

  /**
   * Show form to update user profile.
   *
   * @param User $user
   *
   * @return Response
   */
  public function showProfile(User $user)
  {
    $formType = FormType::EDIT_TYPE;
    return view('user/profile', compact('user', 'formType'));
  }

  /**
   * Save user profile info.
   *
   * @param Request $request
   * @param User $user
   *
   * @return Response
   */
  public function saveProfile(Request $request, User $user)
  {
    if(!Auth::user()->can('user.edit')) {
      return back()->with([
        Message::ERROR_KEY => trans('message.no_permission'), 
        'alert-type' => 'warning'
      ], 403);
    }

    if($request->form_type == FormType::CREATE_TYPE) {
      $validate = [
        'username'            => ['required', 'string', 'min:6', Rule::unique('users')->ignore($user->id)],
        'current_password'    => ['nullable', Rule::requiredIf(!empty($request->new_password))],
        'new_password'        => ['nullable', Rule::requiredIf(!empty($request->current_password)), 'min:6', 'max:32'],
        'confirmed_password'  => ['same:new_password'],
      ];
    } 
    else {
      $validate = [
        'username'            => ['required', 'string', 'min:6'],
        'current_password'    => ['nullable', Rule::requiredIf(!empty($request->new_password))],
        'new_password'        => ['nullable', Rule::requiredIf(!empty($request->current_password)), 'min:6', 'max:32'],
        'confirmed_password'  => ['same:new_password'],
      ];
    }
    $this->validate($request, $validate);

    $user->username = strtolower($request->username);
    if (!empty($request->current_password)) {
      if (Hash::check($request->current_password, auth()->user()->password)) {
        $user->password = bcrypt($request->new_password);
      } 
      else {
        session()->flash(Message::ERROR_KEY, trans('message.incorrect_current_password'));
        return back()->withInput($request->all());
      }
    }

    $user->save();
    session()->flash(Message::SUCCESS_KEY, trans('message.item_saved_success'));
    return back();
  }
}