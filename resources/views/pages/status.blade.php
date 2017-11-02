@extends('layouts.app')

@section('content')
<div class="mdl-card">
	<div class="mdl-card__title">
		<h2 class="mdl-card__title-text">Status</h2>
	</div>
	<div class="mdl-card__actions mdl-card--border" style="width:auto;">
		<div id="mdl-layout">


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
<!-- <script defer>
	jQuery( document ).ready(function() {
		var options = {
			valueNames: ['origin', 'destination', 'amount']
		}, 
		documentTable = new List('mdl-table', options);

		$('input.search').on('keyup', function (e) {
			if (e.keyCode === 27) {
				$(e.currentTarget).val('');
				documentTable.search('');
			}
		});	
	});
</script> -->
@endsection