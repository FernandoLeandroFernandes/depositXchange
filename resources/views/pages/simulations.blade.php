@extends('layouts.app')

@section('content')
<div class="mdl-card">
	<div class="mdl-card__title">
		<h3 class="mdl-card__title-text">Simulations</h3>
	</div>
	<div class="mdl-card__actions mdl-card--border">
		<div id="mdl-table">
			<button class="mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect mdl-button--accent" onclick="document.location = '/simulation/setup'">New Simulation</button>	
			<div class="mdl-textfield mdl-js-textfield mdl-textfield--expandable is-upgraded is-focused">
				<label class="mdl-button mdl-js-button mdl-button--icon" for="filterSimulationsElement">
					<i class="material-icons">search</i>
				</label>
				<div class="mdl-textfield__expandable-holder">
					<input class="mdl-textfield__input search" type="text" id="filterSimulationsElement">
					<label class="mdl-textfield__label" for="sample-expandable">Expandable Input</label>
				</div>
			</div>

			<table id='mdl-table' class="mdl-data-table mdl-js-data-table mdl-data-table--selectable mdl-shadow--2dp">
				<thead>
					<tr>
					<th class="mdl-data-table__cell--non-numeric sort" data-sort="description">Simulation</th>
					<th class="sort" data-sort="max_connections">Connections (Bank)</th>
					<th class="sort" data-sort="total_amount">Amount (Bank)</th>
					<th class="sort" data-sort="exchanged_amount">Amount (Exchange)</th>
					<th>Action</th>
					</tr>
				</thead>
				<tbody class="list">

				@forelse ($simulations as $simulation)

					<tr>
						<td class="mdl-data-table__cell--non-numeric simulation">{{ $simulation->description }}</td>
						<td class="max_connections">{{ $simulation->max_connections }}</td>
						<td class="total_amount">{{ '$'.money_format('%i', $simulation->total_amount) }}</td>
						<td class="exchanged_amount">{{ '$'.money_format('%i', $simulation->exchanged_amount) }}</td>
						<td>
							<button class="mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect mdl-button--accent">Consolidate</button>	
						</td>
					</tr>

				@empty
					<p>No simulations found.</p>

				@endforelse
				</tbody>
				<!-- <tfoot>
				<td class="mdl-data-table__cell--non-numeric" colspan="4">
				...
				</td>
				<td style="padding-bottom: 1em;">
					<button class="mdl-button mdl-js-button mdl-button--primary mdl-js-ripple-effect">
						REFRESH
					</button>
				</td>
			</tfoot> -->
		</table>

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
		<dialog class="mdl-dialog" id="modal-edit-bank">
			<div class="mdl-dialog__content">
				<p>
					This is an example of the MDL Dialog being used as a modal.
					It is using the full width action design intended for use with buttons
					that do not fit within the specified <a href="https://www.google.com/design/spec/components/dialogs.html#dialogs-specs">length metrics</a>.
					
					<form action="#">
						<div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
							<input class="mdl-textfield__input" type="text" id="description">
							<label class="mdl-textfield__label" for="description">Description</label>
						</div>
						<div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
							<input class="mdl-textfield__input" type="text" pattern="-?[0-9]*(\.[0-9]+)?" id="maximum_connections">
							<label class="mdl-textfield__label" for="maximum_connections">Maximum Connections per Bank</label>
							<span class="mdl-textfield__error">Input is not a number!</span>
						</div>
						<div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
							<input class="mdl-textfield__input" type="text" pattern="-?[0-9]*(\.[0-9]+)?" id="maximum_connections">
							<label class="mdl-textfield__label" for="maximum_connections">Maximum Connections per Bank</label>
							<span class="mdl-textfield__error">Input is not a number!</span>
						</div>
						<div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
							<input class="mdl-textfield__input" type="text" pattern="-?[0-9]*(\.[0-9]+)?" id="maximum_connections">
							<label class="mdl-textfield__label" for="maximum_connections">Maximum Connections per Bank</label>
							<span class="mdl-textfield__error">Input is not a number!</span>
						</div>
					</form>						
				</p>
			</div>
			<div class="mdl-dialog__actions mdl-dialog__actions">
				<button type="button" class="mdl-button">Close</button>
				<button type="button" class="mdl-button" disabled>Save</button>
			</div>
		</dialog>
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