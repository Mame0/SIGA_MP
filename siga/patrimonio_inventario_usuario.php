<?
//classes/TCPDF/examples/personal_fotocheck.php
	require_once 'include/cabecera.php';
	require_once 'classes/Html.class.php';
	require_once 'classes/Db.class.php';
	$Db = new Db();
	if(!isset($_POST['busc_pagi_actu']))
		$_POST['busc_pagi_actu']=1;
	
	$fdig=date("YmdHis");
	
	if(empty($_POST['orig_loca_usua']))
	    $_POST['orig_loca_usua']='2';
	
	if(empty($_POST['codi_inve']))
	{
	    $result=$Db->query("select * from mp_patr_inve_mant where acti_inve='1' AND esta_inve='1' order by fech_inve limit 1");
	    foreach($result as $rows)
	    {
	        $_POST['codi_inve']=$rows['codi_inve'];
	        $_POST['fech_inve']=$rows['fech_inve'];
	        $_POST['nomb_inve']=$rows['nomb_inve'];
	    }
	}
	
	if(!empty($_POST['elim_inve']))
	{
	    $result=$Db->query("update mp_patr_inve_regi set esta_regi='0' where codi_regi='$_POST[elim_inve]'");
	}

    if(!empty($_POST['codi_regi']) AND !empty($_POST['obse_inve']))
	{
	    $result=$Db->query("update mp_patr_inve_regi set obse_regi='$_POST[obse_inve]' where codi_regi='$_POST[codi_regi]'");
	}

	if(!empty($_POST['usua_inve_busc']))
	{
	    $result=$Db->query("select * from mp_patr_siga_usua where empleado='".$_POST['usua_inve_busc']."'");
	    $flag_usua=0;
	    foreach($result as $rows)
	    {
	        $flag_usua++;
	        $_POST['usua_inve']=$rows['empleado'];
	        $_POST['usua_dato']="<BR>[".$rows['empleado']."] ".$rows['apellido_paterno']." ".$rows['apellido_materno'].", ".$rows['nombres'];
	    }
	    if(!$flag_usua)
	    {
	        $result=$Db->query("select * from mp_patr_inve_usua_temp where ndoc_usua='".$_POST['usua_inve_busc']."'");
	        foreach($result as $rows)
	        {
	            $flag_usua++;
	            $_POST['usua_inve']=$rows['ndoc_usua'];
	            $_POST['usua_dato']="<BR>[".$rows['ndoc_usua']."] ".$rows['appa_usua']." ".$rows['apma_usua'].", ".$rows['nomb_usua'];
	        }
	    }
	    if(!$flag_usua)
	    {
	        echo"<form name=form_blan method=post></form>
	            <script>
	            if(!confirm('ERROR: Usuario no existe\\nDesea Agregarlo?'))
	                document.form_blan.submit();
	            </script>
	        ";
	    }
	}
	
	if(!empty($_POST['usua_inve_nuev']) AND !empty($_POST['appa_inve_nuev']) AND !empty($_POST['apma_inve_nuev']) AND !empty($_POST['nomb_inve_nuev']))
	{
	    $result=$Db->query("insert into mp_patr_inve_usua_temp values('','".$_POST['usua_inve_nuev']."','".$_POST['appa_inve_nuev']."','".$_POST['apma_inve_nuev']."','".$_POST['nomb_inve_nuev']."','".$_SESSION['iden_oper']."','$fdig','1')");
	    echo"<script>alert('Usuario agregado correctamente')</script>";
	    $_POST['usua_inve']=$_POST['usua_inve_nuev'];
	    $_POST['usua_dato']="<BR>[".$_POST['usua_inve']."] ".$_POST['appa_inve_nuev']." ".$_POST['apma_inve_nuev'].", ".$_POST['nomb_inve_nuev'];
	}
	
	if(!empty($_POST['usua_inve']) AND !empty($_POST['codi_patr']))
	{
	    $result=$Db->query("select * from mp_patr_siga where codigo_patrimonial='".$_POST['codi_patr']."' OR codigo_barra='".$_POST['codi_patr']."'");
	    $flag=0;
	    foreach($result as $rows)
	    {
	        $flag++;
	        
	        //verificamos si ya fué inventariado
	        $flag_inve=0;
	        $resulti=$Db->query("select * from mp_patr_inve_regi where codi_patr='".$rows['codigo_patrimonial']."' AND codi_inve='".$_POST['codi_inve']."' AND esta_regi='1'");
	        foreach($resulti as $rowsi)
	        {
	            $flag_inve++;
	            if($rowsi['usua_inve']==$_POST['usua_inve'])
	                echo"<script>alert('ERROR: Bien ya fue inventariado con el mismo usuario')</script>";
	            else
	            {
	                echo"<script>
	                    if(confirm('ERROR: Bien ya fue inventariado con otro usuario\\nDesea registrarlo aqui?'))
	                        alert('si');
	                    else
	                        alert('no');
	                </script>";
	            }
	        }
	        if($flag_inve==0)
	        {
	            if(empty($_POST['lati_inve']))
	                $_POST['lati_inve']=(!empty($_POST['loca_lati']) ? $_POST['loca_lati'] : '');
	            if(empty($_POST['long_inve']))
	                $_POST['long_inve']=(!empty($_POST['loca_long']) ? $_POST['loca_long'] : '');
	            
                $loca_inve_val = !empty($_POST['loca_inve']) ? $_POST['loca_inve'] : '';
	            //si no fue inventariado, entonces lo registramos
	            $resulti=$Db->query("insert into mp_patr_inve_regi values('','".$_POST['codi_inve']."','".$loca_inve_val."','".$_POST['usua_inve']."','".$rows['codigo_patrimonial']."','".$_POST['lati_inve']."','".$_POST['long_inve']."','','".$_SESSION['iden_oper']."','$fdig','1')");
	            echo"<script>alert('Bien inventariado correctamente')</script>";
	        }
	    }
	    if($flag==0)
	    {
            $nomb_inve_val = !empty($_POST['nomb_inve']) ? $_POST['nomb_inve'] : '';
            $usua_dato_val = !empty($_POST['usua_dato']) ? $_POST['usua_dato'] : '';

	        echo"
	            <form name=form_bien method=post action=\"patrimonio_inventario_nuevo_bien.php\">
	                <input type=hidden name=\"codi_patr\" value=\"".$_POST['codi_patr']."\">
	                <input type=hidden name=\"codi_inve\" value=\"".$_POST['codi_inve']."\">
        			<input type=hidden name=\"fech_inve\" value=\"".$_POST['fech_inve']."\">
        			<input type=hidden name=\"nomb_inve\" value=\"".$nomb_inve_val."\">
        			<input type=hidden name=\"usua_inve\" value=\"".$_POST['usua_inve']."\">
        			<input type=hidden name=\"usua_dato\" value=\"".$usua_dato_val."\">
	            </form>
	            <script>
	                if(confirm('ERROR: Bien no ubicado\\nDesea Ingresarlo?'))
	                    document.form_bien.submit();
	            </script>
	        ";
        }
	}
	
