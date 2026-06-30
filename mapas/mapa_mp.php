<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.4/jquery.min.js"></script>
<script type="text/javascript" src="js/html2canvas.js"></script>
<script type="text/javascript" src="js/jquery.plugin.html2canvas.js"></script>

<?
//	require "include/conection.inc";
//	require "include/html.inc";
//	require "include/gene.inc";
//	$acceso = do_conection();
//	require "include/sesiones/acse_seguridad.php";

	$codi_tipo=$_GET["codi_tipo"];  if(!$codi_tipo) $codi_tipo=$_POST["codi_tipo"];
	$n_codi_cviolencia=$_GET["n_codi_cviolencia"];  if(!$n_codi_cviolencia) $n_codi_cviolencia=$_POST["n_codi_cviolencia"];
	$n_codi_parte=$_GET["n_codi_parte"];  if(!$n_codi_parte) $n_codi_parte=$_POST["n_codi_parte"];
	$n_latitud=$_GET["n_latitud"];  if(!$n_latitud) $n_latitud=$_POST["n_latitud"];
	$n_longitud=$_GET["n_longitud"];  if(!$n_longitud) $n_longitud=$_POST["n_longitud"];
	$nomb_parte=$_GET["nomb_parte"];  if(!$nomb_parte) $nomb_parte=$_POST["nomb_parte"];
	if($n_latitud=='undefined' OR $n_longitud=='undefined')
		unset($n_latitud,$n_longitud);
    	
	if(!$n_longitud)
	{
//		$sql="select n_latitud,n_longitud from inst_sede=a,inst_instancia=b where a.n_codi_sede=b.n_codi_sede AND b.n_codi_instancia='$inst_oper'";
//		$result=mysqli_query($acceso,$sql);
//		list($n_latitud,$n_longitud)=mysqli_fetch_array($result);
	}
?>
<HTML>
<HEAD>
<TITLE>Documento sin t&iacute;tulo</TITLE>
	<link rel="stylesheet" href="estilos/bootstrap.min.css">
	<link href="estilos/cs_caja.css" rel="stylesheet" type="text/css">
</HEAD>
<BODY><center>

<form name="form" method="POST" enctype="multipart/form-data" action="save.php" id="myForm"
onKeypress="if(event.keyCode == 13) event.returnValue = false;"
>
	<input type="hidden" name="img_val" id="img_val" value="" />
	<input type="hidden" name="n_codi_cviolencia" value="<?=$n_codi_cviolencia?>" />
	<input type="hidden" name="n_codi_parte" value="<?=$n_codi_parte?>" />
	<input type="hidden" name="nomb_parte" value="<?=$nomb_parte?>" />
    
	<table>
	<tr>
		<td colspan="2">
			<table width="100%" border=0>
				<tr>
					<td>
