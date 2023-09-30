<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ScheduleReference extends Model
{
    //
    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
    public function scheduleHistory()
    {
        return $this->hasMany(\App\Models\ScheduleHistory::class, 'schedule_reference_id');
    }
}
