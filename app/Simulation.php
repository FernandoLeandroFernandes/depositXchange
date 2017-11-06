<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Simulation extends Model
{
	protected $fillable = [
		'description', 
		'max_connections', 
        'exchange_amount',
		'total_amount',
		'status'
    ];

	public function simulationBanks() {
		return $this->hasMany('App\SimulationBank');
	}

	public function isNew() {
		return $this->status < 1;
	}

	public function isSimulated() {
		return $this->status == 1;
	}

	public function isConsolidated() {
		return $this->status > 1;
	}

	public function status() {
		// Just to remember: ternary operators in PHP sucks...
		return ($this->isNew() ? 'New' : ($this->isSimulated() ? 'Simulated' : ($this->isConsolidated() ? 'Consolidated' : '...')));
	}
}
