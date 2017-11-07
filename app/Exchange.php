<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Exchange extends Model
{
	protected $fillable = [
		'simulation_id',
		'origin_id',
		'destination_id',
		'amount'
	];

	public function simulation() {
		return $this->belongsTo('App\Simulation');
	}

	public function origin() {
		return $this->belongsTo('App\Bank');
	}

	public function destination() {
		return $this->belongsTo('App\Bank');
	}

}
