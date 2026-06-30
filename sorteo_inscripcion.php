<?
	//require_once 'include/cabecera.php';
	require_once 'classes/Html.class.php';
	require_once 'classes/Db.class.php';
	$Db = new Db();

	$fdig=date("YmdHis");
	
	unset($_POST['cade_dni']);
	$result=$Db->select('mp_sorteo_participante', '', '', '');
	foreach($result as $rows)
		$_POST['cade_dni'].=",".$rows['nume_docu'];
	$_POST['cade_dni'].=",";
	
	$_POST['cade_dni']=",29709217,29704088,29724322,";
	
	if($_POST['guardar_personal'])
	{
		$fdig=date(YmdHis);
		if($_POST['codi_part'])
		{
			//$result=$Db->update('mp_jurisprudencia_documento',['nomb_docu'=>$_POST['nomb_docu'],'expe_docu'=>$_POST['expe_docu'],'codi_espe'=>$_POST['codi_espe'],'esta_docu'=>$_POST['esta_docu'],'digi_docu'=>$_SESSION['iden_oper'],'fdig_docu'=>"$fdig"],['codi_docu'=>$_POST['codi_docu']]);
		}
		else
		{
			$result=$Db->insert('mp_sorteo_participante',['nomb_part'=>$_POST['nomb_part'],'nume_docu'=>$_POST['nume_docu'],'codi_depe'=>$_POST['codi_depe'],'esta_part'=>'1','digi_part'=>$_SESSION['iden_oper'],'fdig_part'=>"$fdig"]);
			$_POST['codi_part']=$Db->lastInsertId();
		}
		echo"
			<!--<script>alert('".CONST_MENS_REG_OK."');</script>-->
                        <html><body>
                                <form name=\"form\" method=post action=\"urisprudencia_buscar.php\">
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
	$result_documento=$Db->select('mp_jurisprudencia_documento', ['codi_docu'=>$_POST['codi_docu']], '', '', '');
	$_POST['nomb_docu']=$result_documento[0]['nomb_docu'];
	$_POST['expe_docu']=$result_documento[0]['expe_docu'];
	$_POST['codi_espe']=$result_documento[0]['codi_espe'];
	$_POST['esta_docu']=$result_documento[0]['esta_docu'];
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
				if(document.form.nume_docu.value=='' || document.form.nume_docu.value.length<8)
				{
					alert('Ingrese Nro. de DNI correctamente');
					document.form.nume_docu.focus();
					return false;
				}
				else
				{
				    //alert(document.form.nume_docu.value.length);
				    if(document.form.cade_dni.value.indexOf(","+document.form.nume_docu.value+",")!==-1)
				    {
				        alert('Nro. de DNI ya fue registrado');
					    document.form.nume_docu.focus();
					    return false;
				    }
				    else
				    {
						if(document.form.nomb_part.value=='')
						{
							alert('Ingrese Apellidos y Nombres');
							document.form.nomb_part.focus();
							return false;
						}
						else
						{
						    if(document.form.codi_sede.selectedIndex=='0')
					        {
					            alert('Seleccione Sede');
					            document.form.codi_sede.focus();
					            return false;
					        }
					        else
					        {
					    		if(confirm('VERIFIQUE SU INFORMACION ANTES DE REGISTRARSE:\n\nDNI: '+document.form.nume_docu.value+'\nNOMBRE: '+document.form.nomb_part.value+'\nSEDE: '+document.form.codi_sede.options[document.form.codi_sede.selectedIndex].text+'\n\nDESEA GUARDAR?'))
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
			function f_cancelar()
			{
				document.form.action='sorteo_inscripcion.php';
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
	if($_POST['codi_docu'])
		echo"Editar Informaci&oacute;n Exp. ".$_POST['expe_docu'];
	else
		echo"<br>DÍA DE LA JUVENTUD 2022<BR>Registro de Participantes";
?>
	</h2></center>
		<form name="form" method="post" ENCTYPE="multipart/form-data">
			<input type=hidden name="guardar_personal">
			<input type=hidden name="codi_part" value="<?=$_POST['codi_part']?>">
			<input type=hidden name="cade_dni" value="<?=$_POST['cade_dni']?>">
			<main>
<?
	$html=new htmlclass;


	$arra_options_sede[0]="<- Seleccione ->";
	$result=$Db->select('mp_maes_sorteo_sedes', '', '', '', ['n_codigo'=>'ASC']);
	foreach($result as $rows)
		$arra_options_sede[$rows['n_codigo']]=$rows['x_nombre'];

	//echo $html->put_title_demand("Ingrese su Informaci&oacute;n");
	echo $html->put_text('text',"Nro.&nbsp;DNI","Ingrese Nro. DNI",'nume_docu',$_POST['nume_docu'],'','8',' onchange="return solonumeros(this.value)"');
	echo $html->put_text('text',"Apellidos&nbsp;y&nbsp;Nombres","Ingrese Apellidos y Nombres",'nomb_part',$_POST['nomb_part'],'','100','pattern="[A-Za-z ]+" title="Solo letras"');
	echo $html->put_select("Sede",'codi_sede',$arra_options_sede,$_POST['codi_sede'],"");
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
