<?
	require_once 'include/cabecera.php';
	require_once 'classes/Html.class.php';
	require_once 'classes/Db.class.php';
	$Db = new Db();
	if(!isset($_POST['busc_pagi_actu']))
		$_POST['busc_pagi_actu']=1;

	$fdig=date("YmdHis");
	
	if($_POST['elim_docu'])
	{
	    $result=$Db->query("delete from mp_notif_guia_detalle_temporal where iden_sesi='".session_id()."' AND iden_docu='$_POST[elim_docu]'");
	}
	if($_POST['codi_docu'])
	{
	    $result=$Db->query("select * from mp_notif_documentos where esta_docu=1 AND cbar_docu='$_POST[codi_docu]'");
	    if(count($result)>0)
	    {
	        foreach($result as $rows)
		        $_POST['iden_docu']=$rows['iden_docu'];
	        $result=$Db->query("select * from mp_notif_guia_detalle where esta_deta=1 AND iden_docu='$_POST[iden_docu]'");
	        if(count($result)>0)
	        {
	            echo"<script>alert('ERROR: Documento ya fue asignado');</script>";
	            unset($_POST[codi_docu],$_POST[iden_docu]);
	        }
	        else
	        {
	            $result=$Db->query("select * from mp_notif_guia_detalle_temporal where esta_temp=1 AND iden_sesi='".session_id()."' AND iden_docu='$_POST[iden_docu]'");
    	        if(count($result)>0)
    	        {
    	            echo"<script>alert('ERROR: Documento esta siendo asignado en la presente guia');</script>";
    	            unset($_POST[codi_docu],$_POST[iden_docu]);
    	        }
    	        else
    	        {
    	            $result=$Db->query("insert into mp_notif_guia_detalle_temporal values('".session_id()."','$_POST[iden_docu]','".$_SESSION['iden_oper']."','$fdig','1')");
    	            unset($_POST[codi_docu],$_POST[iden_docu]);
    	        }
	        }
	    }
	    else
	    {
	        echo"<script>alert('ERROR: Código de Documento no existe');</script>";
	        unset($_POST[codi_docu]);
	    }
	}

	if($_POST['guardar_personal'])
	{
		$fdig=date(YmdHis);
		$_POST['anno_guia']=date(Y);
		$_POST['fgen_guia']=date("Y-m-d");
		
		//se llena la cabecera
		$result=$Db->query("select MAX(nume_guia) nume_guia from mp_notif_guia_cabecera where esta_guia=1 AND anno_guia='".$_POST['anno_guia']."'");
		foreach($result as $rows)
		        $_POST['nume_guia']=$rows['nume_guia']+1;
		$result=$Db->insert('mp_notif_guia_cabecera',['nume_guia'=>$_POST['nume_guia'],'anno_guia'=>$_POST['anno_guia'],'fgen_guia'=>$_POST['fgen_guia'],'ugen_guia'=>$_SESSION['iden_oper'],'iden_mens'=>$_POST['iden_mens'],'iden_zona'=>$_POST['iden_zona'],'esta_guia'=>'1','digi_guia'=>$_SESSION['iden_oper'],'fdig_guia'=>"$fdig"]);
		$_POST['iden_guia']=$Db->lastInsertId();
		
		//se llena el detalle
		$hoy=date("Ymd");
    	$result=$Db->query("select * from mp_notif_guia_detalle_temporal where esta_temp=1 AND iden_sesi='".session_id()."' AND fdig_temp='$hoy'");
    	$orde=0;
    	foreach($result as $rows)
    	{
//iden_deta	iden_guia	iden_docu	fgen_deta	ugen_deta	orde_deta	reas_deta	cbar_deta	digi_deta	fdig_deta	esta_deta
            $orde++;
			$result_detalle=$Db->insert('mp_notif_guia_detalle',['iden_guia'=>$_POST['iden_guia'],'iden_docu'=>$rows['iden_docu'],'fgen_deta'=>$_POST['fgen_guia'],'ugen_deta'=>$_SESSION['iden_oper'],'orde_deta'=>$orde,'esta_deta'=>'1','digi_deta'=>$_SESSION['iden_oper'],'fdig_deta'=>"$fdig"]);
		    $result_detalle=$Db->query("update mp_notif_documentos set fasi_docu='$_POST[fgen_guia]' where iden_docu=$rows[iden_docu]");
    	}
		$result=$Db->query("delete from mp_notif_guia_detalle_temporal where (iden_sesi='".session_id()."' AND fdig_temp='$hoy') OR fdig_temp<'$hoy'");

		echo"
			<!--<script>alert('".CONST_MENS_REG_OK."');</script>-->
                        <html><body>
                                <form name=\"form\" method=post action=\"notif_guia_asignacion.php\">
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
				if(document.form.iden_mens.selectedIndex=='0')
				{
					alert('ERROR: Seleccione Mensajero');
					document.form.iden_mens.focus();
					return false;
				}
				else
				{
					if(document.form.iden_zona.selectedIndex=='0')
					{
						alert('ERROR: Seleccione Zona');
						document.form.iden_zona.focus();
						return false;
					}
					else
					{
						if(document.form.cant_docu.value>0)
						{
							if(confirm('Seguro que generar nueva guía?'))
					    	{
					    		document.form.guardar_personal.value='1';
					    		document.form.submit();
					    	}
					    	else
					    		return false;
						}
						else
						{
					    	alert('ERROR: Falta agregar documentos');
							document.form.codi_docu.focus();
							return false;
						}
					}
				}
			}
			function f_agregar()
			{
				document.form.action='';
				document.form.submit();
			}
			function f_cancelar()
			{
				document.form.action='notif_guia_asignacion.php';
				document.form.submit();
			}
			function f_borrar(iden,codi,nume,tipo,remi)
			{
			    if(confirm('Seguro que desea eliminar documento?\nCODIGO: '+codi+'\nNUMERO: '+nume+'\nTIPO: '+tipo+'\nREMITENTE: '+remi))
			    {
			        document.form.elim_docu.value=iden;
		    	    document.form.action='';
			    	document.form.submit();
			    }
			}
			function ajustar_altura()
                        {
                                parent.document.getElementById('body_iframe').height=parent.window.innerHeight-80;
                        }
                        ajustar_altura();
		</script>
	</head>
	<body style="margin-bottom: 30px;">
	<center><h3 style="color:#073A6B"><b>ASIGNACIÓN DE DOCUMENTOS<br>
