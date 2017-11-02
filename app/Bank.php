<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Bank extends Model
{
	protected $fillable = [
		'id', 
		'name', 
		'city', 
		'max_connections', 
		'max_amount'];


	public function investments() {
		return $this->hasMany('App\Investment', 'origin_id');
	}
}