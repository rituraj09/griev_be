<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model; 
use Laravel\Sanctum\HasApiTokens; 

class Official extends Model
{
    use HasApiTokens, HasFactory; 
    protected $hidden = [
        'password',
        'remember_token',
        'id',
    ];
    public $timestamps      = false; 
    protected $table    	= 'officials';
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];
    public function assign()
    {
		return $this->hasone('App\Models\OfficialAssign','user_id','id');
	}
    public function desig()
    {
		return $this->belongsTo('App\Models\Designation','designation' )->withDefault();
	}
}
