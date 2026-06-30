<html>
  <head>
    <title>Simple Map</title>
    <script src="https://polyfill.io/v3/polyfill.min.js?features=default"></script>
	<style>
		#map {
  height: 100%;
}

/* Optional: Makes the sample page fill the window. */
html,
body {
  height: 100%;
  margin: 0;
  padding: 0;
}
	</style>
	<script type="text/javascript" src="../include/coordenadas_arequipa.js"></script>
	<script type="text/javascript" src="../include/coordenadas_camana.js"></script>
	<script type="text/javascript" src="../include/coordenadas_caraveli.js"></script>
	<script type="text/javascript" src="../include/coordenadas_castilla.js"></script>
	<script type="text/javascript" src="../include/coordenadas_caylloma.js"></script>
	<script type="text/javascript" src="../include/coordenadas_condesuyos.js"></script>
	<script type="text/javascript" src="../include/coordenadas_islay.js"></script>
	<script type="text/javascript" src="../include/coordenadas_launion.js"></script>
    <script>
	let map;

function initMap() {
  const myLatLng = { lat: -16.398837623027234, lng: -71.53693601586912 };
  //const image = "https://developers.google.com/maps/documentation/javascript/examples/full/images/beachflag.png";
  const image = "https://mpfnarequipa.pe/siga/img/edificio.png";
  map = new google.maps.Map(document.getElementById("map"), {
    center: myLatLng,
    zoom: 10,
  });
  
  const contentString =
    '<div id="content">' +
    '<div id="siteNotice">' +
    "</div>" +
    '<h1 id="firstHeading" class="firstHeading">La Merced 427</h1>' +
    '<div id="bodyContent">' +
    "<p><b>Uluru</b>, also referred to as <b>Ayers Rock</b>, is a large " +
    "sandstone rock formation in the southern part of the " +
    "Northern Territory, central Australia. It lies 335&#160;km (208&#160;mi) " +
    "south west of the nearest large town, Alice Springs; 450&#160;km " +
    "(280&#160;mi) by road. Kata Tjuta and Uluru are the two major " +
    "features of the Uluru - Kata Tjuta National Park. Uluru is " +
    "sacred to the Pitjantjatjara and Yankunytjatjara, the " +
    "Aboriginal people of the area. It has many springs, waterholes, " +
    "rock caves and ancient paintings. Uluru is listed as a World " +
    "Heritage Site.</p>" +
    '<p>Attribution: Uluru, <a href="https://en.wikipedia.org/w/index.php?title=Uluru&oldid=297882194">' +
    "https://en.wikipedia.org/w/index.php?title=Uluru</a> " +
    "(last visited June 22, 2009).</p>" +
    "</div>" +
    "</div>";
  const infowindow = new google.maps.InfoWindow({
    content: contentString,
  });
  
  const marker=new google.maps.Marker({
    position: myLatLng,
    map,
    title: "Local: La Merced 427\nUsuarios: 27",
	icon: image,
  });
  marker.addListener("click", () => {
    infowindow.open({
      anchor: marker,
      map,
      shouldFocus: false,
    });
  });
  
  var ArequipaArequipaPolygon = new google.maps.Polygon({
    paths: ArequipaArequipaDelimiters,
    strokeColor: '#0D2C52',
    strokeOpacity: 1,
    strokeWeight: 2,
    fillColor: '#FF0000',
    fillOpacity: 0
  });
  ArequipaArequipaPolygon.setMap(map);
  
  var ArequipaCamanaPolygon = new google.maps.Polygon({
    paths: ArequipaCamanaDelimiters,
    strokeColor: '#0D2C52',
    strokeOpacity: 1,
    strokeWeight: 2,
    fillColor: '#FF0000',
    fillOpacity: 0
  });
  ArequipaCamanaPolygon.setMap(map);
  
  var ArequipaCaraveliPolygon = new google.maps.Polygon({
    paths: ArequipaCaraveliDelimiters,
    strokeColor: '#0D2C52',
    strokeOpacity: 1,
    strokeWeight: 2,
    fillColor: '#FF0000',
    fillOpacity: 0
  });
  ArequipaCaraveliPolygon.setMap(map);
  
  var ArequipaCastillaPolygon = new google.maps.Polygon({
    paths: ArequipaCastillaDelimiters,
    strokeColor: '#0D2C52',
    strokeOpacity: 1,
    strokeWeight: 2,
    fillColor: '#FF0000',
    fillOpacity: 0
  });
  ArequipaCastillaPolygon.setMap(map);
  
  var ArequipaCayllomaPolygon = new google.maps.Polygon({
    paths: ArequipaCayllomaDelimiters,
    strokeColor: '#0D2C52',
    strokeOpacity: 1,
    strokeWeight: 2,
    fillColor: '#FF0000',
    fillOpacity: 0
  });
  ArequipaCayllomaPolygon.setMap(map);
  
  var ArequipaCondesuyosPolygon = new google.maps.Polygon({
    paths: ArequipaCondesuyosDelimiters,
    strokeColor: '#0D2C52',
    strokeOpacity: 1,
    strokeWeight: 2,
    fillColor: '#FF0000',
    fillOpacity: 0
  });
  ArequipaCondesuyosPolygon.setMap(map);
  
  var ArequipaIslayPolygon = new google.maps.Polygon({
    paths: ArequipaIslayDelimiters,
    strokeColor: '#0D2C52',
    strokeOpacity: 1,
    strokeWeight: 2,
    fillColor: '#FF0000',
    fillOpacity: 0
  });
  ArequipaIslayPolygon.setMap(map);
  
  var ArequipaLaUnionPolygon = new google.maps.Polygon({
    paths: ArequipaLaUnionDelimiters,
    strokeColor: '#0D2C52',
    strokeOpacity: 1,
    strokeWeight: 2,
    fillColor: '#FF0000',
    fillOpacity: 0
  });
  ArequipaLaUnionPolygon.setMap(map);
}
	</script>
  </head>
  <body><center>
    <div id="map" style="width: 700px; height: 440px; border-width: 1px; border-top-style: solid; border-left-style: solid; border-right-style: solid; border-bottom-style: solid; border-radius: 0.5em; border-color:silver;"></div>

    <!-- Async script executes immediately and must be after any DOM elements used in callback. -->
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAkRtgx0NjhRNtXynb-X0MvGGmkqq9P7MY&callback=initMap&v=weekly" async></script>
  </body>
</html>