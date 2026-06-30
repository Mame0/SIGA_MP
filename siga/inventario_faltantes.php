<?
//classes/TCPDF/examples/personal_fotocheck.php
	require_once 'include/cabecera.php';
	require_once 'classes/Html.class.php';
	require_once 'classes/Db.class.php';
	$Db = new Db();
	if(!isset($_POST['busc_pagi_actu']))
		$_POST['busc_pagi_actu']=1;
	
	$fdig=date("YmdHis");
	
	if(!isset($_POST['codi_inve']))
	{
	    $result=$Db->query("select * from mp_inve_mant where acti_inve='1' AND esta_inve='1' order by fech_inve limit 1");
	    foreach($result as $rows)
	    {
	        $_POST['codi_inve']=$rows['codi_inve'];
	        $_POST['fech_inve']=$rows['fech_inve'];
	        $_POST['nomb_inve']=$rows['nomb_inve'];
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
	    
	<center><h4 style="color:#073A6B"><b>TOMA DE INVENTARIO<BR>[<?=(isset($_POST['fech_inve'])?$_POST['fech_inve']:'')?>] <?=(isset($_POST['nomb_inve'])?$_POST['nomb_inve']:'')?> <?=(isset($_POST['loca_dato'])?$_POST['loca_dato']:'')?> <?=(isset($_POST['depe_dato'])?$_POST['depe_dato']:'')?> <?=(isset($_POST['pers_dato'])?$_POST['pers_dato']:'')?></h4></b></center>
		<form name="form" method="post">
			<input type=hidden name="regi_inve">
			<input type=hidden name="lati_inve">
			<input type=hidden name="long_inve">
			<input type=hidden name="elim_inve">
			<input type=hidden name="obse_inve">
			<input type=hidden name="codi_regi">
			<input type=hidden name="busc_pagi_actu" value="<?=$_POST['busc_pagi_actu']?>">
			<input type=hidden name="codi_inve" value="<?=(isset($_POST['codi_inve'])?$_POST['codi_inve']:'')?>">
			<input type=hidden name="fech_inve" value="<?=(isset($_POST['fech_inve'])?$_POST['fech_inve']:'')?>">
			<input type=hidden name="nomb_inve" value="<?=(isset($_POST['nomb_inve'])?$_POST['nomb_inve']:'')?>">
			<input type=hidden name="loca_inve" value="<?=(isset($_POST['loca_inve'])?$_POST['loca_inve']:'')?>">
			<input type=hidden name="loca_dato" value="<?=(isset($_POST['loca_dato'])?$_POST['loca_dato']:'')?>">
			<input type=hidden name="loca_lati" value="<?=(isset($_POST['loca_lati'])?$_POST['loca_lati']:'')?>">
			<input type=hidden name="loca_long" value="<?=(isset($_POST['loca_long'])?$_POST['loca_long']:'')?>">
			<input type=hidden name="ulti_lect" value="<?=(isset($_POST['codi_patr'])?$_POST['codi_patr']:'')?>">
			<input type=hidden name="orde_inve" value="<?=(isset($_POST['orde_inve'])?$_POST['orde_inve']:'')?>">
<?
	$html=new htmlclass;
	
	   	//OBTENER PERSONAL
        $arra_options_pers[]="<- Seleccione Usuario ->";
        $result=$Db->query("select * from mp_admi_pers where esta_pers='1' order by appa_pers,apma_pers,nomb_pers");
    	foreach($result as $rows)
    	    $arra_options_pers[$rows['ndoc_pers']]=" [".$rows['ndoc_pers']."] ".$rows['appa_pers']." ".$rows['apma_pers'].", ".$rows['nomb_pers'];
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

        echo"<main>";
        echo $html->put_select_buscador("Local",'loca_inve_busc',$arra_options_loca,(isset($_POST['loca_inve_busc'])?$_POST['loca_inve_busc']:'')," onchange=\"document.form.submit()\"");
        echo $html->put_select_buscador(CONST_SUBTITLE_DEPENDENCIA,'codi_depe',$arra_options_depe,(isset($_POST['codi_depe'])?$_POST['codi_depe']:''),"onchange=\"document.form.submit()\"");
        echo $html->put_select_buscador('Usuario','codi_pers',$arra_options_pers,(isset($_POST['codi_pers'])?$_POST['codi_pers']:''),"onchange=\"document.form.submit()\"");
        echo"</main>";

    if(isset($_POST['codi_pers']) && $_POST['codi_pers'])
    {
    	$busc_item_pagi=20;      //cantidad de items por pagina

//$_POST['codi_pers']="40760101";

    	$result=$Db->query("select * from mp_inve_view_regi where docum_identidad='".$_POST['codi_pers']."'");
    	$busc_tota_item=0;
    	foreach($result as $rows)
    	{       
    		$busc_tota_item++;
    	}

        
        	$busc_tota_pagi=ceil($busc_tota_item/$busc_item_pagi);
        	$busc_limi_pagi=($_POST['busc_pagi_actu']-1)*$busc_item_pagi;

        	$result_pagi=$Db->query("select * from mp_inve_view_regi where docum_identidad='".$_POST['codi_pers']."' order by codi_regi desc,descripcion asc limit $busc_limi_pagi,$busc_item_pagi");

    	    echo"<div style=\"width:90%;max-width:800px;margin:auto;\">";
    	    echo $html->put_title_demand("BIENES ASIGNADOS: $busc_tota_item BIENES");

        	if($busc_tota_pagi>0  OR 5==5)
        		echo $html->put_page("1",$_POST['busc_pagi_actu'],$busc_tota_pagi,"");
    	    $head=['1'=>"Nº",'2'=>"CODIGO",'3'=>"<a href=\"javascript:f_cambiar_orden('descripcion asc')\"><font color=black>DESCRIPCION",'4'=>"NRO.SERIE",'5'=>"INFO."];
        	echo $html->put_table_responsive_open();
        	if($busc_tota_item OR 5==5)
    	    {
        		echo $html->put_table_responsive_header($head);
        		$cont=$busc_limi_pagi;
    	    	foreach($result_pagi as $rows)
    		    {
        			$cont++;
        			$func="f_registrar";
        			$colo="<font color=silver>";
        			if($rows['codi_regi']>0)
        			{
        			    $colo="<font color=green><b>";
        			    $func="f_registrar_repetido";
        			}
        			$data=[	'1'=>$colo.$cont,
    	    		    '2'=>$colo.$rows['codigo_patrimonial']."<BR>CB:".$rows['codigo_barra'],
    		    		'3'=>$colo.utf8_encode(utf8_decode(strtoupper($rows['descripcion']))),  
    			    	'4'=>$colo.utf8_encode(utf8_decode(strtoupper($rows['nro_serie'])))."<BR>MARCA: ".$rows['marca']."<BR>MODELO: ".$rows['modelo'],
    				    '5'=>"<a href=\"javascript:alert('MARCA: $rows[marca] \\nMODELO: $rows[modelo] \\nCOLOR: $rows[color] \\nRESPONSABLE: $rows[usuario]  \\nUBICACION: $rows[ubicac_fisica]".(isset($arra_falt_dato[$rows['codigo_patrimonial']])?$arra_falt_dato[$rows['codigo_patrimonial']]:'')."')\"><img src=\"img/icons/info.svg\" width=\"20\">",
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
	
	if(isset($_POST['loca_inve']) && $_POST['loca_inve'] AND isset($_POST['codi_depe']) && $_POST['codi_depe'] AND isset($_POST['codi_pers']) && $_POST['codi_pers'] AND isset($_POST['codi_patr_regi']) && $_POST['codi_patr_regi'] AND isset($_POST['codi_patr']) && $_POST['codi_patr'])
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
                                            <button class=\"button_foot\" onclick=\"return f_registro_inventario('".$_POST['codi_patr_regi']."')\">Registrar</button>
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
