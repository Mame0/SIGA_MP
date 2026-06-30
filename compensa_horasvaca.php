<?
	require_once 'include/cabecera.php';
	require_once 'classes/Html.class.php';
	require_once 'classes/Db.class.php';
	$Db = new Db();

	$fdig=date("YmdHis");

	if($_POST['guardar_personal'])
	{
		$fdig=date(YmdHis);
		if($_POST['codi_bien'])
		{
			$result=$Db->update('mp_horascompensa_vacaciones',['vaca_personal'=>$_POST['codi_pers'], 'vaca_expcea'=>$_POST['vaca_expcea'], 'vaca_anocea'=>$_POST['vaca_anocea'], 'vaca_resolucion'=>utf8_decode($_POST['vaca_resolucion']),
			'vaca_fecemision'=>utf8_decode($_POST['vaca_fecemision']),'vaca_periodo'=> utf8_decode($_POST['vaca_periodo'] ),'vaca_fechaini'=>utf8_decode($_POST['vaca_fechaini']),
			'vaca_fechafin'=>$_POST['vaca_fechafin'], 'vaca_incluyesabdom'=>$_POST['vaca_incluyesabdom'] ]  , ['vaca_autogen'=>$_POST['codi_vaca']]  );
		}
		else
		{
			$result=$Db->insert('mp_horascompensa_vacaciones',['vaca_personal'=>$_POST['codi_pers'], 'vaca_expcea'=>$_POST['vaca_expcea'], 'vaca_anocea'=>$_POST['vaca_anocea'], 'vaca_resolucion'=>utf8_decode($_POST['vaca_resolucion']),
			'vaca_fecemision'=>utf8_decode($_POST['vaca_fecemision']),'vaca_periodo'=> utf8_decode($_POST['vaca_periodo'] ),'vaca_fechaini'=>utf8_decode($_POST['vaca_fechaini']),
			'vaca_fechafin'=>$_POST['vaca_fechafin'], 'vaca_incluyesabdom'=>$_POST['vaca_incluyesabdom'] ]);
			$_POST['vaca_autogen']=$Db->lastInsertId();
		}

		echo"
			<!--<script>alert('".CONST_MENS_REG_OK."');</script>-->
                        <html><body>
                                <form name=\"form\" method=post action=\"compensa_horasvaca.php\">
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
	/*
	$result_documento=$Db->select('mp_horascompensa_vacaciones', ['codi_bien'=>$_POST['codi_bien']], '', '', '');
	$_POST['bien_codpatrimonial']=$result_documento[0]['bien_codpatrimonial'];
	$_POST['bien_correlativo']=$result_documento[0]['bien_correlativo'];
	$_POST['bien_descripcion']=$result_documento[0]['bien_descripcion'];
	$_POST['bien_marca']=$result_documento[0]['bien_marca'];
	$_POST['bien_modelo']=utf8_encode( $result_documento[0]['bien_modelo'] );
	$_POST['bien_serie']=$result_documento[0]['bien_serie'];
	$_POST['bien_tecnologia']=$result_documento[0]['bien_tecnologia'];
	$_POST['bien_cantidad']=$result_documento[0]['bien_cantidad'];
	$_POST['activo']=$result_documento[0]['activo'];
	*/
?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8" />
		<title>Registro de Compensaci&oacute;n por Vacaciones</title>
		<link rel="stylesheet" href="css/forms_demanda.css" />
		<link rel="stylesheet" href="css/forms_foot.css" />
		<link rel="stylesheet" href="css/forms_column.css" />
		<SCRIPT language="JavaScript" src="js/cadenas.js"></SCRIPT>
		<SCRIPT language="JavaScript" src="js/denuncia.js"></SCRIPT>
		<script>
			function f_guardar() {
				if (document.getElementById("codi_pers").value=="0") {
					alert ("SELECCIONE USUARIO DE LA LISTA");
					return false;
				}
				if(document.form.vaca_expcea.value=='') {
					alert('Ingrese Expediente CEA');
					document.form.vaca_expcea.focus();
					return false;
				}
				if(document.form.vaca_anocea.value=='') {
					alert('Ingrese Año CEA');
					document.form.vaca_anocea.focus();
					return false;
				}
				if(document.form.vaca_resolucion.value=='') {
					alert('Ingrese Resolucion');
					document.form.vaca_resolucion.focus();
					return false;
				}
				if(document.form.vaca_fecemision.value=='') {
					alert('Ingrese Fecha de Emision');
					document.form.vaca_fecemision.focus();
					return false;
				}
				if(document.form.vaca_periodo.value=='') {
					alert('Ingrese Periodo Vacacional');
					document.form.vaca_periodo.focus();
					return false;
				}
				if(document.form.vaca_fechaini.value=='') {
					alert('Ingrese Fecha de Inicio');
					document.form.vaca_fechaini.focus();
					return false;
				}
				if(document.form.vaca_fechafin.value=='') {
					alert('Ingrese Fecha de Termino');
					document.form.vaca_fechafin.focus();
					return false;
				}

						if(confirm('Seguro que desea Guardar'))
						{
							document.form.guardar_personal.value='1';
							document.form.submit();
						}
						else
							return false;

			}
			function f_cancelar()
			{
				document.form.action='compensa_horasvaca.php';
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
	if($_POST['codi_bien'])
		echo"Editar Informaci&oacute;n - Bienes Inventario ".$_POST['bien_codpatrimonial'];
	else
		echo"REGISTRAR EXPEDIENTE POR VACACIONES";
?>
	</h2></center>
		<form name="form" method="post" ENCTYPE="multipart/form-data" autocomplete="off">
			<input type=hidden name="guardar_personal">
			<input type=hidden name="codi_bien" value="<?=$_POST['codi_bien']?>">
			<input type=hidden name="busq_tipo" value="<?=$_POST['busq_tipo']?>">
			<input type=hidden name="busq_dato" value="<?=$_POST['busq_dato']?>">
			<input type=hidden name="busq_pagi_actu" value="<?=$_POST['busq_pagi_actu']?>">

<?
	$html=new htmlclass;

	$arra_options_pers[0]="<- Seleccione ->";
	$result=$Db->select('mp_personal', '', '', '', ['pers_apepat'=>'ASC', 'pers_apemat'=>'ASC', 'pers_nombres'=>'ASC']);
	foreach($result as $rows)
		$arra_options_pers[$rows['codi_pers']]= $rows['pers_apepat']." ".$rows['pers_apemat']." ".$rows['pers_nombres'] ;

	echo "<main style='column-count:1;'>";
	echo $html->put_title_demand("INGRESE LOS DATOS");
	echo $html->put_select("Usuario:",'codi_pers',$arra_options_pers,"","");
	echo "</main>";

	echo "<main style='column-count:2;'>";
	echo $html->put_text('text',"Nro.&nbsp;Expediente&nbsp;CEA","",'vaca_expcea',$_POST['vaca_expcea'],'','20','');
	echo $html->put_text('text',"A&ntilde;o&nbsp;Expediente&nbsp;CEA","",'vaca_anocea',$_POST['vaca_anocea'],'','4','style="max-width:150px;"');
	echo "</main>";

	echo "<main style='column-count:2;'>";
	echo $html->put_text('text',"Resoluci&oacute;n","",'vaca_resolucion',$_POST['vaca_resolucion'],'','20','');
	echo $html->put_text('date',"Fec.&nbsp;Emisi&oacute;n","",'vaca_fecemision',$_POST['vaca_fecemision'],'','10','style="max-width:200px;"');
	echo "</main>";

	echo "<main style='column-count:4; column-width:0px;'>";
	echo $html->put_text('text',"Periodo","",'vaca_periodo',$_POST['vaca_periodo'],'','20','');
	echo $html->put_text('date',"Fecha&nbsp;Inicio","",'vaca_fechaini',$_POST['vaca_fechaini'],'','10','style="max-width:200px;"');
	echo $html->put_text('date',"Fecha&nbsp;Termino","",'vaca_fechafin',$_POST['vaca_fechafin'],'','10','style="max-width:200px;"');

	$arra_options_incl[1]="SI";
	$arra_options_incl[0]="NO";
	echo $html->put_select("Incluye&nbsp;S&aacute;bados&nbsp;y&nbsp;Domingos:",'vaca_incluyesabdom',$arra_options_incl,"","");
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
