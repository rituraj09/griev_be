<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OfficialAssign extends Model
{
    use HasFactory;
    public $timestamps      = false; 
    protected $table    	= 'official_assigns';
    protected $hidden = [ 
        'id',
        'user_id', 
    ];
    
    public function offical()
    {
        return $this->belongsTo('App\Models\Official','user_id')->withDefault();
	} 
    public function organisation()
    {
        return $this->belongsTo('App\Models\Organisation','organisation_id')->withDefault();
	} 
    public function district()
    {
        return $this->belongsTo('App\Models\District','district_id')->withDefault();
	} 
}
