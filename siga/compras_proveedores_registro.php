<?
	require_once 'include/cabecera.php';
	require_once 'classes/Html.class.php';
	require_once 'classes/Db.class.php';
	$Db = new Db();

	if($_GET['flag_prov'])
	    $_POST['codi_prov']=$_SESSION['iden_oper'];

	$fdig=date("YmdHis");

	if($_POST['guardar_personal'])
	{
		$fdig=date(YmdHis);
		if($_POST['codi_prov'])
		{
			$result=$Db->update('mp_comp_proveedores',['nomb_prov'=>$_POST['nomb_prov'],'nomb_come'=>$_POST['nomb_come'],'nruc_prov'=>$_POST['nruc_prov'],
			'dire_prov'=>$_POST['dire_prov'],'mail_prov'=>$_POST['mail_prov'],'fono_prov'=>$_POST['fono_prov'],'repr_legal'=>$_POST['repr_legal'],
			'esta_prov'=>$_POST['esta_prov'],
			'cont_prov'=>$_POST['cont_prov'],'rnp_prov'=>$_POST['rnp_prov'],'mype_prov'=>$_POST['mype_prov'],'tipo_rubr'=>$_POST['tipo_rubr'],
			'codi_rubr'=>$_POST['codi_rubr'],'deta_acti'=>$_POST['deta_acti'],'digi_prov'=>$_SESSION['iden_oper'],'fdig_prov'=>"$fdig"],['codi_prov'=>$_POST['codi_prov']]);
		}
		else
		{
			$result=$Db->insert('mp_comp_proveedores',['nomb_prov'=>$_POST['nomb_prov'],'nomb_come'=>$_POST['nomb_come'],'nruc_prov'=>$_POST['nruc_prov'],
			'dire_prov'=>$_POST['dire_prov'],'mail_prov'=>$_POST['mail_prov'],'fono_prov'=>$_POST['fono_prov'],'repr_legal'=>$_POST['repr_legal'],
			'esta_prov'=>$_POST['esta_prov'],
			'cont_prov'=>$_POST['cont_prov'],'rnp_prov'=>$_POST['rnp_prov'],'mype_prov'=>$_POST['mype_prov'],'tipo_rubr'=>$_POST['tipo_rubr'],
			'codi_rubr'=>$_POST['codi_rubr'],'deta_acti'=>$_POST['deta_acti'],'digi_prov'=>$_SESSION['iden_oper'],'fdig_prov'=>"$fdig"]);
			$_POST['codi_prov']=$Db->lastInsertId();
		}

		if($_FILES['file_brochure']['name'] && $_FILES['file_brochure']['size']>0) {
			if(strstr($_FILES['file_brochure']['type'],"pdf") || strstr($_FILES['file_brochure']['type'],"jpg")) {
				$ext0 = explode(".", $_FILES['file_brochure']['name']);
				$extension = end($ext0);
				move_uploaded_file($_FILES['file_brochure']['tmp_name'],"prov_brochure/prov_". $_POST['codi_prov'] .".". $extension);
			}
		}
		unset($_POST['file_brochure']);


		echo"
			<!--<script>alert('".CONST_MENS_REG_OK."');</script>-->
                        <html><body>
                                <form name=\"form\" method=post action=\"compras_proveedores.php\">
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
	$result_documento=$Db->select('mp_comp_proveedores', ['codi_prov'=>$_POST['codi_prov']], '', '', '');
	$_POST['nomb_prov']=$result_documento[0]['nomb_prov'];
	$_POST['nomb_come']=$result_documento[0]['nomb_come'];
	$_POST['nruc_prov']=$result_documento[0]['nruc_prov'];
	$_POST['dire_prov']=$result_documento[0]['dire_prov'];
	$_POST['mail_prov']=$result_documento[0]['mail_prov'];
	$_POST['fono_prov']=$result_documento[0]['fono_prov'];
	$_POST['repr_legal']=$result_documento[0]['repr_legal'];
	$_POST['cont_prov']=$result_documento[0]['cont_prov'];
	$_POST['rnp_prov']=$result_documento[0]['rnp_prov'];
	$_POST['mype_prov']=$result_documento[0]['mype_prov'];
	$_POST['tipo_rubr']=$result_documento[0]['tipo_rubr'];
	$_POST['codi_rubr']=$result_documento[0]['codi_rubr'];
	$_POST['deta_acti']=$result_documento[0]['deta_acti'];
	$_POST['esta_prov']=$result_documento[0]['esta_prov'];
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
				if(document.form.nomb_prov.value=='') {
					alert('Ingrese Nombre');
					document.form.nomb_prov.focus();
					return false;
				}
				if(document.form.nruc_prov.value=='') {
					alert('Ingrese Nro. de RUC');
					document.form.nruc_prov.focus();
					return false;
				}
				if(document.form.tipo_rubr.selectedIndex=='0') {
					alert('Seleccione Rubro');
					document.form.codi_rubr.focus();
					return false;
				}
				if(document.form.codi_rubr.selectedIndex=='0') {
					alert('Seleccione Rubro');
					document.form.codi_rubr.focus();
					return false;
				}

				if(confirm('Seguro que desea Guardar')) {
					document.form.guardar_personal.value='1';
					document.form.submit();
				} else {
					return false;
				}


			}
			function f_cancelar()
			{
				document.form.action='compras_proveedores.php';
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
	if($_POST['codi_prov'])
		echo"Editar Informaci&oacute;n Proveedor [<u>".$_POST['nomb_prov']."</u>]";
	else
		echo"Crear Nuevo Proveedor";
?>
	</h2></center>
		<form name="form" method="post" ENCTYPE="multipart/form-data">
			<input type=hidden name="guardar_personal">
			<input type=hidden name="codi_prov" value="<?=$_POST['codi_prov']?>">
			<input type=hidden name="busq_tipo" value="<?=$_POST['busq_tipo']?>">
			<input type=hidden name="busq_dato" value="<?=$_POST['busq_dato']?>">
			<input type=hidden name="busq_pagi_actu" value="<?=$_POST['busq_pagi_actu']?>">
<?
	$html=new htmlclass;


	$arra_options_rubro[0]="<- Seleccione ->";
	$result=$Db->select('mp_maes_comp_rubro', '', '', '', ['x_nombre'=>'ASC']);
	foreach($result as $rows)
		$arra_options_rubro[$rows['n_codigo']]=$rows['x_nombre'];

	echo "<main>";
	echo $html->put_title_demand("Informaci&oacute;n B&aacute;sica");
	echo $html->put_text('text',"RUC","Ingrese Nro. RUC",'nruc_prov',$_POST['nruc_prov'],'','11','');
	echo $html->put_text('text',"Nombre&nbsp;/&nbsp;Raz&oacute;n&nbsp;Social","Ingrese Nombre",'nomb_prov',$_POST['nomb_prov'],'','50','');
	echo $html->put_text('text',"Nombre&nbsp;Comercial","Nombre Comercial",'nomb_come',$_POST['nomb_come'],'','50','');
	echo "</main>";

	echo "<main>";
	echo $html->put_text('text',"Direcci&oacute;n","Ingrese Dirección",'dire_prov',$_POST['dire_prov'],'','50','');
	echo $html->put_text('text',"Correo&nbsp;Electr&oacute;nico","E-Mail",'mail_prov',$_POST['mail_prov'],'','50','');
	echo $html->put_text('text',"Tel&eacute;fono&nbsp;/&nbsp;Celular","Tel&eacute;fono&nbsp;/&nbsp;Celular",'fono_prov',$_POST['fono_prov'],'','25','');
	echo "</main>";

	echo "<main>";
	echo $html->put_text('text',"Representante&nbsp;Legal","Ingrese Rep. Legal",'repr_legal',$_POST['repr_legal'],'','100','');
	echo $html->put_text('text',"Nombre&nbsp;de&nbsp;Contacto","Ingrese Nombre Contacto",'cont_prov',$_POST['cont_prov'],'','100','');
	echo $html->put_select_estado(CONST_SUBTITLE_STATE,'esta_prov',$_POST['esta_prov'],CONST_OPTION_ENABLE,CONST_OPTION_DISABLE);
	echo "</main>";


	$arra_options_rnp[0]="NO";
	$arra_options_rnp[1]="SI";
	echo "<main>";
	echo $html->put_title_demand("Informaci&oacute;n Adicional");
	echo $html->put_select("Tiene&nbsp;RNP",'rnp_prov',$arra_options_rnp,$_POST['rnp_prov'],"");
	echo $html->put_select("Es&nbsp;MYPE",'mype_prov',$arra_options_rnp,$_POST['mype_prov'],"");
	echo "</main>";

	$arra_options_tpru[0]="<- Seleccione ->";
	$arra_options_tpru[1]="BIENES";
	$arra_options_tpru[2]="SERVICIOS";
	$arra_options_tpru[3]="BIENES Y SERVICIOS";
	echo "<main>";
	echo $html->put_select("Tipo&nbsp;Rubro",'tipo_rubr',$arra_options_tpru,$_POST['tipo_rubr'],"");
	echo $html->put_select("Rubro&nbsp;/&nbsp;Actividad&nbsp;comercial",'codi_rubr',$arra_options_rubro,$_POST['codi_rubr'],"");
	echo $html->put_textarea("Detalle&nbsp;de&nbsp;actividad&nbsp;comercial",'deta_acti',$_POST['deta_acti'],'style="height: 120px;"');
	echo "</main>";
	echo "<main>";
	echo $html->put_upload_file("BROCHURE",'file_brochure','','');
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
