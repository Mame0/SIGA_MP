<?php
	require_once 'include/cabecera.php';
if (!defined('CONST_SEARCH_MENU')) {
    define('CONST_SEARCH_MENU', 'Buscar Menú');
}
	
	$cade_oper_subm = '';
	$cade_oper_menu = '';
	$arra_subm = [];
	$arra_valo_subm = [];

	// Verifica si el usuario está logueado antes de ejecutar consultas
	if (isset($_SESSION['iden_oper']) && !empty($_SESSION['iden_oper'])) {
		//obtiene cadena de los submenus del usuario
		$result_submenus = $Db->query("select distinct iden_subm from mp_admi_oper_role as a,mp_admi_role_subm as b where a.iden_role=b.iden_role AND a.iden_oper=:oper", [':oper' => $_SESSION['iden_oper']]);
		
		$subm_ids = [];
		foreach ($result_submenus as $rows) {
			$subm_ids[] = $rows['iden_subm'];
		}

		if (!empty($subm_ids)) {
			$cade_oper_subm = implode(',', $subm_ids);

			//obtiene array de los submenus del usuario
			$result_full_submenus = $Db->query("select * from mp_admi_subm where iden_subm IN ($cade_oper_subm) order by orde_subm ASC");
			
			$menu_ids = [];
			foreach ($result_full_submenus as $rows) {
				$arra_subm[$rows['iden_menu']][$rows['iden_padr']][$rows['iden_subm']] = $rows['nomb_subm'];
				$arra_valo_subm[$rows['iden_subm']] = $rows;
				$menu_ids[] = $rows['iden_menu'];
			}
			
			if(!empty($menu_ids)){
				$cade_oper_menu = implode(',', array_unique($menu_ids));
			}
		}
	}
?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8" />
		<meta name="author" content="www.isolutions.com" />
		<meta name="viewport" content="width=device-width initial-scale=1.0 maximum-scale=1.0 user-scalable=yes" />

		<title>MPFN - DF Arequipa</title>

		<link rel="stylesheet" href="css/font-awesome.min.css" />
		<link rel="stylesheet" href="css/menu.css" />
		<link rel="stylesheet" href="css/mmenu.css" />
		<link rel="stylesheet" href="css/iso.css" />
		<style>
			iframe { display:block; width:95%; border:none; }
			
			#div-mensajes{
                position: fixed;
                z-index: 100; /*Crea una capa nueva por encima, si tenemos una con valor 2 estará a una altura o por encima de una con valor 1*/
                margin-left:0%; /*Con este margen posicionamos el div donde queramos*/
                font-weight: normal;
            }
		</style>
		<script>
			function load_page(url)
			{
				window.frames.body_iframe.location.href = url;
			}
		</script>
	</head>
	<body>
		<div id="page">
			<div class="header" align=right id="header" name="header">
				<a href="#menu"><span></span></a>
				<div id="div-mensajes"></div> 
				<table border=0 cellpadding=0 cellspacing=0 align=right>
				    <tr>
				        <!--<td>DF Arequipa</td>
				        <td>&nbsp;</td>
				        <td valign=middle><img src="img/logo_blanco.gif" height=25 style="vertical-align: middle;"></td>-->
