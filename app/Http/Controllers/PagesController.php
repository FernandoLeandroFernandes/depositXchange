<?php

namespace App\Http\Controllers;

use \Datetime;
use \Debugbar;

use App\Bank;
use App\Exchange;
use App\Simulation;
use App\SimulationBank;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\DB;

class PagesController extends Controller {

	private $data = array();

	public function about() {
		return view('pages.about');
	}

	public function index() {
		return view('pages.index');
	}

	public function banksJSON(Request $request) {
		return response()->json(Bank::all());
	}

	public function banks(Request $request) {
		$banks = Bank::paginate(20);
		return view('pages.banks', compact('banks'));
	}

	public function bank(Request $request, $action) {
		
		if ($action == "save") {

			$bank = Bank::updateOrCreate(
				[	'id' => $request->input('id')],
				[
					'name' => $request->input('name'),
					'city' => $request->input('city'),
					'max_amount' => $request->input('amount'),
					'max_connections' => $request->input('connections')
				]
			);
			return response()->json($bank);
	
		} else {
			Debugbar::addMessage("Unknown action [$action]!", 'mylabel');

		}

		$banks = Bank::paginate(20);
		return view('pages.banks', compact('banks'));
	}

	public function exchanges(Request $request) {
		$exchanges = Exchange::paginate(5);
		return view('pages.exchanges', compact('exchanges'));
	}

	public function simulations(Request $request) {
		$simulations = Simulation::paginate(5);
		return view('pages.simulations', compact('simulations'));
	}

