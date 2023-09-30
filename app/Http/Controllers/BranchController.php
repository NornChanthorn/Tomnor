<?php

namespace App\Http\Controllers;

use App\Constants\UserRole;
use Illuminate\Http\Request;
use App\Constants\BranchType;
use App\Constants\Message;
use App\Constants\FormType;
use App\Models\Branch;
use App\Models\ProductWarehouse;
use App\Http\Requests\BranchRequest;
use App\Traits\FileHandling;

use Auth;

class BranchController extends Controller
{
    use FileHandling;

    /** @var string Folder name to store branch logo */
    private $logoFolder = 'branch-logo';

    public function __construct()
    {
        //$this->middleware('role:'. UserRole::ADMIN);
    }

    /**
     * Display a listing of branches.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function index(Request $request)
    {
        if(!Auth::user()->can('branch.browse')){
            return back()->with([Message::ERROR_KEY=>trans('message.no_permission'), 'alert-type' => 'warning'], 403);
        }

        $branches = Branch::query();
        if (!empty($request->search)) {
          $searchText = $request->search;
          $branches = $branches
            ->where('name', 'like', '%' . $searchText . '%')
            ->orWhere('location', 'like', '%' . $searchText . '%')
            ->orWhere('phone_1', 'like', '%' . $searchText . '%')
            ->orWhere('phone_2', 'like', '%' . $searchText . '%')
            ->orWhere('phone_3', 'like', '%' . $searchText . '%')
            ->orWhere('phone_4', 'like', '%' . $searchText . '%')
            ->orWhere('address', 'like', '%' . $searchText . '%');
        }

        $itemCount = $branches->count();
        $branches = $branches->sortable()->orderBy('name')->paginate(paginationCount());
        $offset = offset($request->page);

        return view('branch/index', compact('branches', 'itemCount', 'offset'));
    }

    /**
     * Show form to create branch.
     *
     * @param Branch $branch
     *
     * @return Response
     */
    public function create(Branch $branch)
    {
        if(!Auth::user()->can('branch.add')){
            return back()->with([Message::ERROR_KEY=>trans('message.no_permission'), 'alert-type' => 'warning'], 403);
        }

        $title = trans('app.create');
        $formType = FormType::CREATE_TYPE;
        $branchTypes = branchTypes();

        return view('branch/form', compact('branch', 'branchTypes', 'formType', 'title'));
    }

    /**
     * Show form to edit branch
     *
     * @param Branch  $branch
     *
     * @return Response
     */
    public function edit(Branch $branch)
    {
        if(!Auth::user()->can('branch.edit')){
            return back()->with([Message::ERROR_KEY=>trans('message.no_permission'), 'alert-type' => 'warning'], 403);
        }

        $title = trans('app.edit');
        $formType = FormType::EDIT_TYPE;
        $branchTypes = branchTypes();

        return view('branch/form', compact('branch', 'branchTypes',  'formType', 'title'));
    }

    /**
     * Display branch detail.
     *
     * @param Branch  $branch
     *
     * @return Response
     */
    public function show(Branch $branch)
    {
        if(!Auth::user()->can('branch.browse')){
            return back()->with([Message::ERROR_KEY=>trans('message.no_permission'), 'alert-type' => 'warning'], 403);
        }

        $title = trans('app.detail');
        $formType = FormType::SHOW_TYPE;

        return view('branch/form', compact('branch', 'formType', 'title'));
    }

    /**
     * Save new or existing branch.
     *
     * @param Request $request
     * @param FormType $formType
     *
     * @return Response
     */
    public function save(BranchRequest $request, Branch $branch)
    {
      if(!Auth::user()->can('branch.add') && !Auth::user()->can('branch.edit')){
        return back()->with([Message::ERROR_KEY=>trans('message.no_permission'), 'alert-type' => 'warning'], 403);
      }

      $branch->code = $request->code;
      $branch->name = $request->name;
      $branch->name_en = $request->name_en;
      $branch->type = $request->type;
      $branch->location = $request->location;
      $branch->location_en = $request->location_en;
      $branch->email_1 = $request->first_email;
      $branch->email_2 = $request->second_email;
      $branch->phone_1 = $request->first_phone;
      $branch->phone_2 = $request->second_phone;
      $branch->phone_3 = $request->third_phone;
      $branch->phone_4 = $request->fourth_phone;
      $branch->address = $request->address;
      $branch->contract_text = $request->contract_text;
      $branch->invoice_footer_text = $request->invoice_footer_text;
      $branch->others_charges = $request->others_charges;

      if (isset($request->first_logo)) {
        $branch->logo = $this->uploadImage($this->logoFolder, $request->first_logo);
      }

      if (isset($request->second_logo)) {
        $branch->logo_2 = $this->uploadImage($this->logoFolder, $request->second_logo);
      }
      if (isset($request->image_invoice)) {
        $branch->image_invoice = $this->uploadImage($this->logoFolder, $request->image_invoice);
      }

      $branch->save();
      session()->flash(Message::SUCCESS_KEY, trans('message.item_saved_success'));
      return redirect(route('branch.index'));
    }

    /**
     * Show a listing of products of a warehouse.
     *
     * @param Branch $branch
     *
     * @return Response
     */
    public function productList(Branch $branch)
    {
        $stocks = ProductWarehouse::where('warehouse_id', $branch->id)->get();
        $itemCount = $stocks->count();
        return view('branch.product', compact('branch', 'itemCount', 'stocks'));
    }

    /**
     * Delete branch.
     *
     * @param Branch  $branch
     *
     * @return Response
     */
    public function destroy(Branch $branch)
    {
        if(!Auth::user()->can('branch.delete')){
            return back()->with([Message::ERROR_KEY=>trans('message.no_permission'), 'alert-type' => 'warning'], 403);
        }
    }
}
