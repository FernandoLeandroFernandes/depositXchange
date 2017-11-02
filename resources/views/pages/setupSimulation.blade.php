@extends('layouts.app')

@section('content')
<div class="mdl-card">
	<div class="mdl-card__title">
		<h3 class="mdl-card__title-text">Setup Simulation</h3>
	</div>
	<div class="mdl-card__actions mdl-card--border">
		<form id="simulation-setup" action="/simulation/setup">
			<div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
				<input class="mdl-textfield__input" type="text" id="description" required>
				<label class="mdl-textfield__label" for="description">Description</label>
			</div>
			<div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
				<input class="mdl-textfield__input" type="text" pattern="-?[0-9]*(\.[0-9]+)?" id="maximum_connections" required>
				<label class="mdl-textfield__label" for="maximum_connections">Maximum Connections per Bank</label>
				<span class="mdl-textfield__error">Input is not a number!</span>
			</div>
			<div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
				<input class="mdl-textfield__input" type="text" pattern="-?[0-9]*(\.[0-9]+)?" id="maximum_amount" required>
				<label class="mdl-textfield__label" for="maximum_connections">Total Amount per Bank</label>
				<span class="mdl-textfield__error">Input is not a number!</span>
			</div>
			<div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
				<input class="mdl-textfield__input" type="text" pattern="-?[0-9]*(\.[0-9]+)?" id="amount_exchange" required>
				<label class="mdl-textfield__label" for="amount_exchange">Amount per Exchange</label>
				<span class="mdl-textfield__error">Input is not a number!</span>
			</div>
			<div class="mdl-dialog__actions mdl-dialog__actions">
				<button id="button-cancel" type="button" class="mdl-button">Cancel</button>
				<button id="button-save" type="button" class="mdl-button">Save</button>
			</div>
		</form>						
	</div>
</div>
@endsection

@section('script')
<script defer>
	jQuery( document ).ready(function() {

		var valid = true;
		var isSetupValid = function(){
			$(":input").each((index) => {
				valid = valid & (this.value !== "");
			});

		}

		var onChange = function(input){
			save_button.enabled(isSetupValid());
		};

		var save_button = $('button#save-button');
		save_button.on('click', function () {

			var formSetup = $("form#simulation-setup");
			if (isValid()) {
				formSetup.sumit();
			}
			return false;
		});	

		$('button#cancel-button').on('click', () => {
			document.location = "/simulations";
		});
		
		$('input').on('change', (input) => {
			onChange(input);
		});

	}
</script>
@endsection