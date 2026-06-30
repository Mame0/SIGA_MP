<?
	require_once 'include/cabecera.php';
	require_once 'classes/Html.class.php';
	require_once 'classes/Db.class.php';
	$Db = new Db();

	$fdig=date("YmdHis");

	if($_POST['guardar_personal'])
	{
		$fdig=date(YmdHis);
		//$_POST['esta_pers']=1;
		if($_POST['codi_pers'])
		{
			$result=$Db->update('mp_fsup_mpar_ingreso',['carp_depe'=>$_POST['carp_depe'],'carp_anno'=>$_POST['carp_anno'],'carp_caso'=>$_POST['carp_caso'],'carp_cuad'=>$_POST['carp_cuad'],'orig_depe'=>$_POST['orig_depe'],'orig_fisc'=>$_POST['orig_fisc'],'orig_tipo'=>$_POST['orig_tipo'],'ingr_foli'=>$_POST['ingr_foli'],'ingr_obse'=>$_POST['ingr_obse'],'ingr_esta'=>$_POST['ingr_esta']],['codi_ingr'=>$_POST['codi_ingr']]);
			//$result=$Db->update('mp_fotocheck_personal',['appe_pers'=>$_POST['appe_pers']],['codi_pers'=>$_POST['codi_pers']]);
		//die("dstos:".$_POST['nomb_pers']."-".$_POST['codi_pers']);
		}
		else
		{
			$result=$Db->insert('mp_fsup_mpar_ingreso',['carp_depe'=>$_POST['carp_depe'],'carp_anno'=>$_POST['carp_anno'],'carp_caso'=>$_POST['carp_caso'],'carp_cuad'=>$_POST['carp_cuad'],'orig_depe'=>$_POST['orig_depe'],'orig_fisc'=>$_POST['orig_fisc'],'orig_tipo'=>$_POST['orig_tipo'],'ingr_foli'=>$_POST['ingr_foli'],'ingr_obse'=>$_POST['ingr_obse'],'ingr_esta'=>$_POST['ingr_esta'],'ingr_digi'=>$_SESSION['iden_oper'],'ingr_fdig'=>$fdig]);
			$_POST['codi_ingr']=$Db->lastInsertId();
		}

		echo"
			<script>alert('".CONST_MENS_REG_OK."');</script>
                        <html><body>
                                <form name=\"form\" method=post action=\"fsup_mpar_registro.php\">
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
	$result_personal=$Db->select('mp_fotocheck_personal', ['codi_pers'=>$_POST['codi_pers']], '', '', '');
	$_POST['ndni_pers']=$result_personal[0]['ndni_pers'];
	$_POST['appe_pers']=$result_personal[0]['appe_pers'];
	$_POST['nomb_pers']=$result_personal[0]['nomb_pers'];
	$_POST['codi_depe']=$result_personal[0]['codi_depe'];
	$_POST['codi_carg']=$result_personal[0]['codi_carg'];
	$_POST['codi_regi']=$result_personal[0]['codi_regi'];
	$_POST['habi_impr']=$result_personal[0]['habi_impr'];
	$_POST['esta_pers']=$result_personal[0]['esta_pers'];
	*/
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
				if(document.form.carp_depe.value=='')
				{
					alert('Ingrese Cod. de Dependencia');
					document.form.carp_depe.focus();
					return false;
				}
				else
				{
					if(document.form.carp_anno.selectedIndex=='0')
					{
						alert('Ingrese Año');
						document.form.carp_anno.focus();
						return false;
					}
					else
					{
						if(document.form.carp_caso.value=='')
						{
							alert('Ingrese Nro. Caso');
							document.form.carp_caso.focus();
							return false;
						}
						else
						{
							if(document.form.orig_depe.selectedIndex=='0')
							{
								alert('Ingrese Dependencia de Origen');
								document.form.orig_depe.focus();
								return false;
							}
							else
							{
								if(document.form.orig_fisc.selectedIndex=='0')
								{
									alert('Ingrese Fiscal');
									document.form.orig_fisc.focus();
									return false;
								}
								else
								{
									if(document.form.orig_tipo.selectedIndex=='0')
									{
										alert('Seleccione Tipo');
										document.form.orig_tipo.focus();
										return false;
									}
									else
									{
									    if(document.form.ingr_foli.value=='')
						                {
							                alert('Ingrese Folios');
						                	document.form.ingr_foli.focus();
							                return false;
						                }
						                else
						                {
											if(confirm('Seguro que desea Guardar?'))
											{
												document.form.guardar_personal.value='1';
												document.form.submit();
											}
											else
												return false;
						                }
									}
								}
							}
						}
					}
				}
			}
			function f_cancelar()
			{
				document.form.action='personal_listad.php';
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
	if($_POST['codi_pers'])
		echo"Editar Informaci&oacute;n de Personal<BR>".$_POST['apel_pers']." ".$_POST['nomb_pers'];
	else
		echo"Fiscalías Superiores - Mesa de Partes";
?>
	</h2></center>
		<form name="form" method="post" ENCTYPE="multipart/form-data">
			<input type=hidden name="guardar_personal">
			<input type=hidden name="codi_pers" value="<?=$_POST['codi_pers']?>">
			<input type=hidden name="busq_tipo" value="<?=$_POST['busq_tipo']?>">
			<input type=hidden name="busq_dato" value="<?=$_POST['busq_dato']?>">
			<input type=hidden name="busq_pagi_actu" value="<?=$_POST['busq_pagi_actu']?>">
			<main>
<?
	$html=new htmlclass;


	$arra_options_depe[0]="<- Seleccione ->";
	$result=$Db->select('mp_maes_fotocheck_dependencia', '', '', '', ['x_nombre'=>'ASC']);
	foreach($result as $rows)
		$arra_options_depe[$rows['n_codigo']]=utf8_decode(utf8_encode($rows['x_nombre']));

	$arra_options_fisc[0]="<- Seleccione ->";
	$result=$Db->select('mp_fotocheck_personal', ['codi_carg'=>'18'], '', '', ['appe_pers'=>'ASC']);
	foreach($result as $rows)
		$arra_options_fisc[$rows['codi_pers']]=utf8_encode($rows['appe_pers'].", ".$rows['nomb_pers']);
		
	$arra_options_tipo=$Db->get_options("mp_maes_fsup_mpar_tipo");

	$arra_options_anno[0]="<- Seleccione ->";
	for($a=2000;$a<=date("Y");$a++)
		$arra_options_anno[$a]=$a;

	echo $html->put_title_demand("Número de Carpeta");
	echo $html->put_text('number',"Código&nbsp;de&nbsp;Dependencia","Ingrese Dependencia",'carp_depe',$_POST['carp_depe'],'','8','');
	echo $html->put_select("Año",'carp_anno',$arra_options_anno,date("Y"),"");
	echo $html->put_text('number',"Número&nbsp;de&nbsp;Caso","Ingrese Nro. de Caso",'carp_caso',$_POST['carp_caso'],'','50','');
	echo"</main><main>";
	echo $html->put_text('number',"Cuaderno","Ingrese Cuaderno",'carp_cuad',$_POST['carp_cuad'],'','50','');
	echo $html->put_title_demand("Informaci&oacute;n de  Origen");
	echo $html->put_select("Dependencia",'orig_depe',$arra_options_depe,$_POST['orig_depe'],"");
	echo $html->put_select("Fiscal",'orig_fisc',$arra_options_fisc,$_POST['orig_fisc'],"");
	echo $html->put_select("Tipo",'orig_tipo',$arra_options_tipo,$_POST['orig_tipo'],"");
	echo $html->put_title_demand("Información Adicional");
	echo $html->put_text('number',"Folios","Ingrese Folios",'ingr_foli',$_POST['ingr_foli'],'','8','');
	echo $html->put_textarea("Observaciones","Ingrese Observaciones",'ingr_obse',$_POST['ingr_obse'],'','8','');
	//echo $html->put_select_estado("Habilitado&nbsp;para&nbsp;Imprimir",'habi_impr',$_POST['habi_impr'],"SI","NO");
	//echo $html->put_select_estado(CONST_SUBTITLE_STATE,'esta_pers',$_POST['esta_pers'],CONST_OPTION_ENABLE,CONST_OPTION_DISABLE);
	//echo"</main><main>";
	//echo $html->put_title_demand("Foto del Trabajador");
	//echo $html->put_upload_file("Foto&nbsp;<a href=\"classes/TCPDF/examples/fotos/".$_POST['ndni_pers'].".jpg\" target=\"blank\">Ver</a>",'file_pers','','');
	echo"</main>";

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
