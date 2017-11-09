@extends('layouts.app')

@section('content')
<div class="mdl-card-simulation-setup">
	<div class="mdl-card__title">
		<h3 class="mdl-card__title-text">Setup Simulation</h3>
	</div>
	<div class="mdl-card__actions mdl-card--border">
		<div class="mdl-dialog__actions mdl-dialog__actions">
			<button id="finish-button" type="button" class="mdl-button"@if (!$simulation->id) disabled @endif>Finish</button>
		</div>
		<form id="simulation-setup" action="/simulation/setup" method="POST">
			{{ csrf_field() }}
			<input type="hidden" name="simulation" value="{{ $simulation->id }}">
			<div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
				<input 
					class="mdl-textfield__input" 
					type="text"
					name="description" 
					id="description" 
					name="description" 
					value="{{ $simulation->description }}"
					required>
				<label class="mdl-textfield__label" for="description">Description</label>
			</div>
			<!-- <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
				<input 
					class="mdl-textfield__input" 
					type="text" 
					pattern="-?[0-9]*(\.[0-9]+)?" 
					id="max_connections" 
					name="max_connections" 
					value="{{ $simulation->max_connections }}"
					required>
				<label class="mdl-textfield__label" for="max_connections">Maximum Connections per Bank</label>
				<span class="mdl-textfield__error">Input is not a number!</span>
			</div>
			<div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
				<input 
					class="mdl-textfield__input" 
					type="text" 
					pattern="-?[0-9]*(\.[0-9]+)?" 
					id="total_amount" 
					name="total_amount" 
					value="{{ $simulation->total_amount }}"
					required>
				<label class="mdl-textfield__label" for="total_amount">Total Amount per Bank</label>
				<span class="mdl-textfield__error">Input is not a number!</span>
			</div> -->
			<div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
				<input 
					class="mdl-textfield__input" 
					type="text" 
					id="exchange_amount" 
					name="exchange_amount" 
					value="{{ $simulation->exchange_amount }}"
					required>
				<label class="mdl-textfield__label" for="exchange_amount">Amount per Exchange</label>
				<span class="mdl-textfield__error">Input is not a number!</span>
			</div>
			<div class="mdl-dialog__actions mdl-dialog__actions">
				<button id="save-button" type="button" class="mdl-button" disabled>Update</button>
			</div>
		</form>

		<div id="mdl-table">
			<div class="mdl-card__title">
				<h3 class="mdl-card__title-text">Selected Banks</h3>
			</div>
			<!-- <button id="add-bank-modal" type="button" class="mdl-button mdl-button--raised">Adds Bank</button>
			<div class="mdl-textfield mdl-js-textfield mdl-textfield--expandable is-upgraded is-focused">
				<label class="mdl-button mdl-js-button mdl-button--icon" for="filterBanksElement">
					<i class="material-icons">search</i>
				</label>
				<div class="mdl-textfield__expandable-holder">
					<input class="mdl-textfield__input search" type="text" id="filterBanksElement">
					<label class="mdl-textfield__label" for="sample-expandable">Expandable Input</label>
				</div>
			</div> -->

			<table id='mdl-table' class="mdl-data-table mdl-js-data-table mdl-shadow--2dp">
				@if (count($selectedBanks))
				<thead>
					<tr>
						<th class="mdl-data-table__cell--non-numeric sort" data-sort="bank">Bank</th>
						<th data-sort="resources">Available resources</th>
						<th data-sort="resources">Usage(%)</th>
						<th data-sort="connections">Available connections</th>
						<th>Actions</th>
					</tr>
				</thead>
				@endif
				<tbody class="list">

				@forelse ($selectedBanks as $selectedBank)

				<tr>
					<td class="mdl-data-table__cell--non-numeric bank">{{ substr($selectedBank->bank_name, 0, 30) }}</td>
					<td class="amount">{{ '$'.number_format($selectedBank->max_amount - $selectedBank->used_amount, 2) }}</td>
					<td class="amount">{{ number_format($selectedBank->used_amount/(($selectedBank->max_amount>0)?$selectedBank->max_amount:1), 2).'%' }}</td>
					<td class="connections">{{ $selectedBank->max_connections - $selectedBank->used_connections }}</td>
					<td>
					<button 
						class="buttom-sm mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect mdl-button--accent"
						onclick="javascript:{ removeSimulationBank({{ $simulation->id }}, {{ $selectedBank->bank_id }}); }">
						Remove
					</button>
					</td>
				</tr>

				@empty
					<p style="margin-left:20px;">No banks selected.</p>
				@endforelse
				</tbody>
			</table>

		</div>				
		<br>
		<br>

