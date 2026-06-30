<?
	require "include/conection.inc";
	require "include/html.inc";
	require "include/gene.inc";
	$acceso = do_conection();
	require "include/sesiones/acse_seguridad.php";

	$codi_tipo=$_GET["codi_tipo"];  if(!$codi_tipo) $codi_tipo=$_POST["codi_tipo"];
	$n_latitud=$_GET["n_latitud"];  if(!$n_latitud) $n_latitud=$_POST["n_latitud"];
	$n_longitud=$_GET["n_longitud"];  if(!$n_longitud) $n_longitud=$_POST["n_longitud"];
	if($n_latitud=='undefined' OR $n_longitud=='undefined')
		unset($n_latitud,$n_longitud);
    	
?>
<HTML>
<HEAD>
<TITLE>Documento sin t&iacute;tulo</TITLE>
	<link rel="stylesheet" href="estilos/bootstrap.min.css">
	<link href="estilos/cs_caja.css" rel="stylesheet" type="text/css">
</HEAD>
<BODY><center><BR><form name="form">
	<div id="map" style="width: 600px; height: 340px; border-width: 1px; border-top-style: solid; border-left-style: solid; border-right-style: solid; border-bottom-style: solid; border-radius: 0.5em; border-color:silver;"></div>
	<script>
		var map;
		function initMap()
		{
			<?
				if(!$n_latitud)	$n_latitud="-16.398837623027234";
				if(!$n_longitud)	$n_longitud="-71.53693601586912";
				echo"var myLatLng = {lat: $n_latitud, lng: $n_longitud};";
			?>
			var map = new google.maps.Map(document.getElementById('map'),{
				zoom: 14,
				center: myLatLng
			});
			
			<?
				echo"var myLatLng3 = {lat: $n_latitud, lng: $n_longitud};";
			?>
			var marker3 = new google.maps.Marker({
				position: myLatLng3,
				map: map,
				icon: 'imagenes/lugar.png',
				draggable: true,
				animation: google.maps.Animation.DROP,
				title: 'Arrastre hasta la ubicación'
			});
			
			//marker1.setMap(map);
			
			var geocoder = new google.maps.Geocoder();
								
			google.maps.event.addListener(marker3, 'dragend', function() {
				geocoder.geocode({'latLng': marker3.getPosition()}, function(results, status) {
					if (status == google.maps.GeocoderStatus.OK) {
						var address=results[0]['formatted_address'];
						//parent.parent.document.form.x_direccion.value=address;
						if(address.indexOf("Unnamed")!=-1)
							address='';
						<?
							echo"parent.parent.document.form.x_direccion_".$codi_tipo.".value=address;";
						?>
					}
				});
			});
			<?
				echo"
					google.maps.event.addListener(marker3, 'dragend', function (event) {
					    parent.parent.document.form.n_latitud_".$codi_tipo.".value = this.getPosition().lat();
					    parent.parent.document.form.n_longitud_".$codi_tipo.".value = this.getPosition().lng();
					});
				";
			?>

		}
	</script>
	<!--<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAkRtgx0NjhRNtXynb-X0MvGGmkqq9P7MY&callback=initMap" async defer></script>-->
	<!--<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBks0W0NawnPju70JQS5XXPOTTrguDQjWE&callback=initMap" async defer></script>-->
	<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyD6AreMUlOQ90uaRuERD8J4Jv5DnQ85Xys&callback=initMap" async defer></script>
	<!--<table><tr><td><img src="imagenes/vacio.gif" height=5></td></tr><tr>
	<td><input type="text" name="x_direccion" size=10 value="" class="form-control" style="width: 390px; font-size:14px;" placeholder="DIRECCION" onchange="parent.parent.document.form.x_direccion.value='hola'"></td>
	<td><input type="text" name="n_latitud" size=10 value="" class="form-control" style="width: 100px; font-size:14px;" placeholder="LATITUD" readonly></td>
	<td><input type="text" name="n_longitud" size=10 value="" class="form-control" style="width: 100px; font-size:14px;" placeholder="LONGITUD" readonly></td>
	</tr></table>-->
</form>
</BODY>
</HTML>
