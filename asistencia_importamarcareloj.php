<?php
	require_once 'include/cabecera.php';
	require_once 'classes/Html.class.php';
	require_once 'classes/Db.class.php';
	$Db = new Db();

	$nropla=0;
	if($_GET['nropla']) {
		$nropla=$_GET['nropla'];
	}
	if ($nropla==8) { $colini="P"; $nromesesvac=5; }
	if ($nropla==9) { $colini="Q"; $nromesesvac=4; }
	if ($nropla==10) { $colini="R"; $nromesesvac=3; }
	if ($nropla==11) { $colini="S"; $nromesesvac=2; }
	if ($nropla==12) { $colini="T"; $nromesesvac=1; }
	$colfin="T";


require 'simplexlsx.class.php';
if (isset($_POST["accion"])){
	if ($_POST["accion"]=="1"){
		$uploaddir = "temp/";
		$uploadfile = $uploaddir . basename($_FILES['userfile']['name']);
		$rutaarchivo= $uploadfile ;
		$error = $_FILES['userfile']['error'];
		$subido = false;
		if(isset($_POST['accion']) && $error==UPLOAD_ERR_OK) {
			$subido = copy($_FILES['userfile']['tmp_name'], $uploadfile);
		}
		$nombre_archivo = $_FILES['userfile']['name'];
		$tipo_archivo = $_FILES['userfile']['type'];
		$tamano_archivo = $_FILES['userfile']['size'];

		if($subido) {
			$fectarea=date("Y-m-d");
		}else{
			echo "Ocurrió algún error al subir el fichero. No pudo guardarse: ".$error;
		}

		$xlsx = SimpleXLSX::parse($uploadfile);
		list( $num_cols, $num_rows ) = $xlsx->dimension();
		$fil=0;
		foreach ( $xlsx->rows( 1 ) as $r ) {
			$fil++;
			for ( $i = 0; $i < $num_cols; $i ++ ) {
				$datahj1[$fil][$i+1] = ( ( ! empty( $r[ $i ] ) ) ? $r[ $i ] : ' ' ) ;
			}
		}
		for ( $i = 0; $i < $num_cols; $i ++ ) {
			$datahj1[$num_rows][$i+1] = $datahj1[$fil][$i+1] ;
		}


		for ($fil=4;$fil<=10000;$fil++) {
			$codusua = $datahj1[$fil][2] ;
			$fechamrk = $datahj1[$fil][4] ;

			$horaing = $datahj1[$fil][10] ;
			$horasal = $datahj1[$fil][11] ;
			$marcaing = $datahj1[$fil][12] ;
			$marcasal = $datahj1[$fil][13] ;

			$marcaing=str_replace(".:",":",$marcaing);
			$marcasal=str_replace(".:",":",$marcasal);

			if (strlen($marcaing)==8) {$marcaing=substr($marcaing,0,5);}
			if (strlen($marcasal)==8) {$marcasal=substr($marcasal,0,5);}


			$fecexp = explode("/", $fechamrk);
			if (strlen($fechamrk)==19) {
				$fecha = substr($fechamrk,0,10);
			} else {
				$fecha = $fecexp[2]."-". str_pad($fecexp[1], 2, "0", STR_PAD_LEFT) ."-". str_pad($fecexp[0], 2, "0", STR_PAD_LEFT);
			}

			if ($codusua=="") { break; }
			$codusua=str_pad($codusua, 8, "0", STR_PAD_LEFT);

			if (strlen($marcasal)>8) {
				$marcasal = substr($marcasal,11,5);
			}

			if ($marcasal=="00:00" || $marcasal=="" || ($marcasal<=$horasal) ) {
				$horextra = 0;
				$minextra = 0;
			} else {
				$ini = new DateTime($horasal);
				$fin = new DateTime($marcasal);
				$dife = $ini -> diff($fin);
				$horextra = $dife->format('%H');
				$minextra = $dife->format('%i');
			}

			$result_depe=$Db->query("SELECT count( * ) AS cant FROM `mp_asistencia` where dni='".$codusua."' and fecha='".$fecha."' ");
			$cant=$result_depe[0]['cant'];
			if ($cant==0) {
					$result=$Db->insert('mp_asistencia',
					['dni'=>$codusua,'fecha'=>$fecha,'horaentrada'=> $horaing,
					'horasalida'=>$horasal,'horamarcaing'=>$marcaing,'horamarcasal'=>$marcasal,'horasextra'=>$horextra,'minutosextra'=>$minextra ]);
			}
		}

		echo '<html><body>
				<form name="form" method=post action="asistencia_importamarcareloj.php">
				<input type=hidden name="busq_tipo" value="">
				</form>';
		?>
				<script>
					alert ("DATOS IMPORTADOS SATISFACTORIAMENTE");
					document.form.submit();
				</script>
		<?php

		echo '</body></html>';

		exit();
	}
	if ($_POST["accion"]=="2"){
		$uploaddir = "temp/";
		$uploadfile = $uploaddir . basename($_FILES['userfile2']['name']);
		$rutaarchivo= $uploadfile ;
		$error = $_FILES['userfile2']['error'];
		$subido = false;
		if(isset($_POST['accion']) && $error==UPLOAD_ERR_OK) {
			$subido = copy($_FILES['userfile2']['tmp_name'], $uploadfile);
		}
		$nombre_archivo = $_FILES['userfile2']['name'];
		$tipo_archivo = $_FILES['userfile2']['type'];
		$tamano_archivo = $_FILES['userfile2']['size'];

		if($subido) {
			$fectarea=date("Y-m-d");
		}else{
			echo "Ocurrió algún error al subir el fichero. No pudo guardarse: ".$error;
		}

		$xlsx = SimpleXLSX::parse($uploadfile);
		list( $num_cols, $num_rows ) = $xlsx->dimension();
		$fil=0;
		foreach ( $xlsx->rows( 1 ) as $r ) {
			$fil++;
			for ( $i = 0; $i < $num_cols; $i ++ ) {
				$datahj1[$fil][$i+1] = ( ( ! empty( $r[ $i ] ) ) ? $r[ $i ] : ' ' ) ;
			}
		}
		for ( $i = 0; $i < $num_cols; $i ++ ) {
			$datahj1[$num_rows][$i+1] = $datahj1[$fil][$i+1] ;
		}


		for ($fil=4;$fil<=30000;$fil++) {
			$codusua = $datahj1[$fil][2] ;
			$fechamrk = $datahj1[$fil][4] ;

			$horaing = $datahj1[$fil][10] ;
			$horasal = $datahj1[$fil][11] ;
			$marcaing = $datahj1[$fil][12] ;
			$marcasal = $datahj1[$fil][13] ;

			$marcaing=str_replace(".:",":",$marcaing);
			$marcasal=str_replace(".:",":",$marcasal);

			if (strlen($marcaing)==8) {$marcaing=substr($marcaing,0,5);}
			if (strlen($marcasal)==8) {$marcasal=substr($marcasal,0,5);}

			if (strlen($marcasal)>8) {
				$marcasal = substr($marcasal,11,5);
			}

			if (strlen($horasal)==8) {$horasal=substr($horasal,0,5);}
			if (strlen($horasal)>8) {
				$horasal = substr($horasal,11,5);
			}

			if ($marcasal=="00:00" || $marcasal=="" || ($marcasal<=$horasal) ) {
				$horextra = 0;
				$minextra = 0;
			} else {
				$ini = new DateTime($horasal);
				$fin = new DateTime($marcasal);
				$dife = $ini -> diff($fin);
				$horextra = $dife->format('%H');
				$minextra = $dife->format('%i');
			}




			$tothoraspre = $datahj1[$fil][16] ;//total presenciales
			$tothorasrem = $datahj1[$fil][17] ;//total remotas
			$tothoras = $datahj1[$fil][18] ;//total de horas

			$motlicencia = $datahj1[$fil][21] ;//motivo licencia
			$resolucion = $datahj1[$fil][24] ;//resolucion

			if (trim($motlicencia)=="") {$motlicencia="";}
			if (strlen($tothoras)==8) {$tothoras=substr($tothoras,0,5);}


			if (strlen($tothoraspre)==19) {
				$tothoraspre=substr($tothoraspre,11,5);
			}
			if (strlen($tothorasrem)==19) {
				$tothorasrem=substr($tothorasrem,11,5);
			}


			$fecexp = explode("/", $fechamrk);

			if (strlen($fechamrk)==19) {
				$fecha = substr($fechamrk,0,10);
			} else {
				$fecha = $fecexp[2]."-". str_pad($fecexp[1], 2, "0", STR_PAD_LEFT) ."-". str_pad($fecexp[0], 2, "0", STR_PAD_LEFT);
			}

			if ($codusua=="") { break; }
			$codusua=str_pad($codusua, 8, "0", STR_PAD_LEFT);

			if (strlen($tothoras)==19) {
				$tothoras = substr($tothoras,11,5);
			}
			if (strlen($tothoras)>8) {
				$tothoras = substr($tothoras,11,5);
			}

			if ($tothoras=="00:00" || trim($tothoras)=="" || $tothoras==Null ) {
			} else {
				$result_depe=$Db->query("SELECT count( * ) AS cant FROM `mp_asistencia` where dni='".$codusua."' and fecha='".$fecha."' ");
				$cant=$result_depe[0]['cant'];
				if ($cant==0) {
					$result=$Db->insert('mp_asistencia',
					['dni'=>$codusua,'fecha'=>$fecha,'horaentrada'=> $horaing,
					'horasalida'=>$horasal,'horamarcaing'=>$marcaing,'horamarcasal'=>$marcasal,
					'horasextra'=>$horextra,'minutosextra'=>$minextra,
					'horastrabajadas'=>$tothoraspre,'horasremotas'=>$tothorasrem,'horastotales'=>$tothoras, 'licencia_descripcion'=>$motlicencia, 'licencia_resolucion'=>$resolucion ]);
				} else {
					$result=$Db->update('mp_asistencia',
					['dni'=>$codusua,'fecha'=>$fecha,'horaentrada'=> $horaing,
					'horasalida'=>$horasal,'horamarcaing'=>$marcaing,'horamarcasal'=>$marcasal,
					'horasextra'=>$horextra,'minutosextra'=>$minextra,
					'horastrabajadas'=>$tothoraspre,'horasremotas'=>$tothorasrem,'horastotales'=>$tothoras, 'licencia_descripcion'=>$motlicencia, 'licencia_resolucion'=>$resolucion ]  , ['dni'=>$codusua, 'fecha'=>$fecha ]  );
				}
			}

		}

		echo '<html><body>
				<form name="form" method=post action="asistencia_importamarcareloj.php">
				<input type=hidden name="busq_tipo" value="">
				</form>';
		?>
				<script>
					alert ("DATOS IMPORTADOS SATISFACTORIAMENTE");
					document.form.submit();
				</script>
		<?php

		echo '</body></html>';

		exit();
	}
}







