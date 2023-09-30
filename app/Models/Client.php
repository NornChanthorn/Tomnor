<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Kyslik\ColumnSortable\Sortable;

class Client extends Model
{
    use Sortable;

    public $sortable = [
        'name',
        'gender',
        'id_card_number',
        'first_phone',
        'second_phone',
        'sponsor_name',
        'sponsor_phone',
        'sponsor_phone_2',
    ];

    public function commune()
    {
        return $this->belongsTo(Address::class, 'commune_id');
    }

    public function district()
    {
        return $this->belongsTo(Address::class, 'district_id');
    }

    /**
     * Get all records.
     *
     * @param array $fields
     *
     * @return mixed
     */
    public static function getAll($fields = ['*'])
    {
        return self::orderBy('name')->get($fields);
    }

    public function loans()
    {
        return $this->hasMany(Loan::class, 'client_id');
    }

    public function province()
    {
        return $this->belongsTo(Address::class, 'province_id');
    }

    public function sponsorCommune()
    {
        return $this->belongsTo(Address::class, 'sponsor_commune_id');
    }

    public function sponsorDistrict()
    {
        return $this->belongsTo(Address::class, 'sponsor_district_id');
    }

    public function sponsorProvince()
    {
        return $this->belongsTo(Address::class, 'sponsor_province_id');
    }

    public function sponsorVillage()
    {
        return $this->belongsTo(Address::class, 'sponsor_village_id');
    }

    public function village()
    {
        return $this->belongsTo(Address::class, 'village_id');
    }
    public function getLocationAttribute(){
        $provice = null;
        $district = null;
        $commune = null;
        $village = null;
        if(@$this->province->khmer_name){
            $province = @$this->province->khmer_name ? @$this->province->khmer_name : '';
        }else{
            $province = @$this->province->name ? @$this->province->name : '';
        }
        if(@$this->district->khmer_name){
            $district = @$this->district->khmer_name ? ', '.@$this->district->khmer_name : "";
        }else{
            $district = @$this->district->name ? ', '.@$this->district->name : "";
        }
        if(@$this->commune->khmer_name){
            $commune = @$this->commune->khmer_name ? ', '.@$this->commune->khmer_name : "";
        }else{
            $commune = @$this->commune->name ? ', '.@$this->commune->name : "";
        }
        if(@$this->village->name){
            $village = @$this->village->name ? ', '.$this->village->name:'';
        }else{
            $village = @$this->village->name ? ', '.@$this->village->name: '';
        }
        $location = $province.$district.$commune.$village;
        return  $location;
    }
    public function getSlocationAttribute(){
        $provice = null;
        $district = null;
        $commune = null;
        $village = null;
        if(@$this->sponsorProvince->khmer_name){
            $province = @$this->sponsorProvince->khmer_name ? @$this->sponsorProvince->khmer_name : '';
        }else{
            $province = @$this->sponsorProvince->name ? @$this->sponsorProvince->name : '';
        }
        if(@$this->sponsorDistrict->khmer_name){
            $district = @$this->sponsorDistrict->khmer_name ? ', '.@$this->sponsorDistrict->khmer_name : "";
        }else{
            $district = @$this->sponsorDistrict->name ? ', '.@$this->sponsorDistrict->name : "";
        }
        if(@$this->sponsorCommune->khmer_name){
            $commune = @$this->sponsorCommune->khmer_name ? ', '.@$this->sponsorCommune->khmer_name : "";
        }else{
            $commune = @$this->sponsorCommune->name ? ', '.@$this->sponsorCommune->name : "";
        }
        if(@$this->sponsorVillage->name){
            $village = @$this->sponsorVillage->name ? ', '.$this->sponsorVillage->name:'';
        }else{
            $village = @$this->sponsorVillage->name ? ', '.@$this->sponsorVillage->name: '';
        }
     
        $location = $province.$district.$commune.$village;
        return  $location;
    }
    
}
