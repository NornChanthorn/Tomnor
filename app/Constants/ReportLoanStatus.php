<?php

namespace App\Constants;

use App\Constants\LoanStatus;

class ReportLoanStatus
{
    const PENDING = LoanStatus::PENDING;
    const ACTIVE = LoanStatus::ACTIVE;
    const PAID = LoanStatus::PAID;
    const REJECTED = LoanStatus::REJECTED;
    const NEAR_PAYMENT_DATE = 'npd';
    const ON_PAYMENT_DATE = 'opd';
}