//require_once "spreadsheets/vendor/autoload.php";
if (isset($_POST["accionx"])){
	if ($_POST["accion"]=="1"){
		$uploaddir = "temp/";//"notasexcel/";
		$uploadfile = $uploaddir . basename($_FILES['userfile']['name']);
		$rutaarchivo= $uploadfile ;
		$error = $_FILES['userfile']['error'];
		$subido = false;
		if(isset($_POST['accion']) && $error==UPLOAD_ERR_OK) {
			$subido = copy($_FILES['userfile']['tmp_name'], $uploadfile);
		}
		$nombre_archivo = $_FILES['userfile']['name'];
		$tipo_archivo = $_FILES['userfile']['type'];
		$tamano_archivo = $_FILES['userfile']['size'];

		if($subido) {
			$fectarea=date("Y-m-d");
	//			header("location: procesaregxlsinicial.php");
		}else{
			echo "Ocurrió algún error al subir el fichero. No pudo guardarse: ".$error;
		}


		//$spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load( $uploadfile );
		$objReader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader('Xlsx');
		$spreadsheet = $objReader->load($uploadfile);


		$worksheet = $spreadsheet->getActiveSheet();

		//$worksheet->setTitle("Mi Hoja");
		for ($fil=4;$fil<=1000;$fil++) {
			$codusua=$worksheet->getCell("B".$fil)->getValue();
			$fechamrk =$worksheet->getCell("D".$fil)->getFormattedValue();

			//$fecha = date('Y-m-d', PhpOffice\PhpSpreadsheet\Shared\Date::excelToTimestamp($fechamrk));
			$fecexp = explode("/", $fechamrk);
			$fecha = $fecexp[2]."-".$fecexp[1]."-".$fecexp[0];

			$horaing=$worksheet->getCell("J".$fil)->getValue();
			$horasal=$worksheet->getCell("K".$fil)->getValue();
			$marcaing=$worksheet->getCell("L".$fil)->getValue();
			$marcasal=$worksheet->getCell("M".$fil)->getValue();
			if ($codusua=="") { break; }


			if ($marcasal=="00:00:00" || ($marcasal<=$horasal) ) {
				$horextra = 0;
				$minextra = 0;
			} else {
				$ini = new DateTime($horasal);
				$fin = new DateTime($marcasal);
				$dife = $ini -> diff($fin);
				$horextra = $dife->format('%H');
				$minextra = $dife->format('%i');
			}

			$result_depe=$Db->query("SELECT count( * ) AS cant FROM `mp_asistencia` where dni='".$codusua."' and fecha='".$fecha."' ");
			$cant=$result_depe[0]['cant'];
			if ($cant==0) {
					$result=$Db->insert('mp_asistencia',
					['dni'=>$codusua,'fecha'=>$fecha,'horaentrada'=> $horaing,
					'horasalida'=>$horasal,'horamarcaing'=>$marcaing,'horamarcasal'=>$marcasal,'horasextra'=>$horextra,'minutosextra'=>$minextra ]);
			}
		}

		echo '<html><body>
				<form name="form" method=post action="asistencia_importamarcareloj.php">
				<input type=hidden name="busq_tipo" value="">
				</form>';
		?>
				<script>
					alert ("DATOS IMPORTADOS SATISFACTORIAMENTE");
					document.form.submit();
				</script>
		<?php

		echo '</body></html>';

		exit();
	}
}




