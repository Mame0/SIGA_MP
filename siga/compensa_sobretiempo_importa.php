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


		for ($fil=4;$fil<=15000;$fil++) {
			$codusua = $datahj1[$fil][2] ;
			$fechamrk = $datahj1[$fil][4] ;

			$nrohoras = $datahj1[$fil][21] ;
			$nroexpcea = $datahj1[$fil][22] ;
			$anoexpcea = $datahj1[$fil][23] ;

			$nrohoras=str_replace(".:",":",$nrohoras);
			if (strlen($nrohoras)==8) { $nrohoras=substr($nrohoras,0,5); }
			if (strlen($nrohoras)==19) { $nrohoras=substr($nrohoras,11,5); }

			$fecexp = explode("/", $fechamrk);
			if (strlen($fechamrk)==19) {
				$fecha = substr($fechamrk,0,10);
			} else {
				$fecha = $fecexp[2]."-". str_pad($fecexp[1], 2, "0", STR_PAD_LEFT) ."-". str_pad($fecexp[0], 2, "0", STR_PAD_LEFT);
			}

			if ($codusua=="") { break; }
			$codusua=str_pad($codusua, 8, "0", STR_PAD_LEFT);

			if (trim($nroexpcea)=="" || strlen($anoexpcea)>4 ) {

			} else {
				$result_depe=$Db->query("SELECT codi_pers, count( codi_pers ) AS cant FROM `mp_personal` where pers_dni='".$codusua."' ");
				$nroreg=$result_depe[0]['cant'];
				if ($nroreg!=0) {
					$codiper=$result_depe[0]['codi_pers'];

					$result_depe=$Db->query("SELECT count( * ) AS cant FROM `mp_horascompensa_sobretiempo` where sobr_personal=".$codiper." and sobr_fecha='".$fecha."' ");
					$cant=$result_depe[0]['cant'];
					if ($cant==0) {
							$result=$Db->insert('mp_horascompensa_sobretiempo',
							['sobr_personal'=>$codiper,'sobr_fecha'=>$fecha,'sobr_horas'=> $nrohoras,
							'sobr_expcea'=>$nroexpcea,'sobr_anocea'=>$anoexpcea ]);
					}
				}


			}

		}

		echo '<html><body>
				<form name="form" method=post action="compensa_sobretiempo_importa.php">
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
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
		<script>
			function f_importar() {
				event.preventDefault();

document.form.accion.value='1';
		setTimeout(function(){
		var fd = new FormData(document.getElementById("form"));
  $(".progress").show();
  $('#bookdetails').modal("show").on('shown.bs.modal', function() {

			$.ajax({
			  url: "compensa_sobretiempo_importa.php",
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
	<center><h2 style="color:#073A6B">IMPORTACION DE SOBRETIEMPOS DESDE ARCHIVO EXCEL</h2></center>
		<form id="form" name="form" method="post" action="compensa_sobretiempo_importa.php" enctype="multipart/form-data">

<?php
	$html=new htmlclass;

	echo"<main style='column-count:2;'>";
	echo $html->put_upload_file("<b>ARCHIVOS&nbsp;CON&nbsp;SOBRETIEMPOS&nbsp;PARA&nbsp;COMPENSAR</b>","userfile","","");
	echo $html->put_button_colum("&nbsp;","Importar Datos &raquo;","return f_importar()");
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
