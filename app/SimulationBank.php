<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SimulationBank extends Model
{
	protected $fillable = [
        'simulation_id',
        'bank_id'
    ];

	public function bank() {
		return $this->belongsTo('App\Bank');
	}    
}
