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

	// private function synchronize($season) {

	// 	// if there's an already synced season on record...
	// 	if (!is_null($season->sync)) {

	// 		$openMatch = Match::where('league_id', $season->id)
	// 						  ->where('finished', 0)
	// 						  ->orderBy('timeUTC')
	// 						  ->first();
	
	// 		if (empty($openMatch)) return false;

	// 		$matchTime = DateTime::createFromFormat('Y-m-d G:i:s', $openMatch->timeUTC);

	// 		if ($matchTime > new Datetime()) return false;
	// 	}

	// 	// synchronize data
	// 	$this->synchronizeTeams($season->league, $season->year);
	// 	$this->synchronizeMatches($season->league, $season->year);

	// 	return true;
	// }

	// private function synchronizeTeams($league, $year) {
		
	// 	$url = 'https://www.openligadb.de/api/getavailableteams/'.$league.'/'.$year;

	// 	$response = \Httpful\Request::get($url)->send();

	// 	$teams = ($response->body);

	// 	foreach ($teams as $teamData) {
			
	// 		$team = Team::firstOrCreate(
	// 			[ 'id' => $teamData->TeamId ],
	// 			[
	// 			'shortName' => $teamData->ShortName,
	// 			'name' => $teamData->TeamName,
	// 			'shield' => $teamData->TeamIconUrl
	// 			]
	// 		);

	// 	}
	// }

	// private function synchronizeMatches($league, $year) {
	
	// 	$url = 'https://www.openligadb.de/api/getmatchdata/'.$league.'/'.$year;

	// 	$response = \Httpful\Request::get($url)->send();

	// 	$matches = ($response->body);

	// 	foreach ($matches as $matchData) {

	// 		if (!isset($thisLeague)) {

	// 			$thisLeague = League::find($matchData->LeagueId);

	// 			if (is_null($thisLeague) || is_null($thisLeague->id) || is_null($thisLeague->sync)) {

	// 				League::
	// 					  where('league', $league)
	// 					->where('year', $year)
	// 					->update([
	// 						'id' => $matchData->LeagueId, 
	// 						'name' => $matchData->LeagueName,
	// 						'sync' => new Datetime()
	// 					]);
	// 			}
	// 		}

	// 		$winnerTeam = NULL;
	// 		$matchScore = end($matchData->Goals);
	// 		if (!empty($matchScore)) {
	// 			if ($matchScore->ScoreTeam1 != $matchScore->ScoreTeam2) {
	// 				$winnerTeam = $matchScore->ScoreTeam1 > $matchScore->ScoreTeam2 ? 
	// 									$matchData->Team1->TeamId : 
	// 									$matchData->Team2->TeamId;
	// 			}
	// 		}

	// 		$thisMatch = Match::updateOrCreate(
	// 			[ 'id' => $matchData->MatchID ],
	// 			[
	// 			'league_id'		 => $matchData->LeagueId,
	// 			'timeUTC'		 => (new DateTime($matchData->MatchDateTimeUTC)),
	// 			'finished'		 => $matchData->MatchIsFinished,
	// 			'team1_id'		 => $matchData->Team1->TeamId,
	// 			'team2_id'		 => $matchData->Team2->TeamId,
	// 			'scoreTeam1'	 => (!empty($matchScore) ? $matchScore->ScoreTeam1 : 0),
	// 			'scoreTeam2'	 => (!empty($matchScore) ? $matchScore->ScoreTeam2 : 0),
	// 			'winner_team_id' => (!empty($winnerTeam) ? $winnerTeam : NULL)
	// 			]
	// 		);
	// 	}
	// }

	// public function seasonMatchesJSON() {
	// 	$this->synchronizeData();
	// 	return Response::json(Match::get());
	// }

	// public function seasonMatches(Request $request) {

	// 	$league = $request->input('league', 'bl1');
	// 	$year	= $request->input('year', idate('Y'));
		
	// 	$season = $this->loadSeason($league, $year);

	// 	$matches = $season->matches()->paginate(10);

	// 	return view('pages.seasonMatches', compact('league', 'year', 'season', 'matches'));
	// }

	// public function teamsRatios(Request $request) {

	// 	$league = $request->input('league', 'bl1');
	// 	$year	= $request->input('year', idate('Y'));

	// 	$season = $this->loadSeason($league, $year);

	// 	$sql = 
	// 		'SELECT tm.*, count(DISTINCT(am.id)) AS all_matches, count(DISTINCT(wm.id)) AS winner_matches
	// 		FROM teams AS tm
	// 		LEFT OUTER JOIN matches AS am ON ((tm.id = am.team1_id OR tm.id = am.team2_id) AND (am.finished = 1))
	// 		LEFT OUTER JOIN matches AS wm ON ((tm.id = wm.winner_team_id) AND (wm.finished = 1))
	// 		WHERE (am.league_id = '.$season->id.') AND (wm.league_id = '.$season->id.')
	// 		GROUP BY tm.id
	// 		ORDER BY winner_matches DESC';

	// 	$teams = DB::select($sql);

	// 	return view('pages.teamsRatios', compact('league', 'year', 'season', 'teams'));
	// }

	/********************************************************************************************************/

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
		$exchanges = Exchange::paginate(20);
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

			$simulationBanks = DB::select('
				select "banks".id as "bank_id", "banks"."name" as "bank_name", "banks".max_amount, "banks".max_connections, sum("exchanges"."amount") as used_amount, count("exchanges"."origin_id") as used_connections
				from "simulation_banks"
				left outer join "exchanges" on ("exchanges"."simulation_id" = "simulation_banks"."simulation_id" and "exchanges"."origin_id" = "simulation_banks"."bank_id")
				left outer join "banks" on ("banks"."id" = "simulation_banks"."bank_id")
				where "simulation_banks"."simulation_id" = '. $simulation->id .'
				group by "simulation_banks"."bank_id";');
										
			$connections_simulated = 0;
			$amount_simulated = 0;
			$exchange_amount = 0;

			$simulation_max_connections = $simulation->max_connections;
			$simulation_exchange_amount = $simulation->exchange_amount;
			$simulation_total_amount = $simulation->total_amount;

			$data = array();

			while (count($simulationBanks) > 1 && 
				   $amount_simulated < $simulation_total_amount && 
				   $connections_simulated < $simulation_max_connections) {
				
				$simulationBankA = $simulationBanks[rand(0, count($simulationBanks)-1)];
				do {
					$simulationBankB = $simulationBanks[rand(0, count($simulationBanks)-1)];
				} while ($simulationBankA == $simulationBankB);
				
				$exchange_amount = min([$simulation_exchange_amount, 
										$this->availableAmount($simulationBankA, $data),
										$this->availableAmount($simulationBankB, $data)]);

				$exchangeA = Exchange::create([
					'simulation_id' => $simulation->id,
					'origin_id' => $simulationBankA->bank_id,
					'destination_id' => $simulationBankB->bank_id,
					'amount' => $exchange_amount
				]);
				$this->useAmount($simulationBankA, $exchange_amount, $data);
				$this->useConnection($simulationBankA, $data);
				
				$exchangeA = Exchange::create([
					'simulation_id' => $simulation->id,
					'origin_id' => $simulationBankB->bank_id,
					'destination_id' => $simulationBankA->bank_id,
					'amount' => $exchange_amount
				]);
				$this->useAmount($simulationBankB, $exchange_amount, $data);
				$this->useConnection($simulationBankB, $data);

				$amount_simulated += $exchange_amount;
				$connections_simulated++;					
			}
			
			Simulation::updateOrCreate(['id' => $simulation->id], [ 'status' => 1]);
			
			$simulationBanks = DB::select('
				select "banks".id as "bank_id", "banks"."name" as "bank_name", "banks".max_amount, "banks".max_connections, sum("exchanges"."amount") as used_amount, count("exchanges"."origin_id") as used_connections
				from "simulation_banks"
				left outer join "exchanges" on ("exchanges"."origin_id" = "simulation_banks"."bank_id")
				left outer join "banks" on ("banks"."id" = "simulation_banks"."bank_id")
				where "simulation_banks"."simulation_id" = '. $simulation->id .'
				group by "simulation_banks"."bank_id";');

			return view('pages.simulation-run', compact('simulation', 'simulationBanks'));
				
		} else {
			Debugbar::addMessage("Unknown action [$action]!", 'mylabel');
		}

		$simulations = Simulation::paginate(20);
		return view('pages.simulations', compact('simulations'));
	}

	function availableAmount($simulationBank, $data) {
		if (empty($data[$simulationBank->bank_id][0])) {
			Debugbar::addMessage("NO data[$simulationBank->bank_id][0]!", 'mylabel');
			$available = ($simulationBank->max_amount - $simulationBank->used_amount);
			$data[$simulationBank->bank_id][0] = ($available >= 0) ? $available : 0;
			Debugbar::addMessage("Data written in data[simulationBank->bank_id][0]=[$data[$simulationBank->bank_id]!", 'mylabel');
			
		} else {
			Debugbar::addMessage("HAS data[$simulationBank->bank_id][0]!", 'mylabel');
		}
		return $data[$simulationBank->bank_id][0];
	}
	
	function availableConnections($simulationBank, $data) {
		if (empty($data[$simulationBank->bank_id][1])) {
			$available = ($simulationBank->max_connections - $simulationBank->used_connections);
			$data[$simulationBank->bank_id][1] = ($available >= 0 ? $available : 0);
		}
		return $data[$simulationBank->bank_id][1];
	}
	
	function useAmount($simulationBank, $amount, $data) {
		$available = $this->availableAmount($simulationBank, $data) - $amount;
		$data[$simulationBank->bank_id][0] -= ($available > 0 ? $available : 0);
	}
	
	function useConnection($simulationBank, $data) {
		$available = $this->availableConnections($simulationBank, $data) - 1;
		$data[$simulationBank->bank_id][1] -= ($available > 0 ? $available : 0);
	}
	
	public function status(Request $request) {
		return view('pages.status');
	}
}