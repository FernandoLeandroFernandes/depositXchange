@extends('layouts.app')

@section('content')
<div class="mdl-card-simulations">
	<div class="mdl-card__title">
		<h3 class="mdl-card__title-text">Simulations</h3>
	</div>
	<div class="mdl-card__actions mdl-card--border">
		<div id="mdl-table">
			<button class="mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect mdl-button--accent" 
					onclick="document.location = '/simulation/new'">New Simulation</button>	
			<div class="mdl-textfield mdl-js-textfield mdl-textfield--expandable is-upgraded is-focused">
				<label class="mdl-button mdl-js-button mdl-button--icon" for="filterSimulationsElement">
					<i class="material-icons">search</i>
				</label>
				<div class="mdl-textfield__expandable-holder">
					<input class="mdl-textfield__input search" type="text" id="filterSimulationsElement">
					<label class="mdl-textfield__label" for="sample-expandable">Expandable Input</label>
				</div>
			</div>

			<table id='mdl-table' class="mdl-data-table mdl-js-data-table mdl-shadow--2dp">
				<thead>
					<tr>
					<th class="mdl-data-table__cell--non-numeric sort" data-sort="description">Simulation</th>
					<th class="sort" data-sort="exchanged_amount">Amount (Exchange)</th>
					<th class="sort" data-sort="used_amount">Amount (Simulation)</th>
					<th class="sort" data-sort="used_connections">Connections</th>
					<th class="sort" data-sort="exchanged_amount">Status</th>
					<th></th>
					<th>Action</th>
					</tr>
				</thead>
				<tbody class="list">

				@forelse ($simulations as $simulation)

					<tr>
						<td class="mdl-data-table__cell--non-numeric simulation">{{ $simulation->description }}</td>
						<td class="exchanged_amount">{{ '$'.number_format($simulation->exchange_amount, 2) }}</td>
						<td class="used_amount">{{ '$'.number_format($simulation->used_amount, 2) }}</td>
						<td class="used_connections">{{ $simulation->used_connections }}</td>
						<td class="consolidated">{{ $simulation->status() }}</td>
						<td>
						<button 
							id="edit-simulation" 
							class="mdl-button mdl-js-button mdl-button--colored"
							onclick="javascript:{ document.location = '/simulation/{{($simulation->isConsolidated())?'results':'setup'}}?noupdate=on&simulation={{ $simulation->id }}'; }">
							<i class="material-icons">search</i>
					  	</button>	
						<button 
							id="delete-simulation" 
							class="mdl-button mdl-js-button mdl-button--colored"
							onclick="javascript:{ document.location = '/simulation/delete?simulation={{ $simulation->id }}'; }"
							@if ($simulation->isConsolidated()) disabled @endif>
							<i class="material-icons">delete</i>
					  	</button>	
						</td>
						<td>
							@if ($simulation->isNew())
							<button 
								class="mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect mdl-button--accent"
								onclick="javascript: { document.location = '/simulation/run?simulation={{$simulation->id}}' }"
								@if (!$simulation->hasSimulationBanks()) disabled @endif>Simulate</button>
							@elseif ($simulation->isSimulated())
							<button 
								class="mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect mdl-button--accent"
								onclick="javascript: { document.location = '/simulation/run?simulation={{$simulation->id}}' }"
								@if (!$simulation->hasSimulationBanks()) disabled @endif>Resimulate</button>
							<button 
								class="mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect mdl-button--accent"
								onclick="javascript: { document.location = '/simulation/consolidate?simulation={{$simulation->id}}' }">Consolidate</button>
							@elseif ($simulation->isConsolidated())
							<button 
								class="mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect mdl-button--accent"
								onclick="javascript: { document.location = '/simulation/revert?simulation={{$simulation->id}}' }">Revert</button>
							@endif
						</td>
					</tr>

				@empty
					<p>No simulations found.</p>

				@endforelse
				</tbody>
			</table>
			{{ $simulations->links() }}
		</div>
	</div>
</div>
@endsection

@section('script')
<script defer>
	jQuery( document ).ready(function() {
		var options = {
			valueNames: ['description', 'max_connections', 'exchanged_amount', 'total_amount']
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