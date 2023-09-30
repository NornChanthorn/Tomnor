<?php

namespace App\Http\Controllers;

use App\Constants\UserRole;
use GuzzleHttp\Psr7\Response;
use Illuminate\Http\Request;
use App\Constants\FormType;
use App\Constants\Message;
use App\Http\Requests\StaffRequest;
use App\Models\AgentCommission;
use App\Models\Branch;
use App\Models\Staff;
use App\Models\User;
use App\Models\Role;
use App\Traits\FileHandling;
use Illuminate\Validation\Rule;

use Auth;

class StaffController extends Controller
{
  use FileHandling;
  
  /** @var string  Folder name to store image */
  private $imageFolder = 'staff';

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
    if(!Auth::user()->can('staff.browse')){
      return back()->with([Message::ERROR_KEY=>trans('message.no_permission'), 'alert-type' => 'warning'], 403);
    }

    $staff = Staff::query();
    if (!empty($request->branch)) {
      $staff = $staff->where('branch_id', $request->branch);
    }

    if (!empty($request->position)) {
      $staff = $staff->where('position', $request->position);
    }

    if (!empty($request->search)) {
      $staff = $staff->where(function ($query) use ($request) {
        $searchText = $request->search;
        $query->where('name', 'like', '%'. $searchText .'%')
          ->orWhere('id_card_number', 'like', '%'. $searchText .'%')
          ->orWhere('first_phone', 'like', '%'. $searchText .'%')
          ->orWhere('second_phone', 'like', '%'. $searchText .'%')
          ->orWhere('address', 'like', '%'. $searchText .'%');
      });
    }

    $itemCount = $staff->count();
    $staff = $staff->sortable()->orderBy('name')->paginate(paginationCount());
    $offset = offset($request->page);
    $branches = Branch::getAll();
    
