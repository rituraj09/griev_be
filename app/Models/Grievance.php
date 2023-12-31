<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Grievance extends Model
{
    use HasFactory;
    public $timestamps      = false; 
    public function police()
    {
        return $this->belongsTo('App\Models\Organisation','police_id')->withDefault();
	} 
}