<?php
/*
            if(strstr($cade_oper_subm,'143'))   //resumen
                echo"
				        <td valign=middle><img src=\"img/icons/stats_white.svg\" height=20 style=\"vertical-align: middle;\" onclick=\"parent.body_iframe.document.location.href='elecciones_resumen.php'\"></td>
				";
			if(strstr($cade_oper_subm,'144'))   //prevencion
				echo"
				        <td>&nbsp;&nbsp;&nbsp;</td>
				        <td valign=middle><img src=\"img/icons/prevencion_blanco.svg\" height=20 style=\"vertical-align: middle;\" onclick=\"parent.body_iframe.document.location.href='elecciones_prevencion.php'\"></td>
				";
			if(strstr($cade_oper_subm,'145'))   //alerta
				echo"
				        <td>&nbsp;&nbsp;&nbsp;</td>
				        <td valign=middle><img src=\"img/icons/alerta_blanco.svg\" height=20 style=\"vertical-align: middle;\" onclick=\"parent.body_iframe.document.location.href='elecciones_alertas.php'\"></td>
				";
			if(strstr($cade_oper_subm,'146'))   //detencion
				echo"
				        <td>&nbsp;&nbsp;&nbsp;</td>
				        <td valign=middle><img src=\"img/icons/detencion_blanco.svg\" height=20 style=\"vertical-align: middle;\" onclick=\"parent.body_iframe.document.location.href='elecciones_detenciones.php'\"></td>
				";
			if(strstr($cade_oper_subm,'141'))   //coordinaciones
				echo"
				        <td>&nbsp;&nbsp;&nbsp;</td>
				        <td valign=middle><img src=\"img/icons/deal_white.svg\" height=20 style=\"vertical-align: middle;\" onclick=\"parent.body_iframe.document.location.href='elecciones_coordinaciones.php'\"></td>
				";
			if(strstr($cade_oper_subm,'142'))   //difusion
			    echo"
				        <td>&nbsp;&nbsp;&nbsp;</td>
				        <td valign=middle><img src=\"img/icons/facebook_white.svg\" height=20 style=\"vertical-align: middle;\" onclick=\"parent.body_iframe.document.location.href='elecciones_difusion.php'\"></td>
				";
*/
            if(strstr($cade_oper_subm,'263'))   //inventario por dependencia
			    echo"
				        <td>&nbsp;&nbsp;&nbsp;</td>
				        <td valign=middle><img src=\"img/check-square-white.png\" height=20 style=\"vertical-align: middle;\" onclick=\"parent.body_iframe.document.location.href='inventario_bienes_dependencia.php'\"></td>
				";
            if(strstr($cade_oper_subm,'259'))   //inventario ubicacion
			    echo"
				        <td>&nbsp;&nbsp;&nbsp;</td>
				        <td valign=middle><img src=\"img/home-white.png\" height=20 style=\"vertical-align: middle;\" onclick=\"parent.body_iframe.document.location.href='inventario_ubicacion.php'\"></td>
				";
			if(strstr($cade_oper_subm,'258'))   //inventario buscar bienes
			    echo"
				        <td>&nbsp;&nbsp;&nbsp;</td>
				        <td valign=middle><img src=\"img/search-white.png\" height=20 style=\"vertical-align: middle;\" onclick=\"parent.body_iframe.document.location.href='inventario_buscar.php'\"></td>
				";
?>
				    </tr>
				</table>
			</div>
			<center>
			<BR>
			<div class="embed-container">
				<iframe width="90%" height="620" src="index.html" id="body_iframe" name="body_iframe" style="border:0px solid red;" allowfullscreen></iframe>
			</div>
			<nav id="menu">
<?php
	function put_subm($arra_valo_subm,$arra_subm,$rows_menu,$codi)
	{
		if(substr($arra_valo_subm[$codi]['nomb_subm'],0,6)=='CONST_')
			$arra_valo_subm[$codi]['nomb_subm']=constant($arra_valo_subm[$codi]['nomb_subm']);
		if(isset($arra_subm[$rows_menu['iden_menu']][$codi]))
		{
			echo"<li><span><img src=\"img/icons/{$arra_valo_subm[$codi]['icon_subm']}.svg\" width=14px>&nbsp;&nbsp;".$arra_valo_subm[$codi]['nomb_subm']."</span><ul>";
			foreach($arra_subm[$rows_menu['iden_menu']][$codi] as $codi => $nomb)
			{
				//echo"<li><a href=\"{$arra_valo_subm[$codi]['page_subm']}\" target=\"body_iframe\"><img src=\"img/icons/{$arra_valo_subm[$codi]['icon_subm']}.svg\" width=14px>&nbsp;&nbsp;".$arra_valo_subm[$codi]['nomb_subm']."</a></li>";
				put_subm($arra_valo_subm,$arra_subm,$rows_menu,$codi);
			}
			echo"</ul></li>";
		}
		else
			echo"<li><a href=\"{$arra_valo_subm[$codi]['page_subm']}\" target=\"body_iframe\"><img src=\"img/icons/{$arra_valo_subm[$codi]['icon_subm']}.svg\" width=14px>&nbsp;&nbsp;".$arra_valo_subm[$codi]['nomb_subm']."</a></li>";
	}

	$result_menu=$Db->select('mp_admi_menu','','','',['orde_menu'=>'ASC']);
	foreach ($result_menu as $rows_menu)
	{
					echo"
						<div id=\"panel-{$rows_menu['iden_menu']}\">
							<ul>
				";		if (isset($arra_subm[$rows_menu['iden_menu']][0]) && is_array($arra_subm[$rows_menu['iden_menu']][0])) {
					foreach($arra_subm[$rows_menu['iden_menu']][0] as $codi => $nomb)
						put_subm($arra_valo_subm,$arra_subm,$rows_menu,$codi);
				}
		echo"
					</ul>
				</div>
		";
		
	}
