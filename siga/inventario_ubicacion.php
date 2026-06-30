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
	
	if(isset($_POST['dire_orig']) && $_POST['dire_orig']=='inventario_bienes_dependencia.php' AND $_POST['loca_inve_busc'] AND $_POST['codi_depe'])
	{
	    echo"
                    <html><body>
                    <form name=\"form\" method=post action=\"inventario_bienes_dependencia.php\">
                        <input type=hidden name=\"dire_orig\" value=\"inventario_bienes_dependencia.php\">
                    </form>
                    <script>
                        document.form.submit();
                    </script>
                    </body></html>
        ";
        exit;
	}
	    
	if(isset($_POST['loca_inve_busc']))
	{
	    $_POST['loca_inve']=$_POST['loca_inve_busc'];
	    
	    $result=$Db->query("select * from mp_admi_loca where codi_loca='".$_POST['loca_inve']."'");
	    $flag_usua=0;
	    foreach($result as $rows)
	    {
	        $flag_usua++;
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
	        $_POST['nomb_pers']="[".$rows['ndoc_pers']."] ".$rows['appa_pers']." ".$rows['apma_pers'].", ".$rows['nomb_pers'];
	        $_POST['pers_dato']="<BR><u>USUARIO</u>: ".$_POST['nomb_pers'];
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
		    function f_cambiar_local()
		    {
		        document.form.codi_depe.selectedIndex=0;
		        //document.form.codi_pers.selectedIndex=0;
		        document.form.submit();
		    }
		    function f_cambiar_dependencia()
		    {
		        //document.form.codi_pers.selectedIndex=0;
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
	    
	<center><h4 style="color:#073A6B"><b>SELECCIONAR UBICACION<BR>[<?=(isset($_POST['fech_inve']) ? $_POST['fech_inve'] : '')?>] <?=(isset($_POST['nomb_inve']) ? $_POST['nomb_inve'] : '')?> <?=(isset($_POST['loca_dato']) ? $_POST['loca_dato'] : '')?> <?=(isset($_POST['depe_dato']) ? $_POST['depe_dato'] : '')?> <?=(isset($_POST['pers_dato']) ? $_POST['pers_dato'] : '')?></h4></b></center>
		<form name="form" method="post">
			<input type=hidden name="regi_inve">
			<input type=hidden name="lati_inve">
			<input type=hidden name="long_inve">
			<input type=hidden name="elim_inve">
			<input type=hidden name="obse_inve">
			<input type=hidden name="codi_regi">
			<input type=hidden name="busc_pagi_actu" value="<?=(isset($_POST['busc_pagi_actu']) ? $_POST['busc_pagi_actu'] : '')?>">
			<input type=hidden name="codi_inve" value="<?=$_POST['codi_inve']?>">
			<input type=hidden name="fech_inve" value="<?=$_POST['fech_inve']?>">
			<input type=hidden name="nomb_inve" value="<?=$_POST['nomb_inve']?>">
			<input type=hidden name="loca_inve" value="<?=$_POST['loca_inve']?>">
			<input type=hidden name="loca_dato" value="<?=(isset($_POST['loca_dato']) ? $_POST['loca_dato'] : '')?>">
			<input type=hidden name="loca_lati" value="<?=(isset($_POST['loca_lati']) ? $_POST['loca_lati'] : '')?>">
			<input type=hidden name="loca_long" value="<?=(isset($_POST['loca_long']) ? $_POST['loca_long'] : '')?>">
			<input type=hidden name="ulti_lect" value="<?=(isset($_POST['codi_patr']) ? $_POST['codi_patr'] : '')?>">
			<input type=hidden name="orde_inve" value="<?=(isset($_POST['orde_inve']) ? $_POST['orde_inve'] : '')?>">
			
			<input type=hidden name="dire_orig" value="<?=(isset($_POST['dire_orig']) ? $_POST['dire_orig'] : '')?>">
<?
	$html=new htmlclass;
	
	    //OBTENER LOCAL
        $arra_options_loca[]="<- Seleccione Local ->";
        $result=$Db->query("select * from mp_admi_loca where esta_loca='1' order by nom1_loca");
    	foreach($result as $rows)
    	    $arra_options_loca[$rows['codi_loca']]=$rows['nom1_loca']." [".$rows['dire_loca']."]";
    	//FIN OBTENER LOCAL
	
    	//OBTENER DEPENDENCIA
        $arra_options_depe[]="<- Seleccione Dependencia ->";
        //$result=$Db->query("select * from mp_admi_depe where esta_depe='1' order by nomb_depe");
        //$result=$Db->query("select * from mp_admi_depe where esta_depe='1' AND codi_loca='".$_POST['loca_inve_busc']."' order by nomb_depe");
        $result=$Db->query("select distinct a.codi_depe as codi_depe,nomb_depe from mp_admi_depe as a, mp_admi_pers as b where a.codi_depe=b.iden_depe AND esta_depe='1' AND codi_loca='".$_POST['loca_inve_busc']."' order by nomb_depe");
    	foreach($result as $rows)
    	    $arra_options_depe[$rows['codi_depe']]=$rows['nomb_depe'];
    	//FIN OBTENER DEPENDENCIA
    	
    	/*
    	//OBTENER PERSONAL
        $arra_options_pers[]="<- Seleccione Usuario ->";
        //$result=$Db->query("select * from mp_admi_pers where esta_pers='1' order by appa_pers,apma_pers,nomb_pers");
        $result=$Db->query("select * from mp_admi_pers where esta_pers='1' AND iden_depe='".$_POST['codi_depe']."' order by appa_pers,apma_pers,nomb_pers");
    	foreach($result as $rows)
    	    $arra_options_pers[$rows['iden_pers']]=" [".$rows['ndoc_pers']."] ".$rows['appa_pers']." ".$rows['apma_pers'].", ".$rows['nomb_pers'];
    	//FIN OBTENER PERSONAL
        */

        echo"<main>";
        echo $html->put_select("Local",'loca_inve_busc',$arra_options_loca,$_POST['loca_inve_busc']," onchange=\"f_cambiar_local()\"");
        echo $html->put_select(CONST_SUBTITLE_DEPENDENCIA,'codi_depe',$arra_options_depe,$_POST['codi_depe'],"onchange=\"f_cambiar_dependencia()\"");
        //if($_POST['dire_orig']!='inventario_bienes_dependencia.php')
        //    echo $html->put_select_buscador('Usuario','codi_pers',$arra_options_pers,$_POST['codi_pers'],"onchange=\"document.form.submit()\"");
        echo"</main>";
?>
<center>
	</form>
	</body>
</html>
