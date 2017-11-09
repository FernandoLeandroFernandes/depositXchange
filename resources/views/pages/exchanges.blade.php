@extends('layouts.app')

@section('content')
<div class="mdl-card-exchanges">
	<div class="mdl-card__title">
		<h2 class="mdl-card__title-text">Exchanges</h2>
	</div>
	<div class="mdl-card__actions mdl-card--border" style="width:auto;">
		<div id="mdl-table">
			@if (count($exchanges))
			<div class="mdl-textfield mdl-js-textfield mdl-textfield--expandable is-upgraded is-focused">
				<label class="mdl-button mdl-js-button mdl-button--icon" for="filterBanksElement">
					<i class="material-icons">search</i>
				</label>
				<div class="mdl-textfield__expandable-holder">
					<input class="mdl-textfield__input search" type="text" id="filterBanksElement">
					<label class="mdl-textfield__label" for="sample-expandable">Expandable Input</label>
				</div>
			</div>
			@endif
			<table id='mdl-table' class="mdl-data-table mdl-js-data-table mdl-shadow--2dp">
				@if (count($exchanges))
				<thead>
					<tr>
						<th class="mdl-data-table__cell--non-numeric sort" data-sort="simulation">Simulation</th>
						<th class="mdl-data-table__cell--non-numeric sort" data-sort="origin">Origin</th>
						<th class="sort" data-sort="destination">Destination</th>
						<th class="sort" data-sort="amount">Amount</th>
						<!-- <th>ACTIONS</th> -->
					</tr>
				</thead>
				@endif

				<tbody class="list">
				@forelse ($exchanges as $exchange)
					<tr>
						<td class="mdl-data-table__cell--non-numeric origin">{{ $exchange->simulation->description }}</td>
						<td class="mdl-data-table__cell--non-numeric origin">{{ $exchange->origin->name }}</td>
						<td class="destination">{{ $exchange->destination->name }}</td>
						<td>{{ '$'.number_format($exchange->amount, 2) }}</td>
					</tr>
				@empty
					<p>No <strong>consolidated</strong> exchanges found.</p>
				@endforelse
				</tbody>
			</table>
			{{ $exchanges->links() }}
		</div>
	</div>
</div>
@endsection

@section('script')
<script defer>
	jQuery( document ).ready(function() {
		var options = {
			valueNames: ['simulation', 'origin', 'destination', 'amount']
		}, 
		documentTable = new List('mdl-table', options);

		$('input.search').on('keyup', function (e) {
			if (e.keyCode === 27) {
				$(e.currentTarget).val('');
				documentTable.search('');
			}
		});	
	});
</script>
@endsection