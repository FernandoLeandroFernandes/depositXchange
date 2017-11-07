@extends('layouts.app')

@section('content')
<div class="mdl-card">
	<div class="mdl-card__title">
		<h2 class="mdl-card__title-text">Exchanges</h2>
	</div>
	<div class="mdl-card__actions mdl-card--border" style="width:auto;">
		<div id="mdl-table">
			<div class="mdl-textfield mdl-js-textfield mdl-textfield--expandable is-upgraded is-focused">
				<label class="mdl-button mdl-js-button mdl-button--icon" for="filterBanksElement">
					<i class="material-icons">search</i>
				</label>
				<div class="mdl-textfield__expandable-holder">
					<input class="mdl-textfield__input search" type="text" id="filterBanksElement">
					<label class="mdl-textfield__label" for="sample-expandable">Expandable Input</label>
				</div>
			</div>

			<table id='mdl-table' class="mdl-data-table mdl-js-data-table mdl-shadow--2dp">
				<thead>
					<tr>
						<th class="mdl-data-table__cell--non-numeric sort" data-sort="simulation">Simulation</th>
						<th class="mdl-data-table__cell--non-numeric sort" data-sort="origin">Origin</th>
						<th class="sort" data-sort="destination">Destination</th>
						<th class="sort" data-sort="amount">Amount</th>
						<!-- <th>ACTIONS</th> -->
					</tr>
				</thead>
				<tbody class="list">

				@forelse ($exchanges as $exchange)

					<tr>
						<td class="mdl-data-table__cell--non-numeric origin">{{ $exchange->simulation->description }}</td>
						<td class="mdl-data-table__cell--non-numeric origin">{{ $exchange->origin->name }}</td>
						<td class="destination">{{ $exchange->destination->name }}</td>
						<td class="amount">{{ '$'.$exchange->amount }}</td>
						<!-- <td>
							<button class="buttom-sm mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect mdl-button--accent">Details</button>
						</td> -->
					</tr>

				@empty
					<p>No exchanges found.</p>

				@endforelse
				</tbody>
				<!-- <tfoot>
					<td class="mdl-data-table__cell--non-numeric" colspan="3"></td>
					<td style="padding-bottom: 1em;">
					<button class="mdl-button mdl-js-button mdl-button--primary mdl-js-ripple-effect">
						REFRESH
					</button>
					</td>
				</tfoot> -->
			</table>
			{{ $exchanges->links() }}

	<!-- <div class="mdl-layout mdl-js-layout">
		<main class="mdl-layout__content">
			<div class="page-content" style="padding: 16px;">
			<div class="mdl-paging"><span class="mdl-paging__per-page"><span class="mdl-paging__per-page-label">Results per page</span><span class="mdl-paging__per-page-value">10</span>
				<button id="HkhZcTBbWFADje7t2" class="mdl-button mdl-js-button mdl-js-ripple-effect mdl-button--icon mdl-paging__per-page-dropdown"><i class="material-icons">arrow_drop_down</i>
				</button>
				<ul for="HkhZcTBbWFADje7t2" class="mdl-menu mdl-menu--bottom-right mdl-js-menu mdl-js-ripple-effect mdl-js-ripple-effect--ignore-events">
				<li tabindex="-1" data-value="10" class="mdl-menu__item mdl-js-ripple-effect">10</span>
				</li>
				<li tabindex="-1" data-value="20" class="mdl-menu__item mdl-js-ripple-effect">20</span>
				</li>
				<li tabindex="-1" data-value="30" class="mdl-menu__item mdl-js-ripple-effect">30</span>
				</li>
				<li tabindex="-1" data-value="40" class="mdl-menu__item mdl-js-ripple-effect">40</span>
				</li>
				<li tabindex="-1" data-value="50" class="mdl-menu__item mdl-js-ripple-effect">50</span>
				</li>
				</ul>
				</span><span class="mdl-paging__count">11-20 de 25</span>
				<button class="mdl-button mdl-js-button mdl-js-ripple-effect mdl-button--icon mdl-paging__prev"><i class="material-icons">keyboard_arrow_left</i>
				</button>
				<button class="mdl-button mdl-js-button mdl-js-ripple-effect mdl-button--icon mdl-paging__next"><i class="material-icons">keyboard_arrow_right</i>
				</button>
			</div>
			</div>
		</main>
	</div> -->
<!-- </div>
</div>
</div>
</div> -->

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