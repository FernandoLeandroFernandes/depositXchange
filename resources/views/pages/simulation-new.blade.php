@extends('layouts.app')

@section('content')
<div class="mdl-card">
	<div class="mdl-card__title">
		<h3 class="mdl-card__title-text">Setup Simulation</h3>
	</div>
	<div class="mdl-card__actions mdl-card--border">
		<form id="simulation-setup" action="/simulation/setup">
			<div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
				<input 
					class="mdl-textfield__input" 
					type="text" id="description" 
					id="description" 
					name="description" 
					required>
				<label class="mdl-textfield__label" for="description">Description</label>
			</div>
			<div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
				<input 
					class="mdl-textfield__input" 
					type="text" pattern="-?[0-9]*(\.[0-9]+)?" 
					id="max_connections" 
					name="max_connections" 
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
					required>
				<label class="mdl-textfield__label" for="total_amount">Total Amount per Bank</label>
				<span class="mdl-textfield__error">Input is not a number!</span>
			</div>
			<div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
				<input 
					class="mdl-textfield__input" 
					type="text" 
					pattern="-?[0-9]*(\.[0-9]+)?" 
					id="exchange_amount" 
					name="exchange_amount" 
					required>
				<label class="mdl-textfield__label" for="exchange_amount">Amount per Exchange</label>
				<span class="mdl-textfield__error">Input is not a number!</span>
			</div>
			<div class="mdl-dialog__actions mdl-dialog__actions">
				<button id="cancel-button" type="button" class="mdl-button">Cancel</button>
				<button id="save-button" type="button" class="mdl-button" disabled>Save</button>
			</div>
		</form>						
	</div>
</div>
@endsection

@section('script')
<script defer>
	jQuery( document ).ready(function() {

		var passedValidation = function(){
			valid =
				$("input#description")[0].value &&
				$("input#max_connections")[0].value &&
				$("input#total_amount")[0].value &&
				$("input#exchange_amount")[0].value;
			return valid;
		};

		var onChange = function(input){
			$('button#save-button').prop('disabled', !passedValidation());
		};

		$('button#save-button').on('click', function () {

			var formSetup = $("form#simulation-setup");
			if (passedValidation()){
				formSetup.submit();
			}
			return false;
		});	

		$('button#cancel-button').on('click', () => {		
			document.location = "/simulations";
		});
		
		$('input').on('keypress', (input) => {
			onChange(input);
		});

	});
</script>
@endsection