<?php

namespace App\Http\Controllers;

use App\Models\Ime;
use App\Models\TransactionIme;
use App\Models\Product;
use App\Models\Variantion;
use Illuminate\Http\Request;
use App\Constants\Message;

class ImeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getSuggestIme(Request $request){
        $searchString = $request->get('query');
        $imes = Ime::query();
        if(!empty($searchString)){
            $imes->where(function ($query) use ($searchString){
                $query->where('code', 'like', '%'.$searchString.'%');
            });
        }
        $imes = $imes->where('status','available')->get();
        return response()->json($imes);
    }
    public function getIme(Request $request)
    {
        $imes = Ime::query();
        if($request->search){
            $searchString = $request->search;
            $imes->where(function ($query) use ($searchString){
                $query->where('code', 'like', '%'.$searchString.'%');
            });
        }
        // dd($imes);
        $itemCount = $imes->count();
        $imes = $imes->latest('created_at')->paginate(paginationCount());
        $offset = offset($request->page);

        return view('ime.index',compact('imes', 'itemCount', 'offset'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function ime_single()
    {
        $title=trans('app.create');
        return view('ime.form-single',compact('title'));
    }

    public function create(Request $request)
    {

        $title=trans('app.create');
        $transaction_id=$request->transaction_id;
        $product_id=$request->product_id;
        $location_id=$request->location_id;
        $variantion_id=$request->variantion_id;
        $purchase_sell_id= $request->purchase_sell_id;
        $product=Product::find($product_id);
        $variantion=Variantion::find($variantion_id);
        $transaction_imes = TransactionIme::where('transaction_id',$transaction_id)->where('purchase_sell_id',$purchase_sell_id)->get();
        $offset = offset($request->page);
        $qty=$request->qty;
        $type=$request->type;
        
        return view('ime.form',compact('purchase_sell_id','type','title','product','variantion','transaction_id','qty','location_id','transaction_imes','offset'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function save(Request $request)
    {
        $ime = Ime::where('code',$request->code);

        if($request->type!="purchase"){
            $ime->where('status','available');
        }
        if($request->type=="purchase"){
           $ime->where('status','sold');
        }
        $ime = $ime->first();
        if(!empty($ime)){
            $ime_id = $ime->id;
        }else{
            if($request->type=="purchase"){
                $this->validate($request,array(
                    'code'  => 'required|unique:imes',
                ));
                $ime = new Ime();
                $ime->code = $request->code;
                $ime->product_id = $request->product_id;
                $ime->variantion_id = $request->variantion_id;
                if($ime->save()){
                    $ime_id = $ime->id;
                }
            }else{
                session()->flash(Message::ERROR_KEY, trans('message.item_saved_fail'));
                return redirect()->back();
            }
        }

        if($ime_id){
            if($request->type=="loan"){
                if($ime->product_id == $request->product_id && $ime->variantion_id==$request->variantion_id){
                    $transaction_ime = new TransactionIme();
                    $transaction_ime->ime_id = $ime_id;
                    $transaction_ime->purchase_sell_id = $request->purchase_sell_id;
                    $transaction_ime->transaction_id = $request->transaction_id;
                    $transaction_ime->location_id = $request->location_id;
                    if($transaction_ime->save()){
                        $ime->status = "sold";
                        if($ime->save()){
                            session()->flash(Message::SUCCESS_KEY, trans('message.item_saved_success'));
                            return redirect()->back();
                        }
                    }
                }
                if($ime->product_id == null &&  $ime->variantion_id==null){
                    $transaction_ime = new TransactionIme();
                    $transaction_ime->ime_id = $ime_id;
                    $transaction_ime->purchase_sell_id = $request->purchase_sell_id;
                    $transaction_ime->transaction_id = $request->transaction_id;
                    $transaction_ime->location_id = $request->location_id;
                    if($transaction_ime->save()){
                        $ime->status = "sold";
                        $ime->product_id = $request->product_id;
                        $ime->variantion_id = $request->variantion_id;
                        if($ime->save()){
                            session()->flash(Message::SUCCESS_KEY, trans('message.item_saved_success'));
                            return redirect()->back();
                        }
                    } 
                }
                session()->flash(Message::ERROR_KEY, trans('message.item_saved_fail'));
                return redirect()->back();
                
            }
            if($request->type=="sale"){
                if($ime->product_id == $request->product_id && $ime->variantion_id==$request->variantion_id){
                    $transaction_ime = new TransactionIme();
                    $transaction_ime->ime_id = $ime_id;
                    $transaction_ime->purchase_sell_id = $request->purchase_sell_id;
                    $transaction_ime->transaction_id = $request->transaction_id;
                    $transaction_ime->location_id = $request->location_id;
                    if($transaction_ime->save()){
                        $ime->status = "sold";
                        if($ime->save()){
                            session()->flash(Message::SUCCESS_KEY, trans('message.item_saved_success'));
                            return redirect()->back();
                        }
                    }
                }
                if($ime->product_id == null &&  $ime->variantion_id==null){
                    $transaction_ime = new TransactionIme();
                    $transaction_ime->ime_id = $ime_id;
                    $transaction_ime->purchase_sell_id = $request->purchase_sell_id;
                    $transaction_ime->transaction_id = $request->transaction_id;
                    $transaction_ime->location_id = $request->location_id;
                    if($transaction_ime->save()){
                        $ime->status = "sold";
                        $ime->product_id = $request->product_id;
                        $ime->variantion_id = $request->variantion_id;
                        if($ime->save()){
                            session()->flash(Message::SUCCESS_KEY, trans('message.item_saved_success'));
                            return redirect()->back();
                        }
                    } 
                }
                session()->flash(Message::ERROR_KEY, trans('message.item_saved_fail'));
                return redirect()->back();
                
            }
            if($request->type=="transfer"){
                if($ime->product_id == $request->product_id && $ime->variantion_id==$request->variantion_id){
                    $transaction_ime = new TransactionIme();
                    $transaction_ime->ime_id = $ime_id;
                    $transaction_ime->purchase_sell_id = $request->purchase_sell_id;
                    $transaction_ime->transaction_id = $request->transaction_id;
                    $transaction_ime->location_id = $request->location_id;
                    if($transaction_ime->save()){
                        $ime->status = "available";
                        if($ime->save()){
                            session()->flash(Message::SUCCESS_KEY, trans('message.item_saved_success'));
                            return redirect()->back();
                        }
                    }
                }
                if($ime->product_id == null &&  $ime->variantion_id==null){
                    $transaction_ime = new TransactionIme();
                    $transaction_ime->ime_id = $ime_id;
                    $transaction_ime->purchase_sell_id = $request->purchase_sell_id;
                    $transaction_ime->transaction_id = $request->transaction_id;
                    $transaction_ime->location_id = $request->location_id;
                    if($transaction_ime->save()){
                        $ime->status = "available";
                        $ime->product_id = $request->product_id;
                        $ime->variantion_id = $request->variantion_id;
                        if($ime->save()){
                            session()->flash(Message::SUCCESS_KEY, trans('message.item_saved_success'));
                            return redirect()->back();
                        }
                    } 
                }
                session()->flash(Message::ERROR_KEY, trans('message.item_saved_fail'));
                return redirect()->back();
                
            }
            if($request->type=="purchase"){
                $transaction_ime = new TransactionIme();
                $transaction_ime->ime_id = $ime_id;
                $transaction_ime->purchase_sell_id = $request->purchase_sell_id;
                $transaction_ime->transaction_id = $request->transaction_id;
                $transaction_ime->location_id = $request->location_id;
                if($transaction_ime->save()){
                    $ime->status = "available";
                    if($ime->save()){
                        session()->flash(Message::SUCCESS_KEY, trans('message.item_saved_success'));
                        return redirect()->back();
                    }
                }
            }   
            session()->flash(Message::SUCCESS_KEY, trans('message.item_saved_success'));
            return redirect()->back();   
        }

    }
    public function save_ime(Request $request)
    {
        $ime = Ime::where('code',$request->code)->first() ?? new Ime();
        if(!empty($ime->id) && $ime->product_id==null && $ime->variantion_id==null){
            $ime->code = $request->code;
            $ime->product_id = $request->product_id;
            $ime->variantion_id = $request->variantion_id;
        }else{
            $this->validate($request,array(
                'code'  => 'required|unique:imes',
            ));
            // $ime = new Ime();
            $ime->code = $request->code;
            $ime->product_id = $request->product_id;
            $ime->variantion_id = $request->variantion_id;
        }
       
        if($ime->save()){
            session()->flash(Message::SUCCESS_KEY, trans('message.item_saved_success'));
            return redirect()->back();
        }
    }
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id, Request $request)
    {
        //
        $ime=Ime::find($id);
        $title=trans('app.detail');
        $offset = offset($request->page);
        return view('ime.show',compact('title','ime','offset'));
    }
    public function show_ime(Request $request)
    {
        //
        
        $ime=Ime::where('product_id',$request->product_id)->where('variantion_id',$request->variantion_id)->where('status','available')->get();
        $product=Product::find($request->product_id);
        $variantion = Variantion::find($request->variantion_id);

        $title = $product->name.($variantion->name!='DUMMY' ? ' - '.$variantion->name : '');
        $offset = offset($request->page);
        return view('product.show-ime',compact('title','product','variantion','ime','offset'));
    }
    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $ime = Ime::find($id);
        if(!empty($ime->transaction_ime)){
            $transaction_ime=TransactionIme::where('ime_id',$id);
            $transaction_ime->delete();
        }
        $ime->delete();
        session()->flash(Message::SUCCESS_KEY, trans('message.item_deleted_success'));
        return redirect()->back();
        //
    }
}
