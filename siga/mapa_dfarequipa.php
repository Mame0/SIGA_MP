<?
	require_once 'include/cabecera.php';
	require_once 'classes/Html.class.php';
	require_once 'classes/Db.class.php';
	$Db = new Db();
	if(!isset($_POST['busc_pagi_actu']))
		$_POST['busc_pagi_actu']=1;
?>
<html>
  <head>
    <title>DF Arequipa</title>
		<link rel="stylesheet" href="css/forms_demanda.css" />
		<link rel="stylesheet" href="css/forms_foot.css" />
		<link rel="stylesheet" href="css/forms_column.css" />
		<SCRIPT language="JavaScript" src="js/cadenas.js"></SCRIPT>
		<SCRIPT language="JavaScript" src="js/denuncia.js"></SCRIPT>
    <script src=""></script>
	<style>
		#map {
            height: 100%;
        }
        html,
        body {
            height: 100%;
            margin: 0;
            padding: 0;
        }
	</style>
	<script type="text/javascript" src="include/coordenadas_arequipa.js"></script>
	<script type="text/javascript" src="include/coordenadas_camana.js"></script>
	<script type="text/javascript" src="include/coordenadas_caraveli.js"></script>
	<script type="text/javascript" src="include/coordenadas_castilla.js"></script>
	<script type="text/javascript" src="include/coordenadas_caylloma.js"></script>
	<script type="text/javascript" src="include/coordenadas_condesuyos.js"></script>
	<script type="text/javascript" src="include/coordenadas_islay.js"></script>
	<script type="text/javascript" src="include/coordenadas_launion.js"></script>
    <script>
	    let map;
        function initMap() 
        {
            const myLatLng = { lat: -16.00748879859587, lng: -72.97967520272339 };
            const image = "https://mpfnarequipa.pe/siga/img/marcador_edificio.png";
            map = new google.maps.Map(document.getElementById("map"), 
            {
                center: myLatLng,
                zoom: 8,
            });
<?

    $result=$Db->query("select * from mp_admi_loca where lati_loca<>'' AND long_loca<>'' AND esta_loca='1'");
    foreach($result as $rows)
    {
        echo"
            const marker_".$rows['codi_loca']."=new google.maps.Marker({
                position: { lat: ".$rows['lati_loca'].", lng: ".$rows['long_loca']." },
                map,
                title: \"Nombre: ".$rows['nom1_loca']."\\nDireccion: ".$rows['dire_loca']."\",
	            icon: image,
            });
            
            const contentString_".$rows['codi_loca']." =
            '<div id=\"content\" >' +
                '<div id=\"siteNotice\">' +
                \"</div>\" +
                '<h1 id=\"firstHeading\" class=\"firstHeading\">".$rows['nom1_loca']."</h1>' +
                '<div id=\"bodyContent\" style=\"text-align: left;\">' +
                    \"<p><table border=0>\" +
                        \"<tr><td width=1%><b>Tipo&nbsp;de&nbsp;Local</b></td><td width=1%>&nbsp;:&nbsp;</td><td width=200px>Propio/Alquilado</td></tr>\" +
                        \"<tr><td width=1%><b>Nro.&nbspde&nbspPisos</b></td><td width=1%>&nbsp;:&nbsp;</td><td width=200px>2</td></tr>\" +
                        \"<tr><td width=1%><b>Coordenadas</b></td><td width=1%>&nbsp;:&nbsp;</td><td width=200px>".$rows['lati_loca'].",".$rows['long_loca']."</td></tr>\" +
                        \"<tr><td width=1%><b>C&aacute;maras</b></td><td width=1%>&nbsp;:&nbsp;</td><td width=200px><a target='blank' href='http://10.4.".$rows['rang_loca'].".16/'>http://10.4.".$rows['rang_loca'].".16/</a></td></tr>\" +
                    \"</table></p>\" +
                \"</div>\" +
                \"</div>\";
    

    
            const infowindow_".$rows['codi_loca']." = new google.maps.InfoWindow({
                content: contentString_".$rows['codi_loca'].",
            });
  
            
            marker_".$rows['codi_loca'].".addListener(\"click\", () => {
                infowindow_".$rows['codi_loca'].".open({
                    anchor: marker_".$rows['codi_loca'].",
                    map,
                    shouldFocus: false,
                });
            });
        ";
    }
    
    $arra_mapa=array("ArequipaArequipa","ArequipaCamana","ArequipaCaraveli","ArequipaCastilla","ArequipaCaylloma","ArequipaCondesuyos","ArequipaIslay","ArequipaLaUnion");
    foreach($arra_mapa as $mapa)
    {
        echo"
            var ".$mapa."Polygon = new google.maps.Polygon({
                paths: ".$mapa."Delimiters,
                strokeColor: '#0D2C52',
                strokeOpacity: 0.8,
                strokeWeight: 1,
                fillColor: '#FF0000',
                fillOpacity: 0
            });
            ".$mapa."Polygon.setMap(map);
        ";
    }
?>

        }
    </script>
  </head>
  <body><center>
    <center><h2 style="color:#073A6B">Información Geográfica - DF Arequipa</h2></center>
    <div id="map" style="width: 98%; height: 90%; border-width: 1px; border-top-style: solid; border-left-style: solid; border-right-style: solid; border-bottom-style: solid; border-radius: 0.5em; border-color:silver;"></div>
    <!-- Async script executes immediately and must be after any DOM elements used in callback. -->
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAkRtgx0NjhRNtXynb-X0MvGGmkqq9P7MY&callback=initMap&v=weekly" async></script>
    <!--<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAOVYRIgupAurZup5y1PRh8Ismb1A3lLao&libraries=places&callback=initMap" async></script>-->
  </body>
</html>