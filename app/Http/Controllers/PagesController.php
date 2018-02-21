<?php

namespace App\Http\Controllers;

use \Datetime;

use App\Bank;
use App\Exchange;
use App\Simulation;
use App\SimulationBank;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Collection;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;

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

		$select = DB::select('
			select "banks".id, "banks"."name", "banks"."city", "banks".max_amount, "banks".max_connections, sum("exchanges"."amount") as used_amount, count("exchanges"."origin_id") as used_connections
			from "banks"
			left outer join "exchanges" on ("exchanges"."consolidated" = 1 AND "exchanges"."origin_id" = "banks"."id")
			group by "banks"."id";');

		$itemsPerPage = 8;
		$currentPage  = LengthAwarePaginator::resolveCurrentPage('page');
		$collection   = new Collection($select);
		
		$paginatedResults = $collection->slice(($currentPage - 1) * $itemsPerPage, $itemsPerPage)->all();
		$banks = new LengthAwarePaginator(
							$paginatedResults, 
							count($collection), 
							$itemsPerPage, 
							$currentPage,
							[
								'path' => LengthAwarePaginator::resolveCurrentPath(),
								'currentPage' => 'currentPage',
							]);


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

		} else if ($action == "details") {

			if ($request->input('bank')) {

				$bank_id = (int)$request->input('bank');

				$bank = DB::select('
					select "banks".id, "banks"."name", "banks"."city", "banks".max_amount, "banks".max_connections, sum("exchanges"."amount") as used_amount, count("exchanges"."origin_id") as used_connections
					from "banks"
					left outer join "exchanges" on ("exchanges"."consolidated" = 1 AND "exchanges"."origin_id" = "banks"."id")
					where  "banks".id = ?
					group by "banks"."id";',
					[$bank_id])[0];

				$exchanges = DB::select('
					select "sm"."description" as "simulation", "bo"."id" as "origin_id", "bo"."name" as "origin_name", "bd"."id" as "destination_id", "bd"."name" as "destination_name", "ex"."amount"
					from "exchanges" as "ex"
					left outer join "simulations" as "sm" on ("sm"."id" = "ex"."simulation_id")
					left outer join "banks" as "bo" on ("bo"."id" = "ex"."origin_id")
					left outer join "banks" as "bd" on ("bd"."id" = "ex"."destination_id")
					where "ex"."consolidated" = 1 AND ("bo".id = '.$bank_id.' OR "bd".id = '.$bank_id.')
					order by "ex"."id";');

				return view('pages.banks-detail', compact('bank', 'exchanges'));
			}
		}

		$banks = Bank::paginate(20);

		return view('pages.banks', compact('banks'));
	}

	public function exchanges(Request $request) {
		$exchanges = Exchange::where('consolidated', 1)->paginate(10);
		return view('pages.exchanges', compact('exchanges'));
	}

	public function simulations(Request $request) {
		$simulations = Simulation::paginate(5);
		return view('pages.simulations', compact('simulations'));
	}

	public function simulation(Request $request, $action) {

		if ($action == "new") {

			if ($request->input('id')) {

				$simulation = Simulation::updateOrCreate(
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
					[	'id' => $request->input('simulation') ],
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
					$simulationBank = SimulationBank::create([
						'simulation_id' => $simulation->id,
						'bank_id' => $request->input('bank-id'),
					]);

					// return response()->json($simulationBank);

				} catch (Exception $e) {

				}

			} else if ($operation == 'remove') {
				SimulationBank::destroy(
					$request->input('simulation_bank-id')
				);
				// return  response()->json(['status'=>'processsed']);
			}

			$selectedBanks = DB::select('
				select "simulation_banks".id as "simulation_bank_id", "banks".id as "bank_id", "banks"."name" as "bank_name", "banks".max_amount, "banks".max_connections, sum("exchanges"."amount") as used_amount, count("exchanges"."origin_id") as used_connections
				from "simulation_banks"
				left outer join "exchanges" on ("exchanges"."consolidated" = 1 AND "exchanges"."origin_id" = "simulation_banks"."bank_id")
				left outer join "banks" on ("banks"."id" = "simulation_banks"."bank_id")
				where "simulation_banks"."simulation_id" = ? 
				group by "simulation_banks"."bank_id";', 
				[$simulation->id]);

			$chosenBanks = [];
			foreach ($simulation->simulationBanks as $simulationBank) {
				$chosenBanks[] = $simulationBank->bank->id;
			}

			$select = DB::select('
				select "banks".id as "bank_id", "banks"."name" as "bank_name", "banks".max_amount, "banks".max_connections, sum("exchanges"."amount") as used_amount, count("exchanges"."origin_id") as used_connections
				from "banks"
				left outer join "exchanges" on ("exchanges"."consolidated" = 1 AND "exchanges"."origin_id" = "banks"."id")
				where  NOT "banks".id IN ('.implode($chosenBanks, ',').')
				group by "banks"."id";');
			
			$itemsPerPage = 6;
			$currentPage  = LengthAwarePaginator::resolveCurrentPage('page');
			$collection   = new Collection($select);
			
			$paginatedResults = $collection->slice(($currentPage - 1) * $itemsPerPage, $itemsPerPage)->all();
			$availableBanks = new LengthAwarePaginator(
								$paginatedResults, 
								count($collection), 
								$itemsPerPage, 
								$currentPage,
								[
									'path' => LengthAwarePaginator::resolveCurrentPath(),
									'currentPage' => 'currentPage',
								]);
		

				// select "banks".id as "bank_id", "banks"."name" as "bank_name", "banks".max_amount, "banks".max_connections, sum("exchanges"."amount") as used_amount, count("exchanges"."origin_id") as used_connections
				// from "simulation_banks"
				// left outer join "exchanges" on ("exchanges"."consolidated" = 1 AND "exchanges"."origin_id" = "simulation_banks"."bank_id")
				// left outer join "banks" on ("banks"."id" = "simulation_banks"."bank_id")
				// where "simulation_banks"."simulation_id" = ? AND "banks".id NOT IN (?)
				// group by "simulation_banks"."bank_id";', 
				// [$simulation->id, implode($chosenBanks, ',')]);

				// $availableBanks = Bank::whereNotIn('id', $chosenBanks)->paginate(20);

				
			return view('pages.simulation-setup', compact('simulation', 'selectedBanks', 'availableBanks'));
			

		} else if ($action == "run") {
			
			$simulation = Simulation::find($request->input('simulation'));

			if (!$simulation->isConsolidated()) {

				DB::delete('delete from exchanges where simulation_id = ' . $simulation->id);

				$simulationBanks = DB::select('
					select "banks".id as "bank_id", "banks"."name" as "bank_name", "banks".max_amount, "banks".max_connections, sum("exchanges"."amount") as used_amount, count("exchanges"."origin_id") as used_connections
					from "simulation_banks"
					left outer join "exchanges" on ("exchanges"."consolidated" = 1 AND "exchanges"."origin_id" = "simulation_banks"."bank_id")
					left outer join "banks" on ("banks"."id" = "simulation_banks"."bank_id")
					where "simulation_banks"."simulation_id" = ?
					group by "simulation_banks"."bank_id";',
					[$simulation->id]);

				if (count($simulationBanks) > 1) {

					$connections_simulated = 0;
					$amount_simulated = 0;
					$exchange_amount = 0;

					$count = (count($simulation->simulationBanks));
					$simulation_max_connections = $count*($count-1);
					$simulation_exchange_amount = $simulation->exchange_amount;

					$this->data = array();

					$originBanks = $simulationBanks;

					foreach ($originBanks as $originBank) {

						$destinationBanks = $this->destinationBanks($originBank, $simulationBanks);

						foreach ($destinationBanks as $destinationBank) {

							$exchange_amount = min([$simulation_exchange_amount, 
													$this->availableAmount($originBank),
													$this->availableAmount($destinationBank)]);

							$availableConnections = min([$this->availableConnections($originBank),
															$this->availableConnections($destinationBank)]);

							if ($exchange_amount && $availableConnections) {

								Exchange::create([
									'simulation_id' => $simulation->id,
									'origin_id' => $originBank->bank_id,
									'destination_id' => $destinationBank->bank_id,
									'amount' => $exchange_amount ]);

								$this->useAmount($originBank, $exchange_amount);
								$this->useConnection($originBank);
								
								Exchange::create([
									'simulation_id' => $simulation->id,
									'origin_id' => $destinationBank->bank_id,
									'destination_id' => $originBank->bank_id,
									'amount' => $exchange_amount ]);

								$this->useAmount($destinationBank, $exchange_amount);
								$this->useConnection($destinationBank);
								$this->linkSimulationBanks($originBank, $destinationBank);

								$amount_simulated += $exchange_amount*2;
								$connections_simulated += 2;					
							}
						}
					}

					// Update simulations data
					$simulation->status = 1;
					$simulation->used_amount = $amount_simulated;
					$simulation->used_connections = $connections_simulated;
					$simulation->update();
				}
			}

			$simulationBanks = DB::select('
				select "banks".id as "bank_id", "banks"."name" as "bank_name", "banks".max_amount, "banks".max_connections, sum("exchanges"."amount") as used_amount, count("exchanges"."origin_id") as used_connections
				from "simulation_banks"
				left outer join "exchanges" on ("exchanges"."simulation_id" = "simulation_banks"."simulation_id" AND "exchanges"."origin_id" = "simulation_banks"."bank_id")
				left outer join "banks" on ("banks"."id" = "simulation_banks"."bank_id")
				where "simulation_banks"."simulation_id" = ? 
				group by "simulation_banks"."bank_id";', 
				[$simulation->id]);

			$exchanges = $simulation->exchanges()->get();

			return view('pages.simulation-result', 
						compact('simulation', 
								'simulationBanks', 
								'simulation_max_connections', 
								'simulation_exchange_amount', 
								'simulation_total_amount',
								'exchanges'));

		} else if ($action == "results") {

			$simulation = Simulation::find($request->input('simulation'));
			
			$simulationBanks = DB::select('
				select "banks".id as "bank_id", "banks"."name" as "bank_name", "banks".max_amount, "banks".max_connections, sum("exchanges"."amount") as used_amount, count("exchanges"."origin_id") as used_connections
				from "simulation_banks"
				left outer join "exchanges" on ("exchanges"."simulation_id" = "simulation_banks"."simulation_id" AND "exchanges"."origin_id" = "simulation_banks"."bank_id")
				left outer join "banks" on ("banks"."id" = "simulation_banks"."bank_id")
				where "simulation_banks"."simulation_id" = '. $simulation->id .'
				group by "simulation_banks"."bank_id";');

			$exchanges = $simulation->exchanges()->get();

			return view('pages.simulation-result', 
						compact('simulation', 
								'simulationBanks', 
								'simulation_max_connections', 
								'simulation_exchange_amount', 
								'simulation_total_amount',
								'exchanges'));
	
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
				DB::delete('delete from exchanges where simulation_id = ' . $simulation->id);
				DB::delete('delete from simulation_banks where simulation_id = ' . $simulation->id);
				DB::delete('delete from simulations where id = ' . $simulation->id);
			}
		}

		$simulations = Simulation::paginate(5);
		return view('pages.simulations', compact('simulations'));
	}

	function sortBanksByAvailableAmount(&$banks) {
	
		$amounts = array();
		foreach ($banks as &$bank) {
			$amounts[] = $this->availableAmount($bank);
		}
		array_multisort($amounts, $banks);
	}

	function linkSimulationBanks($origin, $destination) {

		if (!isset($this->data[$origin->bank_id][2])) {
			$this->data[$origin->bank_id][2] = array();
		}
		$this->data[$origin->bank_id][2][] = $destination->bank_id;
			
		if (!isset($this->data[$destination->bank_id][2])) {
			$this->data[$destination->bank_id][2] = array();
		}
		$this->data[$destination->bank_id][2][] = $origin->bank_id;
	}

	function alreadyLinkedSimulationBanks($origin, $destination) {
		
		if (isset($this->data[$origin->bank_id][2]) &&
			isset($this->data[$destination->bank_id][2])) {

			return in_array($origin, $this->data[$destination->bank_id][2]) && 
				   in_array($destination, $this->data[$origin->bank_id][2]);

		}
		return false;
	}

	function destinationBanks($originBank, $simulationBanks) {
	
		$destinationBanks = array();
		foreach ($simulationBanks as $simulationBank) {
			if ($simulationBank->bank_id != $originBank->bank_id) {

				if (isset($this->data[$originBank->bank_id][2])) {

					$found = false;
					foreach ($this->data[$originBank->bank_id][2] as $id) {
						if ($found = ($id == $simulationBank->bank_id)) {
							break;
						}
					}
					if ($found) continue;
				}
				$destinationBanks[] = $simulationBank;
			}
		}
		return $destinationBanks;
	}

	function selectOriginBank($simulation, $simulationBanks) {

		$countSimulationBanks = count($simulationBanks);
		
		if ($countSimulationBanks) {

			$this->sortBanksByAvailableAmount($simulationBanks);

			$index = 0;
			do {
				$origin = $simulationBanks[$index++];
				if ($index > $countSimulationBanks) {
					return null;
				}
			} while (!$this->availableConnections($origin) ||
					 !$this->availableMutualConnections($origin, $simulationBanks));

			return $origin;
		}
		return null;
	}
	
	function selectDestinationBank($origin, $simulation, $simulationBanks) {

		$countSimulationBanks = count($simulationBanks);

		if ($origin && $countSimulationBanks > 1) {
			
			$this->sortBanksByAvailableAmount($simulationBanks);

			$index = 1;
			do {

				$destination = $simulationBanks[$index++];
				if ($index > $countSimulationBanks) return null;

			} while (!$this->availableConnections($destination) ||
					 !$this->availableMutualConnections($destination, $simulationBanks) ||
					  $this->alreadyLinkedSimulationBanks($origin, $destination));

			$this->linkSimulationBanks($origin, $destination);

			return $destination;
		}
		return null;
	}
	
	function availableAmount($simulationBank) {

		if (!isset($this->data[$simulationBank->bank_id])) {

			$available = ($simulationBank->max_amount - $simulationBank->used_amount);
			$this->data[$simulationBank->bank_id][0] = ($available >= 0) ? $available : 0;
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
	
	function availableMutualConnections($simulationBank, $simulationBanks) {

		$usedMutualConnections = 0;

		if (isset($this->data[$simulationBank->bank_id][2])) {
			$usedMutualConnections = $this->data[$simulationBank->bank_id][2].length;
		}
		
		$totalMutualConnections = (count($simulationBanks)-1);
		$availableMutualConnections = ($totalMutualConnections-$usedMutualConnections);
		
		return $availableMutualConnections;
	}
	
	function useAmount($simulationBank, $amount) {
		$available = $this->availableAmount($simulationBank) - $amount;
		$this->data[$simulationBank->bank_id][0] = ($available > 0 ? $available : 0);
	}
	
	function useConnection($simulationBank) {
		$available = $this->availableConnections($simulationBank, $this->data) - 1;
		$this->data[$simulationBank->bank_id][1] = ($available > 0 ? $available : 0);
	}
	
	function toString($object) {
		ob_start();
		var_dump($object);
		return ob_get_clean();
	}

	public function status(Request $request) {
		return view('pages.status');
	}
}