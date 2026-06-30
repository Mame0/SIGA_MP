	new Mmenu(
				document.querySelector('#menu'),
				{
					extensions	: [ 'theme-white', 'shadow-page' ],
					setSelected	: true,
					counters	: true,
					searchfield : {
						placeholder		: 'Buscar en el Men\u00FA'
					},
					iconbar		: {
						use 		: '(min-width: 450px)',
						top 		: [
							'<a href="#/"><img src="img/icons/home.svg" width=14px><!--<span class="fa fa-home">--></span></a>'
						],
						bottom 		: [
							'<a href="#/"><img src="img/icons/user.svg" width=14px><!--<span class="fa fa-twitter"></span>--></a>',
							'<a href="#/"><img src="img/icons/calendar.svg" width=14px><!--<span class="fa fa-facebook"></span>--></a>',
							'<a href="logout.php"><img src="img/icons/power.svg" width=14px><!--<span class="fa fa-youtube"></span>--></a>'
						]
					},
					sidebar		: {
						collapsed		: {
							use 			: '(min-width: 450px)',
							hideNavbar		: false
						},
						expanded		: {
							use 			: '(min-width: 992px)'
						}
					},
					navbars		: [
						{
							content		: [ 'searchfield' ]
						}, {
							type		: 'tabs',
							content		: [
								'<a href="#panel-menu"><img src="img/icons/file.svg" width=14px"><span>'+'CONST_MENU_NORM'+'</span></a>',
								'<a href="#panel-cart"><img src="img/icons/settings.svg" width=14px"><span>Administrar</span></a>',
								'<a href="#panel-account"><img src="img/icons/user.svg" width=14px"><span>Usuario</span></a>',
								
							]
						}, {
							content		: [ 'prev', 'breadcrumbs', 'close' ]
						}, {
							position	: 'bottom',
							content		: [ '<a href="http://www.google.com" target="_blank">Jesus Barreda</a>' ]
						}
					]
				}, {
					searchfield : {
						clear 		: true
					},
					navbars		: {
						breadcrumbs	: {
							removeFirst	: true
						}
					}
				}
			);

			document.addEventListener( 'click', function( evnt ) {
				var anchor = evnt.target.closest( 'a[href^="#/"]' );
				if ( anchor ) {
					alert('Thank you for clicking, but that\'s a demo link.');
					evnt.preventDefault();
				}
			});
