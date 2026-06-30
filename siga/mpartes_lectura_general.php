<?
//classes/TCPDF/examples/personal_fotocheck.php
	require_once 'include/cabecera.php';
	require_once 'classes/Html.class.php';
	require_once 'classes/Db.class.php';
	$Db = new Db();
	if(!isset($_POST['busc_pagi_actu']))
		$_POST['busc_pagi_actu']=1;
		
		//echo"<HR>".$_SESSION['codi_depe']."<HR>";
	
	if(isset($_POST['codi_elim']) && $_POST['codi_elim'])
	{
	    $obse="Usuario: ".$_SESSION['logi_oper']."\nFecha: ".date("d/m/Y H:i:s")."\nObservación: ".$_POST['obse_elim'];
	    $result=$Db->query("update mp_mpar_carpetas set esta_mpar='0',obse_mpar='$obse' where codi_mpar='".$_POST['codi_elim']."' AND esta_mpar='1'");
	}
	
	if(isset($_POST['codi_mpar_agre']) && $_POST['codi_mpar_agre'])
	{
		$fdig=date("YmdHis");
		
		$ya_existe=0;
		$result=$Db->query("select * from mp_mpar_carpetas where mpar_cbar='".(isset($_POST['codi_mpar_agre']) ? $_POST['codi_mpar_agre'] : '')."' AND anno_mpar='".(isset($_POST['anno_mpar']) ? $_POST['anno_mpar'] : '')."' AND depe_mpar='".(isset($_SESSION['codi_depe']) ? $_SESSION['codi_depe'] : '')."' AND esta_mpar='1'");
		foreach($result as $rows)
		    $ya_existe++;
		
		if($ya_existe==0)
		{
		    $result=$Db->query("select max(nume_mpar) nume from mp_mpar_carpetas where anno_mpar='".(isset($_POST['anno_mpar']) ? $_POST['anno_mpar'] : '')."' AND depe_mpar='".(isset($_SESSION['codi_depe']) ? $_SESSION['codi_depe'] : '')."' AND esta_mpar='1'");
            foreach($result as $rows)
                $nume=$rows['nume'];
            $nume++;
		    
			$result=$Db->insert('mp_mpar_carpetas',['anno_mpar'=>(isset($_POST['anno_mpar']) ? $_POST['anno_mpar'] : ''),'nume_mpar'=>$nume,'tdoc_mpar'=>(isset($_POST['tdoc_mpar']) ? $_POST['tdoc_mpar'] : ''),'mpar_cbar'=>(isset($_POST['codi_mpar_agre']) ? $_POST['codi_mpar_agre'] : ''),'depe_mpar'=>(isset($_SESSION['codi_depe']) ? $_SESSION['codi_depe'] : ''),'esta_mpar'=>'1','digi_mpar'=>(isset($_SESSION['iden_oper']) ? $_SESSION['iden_oper'] : ''),'fdig_mpar'=>"$fdig"]);
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
					<input type=hidden name=\"busq_pagi_actu\" value=\"".(isset($_POST['busq_pagi_actu']) ? $_POST['busq_pagi_actu'] : '')."\">
                                </form>
                                <script>
                                        document.form.submit();
                                </script>
                        </body></html>
		";

	}
	
	
    $result=$Db->query("select * from mp_admi_depe where codi_depe='".(isset($_SESSION['codi_depe']) ? $_SESSION['codi_depe'] : '')."' ");
    foreach($result as $rows)
        $nomb_depe=$rows['abre_depe'];
    if(!isset($nomb_depe)) $nomb_depe = "";
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
	<center><h2 style="color:#073A6B">INGRESO DE MESA DE PARTES<BR><?=$nomb_depe?></h2></center>
		<form name="form" method="post">
			<input type=hidden name="codi_elim">
			<input type=hidden name="obse_elim">
			<input type=hidden name="busc_pagi_actu" value="<?=(isset($_POST['busc_pagi_actu']) ? $_POST['busc_pagi_actu'] : '')?>">
<?
	$html=new htmlclass;

	$arra_options_espe[0]="<- Todas ->";
        $result=$Db->select('mp_maes_jurisprudencia_especialidad', '', '', '', ['x_nombre'=>'ASC']);
        foreach($result as $rows)
                $arra_options_espe[$rows['n_codigo']]=$rows['x_nombre'];
    
    $result=$Db->select('mp_maes_mpar_tdoc', '', '', '', ['n_codigo'=>'ASC']);
    foreach($result as $rows)
            $arra_options_tdoc[$rows['n_codigo']]=$rows['x_nombre'];

$busc_tipo=1;	//1 nombre - 2 ndoc - 3 esca - 4 marc
$arra_options_tipo['2023']=2023;
$arra_options_tipo['2024']=2024;
$arra_options_tipo['2025']=2025;

	echo"<main>";
	echo $html->put_title_demand("FORMULARIO DE INGRESO");
	echo $html->put_select("A&nacute;o",'anno_mpar',$arra_options_tipo,date("Y"),"");
	echo $html->put_select("Tipo&nbsp;de&nbsp;Documento",'tdoc_mpar',$arra_options_tdoc,(isset($_POST['tdoc_mpar']) ? $_POST['tdoc_mpar'] : ''),"");
	echo $html->put_text('text','Documento',"Ingrese Nro. Documento",'codi_mpar_agre',(isset($_POST['codi_mpar_agre']) ? $_POST['codi_mpar_agre'] : ''),'','100','');
	//echo $html->put_text('text',"<a href=\"javascript:f_buscar()\">Click&nbsp;<u>AQUI</u>&nbsp;para&nbsp;Buscar</a>","Ingrese datos (Comod&iacute;n: %)",'busq_dato',$_POST['busq_dato'],'','100','');
	//echo"</main><main>";
	//echo $html->put_button_colum("&nbsp;","Ingresar Carpeta &raquo;","return check_buscar()");
	echo"</main>";

//if($_POST['text_busc'])
//{
	$busc_item_pagi=100;      //cantidad de items por pagina

	//$result=$Db->query("select * from mp_jurisprudencia_documento where nomb_docu like '%:m_busq%'",[':m_busq'=>$_POST['text_busc']]);
	$result=$Db->query("select * from mp_mpar_carpetas where esta_mpar=1 AND codi_depe=0 AND codi_pers=0 AND depe_mpar='".(isset($_SESSION['codi_depe']) ? $_SESSION['codi_depe'] : '')."' order by fdig_mpar desc");
	$busc_tota_item=0;
	foreach($result as $rows)
	{       
		$busc_tota_item++;
	}
	$busc_tota_pagi=ceil($busc_tota_item/$busc_item_pagi);
	$busc_limi_pagi=($_POST['busc_pagi_actu']-1)*$busc_item_pagi;

	$result_pagi=$Db->query("select * from mp_mpar_carpetas where esta_mpar=1 AND codi_depe=0 AND codi_pers=0 AND depe_mpar='".(isset($_SESSION['codi_depe']) ? $_SESSION['codi_depe'] : '')."' order by fdig_mpar desc limit $busc_limi_pagi,$busc_item_pagi");
	echo"<div style=\"width:90%;max-width:800px;margin:auto;\">";
	echo $html->put_title_demand("CARPETAS SIN ASIGNAR: $busc_tota_item DISPONIBLES");
	if($busc_tota_pagi>0)
		echo $html->put_page("1",$_POST['busc_pagi_actu'],$busc_tota_pagi,"");
	$head=['1'=>"Nº",'2'=>"CODIGO",'3'=>"DOCUMENTO",'4'=>"F.INGRESO",'5'=>"USUARIO",'6'=>"DESPACHO",'7'=>"FISCAL",'8'=>"&nbsp;"];
	echo $html->put_table_responsive_open();
	if($busc_tota_item)
	{
		echo $html->put_table_responsive_header($head);
		$cont=$busc_limi_pagi;
		foreach($result_pagi as $rows)
		{
			$cont++;
			$rows['fdig_mpar']=substr($rows['fdig_mpar'],6,2).'/'.substr($rows['fdig_mpar'],4,2).'/'.substr($rows['fdig_mpar'],0,4).' '.substr($rows['fdig_mpar'],8,2).':'.substr($rows['fdig_mpar'],10,2);
			$data=[	'1'=>$cont,
			    '2'=>$rows['anno_mpar'].'-'.str_pad($rows['nume_mpar'], 4, '0', STR_PAD_LEFT),
				'3'=>$arra_options_tdoc[$rows['tdoc_mpar']]."<BR>".$rows['mpar_cbar'],
				'4'=>$rows['fdig_mpar'],
				'5'=>(isset($_SESSION['logi_oper']) ? $_SESSION['logi_oper'] : ''),
				'6'=>'<font color=silver><i>Pendiente',
				'7'=>'<font color=silver><i>Pendiente',
				'8'=>"<a href=\"javascript:f_eliminar('".$rows['codi_mpar']."','".$rows['mpar_cbar']."')\"><img src=\"img/delete.png\" width=\"20\">",
			];
			    //'4'=>"<a href=\"javascript:f_ver('docu_".str_pad($rows['codi_docu'], 6, "0", STR_PAD_LEFT).".pdf')\"><img src=\"img/pdf_image.gif\" width=\"20\">",
			echo $html->put_table_responsive_data($head,$data);
		}
	}
	else
		echo $html->put_table_responsive_title("<font color=silver>No Existen Carpetas");
	echo $html->put_table_responsive_close();
	if($busc_tota_pagi>0)
		echo $html->put_page("2",$_POST['busc_pagi_actu'],$busc_tota_pagi,"");
	echo"</div>";
//}
	//if($busc_tota_item>0)
	//{
	
		echo $html->put_separator_demand("30");
		echo"
                        <div align=center class=\"foot\">
                                <center>
                                <div align=center class=\"foot2\">
                                        <div class=\"div_button_foot\" style=\"\">
                                                <button class=\"button_foot\" onclick=\"document.form.reset()\">Cancelar</button>
                                        </div>
                                        <div class=\"div_button_foot\"><center>
                                                <button class=\"button_foot\" onclick=\"check_buscar()\">Ingresar Carpeta</button>
                                        </div>
                                </div>
                        </div>
                ";
    
	//}
?>
<center>
    <script>document.form.codi_mpar_agre.focus();</script>
	</form>
	</body>
</html>