<?
	if($_POST['codi_docu'])
		echo"Editar Informaci&oacute;n <BR>".$_POST['nomb_docu'];
	else
		echo"Guía de Asignación";
?>
	</b></h3></center>
		<form name="form" method="post" ENCTYPE="multipart/form-data">
			<input type=hidden name="guardar_personal">
			<input type=hidden name="elim_docu">
			<input type=hidden name="busq_tipo" value="<?=$_POST['busq_tipo']?>">
			<input type=hidden name="busq_dato" value="<?=$_POST['busq_dato']?>">
			<input type=hidden name="busq_pagi_actu" value="<?=$_POST['busq_pagi_actu']?>">
			<main>
<?
	$html=new htmlclass;

	$arra_options_zona[0]="<- Seleccione Zona ->";
	$arra_options_zona[-1]="<- Seleccione Zona ->";
    $result=$Db->select('mp_notif_zonas', '', '', '', ['nomb_zona'=>'ASC']);
    foreach($result as $rows)
        $arra_options_zona[$rows['iden_zona']]=$rows['nomb_zona'];
    
    $arra_options_mens[0]="<- Seleccione Mensajero ->";
    $arra_options_mens[-1]="<- Seleccione Mensajero ->";
    $result=$Db->query("select * from mp_maes_personal where codi_carg in (5) order by appa_pers,apma_pers,nomb_pers");
    foreach($result as $rows)
            $arra_options_mens[$rows['iden_pers']]=$rows['appa_pers'].' '.$rows['apma_pers'].', '.$rows['nomb_pers'];
    
    $result=$Db->select('mp_maes_notif_tdocumento', '', '', '', ['x_nombre'=>'ASC']);
    foreach($result as $rows)
        $arra_options_tipo[$rows['n_codigo']]=$rows['x_nombre'];
    
    $result=$Db->query("select * from mp_maes_personal where codi_carg in (17,18,19,20)");
    foreach($result as $rows)
            $arra_options_remi[$rows['iden_pers']]=$rows['appa_pers'].' '.$rows['apma_pers'].', '.$rows['nomb_pers'];

    echo $html->put_title_demand("Datos de la Gu&iacute;a");
    echo $html->put_select_buscador("Mensajero",'iden_mens',$arra_options_mens,$_POST['iden_mens'],"");
    echo $html->put_select_buscador("Zona",'iden_zona',$arra_options_zona,$_POST['iden_zona'],"");
    //echo $html->put_title_demand("Informaci&oacute;n de Documentos");
    echo $html->put_text('text',"Agregar&nbsp;Documento&nbsp;<a href=\"javascript:f_agregar()\">(Click&nbsp;Aqui)</a>","Ingrese Código",'codi_docu',$_POST['codi_docu'],'','200','');
    //echo $html->put_button_colum("&nbsp;",'Agregar Documento'." &raquo;","return f_agregar()");
    
    /*
	echo $html->put_title_demand("Informaci&oacute;n B&aacute;sica");
	echo $html->put_text('text',"Código&nbsp;de&nbsp;Barras","Ingrese Código",'dire_docu',$_POST['dire_docu'],'','200','');
	echo $html->put_text('text',"Número","Ingrese Número",'dire_docu',$_POST['dire_docu'],'','200','');
	echo $html->put_select("Tipo&nbsp;Documento",'codi_tema',$arra_options_tema,$_POST['codi_tema'],"");
	echo $html->put_title_demand("Remitente");
	echo $html->put_select("Nombre",'codi_tema',$arra_options_tema,$_POST['codi_tema'],"");
	echo $html->put_text('text',"Cargo","Ingrese Cargo",'dire_docu',$_POST['dire_docu'],'','200','');
	echo $html->put_title_demand("Destinatario");
	echo $html->put_select("Nombre",'codi_tema',$arra_options_tema,$_POST['codi_tema'],"");
	echo $html->put_text('text',"Dirección","Ingrese Enlace Google Drive",'driv_docu',$_POST['driv_docu'],'','200','');
	*/
	echo"</main>";

	

	
	$busc_item_pagi=50;      //cantidad de items por pagina
	$fech_busq=date("d-m-Y");

	//$result=$Db->query("select * from mp_jurisprudencia_documento where nomb_docu like '%:m_busq%'",[':m_busq'=>$_POST['text_busc']]);
	$hoy=date("Ymd");

	$result=$Db->query("select * from mp_notif_guia_detalle_temporal where esta_temp=1 AND iden_sesi='".session_id()."' AND fdig_temp='$hoy'");
	$busc_tota_item=0;
	foreach($result as $rows)
	{       
		$busc_tota_item++;
	}
	$busc_tota_pagi=ceil($busc_tota_item/$busc_item_pagi);
	$busc_limi_pagi=($_POST['busc_pagi_actu']-1)*$busc_item_pagi;
	$result_pagi=$Db->query("select * from mp_notif_guia_detalle_temporal=a,mp_notif_documentos=b where a.iden_docu=b.iden_docu AND a.esta_temp=1 AND a.iden_sesi='".session_id()."' AND a.fdig_temp='$hoy' order by a.fdig_temp desc limit $busc_limi_pagi,$busc_item_pagi");

	echo"<div style=\"width:90%;max-width:800px;margin:auto;\">";
	echo $html->put_title_demand("DOCUMENTOS AGREGADOS $fech_busq: $busc_tota_item ENCONTRADOS");
	echo"<input type=hidden name='cant_docu' value='$busc_tota_item'>";

	if($busc_tota_pagi>0)
		echo $html->put_page("1",$_POST['busc_pagi_actu'],$busc_tota_pagi,"");
	$head=['1'=>"N°",'2'=>"CODIGO",'3'=>"NUMERO",'4'=>"TIPO",'5'=>"REMITENTE",'6'=>""];
	echo $html->put_table_responsive_open();
	if($busc_tota_item)
	{
		echo $html->put_table_responsive_header($head);
		$cont=$busc_limi_pagi;
		foreach($result_pagi as $rows)
		{
			$cont++;
			$data=[	'1'=>$cont,
				'2'=>$rows['cbar_docu'],
				'3'=>$rows['nume_docu'],
				'4'=>$arra_options_tipo[$rows['iden_tipo']],
				'5'=>$arra_options_remi[$rows['iden_remi']],
				'6'=>"<a href=\"javascript:f_borrar('".$rows['iden_docu']."','".$rows['cbar_docu']."','".$rows['nume_docu']."','".$arra_options_tipo[$rows['iden_tipo']]."','".$arra_options_remi[$rows['iden_remi']]."')\" alt=\"Ver Video en Youtube\"><img src=\"img/icons/trash.svg\" width=\"20\">",
			];
			    //'3'=>$rows['sumi_docu'],
			echo $html->put_table_responsive_data($head,$data);
		}
	}
	else
		echo $html->put_table_responsive_title("No Existen Documentos Agregados");
	echo $html->put_table_responsive_close();
	if($busc_tota_pagi>0)
		echo $html->put_page("2",$_POST['busc_pagi_actu'],$busc_tota_pagi,"");
		
	echo"</div>";
	
	
	
	
	
	

	echo $html->put_separator_demand("30");

                echo"
                        <div align=center class=\"foot\">
                                <center>
                                <div align=center class=\"foot2\">
                                        <div class=\"div_button_foot\" style=\"\">
                                                <button class=\"button_foot\" onclick=\"f_cancelar()\">&laquo; Cancelar</button>
                                        </div>
                                        <div class=\"div_button_foot\"><center>
                                                <button class=\"button_foot\" onclick=\"return f_guardar()\">Generar Gu&iacute;a &raquo;</button>
                                        </div>
                                </div>
                        </div>
                ";
    echo"<script>
        document.form.codi_docu.focus();
        const input = document.getElementById('codi_docu');
        input.addEventListener('keypress', function(event) {
            if (event.key === 'Enter') {
                event.preventDefault();
                // Executar ações desejadas
                f_agregar();
            }
        });
    </script>";
?>
<center>
	</form>
	</body>
</html>
