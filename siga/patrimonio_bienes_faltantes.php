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
	if($_POST['elim_bien'])
	{
	    $result=$Db->query("update mp_patr_inve_falt set esta_falt=0 where codi_falt='$_POST[elim_bien]'");
	}
	if($_POST['codi_falt'] AND $_POST['obse_falt'])
	{
	    $result=$Db->query("update mp_patr_inve_falt set obse_falt='$_POST[obse_falt]' where codi_falt='$_POST[codi_falt]'");
	}
	if($_POST['agre_bien'])
	{
	    $result=$Db->query("select * from mp_patr_siga where codigo_patrimonial='".$_POST['agre_bien']."' OR codigo_barra='".$_POST['agre_bien']."'");
	    $flag=0;
	    foreach($result as $rows)
	    {
	        $flag++;
	        
	        //verificamos si ya esta dentro de los faltantes
	        $flag_inve=0;
	        $resulti=$Db->query("select * from mp_patr_inve_falt where codi_patr='".$rows['codigo_patrimonial']."' AND codi_inve='".$_POST['codi_inve']."' AND esta_falt='1'");
	        foreach($resulti as $rowsi)
	        {
	            $flag_inve++;
	            echo"<script>alert('ERROR: Bien ya fue agregado como faltante')</script>";
	        }
	        if($flag_inve==0)
	        {
	            //si no fue inventariado, entonces lo registramos
	            $resulti=$Db->query("insert into mp_patr_inve_falt values('','".$_POST['codi_inve']."','".$_POST['agre_bien']."','".$_POST['obse_falt']."','".$_SESSION['iden_oper']."','$fdig','1')");
	            echo"<script>alert('Bien faltante agregado correctamente')</script>";
	        }
	    }
	    if($flag==0)
	        echo"
	            <script>
	                alert('ERROR: Bien no ubicado')
	            </script>
	        ";
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
			function f_eliminar(codi,patr,nomb,barr)
			{
			    if(confirm('Seguro que desea eliminar bien faltante?\n'+nomb))
			    {
			        document.form.elim_bien.value=codi;
			        document.form.submit();
			    }
			    else
			        return false;
			}
			function f_recargar()
			{
				document.form.submit();
			}
			function f_registrar()
			{
			    document.form.regi_post.value='1';
				document.form.action='';
				document.form.target="";
				document.form.submit();
			}
			function f_agregar_faltante()
			{
			    codi=window.prompt('Ingrese Codigo de Bien Faltante');
			    if(codi)
			    {
			        document.form.agre_bien.value=codi;
			        document.form.submit();
			    }
			    else
			        return false;
		    }
		    function f_observacion(codi,obse)
			{
			    nuev_obse=window.prompt('Ingrese Observacion',obse);
			    if(nuev_obse)
			    {
			        document.form.obse_falt.value=nuev_obse;
			        document.form.codi_falt.value=codi;
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
	    
	<center><h4 style="color:#073A6B"><b>BIENES FALTANTES <BR>[<?=$_POST['fech_inve']?>] <?=$_POST['nomb_inve']?></h4></b></center>
		<form name="form" method="post">
			<input type=hidden name="regi_inve">
			<input type=hidden name="agre_bien">
			<input type=hidden name="elim_bien">
			<input type=hidden name="obse_falt">
			<input type=hidden name="codi_falt">
			<input type=hidden name="codi_inve" value="<?=$_POST['codi_inve']?>">
			<input type=hidden name="fech_inve" value="<?=$_POST['fech_inve']?>">
			<input type=hidden name="nomb_inve" value="<?=$_POST['nomb_inve']?>">
			<input type=hidden name="busc_pagi_actu" value="<?=$_POST['busc_pagi_actu']?>">
<?
	$html=new htmlclass;
    	
    	$busc_item_pagi=10000;      //cantidad de items por pagina
	
    	$result=$Db->query("select * from mp_patr_siga as a, mp_patr_inve_falt as b where a.codigo_patrimonial=b.codi_patr AND b.codi_inve='$_POST[codi_inve]'  AND esta_falt='1'");
    	$busc_tota_item=0;
    	foreach($result as $rows)
    	{       
    		$busc_tota_item++;
    	}

    	$busc_tota_pagi=ceil($busc_tota_item/$busc_item_pagi);
    	$busc_limi_pagi=($_POST['busc_pagi_actu']-1)*$busc_item_pagi;

    	$result_pagi=$Db->query("select * from mp_patr_siga as a, mp_patr_inve_falt as b where a.codigo_patrimonial=b.codi_patr AND b.codi_inve='$_POST[codi_inve]' AND esta_falt='1' order by descripcion,codigo_barra asc limit $busc_limi_pagi,$busc_item_pagi");

    	echo"<div style=\"width:90%;max-width:800px;margin:auto;\">";
    	echo $html->put_title_demand("BIENES FALTANTES: $busc_tota_item BIENES");

    	if($busc_tota_pagi>0  OR 5==5)
    		echo $html->put_page("1",$_POST['busc_pagi_actu'],$busc_tota_pagi,"");
    	$head=['1'=>"Nº",'2'=>"COD.PATR.",'3'=>"DESCRIPCION",'4'=>"COD.BARR.",'5'=>"NRO.SERIE",'6'=>"RESPONSABLE",'7'=>"INV.",'8'=>"ELIM.",'9'=>"EDIT"];
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
            	$result_inv=$Db->query("select * from mp_patr_inve_regi where codi_inve='".$_POST['codi_inve']."' AND codi_patr='".$rows['codigo_patrimonial']."'");
            	foreach($result_inv as $rows_inv)
            	{
            	    $i++;
            	    if(!$rows_inv['lati_regi'])
            	        $rows_inv['lati_regi']="-16.399236";
        	        if(!$rows_inv['long_regi'])
            	        $rows_inv['long_regi']="-71.52795";
            	    $chec="<img src=\"img/icons/ok-svgrepo-com.svg\" width=\"20\">";
            	    $resultd=$Db->query("SELECT *,(acos(sin(radians(lati_loca)) * sin(radians($rows_inv[lati_regi])) + cos(radians(lati_loca)) * cos(radians($rows_inv[lati_regi])) * cos(radians(long_loca) - radians($rows_inv[long_regi]))) * 6378) as distanciaPunto1Punto2 from mp_admi_loca order by distanciaPunto1Punto2 limit 1");
                    foreach($resultd as $rowsd)
                        $arra_ubic[$rows_inv['codi_patr']]=substr($rows_inv['fdig_regi'],6,2).'/'.substr($rows_inv['fdig_regi'],4,2).'/'.substr($rows_inv['fdig_regi'],0,4).' '.substr($rows_inv['fdig_regi'],8,2).':'.substr($rows_inv['fdig_regi'],10,2).':'.substr($rows_inv['fdig_regi'],10,2).' - '.$rowsd['nom1_loca'].' ['.$rowsd['dire_loca'].']';
            	}
    	
    			$data=[	'1'=>$colo.$cont,
    				'2'=>$rows['codigo_patrimonial'],
    				'3'=>utf8_encode(utf8_decode(strtoupper($rows['descripcion']))),  
    				'4'=>utf8_encode(utf8_decode(strtoupper($rows['codigo_barra']))),
    				'5'=>utf8_encode(utf8_decode(strtoupper($rows['nro_serie']))),
    				'6'=>utf8_encode(utf8_decode(strtoupper($rows['nomb_resp']))),
    				'7'=>"<a href=\"javascript:alert('COLOR: $rows[color] \\nMARCA: $rows[marca] \\nMODELO: $rows[modelo] \\nOBSERVACION: $rows[obse_falt] \\n---------------------\\nINVENTARIO: ".$arra_ubic[$rows['codigo_patrimonial']]."')\">$chec",
    				'8'=>"<a href=\"javascript:f_eliminar('$rows[codi_falt]','$rows[codigo_patrimonial]','$rows[descripcion]','$rows[codigo_barra]')\"><img src=\"img/icons/trash.svg\" width=\"20\">",
    				'9'=>"<a href=\"javascript:f_observacion('$rows[codi_falt]','$rows[obse_falt]')\"><img src=\"img/icons/edit.svg\" width=\"20\">",
    			];
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
    	
	//if($busc_tota_item>0 AND 5==6)
	//{
		echo $html->put_separator_demand("30");
		echo"
                        <div align=center class=\"foot\">
                                <center>
                                <div align=center class=\"foot2\">
                                        <div class=\"div_button_foot\" style=\"\">
                                                <button class=\"button_foot\" onclick=\"f_recargar()\">Recargar</button>
                                        </div>
                                        <div class=\"div_button_foot\"><center>
                                                <button class=\"button_foot\" onclick=\"return f_agregar_faltante()\">Agregar Bien Faltante</button>
                                        </div>
                                </div>
                        </div>
                ";
	//}
?>
<center>
    <script>document.form.codi_patr.focus();</script>
	</form>
	</body>
</html>
