<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VariantionLocationDetails extends Model
{
  function location(){
      return $this->hasOne(Branch::class, 'id', 'location_id');
  }

    function variants(){
        return $this->hasMany(Variantion::class, 'id', 'variant_id');
    }

}
