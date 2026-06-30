<?
	require_once 'include/cabecera.php';
	require_once 'classes/Html.class.php';
	require_once 'classes/Db.class.php';
	$Db = new Db();
	
	$fdig=date("YmdHis");
	
	if($_POST['guardar_personal'])
	{
	    if($_POST['fech_noti'])
	        $_POST['fech_noti']=substr($_POST['fech_noti'],0,4).substr($_POST['fech_noti'],5,2).substr($_POST['fech_noti'],8,2);
		if($_POST['codi_noti'])
		{
			$result=$Db->update('mp_noticias',['titu_noti'=>$_POST['titu_noti'],'subt_noti'=>$_SESSION['subt_noti'],'cont_noti'=>$_POST['cont_noti'],'fech_noti'=>$_POST['fech_noti'],'digi_noti'=>$_SESSION['iden_oper'],'fdig_noti'=>"$fdig",'esta_noti'=>$_POST['esta_noti']],['codi_noti'=>$_POST['codi_noti']]);
		}
		else
		{
			$result=$Db->insert('mp_noticias',['titu_noti'=>$_POST['titu_noti'],'subt_noti'=>$_SESSION['subt_noti'],'cont_noti'=>$_POST['cont_noti'],'fech_noti'=>$_POST['fech_noti'],'digi_noti'=>$_SESSION['iden_oper'],'fdig_noti'=>"$fdig",'esta_noti'=>$_POST['esta_noti']]);
			$_POST['codi_part']=$Db->lastInsertId();
		}
		echo"
			<script>alert('".CONST_MENS_REG_OK."');</script>
                        <html><body>
                                <form name=\"form\" method=post action=\"imagen_noticias.php\">
					
                                </form>
                                <script>
                                        document.form.submit();
                                </script>
                        </body></html>
		";

	}
	$result_documento=$Db->select('mp_noticias', ['codi_noti'=>$_POST['codi_noti']], '', '', '');
	$_POST['titu_noti']=$result_documento[0]['titu_noti'];
	$_POST['subt_noti']=$result_documento[0]['subt_noti'];
	$_POST['fech_noti']=substr($result_documento[0]['fech_noti'],0,4).'-'.substr($result_documento[0]['fech_noti'],4,2).'-'.substr($result_documento[0]['fech_noti'],6,2);
	$_POST['cont_noti']=$result_documento[0]['cont_noti'];
	$_POST['esta_noti']=$result_documento[0]['esta_noti'];
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
				if(document.form.titu_noti.value=='')
				{
					alert('Ingrese Titulo');
					document.form.titu_noti.focus();
					return false;
				}
				else
				{
				    if(document.form.fech_noti.value=='')
				    {
				        alert('Ingrese Fecha');
					    document.form.fech_noti.focus();
					    return false;
				    }
				    else
				    {
						if(document.form.cont_noti.value=='')
						{
							alert('Ingrese contenido');
							document.form.cont_noti.focus();
							return false;
						}
						else
						{
					    		if(confirm('Seguro que desea guardar?'))
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
			function f_cancelar()
			{
				document.form.action='imagen_noticias.php';
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
	<center><font style="color:#073A6B;font-weight: bold;"><font style="font-size: 20;">REGISTRAR NUEVA NOTICIA</font><br>USUARIO: <?=$_SESSION['logi_oper']?></font></center>
		<form name="form" method="post" ENCTYPE="multipart/form-data">
			<input type=hidden name="guardar_personal">
			<input type=hidden name="regresar_reporte" value="<?=$_POST['regresar_reporte']?>">
			<input type=hidden name="codi_noti" value="<?=$_POST['codi_noti']?>">
			<main>
<?
	$html=new htmlclass;
	
	if(!$_POST['ubig_difu'])
	    $_POST['ubig_difu']='01';

	$arra_options_prov[0]="<- Seleccione ->";
	$result=$Db->query("select distinct cdep,cpro,cdis,prov,dist from ubig_reni WHERE cdep='04' AND cpro<>'00' AND cdis<>'00' order by cpro,cdis");
	foreach($result as $rows)
	{
	    $c=$rows['cdep'].$rows['cpro'].$rows['cdis'];
		$arra_options_prov[$c]=$rows['prov']." - ".$rows['dist'];
	}

	$arra_options_tdif[0]="<- Seleccione ->";
	$result=$Db->select('mp_maes_elecciones_difusion', '', '', '', ['n_codigo'=>'ASC']);
	foreach($result as $rows)
		$arra_options_tdif[$rows['n_codigo']]=$rows['x_nombre'];
	
	//if(!$_POST['fech_noti'])
	    $_POST['fech_noti']=date("Y-m-d");

	echo $html->put_title_demand("Ingrese InformaciÃ³n");
	echo $html->put_text('text',"T&iacute;tulo","Ingrese T&iacute;tulo",'titu_noti',$_POST['titu_noti'],'','200','');
	echo $html->put_text('date',"Fecha","Ingrese Fecha",'fech_noti',$_POST['fech_noti'],'','200','');
	//echo $html->put_text('text',"Subt¨ªtulo","Ingrese Subt¨ªtulo",'subt_noti',$_POST['subt_noti'],'','200','');
	echo $html->put_select_estado(CONST_SUBTITLE_STATE,'esta_noti',$_POST['esta_noti'],'Activa','Inactiva');
    echo $html->put_title_demand("Contenito de la Noticia");
    echo $html->put_textarea("Contenido",'cont_noti',$_POST['cont_noti'],'style="height: 100px;"');
    echo $html->put_title_demand("Subir Imagen");
	echo $html->put_upload_file("",'file_docu','','');
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
