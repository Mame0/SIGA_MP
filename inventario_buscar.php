<?
//classes/TCPDF/examples/personal_fotocheck.php
	require_once 'include/cabecera.php';
	require_once 'classes/Html.class.php';
	require_once 'classes/Db.class.php';
	$Db = new Db();
	if(!isset($_POST['busc_pagi_actu']))
		$_POST['busc_pagi_actu']=1;

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
		    
			function f_nueva_busqueda()
			{
			    document.form.codi_patr.value='';
			    //document.form.codi_patr_regi.value='';
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
	    
	<center><h4 style="color:#073A6B"><b>BUSCAR BIENES</h4></b></center>
		<form name="form" method="post">
			<input type=hidden name="regi_inve">
			<input type=hidden name="lati_inve">
			<input type=hidden name="long_inve">
			<input type=hidden name="elim_inve">
			<input type=hidden name="obse_inve">
			<input type=hidden name="codi_regi">
			<input type=hidden name="busc_pagi_actu" value="<?=(isset($_POST['busc_pagi_actu']) ? $_POST['busc_pagi_actu'] : '')?>">
			<input type=hidden name="codi_inve" value="<?=(isset($_POST['codi_inve']) ? $_POST['codi_inve'] : '')?>">
			<input type=hidden name="fech_inve" value="<?=(isset($_POST['fech_inve']) ? $_POST['fech_inve'] : '')?>">
			<input type=hidden name="nomb_inve" value="<?=(isset($_POST['nomb_inve']) ? $_POST['nomb_inve'] : '')?>">
			<input type=hidden name="loca_inve" value="<?=(isset($_POST['loca_inve']) ? $_POST['loca_inve'] : '')?>">
			<input type=hidden name="loca_dato" value="<?=(isset($_POST['loca_dato']) ? $_POST['loca_dato'] : '')?>">
			<input type=hidden name="loca_lati" value="<?=(isset($_POST['loca_lati']) ? $_POST['loca_lati'] : '')?>">
			<input type=hidden name="loca_long" value="<?=(isset($_POST['loca_long']) ? $_POST['loca_long'] : '')?>">
			<input type=hidden name="ulti_lect" value="<?=(isset($_POST['codi_patr']) ? $_POST['codi_patr'] : '')?>">
			<input type=hidden name="orde_inve" value="<?=(isset($_POST['orde_inve']) ? $_POST['orde_inve'] : '')?>">
<?
	$html=new htmlclass;
	
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

        	$busc_tota_pagi=ceil($busc_tota_item/$busc_item_pagi);
        	$busc_limi_pagi=($_POST['busc_pagi_actu']-1)*$busc_item_pagi;

        	//$result_pagi=$Db->query("select * from mp_inve_siga where codigo_patrimonial like '%".$_POST['codi_patr']."%' OR codigo_barra like '%".$_POST['codi_patr']."%' OR nro_serie like '%".$_POST['codi_patr']."%' OR observaciones like '%".$_POST['codi_patr']."%' order by descripcion asc limit $busc_limi_pagi,$busc_item_pagi");
        	$result_pagi=$Db->query("select * from mp_inve_view_regi where codigo_patrimonial like '%".$_POST['codi_patr']."%' OR codigo_barra like '%".$_POST['codi_patr']."%' OR nro_serie like '%".$_POST['codi_patr']."%' OR observaciones like '%".$_POST['codi_patr']."%' order by descripcion asc limit $busc_limi_pagi,$busc_item_pagi");

    	    echo"<div style=\"width:90%;max-width:800px;margin:auto;\">";
    	    echo $html->put_title_demand("BIENES ENCONTRADOS: $busc_tota_item BIENES");

    	    if($busc_tota_pagi>0  OR 5==5)
        		echo $html->put_page("1",$_POST['busc_pagi_actu'],$busc_tota_pagi,"");
    	    $head=['1'=>"Nº",'2'=>"CODIGO",'3'=>"<a href=\"javascript:f_cambiar_orden('descripcion asc')\"><font color=black>DESCRIPCION",'4'=>"NRO.SERIE",'5'=>"INFO."];
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
        			];
    		    	echo $html->put_table_responsive_data($head,$data);
        		}
        	}
    	    else
    		    echo $html->put_table_responsive_title("Usuario no tiene bienes asignados");
		
        	echo $html->put_table_responsive_close();
        	if($busc_tota_pagi>0  OR 5==5)
    	    	echo $html->put_page("2",$_POST['busc_pagi_actu'],$busc_tota_pagi,"");
        	echo"</div>";
    }	
	
	if(isset($_POST['loca_inve']) AND isset($_POST['codi_depe']) AND isset($_POST['codi_pers']) AND isset($_POST['codi_patr_regi']) AND isset($_POST['codi_patr']))
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
	</form>
	</body>
</html>
