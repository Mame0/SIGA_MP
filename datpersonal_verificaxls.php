<?
	require_once 'include/cabecera.php';
	require_once 'classes/Html.class.php';
	require_once 'classes/Db.class.php';
	$Db = new Db();

	$fdig=date("YmdHis");


require_once "spreadsheets/vendor/autoload.php";

	if($_GET['ini']) {
		$result=$Db->update('mp_personal', ['activo'=>0], ['activo'=>1]);
		exit();
	}

	if($_POST['guardar_personal'])
	{
		$fdig=date(YmdHis);
		if($_FILES['file_docu']['name'] AND $_FILES['file_docu']['size']>0) {
			move_uploaded_file($_FILES['file_docu']['tmp_name'],"temp/".$_FILES['file_docu']['name']);
		}
		unset($_POST['file_docu']);

		$nomxls="temp/".$_FILES['file_docu']['name'];
		$spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($nomxls);
		$worksheet = $spreadsheet->getActiveSheet();
		for ($fil=2;$fil<=1000;$fil++) {
			$dni=$worksheet->getCell("BJ".$fil)->getValue() ;

			if ($dni=="") {break;}

			$apenom=utf8_decode ( $worksheet->getCell("B".$fil)->getValue() );
			$lug = strpos($apenom,",");
			$apellidos=substr($apenom,0,$lug);
			$nombre=substr($apenom,($lug+1),100); //nombres
			$lug = strpos($apellidos," ");
			$apepat=substr($apellidos,0,$lug);//apellido paterno
			$apemat=substr($apellidos,($lug+1),100);//apellido materno

			$fecingre=$worksheet->getCell("l".$fil)->getValue() ;
			$fecing= date('Y-m-d', PhpOffice\PhpSpreadsheet\Shared\Date::excelToTimestamp($fecingre));

			$meta=$worksheet->getCell("F".$fil)->getValue() ;

			$result_depe=$Db->query("SELECT count( * ) AS cant FROM `mp_personal` where pers_dni='".$dni."' ");
			$cant=$result_depe[0]['cant'];
			if ($cant==0) {
				$result=$Db->insert('mp_personal',
				['pers_apepat'=>$apepat, 'pers_apemat'=>$apemat, 'pers_nombres'=>$nombre,
				'pers_dni'=>$dni, 'meta'=>$meta, 'pers_fecing'=>$fecing, 'activo'=>1 ]);
			} else {
				$result=$Db->update('mp_personal',
				['meta'=>$meta, 'pers_fecing'=>$fecing,'activo'=>1 ], ['pers_dni'=>$dni]);
			}
		}
		unlink ("temp/".$_FILES['file_docu']['name']);

		echo"
                        <html><body>
                                <form name=\"form\" method=post action=\"datpersonal_verificaxls.php\">
                                </form>
                                <script>
                                        document.form.submit();
                                </script>
                        </body></html>
		";
		exit();
	}

?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8" />
		<title></title>
		<link rel="stylesheet" href="css/forms_demanda.css" />
		<link rel="stylesheet" href="css/forms_foot.css" />
		<link rel="stylesheet" href="css/forms_column.css" />
		<SCRIPT language="JavaScript" src="js/cadenas.js"></SCRIPT>
		<SCRIPT language="JavaScript" src="js/denuncia.js"></SCRIPT>

		<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>

		<script>
			function f_guardar() {
							if(confirm('Desea continuar y procesar la informacion del archivo seleccionado?'))
							{
								document.form.guardar_personal.value='1';
								document.form.submit();
							}
							else
								return false;
			}
			function inicializa() {
				if(confirm('TODO EL PERSONAL SERA COLOCADO COMO INACTIVO, DESEA CONTINUAR?')==true) {

					$.ajax({
						type: "GET",
						url: "datpersonal_verificaxls.php",
						cache: false,
						data: { ini: 1 }
					}).done(function( respuesta2 ) {
						var html2=respuesta2.trim();
						alert ("TODOS LOS USUARIOS FUERON COLOCADOS COMO INACTIVOS");
					});
					return false;

				}
			}
			function ajustar_altura()
                        {
                                parent.document.getElementById('body_iframe').height=parent.window.innerHeight-80;
                        }
                        ajustar_altura();
		</script>
	</head>
	<body style="margin-bottom: 30px;">
	<center><h2 style="color:#073A6B">
<?
	if($_POST['codi_docu'])
		echo"Editar Informaci&oacute;n Exp. ".$_POST['expe_docu'];
	else
		echo "VERIFICAR INFORMACION DE PERSONAL ACTIVO DESDE ARCHIVO XLS";
?>
	</h2></center>
		<form name="form" method="post" ENCTYPE="multipart/form-data">
			<input type=hidden name="guardar_personal">
			<main>
<?
	$html=new htmlclass;

	echo $html->put_title_demand("Verificacion de Informacion de Personal");
	echo"</main><main>";
	echo $html->put_title_demand("Subir Archivo Excel a Verificar");
	echo $html->put_upload_file("",'file_docu','','');
	echo"</main>";

	echo $html->put_separator_demand("30");

                echo"
                        <div align=center class=\"foot\">
                                <center>
                                <div align=center class=\"foot2\">
                                        <div class=\"div_button_foot\" style=\"\">
                                                <button class=\"button_foot\" style='background-color:#B40404;' onclick=\"inicializa()\">COLOCAR INACTIVO A TODOS</button>
                                        </div>
                                        <div class=\"div_button_foot\"><center>
                                                <button class=\"button_foot\" onclick=\"return f_guardar()\">Subir Archivo y Verificar &raquo;</button>
                                        </div>
                                </div>
                        </div>
                ";
?>
<div id='cargadorvacio'></div>
<center>
	</form>
	</body>
</html>
