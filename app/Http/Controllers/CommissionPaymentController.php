<?php

namespace App\Http\Controllers;

use App\Constants\UserRole;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Constants\Message;
use App\Models\CommissionPayment;
use App\Models\Staff;
use App\Traits\AgentUtil;

use Auth;

class CommissionPaymentController extends Controller
{
    use AgentUtil;

    public function __construct()
    {
        //$this->middleware('role:'. UserRole::ADMIN);
    }

    /**
     * Display commission payment form.
     *
     * @return Response
     */
    public function index()
    {
        if(!Auth::user()->can('staff.commission')){
            return back()->with([Message::ERROR_KEY=>trans('message.no_permission'), 'alert-type' => 'warning'], 403);
        }

        $agents = Staff::getAll();
        return view('payment/commission-payment', compact('agents'));
    }

    /**
     * Save agent commission payment.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function save(Request $request)
    {
        if(!Auth::user()->can('staff.commission')){
            return back()->with([Message::ERROR_KEY=>trans('message.no_permission'), 'alert-type' => 'warning'], 403);
        }

        session()->flash(Message::ERROR_KEY, 'This function is blocked temporarily.');
        return back();

        $this->validate($request, [
            'agent_id' => 'required|integer',
            'payment_date' => 'required|date',
            'payment_amount' => 'required|numeric|gt:0'
        ]);

        $paymentAmount = decimalNumber($request->payment_amount);
        $totalCommission = $this->getAgentCommission($request->agent_id);
        $paidCommission = CommissionPayment::where('staff_id', $request->agent_id)->sum('amount');
        $balance = decimalNumber($totalCommission - $paidCommission);

        // If inputted amount is greater than remaining commision amount
        if ($paymentAmount > $balance) {
            session()->flash(Message::ERROR_KEY, trans('message.payment_amount_lt_or_et_remaining_amount', [
                'amount' => $balance
            ]));
            return back()->withInput($request->all());
        }

        $commissionPayment = new CommissionPayment();
        $commissionPayment->staff_id = $request->agent_id;
        $commissionPayment->paid_date = dateIsoFormat($request->payment_date);
        $commissionPayment->amount = $paymentAmount;
        $commissionPayment->receipt_reference = $request->reference_number;
        $commissionPayment->note = $request->note;
        $commissionPayment->save();

        session()->flash(Message::SUCCESS_KEY, trans('message.item_saved_success'));
        return back();
    }

    /**
     * Get commission info of an agent.
     *
     * @param Request $request
     * @param int $staffId ID of the agent
     *
     * @return JsonResponse
     */
    public function getAgentCommissionInfo(Request $request, $staffId)
    {
        if(!Auth::user()->can('staff.commission')){
            return back()->with([Message::ERROR_KEY=>trans('message.no_permission'), 'alert-type' => 'warning'], 403);
        }

        if (!$request->ajax() || empty($staffId)) {
            return back();
        }

        $totalCommission = $this->getAgentCommission($staffId);
        $paidCommission = CommissionPayment::where('staff_id', $staffId)->sum('amount');

        return response()->json([
            'totalCommission' => decimalNumber($totalCommission, true),
            'paidCommission' => decimalNumber($paidCommission, true),
            'balance' => decimalNumber($totalCommission - $paidCommission, true),
        ]);
    }
}
