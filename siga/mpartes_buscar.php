<?
//classes/TCPDF/examples/personal_fotocheck.php
	require_once 'include/cabecera.php';
	require_once 'classes/Html.class.php';
	require_once 'classes/Db.class.php';
	$Db = new Db();
	if(!isset($_POST['busc_pagi_actu']))
		$_POST['busc_pagi_actu']=1;
		
		//echo"<HR>".$_SESSION['codi_depe']."<HR>";
	
	if($_POST['codi_mpar_agreEEEEE'])
	{
		$fdig=date(YmdHis);
		
		$ya_existe=0;
		$result=$Db->query("select * from mp_mpar_carpetas where mpar_cbar='".$_POST['codi_mpar_agre']."' AND esta_mpar='1'");
		foreach($result as $rows)
		    $ya_existe++;
		
		if($ya_existe==0)
		{
			$result=$Db->insert('mp_mpar_carpetas',['mpar_cbar'=>$_POST['codi_mpar_agre'],'depe_mpar'=>$_SESSION['codi_depe'],'esta_mpar'=>'1','digi_mpar'=>$_SESSION['iden_oper'],'fdig_mpar'=>"$fdig"]);
			$_POST['codi_mpar']=$Db->lastInsertId();
		}
		else
		{
		    echo"<script>alert('ERROR: Carpeta ya fue ingresada');</script>";
		}
		unset($_POST['codi_mpar'],$_POST['codi_mpar_agre']);

		echo"
		                <html><body>
                                <form name=\"form\" method=post action=\"mpartes_lectura_general.php\">
					<input type=hidden name=\"busq_pagi_actu\" value=\"".$_POST['busq_pagi_actu']."\">
                                </form>
                                <script>
                                        document.form.submit();
                                </script>
                        </body></html>
		";

	}
	
	
    $result=$Db->query("select * from mp_admi_depe where codi_depe='".$_SESSION['codi_depe']."' ");
    foreach($result as $rows)
        $nomb_depe=$rows['abre_depe'];
        //echo"<HR>".$_SESSION['codi_depe']."- $nomb_depe<HR>";
