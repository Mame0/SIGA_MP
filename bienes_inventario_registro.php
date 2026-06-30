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
			$result=$Db->update('mp_bienesinventario',['bien_codpatrimonial'=>$_POST['bien_codpatrimonial'], 'bien_correlativo'=>$_POST['bien_correlativo'], 'bien_descripcion'=>utf8_decode($_POST['bien_descripcion']),
			'bien_marca'=>utf8_decode($_POST['bien_marca']),'bien_modelo'=> utf8_decode($_POST['bien_modelo'] ),'bien_serie'=>utf8_decode($_POST['bien_serie']),
			'bien_tecnologia'=>$_POST['bien_tecnologia'],
			'bien_cantidad'=>$_POST['bien_cantidad'],'activo'=>$_POST['activo'] ]  , ['codi_bien'=>$_POST['codi_bien']]  );
		}
		else
		{
			$result=$Db->insert('mp_bienesinventario',['bien_codpatrimonial'=>$_POST['bien_codpatrimonial'], 'bien_correlativo'=>$_POST['bien_correlativo'], 'bien_descripcion'=>utf8_decode($_POST['bien_descripcion']),
			'bien_marca'=>utf8_decode($_POST['bien_marca']),'bien_modelo'=> utf8_decode($_POST['bien_modelo'] ),'bien_serie'=>utf8_decode($_POST['bien_serie']),
			'bien_tecnologia'=>$_POST['bien_tecnologia'],
			'bien_cantidad'=>$_POST['bien_cantidad'],'activo'=>$_POST['activo'] ]);
			$_POST['codi_bien']=$Db->lastInsertId();
		}

		echo"
			<!--<script>alert('".CONST_MENS_REG_OK."');</script>-->
                        <html><body>
                                <form name=\"form\" method=post action=\"bienes_inventario.php\">
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
	$result_documento=$Db->select('mp_bienesinventario', ['codi_bien'=>$_POST['codi_bien']], '', '', '');
	$_POST['bien_codpatrimonial']=$result_documento[0]['bien_codpatrimonial'];
	$_POST['bien_correlativo']=$result_documento[0]['bien_correlativo'];
	$_POST['bien_descripcion']=$result_documento[0]['bien_descripcion'];
	$_POST['bien_marca']=$result_documento[0]['bien_marca'];
	$_POST['bien_modelo']=utf8_encode( $result_documento[0]['bien_modelo'] );
	$_POST['bien_serie']=$result_documento[0]['bien_serie'];
	$_POST['bien_tecnologia']=$result_documento[0]['bien_tecnologia'];
	$_POST['bien_cantidad']=$result_documento[0]['bien_cantidad'];
	$_POST['activo']=$result_documento[0]['activo'];

?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8" />
		<title>Actualizaci&oacute;n de Bienes de Inventario</title>
		<link rel="stylesheet" href="css/forms_demanda.css" />
		<link rel="stylesheet" href="css/forms_foot.css" />
		<link rel="stylesheet" href="css/forms_column.css" />
		<SCRIPT language="JavaScript" src="js/cadenas.js"></SCRIPT>
		<SCRIPT language="JavaScript" src="js/denuncia.js"></SCRIPT>
		<script>
			function f_guardar()
			{
				if(document.form.bien_codpatrimonial.value=='')
				{
					alert('Ingrese Codigo Patrimonial');
					document.form.bien_codpatrimonial.focus();
					return false;
				}
				else
				{
					if(document.form.bien_descripcion.value=='')
					{
						alert('Ingrese Descripcion del bien');
						document.form.bien_descripcion.focus();
						return false;
					}
					else
					{
						if(confirm('Seguro que desea Guardar'))
						{
							document.form.guardar_personal.value='1';
							document.form.submit();
						}
						else
							return false;
					}
				}
			}
			function f_cancelar()
			{
				document.form.action='bienes_inventario.php';
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
		echo"Registrar Nuevo Bien";
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

	echo "<main style='column-count:2;'>";
	echo $html->put_title_demand("INGRESE LOS DATOS DEL BIEN");
	echo $html->put_text('text',"C&oacute;d.&nbsp;Patrimonial","",'bien_codpatrimonial',$_POST['bien_codpatrimonial'],'','10','style="max-width:150px;"');
	echo $html->put_text('text',"Correlativo","",'bien_correlativo',$_POST['bien_correlativo'],'','50','');
	echo "</main>";

	echo "<main style='column-count:2;'>";
	echo $html->put_text('text',"Descripci&oacute;n","Descripci&oacute;n del bien",'bien_descripcion',$_POST['bien_descripcion'],'','50','');
	echo $html->put_text('text',"Marca","",'bien_marca',$_POST['bien_marca'],'','20','');
	echo "</main>";

	$arra_options_tptecno[0]="<- Seleccione ->";
        $result=$Db->select('mp_bienestecnologias', '', '', '', ['tecno_descripcion'=>'ASC']);
        foreach($result as $rows)
                $arra_options_tptecno[$rows['tecno_id']]= $rows['tecno_descripcion'] ;


	echo "<main style='column-count:3;'>";
	echo $html->put_text('text',"Modelo","",'bien_modelo',$_POST['bien_modelo'],'','20','');
	echo $html->put_text('text',"Serie","",'bien_serie',$_POST['bien_serie'],'','20','');
	echo $html->put_select("Grupo&nbsp;Tecnolog&iacute;a",'bien_tecnologia',$arra_options_tptecno,$_POST['bien_tecnologia'],'');
	echo "</main>";


	$arra_options_dispo[1]="SI - Disponible";
	$arra_options_dispo[0]="NO - Transferido";


	$arra_options_activo[1]="SI";
	$arra_options_activo[0]="NO";
	echo "<main style='column-count:2;'>";
	//echo $html->put_text('text',"Cantidad","",'bien_cantidad',$_POST['bien_cantidad'],'','5','style="max-width:100px;"');
	echo $html->put_select("Disponible&nbsp;en&nbsp;almac&eacute;n",'bien_cantidad',$arra_options_dispo,$_POST['bien_cantidad'],"");

	echo $html->put_select("Operativo",'activo',$arra_options_activo,$_POST['activo'],"");
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
