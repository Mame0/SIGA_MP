<?
//classes/TCPDF/examples/personal_fotocheck.php
	require_once 'include/cabecera.php';
	require_once 'classes/Html.class.php';
	require_once 'classes/Db.class.php';
	$Db = new Db();
	if(!isset($_POST['busc_pagi_actu']))
		$_POST['busc_pagi_actu']=1;

    // Inicializar variables $_POST para evitar advertencias
    $vars = ['orig_loca_usua', 'codi_inve', 'elim_inve', 'codi_regi', 'obse_inve', 'loca_inve_busc', 'loca_inve', 'codi_patr', 'ulti_lect', 'lati_inve', 'long_inve', 'orde_inve', 'fech_inve', 'nomb_inve', 'loca_dato', 'loca_lati', 'loca_long'];
    foreach($vars as $var) {
        if(!isset($_POST[$var])) $_POST[$var] = '';
    }
	
	$fdig=date("YmdHis");
	
	if(empty($_POST['orig_loca_usua']))
	    $_POST['orig_loca_usua']='1';
	
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

	if(!empty($_POST['loca_inve_busc']))
	{
	    $_POST['loca_inve']=$_POST['loca_inve_busc'];
	    
	    $result=$Db->query("select * from mp_admi_loca where codi_loca='".$_POST['loca_inve']."'");
	    $flag_usua=0;
	    foreach($result as $rows)
	    {
	        $flag_usua++;
	        $_POST['loca_inve']=$rows['codi_loca'];
	        $_POST['loca_dato']="<BR>LOCAL: ".$rows['nom1_loca'];
	        $_POST['loca_lati']=$rows['lati_loca'];
	        $_POST['loca_long']=$rows['long_loca'];
	    }
	}

	if(!empty($_POST['loca_inve']) AND !empty($_POST['codi_patr']))
	{
	    $result=$Db->query("select * from mp_patr_siga where codigo_patrimonial='".$_POST['codi_patr']."' OR codigo_barra='".$_POST['codi_patr']."' limit 1");
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
	                    $resultx=$Db->query("update mp_patr_inve_regi set esta_regi='0' where codi_regi='".$rowsi['codi_regi']."'");
	                    $resultx=$Db->query("insert into mp_patr_inve_regi values('','".$_POST['codi_inve']."','".$_POST['loca_inve']."','".$rows['docum_identidad']."','".$rows['codigo_patrimonial']."','".$_POST['lati_inve']."','".$_POST['long_inve']."','','".$_SESSION['iden_oper']."','$fdig','1')");
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
	                $_POST['lati_inve']=(!empty($_POST['loca_lati']) ? $_POST['loca_lati'] : '');
	            if(empty($_POST['long_inve']))
	                $_POST['long_inve']=(!empty($_POST['loca_long']) ? $_POST['loca_long'] : '');
	            //si no fue inventariado, entonces lo registramos
	            $resulti=$Db->query("insert into mp_patr_inve_regi values('','".$_POST['codi_inve']."','".$_POST['loca_inve']."','".$rows['docum_identidad']."','".$rows['codigo_patrimonial']."','".$_POST['lati_inve']."','".$_POST['long_inve']."','','".$_SESSION['iden_oper']."','$fdig','1')");
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
	    if($flag==0)
	    {
	        if($_POST['ulti_lect']==$_POST['codi_patr'])
	        {
                $nomb_inve_val = !empty($_POST['nomb_inve']) ? $_POST['nomb_inve'] : '';
                $usua_dato_val = !empty($_POST['usua_dato']) ? $_POST['usua_dato'] : '';
                $usua_inve_val = !empty($_POST['usua_inve']) ? $_POST['usua_inve'] : '';
	            echo"
	            <form name=form_bien method=post action=\"patrimonio_inventario_nuevo_bien.php\">
	                <input type=hidden name=\"codi_patr\" value=\"".$_POST['codi_patr']."\">
	                <input type=hidden name=\"codi_inve\" value=\"".$_POST['codi_inve']."\">
        			<input type=hidden name=\"fech_inve\" value=\"".$_POST['fech_inve']."\">
        			<input type=hidden name=\"nomb_inve\" value=\"".$nomb_inve_val."\">
        			<input type=hidden name=\"usua_inve\" value=\"".$usua_inve_val."\">
        			<input type=hidden name=\"usua_dato\" value=\"".$usua_dato_val."\">
	            </form>
	            <script>
	                document.form_bien.submit();
	            </script>
	            ";
	            $_POST['ulti_lect']='';
	        }
	        else
	        {
	            $mens="ERROR: Bien no ubicado. Si desea agregarlo, vuelva a leerlo porfavor";
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
			function f_registrar()
			{
			    document.form.regi_post.value='1';
				document.form.action='';
				document.form.target="";
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
	    
	<center><h4 style="color:#073A6B"><b>TOMA DE INVENTARIO POR LOCAL<BR>[<?=$_POST['fech_inve']?>] <?=$_POST['nomb_inve']?> <?=$_POST['loca_dato']?></h4></b></center>
		<form name="form" method="post">
			<input type=hidden name="regi_inve">
			<input type=hidden name="lati_inve">
			<input type=hidden name="long_inve">
			<input type=hidden name="elim_inve">
			<input type=hidden name="obse_inve">
			<input type=hidden name="codi_regi">
			<input type=hidden name="busc_pagi_actu" value="<?=$_POST['busc_pagi_actu']?>">
			<input type=hidden name="codi_inve" value="<?=$_POST['codi_inve']?>">
			<input type=hidden name="fech_inve" value="<?=$_POST['fech_inve']?>">
			<input type=hidden name="nomb_inve" value="<?=$_POST['nomb_inve']?>">
			<input type=hidden name="loca_inve" value="<?=$_POST['loca_inve']?>">
			<input type=hidden name="loca_dato" value="<?=$_POST['loca_dato']?>">
			<input type=hidden name="loca_lati" value="<?=$_POST['loca_lati']?>">
			<input type=hidden name="loca_long" value="<?=$_POST['loca_long']?>">
			<input type=hidden name="ulti_lect" value="<?=$_POST['codi_patr']?>">
			<input type=hidden name="orde_inve" value="<?=$_POST['orde_inve']?>">
			<input type=hidden name="orig_loca_usua" value="<?=$_POST['orig_loca_usua']?>">
<?
	$html=new htmlclass;
	
	if(empty($_POST['loca_inve']))
	{
	    $arra_options_loca[]="<- Seleccione Local ->";
	    $result=$Db->query("select * from mp_admi_loca where esta_loca='1' order by nom1_loca");
    	foreach($result as $rows)
    	    $arra_options_loca[$rows['codi_loca']]=$rows['nom1_loca']." [".$rows['dire_loca']."]";
        echo"<main>";
	    echo $html->put_select("Local",'loca_inve_busc',$arra_options_loca,$_POST['loca_inve_busc']," onchange=\"document.form.submit()\"");
        //echo $html->put_button_colum("&nbsp;","Buscar usuario &raquo;","return f_buscar_usuario()");
        echo"</main>";
        
        
        
        
        
        echo"<BR>";
    	//$result_pagi=$Db->query("select * from mp_patr_inve_regi as a, mp_patr_siga as b where a.codi_patr=b.codigo_patrimonial AND codi_inve='".$_POST['codi_inve']."' AND codi_loca='".$_POST['loca_inve']."' AND esta_regi='1' order by $_POST[orde_inve] limit $busc_limi_pagi,$busc_item_pagi");

    	echo"<div style=\"width:90%;max-width:800px;margin:auto;\">";
    	//echo $html->put_title_demand("BIENES INVENTARIADOS: $busc_tota_item BIENES");

    	$head=['1'=>"Nº",'2'=>"LOCAL",'3'=>"BIENES<BR>SIGA",'4'=>"BIENES<BR>INVENTARIADOS",'5'=>"BIENES<BR>FALTANTES",'6'=>"VER"];
    	echo $html->put_table_responsive_open();
		echo $html->put_table_responsive_header($head);
		$cont=0;
		//foreach($result_pagi as $rows)
		foreach($arra_options_loca as $codi_loca => $nomb_loca)
		{
			$cont++;
			$data=[	'1'=>$cont,
			    '2'=>utf8_encode(utf8_decode($nomb_loca)),
				'3'=>"0",  
				'4'=>"0",
				'5'=>"0",
				'6'=>"<a href=\"javascript:f_local('$codi_loca','')\"><img src=\"img/icons/eye.svg\" width=\"20\">",
			];
			//f_eliminar(codi,patr,barr,nomb)
			echo $html->put_table_responsive_data($head,$data);
			    //'6'=>"<a href=\"javascript:f_eliminar('$rows[docu_post]','$rows[appa_post]','$rows[apma_post]','$rows[nomb_post]','$rows[codi_post]')\"><img src=\"img/delete.png\" width=\"20\">",
		}
    	echo $html->put_table_responsive_close();
    	echo"</div>";
        
        
        
        
        
        
	}
	else
	{
	    if(empty($_POST['orde_inve']))
	        $_POST['orde_inve']="fdig_regi desc";
    	echo"<main>";
    	echo $html->put_text('text',"CÓDIGO&nbsp;DEL&nbsp;BIEN","Ingrese CÓDIGO",'codi_patr','','','15','');
    	echo $html->put_button_colum("&nbsp;","Buscar bien &raquo;","return f_buscar_bien()");
    	//if($_SESSION['iden_oper']==1)
    	    echo $html->put_button_colum("&nbsp;","Búsqueda Avanzada &raquo;","return f_buscar_avanzado()");
    	echo"</main>";

    	$busc_item_pagi=20;      //cantidad de items por pagina
    	
        $arra_falt = [];
        $arra_falt_dato = [];
    	$result=$Db->query("select * from mp_patr_inve_falt where codi_inve='".$_POST['codi_inve']."' AND esta_falt='1'");
    	$busc_tota_item=0;
    	foreach($result as $rows)
    	{       
    		$arra_falt[$rows['codi_patr']]=$rows['codi_patr'];
    		$arra_falt_dato[$rows['codi_patr']]="\\n FALTANTE: [{$rows['nomb_resp']}] {$rows['obse_falt']}";
    	}
    	
    	$result=$Db->query("select * from mp_admi_oper");
        $arra_oper = [];
    	foreach($result as $rows)
    	{       
    		$arra_oper[$rows['iden_oper']]=$rows['appa_oper'];
    	}
	
    	$result=$Db->query("select * from mp_patr_inve_regi as a, mp_patr_siga as b where a.codi_patr=b.codigo_patrimonial AND codi_inve='".$_POST['codi_inve']."' AND codi_loca='".$_POST['loca_inve']."' AND esta_regi='1'");
    	$busc_tota_item=0;
    	foreach($result as $rows)
    	{       
    		$busc_tota_item++;
    	}

    	$busc_tota_pagi=ceil($busc_tota_item/$busc_item_pagi);
    	$busc_limi_pagi=($_POST['busc_pagi_actu']-1)*$busc_item_pagi;

    	$result_pagi=$Db->query("select * from mp_patr_inve_regi as a, mp_patr_siga as b where a.codi_patr=b.codigo_patrimonial AND codi_inve='".$_POST['codi_inve']."' AND codi_loca='".$_POST['loca_inve']."' AND esta_regi='1' order by $_POST[orde_inve] limit $busc_limi_pagi,$busc_item_pagi");

    	echo"<div style=\"width:90%;max-width:800px;margin:auto;\">";
    	echo $html->put_title_demand("BIENES INVENTARIADOS: $busc_tota_item BIENES");

    	if($busc_tota_pagi>0  OR 5==5)
    		echo $html->put_page("1",$_POST['busc_pagi_actu'],$busc_tota_pagi,"");
    	$head=['1'=>"Nº",'2'=>"COD.PATR.",'3'=>"<a href=\"javascript:f_cambiar_orden('descripcion asc')\"><font color=black>DESCRIPCION",'4'=>"NRO.SERIE",'5'=>"<a href=\"javascript:f_cambiar_orden('fdig_regi desc')\"><font color=black>FECHA</a>",'6'=>"INFO.",'7'=>"ELIM.",'8'=>"OBS."];
    	echo $html->put_table_responsive_open();
    	if($busc_tota_item OR 5==5)
    	{
    		echo $html->put_table_responsive_header($head);
    		$cont=$busc_limi_pagi;
    		foreach($result_pagi as $rows)
    		{
    			$cont++;
    			$colo='';
    			if(isset($arra_falt[$rows['codigo_patrimonial']]))
    			    $colo="<font color=white style=\"background-color:red\">";
    			
                // Safe access to arra_oper
                $oper_name = isset($arra_oper[$rows['digi_regi']]) ? $arra_oper[$rows['digi_regi']] : '';
                
    			$data=[	'1'=>$colo.$cont,
    			    '2'=>$colo.$rows['codigo_patrimonial']."<BR>CB:".$rows['codigo_barra'],
    				'3'=>$colo.utf8_encode(utf8_decode(strtoupper($rows['descripcion']))),  
    				'4'=>$colo.utf8_encode(utf8_decode(strtoupper($rows['nro_serie']))),
    				'5'=>$colo.substr($rows['fdig_regi'],6,2)."/".substr($rows['fdig_regi'],4,2)."/".substr($rows['fdig_regi'],0,4)."<BR>".substr($rows['fdig_regi'],8,2).":".substr($rows['fdig_regi'],10,2).":".substr($rows['fdig_regi'],12,2)."<BR>".$oper_name,
    				'6'=>$colo."<a href=\"javascript:alert('MARCA: $rows[marca] \\nMODELO: $rows[modelo] \\nCOLOR: $rows[color] \\nRESPONSABLE: $rows[usuario]  \\nUBICACION: $rows[ubicac_fisica]".(isset($arra_falt_dato[$rows['codigo_patrimonial']]) ? $arra_falt_dato[$rows['codigo_patrimonial']] : '')."')\"><img src=\"img/icons/info.svg\" width=\"20\">",
    				'7'=>$colo."<a href=\"javascript:f_eliminar('$rows[codi_regi]','$rows[codigo_patrimonial]','$rows[codigo_barra]','$rows[descripcion]')\"><img src=\"img/icons/trash.svg\" width=\"20\">",
    				'8'=>$colo."<a href=\"javascript:f_observacion('$rows[codi_regi]','$rows[obse_regi]')\"><img src=\"img/icons/edit.svg\" width=\"20\">",
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
	if(!empty($_POST['loca_inve']))
	{
		echo $html->put_separator_demand("30");
		echo"
                        <div align=center class=\"foot\">
                                <center>
                                <div align=center class=\"foot2\">
                                        <div class=\"div_button_foot\" style=\"\">
                                            <button class=\"button_foot\" onclick=\"f_consolidar()\">Consolidar Local</button>
                                        </div>
                                        <div class=\"div_button_foot\"><center>
                                            <button class=\"button_foot\" onclick=\"f_cambiar_local()\">Cambiar Local</button>
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
