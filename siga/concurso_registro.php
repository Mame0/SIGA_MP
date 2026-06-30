<?
	require_once 'include/cabecera.php';
	require_once 'classes/Html.class.php';
	require_once 'classes/Db.class.php';
	$Db = new Db();
	
	$fdig=date("YmdHis");

	if($_POST['guardar_postulante'])
	{
		$fdig=date(YmdHis);
		//$_POST['esta_post']=1;
		if($_POST['codi_post'])
		{
			$result=$Db->update("mp_concurso_postulantes",['codi_plaz'=>$_POST['codi_plaz'],'docu_post'=>$_POST['docu_post'],'appa_post'=>$_POST['appa_post'],'apma_post'=>$_POST['apma_post'],'nomb_post'=>$_POST['nomb_post'],'tdoc_post'=>$_POST['tdoc_post'],'regi_post'=>$_POST['regi_post'],'mail_post'=>$_POST['mail_post'],'celu_post'=>$_POST['celu_post'],'digi_post'=>$_SESSION['iden_oper'],'fdig_post'=>$fdig,'esta_post'=>$_POST['esta_post']],['codi_post'=>$_POST['codi_post']]);
		}
		else
		{
			$result=$Db->insert("mp_concurso_postulantes",['codi_plaz'=>$_POST['codi_plaz'],'docu_post'=>$_POST['docu_post'],'appa_post'=>$_POST['appa_post'],'apma_post'=>$_POST['apma_post'],'nomb_post'=>$_POST['nomb_post'],'tdoc_post'=>$_POST['tdoc_post'],'regi_post'=>$_POST['regi_post'],'mail_post'=>$_POST['mail_post'],'celu_post'=>$_POST['celu_post'],'digi_post'=>$_SESSION['iden_oper'],'fdig_post'=>$fdig,'esta_post'=>$_POST['esta_post']]);
			$_POST['codi_post']=$Db->lastInsertId();
		}
		
		echo"
			<!--<script>alert('".CONST_MENS_REG_OK."');</script>-->
                        <html><body>
                                <form name=\"form\" method=post action=\"concurso_postulante.php\">
					<input type=hidden name=\"codi_plaz\" value=\"".$_POST['codi_plaz']."\">
					<input type=hidden name=\"busq_tipo\" value=\"".$_POST['busq_tipo']."\">
					<input type=hidden name=\"busq_dato\" value=\"".$_POST['busq_dato']."\">
					<input type=hidden name=\"codi_exam\" value=\"".$_POST['codi_exam']."\">
					<input type=hidden name=\"busq_pagi_actu\" value=\"".$_POST['busq_pagi_actu']."\">
					<input type=hidden name=\"codi_form\" value=\"".$_POST['codi_form']."\">
                                </form>
                                <script>
                                        document.form.submit();
                                </script>
                        </body></html>
		";

	}
	$result_postonal=$Db->select("mp_concurso_postulantes", ['codi_post'=>$_POST['codi_post']], '', '', '');
	$_POST['codi_plaz']=$result_postonal[0]['codi_plaz'];
	$_POST['docu_post']=$result_postonal[0]['docu_post'];
	$_POST['appa_post']=$result_postonal[0]['appa_post'];
	$_POST['apma_post']=$result_postonal[0]['apma_post'];
	$_POST['nomb_post']=$result_postonal[0]['nomb_post'];
	$_POST['tdoc_post']=$result_postonal[0]['tdoc_post'];
	$_POST['regi_post']=$result_postonal[0]['regi_post'];
	$_POST['mail_post']=$result_postonal[0]['mail_post'];
	$_POST['celu_post']=$result_postonal[0]['celu_post'];
	$_POST['regi_asis']=$result_postonal[0]['regi_asis'];
	$_POST['fdig_asis']=$result_postonal[0]['fdig_asis'];
	$_POST['esta_post']=$result_postonal[0]['esta_post'];
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
			function f_guardar_postulante()
			{
				if(document.form.docu_post.value=='')
				{
					alert('Ingrese Nro. de DNI');
					document.form.docu_post.focus();
					return false;
				}
				else
				{
					if(document.form.appa_post.value=='')
					{
						alert('Ingrese Apellido Paterno');
						document.form.appa_post.focus();
						return false;
					}
					else
					{
						if(document.form.nomb_post.value=='')
						{
							alert('Ingrese Nombres');
							document.form.nomb_post.focus();
							return false;
						}
						else
						{
							if(confirm('Seguro que desea Guardar'))
							{
								document.form.guardar_postulante.value='1';
								document.form.submit();
							}
							else
								return false;
						}
					}
				}
			}
			function f_cancelar_documento()
			{
				document.form.action='concurso_postulante.php';
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
	<center><h2 style="color:#bb0400">
<?
	if($_POST['codi_post'])
		echo"Editar Informaci&oacute;n de Postulante<BR>".$_POST['appa_post']." ".$_POST['apma_post'].", ".$_POST['nomb_post'];
	else
		echo"Crear Nuevo Postulante";
?>
	</h2></center>
		<form name="form" method="post" ENCTYPE="multipart/form-data">
			<input type=hidden name="guardar_postulante">
			<input type=hidden name="codi_plaz" value="<?=$_POST['codi_plaz']?>">
			<input type=hidden name="codi_post" value="<?=$_POST['codi_post']?>">
			<input type=hidden name="busq_tipo" value="<?=$_POST['busq_tipo']?>">
			<input type=hidden name="busq_dato" value="<?=$_POST['busq_dato']?>">
			<input type=hidden name="codi_depe" value="<?=$_POST['codi_depe']?>">
			<input type=hidden name="busq_pagi_actu" value="<?=$_POST['busq_pagi_actu']?>">
			<input type=hidden name="codi_form" value="<?=$_POST['codi_form']?>">
			<main>
<?
	$html=new htmlclass;


	$result=$Db->select('mp_concurso_examen','','','',['fech_exam'=>'ASC']);
	$arra_options_exam[0]="<- ".CONST_OPTION_SELECT." ->";
	foreach ($result as $rows)
		$arra_options_exam[$rows['codi_exam']]="[".$rows['fech_exam']."] ";
	
	$arra_options_regi[0]="<- Seleccione ->";
	$result=$Db->select('mp_maes_concurso_regimen', '', '', '', ['x_nombre'=>'ASC']);
	foreach($result as $rows)
		$arra_options_regi[$rows['n_codigo']]=$rows['x_nombre'];
	
	$result=$Db->select('mp_concurso_proceso','','','',['codi_proc'=>'ASC']);
	$arra_options_proc[0]="<- ".CONST_OPTION_SELECT." ->";
	foreach ($result as $rows)
		$arra_options_proc[$rows['codi_proc']]=$arra_options_exam[$rows['codi_exam']]." ".$arra_options_regi[$rows['regi_proc']]." Nro. ".$rows['nume_proc']."-".$rows['anno_proc'];
	
	$result=$Db->select('mp_concurso_plazas','','','',['codi_plaz'=>'ASC']);
	$arra_options_plaz[0]="<- ".CONST_OPTION_SELECT." ->";
	foreach ($result as $rows)
		$arra_options_plaz[$rows['codi_plaz']]=$arra_options_proc[$rows['codi_proc']]." - ".$rows['nomb_plaz'];

	echo $html->put_title_demand("Informaci&oacute;n Personal");
	echo $html->put_text('text',"DNI","Ingrese Nro. DNI",'docu_post',$_POST['docu_post'],'','8','');
	echo $html->put_text('text',"Tipo&nbsp;Documento","Ingrese Tipo",'tdoc_post',$_POST['tdoc_post'],'','100','');
	echo $html->put_select("Plaza",'codi_plaz',$arra_options_plaz,$_POST['codi_plaz'],"");
	echo"</main><main>";
	echo $html->put_text('text',"Apellido&nbsp;Paterno","Ingrese Apellido Paterno",'appa_post',$_POST['appa_post'],'','100','');
	echo $html->put_text('text',"Apellido&nbsp;Paterno","Ingrese Apellido Materno",'apma_post',$_POST['apma_post'],'','100','');
	echo $html->put_text('text',"Nombres","Ingrese Nombres",'nomb_post',$_POST['nomb_post'],'','50','');
	echo"</main><main>";
	echo $html->put_text('text',"R&eacute;gimen","Ingrese R&eacute;gimen",'regi_post',$_POST['regi_post'],'','50','');
	echo $html->put_text('text',"Correo","Ingrese correo",'mail_post',$_POST['mail_post'],'','50','');
	echo $html->put_text('text',"Celular","Ingrese celular",'celu_post',$_POST['celu_post'],'','50','');
	echo"</main><main>";
	echo $html->put_title_demand("Registro de Asistencia");
	echo $html->put_select_estado("Asistencia",'regi_asis',$_POST['regi_asis'],"SI","NO");
	echo $html->put_text('text',"Fecha&nbsp;Registro","Ingrese fecha y hora",'fdig_asis',$_POST['fdig_asis'],'','50','');
	echo"</main><main>";
	echo $html->put_title_demand("Estado del Postulante");
	echo $html->put_select_estado("Estado",'esta_post',$_POST['esta_post'],"Activo","Inactivo");
	
	echo"</main>";

	echo $html->put_separator_demand("30");

                echo"
                        <div align=center class=\"foot\">
                                <center>
                                <div align=center class=\"foot2\">
                                        <div class=\"div_button_foot\" style=\"\">
                                                <button class=\"button_foot\" onclick=\"f_cancelar_documento()\">&laquo; Cancelar</button>
                                        </div>
                                        <div class=\"div_button_foot\"><center>
                                                <button class=\"button_foot\" onclick=\"return f_guardar_postulante()\">Guardar &raquo;</button>
                                        </div>
                                </div>
                        </div>
                ";
?>
<center>
	</form>
	</body>
</html>
