@extends('layouts.app')

@section('content')
<div class="mdl-card-bank-details">
	<div class="mdl-dialog__actions mdl-dialog__actions">
		<button 
			type="button" 
			class="mdl-button"
			onclick="javascript:{ document.location = '/banks'; }">Back to Banks</button>
	</div>
	<div class="mdl-card__title">
		<h2 class="mdl-card__title-text"><strong>{{ $bank->name }}</strong></h2>
	</div>
	<div class="mdl-card__title">
		<h2 class="mdl-card__title-text">Details</h2>
	</div>
	<div class="mdl-card__actions mdl-card--border" style="width:auto;">
		<div id="mdl-table">
			<table id='mdl-table' class="mdl-data-table mdl-js-data-table mdl-shadow--2dp">
				<thead>
					<tr>
						<th>City</th>
						<th>Total resources</th>
						<th>Available resources</th>
						<th>Usage(%)</th>
						<th>Max. Connections</th>
						<th>Used Connections</th>
					</tr>
				</thead>

				<tbody class="list">
					<tr>
						<td class="mdl-data-table__cell--non-numeric city">{{ $bank->city }}</td>
						<td class="amount">{{ '$'.number_format($bank->max_amount, 2) }}</td>
						<td class="amount">{{ '$'.number_format($bank->max_amount-$bank->used_amount, 2) }}</td>
						<td class="amount">{{ number_format($bank->used_amount/(($bank->max_amount>0)?$bank->max_amount:1), 2).'%' }}</td>
						<td class="connections">{{ $bank->max_connections }}</td>
						<td class="connections">{{ $bank->used_connections }}</td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>


	<div class="mdl-card__title">
		<h2 class="mdl-card__title-text">Exchanges</h2>
	</div>
	<div class="mdl-card__actions mdl-card--border" style="width:auto;">
		<div id="mdl-table">
			<table id='mdl-table' class="mdl-data-table mdl-js-data-table mdl-shadow--2dp">
				@if (count($exchanges))
				<thead>
					<tr>
						<th class="mdl-data-table__cell--non-numeric sort" data-sort="simulation">Simulation</th>
						<th class="mdl-data-table__cell--non-numeric sort" data-sort="origin">Origin</th>
						<th class="sort" data-sort="destination">Destination</th>
						<th class="sort" data-sort="amount">Amount</th>
					</tr>
				</thead>
				@endif

				<tbody class="list">
				@forelse ($exchanges as $exchange)
					<tr>
						<td class="mdl-data-table__cell--non-numeric origin">{{ $exchange->simulation }}</td>
						<td class="mdl-data-table__cell--non-numeric origin">{{ $exchange->origin_name }}</td>
						<td class="destination">{{ $exchange->destination_name }}</td>
						<td>{{ '$'.number_format($exchange->amount, 2) }}</td>
					</tr>
				@empty
					<p>No <strong>consolidated</strong> exchanges found.</p>
				@endforelse
				</tbody>
			</table>
			<?php
			//{{ $exchanges->links() }}
			?>
		</div>
	</div>
	<div class="mdl-dialog__actions mdl-dialog__actions">
		<button 
			type="button" 
			class="mdl-button"
			onclick="javascript:{ document.location = '/banks'; }">Back to Banks</button>
	</div>	
</div>
@endsection