?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8" />
		<title></title>
		<link rel="stylesheet" href="css/forms_demanda.css" />
		<link rel="stylesheet" href="css/forms_foot.css" />
		<link rel="stylesheet" href="css/forms_column.css" />
		<SCRIPT language="JavaScript" src="js/cadenas.js"></SCRIPT>
		<SCRIPT language="JavaScript" src="js/denuncia.js"></SCRIPT>
		
		<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
		
		<script>
		    function f_mensaje(mens)
		    {
		        parent.document.getElementById('div-mensajes').innerHTML = mens;
		    }
		    function f_buscar_usuario()
			{
			    document.form.action='';
				document.form.target="";
				document.form.submit();
			}
			function f_crear_usuario()
			{
			    if(document.form.appa_inve_nuev.value=='')
			    {
			        alert('ERROR: Ingrese Apellido Paterno');
			        document.form.appa_inve_nuev.focus();
			        return false;
			    }
			    else
			    {
			        if(document.form.apma_inve_nuev.value=='')
    			    {
	    		        alert('ERROR: Ingrese Apellido Materno');
		    	        document.form.apma_inve_nuev.focus();
		    	        return false;
			        }
			        else
			        {
			            if(document.form.nomb_inve_nuev.value=='')
        			    {
		        	        alert('ERROR: Ingrese Nombres');
        			        document.form.nomb_inve_nuev.focus();
        			        return false;
		        	    }
			            else
			            {
			                if(confirm('Seguro que desea crear usuario?'))
			                {
			                    document.form.action='';
        	    	    		document.form.target="";
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
			    document.form.usua_inve_nuev.value='';
			    document.form.action='';
				document.form.target="";
				document.form.submit();
			}
			function f_buscar_bien()
			{
			    document.form.action='';
				document.form.target="";
				document.form.submit();
			}
			function f_recargar()
			{
				document.form.submit();
			}
			function f_consolidado()
			{
			    document.form.action='patrimonio_inventario_local_consolidar.php';
			    document.form.target="";
				document.form.submit();
			}
			function f_generar_pdf()
			{
			    document.form.action='classes/TCPDF/examples/patrimonio_inventario_usuario.php';
			    document.form.target="blank";
			    document.form.submit();
			}
			function f_cambiar_usuario()
			{
			    document.form.usua_inve.value='';
			    document.form.usua_dato.value='';
			    document.form.action='';
				document.form.target="";
				document.form.submit();
			}
			function f_registrar()
			{
			    document.form.regi_post.value='1';
				document.form.action='';
				document.form.target="";
				document.form.submit();
			}
			function f_eliminar(codi,patr,barr,nomb)
			{
			    if(confirm('Seguro que desea eliminar inventario de: \n '+nomb))
			    {
			        document.form.elim_inve.value=codi;
			        document.form.submit();
			    }
			    //else
			     //   swal("Oops!", "Something went wrong on the page!", "error");
			}
			function f_observacion(codi,obse)
			{
			    nuev_obse=window.prompt('Ingrese Observacion',obse);
			    if(nuev_obse)
			    {
			        document.form.obse_inve.value=nuev_obse;
			        document.form.codi_regi.value=codi;
			        document.form.submit();
			    }
			    else
			        return false;
			}
			function f_editar_personal(codi)
			{
				document.form.codi_pers.value=codi;
				document.form.action='personal_registro.php';
				document.form.target="";
				document.form.submit();
			}
			function PadLeft(value, length)
			{
				return (value.toString().length < length) ? PadLeft("0" + value, length) : 
				value;
			}
			function ajustar_altura()
                        {
                                parent.document.getElementById('body_iframe').height=parent.window.innerHeight-80;
                        }
                        ajustar_altura();
		</script>

	</head>
	<body style="margin-bottom: 30px;">
	    
	<center><h4 style="color:#073A6B"><b>
<?
    $fech_inve_val = !empty($_POST['fech_inve']) ? $_POST['fech_inve'] : '';
    $nomb_inve_val = !empty($_POST['nomb_inve']) ? $_POST['nomb_inve'] : '';
    $loca_dato_val = !empty($_POST['loca_dato']) ? $_POST['loca_dato'] : '';
    $usua_dato_val = !empty($_POST['usua_dato']) ? $_POST['usua_dato'] : '';

    if(!empty($_POST['loca_inve']))
	    echo"TOMA DE INVENTARIO POR LOCAL<BR>[".$fech_inve_val."] ".$nomb_inve_val." ".$loca_dato_val." ".$usua_dato_val;
    else
	    echo"TOMA DE INVENTARIO POR USUARIO<BR>[".$fech_inve_val."] ".$nomb_inve_val." ".$usua_dato_val;
?>
	</h4></b></center>
		<form name="form" method="post">
			<input type=hidden name="regi_inve">
			<input type=hidden name="lati_inve">
			<input type=hidden name="long_inve">
			<input type=hidden name="elim_inve">
			<input type=hidden name="obse_inve">
			<input type=hidden name="codi_regi">
			<input type=hidden name="busc_pagi_actu" value="<?=!empty($_POST['busc_pagi_actu']) ? $_POST['busc_pagi_actu'] : ''?>">
			<input type=hidden name="codi_inve" value="<?=!empty($_POST['codi_inve']) ? $_POST['codi_inve'] : ''?>">
			<input type=hidden name="fech_inve" value="<?=$fech_inve_val?>">
			<input type=hidden name="nomb_inve" value="<?=$nomb_inve_val?>">
			<input type=hidden name="usua_inve" value="<?=!empty($_POST['usua_inve']) ? $_POST['usua_inve'] : ''?>">
			<input type=hidden name="usua_dato" value="<?=$usua_dato_val?>">
			<input type=hidden name="loca_inve" value="<?=!empty($_POST['loca_inve']) ? $_POST['loca_inve'] : ''?>">
			<input type=hidden name="loca_dato" value="<?=$loca_dato_val?>">
			<input type=hidden name="loca_lati" value="<?=!empty($_POST['loca_lati']) ? $_POST['loca_lati'] : ''?>">
			<input type=hidden name="loca_long" value="<?=!empty($_POST['loca_long']) ? $_POST['loca_long'] : ''?>">
			<input type=hidden name="orig_loca_usua" value="<?=!empty($_POST['orig_loca_usua']) ? $_POST['orig_loca_usua'] : ''?>">
<?
	$html=new htmlclass;
	
	if(empty($_POST['usua_inve']))
	{
	    if(!empty($_POST['usua_inve_busc']))
	    {
	        $_POST['usua_inve_nuev']=$_POST['usua_inve_busc'];
	        echo"<main>";
    	    echo $html->put_text('text',"DNI","Ingrese DNI",'usua_inve_nuev',$_POST['usua_inve_nuev'],'','15',' readonly');
    	    echo"</main>";
    	    echo"<main>";
    	    echo $html->put_text('text',"AP.PATERNO","Apellido Paterno",'appa_inve_nuev','','','100','');
    	    echo $html->put_text('text',"AP.MATERNO","Apellido Materno",'apma_inve_nuev','','','100','');
    	    echo $html->put_text('text',"NOMBRES","Nombres",'nomb_inve_nuev','','','100','');
    	    echo"</main>";
	    }
	    else
	    {
	        echo"<main>";
    	    echo $html->put_text('text',"DNI","Ingrese DNI",'usua_inve_busc','','','15','');
	        echo $html->put_button_colum("&nbsp;","Buscar usuario &raquo;","return f_buscar_usuario()");
	        echo"</main>";
	    }
	}
	else
	{
	    $i=0;
        $loca_inve_val = !empty($_POST['loca_inve']) ? $_POST['loca_inve'] : '';
	    if(!empty($_POST['loca_inve']))
	        $result=$Db->query("select * from mp_patr_inve_regi where codi_inve='".$_POST['codi_inve']."' AND usua_inve='".$_POST['usua_inve']."' AND codi_loca='".$_POST['loca_inve']."'");
	    else
    	    $result=$Db->query("select * from mp_patr_inve_regi where codi_inve='".$_POST['codi_inve']."' AND usua_inve='".$_POST['usua_inve']."'");
        
        $arra_inve = [];
    	foreach($result as $rows)
    	{
    	    $i++;
    	    $arra_inve[$i]=$rows['codi_patr'];
    	    $arra_inve_codi[$rows['codi_patr']]=$rows['codi_regi'];
    	    $arra_inve_obse[$rows['codi_patr']]=$rows['obse_regi'];
    	    
    	    if(empty($rows['lati_regi']))
    	        $rows['lati_regi']='-16.399236';
    	    if(empty($rows['long_regi']))
    	        $rows['long_regi']='-71.52795';
    	   
    	    $resultd=$Db->query("SELECT *,(acos(sin(radians(lati_loca)) * sin(radians({$rows['lati_regi']})) + 
cos(radians(lati_loca)) * cos(radians({$rows['lati_regi']})) * 
cos(radians(long_loca) - radians({$rows['long_regi']}))) * 6378) as 
distanciaPunto1Punto2 from mp_admi_loca order by distanciaPunto1Punto2 limit 1");
            foreach($resultd as $rowsd)
                $arra_ubic[$rows['codi_patr']]=substr($rows['fdig_regi'],6,2).'/'.substr($rows['fdig_regi'],4,2).'/'.substr($rows['fdig_regi'],0,4).' '.substr($rows['fdig_regi'],8,2).':'.substr($rows['fdig_regi'],10,2).':'.substr($rows['fdig_regi'],10,2).' ['.$rows['digi_regi'].'] - '.$rowsd['nom1_loca'].' ['.$rowsd['dire_loca'].']';
                
    	}
	
    	echo"<main>";
    	//echo $html->put_title_demand("INGRESE DOCUMENTO");
    	//echo $html->put_select("Plaza",'codi_plaz',$arra_options_regi,$_POST['codi_plaz'],"");
    	echo $html->put_text('text',"CÓDIGO&nbsp;DEL&nbsp;BIEN","Ingrese CÓDIGO",'codi_patr','','','15','');
    	echo $html->put_button_colum("&nbsp;","Buscar bien &raquo;","return f_buscar_bien()");
    	echo"</main>";
    	//echo"<main>";
    	//echo $html->put_select("Formato",'codi_form',$arra_options_form,$_POST['codi_form'],"");
    	echo"</main>";

    	$busc_item_pagi=10000;      //cantidad de items por pagina
	
    	$result=$Db->query("select * from mp_patr_siga where docum_identidad='".$_POST['usua_inve']."'");
    	$busc_tota_item=0;
    	foreach($result as $rows)
    	{       
    		$busc_tota_item++;
    	}

    	$busc_tota_pagi=ceil($busc_tota_item/$busc_item_pagi);
    	$busc_limi_pagi=($_POST['busc_pagi_actu']-1)*$busc_item_pagi;

    	$result_pagi=$Db->query("select * from mp_patr_siga where docum_identidad='".$_POST['usua_inve']."' order by descripcion asc limit $busc_limi_pagi,$busc_item_pagi");

    	echo"<div style=\"width:90%;max-width:800px;margin:auto;\">";
    	echo $html->put_title_demand("ASIGNACIÓN SEGUN SIGA: $busc_tota_item BIENES");

    	//if($busc_tota_pagi>0  OR 5==5)
    	//	echo $html->put_page("1",$_POST['busc_pagi_actu'],$busc_tota_pagi,"");
    	$head=['1'=>"Nº",'2'=>"COD.PATR.",'3'=>"DESCRIPCION",'4'=>"COD.BARR.",'5'=>"NRO.SERIE",'6'=>"INV.",'7'=>"ELIM.",'8'=>"OBS."];
    	echo $html->put_table_responsive_open();
    	if($busc_tota_item OR 5==5)
    	{
    		echo $html->put_table_responsive_header($head);
    		$cont=$busc_limi_pagi;
    		foreach($result_pagi as $rows)
    		{
    			$cont++;
    			$colo="<font color=silver>";
    			$chec="<img src=\"img/icons/delete-remove-uncheck-svgrepo-com.svg\" width=\"20\">";
    			$elim="";
    			$obse="";
    			
    			if(!isset($arra_ubic[$rows['codigo_patrimonial']]))
    			    $arra_ubic[$rows['codigo_patrimonial']]='Sin inventario';
    			
    			if(array_search($rows['codigo_patrimonial'],$arra_inve))
    			{
    			    $colo="<font class=\"background-color: green\" color=black>";
    			    $chec="<img src=\"img/icons/ok-svgrepo-com.svg\" width=\"20\">";
    			    $elim="<img src=\"img/icons/trash.svg\" width=\"20\">";
    			    $obse="<img src=\"img/icons/edit.svg\" width=\"20\">";
    			}
    			$data=[	'1'=>$colo.$cont,
    			    '2'=>$colo.$rows['codigo_patrimonial'],
    				'3'=>$colo.utf8_encode(utf8_decode(strtoupper($rows['descripcion']))),  
    				'4'=>$colo.utf8_encode(utf8_decode(strtoupper($rows['codigo_barra']))),
    				'5'=>$colo.utf8_encode(utf8_decode(strtoupper($rows['nro_serie']))),
    				'6'=>"<a href=\"javascript:alert('MARCA: $rows[marca] \\nMODELO: $rows[modelo] \\nCOLOR: $rows[color] \\n--------------------- \\nINVENTARIO: ".$arra_ubic[$rows['codigo_patrimonial']]."')\">".$chec,
    				'7'=>"<a href=\"javascript:f_eliminar('".$arra_inve_codi[$rows['codigo_patrimonial']]."','$rows[codigo_patrimonial]','$rows[codigo_barra]','$rows[descripcion]')\">$elim",
    				'8'=>"<a href=\"javascript:f_observacion('".$arra_inve_codi[$rows['codigo_patrimonial']]."','".$arra_inve_obse[$rows['codigo_patrimonial']]."')\">$obse",
    			];
    			echo $html->put_table_responsive_data($head,$data);
    			    //'6'=>"<a href=\"javascript:f_eliminar('$rows[docu_post]','$rows[appa_post]','$rows[apma_post]','$rows[nomb_post]','$rows[codi_post]')\"><img src=\"img/delete.png\" width=\"20\">",
    		}
    	}
    	else
    		echo $html->put_table_responsive_title("Usuario no tiene bienes asignados");
		
    	echo $html->put_table_responsive_close();
    	//if($busc_tota_pagi>0  OR 5==5)
    	//	echo $html->put_page("2",$_POST['busc_pagi_actu'],$busc_tota_pagi,"");
    	echo"</div>";
    	
    	
    	$result_pagi=$Db->query("select * from mp_patr_inve_regi as a, mp_patr_siga as b where a.codi_patr=b.codigo_patrimonial AND a.codi_inve='".$_POST['codi_inve']."' AND a.usua_inve='".$_POST['usua_inve']."' AND b.docum_identidad<>'".$_POST['usua_inve']."' AND a.esta_regi='1' order by b.descripcion asc");
    	    	echo"<div style=\"width:90%;max-width:800px;margin:auto;\">";
    	echo $html->put_title_demand("BIENES INVENTARIADOS NO ASIGNADOS:");

    	$head=['1'=>"Nº",'2'=>"COD.PATR.",'3'=>"DESCRIPCION",'4'=>"COD.BARR.",'5'=>"NRO.SERIE",'6'=>"INV.",'7'=>"ELIM.",'8'=>"OBS."];
    	echo $html->put_table_responsive_open();
    	if($busc_tota_item OR 5==5)
    	{
    		echo $html->put_table_responsive_header($head);
    		$cont=$busc_limi_pagi;
    		foreach($result_pagi as $rows)
    		{
    			$cont++;
    			$data=[	'1'=>$cont,
    				'2'=>$rows['codigo_patrimonial'],
    				'3'=>utf8_encode(utf8_decode(strtoupper($rows['descripcion']))),  
    				'4'=>utf8_encode(utf8_decode(strtoupper($rows['codigo_barra']))),
    				'5'=>utf8_encode(utf8_decode(strtoupper($rows['nro_serie']))),
    				'6'=>"<a href=\"javascript:alert('MARCA: $rows[marca] \\nMODELO: $rows[modelo] \\nCOLOR: $rows[color] \\nRESPONSABLE: $rows[usuario] \\n--------------------- \\n INVENTARIO: ".$arra_ubic[$rows['codigo_patrimonial']]."')\"><img src=\"img/icons/ok-svgrepo-com.svg\" width=\"20\">",
    				'7'=>"<a href=\"javascript:f_eliminar('$rows[codi_regi]','$rows[codigo_patrimonial]','$rows[codigo_barra]','$rows[descripcion]')\"><img src=\"img/icons/trash.svg\" width=\"20\">",
    				'8'=>"<a href=\"javascript:f_observacion('$rows[codi_regi]','$rows[obse_regi]')\"><img src=\"img/icons/edit.svg\" width=\"20\">",
    			];
    			    //'6'=>"<a href=\"javascript:f_eliminar('$rows[docu_post]','$rows[appa_post]','$rows[apma_post]','$rows[nomb_post]','$rows[codi_post]')\"><img src=\"img/delete.png\" width=\"20\">",
    			echo $html->put_table_responsive_data($head,$data);
    		}
    	}
        
    	echo $html->put_table_responsive_close();
    	echo"</div>";
    	
	}
	if(!empty($_POST['loca_inve']) OR !empty($_POST['usua_inve']))
	{
		echo $html->put_separator_demand("30");
		echo"
                        <div align=center class=\"foot\">
                                <center>
                                <div align=center class=\"foot2\">
                                        <div class=\"div_button_foot\" style=\"\">
        ";
        if(!empty($_POST['loca_inve']))
        {
            if(!empty($_POST['usua_inve']))
                echo"<button class=\"button_foot\" onclick=\"f_generar_pdf()\">Generar PDF</button>";
            else
                echo"<button class=\"button_foot\" onclick=\"f_recargar()\">Actualizar</button>";
        }
        else
        {
            if(!empty($_POST['usua_inve_busc']) AND empty($_POST['usua_inve']))
                echo"<button class=\"button_foot\" onclick=\"f_cancelar()\">Cancelar</button>";
            else
                echo"<button class=\"button_foot\" onclick=\"f_generar_pdf()\">Generar PDF</button>";
        }
        echo"
                                        </div>
                                        <div class=\"div_button_foot\"><center>
        ";
        if(!empty($_POST['loca_inve']))
            echo"<button class=\"button_foot\" onclick=\"f_consolidado()\">Regresar a Consolidado</button>";
        else
        {
            if(!empty($_POST['usua_inve_busc']) AND empty($_POST['usua_inve']))
                echo"<button class=\"button_foot\" onclick=\"return f_crear_usuario()\">Crear Usuario</button>";
            else
                echo"<button class=\"button_foot\" onclick=\"f_cambiar_usuario()\">Cambiar Usuario</button>";
        }
        echo"
                                        </div>
                                </div>
                        </div>
        ";
	}
?>
<center>
    <script>
        navigator.geolocation.getCurrentPosition(function(position){
            let lat = position.coords.latitude;
            let long = position.coords.longitude;
            document.form.lati_inve.value=lat;
            document.form.long_inve.value=long;
        });
<?
    if(!empty($_POST['usua_inve']))
        echo"document.form.codi_patr.focus();";
    else
        echo"document.form.usua_inve_busc.focus();";
?>
    </script>
	</form>
	</body>
</html>
