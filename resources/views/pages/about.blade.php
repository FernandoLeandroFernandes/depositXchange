@extends('layouts.app')

@section('content')

<div class="mdl-card-banks">
	<div class="mdl-card__title">
		<h3 class="mdl-card__title-text">About</h3>
	</div>
	<div class="mdl-card__actions mdl-card--border">
		<p class="comment">This application was developed by <strong>SparkSignals</strong> for deposit exchange simulation between banks.</p>
		<p class="comment">Contact us on <strong>go@sparksignals.com</strong>, <strong>(407) 925-9427</strong> or accessing our CEO's <a href="https://fernandoleandrofernandes.github.io/" target="blank">blog</a>.</p>
		<!-- <div id="mdl-table">
			<button 
				type="button" 	
				class="buttom-sm mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect mdl-button--accent"
				onclick="javascript:{ document.location = '/simulations'; }">
				Go to Simulations
			</button>
		</div> -->

	</div>
</div>
@endsection