?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8" />
		<title>SIOJAlimentos</title>
		<link rel="stylesheet" href="css/forms_demanda.css" />
		<link rel="stylesheet" href="css/forms_foot.css" />
		<link rel="stylesheet" href="css/forms_column.css" />
		<SCRIPT language="JavaScript" src="js/cadenas.js"></SCRIPT>
		<SCRIPT language="JavaScript" src="js/denuncia.js"></SCRIPT>
		<script>
			function check_buscar()
			{
				document.form.action='';
				document.form.target="";
				document.form.submit();
			}
			function f_eliminar(codi,nume)
			{
			    if(confirm('Seguro que desea eliminar carpeta '+nume+'?'))
			    {
    			    obse=prompt('Ingrese Motivo');
    			    if(obse)
    			    {
        				document.form.codi_elim.value=codi;
        				document.form.obse_elim.value=obse;
	        			document.form.action='';
		        		document.form.target="";
			        	document.form.submit();
    			    }
    			    else
    			        alert('ERROR: Ingrese Observacion');
			    }
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
	<center><h2 style="color:#073A6B">BUSCAR CARPETAS<BR><?=$nomb_depe?></h2></center>
		<form name="form" method="post">
			<input type=hidden name="codi_elim">
			<input type=hidden name="obse_elim">
			<input type=hidden name="busc_pagi_actu" value="<?=$_POST['busc_pagi_actu']?>">
<?
	$html=new htmlclass;

	$result=$Db->query("select * from mp_maes_personal");
	foreach($result as $rows)
        $arra_pers[$rows['iden_pers']]=$rows['nomb_pers'].' '.$rows['appa_pers'].' '.$rows['apma_pers'];

    $result=$Db->query("select * from mp_admi_depe");
	foreach($result as $rows)
        $arra_depe[$rows['codi_depe']]=$rows['nomb_depe'];
    
    $result=$Db->query("select * from mp_admi_oper");
	foreach($result as $rows)
        $arra_oper[$rows['iden_oper']]=$rows['nomb_oper'].' '.$rows['appa_oper'].' '.$rows['apma_oper'];
    
    $arra_options_tipo[2023]=2023;
    $arra_options_tipo[2024]=2024;
    $arra_options_tipo[2025]=2025;
    
    if(!$_POST['anno_mpar'])
        $_POST['anno_mpar']=date(Y);

	echo"<main>";
	echo $html->put_title_demand("FORMULARIO DE BUSQUEDA");
	//echo $html->put_select("Especialidad",'codi_espe',$arra_options_espe,$_POST['codi_espe'],"");
	echo $html->put_select("A&nacute;o",'anno_mpar',$arra_options_tipo,$_POST['anno_mpar'],"");
	echo $html->put_text('text','Código',"Ingrese Carpeta",'codi_mpar_busc',$_POST['codi_mpar_busc'],'','50','');
	//echo $html->put_text('text',"<a href=\"javascript:f_buscar()\">Click&nbsp;<u>AQUI</u>&nbsp;para&nbsp;Buscar</a>","Ingrese datos (Comod&iacute;n: %)",'busq_dato',$_POST['busq_dato'],'','100','');
	echo $html->put_button_colum("&nbsp;","Buscar Carpeta &raquo;","return check_buscar()");
	echo"</main>";

if($_POST['codi_mpar_busc'])
{
	//$result=$Db->query("select * from mp_mpar_carpetas where esta_mpar=1 AND codi_mpar='".$_POST['codi_mpar_busc']."' AND depe_mpar='".$_SESSION['codi_depe']."'");
	$result=$Db->query("select * from mp_mpar_carpetas where esta_mpar=1 AND mpar_cbar='".$_POST['codi_mpar_busc']."' AND anno_mpar='".$_POST['anno_mpar']."' AND depe_mpar='".$_SESSION['codi_depe']."'");
	foreach($result as $rows)
	{       
	    $rows['fdig_mpar']=substr($rows['fdig_mpar'],6,2).'/'.substr($rows['fdig_mpar'],4,2).'/'.substr($rows['fdig_mpar'],0,4).' '.substr($rows['fdig_mpar'],8,2).':'.substr($rows['fdig_mpar'],10,2);
	    
	    if($rows['codi_depe'])
	        $rows['fech_asig']=substr($rows['fech_asig'],6,2).'/'.substr($rows['fech_asig'],4,2).'/'.substr($rows['fech_asig'],0,4).' '.substr($rows['fech_asig'],8,2).':'.substr($rows['fech_asig'],10,2);
	//echo"<HR>select * from mp_mpar_carpetas where esta_mpar=1 AND codi_mpar='".$_POST['codi_mpar_busc']."'<HR>";
	    echo"<main style='column-count:1;'>";
	    echo $html->put_title_demand("CARPETA BUSCADA: ".$_POST['codi_mpar_busc']);
	    echo $html->put_text('text','Caso&nbsp;Nro.',"",'codi_mpar',dar_formato_carpeta($_POST['codi_mpar_busc'],$rows['anno_mpar']),'','50','readonly style="max-width:240px"');
	    echo $html->put_title_demand("Datos de Ingreso");
	    echo $html->put_text('text','Mesa&nbsp;de&nbsp;Partes',"",'depe_mpar',$arra_depe[$rows['depe_mpar']],'','30','readonly style="max-width:800px"');
	    echo $html->put_text('text','Usuario&nbsp;Responsable',"",'digi_mpar',$arra_oper[$rows['digi_mpar']],'','30','readonly style="max-width:800px"');
	    echo $html->put_text('text','Fecha&nbsp;de&nbsp;Ingreso.',"",'fdig_mpar',$rows['fdig_mpar'],'','30','readonly style="max-width:240px"');
	    echo $html->put_title_demand("Datos de Asignaci&oacute;n");
	    echo $html->put_text('text','Despacho&nbsp;Fiscal',"",'codi_depe',$arra_depe[$rows['codi_depe']],'','30','readonly style="max-width:800px"');
	    echo $html->put_text('text','Fiscal&nbsp;Asignado',"",'codi_pers',$arra_pers[$rows['codi_pers']],'','30','readonly style="max-width:800px"');
	    echo $html->put_text('text','Fecha&nbsp;de&nbsp;Asignaci&oacute;n',"",'fdig_mpar',$rows['fech_asig'],'','30','readonly style="max-width:240px"');
	    //echo $html->put_text('text','Fecha&nbsp;de&nbsp;Ingreso.',"",'fdig_mpar','05/05/05','','30','readonly');
	    echo"</main>";
	}
}
	//if($busc_tota_item>0)
	//{
	/*
		echo $html->put_separator_demand("30");
		echo"
                        <div align=center class=\"foot\">
                                <center>
                                <div align=center class=\"foot2\">
                                        <div class=\"div_button_foot\" style=\"\">
                                                <button class=\"button_foot\" onclick=\"document.form.reset()\">Anular Selección</button>
                                        </div>
                                        <div class=\"div_button_foot\"><center>
                                                <button class=\"button_foot\" onclick=\"f_nuevo()\">Asignar Aleatoriamente</button>
                                        </div>
                                </div>
                        </div>
                ";
    */
	//}
?>
<center>
    <script>document.form.codi_mpar_agre.focus();</script>
	</form>
	</body>
</html>
