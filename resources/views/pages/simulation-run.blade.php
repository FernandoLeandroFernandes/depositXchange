@extends('layouts.app')

@section('content')
<div class="mdl-card">
	<div class="mdl-card__title">
		<h3 class="mdl-card__title-text">Simulation Results</h3>
	</div>
	<div class="mdl-card__actions mdl-card--border">
		<h4>Description: {{ $simulation->description }}</h4>
		<h4>Amount resourses: {{ '$'.number_format($simulation->total_amount, 2) }}</h4>
		<h4>Exchange amount: {{ '$'.number_format($simulation->exchange_amount, 2) }}</h4>
		<h4>Maximum connections: {{ $simulation->max_connections }}</h4>
		<h4>Status: {{ $simulation->status() }}</h4>
	</div>
	<div class="mdl-card__actions mdl-card--border">
		<div id="mdl-table">
			<div class="mdl-card__title">
				<h3 class="mdl-card__title-text">Exchanges</h3>
			</div>

			<table id='mdl-table' class="mdl-data-table mdl-js-data-table mdl-shadow--2dp">
				@if (count($simulationBanks) > 0)
				<thead>
					<tr>
						<th class="mdl-data-table__cell--non-numeric sort" data-sort="bank">Bank</th>
						<th data-sort="resources">Used resources</th>
						<th data-sort="connections">Used connections</th>
					</tr>
				</thead>
				@endif
				<tbody class="list">

				@forelse ($simulationBanks as $simulationBank)

				<tr>
					<td class="mdl-data-table__cell--non-numeric bank">{{ substr($simulationBank->bank_name, 0, 30) }}</td>
					<td class="amount">{{ '$'.number_format($simulationBank->used_amount, 2) }}</td>
					<td class="connections">{{ $simulationBank->used_connections }}</td>
				</tr>

				@empty
					<p style="margin-left:20px;">No banks in this simulation.</p>
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