    return view('staff/index', compact('branches', 'itemCount', 'offset', 'staff'));
  }

  /**
   * Show the form for creating a new resource.
   *
   * @param Staff $staff
   *
   * @return Response
   */
  public function create(Staff $staff)
  {
    if(!Auth::user()->can('staff.add')){
      return back()->with([Message::ERROR_KEY=>trans('message.no_permission'), 'alert-type' => 'warning'], 403);
    }

    $title = trans('app.create');
    $formType = FormType::CREATE_TYPE;
    $branches = Branch::getAll();
    $roles = Role::getAll();
    // $roles = array_pluck(Role::all(),'display_name','id');
    
    return view('staff/form', compact('branches', 'formType', 'staff', 'title', 'roles'));
  }

  /**
   * Show the form for editing the specified resource.
   *
   * @param Staff $staff
   *
   * @return Response
   */
  public function edit(Staff $staff)
  {
    if(!Auth::user()->can('staff.edit')){
      return back()->with([Message::ERROR_KEY=>trans('message.no_permission'), 'alert-type' => 'warning'], 403);
    }

    $title = trans('app.edit');
    $formType = FormType::EDIT_TYPE;
    $branches = Branch::getAll();
    $roles = Role::getAll();

    return view('staff/form', compact('branches', 'formType', 'staff', 'title', 'roles'));
  }

  /**
   * Display the specified resource.
   *
   * @param Staff $staff
   *
   * @return \Illuminate\Http\Response
   */
  public function show(Staff $staff)
  {
    if(!Auth::user()->can('staff.browse')){
      return back()->with([Message::ERROR_KEY=>trans('message.no_permission'), 'alert-type' => 'warning'], 403);
    }

    $title = trans('app.detail');
    $formType = FormType::SHOW_TYPE;

    return view('staff/form', compact('title', 'formType', 'staff'));
  }

  /**
   * Save new or existing staff.
   *
   * @param Request $request
   * @param Staff $staff
   *
   * @return Response
   */
  public function save(StaffRequest $request, Staff $staff)
  {
    if(!Auth::user()->can('staff.add') && !Auth::user()->can('staff.edit')){
      return back()->with([Message::ERROR_KEY=>trans('message.no_permission'), 'alert-type' => 'warning'], 403);
    }

    if($request->form_type == FormType::CREATE_TYPE) {
      $this->validate($request, [
        'username' => [Rule::unique('users')],
        'password' => ['nullable', Rule::requiredIf(request('form_type') == FormType::CREATE_TYPE), 'min:6', 'max:32'],
      ]);
    }

    $staff->name            = $request->name;
    $staff->gender          = $request->gender;
    $staff->date_of_birth   = dateIsoFormat($request->date_of_birth);
    $staff->id_card_number  = str_replace(' ', '', $request->id_card_number);
    $staff->first_phone     = $request->first_phone;
    $staff->second_phone    = $request->second_phone;
    $staff->position        = $request->position;
    $staff->address         = $request->address;
    $staff->branch_id       = $request->branch;

    // if ($request->form_type == FormType::CREATE_TYPE) {
    //   $staff->branch_id = $request->branch;
    // }

    if (!empty($request->profile_photo)) {
      $staff->profile_photo = $this->uploadImage($this->imageFolder, $request->profile_photo);
    }

    if (!empty($request->id_card_photo)) {
      $staff->id_card_photo = $this->uploadImage($this->imageFolder, $request->id_card_photo);
    }
      // Save login info
      $user = $staff->user ?? new User();
      $user->name = $request->name;
      $user->username = strtolower($request->username);

      if (isset($request->password)) {
        $user->password = bcrypt($request->password);
      } 
      elseif ($user->password === null) { 
        // For existing staff that have no data in users table
        $user->password = bcrypt('123456');
      }

      $user->save();
      //Attach the selected Roles
      //-------------------------
      if($request->has('role')) {
        $user->roles()->sync([$request->input('role')]);
      }
      $staff->user_id = $user->id;

    if ($staff->save()) {
      session()->flash(Message::SUCCESS_KEY, trans('message.item_saved_success'));
    } 
    else {
      session()->flash(Message::ERROR_KEY, trans('message.item_saved_fail'));
    }

    return redirect()->route('staff.index');
  }

  /**
   * Show a listing of commission history and form to set new commission.
   *
   * @param Request $request
   * @param Staff $staff
   *
   * @return Response
   */
  public function commission(Request $request, Staff $staff)
  {
    if(!Auth::user()->can('staff.commission')){
      return back()->with([Message::ERROR_KEY=>trans('message.no_permission'), 'alert-type' => 'warning'], 403);
    }

    $commissions = AgentCommission::where('staff_id', $staff->id)->orderBy('start_date', 'desc')->orderBy('id', 'desc')->paginate(paginationCount());
    $offset = offset($request->page);

    return view('staff/commission', compact('commissions', 'offset', 'staff'));
  }

  /**
   * Save agent commission.
   *
   * @param Request $request
   * @param Staff $staff
   *
   * @return Response
   */
  public function saveCommission(Request $request, Staff $staff)
  {
    if(!Auth::user()->can('staff.commission')){
      return back()->with([Message::ERROR_KEY=>trans('message.no_permission'), 'alert-type' => 'warning'], 403);
    }

    $this->validate($request, [
      'start_date' => 'required|date',
      'amount' => 'required|numeric|gt:0',
    ]);

    // $latestCommission = AgentCommission::where('staff_id', $staff->id)->orderBy('start_date', 'desc')->first();
    // dd($latestCommission);

    $commission = new AgentCommission();
    $commission->staff_id = $staff->id;
    $commission->start_date = dateIsoFormat($request->start_date);
    $commission->amount = decimalNumber($request->amount);
    $commission->save();

    session()->flash(Message::SUCCESS_KEY, trans('message.item_saved_success'));
    return back();
  }

  /**
   * Get agents by branch from Ajax request.
   *
   * @param Request $request
   * @param int $branchId
   *
   * @return JsonResponse
   */
  public function getAgents(Request $request, $branchId)
  {
    if (!$request->ajax()) {
      return back();
    }

    $agents = Staff::where('branch_id', $branchId)->whereNotNull('user_id')->orderBy('name')->get();
    return response()->json($agents);
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  \App\Models\Staff  $staff
   * @return \Illuminate\Http\Response
   */
  public function destroy(Staff $staff)
  {
    if(!Auth::user()->can('staff.delete')){
      return back()->with([Message::ERROR_KEY=>trans('message.no_permission'), 'alert-type' => 'warning'], 403);
    }

    if (!empty($staff->user_id || NULL != $staff->user_id)) {
      $user = User::where('id',$staff->user_id)->first();
      if(!empty($user)):
        if ($user->roles() != NULL) {
          $user->roles()->detach();
        }
        if ($user->delete()) {
          if($staff->delete()):
            session()->flash(Message::SUCCESS_KEY, trans('message.item_saved_success'));
            return 'Yes';
          endif;
        }
      endif;
    }
    else {
      if($staff->delete()):
        session()->flash(Message::SUCCESS_KEY, trans('message.item_saved_success'));
        return 'Yes';
      endif;
    }
  }
}