<input id="pac-input" class="form-control" type="text" placeholder="Buscar Ubicaci&oacute;n" style="width:400px; margin-top: 1px;padding: 0 11px 0 13px;">
					</td>
					<td align=right>
						<button type="button" id="capturar" style="width:50px; height:25;margin-top: 1px;">Aceptar</button>
						 <div id="googlemapimage">
 							<img id="googlemapbinary"/>
 						</div>

					</td>
                </tr>
			</table>	
		</td>
	</tr>
	<tr>
        <td>	

	<div id="map" style="width: 700px; height: 440px; border-width: 1px; border-top-style: solid; border-left-style: solid; border-right-style: solid; border-bottom-style: solid; border-radius: 0.5em; border-color:silver;"></div>
	<script>
		var map;
		function initMap()
		{
			<?
				if(!$n_latitud)	$n_latitud="-16.398837623027234";
				if(!$n_longitud)	$n_longitud="-71.53693601586912";
				echo"var myLatLng = {lat: $n_latitud, lng: $n_longitud};";
			?>
			map = new google.maps.Map(document.getElementById('map'),{
				zoom: 16,
				center: myLatLng
			});
			
			var point = new GLatLng(-19.000514,46.603516);
        map.addOverlay(new GMarker(point));


        var input2 = document.getElementById('capturar');
//        map.controls[google.maps.ControlPosition.TOP_RIGHT].push(input2);

// Create the search box and link it to the UI element.
        var input = document.getElementById('pac-input');
        var searchBox = new google.maps.places.SearchBox(input);
//        map.controls[google.maps.ControlPosition.TOP_LEFT].push(input);

// Bias the SearchBox results towards current map's viewport.
        map.addListener('bounds_changed', function() {
          searchBox.setBounds(map.getBounds());
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
			
			marker1.setMap(map);
			
			var geocoder = new google.maps.Geocoder();

google.maps.event.addListener(map, 'click', function(event) {
            placeMarker(event.latLng);
//		marker3.setPosition = myLatLng3;
        });
								
			google.maps.event.addListener(marker3, 'dragend', function() {
				geocoder.geocode({'latLng': marker3.getPosition()}, function(results, status) {
					if (status == google.maps.GeocoderStatus.OK) {
						var address=results[0]['formatted_address'];
						if(address.indexOf("Unnamed")!=-1)
							address='';
						//parent.parent.document.form.x_direccion.value=address;
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


var markers = [];
        // Listen for the event fired when the user selects a prediction and retrieve
        // more details for that place.
        searchBox.addListener('places_changed', function() {
          var places = searchBox.getPlaces();

          if (places.length == 0) {
            return;
          }


          // Clear out the old markers.
//          markers.forEach(function(marker) {
//            marker.setMap(null);
//          });
//          markers = [];

          // For each place, get the icon, name and location.
          var bounds = new google.maps.LatLngBounds();
          places.forEach(function(place) {
            if (!place.geometry) {
              console.log("Returned place contains no geometry");
              return;
            }
            var icon = {
              url: place.icon,
              size: new google.maps.Size(71, 71),
              origin: new google.maps.Point(0, 0),
              anchor: new google.maps.Point(17, 34),
              scaledSize: new google.maps.Size(25, 25)
            };

            // Create a marker for each place.
            markers.push(new google.maps.Marker({
//              map: map,
//              icon: icon,
//              title: place.name,
//              position: place.geometry.location
            })
);

            if (place.geometry.viewport) {
              // Only geocodes have viewport.
              bounds.union(place.geometry.viewport);
            } else {
              bounds.extend(place.geometry.location);
            }
          });
          map.fitBounds(bounds);
        });



			function placeMarker(location)
			{
				if (marker3 == undefined)
				{
					marker3 = new google.maps.Marker(
					{
						position: location,
						map: map, 
						animation: google.maps.Animation.DROP,
					});
				}
				else
				{
					marker3.setPosition(location);
				}
				<?
				echo"
				    parent.parent.document.form.n_latitud_".$codi_tipo.".value = marker3.getPosition().lat();
				    parent.parent.document.form.n_longitud_".$codi_tipo.".value = marker3.getPosition().lng();
				";
				?>
				geocoder.geocode({'latLng': marker3.getPosition()}, function(results, status)
				{
					if (status == google.maps.GeocoderStatus.OK)
					{
						var address=results[0]['formatted_address'];
						if(address.indexOf("Unnamed")!=-1)
							address='';
						//parent.parent.document.form.x_direccion.value=address;
						<?
						echo"parent.parent.document.form.x_direccion_".$codi_tipo.".value=address;";
						?>
					}
				});
				//map.setCenter(location);
		        }
		}


	</script>
	<!--<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAkRtgx0NjhRNtXynb-X0MvGGmkqq9P7MY&callback=initMap" async defer></script>-->
	<!--<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBks0W0NawnPju70JQS5XXPOTTrguDQjWE&callback=initMap" async defer></script>-->
<!--<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyD6AreMUlOQ90uaRuERD8J4Jv5DnQ85Xys&callback=initMap" async defer></script>-->

	<!--<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyD6AreMUlOQ90uaRuERD8J4Jv5DnQ85Xys&callback=initMap&libraries=places" async defer></script>-->
	<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAkRtgx0NjhRNtXynb-X0MvGGmkqq9P7MY&callback=initMap&libraries=places" async defer></script>

	<!--<table><tr><td><img src="imagenes/vacio.gif" height=5></td></tr><tr>
	<td><input type="text" name="x_direccion" size=10 value="" class="form-control" style="width: 390px; font-size:14px;" placeholder="DIRECCION" onchange="parent.parent.document.form.x_direccion.value='hola'"></td>
	<td><input type="text" name="n_latitud" size=10 value="" class="form-control" style="width: 100px; font-size:14px;" placeholder="LATITUD" readonly></td>
	<td><input type="text" name="n_longitud" size=10 value="" class="form-control" style="width: 100px; font-size:14px;" placeholder="LONGITUD" readonly></td>
	</tr></table>-->
    
  </td>

	</tr>
</table>
</form>
    
    
    <script>

	$('#capturar').on('click', function() {
	    var z=map.getZoom();

//this.getPosition().lat()

	    //map.setCenter({lat: -16.39162550963693, lng: -71.54168128967285});
	    //map.setCenter(map.marker3.getPosition());
<?
	echo"
		var la=parent.parent.document.form.n_latitud_".$codi_tipo.".value;
		var lo=parent.parent.document.form.n_longitud_".$codi_tipo.".value;
	";
?>
	    var xla=map.getCenter().lat();
	    var xlo=map.getCenter().lng();
	    if(z==16 && xla==la && xlo==lo)
	    {
document.getElementById('cargando').style.display='block';
		$('#map').html2canvas({
			"proxy": "html2canvasproxy.php",
	        	"logging": true,
	        	"useCORS": true,
			onrendered: function (canvas)
			{
		                var img = canvas.toDataURL("image/png");
				var output = encodeURIComponent(img);
				$.post('save.php', 'image='+output+'&n_codi_cviolencia='+document.form.n_codi_cviolencia.value+'&n_codi_parte='+document.form.n_codi_parte.value+'&nomb_parte='+document.form.nomb_parte.value+'&nume_denu_alea='+parent.parent.document.form.nume_denu_alea.value, function(){

   				});
				img = img.replace('data:image/png;base64,', '');
				var finalImageSrc = 'data:image/png;base64,' + img;
				$('#googlemapbinary').attr('src', finalImageSrc);   
				return false;
			}
		});
		//setTimeout (function() { parent.parent.GB_hide(); }, 7000);
		setTimeout (function() { parent.parent.GB_hide(); }, 15000);
	    }
	    else
	    {
		map.setZoom(16);
	        map.setCenter(new google.maps.LatLng(la, lo));
	    }
	});
	</script>
 
<img src="imagenes/loading.gif" id="cargando" style="position: fixed; width: 150px;height: 150px; top: 50%; left: 50%;margin-top: -75px;margin-left: -75px;  display:none;" alt="" />
</BODY>
</HTML>