?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8" />
		<title>-</title>
		<link rel="stylesheet" href="css/forms_demanda.css" />
		<link rel="stylesheet" href="css/forms_foot.css" />
		<link rel="stylesheet" href="css/forms_column.css" />
		<SCRIPT language="JavaScript" src="js/cadenas.js"></SCRIPT>
		<SCRIPT language="JavaScript" src="js/denuncia.js"></SCRIPT>
		<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
		<script>
			function f_importar() {
				event.preventDefault();

document.form.accion.value='1';
		setTimeout(function(){
		var fd = new FormData(document.getElementById("form"));
  $(".progress").show();
  $('#bookdetails').modal("show").on('shown.bs.modal', function() {

			$.ajax({
			  url: "asistencia_importamarcareloj.php",
			  async: false,
			  type: "POST",
			  data: fd,
			  processData: false,  // tell jQuery not to process the data
			  contentType: false   // tell jQuery not to set contentType
			}).done(function( data ) {
				data=data.replace("\n","");
				$('#bookdetails').modal("hide");

				document.form.accion.value='0';
				document.form.submit();

			}).fail(function (jqXHR, textStatus) {
				//alert (textStatus);
			});

  });


		}, 200);


//				document.form.accion.value='1';
//				document.form.submit();
			}


			function f_importar2() {
				event.preventDefault();

document.form.accion.value='2';
		setTimeout(function(){
		var fd = new FormData(document.getElementById("form"));
  $(".progress").show();
  $('#bookdetails').modal("show").on('shown.bs.modal', function() {

			$.ajax({
			  url: "asistencia_importamarcareloj.php",
			  async: false,
			  type: "POST",
			  data: fd,
			  processData: false,  // tell jQuery not to process the data
			  contentType: false   // tell jQuery not to set contentType
			}).done(function( data ) {
				data=data.replace("\n","");
				$('#bookdetails').modal("hide");

				document.form.accion.value='0';
				document.form.submit();

			}).fail(function (jqXHR, textStatus) {
				//alert (textStatus);
			});

  });


		}, 200);


//				document.form.accion.value='1';
//				document.form.submit();
			}




			function ajustar_altura() {
				parent.document.getElementById('body_iframe').height=parent.window.innerHeight-80;
			}
			ajustar_altura();
		</script>
	</head>
	<body style="margin-bottom: 30px;">
	<center><h2 style="color:#073A6B">IMPORTACION DE ASISTENCIAS DESDE ARCHIVO EXCEL</h2></center>
		<form id="form" name="form" method="post" action="asistencia_importamarcareloj.php" enctype="multipart/form-data">

