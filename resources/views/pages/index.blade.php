@extends('layouts.app')

@section('content')

<div class="mdl-card-banks">
	<div class="mdl-card__title">
		<h3 class="mdl-card__title-text">Welcome to DepositXchange!</h3>
	</div>
	<div class="mdl-card__actions mdl-card--border">
		<p class="comment">This application was designed to simulate, and keep track, of FDIC insured deposits between banks.</p>
		<div id="mdl-table">
			<button 
				type="button" 	
				class="buttom-sm mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect mdl-button--accent"
				onclick="javascript:{ document.location = '/simulations'; }">
				Go to Simulations
			</button>
		</div>

	</div>
</div>
@endsection