?>
			</nav>
		</div>

		<!-- mmenu scripts -->
		<script src="js/mmenu.polyfills.js"></script>
		<script src="js/mmenu.js"></script>
		<!--<script src="js/iso.js"></script>-->
		<script>
		
	new Mmenu(
				document.querySelector('#menu'),
				{
					extensions	: [ 'theme-white', 'shadow-page' ],
					setSelected	: true,
					counters	: true,
					searchfield : {
						placeholder		: '<?=CONST_SEARCH_MENU?>'
					},
					iconbar		: {
						use 		: '(min-width: 299px)',
						top 		: [
							'<a href="#/"><img src="img/icons/home.svg" width=16px><!--<span class="fa fa-home">--></span></a>',
						],
						bottom 		: [
							'<a href="#/"><img src="img/mpfn.png" width=16px><!--<span class="fa fa-home">--></span></a>',
							'<a href="admin_account.php" target="body_iframe"><img src="img/icons/user.svg" width=14px><!--<span class="fa fa-twitter"></span>--></a>',
							'<a href="logout.php"><img src="img/icons/power.svg" width=20px><!--<span class="fa fa-youtube"></span>--></a>'
						]
					},
					sidebar		: {
						collapsed		: {
							use 			: '(min-width: 299px)',
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
<?php
	require_once 'classes/Db.class.php';
        $Db = new Db();
        
        //'<a href="elecciones_incidencias.php" target="blank"><img src="img/icons/file.svg" width=20px><!--<span class="fa fa-twitter"></span>--></a>',
		//					'<a href="elecciones_coordinaciones.php" target="body_iframe"><img src="img/icons/deal.svg" width=20px><!--<span class="fa fa-twitter"></span>--></a>',
		//					'<a href="elecciones_difusion.php" target="body_iframe"><img src="img/icons/facebook.svg" width=20px><!--<span class="fa fa-twitter"></span>--></a>',
        //'<a href="admin_account.php" target="body_iframe"><img src="img/icons/user.svg" width=14px><!--<span class="fa fa-twitter"></span>--></a>',
		//'<a href="#page"><img src="img/icons/calendar.svg" width=14px><!--<span class="fa fa-facebook"></span>--></a>',
	// Se comprueba que $cade_oper_menu no esté vacía para evitar un error de sintaxis SQL.
	if (!empty($cade_oper_menu)) {
		$result=$Db->query("select * from mp_admi_menu where iden_menu IN ($cade_oper_menu) AND esta_menu=1 order by orde_menu ASC");
		foreach($result as $rows => $cols)
			echo"'<a href=\"#panel-".$cols['iden_menu']."\"><img src=\"img/icons/{$cols['icon_menu']}.svg\" width=14px><span>".constant($cols['nomb_menu'])."</span></a>',";
	}
?>
								
							]
						}, {
							content		: [ 'prev', 'breadcrumbs', 'close' ]
						}, {
							position	: 'bottom',
							content		: [ '<a href="http://www.google.com" target="_blank"><?=(isset($_SESSION['nomb_oper']) ? $_SESSION['nomb_oper'] : '')?></a>' ]
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
					//alert('Thank you for clicking, but that\'s a demo link.');
					evnt.preventDefault();
				}
			});
			window.onresize = function() {
				document.getElementById('body_iframe').height=window.innerHeight-90;
			}
			document.getElementById('body_iframe').height=window.innerHeight-90;
		</script>
	</body>
</html>
