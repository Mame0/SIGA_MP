<?
	require_once 'include/cabecera.php';
	require_once 'classes/Html.class.php';
	require_once 'classes/Db.class.php';
	$Db = new Db();

	$fdig=date("YmdHis");
	$_POST['freg_docu']=date("Y-m-d");
	
	if($_POST['guardar_personal'])
	{
	    $result=$Db->query("select codi_depe from mp_maes_personal where iden_pers='".$_POST['iden_remi']."'");
	    foreach($result as $rows)
		    $depe=$rows['codi_depe'];
		
		$fdig=date(YmdHis);
		if($_POST['iden_docu'])
		{
			$result=$Db->update('mp_notif_documentos',['cbar_docu'=>$_POST['cbar_docu'],'nume_docu'=>$_POST['nume_docu'],'iden_tipo'=>$_POST['iden_tipo'],'iden_remi'=>$_POST['iden_remi'],'depe_remi'=>$depe,'iden_dest'=>$_POST['iden_dest'],'nomb_dest'=>$_POST['nomb_dest'],'dire_dest'=>$_POST['dire_dest'],'freg_docu'=>$_POST['freg_docu'],'hreg_docu'=>$_POST['hreg_docu'],'remi_docu'=>$_POST['remi_docu'],'dest_frec'=>$_POST['dest_frec'],'esta_docu'=>$_POST['esta_docu'],'digi_docu'=>$_SESSION['iden_oper'],'fdig_docu'=>"$fdig"],['iden_docu'=>$_POST['iden_docu']]);
		}
		else
		{
			$result=$Db->insert('mp_notif_documentos',['cbar_docu'=>$_POST['cbar_docu'],'nume_docu'=>$_POST['nume_docu'],'iden_tipo'=>$_POST['iden_tipo'],'iden_remi'=>$_POST['iden_remi'],'depe_remi'=>$depe,'iden_dest'=>$_POST['iden_dest'],'nomb_dest'=>$_POST['nomb_dest'],'dire_dest'=>$_POST['dire_dest'],'freg_docu'=>$_POST['freg_docu'],'hreg_docu'=>$_POST['hreg_docu'],'remi_docu'=>$_POST['remi_docu'],'dest_frec'=>$_POST['dest_frec'],'esta_docu'=>$_POST['esta_docu'],'digi_docu'=>$_SESSION['iden_oper'],'fdig_docu'=>"$fdig"]);
			$_POST['codi_docu']=$Db->lastInsertId();
		}

		echo"
			<!--<script>alert('".CONST_MENS_REG_OK."');</script>-->
                        <html><body>
                                <form name=\"form\" method=post action=\"notif_registro.php\">
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
	$result_documento=$Db->select('mp_notif_documentos', ['iden_docu'=>$_POST['iden_docu']], '', '', '');
	$_POST['cbar_docu']=$result_documento[0]['cbar_docu'];
	$_POST['nume_docu']=$result_documento[0]['nume_docu'];
	$_POST['iden_tipo']=$result_documento[0]['iden_tipo'];
	$_POST['iden_remi']=$result_documento[0]['iden_remi'];
	$_POST['carg_remi']=$result_documento[0]['carg_remi'];
	$_POST['iden_dest']=$result_documento[0]['iden_dest'];
	$_POST['dire_dest']=$result_documento[0]['dire_dest'];
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
			    //if(document.form.codi_tema.selectedIndex=='0')
				if(document.form.cbar_docu.value=='')
				{
					alert('Ingrese Codigo de Barras');
					document.form.cbar_docu.focus();
					return false;
				}
				else
				{
					if(document.form.nume_docu.value=='')
					{
						alert('Ingrese Número de Documento');
						document.form.nume_docu.focus();
						return false;
					}
					else
					{
					    if(document.form.iden_tipo.selectedIndex=='0')
						{
							alert('Seleccione Tipo de Documento');
							document.form.iden_tipo.focus();
							return false;
						}
						else
						{
						    if(document.form.iden_remi.selectedIndex=='0')
			    			{
		    					alert('Seleccione Remitente');
	    						document.form.iden_remi.focus();
    							return false;
						    }
						    else
						    {
						        if(document.form.iden_dest.selectedIndex=='0')
			    			    {
    		    					alert('Seleccione Destinatario');
	    						    document.form.iden_dest.focus();
    							    return false;
						        }
						        else
						        {
					    		    if(confirm('Seguro que desea Registrar Documento?'))
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
			function f_cancelar()
			{
				document.form.action='notif_registro.php';
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
	<center><h3 style="color:#073A6B"><b>REGISTRO DE DOCUMENTOS INGRESADOS POR RECEPCIÓN<br>
<?
	if($_POST['codi_docu'])
		echo"Editar Informaci&oacute;n <BR>".$_POST['nomb_docu'];
	else
		echo"Crear Nuevo";
?>
	</b></h3></center>
		<form name="form" method="post" ENCTYPE="multipart/form-data">
			<input type=hidden name="guardar_personal">
			<input type=hidden name="codi_docu" value="<?=$_POST['codi_docu']?>">
			<input type=hidden name="busq_tipo" value="<?=$_POST['busq_tipo']?>">
			<input type=hidden name="busq_dato" value="<?=$_POST['busq_dato']?>">
			<input type=hidden name="busq_pagi_actu" value="<?=$_POST['busq_pagi_actu']?>">
			<main>
<?
	$html=new htmlclass;


	$arra_options_depe[0]="<- Seleccione ->";
	$result=$Db->select('ali_maes_pers_dependencia', '', '', '', ['x_nombre'=>'ASC']);
	foreach($result as $rows)
		$arra_options_depe[$rows['n_codigo']]=$rows['x_nombre'];

	$arra_options_carg[0]="<- Seleccione ->";
	$result=$Db->select('ali_maes_pers_cargo', '', '', '', ['x_nombre'=>'ASC']);
	foreach($result as $rows)
		$arra_options_carg[$rows['n_codigo']]=$rows['x_nombre'];
	
	$arra_options_dest[0]="<- Seleccione Destinatario ->";
	$arra_options_dest[-1]="<- Seleccione Destinatario ->";
	$result=$Db->query("select * from mp_notif_destinatario_frecuente where esta_dest=1 order by nomb_dest");
	foreach($result as $rows)
		$arra_options_dest[$rows['iden_dest']]=$rows['nomb_dest'].' - '.$rows['dire_dest'];

	$arra_options_remi[0]="<- Seleccione Remitente ->";
	$arra_options_remi[-1]="<- Seleccione Remitente ->";
	$result=$Db->query("select * from mp_maes_personal where codi_carg in (17,18,19,20) order by appa_pers,apma_pers,nomb_pers");
	foreach($result as $rows)
		$arra_options_remi[$rows['iden_pers']]=$rows['appa_pers'].' '.$rows['apma_pers'].', '.$rows['nomb_pers'];

	$arra_options_tipo[0]="<- Seleccione Documento ->";
        $result=$Db->select('mp_maes_notif_tdocumento', '', '', '', ['x_nombre'=>'ASC']);
        foreach($result as $rows)
                $arra_options_tipo[$rows['n_codigo']]=$rows['x_nombre'];
    
    $arra_options_esta[1]="Activo";
    $arra_options_esta[0]="Inactivo";

	echo $html->put_title_demand("Informaci&oacute;n B&aacute;sica");
	echo $html->put_text('text',"Código&nbsp;de&nbsp;Barras","Ingrese Código",'cbar_docu',$_POST['cbar_docu'],'','30','');
	echo $html->put_text('text',"Número","Ingrese Número",'nume_docu',$_POST['nume_docu'],'','20','');
	echo $html->put_select("Tipo&nbsp;Documento",'iden_tipo',$arra_options_tipo,$_POST['iden_tipo'],"");
	echo $html->put_title_demand("Remitente");
	echo $html->put_select_buscador("Nombre",'iden_remi',$arra_options_remi,$_POST['iden_remi'],"");
	echo $html->put_text('text',"Cargo","Ingrese Cargo",'carg_remi',$_POST['carg_remi'],'','100','');
	echo $html->put_title_demand("Destinatario");
	echo $html->put_select_buscador("Nombre",'iden_dest',$arra_options_dest,$_POST['iden_dest'],"");
	echo $html->put_text('text',"Dirección","",'dire_dest',$_POST['dire_dest'],'','100','');
	echo $html->put_title_demand("Estado");
	echo $html->put_select("Estado",'esta_docu',$arra_options_esta,$_POST['esta_docu'],"");
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
