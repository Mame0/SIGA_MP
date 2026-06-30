<?
	require_once 'include/cabecera.php';
	require_once 'classes/Html.class.php';
	require_once 'classes/Db.class.php';
	$Db = new Db();

	$fdig=date("YmdHis");

	if($_POST['guardar_fecha'])
	{
		$fdig=date(YmdHis);
		if($_POST['autogen'])
		{
			$result=$Db->update('mp_asistencia_feccompensables',['fechacompensable'=>$_POST['fechacompensable'],
			'canthoras'=>$_POST['canthoras'],
			'descripcionfecha'=> $_POST['descripcionfecha'] ,
			'fechainicialcompensa'=>$_POST['fechainicialcompensa'],
			'fechafinalcompensa'=>$_POST['fechafinalcompensa'] ]  , ['autogen'=>$_POST['autogen']]  );
		}
		else
		{
			$result=$Db->insert('mp_asistencia_feccompensables',['fechacompensable'=>$_POST['fechacompensable'],
			'canthoras'=>$_POST['canthoras'],
			'descripcionfecha'=> $_POST['descripcionfecha'] ,
			'fechainicialcompensa'=>$_POST['fechainicialcompensa'],
			'fechafinalcompensa'=>$_POST['fechafinalcompensa'] ]);
			$_POST['autogen']=$Db->lastInsertId();
		}
		unset($_POST['file_docu']);

		echo"
			<!--<script>alert('".CONST_MENS_REG_OK."');</script>-->
                        <html><body>
                                <form name=\"form\" method=post action=\"asistencia_fechacompensar_registro.php\">
					<input type=hidden name=\"busq_tipo\" value=\"".$_POST['busq_tipo']."\">
					<input type=hidden name=\"busq_dato\" value=\"".$_POST['busq_dato']."\">
					<input type=hidden name=\"busq_pagi_actu\" value=\"".$_POST['busq_pagi_actu']."\">
                                </form>
                                <script>
                                        document.form.submit();
                                </script>
                        </body></html>
		";

	}
	$result_documento=$Db->select('mp_asistencia_feccompensables', ['autogen'=>$_POST['autogen']], '', '', '');
	$_POST['fechacompensable']=$result_documento[0]['fechacompensable'];
	$_POST['canthoras']=$result_documento[0]['canthoras'];
	$_POST['descripcionfecha']= $result_documento[0]['descripcionfecha'] ;
	$_POST['fechainicialcompensa']=$result_documento[0]['fechainicialcompensa'];
	$_POST['fechafinalcompensa']=$result_documento[0]['fechafinalcompensa'];

?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8" />
		<title>SIOJAlimentos</title>
		<link rel="stylesheet" href="css/forms_demanda.css" />
		<link rel="stylesheet" href="css/forms_foot.css" />
		<link rel="stylesheet" href="css/forms_column.css" />
		<SCRIPT language="JavaScript" src="js/cadenas.js"></SCRIPT>
		<SCRIPT language="JavaScript" src="js/denuncia.js"></SCRIPT>
		<script>
			function f_guardar()
			{
					if(document.form.fechacompensable.value=='')
					{
						alert('Ingrese Fecha que sera compensada');
						document.form.fechacompensable.focus();
						return false;
					}
					if(document.form.descripcionfecha.value=='')
					{
						alert('Ingrese Descripcion de la fecha');
						document.form.descripcionfecha.focus();
						return false;
					}
					if(document.form.canthoras.value=='')
					{
						alert('Ingrese la cantidad de horas a compensar');
						document.form.canthoras.focus();
						return false;
					}
					if(document.form.fechainicialcompensa.value=='')
					{
						alert('Ingrese la Fecha Inicio Compensacion');
						document.form.fechainicialcompensa.focus();
						return false;
					}
					if(document.form.fechafinalcompensa.value=='')
					{
						alert('Ingrese la Fecha Final Compensacion');
						document.form.fechafinalcompensa.focus();
						return false;
					}

							if(confirm('Seguro que desea Guardar'))
							{
								document.form.guardar_fecha.value='1';
								document.form.submit();
							}
							else
								return false;
			}
			function f_cancelar()
			{
				document.form.action='asistencia_fechascompensar.php';
				document.form.submit();
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
	if($_POST['autogen'])
		//echo"Editar Informaci&oacute;n Exp. ".$_POST['expe_docu'];
		echo"Editar Informaci&oacute;n - Fechas a COmpensar Asistencias ".$_POST['descripcionfecha'];
	else
		echo"REGISTRAR NUEVA FECHA A COMPENSAR ASISTENCIA";
?>
	</h2></center>
		<form name="form" method="post" ENCTYPE="multipart/form-data">
			<input type=hidden name="guardar_fecha">
			<input type=hidden name="autogen" value="<?=$_POST['autogen']?>">
			<input type=hidden name="busq_pagi_actu" value="<?=$_POST['busq_pagi_actu']?>">

<?
	$html=new htmlclass;

	echo "<main style='column-count:2;'>";
	echo $html->put_text('date',"Fecha&nbsp;a&nbsp;Compensar","aaaa-mm-dd",'fechacompensable',$_POST['fechacompensable'],'','10','style="max-width:200px;"');
	echo $html->put_text('text',"Descripci&oacute;n","",'descripcionfecha',$_POST['descripcionfecha'],'','50','style="max-width:600px;"');
	echo "</main>";
	echo "<main style='column-count:3;'>";
	echo $html->put_text('text',"Horas&nbsp;a&nbsp;compensar","",'canthoras',$_POST['canthoras'],'','6','style="max-width:100px;"');
	echo $html->put_text('date',"Fecha&nbsp;Inicio&nbsp;Compensaci&oacute;n","aaaa-mm-dd",'fechainicialcompensa',$_POST['fechainicialcompensa'],'','10','style="max-width:200px;"');
	echo $html->put_text('date',"Fecha&nbsp;Fin&nbsp;Compensaci&oacute;n","aaaa-mm-dd",'fechafinalcompensa',$_POST['fechafinalcompensa'],'','10','style="max-width:200px;"');
	echo "</main>";


	echo $html->put_separator_demand("30");

                echo"
                        <div align=center class=\"foot\">
                                <center>
                                <div align=center class=\"foot2\">
                                        <div class=\"div_button_foot\" style=\"\">
                                                <button class=\"button_foot\" onclick=\"f_cancelar()\">&laquo; Cancelar</button>
                                        </div>
                                        <div class=\"div_button_foot\"><center>
                                                <button class=\"button_foot\" onclick=\"return f_guardar()\">Guardar &raquo;</button>
                                        </div>
                                </div>
                        </div>
                ";
?>
<center>
	</form>
	</body>
</html>
