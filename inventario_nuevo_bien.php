<?
//classes/TCPDF/examples/personal_fotocheck.php
	require_once 'include/cabecera.php';
	require_once 'classes/Html.class.php';
	require_once 'classes/Db.class.php';
	$Db = new Db();

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
	    
	<center><h4 style="color:#073A6B"><b>AGREGAR NUEVO BIEN<BR>[<?=(isset($_POST['fech_inve'])?$_POST['fech_inve']:'')?>] <?=(isset($_POST['nomb_inve'])?$_POST['nomb_inve']:'')?> <?=(isset($_POST['loca_dato'])?$_POST['loca_dato']:'')?> <?=(isset($_POST['depe_dato'])?$_POST['depe_dato']:'')?> <?=(isset($_POST['pers_dato'])?$_POST['pers_dato']:'')?></h4></b></center>
		<form name="form" method="post">
			<input type=hidden name="regi_inve">
			<input type=hidden name="lati_inve">
			<input type=hidden name="long_inve">
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
	
    	//OBTENER DESCRIPCION
        $arra_options_desc[]="<- Seleccione Descripción ->";
        $result=$Db->query("select distinct descripcion from mp_inve_siga order by descripcion");
    	foreach($result as $rows)
    	    $arra_options_desc[]=$rows['descripcion'];
    	//FIN OBTENER DESCRIPCION
    	
    	//OBTENER MARCA
        $arra_options_marc[]="<- Seleccione Marcaa ->";
        $result=$Db->query("select distinct marca from mp_inve_siga order by marca");
    	foreach($result as $rows)
    	    $arra_options_marc[]=$rows['marca'];
    	//FIN OBTENER MARCA
    	
    	//OBTENER COLOR
        $arra_options_colo[]="<- Seleccione Color ->";
        $result=$Db->query("select distinct color from mp_inve_siga order by color");
    	foreach($result as $rows)
    	    $arra_options_colo[]=$rows['color'];
    	//FIN OBTENER COLOR
    	
    	//OBTENER ESTADO
        $arra_options_esta[]="<- Seleccione Estado ->";
        $result=$Db->query("select distinct nombre from mp_inve_siga order by nombre");
    	foreach($result as $rows)
    	    $arra_options_esta[]=$rows['nombre'];
    	//FIN OBTENER ESTADO
	
        echo"<main>";
        echo $html->put_select_buscador("Descripción",'desc_bien',$arra_options_desc,(isset($_POST['desc_bien'])?$_POST['desc_bien']:''),"");
        echo $html->put_select_buscador("Marca",'marc_bien',$arra_options_marc,(isset($_POST['codi_marc'])?$_POST['codi_marc']:''),"");
        echo $html->put_text('text',"MODELO","",'mode_bien',(isset($rows['mode_bien'])?$rows['mode_bien']:''),'','15','');
        echo"</main><main>";
        echo $html->put_text('text',"NRO.SERIE","",'seri_bien',(isset($rows['seri_bien'])?$rows['seri_bien']:''),'','15','');
        echo $html->put_select_buscador('Color','colo_bien',$arra_options_colo,(isset($_POST['colo_bien'])?$_POST['colo_bien']:''),"");
        echo $html->put_select('Estado','esta_bien',$arra_options_esta,(isset($_POST['esta_bien'])?$_POST['esta_bien']:''),"");
        echo"</main>";
?>
<center>
	</form>
	</body>
</html>
