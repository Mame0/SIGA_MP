<?

	require_once 'include/cabecera.php';
	require_once 'classes/Html.class.php';
	require_once 'classes/Db.class.php';
	$Db = new Db();
	if(!isset($_POST['busc_pagi_actu']))
		$_POST['busc_pagi_actu']=1;

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
			function f_exportar()
			{
				event.preventDefault();
				var nroplantilla = document.form.nromes.value;
				//window.location.href = "proyeccionplantilla.php?nropla="+nroplantilla;
				//mostrarespera("Exportando Maestro de Tarifas...");
				setTimeout(function(){
					$("#cargadorvacio").load("proyeccionplantilla.php?nropla="+nroplantilla, function(){
						window.location.href = "proyeccionplantilla.php?rt=temp/&dwld=proyeccion.xlsx&nwfile=proyeccion_mes_"+ nroplantilla +".xlsx";
						//ocultarespera();
					});
				}, 200);


			}
			function f_exportar2() {
				event.preventDefault();
				window.location.href = "plantilla276.php";
			}
			function f_exportar3() {
				event.preventDefault();
				window.location.href = "plantilla728.php";
			}


			function ajustar_altura()
                        {
                                parent.document.getElementById('body_iframe').height=parent.window.innerHeight-80;
                        }
                        ajustar_altura();
		</script>
	</head>
	<body style="margin-bottom: 30px;">
	<center><h2 style="color:#073A6B">PROYECCION DE SALDOS DL 276/728 PARA PLANILLA</h2></center>
		<form name="form" method="post">
			<input type=hidden name="codi_bien">
			<input type=hidden name="busc_pagi_actu" value="<?=$_POST['busc_pagi_actu']?>">
<?
	$html=new htmlclass;

	$arra_options_mes[0]="<- Seleccione ->";
	$arra_options_mes[1]="Enero";
	$arra_options_mes[2]="Febrero";
	$arra_options_mes[3]="Marzo";
	$arra_options_mes[4]="Abril";
	$arra_options_mes[5]="Mayo";
	$arra_options_mes[6]="Junio";
	$arra_options_mes[7]="Julio";
	$arra_options_mes[8]="Agosto";
	$arra_options_mes[9]="Septiembre";
	$arra_options_mes[10]="Octubre";
	$arra_options_mes[11]="Noviembre";
	$arra_options_mes[12]="Diciembre";


	echo"<main style='column-count:2;'>";
	echo $html->put_title_demand("GENERA PROYECCION DE SALDOS A PLANTILLAS EXCEL");
	echo $html->put_select("Mes",'nromes',$arra_options_mes,$_POST['nromes'],"");
	echo $html->put_button_colum("&nbsp;","Exporta Proyeccion de Saldos &raquo;","return f_exportar()");
	echo"</main>";

	echo"<main style='column-count:2;'>";
	echo $html->put_title_demand("GENERA PROYECCION POR DECRETO LEY 276 / 728 A PLANTILLA EXCEL");
	echo $html->put_button_colum("&nbsp;","Exporta Proyeccion DL 276 &raquo;","return f_exportar2()");
	echo $html->put_button_colum("&nbsp;","Exporta Proyeccion DL 728 &raquo;","return f_exportar3()");
	echo"</main>";


?>
<div id='cargadorvacio'></div>

<center>
	</form>
	</body>
</html>
