@extends('layouts.app')

@section('content')
<div class="mdl-card">
	<div class="mdl-card__title">
		<h3 class="mdl-card__title-text">Banks</h3>
		<!-- <h2>Banks</h2> -->
	</div>
	<div class="mdl-card__actions mdl-card--border">
		<div id="mdl-table">
			<button id="add-bank-modal" type="button" class="mdl-button mdl-button--raised">Add Bank</button>
			<div class="mdl-textfield mdl-js-textfield mdl-textfield--expandable is-upgraded is-focused">
				<label class="mdl-button mdl-js-button mdl-button--icon" for="filterBanksElement">
					<i class="material-icons">search</i>
				</label>
				<div class="mdl-textfield__expandable-holder">
					<input class="mdl-textfield__input search" type="text" id="filterBanksElement">
					<label class="mdl-textfield__label" for="sample-expandable">Expandable Input</label>
				</div>
			</div>

			<table id='mdl-table' class="mdl-data-table mdl-js-data-table mdl-data-table--selectable mdl-shadow--2dp">
				<thead>
					<tr>
						<th class="mdl-data-table__cell--non-numeric sort" data-sort="bank">Bank</th>
						<th class="mdl-data-table__cell--non-numeric sort" data-sort="city">City</th>
						<th data-sort="amount">Amount</th>
						<th data-sort="connections">Connections</th>
						<th>Actions</th>
					</tr>
				</thead>
				<tbody class="list">

				@forelse ($banks as $bank)

					<tr>
						<td class="mdl-data-table__cell--non-numeric bank">{{ substr($bank->name, 0, 30) }}</td>
						<td class="mdl-data-table__cell--non-numeric city">{{ $bank->city }}</td>
						<td class="amount">{{ '$'.money_format('%i', $bank->max_amount) }}</td>
						<td class="connections">{{ $bank->max_connections }}</td>
						<td>
						<button 
							class="buttom-sm mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect mdl-button--accent"
							onclick="javascript:{ editBank({{ $bank->id }}, '{{ $bank->name }}', '{{ $bank->city }}', {{ $bank->max_amount }}, {{ $bank->max_connections }}); }">
							Edit
						</button>
						</td>
					</tr>

				@empty
					<p>No banks found.</p>

				@endforelse
				</tbody>
				<!-- <tfoot>
					<td class="mdl-data-table__cell--non-numeric" colspan="3">
					this ist my footer
					</td>
					<td style="padding-bottom: 1em;">
					<button class="mdl-button mdl-js-button mdl-button--primary mdl-js-ripple-effect">
						Button
					</button>
					</td>
				</tfoot> -->
			</table>
			{{ $banks->links() }}

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

		<dialog class="mdl-dialog" id="bank-modal-dialog">
			<div class="mdl-dialog__content">
				<h3 id="title">Bank</h3>
				<div class="mdl-grid">
					<div class="mdl-layout-spacer"></div>
						<div class="mdl-cell mdl-cell--12-col">					<!-- <div class="mdl-layout-spacer"></div> -->
						
							<form id="bankForm" action="#">
								<input type="hidden" id="id" value="">
								<div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
								<input class="mdl-textfield__input" type="text" id="bank">
									<label class="mdl-textfield__label" for="bank">Bank</label>
								</div>
								<div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
									<input class="mdl-textfield__input" type="text" id="city">
									<label class="mdl-textfield__label" for="city">City</label>
								</div>
								<div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
									<input class="mdl-textfield__input" type="text" pattern="-?[0-9]*(\.[0-9]+)?" id="amount">
									<label class="mdl-textfield__label" for="amount">Resources Amount</label>
									<span class="mdl-textfield__error">Input is not a number!</span>
								</div>
								<div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
									<input class="mdl-textfield__input" type="text" pattern="-?[0-9]*(\.[0-9]+)?" id="connections">
									<label class="mdl-textfield__label" for="connections">Maximum Connections</label>
									<span class="mdl-textfield__error">Input is not a number!</span>
								</div>
							</form>	

						</div>
					</div>
				</div>
				<div class="mdl-dialog__actions mdl-dialog__actions">
					<button id="cancel-button" type="button" class="mdl-button">Cancel</button>
					<button id="save-button" type="button" class="mdl-button">Save</button>
				</div>
			</div>
		</dialog>
	</div>
</div>
@endsection

@section('script')
<script>
	jQuery( document ).ready(function() {
		var options = {
			valueNames: ['bank', 'city', 'amount', 'connections']
		}, 
		documentTable = new List('mdl-table', options);

		$('input.search').on('keyup', function (e) {
			if (e.keyCode === 27) {
				$(e.currentTarget).val('');
				documentTable.search('');
			}
		});	

		//MDL Text Input Cleanup
		document.checkMaterialTextFields = function(){
			var mdlInputs = this.querySelectorAll('.mdl-js-textfield');
			for (var i = 0, l = mdlInputs.length; i < l; i++) {
				mdlInputs[i].MaterialTextfield.checkDirty();
			}  
		}		
	});

	(function() {
        'use strict';
        var dialog = document.querySelector('#bank-modal-dialog');
	
		var buttonSave = dialog.querySelector('#save-button');
        var buttonCancel = dialog.querySelector('#cancel-button');
        var buttonAddBank = document.querySelector('#add-bank-modal');
		
		if (! dialog.showModal) {
            dialogPolyfill.registerDialog(dialog);
		}
		var saveDialogHandler = function(event) {

			$.ajax({
				url: '/bank/save',
				type: 'POST',
				contentType: 'application/json',
				headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
				data: 
					JSON.stringify({
						'id': $("#id")[0].value,
						'name': $("#bank")[0].value,
						'city': $("#city")[0].value,
						'amount': $("#amount")[0].value,
						'connections': $("#connections")[0].value,
					}),
				error: function(reject) {
					$('#snack').html('<p>An error has occurred</p>');
				},
				dataType: 'json',
				success: function(data) {
					location.reload();
				},
			});			
		};
        var closeDialogHandler = function(event) {
            dialog.close();
        };
        var addBankHandler = function(event) {
            dialog.showModal();
        };
        buttonAddBank.addEventListener('click', addBankHandler);
        buttonSave.addEventListener('click', saveDialogHandler);
		buttonCancel.addEventListener('click', closeDialogHandler);
		
	}());	
	
	function editBank(id, name, city, amount, connections) {

		$("#id")[0].value = id;
		$("#bank")[0].value = name;
		$("#city")[0].value = city;
		$("#amount")[0].value = amount;
		$("#connections")[0].value = connections;
		
		document.checkMaterialTextFields();

		document.querySelector('#bank-modal-dialog').showModal();
	}
</script>
@endsection