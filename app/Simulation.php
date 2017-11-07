<?php

namespace App;

use \Debugbar;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

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

	public function hasSimulationBanks() {

		$select = DB::select('select count(id) as banks from simulation_banks where simulation_id = ' . $this->id)[0];
		return ((int)($select->banks) > 0);
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
