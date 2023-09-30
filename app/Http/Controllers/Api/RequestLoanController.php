<?php

namespace App\Http\Controllers\Api;

use \Carbon\Carbon;
use App\Models\Loan;
use App\Models\Client;
use Illuminate\Http\Request;
use App\Traits\FileHandling;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
class RequestLoanController extends Controller
{
    use FileHandling;

    
    /** @var string Folder name to store image */
    private $imageFolder = 'client';
    
    /** @var string Folder name to store general documents */
    private $fileFolder = 'documents';
    //
    public function requestLoan(Request $request){
        
        $client = new Client();
        // Personal info
        $client->name  = $request->name;
        $client->first_phone  = $request->first_phone;
        $client->province_id = $request->province_id;
        $client->occupation_1 = $request->occupation_1;
        $client->save();
        $loan = new Loan();
        $loan->type = 'cash';
        $loan->account_number = nextLoanAccNum();
        $loan->loan_amount = $request->loan_amount;
        $loan->client_id = $client->id;
        $loan->frequency = $request->frequency;
        $loan->loan_start_date = Carbon::now()->toDateTimeString();
        $loan->status='p';
        if ($loan->save()) {
            $output = [
              'success' => true,
              'message' => trans('message.item_saved_success'),
            ];
        }else {
            $output = [
              'success' => false,
              'message' => trans('message.item_saved_fail')
            ];
        }
        return response()->json($output);
    }
    public function checkClent(Request $request)
    {
        // $name=$request->name;
        $phone=$request->first_phone;
        $valid;

        if(!empty($phone)) {
            $query = Client::where('first_phone',$phone);    
            if ($query->first()!=null) {
                $valid = 'false';
            }else{
                $valid = 'true';
            }
           echo $valid;
        }else {
            echo 'true';
        }
        
       
        exit;
    }
}
