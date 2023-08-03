<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OfficialAssign extends Model
{
    use HasFactory;
    public $timestamps      = false; 
    protected $table    	= 'official_assigns';
   
    
    public function offical()
    {
        return $this->belongsTo('App\Models\Official','user_id')->withDefault();
	} 
}
