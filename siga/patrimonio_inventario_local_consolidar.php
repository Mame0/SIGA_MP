<?
//classes/TCPDF/examples/personal_fotocheck.php
	require_once 'include/cabecera.php';
	require_once 'classes/Html.class.php';
	require_once 'classes/Db.class.php';
	$Db = new Db();
	if(!isset($_POST['busc_pagi_actu']))
		$_POST['busc_pagi_actu']=1;
	
	$fdig=date("YmdHis");
	
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
			function f_recargar()
			{
				document.form.submit();
			}
			function f_inventario()
			{
			    document.form.action='patrimonio_inventario_local.php';
				document.form.submit();
			}
			function f_usuario(docu,dato)
			{
			    document.form.usua_inve.value=docu;
			    document.form.usua_dato.value=dato;
			    document.form.action='patrimonio_inventario_usuario.php';
				document.form.submit();
			}
			function f_cambiar_local()
			{
			    document.form.loca_inve.value='';
			    document.form.loca_dato.value='';
			    document.form.action='patrimonio_inventario_local.php';
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
	    
	<center><h4 style="color:#073A6B"><b>CONSOLIDADO POR LOCAL<BR>[<?=$_POST['fech_inve']?>] <?=$_POST['nomb_inve']?> <?=$_POST['loca_dato']?></h4></b></center>
		<form name="form" method="post">
			<input type=hidden name="regi_inve">
			<input type=hidden name="lati_inve">
			<input type=hidden name="long_inve">
			<input type=hidden name="elim_inve">
			<input type=hidden name="busc_pagi_actu" value="<?=$_POST['busc_pagi_actu']?>">
			<input type=hidden name="codi_inve" value="<?=$_POST['codi_inve']?>">
			<input type=hidden name="fech_inve" value="<?=$_POST['fech_inve']?>">
			<input type=hidden name="nomb_inve" value="<?=$_POST['nomb_inve']?>">
			<input type=hidden name="loca_inve" value="<?=$_POST['loca_inve']?>">
			<input type=hidden name="loca_dato" value="<?=$_POST['loca_dato']?>">
			<input type=hidden name="usua_inve">
			<input type=hidden name="usua_dato">
<?
	$html=new htmlclass;
	
    	$busc_item_pagi=100;      //cantidad de items por pagina
	
    	$result=$Db->query("select docum_identidad,usuario,count(*) from mp_patr_inve_regi as a, mp_patr_siga as b where a.codi_patr=b.codigo_patrimonial AND codi_inve='".$_POST['codi_inve']."' AND codi_loca='".$_POST['loca_inve']."' AND esta_regi='1' group by docum_identidad");
    	$busc_tota_item=0;
    	foreach($result as $rows)
    	{       
    		$busc_tota_item++;
    	}

    	$busc_tota_pagi=ceil($busc_tota_item/$busc_item_pagi);
    	$busc_limi_pagi=($_POST['busc_pagi_actu']-1)*$busc_item_pagi;

    	$result_pagi=$Db->query("select docum_identidad,usuario,count(*) as inventariados from mp_patr_inve_regi as a, mp_patr_siga as b where a.codi_patr=b.codigo_patrimonial AND codi_inve='".$_POST['codi_inve']."' AND codi_loca='".$_POST['loca_inve']."' AND esta_regi='1' group by docum_identidad order by usuario asc limit $busc_limi_pagi,$busc_item_pagi");

    	echo"<div style=\"width:90%;max-width:800px;margin:auto;\">";
    	//echo $html->put_title_demand("BIENES INVENTARIADOS: $busc_tota_item BIENES");

    	if($busc_tota_pagi>0  OR 5==5)
    		echo $html->put_page("1",$_POST['busc_pagi_actu'],$busc_tota_pagi,"");
    	$head=['1'=>"Nº",'2'=>"DNI",'3'=>"APELLIDOS Y NOMBRES",'4'=>"BIENES<BR>SIGA",'5'=>"BIENES<BR>INVENTARIADOS",'6'=>"BIENES<BR>FALTANTES",'7'=>"VER"];
    	echo $html->put_table_responsive_open();
    	if($busc_tota_item OR 5==5)
    	{
    		echo $html->put_table_responsive_header($head);
    		$cont=$busc_limi_pagi;
    		foreach($result_pagi as $rows)
    		{
    			$cont++;
    			
    	        $result_siga=$Db->query("select count(*) as siga from mp_patr_siga where docum_identidad='$rows[docum_identidad]'");
    		    foreach($result_siga as $rows_siga)
    			    $arra_siga[$rows['docum_identidad']]=$rows_siga['siga'];
    			$data=[	'1'=>$cont,
    			    '2'=>$rows['docum_identidad'],
    				'3'=>utf8_encode(utf8_decode(strtoupper($rows['usuario']))),  
    				'4'=>$arra_siga[$rows['docum_identidad']],
    				'5'=>$rows['inventariados'],
    				'6'=>$arra_siga[$rows['docum_identidad']]-$rows['inventariados'],
    				'7'=>"<a href=\"javascript:f_usuario('$rows[docum_identidad]','<BR>[$rows[docum_identidad]] $rows[usuario]')\"><img src=\"img/icons/eye.svg\" width=\"20\">",
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
    	
		echo $html->put_separator_demand("30");
		echo"
                        <div align=center class=\"foot\">
                                <center>
                                <div align=center class=\"foot2\">
                                        <div class=\"div_button_foot\" style=\"\">
                                            <button class=\"button_foot\" onclick=\"f_inventario()\">Regresar</button>
                                        </div>
                                        <div class=\"div_button_foot\"><center>
                                            <button class=\"button_foot\" onclick=\"f_cambiar_local()\">Cambiar Local</button>
                                        </div>
                                </div>
                        </div>
        ";
?>
<center>
	</form>
	</body>
</html>
