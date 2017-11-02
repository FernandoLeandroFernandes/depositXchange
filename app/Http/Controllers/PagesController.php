<?php

namespace App\Http\Controllers;

use \Datetime;
use \Debugbar;

use App\Bank;
use App\Exchange;
use App\Simulation;

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

	public function setupSimulation(Request $request) {
		return view('pages.setupSimulation');
	}
	
	public function newSimulation(Request $request) {

		$simulation = new Simulation();

		$simulations = Simulation::all();
		return view('pages.simulations', compact('simulations'));
	}

	public function runSimulation(Request $request) {
		return view('pages.runsimulation');
	}

	public function simulations(Request $request) {
		$simulations = Simulation::all();
		return view('pages.simulations', compact('simulations'));
	}

	public function status(Request $request) {
		return view('pages.status');
	}
}