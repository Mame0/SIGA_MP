<?
//classes/TCPDF/examples/personal_fotocheck.php
	require_once 'include/cabecera.php';
	require_once 'classes/Html.class.php';
	require_once 'classes/Db.class.php';
	$Db = new Db();
	if(!isset($_POST['busc_pagi_actu']))
		$_POST['busc_pagi_actu']=1;

    // Inicializar variables $_POST para evitar advertencias
    $vars = ['codi_inve', 'fech_inve', 'nomb_inve', 'orig_loca_usua', 'loca_inve', 'usua_inve', 'usua_dato', 'loca_dato', 'loca_lati', 'loca_long', 'codi_patr', 'orde_inve'];
    foreach($vars as $var) {
        if(!isset($_POST[$var])) $_POST[$var] = '';
    }
	
	if(!$_POST['codi_inve'])
	{
	    $result=$Db->query("select * from mp_patr_inve_mant where acti_inve='1' AND esta_inve='1' order by fech_inve limit 1");
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
		    function f_buscar_usuario()
			{
			    document.form.action='';
				document.form.target="";
				document.form.submit();
			}
			function f_recargar()
			{
				document.form.submit();
			}
			function f_regresar_local()
			{
			    document.form.action='patrimonio_inventario_local.php';
				document.form.target="";
				document.form.submit();
			}
			function f_regresar_usuario()
			{
			    document.form.action='patrimonio_inventario_usuario.php';
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
			function f_inventariar(codi,desc,orig)
			{
			    if(confirm('Seguro que desea registrar inventario de '+desc+'?'))
			    {
    			    document.form.codi_patr.value=codi;
	    		    if(orig==1)
		    	        document.form.action='patrimonio_inventario_local.php';
			        else
			            document.form.action='patrimonio_inventario_usuario.php';
    			    document.form.target="";
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
	    
	<center><h4 style="color:#073A6B"><b>
<?
    if($_POST['orig_loca_usua']==2)
    {
        if($_POST['loca_inve'])
    	    echo"TOMA DE INVENTARIO POR LOCAL - BUSCAR BIENES<BR>[$_POST[fech_inve]] $_POST[nomb_inve] $_POST[loca_dato] $_POST[usua_dato]";
        else
	        echo"TOMA DE INVENTARIO POR USUARIO - BUSCAR BIENES<BR>[$_POST[fech_inve]] $_POST[nomb_inve] $_POST[usua_dato]";
    }
    elseif($_POST['orig_loca_usua']==1)
    {
	    echo"TOMA DE INVENTARIO POR LOCAL - BUSCAR BIENES<BR>[$_POST[fech_inve]] $_POST[nomb_inve] $_POST[loca_dato]";
    }
    else
	    echo"BUSCAR BIENES";
?>
	</h4></b></center>
		<form name="form" method="post">
			<input type=hidden name="regi_inve">
			<input type=hidden name="busc_pagi_actu" value="<?=$_POST['busc_pagi_actu']?>">
			<input type=hidden name="codi_inve" value="<?=$_POST['codi_inve']?>">
			<input type=hidden name="fech_inve" value="<?=$_POST['fech_inve']?>">
			<input type=hidden name="nomb_inve" value="<?=$_POST['nomb_inve']?>">
			<input type=hidden name="usua_inve" value="<?=$_POST['usua_inve']?>">
			<input type=hidden name="usua_dato" value="<?=$_POST['usua_dato']?>">
			<input type=hidden name="loca_inve" value="<?=$_POST['loca_inve']?>">
			<input type=hidden name="loca_dato" value="<?=$_POST['loca_dato']?>">
			<input type=hidden name="loca_lati" value="<?=$_POST['loca_lati']?>">
			<input type=hidden name="loca_long" value="<?=$_POST['loca_long']?>">
			<input type=hidden name="orig_loca_usua" value="<?=$_POST['orig_loca_usua']?>">
<?
	$html=new htmlclass;
    	
    	echo"<main>";
    	//echo $html->put_title_demand("INGRESE DOCUMENTO");
    	//echo $html->put_select("Plaza",'codi_plaz',$arra_options_regi,$_POST['codi_plaz'],"");
    	echo $html->put_text('text',"CÓDIGO&nbsp;DEL&nbsp;BIEN","Ingrese CÓDIGO",'codi_patr',$_POST['codi_patr'],'','15','');
    	echo $html->put_button_colum("&nbsp;","Buscar bien &raquo;","return f_buscar_bien()");
    	echo"</main>";
    	//echo"<main>";
    	//echo $html->put_select("Formato",'codi_form',$arra_options_form,$_POST['codi_form'],"");
    	echo"</main>";
if($_POST['codi_patr'])
{
    	$busc_item_pagi=10000;      //cantidad de items por pagina
	
    	$result=$Db->query("select * from mp_patr_siga where codigo_patrimonial like '%".$_POST['codi_patr']."%' OR codigo_barra like '%".$_POST['codi_patr']."%' OR nro_serie like '%".$_POST['codi_patr']."%' OR observaciones like '%".$_POST['codi_patr']."%'");
    	$busc_tota_item=0;
    	foreach($result as $rows)
    	{       
    		$busc_tota_item++;
    	}

    	$busc_tota_pagi=ceil($busc_tota_item/$busc_item_pagi);
    	$busc_limi_pagi=($_POST['busc_pagi_actu']-1)*$busc_item_pagi;

    	$result_pagi=$Db->query("select * from mp_patr_siga where codigo_patrimonial like '%".$_POST['codi_patr']."%' OR codigo_barra like '%".$_POST['codi_patr']."%' OR nro_serie like '%".$_POST['codi_patr']."%' OR observaciones like '%".$_POST['codi_patr']."%' order by descripcion asc limit $busc_limi_pagi,$busc_item_pagi");

//echo"<HR>select * from mp_patr_siga where codigo_patrimonial like '%".$_POST['codi_patr']."%' OR codigo_barra like '%".$_POST['codi_patr']."%' OR nro_serie like '%".$_POST['codi_patr']."%' OR observaciones like '%".$_POST['codi_patr']."%' order by descripcion asc limit $busc_limi_pagi,$busc_item_pagi<HR>";    	
    	echo"<div style=\"width:90%;max-width:800px;margin:auto;\">";
    	echo $html->put_title_demand("BIENES ENCONTRADOS: $busc_tota_item BIENES");

    	if($busc_tota_pagi>0  OR 5==5)
    		echo $html->put_page("1",$_POST['busc_pagi_actu'],$busc_tota_pagi,"");
    	if($_POST['usua_inve'] OR $_POST['loca_inve'])
    	    $head=['1'=>"Nº",'2'=>"COD.PATR.",'3'=>"DESCRIPCION",'4'=>"COD.BARR.",'5'=>"NRO.SERIE",'6'=>"RESP.",'7'=>"INVE.",'8'=>"SELE."];
        else
    	    $head=['1'=>"Nº",'2'=>"COD.PATR.",'3'=>"DESCRIPCION",'4'=>"COD.BARR.",'5'=>"NRO.SERIE",'6'=>"RESP.",'7'=>"INVE."];
    	echo $html->put_table_responsive_open();
    	if($busc_tota_item OR 5==5)
    	{
    		echo $html->put_table_responsive_header($head);
    		$cont=$busc_limi_pagi;
    		foreach($result_pagi as $rows)
    		{
    			$cont++;
    			$chec="<img src=\"img/icons/delete-remove-uncheck-svgrepo-com.svg\" width=\"20\">";
    			
    			//para buscar si ha sido inventariado
    			$arra_ubic[$rows['codigo_patrimonial']]='Sin Inventario';
    			$i=0;
            	$result_inv=$Db->query("select * from mp_patr_inve_regi where codi_inve='".$_POST['codi_inve']."' AND codi_patr='".$rows['codigo_patrimonial']."' AND esta_regi='1'");
            	foreach($result_inv as $rows_inv)
            	{
            	    $i++;
            	    $chec="<img src=\"img/icons/ok-svgrepo-com.svg\" width=\"20\">";
                    
                    if(!isset($rows_inv['lati_regi']) || !$rows_inv['lati_regi'])
    	                $rows_inv['lati_regi']='-16.399236';
    	            if(!isset($rows_inv['long_regi']) || !$rows_inv['long_regi'])
    	                $rows_inv['long_regi']='-71.52795';
    	        
    	            $resultd=$Db->query("SELECT *,(acos(sin(radians(lati_loca)) * sin(radians(".$rows_inv['lati_regi'].")) + cos(radians(lati_loca)) * cos(radians(".$rows_inv['lati_regi'].")) * cos(radians(long_loca) - radians(".$rows_inv['long_regi']."))) * 6378) as distanciaPunto1Punto2 from mp_admi_loca order by distanciaPunto1Punto2 limit 1");

                    foreach($resultd as $rowsd)
                        $arra_ubic[$rows_inv['codi_patr']]=substr($rows_inv['fdig_regi'],6,2).'/'.substr($rows_inv['fdig_regi'],4,2).'/'.substr($rows_inv['fdig_regi'],0,4).' '.substr($rows_inv['fdig_regi'],8,2).':'.substr($rows_inv['fdig_regi'],10,2).':'.substr($rows_inv['fdig_regi'],10,2).' - '.$rowsd['nom1_loca'].' ['.$rowsd['dire_loca'].']';
            	}
            	
                $colo = '';
    			$data=[	'1'=>$colo.$cont,
    				'2'=>$rows['codigo_patrimonial'],
    				'3'=>utf8_encode(utf8_decode(strtoupper($rows['descripcion']))),  
    				'4'=>utf8_encode(utf8_decode(strtoupper($rows['codigo_barra']))),
    				'5'=>utf8_encode(utf8_decode(strtoupper($rows['nro_serie']))),
    				'6'=>utf8_encode(utf8_decode(strtoupper($rows['usuario']))),
    				'7'=>"<a href=\"javascript:alert('COLOR: $rows[color] \\nMARCA: $rows[marca] \\nMODELO: $rows[modelo] \\nOBSERVACIONES: $rows[observaciones] \\n---------------------\\nINVENTARIO: ".$arra_ubic[$rows['codigo_patrimonial']]."')\">$chec",
    			];
    			if($_POST['usua_inve'] OR $_POST['loca_inve'])
    			    $data[8]="<a href=\"javascript:f_inventariar('$rows[codigo_patrimonial]','$rows[descripcion]','$_POST[orig_loca_usua]')\"><img src=\"img/icons/open-select-hand-gesture-svgrepo-com.svg\" width=\"20\">";
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
}    	
	if($_POST['orig_loca_usua'])
	{
		echo $html->put_separator_demand("30");
		echo"
                        <div align=center class=\"foot\">
                                <center>
                                <div align=center class=\"foot2\">
                                        <div class=\"div_button_foot\" style=\"\">
                                                <button class=\"button_foot\" onclick=\"f_regresar()\">Cancelar</button>
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
    <script>document.form.codi_patr.focus();</script>
	</form>
	</body>
</html>
