@extends('layouts.app')

@section('content')
<div class="mdl-card-simulation-results">
	<div class="mdl-dialog__actions mdl-dialog__actions">
		<button id="back-button" type="button" class="mdl-button" onclick="javascript: { document.location = '/simulations'; }">Back to Simulations</button>
	</div>
	<div class="mdl-card__title">
		<h3 class="mdl-card__title-text">Simulation Results</h3>
	</div>
	<div class="mdl-card__actions mdl-card--border">
		<div id="mdl-table">

			<div class="mdl-card__title">
				<h3 class="mdl-card__title-text">Simulation</h3>
			</div>
			<table id='mdl-table' class="mdl-data-table mdl-js-data-table mdl-shadow--2dp">
				<thead>
					<tr>
						<th class="mdl-data-table__cell--non-numeric">Description</th>
						<th data-sort="resources">Exchange amount</th>
						<th data-sort="resources">Used resources</th>
						<th data-sort="connections">Used connections</th>
					</tr>
				</thead>
				<tbody class="list">
					<tr>
						<td class="mdl-data-table__cell--non-numeric">{{ $simulation->description }}</td>
						<td>{{ '$'.number_format($simulation->exchange_amount, 2) }}</td>
						<td>{{ '$'.number_format($simulation->used_amount, 2) }}</td>
						<td>{{ $simulation->used_connections }}</td>
					</tr>
				</tbody>
			</table>
			<br>
			<div class="mdl-card__title">
				<h3 class="mdl-card__title-text">Simulation Banks Status</h3>
			</div>
			<table id='mdl-table' class="mdl-data-table mdl-js-data-table mdl-shadow--2dp">
				@if (count($simulationBanks) > 0)
				<thead>
					<tr>
						<th class="mdl-data-table__cell--non-numeric sort" data-sort="bank">Bank</th>
						<th data-sort="resources">Total  resources</th>
						<th data-sort="resources">Used resources</th>
						<th data-sort="connections">Total connections</th>
						<th data-sort="connections">Used connections</th>
					</tr>
				</thead>
				@endif
				<tbody class="list">

				@forelse ($simulationBanks as $simulationBank)

				<tr>
					<td class="mdl-data-table__cell--non-numeric bank">{{ substr($simulationBank->bank_name, 0, 30) }}</td>
					<td class="amount">{{ '$'.number_format($simulationBank->max_amount, 2) }}</td>
					<td class="amount">{{ '$'.number_format($simulationBank->used_amount, 2) }}</td>
					<td class="connections">{{ $simulationBank->max_connections }}</td>
					<td class="connections">{{ $simulationBank->used_connections }}</td>
				</tr>

				@empty
					<p style="margin-left:20px;">No banks in this simulation.</p>
				@endforelse
				</tbody>
			</table>
			<br>
			<div class="mdl-card__title">
				<h3 class="mdl-card__title-text">Simulation Exchanges</h3>
			</div>
			<table id='mdl-table' class="mdl-data-table mdl-js-data-table mdl-shadow--2dp">
				@if (!$exchanges->isEmpty())
				<thead>
					<tr>
						<!-- <th class="mdl-data-table__cell--non-numeric sort" data-sort="simulation">Simulation</th> -->
						<th class="mdl-data-table__cell--non-numeric sort" data-sort="origin">Origin</th>
						<th class="sort" data-sort="destination">Destination</th>
						<th class="sort" data-sort="amount">Amount</th>
					</tr>
				</thead>
				@endif

				<tbody class="list">
				@forelse ($exchanges as $exchange)
					<tr>
						<!-- <td class="mdl-data-table__cell--non-numeric origin">{{ $exchange->simulation->description }}</td> -->
						<td class="mdl-data-table__cell--non-numeric origin">{{ $exchange->origin->name }}</td>
						<td class="destination">{{ $exchange->destination->name }}</td>
						<!-- <td class="amount">{{ '$'.$exchange->amount }}</td> -->
						<td>{{ '$'.number_format($exchange->amount, 2) }}</td>
					</tr>
				@empty
					<p>No exchanges found.</p>
				@endforelse
				</tbody>
			</table>

			<!-- <form id="simulation-bank" action="/simulation/setup" method="POST">
				{{ csrf_field() }}
				<input type="hidden" id="simulation" name="simulation" value="{{ $simulation->id }}">
				<input type="hidden" id="simulation_bank-id" name="simulation_bank-id">
				<input type="hidden" id="bank-id" name="bank-id">
				<input type="hidden" id="operation" name="operation">
				<input type="hidden" name="noupdate" value="on">
			</form> -->
		</div>
	</div>
	<br>
	<div class="mdl-dialog__actions mdl-dialog__actions">
		<button id="back-button" type="button" class="mdl-button" onclick="javascript: { document.location = '/simulations'; }">Back to Simulations</button>
	</div>	
</div>
@endsection

@section('script')
<script defer>
	jQuery( document ).ready(function() {

		// Filtering options
		var options = {
			valueNames: ['bank']
		}, 
		documentTable = new List('mdl-available-tables', options);

		$('input.search').on('keyup', function (e) {
			if (e.keyCode === 27) {
				$(e.currentTarget).val('');
				documentTable.search('');
			}
		});	

		$('button#finish-button').on('click', () => {		
			document.location = "/simulations";
		});
		
	});

</script>
@endsection