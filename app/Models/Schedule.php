<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    public function loan()
    {
        return $this->belongsTo(Loan::class, 'loan_id');
    }
    
}
