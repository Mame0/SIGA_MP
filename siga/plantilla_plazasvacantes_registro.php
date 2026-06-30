<?
	require_once 'include/cabecera.php';
	require_once 'classes/Html.class.php';
	require_once 'classes/Db.class.php';
	$Db = new Db();

	$fdig=date("YmdHis");

	if($_POST['guardar_plazas'])
	{
		$fdig=date(YmdHis);
		if($_POST['autogen'])
		{
			$result=$Db->update('mp_plan_plazasvacantes',['mesplaza'=>$_POST['mesplaza'],
			'anoplaza'=>$_POST['anoplaza'],
			'meta'=> $_POST['meta'] ,
			'codcargo'=>$_POST['codcargo'],
			'nroplazas'=>$_POST['nroplazas'] ]  , ['autogen'=>$_POST['autogen']]  );
		}
		else
		{
			$result=$Db->insert('mp_plan_plazasvacantes',['mesplaza'=>$_POST['mesplaza'],
			'anoplaza'=>$_POST['anoplaza'],
			'meta'=> $_POST['meta'] ,
			'codcargo'=>$_POST['codcargo'],
			'nroplazas'=>$_POST['nroplazas'] ]);
			$_POST['autogen']=$Db->lastInsertId();
		}
		//unset($_POST['file_docu']);

		echo"
			<!--<script>alert('".CONST_MENS_REG_OK."');</script>-->
                        <html><body>
                                <form name=\"form\" method=post action=\"plantilla_plazasvacantes_registro.php\">
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
	$result_documento=$Db->select('mp_plan_plazasvacantes', ['autogen'=>$_POST['autogen']], '', '', '');
	$_POST['mesplaza']=$result_documento[0]['mesplaza'];
	$_POST['anoplaza']=$result_documento[0]['anoplaza'];
	$_POST['meta']=utf8_encode( $result_documento[0]['meta'] );
	$_POST['codcargo']=$result_documento[0]['codcargo'];
	$_POST['nroplazas']=$result_documento[0]['nroplazas'];

?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8" />
		<title>PLAZAS VACANTES</title>
		<link rel="stylesheet" href="css/forms_demanda.css" />
		<link rel="stylesheet" href="css/forms_foot.css" />
		<link rel="stylesheet" href="css/forms_column.css" />
		<SCRIPT language="JavaScript" src="js/cadenas.js"></SCRIPT>
		<SCRIPT language="JavaScript" src="js/denuncia.js"></SCRIPT>
		<script>
			function f_guardar()
			{
					if(document.form.mesplaza.value=='')
					{
						alert('Ingrese el mes');
						document.form.mesplaza.focus();
						return false;
					}
					if(document.form.anoplaza.value=='')
					{
						alert('Ingrese el año');
						document.form.anoplaza.focus();
						return false;
					}
					if(document.form.meta.value=='')
					{
						alert('Ingrese la meta');
						document.form.meta.focus();
						return false;
					}
					if(document.form.codcargo.value=='')
					{
						alert('Seleccione el cargo');
						document.form.codcargo.focus();
						return false;
					}
					if(document.form.nroplazas.value=='')
					{
						alert('Ingrese la cantida de plazas');
						document.form.nroplazas.focus();
						return false;
					}

							if(confirm('Seguro que desea Guardar'))
							{
								document.form.guardar_plazas.value='1';
								document.form.submit();
							}
							else
								return false;
			}
			function f_cancelar()
			{
				document.form.action='plantilla_plazasvacantes.php';
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
		echo"Editar Informaci&oacute;n - Plazas Vacantes "; //.$_POST['descripcionfecha'];
	else
		echo"REGISTRAR NUEVAS PLAZAS VACANTES";
?>
	</h2></center>
		<form name="form" method="post" ENCTYPE="multipart/form-data">
			<input type=hidden name="guardar_plazas">
			<input type=hidden name="autogen" value="<?=$_POST['autogen']?>">
			<input type=hidden name="busq_pagi_actu" value="<?=$_POST['busq_pagi_actu']?>">

<?
	$html=new htmlclass;

	echo "<main style='column-count:3;'>";
	echo $html->put_text('text',"Mes","",'mesplaza',$_POST['mesplaza'],'','2','style="max-width:50px;"');
	echo $html->put_text('text',"A&ntilde;o","",'anoplaza',$_POST['anoplaza'],'','4','style="max-width:80px;"');
	echo $html->put_text('text',"Meta","",'meta',$_POST['meta'],'','4','style="max-width:80px;"');
	echo "</main>";
	echo "<main style='column-count:2;'>";
	$arra_options_carg[0]="<- Seleccione ->";
        $result=$Db->select('mp_plan_escalaremunerativa', '', '', '', ['n_codigo'=>'ASC']);
        foreach($result as $rows)
                $arra_options_carg[$rows['n_codigo']]= $rows['esccargo'] ;
		echo $html->put_select("Cargo",'codcargo',$arra_options_carg,$_POST['codcargo'],'style="max-width:600px;"');
	echo $html->put_text('text',"Nro&nbsp;Plazas","",'nroplazas',$_POST['nroplazas'],'','3','style="max-width:80px;"');
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
