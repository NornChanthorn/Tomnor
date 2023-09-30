<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\ExtendedProperty;
use App\Constants\ExtendedProperty as EPropertyType;
use App\Models\ExtendedProperty as Position;
use App\Constants\FormType;
use App\Constants\Message;
use Illuminate\Http\Request;
use App\Http\Requests\ExpenseRequest;
use App\Traits\FileHandling;
use Auth;
class ExpenseController extends Controller
{
    use FileHandling;

    /** @var string  Folder name to store image */
    private $imageFolder = 'expense';
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if(!Auth::user()->can('expense.browse')){
            return back()->with([Message::ERROR_KEY=>trans('message.no_permission'), 'alert-type' => 'warning'], 403);
        }
        $expenses = Expense::query();
        if (!empty($request->search)) {
            $expenses = $expenses->where(function ($query) use ($request) {
              $searchText = $request->search;
              $query->where('note', 'like', '%'. $searchText .'%');
            });
        }
        $itemCount = $expenses->count();
        $expenses = $expenses->sortable()->paginate(paginationCount());
        $offset = offset($request->page);
        return view('expense.index',compact('expenses', 'itemCount', 'offset'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Expense $expense)
    {
        if(!Auth::user()->can('expense.add')){
            return back()->with([Message::ERROR_KEY=>trans('message.no_permission'), 'alert-type' => 'warning'], 403);
        }
        $title = trans('app.create');
        $formType = FormType::CREATE_TYPE;
        $categories = ExtendedProperty::where('property_name',EPropertyType::EXPENSE)->where('group_name',EPropertyType::EXPENSE)->get();
        return view('expense/form', compact('formType', 'title', 'expense', 'categories'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Expense  $expense
     * @return \Illuminate\Http\Response
     */
    public function show(Expense $expense)
    {
        if(!Auth::user()->can('expense.browse')){
            return back()->with([Message::ERROR_KEY=>trans('message.no_permission'), 'alert-type' => 'warning'], 403);
        }
        $title = trans('app.detail');
        $formType = FormType::SHOW_TYPE;
        $categories = ExtendedProperty::where('property_name',EPropertyType::EXPENSE)->where('group_name',EPropertyType::EXPENSE)->get();
        return view('expense/form', compact('formType', 'title', 'expense', 'categories'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Expense  $expense
     * @return \Illuminate\Http\Response
     */
    public function edit(Expense $expense)
    {
        if(!Auth::user()->can('expense.edit')){
            return back()->with([Message::ERROR_KEY=>trans('message.no_permission'), 'alert-type' => 'warning'], 403);
        }
        $title = trans('app.edit');
        $formType = FormType::EDIT_TYPE;
        $categories = ExtendedProperty::where('property_name',EPropertyType::EXPENSE)->where('group_name',EPropertyType::EXPENSE)->get();
        return view('expense/form', compact('formType', 'title', 'expense', 'categories'));
    }
    public function save(Request $request,Expense $expense)
    {
        if(!Auth::user()->can('expense.add') && !Auth::user()->can('expense.edit')){
            return back()->with([Message::ERROR_KEY=>trans('message.no_permission'), 'alert-type' => 'warning'], 403);
        }
        $expense->refno = $request->refno;
        $expense->category_id = $request->category_id;
        $expense->amount = $request->amount;
        $expense->expense_date = dateIsoFormat($request->expense_date);
        $expense->note = $request->note;

        if (!empty($request->ref_doc)) {
            $expense->ref_doc = $this->uploadImage($this->imageFolder, $request->ref_doc);
        }
        if ($expense->save()) {
            session()->flash(Message::SUCCESS_KEY, trans('message.item_saved_success'));
          }
          else {
            session()->flash(Message::ERROR_KEY, trans('message.item_saved_fail'));
          }

          return redirect(route('expense.index'));
    }

    public function type(Request $request)
    {
        if(!Auth::user()->can('expense_type.browse')){
            return back()->with([Message::ERROR_KEY=>trans('message.no_permission'), 'alert-type' => 'warning'], 403);
        }
        $positions = Position::expense();
        if (!empty($request->search)) {
            $positions = $positions->where('value', 'like', '%' . $request->search . '%');
        }

        $itemCount = $positions->count();
        $positions = $positions->sortable()->orderBy('value')->paginate(paginationCount());
        $offset = offset($request->page);

        return view('expense_type/index', compact('itemCount', 'offset', 'positions'));
    }
    public function typeCreate()
    {
        if(!Auth::user()->can('expense_type.add')){
            return back()->with([Message::ERROR_KEY=>trans('message.no_permission'), 'alert-type' => 'warning'], 403);
        }
        $title = trans('app.create');
        return view('expense_type/form', compact('title'));
    }
    public function typeEdit($id)
    {
        if(!Auth::user()->can('expense_type.edit')){
            return back()->with([Message::ERROR_KEY=>trans('message.no_permission'), 'alert-type' => 'warning'], 403);
        }
        $position= Position::find($id);
        $title = trans('app.edit');
        return view('expense_type/form', compact('title','position'));
    }
    public function typeSave(Request $request)
    {
        if(!Auth::user()->can('expense_type.add') && !Auth::user()->can('expense_type.edit')){
            return back()->with([Message::ERROR_KEY=>trans('message.no_permission'), 'alert-type' => 'warning'], 403);
        }
        $this->validate($request, [
            'title' => 'required|max:255',
        ]);

        $position = Position::find($request->id) ?? new Position();
        $position->property_name = EPropertyType::EXPENSE;
        $position->group_name = EPropertyType::EXPENSE;
        $position->value = $request->title;

        if ($position->save()) {
            session()->flash(Message::SUCCESS_KEY, trans('message.item_saved_success'));
        } else {
            session()->flash(Message::ERROR_KEY, trans('message.item_saved_fail'));
        }

        return redirect()->route('expense-type.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Expense  $expense
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if(!Auth::user()->can('expense.delete')){
            return back()->with([Message::ERROR_KEY=>trans('message.no_permission'), 'alert-type' => 'warning'], 403);
        }
        $expense = Expense::find($id);
        if($expense->delete()) {
            session()->flash(Message::SUCCESS_KEY, trans('message.item_saved_success'));
            // return redirect(route('user.index'));
        }
    }
}
