<?php

namespace App\Http\Controllers;

use App\Constants\UserRole;
use Illuminate\Http\Request;
use App\Constants\ExtendedProperty as EPropertyType;
use App\Constants\Message;
use App\Models\ExtendedProperty as ProductCategory;
use Auth;

class ProductCategoryController extends Controller
{
    public function __construct()
    {
        //$this->middleware('role:'. UserRole::ADMIN);
    }

    /**
     * Display a listing of product categories.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function index(Request $request)
    {
        if(!Auth::user()->can('product-type.browse')){
            return back()->with([Message::ERROR_KEY=>trans('message.no_permission'), 'alert-type' => 'warning'], 403);
        }

        $productCategories = ProductCategory::productCategory();
        if (!empty($request->search)) {
            $productCategories = $productCategories->where('value', 'like', '%' . $request->search . '%');
        }

        $itemCount = $productCategories->count();
        $productCategories = $productCategories->sortable()->orderBy('value')->paginate(paginationCount());
        $offset = offset($request->page);

        return view('product-category/index', compact('itemCount', 'offset', 'productCategories'));
    }

    /**
     * Show form to create product category.
     *
     * @return Response
     */
    public function create(ProductCategory $productCategory)
    {
        if(!Auth::user()->can('product-type.add')){
            return back()->with([Message::ERROR_KEY=>trans('message.no_permission'), 'alert-type' => 'warning'], 403);
        }

        $title = trans('app.create');

        return view('product-category/form', compact('productCategory', 'title'));
    }

    /**
     * Show form to edit product category.
     *
     * @param ProductCategory $productCategory
     *
     * @return Response
     */
    public function edit(ProductCategory $productCategory)
    {
        if(!Auth::user()->can('product-type.edit')){
            return back()->with([Message::ERROR_KEY=>trans('message.no_permission'), 'alert-type' => 'warning'], 403);
        }

        $title = trans('app.edit');

        return view('product-category/form', compact('productCategory', 'title'));
    }

    /**
     * Save new or existing product category.
     *
     * @param Request $request
     * @param ProductCategory $productCategory
     *
     * @return Response
     */
    public function save(Request $request, ProductCategory $productCategory)
    {
        if(!Auth::user()->can('product-type.add') && !Auth::user()->can('product-type.edit')){
            return back()->with([Message::ERROR_KEY=>trans('message.no_permission'), 'alert-type' => 'warning'], 403);
        }

        $this->validate($request, [
            'name' => 'required|max:255',
        ]);

        $productCategory->property_name = EPropertyType::PRODUCT_CATEGORY;
        $productCategory->value = $request->name;
        $productCategory->group_name = EPropertyType::PRODUCT_CATEGORY;
        $productCategory->save();

        session()->flash(Message::SUCCESS_KEY, trans('message.item_saved_success'));
        return redirect()->route('product_category.index');
    }

    /**
     * Delete product category.
     *
     * @param ProductCategory $productCategory
     *
     * @return Response
     */
    public function destroy(ProductCategory $productCategory)
    {
        if(!Auth::user()->can('product-type.delete')){
            return back()->with([Message::ERROR_KEY=>trans('message.no_permission'), 'alert-type' => 'warning'], 403);
        }

        abort(404);
    }
}