<!-- TABLE: BANKS USED IN THIS SIMULATION -->

		<div id="mdl-available-tables">
			<div class="mdl-card__title">
				<h3 class="mdl-card__title-text">Available Banks</h3>
			</div>
			<div class="mdl-textfield mdl-js-textfield mdl-textfield--expandable is-upgraded is-focused">
				<label class="mdl-button mdl-js-button mdl-button--icon" for="filterBanksElement">
					<i class="material-icons">search</i>
				</label>
				<div class="mdl-textfield__expandable-holder">
					<input class="mdl-textfield__input search" type="text" id="filterBanksElement">
					<label class="mdl-textfield__label" for="sample-expandable">(input)</label>
				</div>
			</div>
			<table id='mdl-available-tables' class="mdl-data-table mdl-js-data-table mdl-shadow--2dp">
				<thead>
					<tr>
						<th class="mdl-data-table__cell--non-numeric sort" data-sort="bank">Bank</th>
						<th data-sort="resources">Available resources</th>
						<th data-sort="resources">Usage(%)</th>
						<th data-sort="connections">Available connections</th>

						<!-- <th class="mdl-data-table__cell--non-numeric sort" data-sort="bank">Bank</th>
						<th data-sort="resources">Total resources</th>
						<th data-sort="connections">Total connections</th> -->
						<th>Actions</th>
					</tr>
				</thead>
				<tbody class="list">

				@forelse ($availableBanks as $availableBank)

					<tr>
						<td class="mdl-data-table__cell--non-numeric bank">{{ substr($availableBank->bank_name, 0, 30) }}</td>
						<td class="amount">{{ '$'.number_format($availableBank->max_amount - $availableBank->used_amount, 2) }}</td>
						<td class="amount">{{ number_format($availableBank->used_amount / (($availableBank->max_amount>0)?$availableBank->max_amount:1), 2).'%' }}</td>
						<td class="connections">{{ $availableBank->max_connections }}</td>
						<td>
						<button 
							class="buttom-sm mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect mdl-button--accent"
							onclick="javascript:{ addSimulationBank({{ $simulation->id }}, {{ $availableBank->bank_id }}); }">
							Select
						</button>
						</td>
					</tr>

				@empty
					<p>No banks found.</p>
				@endforelse
				</tbody>
			</table>
			{!! str_replace('/?', '/simulation?', $availableBanks->appends(['noupdate'=>'on', 'simulation'=>$simulation->id])->render()) !!}
			<?php
			/*
			{!! str_replace('/?', '/simulation?', $availableBanks->appends($_GET)->render()) !!}
			{{ $availableBanks->appends($_GET)->links() }}
			*/
			?>

			<form id="simulation-bank" action="/simulation/setup" method="POST">
				{{ csrf_field() }}
				<input type="hidden" id="simulation" name="simulation" value="{{ $simulation->id }}">
				<input type="hidden" id="simulation_bank-id" name="simulation_bank-id">
				<input type="hidden" id="bank-id" name="bank-id">
				<input type="hidden" id="operation" name="operation">
				<input type="hidden" name="noupdate" value="on">
			</form>
		</div>
	</div>
</div>
@endsection

@section('script')
<script defer>
	jQuery( document ).ready(function() {

		// Filtering options
		var options = {
			valueNames: ['bank', 'city', 'amount', 'connections']
		}, 
		documentTable = new List('mdl-available-tables', options);

		$('input.search').on('keyup', function (e) {
			if (e.keyCode === 27) {
				$(e.currentTarget).val('');
				documentTable.search('');
			}
		});	

		// Setup input fields
		var passValidation = function(input){
			var amount = $("input#exchange_amount")[0].value;
			var isAmountNumeric = (!isNaN(parseFloat(amount)) && isFinite(amount));
			filled =
				$("input#description")[0].value &&
				// $("input#max_connections")[0].value &&
				// $("input#total_amount")[0].value &&
				$("input#exchange_amount")[0].value;
			return filled && isAmountNumeric;
		};

		var onChange = function(input){
			$('button#save-button').prop('disabled', !passValidation());
		};

		$('button#save-button').on('click', function () {

			var formSetup = $("form#simulation-setup");
			if (passedValidation()){
				formSetup.submit();
			}
			return false;
		});	

		$('button#finish-button').on('click', () => {		
			document.location = "/simulations";
		});
		
		$('input').on('keyup', (input) => {
			onChange(input);
		});

	});

	function addSimulationBank(simulation, bank) {
	
		// $.ajax({
		// 	url: '/simulation/setup',
		// 	type: 'POST',
		// 	contentType: 'application/json',
		// 	headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
		// 	data: 
		// 		JSON.stringify({
		// 			'operation': 'add',
		// 			'noupdate' : 'on',
		// 			'simulation': simulation,
		// 			'bank-id': bank,
		// 		}),
		// 	error: function(reject) {
		// 		$('#snack').html('<p>An error has occurred</p>');
		// 	},
		// 	dataType: 'json',
		// 	success: function(data) {
		// 		location.reload(true);
		// 	},
		// });

		$("form#simulation-bank input#operation")[0].value = 'add';
		$("form#simulation-bank input#bank-id")[0].value = bank;
		$("form#simulation-bank").submit();
	}

	function removeSimulationBank(simulation, bank) {

		// $.ajax({
		// 	url: '/simulation/setup',
		// 	type: 'POST',
		// 	contentType: 'application/json',
		// 	headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
		// 	data: 
		// 		JSON.stringify({
		// 			'operation': 'remove',
		// 			'noupdate' : 'on',
		// 			'simulation': simulation,
		// 			'simulation_bank-id': bank,
		// 		}),
		// 	error: function(reject) {
		// 		$('#snack').html('<p>An error has occurred</p>');
		// 	},
		// 	dataType: 'json',
		// 	success: function(data) {
		// 		location.reload(true);
		// 	},
		// });
	
		$("form#simulation-bank input#operation")[0].value = 'remove';
		$("form#simulation-bank input#simulation_bank-id")[0].value = bank;
		$("form#simulation-bank").submit();
	}

</script>
@endsection