<?php

namespace App\Http\Controllers;

use App\Models\VariantionLocationDetails;
use Illuminate\Http\Request;
use App\Traits\ProductUtil;
use App\Traits\TransactionUtil;
use Config;
class UpdatedQTYLocationController extends Controller
{
    use ProductUtil, TransactionUtil;
    //
    public function updatedqty(Request $request){
        if (Config::get('app.WRONG_STOCK')==true){
            $Vld = VariantionLocationDetails::where('product_id',$request->id)->where('location_id',$request->location_id)->where('variantion_id',$request->variantion_id)->first();
            $Vld->qty_available = $request->qty_available;
        //    dd( $request->qty_available);
            if( $Vld->save()){
                return redirect()->back();
            } 
        }
        return redirect()->back();
        
    }
}