	public function simulation(Request $request, $action) {

		if ($action == "new") {

			if ($request->input('id')) {

				$simulation = Simulation::create(
					[	'id' => $request->input('id') ],
					[
						'description' => $request->input('description'),
						'exchange_amount' => $request->input('exchange_amount'),
						'total_amount' => $request->input('total_amount'),
						'max_connections' => $request->input('max_connections')
					]
				);
			}

			return view('pages.simulation-new', compact('simulation'));

		} else if ($action == "setup") {

			$simulation = Simulation::find($request->input('simulation'));

			if (!$request->input('noupdate')) {

				$simulation = Simulation::updateOrCreate(
					[	'id' => $request->input('simulation-id') ],
					[
						'description' => $request->input('description'),
						'exchange_amount' => $request->input('exchange_amount'),
						'total_amount' => $request->input('total_amount'),
						'max_connections' => $request->input('max_connections')
					]
				);
			}

			$operation = $request->input('operation');

			if ($operation == 'add') {

				try {

					SimulationBank::create([
						'simulation_id' => $simulation->id,
						'bank_id' => $request->input('bank-id'),
					]);

				} catch (Exception $e) {

				}

			} else if ($operation == 'remove') {
				SimulationBank::destroy(
					$request->input('simulation_bank-id')
				);
			}

			$selected_banks = [];
			foreach ($simulation->simulationBanks as $simulationBank) {
				$selected_banks[] = $simulationBank->bank->id;
			}

			Debugbar::addMessage("Selected banks [".count($selected_banks)."]", 'mylabel');
			
			$banks = Bank::whereNotIn('id', $selected_banks)->paginate(20);
			return view('pages.simulation-setup', compact('simulation', 'banks'));
			

		} else if ($action == "run") {
			
			$simulation = Simulation::find($request->input('simulation'));

			if (!$simulation->isConsolidated()) {

				DB::delete('delete from exchanges where simulation_id = '.$simulation->id);

				$simulationBanks = DB::select('
					select "banks".id as "bank_id", "banks"."name" as "bank_name", "banks".max_amount, "banks".max_connections, sum("exchanges"."amount") as used_amount, count("exchanges"."origin_id") as used_connections
					from "simulation_banks"
					left outer join "exchanges" on ("exchanges"."consolidated" = 1 AND "exchanges"."origin_id" = "simulation_banks"."bank_id")
					left outer join "banks" on ("banks"."id" = "simulation_banks"."bank_id")
					where "simulation_banks"."simulation_id" = '. $simulation->id .'
					group by "simulation_banks"."bank_id";');
											
				$connections_simulated = 0;
				$amount_simulated = 0;
				$exchange_amount = 0;

				$simulation_max_connections = $simulation->max_connections;
				$simulation_exchange_amount = $simulation->exchange_amount;
				$simulation_total_amount = $simulation->total_amount;

				$this->data = array();

				while (count($simulationBanks) > 1 && 
					$amount_simulated < $simulation_total_amount && 
					$connections_simulated < $simulation_max_connections) {
					
					$simulationBankA = $simulationBanks[rand(0, count($simulationBanks)-1)];
					do {
						$simulationBankB = $simulationBanks[rand(0, count($simulationBanks)-1)];
					} while ($simulationBankA == $simulationBankB);
					
					$exchange_amount = min([$simulation_exchange_amount, 
											$this->availableAmount($simulationBankA),
											$this->availableAmount($simulationBankB)]);

					if ($exchange_amount) {
						$exchangeA = Exchange::create([
								'simulation_id' => $simulation->id,
								'origin_id' => $simulationBankA->bank_id,
								'destination_id' => $simulationBankB->bank_id,
								'amount' => $exchange_amount ]);

						$this->useAmount($simulationBankA, $exchange_amount);
						$this->useConnection($simulationBankA);
						
						$exchangeA = Exchange::create([
								'simulation_id' => $simulation->id,
								'origin_id' => $simulationBankB->bank_id,
								'destination_id' => $simulationBankA->bank_id,
								'amount' => $exchange_amount ]);

						$this->useAmount($simulationBankB, $exchange_amount);
						$this->useConnection($simulationBankB);

						$amount_simulated += $exchange_amount*2;
						$connections_simulated += 2;					
					}

				}
				
				Simulation::updateOrCreate(['id' => $simulation->id], ['status' => 1]);
			}

			$simulationBanks = DB::select('
				select "banks".id as "bank_id", "banks"."name" as "bank_name", "banks".max_amount, "banks".max_connections, sum("exchanges"."amount") as used_amount, count("exchanges"."origin_id") as used_connections
				from "simulation_banks"
				left outer join "exchanges" on ("exchanges"."simulation_id" = "simulation_banks"."simulation_id" AND "exchanges"."origin_id" = "simulation_banks"."bank_id")
				left outer join "banks" on ("banks"."id" = "simulation_banks"."bank_id")
				where "simulation_banks"."simulation_id" = '. $simulation->id .'
				group by "simulation_banks"."bank_id";');

			return view('pages.simulation-result', 
						compact('simulation', 
								'simulationBanks', 
								'simulation_max_connections', 
								'simulation_exchange_amount', 
								'simulation_total_amount'));

		} else if ($action == "results") {

			$simulation = Simulation::find($request->input('simulation'));
			
			$simulationBanks = DB::select('
				select "banks".id as "bank_id", "banks"."name" as "bank_name", "banks".max_amount, "banks".max_connections, sum("exchanges"."amount") as used_amount, count("exchanges"."origin_id") as used_connections
				from "simulation_banks"
				left outer join "exchanges" on ("exchanges"."simulation_id" = "simulation_banks"."simulation_id" AND "exchanges"."origin_id" = "simulation_banks"."bank_id")
				left outer join "banks" on ("banks"."id" = "simulation_banks"."bank_id")
				where "simulation_banks"."simulation_id" = '. $simulation->id .'
				group by "simulation_banks"."bank_id";');

			return view('pages.simulation-result', 
			compact('simulation', 
					'simulationBanks', 
					'simulation_max_connections', 
					'simulation_exchange_amount', 
					'simulation_total_amount'));
	
		} else if ($action == "consolidate") {
			
			$simulation = Simulation::find($request->input('simulation'));
			
			if ($simulation && $simulation->isSimulated()) {
				
				Simulation::updateOrCreate([ 'id' => $simulation->id ], [ 'status' => 2 ]);
				
				DB::update('update "exchanges" set consolidated = 1 where simulation_id = ' . $simulation->id);
			}

			$simulations = Simulation::paginate(5);
			return view('pages.simulations', compact('simulations'));
	
		} else if ($action == "revert") {
			
			$simulation = Simulation::find($request->input('simulation'));
			
			if ($simulation && $simulation->isConsolidated()) {
				
				Simulation::updateOrCreate([ 'id' => $simulation->id ], [ 'status' => 1 ]);
				
				DB::update('update "exchanges" set consolidated = 0 where simulation_id = ' . $simulation->id);
			}

			$simulations = Simulation::paginate(5);
			return view('pages.simulations', compact('simulations'));
	
		} else if ($action == "delete") {
			
			$simulation = Simulation::find($request->input('simulation'));
			
			if ($simulation && !$simulation->isConsolidated()) {
				DB::delete('delete from exchanges where simulation_id = '.$simulation->id);
				DB::delete('delete from simulation_banks where simulation_id = '.$simulation->id);
				DB::delete('delete from simulations where id = '.$simulation->id);
			}

		} else {
			Debugbar::addMessage("Unknown action [$action]!", 'mylabel');
		}

		$simulations = Simulation::paginate(20);
		return view('pages.simulations', compact('simulations'));
	}

	function availableAmount($simulationBank) {

		if (!isset($this->data[$simulationBank->bank_id])) {

			$available = ($simulationBank->max_amount - $simulationBank->used_amount);
			$this->data[$simulationBank->bank_id][0] = ($available >= 0) ? $available : 0;

			// ob_start();
			// var_dump($this->data);
			// $out = ob_get_clean();
			// Debugbar::addMessage("Data written! OUT:[".$out."]", 'mylabel');
			
		} else {
			// Debugbar::addMessage("HAS data[$simulationBank->bank_id][0]!", 'mylabel');
		}
		return $this->data[$simulationBank->bank_id][0];
	}
	
	function availableConnections($simulationBank) {
		if (!isset($this->data[$simulationBank->bank_id][1])) {
			$available = ($simulationBank->max_connections - $simulationBank->used_connections);
			$this->data[$simulationBank->bank_id][1] = ($available >= 0 ? $available : 0);
		}
		return $this->data[$simulationBank->bank_id][1];
	}
	
	function useAmount($simulationBank, $amount) {
		$available = $this->availableAmount($simulationBank, $this->data) - $amount;
		$this->data[$simulationBank->bank_id][0] = ($available > 0 ? $available : 0);

		// ob_start();
		// var_dump($this->data);
		// $out = ob_get_clean();
		// Debugbar::addMessage("useAmount! OUT:[".$out."]", 'mylabel');
	}
	
	function useConnection($simulationBank) {
		$available = $this->availableConnections($simulationBank, $this->data) - 1;
		$this->data[$simulationBank->bank_id][1] = ($available > 0 ? $available : 0);
	}
	
	public function status(Request $request) {
		return view('pages.status');
	}
}