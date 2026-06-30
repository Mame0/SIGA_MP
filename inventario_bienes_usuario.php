<?
//classes/TCPDF/examples/personal_fotocheck.php
	require_once 'include/cabecera.php';
	require_once 'classes/Html.class.php';
	require_once 'classes/Db.class.php';
	$Db = new Db();
	if(!isset($_POST['busc_pagi_actu']))
		$_POST['busc_pagi_actu']=1;
	
	$fdig=date("YmdHis");
	
	if(!isset($_POST['codi_inve']) || empty($_POST['codi_inve']))
	{
	    $result=$Db->query("select * from mp_inve_mant where acti_inve='1' AND esta_inve='1' order by fech_inve limit 1");
	    foreach($result as $rows)
	    {
	        $_POST['codi_inve']=$rows['codi_inve'];
	        $_POST['fech_inve']=$rows['fech_inve'];
	        $_POST['nomb_inve']=$rows['nomb_inve'];
	    }
	}

    // Inicializar variables para evitar undefined index
    $_POST['codi_loca_sele'] = '';
    $_POST['codi_depe_sele'] = '';
    $_POST['codi_usua_sele'] = '';
    $loca_nuev = '';
    $depe_nuev = '';
    $usua_nuev = '';
    $obse_nuev = '';
    $ndoc_pers_sele = '';
    $aler_usua = '';

    //PARA ACTUALIZAR LA TABLA DE SELECCION
    $result=$Db->query("select * from mp_inve_sele where codi_oper='".$_SESSION['iden_oper']."'");
	foreach($result as $rows)
	{
	    $_POST['codi_loca_sele']=$rows['codi_loca'];
	    $_POST['codi_depe_sele']=$rows['codi_depe'];
	    $_POST['codi_usua_sele']=$rows['codi_usua'];
	}
	if(!isset($_POST['loca_inve_busc']))    $_POST['loca_inve_busc']=$_POST['codi_loca_sele'];
	if(!isset($_POST['codi_depe']))         $_POST['codi_depe']=$_POST['codi_depe_sele'];
	if(!isset($_POST['codi_pers']))         $_POST['codi_pers']=$_POST['codi_usua_sele'];
	$result=$Db->query("delete from mp_inve_sele where codi_oper='".$_SESSION['iden_oper']."'");
	$result=$Db->query("insert into mp_inve_sele values('".$_SESSION['iden_oper']."','".$_POST['loca_inve_busc']."','".$_POST['codi_depe']."','".$_POST['codi_pers']."')");
	//$result=$Db->query("update mp_inve_sele set codi_loca='".$_POST['loca_inve_busc']."',codi_depe='".$_POST['codi_depe']."',codi_usua='".$_POST['codi_pers']."' where codi_oper='".$_SESSION['iden_oper']."'");
	//FIN PARA ACTUALIZAR LA TABLA DE SELECCION
	    
	if(isset($_POST['loca_inve_busc']))
	{
	    $_POST['loca_inve']=$_POST['loca_inve_busc'];
	    
	    $result=$Db->query("select * from mp_admi_loca where codi_loca='".$_POST['loca_inve']."'");
	    $flag_usua=0;
	    foreach($result as $rows)
	    {
	        $flag_usua++;
	        $loca_nuev=$_POST['loca_inve'];
	        $_POST['nomb_loca']=$rows['nom1_loca'];
	        $_POST['loca_inve']=$rows['codi_loca'];
	        $_POST['loca_dato']="<BR><u>LOCAL</u>: ".$rows['nom1_loca'];
	        $_POST['loca_lati']=$rows['lati_loca'];
	        $_POST['loca_long']=$rows['long_loca'];
	    }
	}
	
	if(isset($_POST['codi_depe']))
	{
	    $_POST['depe_inve']=$_POST['codi_depe'];
	    
	    $result=$Db->query("select * from mp_admi_depe where codi_depe='".$_POST['codi_depe']."'");
	    $flag_usua=0;
	    foreach($result as $rows)
	    {
	        $flag_usua++;
	        $depe_nuev=$_POST['codi_depe'];
	        $_POST['nomb_depe']=$rows['nomb_depe'];
	        $_POST['depe_dato']="<BR><u>DEPENDENCIA</u>: ".$rows['nomb_depe'];
	    }
	}

    if(isset($_POST['codi_pers']))
	{
	    $_POST['pers_inve']=$_POST['codi_pers'];
	    
	    $result=$Db->query("select * from mp_admi_pers where iden_pers='".$_POST['codi_pers']."'");
	    $flag_usua=0;
	    foreach($result as $rows)
	    {
	        $flag_usua++;
	        $usua_nuev=$_POST['codi_pers'];
	        $_POST['nomb_pers']="[".$rows['ndoc_pers']."] ".$rows['appa_pers']." ".$rows['apma_pers'].", ".$rows['nomb_pers'];
	        $_POST['pers_dato']="<BR><u>USUARIO</u>: ".$_POST['nomb_pers'];
	        $ndoc_pers_sele=$rows['ndoc_pers'];
	    }
	}

	if(isset($_POST['regi_inve']) && $_POST['regi_inve'])
	{
	    if(!empty($_POST['codi_regi']))
	    {
	        $sql="update mp_inve_regi set esta_regi='0' where codi_regi='".$_POST['codi_regi']."'";
	        $resultx=$Db->query($sql);
	    }

	    //$sql="insert into mp_inve_regi values('','".$_POST['codi_inve']."','".$_POST['loca_inve_busc']."','".$_POST['codi_depe']."','".$_POST['codi_pers']."','".$_POST['regi_inve']."','".$_POST['lati_inve']."','".$_POST['long_inve']."','".$_POST['iest_regi']."','".$_POST['iuso_regi']."','".$_POST['iare_regi']."','".$_POST['iobs_regi']."','".$_POST['iubi_regi']."','".$_POST['idep_regi']."','".$_POST['iusu_regi']."','".$_SESSION['iden_oper']."','$fdig','1')";
	    // Ensure variables are set for SQL insertion
	    $lati_inve = isset($_POST['lati_inve']) ? $_POST['lati_inve'] : '';
	    $long_inve = isset($_POST['long_inve']) ? $_POST['long_inve'] : '';
	    $iest_regi = isset($_POST['iest_regi']) ? $_POST['iest_regi'] : '';
	    $iuso_regi = isset($_POST['iuso_regi']) ? $_POST['iuso_regi'] : '';
	    $iare_regi = isset($_POST['iare_regi']) ? $_POST['iare_regi'] : '';
	    $iobs_regi = isset($_POST['iobs_regi']) ? $_POST['iobs_regi'] : '';
	    $iubi_regi_auto = isset($_POST['iubi_regi_auto']) ? $_POST['iubi_regi_auto'] : '';
	    $idep_regi_auto = isset($_POST['idep_regi_auto']) ? $_POST['idep_regi_auto'] : '';
	    $iusu_regi_auto = isset($_POST['iusu_regi_auto']) ? $_POST['iusu_regi_auto'] : '';

	    $sql="insert into mp_inve_regi values('','".$_POST['codi_inve']."','".$_POST['loca_inve_busc']."','".$_POST['codi_depe']."','".$_POST['codi_pers']."','".$_POST['regi_inve']."','".$lati_inve."','".$long_inve."','".$iest_regi."','".$iuso_regi."','".$iare_regi."','".$iobs_regi."','".$iubi_regi_auto."','".$idep_regi_auto."','".$iusu_regi_auto."','".$_SESSION['iden_oper']."','$fdig','1')";
	    //echo"<HR>$sql<HR>";
	    $resultx=$Db->query($sql);
	    unset($_POST['regi_inve'],$_POST['iare_regi'],$_POST['codi_patr']);
	}
	
	if(5==6)
	{
	    $result=$Db->query("select * from mp_inve_siga where codigo_patrimonial='".$_POST['codi_patr']."' OR codigo_barra='".$_POST['codi_patr']."' limit 1");
	    $flag=0;
	    foreach($result as $rows)
	    {
	        $flag++;
	        
	        //verificamos si ya fué inventariado
	        $flag_inve=0;
	        $resulti=$Db->query("select * from mp_inve_regi where codi_patr='".$rows['codigo_patrimonial']."' AND codi_inve='".$_POST['codi_inve']."' AND esta_regi='1'");
	        foreach($resulti as $rowsi)
	        {
	            $flag_inve++;
	            if($rowsi['codi_loca']==$_POST['loca_inve'])
	            {
	                $mens="ERROR: Bien ya fue inventariado en este mismo Local";
	                    echo"<script>
	                        parent.document.getElementById('header').style.background='#FF0000';
	                        parent.document.getElementById('div-mensajes').innerHTML = '<FONT COLOR=SILVER><U>ERROR</U>:</FONT> $mens';
	                        setTimeout(function(){
                                parent.document.getElementById('div-mensajes').innerHTML ='';
                                parent.document.getElementById('header').style.background='#073A6B';
                            }, 4000);
	                    </script>";
	            }
	            else
	            {
	                if($_POST['ulti_lect']==$_POST['codi_patr'])
	                {
	                    $resultx=$Db->query("update mp_inve_regi set esta_regi='0' where codi_regi='".$rowsi['codi_regi']."'");
	                    $resultx=$Db->query("insert into mp_inve_regi values('','".$_POST['codi_inve']."','".$_POST['loca_inve']."','".$rows['docum_identidad']."','".$rows['codigo_patrimonial']."','".$_POST['lati_inve']."','".$_POST['long_inve']."','','".$_SESSION['iden_oper']."','$fdig','1')");
	                    $_POST['ulti_lect']='';
	                    $mens_post_agre="<font color=silver><u>BIEN UBICADO</u>:</font> ".$rows['descripcion'];
         	            echo"<script>
	                        parent.document.getElementById('header').style.background='#45BE00';
	                        parent.document.getElementById('div-mensajes').innerHTML = '$mens_post_agre';
	                        setTimeout(function(){
                                parent.document.getElementById('div-mensajes').innerHTML ='';
                                parent.document.getElementById('header').style.background='#073A6B';
                            }, 4000);
	                    </script>";
	                }
	                else
	                {
	                    $mens="ERROR: Bien ya fue inventariado en otro Local. Si desea registrarlo aqui, vuelva a leerlo porfavor";
	                    echo"<script>
	                        parent.document.getElementById('header').style.background='#FF0000';
	                        parent.document.getElementById('div-mensajes').innerHTML = '<FONT COLOR=SILVER><U>ERROR</U>:</FONT> $mens';
	                        setTimeout(function(){
                                parent.document.getElementById('div-mensajes').innerHTML ='';
                                parent.document.getElementById('header').style.background='#073A6B';
                            }, 4000);
	                    </script>";
	                }
	            }
	        }
	        if($flag_inve==0)
	        {
	            if(empty($_POST['lati_inve']))
	                $_POST['lati_inve']=(isset($_POST['loca_lati']) ? $_POST['loca_lati'] : '');
	            if(empty($_POST['long_inve']))
	                $_POST['long_inve']=(isset($_POST['loca_long']) ? $_POST['loca_long'] : '');
	            //si no fue inventariado, entonces lo registramos
	            $resulti=$Db->query("insert into mp_inve_regi values('','".$_POST['codi_inve']."','".$_POST['loca_inve']."','".$rows['docum_identidad']."','".$rows['codigo_patrimonial']."','".$_POST['lati_inve']."','".$_POST['long_inve']."','','".$_SESSION['iden_oper']."','$fdig','1')");
	            //echo"<script>alert('Bien inventariado correctamente')</script>";
	            $mens_post_agre="<font color=silver><u>BIEN UBICADO</u>:</font> ".$rows['descripcion'];
	            echo"<script>
	                parent.document.getElementById('header').style.background='#45BE00';
	                parent.document.getElementById('div-mensajes').innerHTML = '$mens_post_agre';
	                setTimeout(function(){
                        parent.document.getElementById('div-mensajes').innerHTML ='';
                        parent.document.getElementById('header').style.background='#073A6B';
                    }, 4000);
	            </script>"; 
	        }
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
			function f_recargar()
			{
				document.form.submit();
			}
			function f_consolidar()
			{
			    document.form.action='patrimonio_inventario_local_consolidar.php';
				document.form.submit();
			}
			function f_buscar_avanzado()
			{
			    document.form.action='patrimonio_buscar_bienes.php';
				document.form.submit();
			}
			function f_cambiar_local()
			{
			    document.form.loca_inve.value='';
			    document.form.loca_dato.value='';
			    document.form.action='';
				document.form.target="";
				document.form.submit();
			}
			function f_nueva_busqueda()
			{
			    document.form.codi_patr.value='';
			    //document.form.codi_patr_regi.value='';
				document.form.submit();
			}
			function f_cambiar_orden(orde)
			{
			    document.form.orde_inve.value=orde;
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
			function f_registrar(codi)
			{
			    document.form.codi_patr.value=codi;
			    //document.form.codi_patr_regi.value=codi;
			    document.form.submit();
			}
			function f_registrar_repetido(codi)
			{
			    if(confirm('Bien ya fue inventariado\nDesea volver a registrarlo?'))
			    {
			        document.form.codi_patr.value=codi;
			        document.form.submit();
			    }
			    else
			        return false
			}
			function f_registro_inventario(codi,aler)
			{
			    if(document.form.iest_regi.value=='0')
			    {
			        alert('Seleccione Estado del Bien');
			        document.form.iest_regi.focus();
			        return false;
			    }
			    else
			    {
			        if(document.form.iuso_regi.value=='0')
			        {
			            alert('Seleccione USO');
			            document.form.iuso_regi.focus();
			            return false;
			        }
			        else
			        {
			            if(document.form.iare_regi.value=='')
			            {
			                alert('Ingrese Código ARE');
			                document.form.iare_regi.focus();
			                return false;
			            }
			            else
			            {
			                if(aler)
			                {
			                    if(document.form.vali_usua.selectedIndex=='0')
			                    {
			                        alert('Usuario no coincide, validar si es el mismo');
			                        document.form.vali_usua.focus();
			                        return false;
			                    }
			                    else
			                    {
			                        if(confirm('Seguro que desea registrar inventario?'))
			                        {
         			                    document.form.regi_inve.value=codi;
			                            document.form.submit();
			                        }
			                        else
         			                    return false;
			                    }
			                }
			                else
			                {
			                    if(confirm('Seguro que desea registrar inventario?'))
			                    {
     			                    document.form.regi_inve.value=codi;
			                        document.form.submit();
			                    }
			                    else
     			                    return false;
			                }
			            }
			        }
			    }
			}
			function f_cambio_usuario(loca,depe,usua,obse)
			{
			    if(document.form.vali_usua.selectedIndex==1)
			    {
			        document.form.iubi_regi_auto.value=loca;
			        document.form.idep_regi_auto.value=depe;
			        document.form.iusu_regi_auto.value=usua;
			        document.form.iobs_regi.value=obse;
			    }
			    else
			    {
			        document.form.iubi_regi_auto.value='';
			        document.form.idep_regi_auto.value='';
			        document.form.iusu_regi_auto.value='';
			        document.form.iobs_regi.value='';
			    }
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
	    
	<center><h4 style="color:#073A6B"><b>TOMA DE INVENTARIO<BR>[<?=(isset($_POST['fech_inve'])?$_POST['fech_inve']:'')?>] <?=(isset($_POST['nomb_inve'])?$_POST['nomb_inve']:'')?> <?=(isset($_POST['loca_dato']) ? $_POST['loca_dato'] : '')?> <?=(isset($_POST['depe_dato']) ? $_POST['depe_dato'] : '')?> <?=(isset($_POST['pers_dato']) ? $_POST['pers_dato'] : '')?></h4></b></center>
		<form name="form" method="post">
			<input type=hidden name="regi_inve">
			<input type=hidden name="lati_inve">
			<input type=hidden name="long_inve">
			<input type=hidden name="elim_inve">
			<input type=hidden name="obse_inve">
			<input type=hidden name="codi_regi">
			<input type=hidden name="iubi_regi_auto">
			<input type=hidden name="idep_regi_auto">
			<input type=hidden name="iusu_regi_auto">
			<input type=hidden name="busc_pagi_actu" value="<?=(isset($_POST['busc_pagi_actu'])?$_POST['busc_pagi_actu']:'')?>">
			<input type=hidden name="codi_inve" value="<?=(isset($_POST['codi_inve'])?$_POST['codi_inve']:'')?>">
			<input type=hidden name="fech_inve" value="<?=(isset($_POST['fech_inve'])?$_POST['fech_inve']:'')?>">
			<input type=hidden name="nomb_inve" value="<?=(isset($_POST['nomb_inve'])?$_POST['nomb_inve']:'')?>">
			<input type=hidden name="loca_inve" value="<?=(isset($_POST['loca_inve'])?$_POST['loca_inve']:'')?>">
			<input type=hidden name="loca_dato" value="<?=(isset($_POST['loca_dato']) ? $_POST['loca_dato'] : '')?>">
			<input type=hidden name="loca_lati" value="<?=(isset($_POST['loca_lati']) ? $_POST['loca_lati'] : '')?>">
			<input type=hidden name="loca_long" value="<?=(isset($_POST['loca_long']) ? $_POST['loca_long'] : '')?>">
			<input type=hidden name="ulti_lect" value="<?=(isset($_POST['codi_patr']) ? $_POST['codi_patr'] : '')?>">
			<input type=hidden name="orde_inve" value="<?=(isset($_POST['orde_inve']) ? $_POST['orde_inve'] : '')?>">
<?
	$html=new htmlclass;
	
	$obse_nuev=(isset($_POST['nomb_loca']) ? $_POST['nomb_loca'] : '')."/".(isset($_POST['nomb_depe']) ? $_POST['nomb_depe'] : '')."/".(isset($_POST['nomb_pers']) ? $_POST['nomb_pers'] : '');
	
	if(!empty($_POST['loca_inve_busc']) AND !empty($_POST['codi_depe']) AND !empty($_POST['codi_pers']))
	{
	    echo"
	        <input type=hidden name=\"loca_inve_busc\" value=\"".$_POST['loca_inve_busc']."\">
	        <input type=hidden name=\"codi_depe\" value=\"".$_POST['codi_depe']."\">
	        <input type=hidden name=\"codi_pers\" value=\"".$_POST['codi_pers']."\">
	    ";
	}
	else
	{
    	//OBTENER PERSONAL
        $arra_options_pers[]="<- Seleccione Usuario ->";
        $result=$Db->query("select * from mp_admi_pers where esta_pers='1' order by appa_pers,apma_pers,nomb_pers");
    	foreach($result as $rows)
    	    $arra_options_pers[$rows['iden_pers']]=" [".$rows['ndoc_pers']."] ".$rows['appa_pers']." ".$rows['apma_pers'].", ".$rows['nomb_pers'];
    	//FIN OBTENER PERSONAL
	
        //OBTENER LOCAL
        $arra_options_loca[]="<- Seleccione Local ->";
        $result=$Db->query("select * from mp_admi_loca where esta_loca='1' order by nom1_loca");
    	foreach($result as $rows)
    	    $arra_options_loca[$rows['codi_loca']]=$rows['nom1_loca']." [".$rows['dire_loca']."]";
    	//FIN OBTENER LOCAL
	
    	//OBTENER DEPENDENCIA
        $arra_options_depe[]="<- Seleccione Dependencia ->";
        $result=$Db->query("select * from mp_admi_depe where esta_depe='1' order by nomb_depe");
    	foreach($result as $rows)
    	    $arra_options_depe[$rows['codi_depe']]=$rows['nomb_depe'];
    	//FIN OBTENER DEPENDENCIA
    	
    	if(!isset($_POST['loca_inve_busc'])) $_POST['loca_inve_busc'] = '';
    	if(!isset($_POST['codi_depe'])) $_POST['codi_depe'] = '';
    	if(!isset($_POST['codi_pers'])) $_POST['codi_pers'] = '';

        echo"<main>";
        echo $html->put_select_buscador("Local",'loca_inve_busc',$arra_options_loca,$_POST['loca_inve_busc']," onchange=\"document.form.submit()\"");
        echo $html->put_select_buscador(CONST_SUBTITLE_DEPENDENCIA,'codi_depe',$arra_options_depe,$_POST['codi_depe'],"onchange=\"document.form.submit()\"");
        echo $html->put_select_buscador('Usuario','codi_pers',$arra_options_pers,$_POST['codi_pers'],"onchange=\"document.form.submit()\"");
        echo"</main>";
	}
    echo"<main>";
    echo $html->put_text('text',"CÓDIGO&nbsp;DEL&nbsp;BIEN","Ingrese CÓDIGO",'codi_patr',(isset($_POST['codi_patr']) ? $_POST['codi_patr'] : ''),'','15','');
    echo $html->put_button_colum("&nbsp;","Buscar Bien &raquo;","return f_buscar_bien()");
    	//if($_SESSION['iden_oper']==1)
    	//    echo $html->put_button_colum("&nbsp;","Búsqueda Avanzada &raquo;","return f_buscar_avanzado()");
    echo"</main>";

    if(isset($_POST['codi_patr']) && $_POST['codi_patr'])
    {
    	$busc_item_pagi=20;      //cantidad de items por pagina

    	$result=$Db->query("select * from mp_inve_siga where codigo_patrimonial like '%".$_POST['codi_patr']."%' OR codigo_barra like '%".$_POST['codi_patr']."%' OR nro_serie like '%".$_POST['codi_patr']."%' OR observaciones like '%".$_POST['codi_patr']."%'");
    	$busc_tota_item=0;
    	foreach($result as $rows)
    	{       
    		$busc_tota_item++;
    		$_POST['codi_patr_sele']=$rows['codigo_patrimonial'];
    	}

        if($busc_tota_item==1)
        {
            $arra_options_iest[]="<- Seleccione Estado ->";
            $arra_options_iest['Bueno']="Bueno";
            $arra_options_iest['Regular']="Regular";
            $arra_options_iest['Malo']="Malo";
            
            $arra_options_iuso[]="<- Seleccione Uso ->";
            $arra_options_iuso['SI']="SI";
            $arra_options_iuso['NO']="NO";
            
            $arra_options_vali[]="<- Seleccione ->";
            $arra_options_vali[1]="SI";
            $arra_options_vali[2]="NO";
            
            //$result_pagi=$Db->query("select * from mp_inve_siga where codigo_patrimonial='".$_POST['codi_patr']."'");
            $result_pagi=$Db->query("select * from mp_inve_view_regi where codigo_patrimonial='".$_POST['codi_patr_sele']."'");
            foreach($result_pagi as $rows)
            {
                $_POST['codi_patr_regi']=$rows['codigo_patrimonial'];
                echo"<input type=hidden name=\"codi_patr_regi\" value=\"".$_POST['codi_patr_regi']."\">";
                echo"<input type=hidden name=\"codi_regi\" value=\"".$rows['codi_regi']."\">";
                
                if(empty($rows['iest_regi'])) $rows['iest_regi']=$rows['nombre'];
                if(empty($rows['iuso_regi'])) $rows['iuso_regi']=$rows['uso'];
                
                //$aler_usua=""; // Already initialized
                $ndoc_pers_siga=$rows['docum_identidad'];
                if($ndoc_pers_sele!=$ndoc_pers_siga)
                    $aler_usua="<font color=red>";
            
                echo"<main>";
                if($rows['codi_regi']>0)
                    echo $html->put_title_demand("Información del Bien <font color=red>[BIEN INVENTARIADO EL ".substr($rows['fdig_regi'],0,4)."-".substr($rows['fdig_regi'],4,2)."-".substr($rows['fdig_regi'],6,2)."]");
                else
                    echo $html->put_title_demand("Información del Bien");
                echo $html->put_text('text',"CÓDIGO&nbsp;PATRIMONIAL","",'info_cpat',$rows['codigo_patrimonial'],'','15',' disabled');
                echo $html->put_text('text',"CÓDIGO&nbsp;DE&nbsp;BARRAS","",'info_cbar',$rows['codigo_barra'],'','15',' disabled');
                echo $html->put_text('text',"DESCRIPCIÓN","",'info_desc',$rows['descripcion'],'','15',' disabled');
                echo"</main><main>";
                echo $html->put_text('text',"MARCA","",'info_marc',$rows['marca'],'','15',' disabled');
                echo $html->put_text('text',"MODELO","",'info_marc',$rows['modelo'],'','15',' disabled');
                echo $html->put_text('text',"NRO.SERIE","",'info_marc',$rows['nro_serie'],'','15',' disabled');
                echo"</main><main>";
                echo $html->put_text('text',"COLOR","",'info_marc',$rows['color'],'','15',' disabled');
                echo $html->put_text('text',"ESTADO","",'info_marc',$rows['nombre'],'','15',' disabled');
                echo $html->put_text('text',"FECHA&nbsp;ALTA","",'info_marc',$rows['fecha_alta'],'','15',' disabled');
                echo"</main><main>";
                echo $html->put_text('text',"UBICACIÓN","",'info_marc',$rows['ubicac_fisica'],'','15',' disabled');
                echo $html->put_text('text',"$aler_usua USUARIO</font>","",'info_marc',$rows['usuario'],'','15',' disabled');
                echo $html->put_text('text',"OBSERVACIONES","",'info_marc',$rows['observaciones'],'','15',' disabled');
                echo $html->put_title_demand("Registrar Inventario");
                echo $html->put_select("ESTADO",'iest_regi',$arra_options_iest,$rows['iest_regi'],"");
                echo $html->put_select("EN&nbsp;USO",'iuso_regi',$arra_options_iuso,$rows['iuso_regi'],"");
                echo $html->put_text('text',"CÓDIGO&nbsp;ARE","",'iare_regi',$rows['iare_regi'],'','15','');
                echo"</main>";
                
                //OBTENER PERSONAL
                $arra_options_pers=[];
                $arra_options_pers[]="<- Seleccione Usuario ->";
                $result=$Db->query("select * from mp_admi_pers where esta_pers='1' order by appa_pers,apma_pers,nomb_pers");
    	        foreach($result as $rows2)
    	            $arra_options_pers[$rows2['iden_pers']]=" [".$rows2['ndoc_pers']."] ".$rows2['appa_pers']." ".$rows2['apma_pers'].", ".$rows2['nomb_pers'];
    	        //FIN OBTENER PERSONAL
	
                //OBTENER LOCAL
                $arra_options_loca=[];
                $arra_options_loca[]="<- Seleccione Local ->";
                $result=$Db->query("select * from mp_admi_loca where esta_loca='1' order by nom1_loca");
    	        foreach($result as $rows2)
    	            $arra_options_loca[$rows2['codi_loca']]=$rows2['nom1_loca']." [".$rows2['dire_loca']."]";
    	        //FIN OBTENER LOCAL
    	        
    	        //OBTENER DEPENDENCIA
    	        $arra_options_depe=[];
                $arra_options_depe[]="<- Seleccione Dependencia ->";
                $result=$Db->query("select * from mp_admi_depe where esta_depe='1' order by nomb_depe");
    	        foreach($result as $rows)
    	            $arra_options_depe[$rows['codi_depe']]=$rows['nomb_depe'];
    	        //FIN OBTENER DEPENDENCIA
    	        
    	        if(!isset($rows['vali_usua'])) $rows['vali_usua'] = '';
    	        if(!isset($rows['iobs_regi'])) $rows['iobs_regi'] = '';

                echo"<main>";
                echo $html->put_title_demand("Modificar Información");
                echo $html->put_text('text',"OBSERVACIÓN","",'iobs_regi',$rows['iobs_regi'],'','15','');
                echo $html->put_select("VALIDAR&nbsp;CAMBIO&nbsp;USUARIO",'vali_usua',$arra_options_vali,$rows['vali_usua']," onchange=\"f_cambio_usuario('$loca_nuev','$depe_nuev','$usua_nuev','$obse_nuev')\"");
                //echo $html->put_checkbox('CAMBIO&nbsp;DE&nbsp;USUARIO',"chec_camb",$rows['chec_camb']," onclick=\"f_cambio_usuario('$loca_nuev','$depe_nuev','$usua_nuev','$obse_nuev')\"",'Validar Usuario');
                //echo $html->put_checkbox('Cambio&nbsp;de&nbsp;Usuario',"chec_camb",$rows['chec_camb']," ",'Hola');
                
                //vali_usua
                
                //put_checkbox($label,$name,$value,$others,$label2='')
                echo"</main>";
                
                //echo"<main>";
                //echo $html->put_select_buscador("UBICACIÓN&nbsp;ACTUAL",'iubi_regi',$arra_options_loca,$rows['iubi_regi'],"");
                //echo $html->put_select_buscador('DEPENDENCIA&nbsp;ACTUAL','idep_regi',$arra_options_depe,$_POST['idep_regi'],"");
                //echo $html->put_select_buscador('USUARIO&nbsp;ACTUAL','iusu_regi',$arra_options_pers,$rows['iusu_regi'],"");
                //echo"</main>";
            }
        }
        else
        {
        	$busc_tota_pagi=ceil($busc_tota_item/$busc_item_pagi);
        	$busc_limi_pagi=($_POST['busc_pagi_actu']-1)*$busc_item_pagi;

        	//$result_pagi=$Db->query("select * from mp_inve_siga where codigo_patrimonial like '%".$_POST['codi_patr']."%' OR codigo_barra like '%".$_POST['codi_patr']."%' OR nro_serie like '%".$_POST['codi_patr']."%' OR observaciones like '%".$_POST['codi_patr']."%' order by descripcion asc limit $busc_limi_pagi,$busc_item_pagi");
        	$result_pagi=$Db->query("select * from mp_inve_view_regi where codigo_patrimonial like '%".$_POST['codi_patr']."%' OR codigo_barra like '%".$_POST['codi_patr']."%' OR nro_serie like '%".$_POST['codi_patr']."%' OR observaciones like '%".$_POST['codi_patr']."%' order by descripcion asc limit $busc_limi_pagi,$busc_item_pagi");

    	    echo"<div style=\"width:90%;max-width:800px;margin:auto;\">";
    	    echo $html->put_title_demand("BIENES ENCONTRADOS: $busc_tota_item BIENES");

    	    if($busc_tota_pagi>0  OR 5==5)
        		echo $html->put_page("1",$_POST['busc_pagi_actu'],$busc_tota_pagi,"");
    	    $head=['1'=>"Nº",'2'=>"CODIGO",'3'=>"<a href=\"javascript:f_cambiar_orden('descripcion asc')\"><font color=black>DESCRIPCION",'4'=>"NRO.SERIE",'5'=>"INFO.",'6'=>"REG."];
        	echo $html->put_table_responsive_open();
        	if($busc_tota_item OR 5==5)
    	    {
        		echo $html->put_table_responsive_header($head);
        		$cont=$busc_limi_pagi;
        		if(!isset($arra_falt_dato)) $arra_falt_dato=[];
    	    	foreach($result_pagi as $rows)
    		    {
        			$cont++;
        			$func="f_registrar";
        			unset($colo);
        			$colo = '';
        			if($rows['codi_regi']>0)
        			{
        			    $colo="<font color=green>";
        			    $func="f_registrar_repetido";
        			}
        			$data=[	'1'=>$colo.$cont,
    	    		    '2'=>$colo.$rows['codigo_patrimonial']."<BR>CB:".$rows['codigo_barra'],
    		    		'3'=>$colo.utf8_encode(utf8_decode(strtoupper($rows['descripcion']))),  
    			    	'4'=>$colo.utf8_encode(utf8_decode(strtoupper($rows['nro_serie'])))."<BR>MARCA: ".$rows['marca']."<BR>MODELO: ".$rows['modelo'],
    				    '5'=>"<a href=\"javascript:alert('MARCA: $rows[marca] \\nMODELO: $rows[modelo] \\nCOLOR: $rows[color] \\nRESPONSABLE: $rows[usuario]  \\nUBICACION: $rows[ubicac_fisica]".(isset($arra_falt_dato[$rows['codigo_patrimonial']]) ? $arra_falt_dato[$rows['codigo_patrimonial']] : '')."')\"><img src=\"img/icons/info.svg\" width=\"20\">",
        				'6'=>"<a href=\"javascript:$func('$rows[codigo_patrimonial]')\"><img src=\"img/icons/edit.svg\" width=\"20\">",
        			];
    	    		//f_eliminar(codi,patr,barr,nomb)
    		    	echo $html->put_table_responsive_data($head,$data);
    			        //'6'=>"<a href=\"javascript:f_eliminar('$rows[docu_post]','$rows[appa_post]','$rows[apma_post]','$rows[nomb_post]','$rows[codi_post]')\"><img src=\"img/delete.png\" width=\"20\">",
        		}
        	}
    	    else
    		    echo $html->put_table_responsive_title("Usuario no tiene bienes asignados");
		
        	echo $html->put_table_responsive_close();
        	if($busc_tota_pagi>0  OR 5==5)
    	    	echo $html->put_page("2",$_POST['busc_pagi_actu'],$busc_tota_pagi,"");
        	echo"</div>";
        }
    }	
	
	if(isset($_POST['loca_inve']) AND isset($_POST['codi_depe']) AND isset($_POST['codi_pers']) AND isset($_POST['codi_patr_regi']) AND isset($_POST['codi_patr']) && !empty($_POST['loca_inve']) && !empty($_POST['codi_depe']) && !empty($_POST['codi_pers']))
	{
		echo $html->put_separator_demand("30");
		echo"
                        <div align=center class=\"foot\">
                                <center>
                                <div align=center class=\"foot2\">
                                        <div class=\"div_button_foot\" style=\"\">
                                            <button class=\"button_foot\" onclick=\"f_nueva_busqueda()\">Cancelar</button>
                                        </div>
                                        <div class=\"div_button_foot\"><center>
                                            <button class=\"button_foot\" onclick=\"return f_registro_inventario('".(isset($_POST['codi_patr_regi'])?$_POST['codi_patr_regi']:'')."','$aler_usua')\">Registrar</button>
                                        </div>
                                </div>
                        </div>
        ";
	}
?>
<center>
    <script>
        function poner_focus()
        {
            //alert('Hola');
            document.form.codi_patr.focus();
        }
        document.form.codi_patr.focus();
        navigator.geolocation.getCurrentPosition(function(position){
            let lat = position.coords.latitude;
            let long = position.coords.longitude;
            document.form.lati_inve.value=lat;
            document.form.long_inve.value=long;
        });
        //setTimeout(poner_focus,4000);
        
    </script>
	</form>
	</body>
</html>