<?php
	$html=new htmlclass;

	echo"<main style='column-count:2;'>";
	echo $html->put_upload_file("<b>ARCHIVOS&nbsp;CON&nbsp;MARCACIONES&nbsp;DE&nbsp;ASISTENCIAS</b>","userfile","","");
	echo $html->put_button_colum("&nbsp;","Importar Marcaciones &raquo;","return f_importar()");
	echo"</main>";

	echo"<main style='column-count:2;'>";
	echo $html->put_upload_file("<b>ARCHIVOS&nbsp;CON&nbsp;HORAS&nbsp;REMOTAS&nbsp;Y&nbsp;POR&nbsp;LICENCIAS</b>","userfile2","","");
	echo $html->put_button_colum("&nbsp;","Importar Horas Trabajadas &raquo;","return f_importar2()");
	echo"</main>";

echo "<input name='accion' type='hidden' id='accion' value=''>";

?>
<div id='cargadorvacio'></div>

	</form>
	</body>
</html>


    <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js" ></script>
    <script type="text/javascript" src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js" ></script>
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css" />

<!--<button type="button" class="btn btn-info push">Open Modal</button>-->
<div id="bookdetails" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Procesando...</h4>
      </div>
      <div class="modal-body" style="text-align: center;">
	      <div class="spinner" style="text-align: center;"></div>
<!--          <img src="http://conferoapp.com/icons/preloader.gif" class="progress"> -->
      </div>
    </div>
  </div>
</div>


<style>
.spinner {
	margin: auto;
  border: 4px solid rgba(0, 0, 0, 0.1);
  width: 100px;
  height: 100px;
  border-radius: 50%;
  border-left-color: #09f;

  animation: spin 1s ease infinite;
}
/* Safari */
@-webkit-keyframes spin {
  0% { -webkit-transform: rotate(0deg); }
  100% { -webkit-transform: rotate(360deg); }
}
@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

.progress {
  width: 150px;
  height: 150px;
}
</style>
