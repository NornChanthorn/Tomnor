<?php

namespace App\Traits;

use App\Models\Loan;
use App\Constants\LoanStatus;

trait AgentUtil
{
    /**
     * Calculate commission amount of one or all agents.
     *
     * @param int|null $staffId Get commission amount of all agents if null
     *
     * @return double
     */
    public function getAgentCommission($staffId = null)
    {
        $paidLoans = Loan::where('status', LoanStatus::ACTIVE)
            ->whereDoesntHave('schedules', function ($query) {
                $query->where('paid_status', 0);
            });
        if ($staffId !== null) {
            $paidLoans = $paidLoans->where('staff_id', $staffId);
        }
        $paidLoans = $paidLoans->get();

        $totalPaidInterest = 0;
        foreach ($paidLoans as $paidLoan) {
            $totalPaidInterest += $paidLoan->schedules()->sum('paid_interest');
        }

        $commissionAmount = $totalPaidInterest * 0.2; // 20% of total interest of fully paid loans
        return $commissionAmount;
    }
}
