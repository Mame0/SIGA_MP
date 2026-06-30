<?
	require_once 'include/cabecera.php';
	require_once 'classes/Html.class.php';
	require_once 'classes/Db.class.php';
	$Db = new Db();

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
	
	if($_POST['codi_patr'] AND $_POST['codi_tipo'])
	{
	    //$result=$Db->query("insert into mp_patr_inve_bien_temp values('','".$_POST['codi_patr']."','".$_POST['codi_barr']."','".$_POST['nume_seri']."','".$_POST['codi_tipo']."','".$_POST['codi_marc']."','".$_POST['codi_colo']."','".$_SESSION['iden_oper']."','$fdig','1')");
	    $result=$Db->query("insert into mp_patr_siga(codigo_patrimonial,descripcion,nro_serie,marca,codigo_barra,color) values('".$_POST['codi_patr']."','".$_POST['codi_tipo']."','".$_POST['nume_seri']."','".$_POST['codi_marc']."','".$_POST['codi_barr']."','COLOR: ".$_POST['codi_colo']."')");
	    $resulti=$Db->query("insert into mp_patr_inve_regi values('','".$_POST['codi_inve']."','','".$_POST['usua_inve']."','".$_POST['codi_patr']."','".$_POST['lati_inve']."','".$_POST['long_inve']."','".$_SESSION['iden_oper']."','$fdig','1')");
	    echo"
	            <form name=form_bien method=post action=\"patrimonio_inventario.php\">
	                <input type=hidden name=\"codi_inve\" value=\"".$_POST['codi_inve']."\">
        			<input type=hidden name=\"fech_inve\" value=\"".$_POST['fech_inve']."\">
        			<input type=hidden name=\"nomb_inve\" value=\"".$_POST['nomb_inve']."\">
        			<input type=hidden name=\"usua_inve\" value=\"".$_POST['usua_inve']."\">
        			<input type=hidden name=\"usua_dato\" value=\"".$_POST['usua_dato']."\">
	            </form>
	            <script>
	                alert('Bien inventariado correctamente')
	                document.form_bien.submit();
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
			function f_crear_bien()
			{
			    if(document.form.codi_patr.value=='')
			    {
			        alert('ERROR: Ingrese Cod. Patrimonial');
			        document.form.codi_patr.focus();
			        return false;
			    }
			    else
			    {
			        if(document.form.codi_tipo.value=='')
    			    {
	    		        alert('ERROR: Seleccione Tipo de Bien');
		    	        document.form.codi_tipo.focus();
		    	        return false;
			        }
			        else
			        {
		                if(confirm('Seguro que desea guardar nuevo bien?'))
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
			function f_cancelar()
			{
			    document.form.codi_patr.value='';
			    document.form.action='patrimonio_inventario.php';
				document.form.target="";
				document.form.submit();
			}
			function f_recargar()
			{
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
	    
	<center><h4 style="color:#073A6B"><b>TOMA DE INVENTARIO <BR>[<?=$_POST['fech_inve']?>] <?=$_POST['nomb_inve']?> <?=$_POST['usua_dato']?><BR>INGRESAR NUEVO BIEN</h4></b></center>
		<form name="form" method="post">
			<input type=hidden name="regi_inve">
			<input type=hidden name="lati_inve">
			<input type=hidden name="long_inve">
			<input type=hidden name="codi_inve" value="<?=$_POST['codi_inve']?>">
			<input type=hidden name="fech_inve" value="<?=$_POST['fech_inve']?>">
			<input type=hidden name="nomb_inve" value="<?=$_POST['nomb_inve']?>">
			<input type=hidden name="usua_inve" value="<?=$_POST['usua_inve']?>">
			<input type=hidden name="usua_dato" value="<?=$_POST['usua_dato']?>">
<?
	$html=new htmlclass;
	
	$arra_bien[-1]="<-- Seleccione Bien -->";
	$arra_bien[0]="<-- Seleccione Bien -->";
	$result=$Db->query("select distinct descripcion from mp_patr_siga order by descripcion");
	$i=0;
	foreach($result as $rows)
	{
	    $i++;
	    $arra_bien[$rows['descripcion']]=$rows['descripcion'];
	}
	
	$arra_colo[-1]="<-- Seleccione Color -->";
	$arra_colo[0]="<-- Seleccione Color -->";
	$result=$Db->query("select distinct color from mp_patr_siga order by color");
	foreach($result as $rows)
	    $arra_colo[$rows['color']]=$rows['color'];
	
	$arra_marc[-1]="<-- Seleccione Marca -->";
	$arra_marc[0]="<-- Seleccione Marca -->";
	$result=$Db->query("select distinct marca from mp_patr_siga order by marca");
	foreach($result as $rows)
	    $arra_marc[$rows['marca']]=$rows['marca']; 
	
            echo"<main>";
    	    echo $html->put_text('text',"COD.PATRIMONIAL&nbsp;(12&nbsp;dígitos)","Ingrese Código Patrimonial",'codi_patr',$_POST['codi_patr'],'','15','');
    	    echo $html->put_text('text',"COD&nbsp;DE&nbsp;BARRAS&nbsp;(7&nbsp;dígitos)","Ingrese Código Patrimonial",'codi_barr',$_POST['codi_barr'],'','15','');
    	    echo $html->put_text('text',"NUM.&nbsp;SERIE","Ingrese Número de Serie",'nume_seri',$_POST['nume_seri'],'','25','');
    	    echo"</main>";
    	    echo"<main>";
    	    echo $html->put_select_buscador("TIPO",'codi_tipo',$arra_bien,$_POST['codi_tipo'],"");
    	    echo $html->put_select_buscador("MARCA",'codi_marc',$arra_marc,$_POST['codi_marc'],"");
    	    echo $html->put_select_buscador("COLOR",'codi_colo',$arra_colo,$_POST['codi_colo'],"");
    	    echo"</main>";
	
	//if($busc_tota_item>0 AND 5==6)
	//{
		echo $html->put_separator_demand("30");
		echo"
                        <div align=center class=\"foot\">
                                <center>
                                <div align=center class=\"foot2\">
                                        <div class=\"div_button_foot\" style=\"\">
                                            <button class=\"button_foot\" onclick=\"f_cancelar()\">Cancelar</button>
                                        </div>
                                        <div class=\"div_button_foot\"><center>
                                            <button class=\"button_foot\" onclick=\"return f_crear_bien()\">Guardar Nuevo Bien</button>
                                        </div>
                                </div>
                        </div>
        ";
	//}
?>
<center>
    <script>
        navigator.geolocation.getCurrentPosition(function(position){
            let lat = position.coords.latitude;
            let long = position.coords.longitude;
            document.form.lati_inve.value=lat;
            document.form.long_inve.value=long;
        });
        document.form.usua_inve_busc.focus();
    </script>
	</form>
	</body>
</html>
