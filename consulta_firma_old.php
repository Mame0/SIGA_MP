<?
//classes/TCPDF/examples/personal_fotocheck.php
	require_once 'include/cabecera.php';
	require_once 'classes/Html.class.php';
	require_once 'classes/Db.class.php';
	$Db = new Db();
	if(!isset($_POST['busc_pagi_actu']))
		$_POST['busc_pagi_actu']=1;
	function formato_expediente($expe)
	{
	    $expe=substr($expe,4).'-'.substr($expe,0,4);
	    return $expe;
	}
	function get_client_ip()
	{
        $ipaddress = '';
        if (getenv('HTTP_CLIENT_IP'))
            $ipaddress = getenv('HTTP_CLIENT_IP');
        else if(getenv('HTTP_X_FORWARDED_FOR'))
            $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
        else if(getenv('HTTP_X_FORWARDED'))
            $ipaddress = getenv('HTTP_X_FORWARDED');
        else if(getenv('HTTP_FORWARDED_FOR'))
            $ipaddress = getenv('HTTP_FORWARDED_FOR');
        else if(getenv('HTTP_FORWARDED'))
           $ipaddress = getenv('HTTP_FORWARDED');
        else if(getenv('REMOTE_ADDR'))
            $ipaddress = getenv('REMOTE_ADDR');
        else
            $ipaddress = 'UNKNOWN';
        return $ipaddress;
	}
	$dire_auop=get_client_ip();
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
			function f_buscar()
			{
				document.form.action='';
				document.form.target="";
				document.form.submit();
			}
			function f_buscar_expediente()
			{
			    if(document.form.busq_anno.selectedIndex==0)
			    {
			        alert('ERROR: Seleccione Año');
			        document.form.busq_anno.focus();
			        return false;
			    }
			    else
			    {
    			    if(document.form.busq_expe.value=='')
	    		    {
		    	        alert('ERROR: Ingrese Expediente');
			            document.form.busq_expe.focus();
			            return false;
			        }
    			    else
	    		    {
	    		        document.form.expe_firm.value='';
		    		    document.form.action='';
			    	    document.form.target="";
				        document.form.submit();
			        }
			    }
			}
			function f_expediente(expe)
			{
			    document.form.expe_firm.value=expe;
			    document.form.submit();
			}
			function f_reiniciar()
			{
			    document.form.arch_audi.value='';
			    document.form.expe_audi.value='';
			    document.form.busq_anno.selectedIndex='0';
			    document.form.busq_expe.value='';
				document.form.action='';
				document.form.submit();
			}
			function f_informacion(obse,depe)
			{
				alert('INSTANCIA: '+depe+'\nOBSERVACION: '+obse);
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
	<center><h4 style="color:#073A6B"><B>CONSULTA DE FIRMAS</B></h4></center>
		<form name="form" method="post">
			<input type=hidden name="codi_pers">
			<input type=hidden name="expe_firm" value="<?=$_POST['expe_firm']?>">
			<input type=hidden name="busc_pagi_actu" value="<?=$_POST['busc_pagi_actu']?>">
			<input type=hidden name="codi_form" value="<?=$_POST['codi_form']?>">

<?
	$html=new htmlclass;

	$arra_options_anno[0]="<- Seleccione Año ->";
	$result=$Db->query("select distinct SUBSTRING(tx_formato,7,4) as anno from mp_cons_firm order by anno");
	foreach($result as $rows)
		$arra_options_anno[$rows['anno']]=$rows['anno'];
	
	echo"<main>";
	echo $html->put_title_demand("Formulario de B&uacute;squeda");
	echo $html->put_select("A&ntilde;o",'busq_anno',$arra_options_anno,$_POST['busq_anno'],"");
	echo $html->put_text('text',"Expediente","Ingrese Expediente",'busq_expe',$_POST['busq_expe'],'','15','');
	echo $html->put_button_colum("&nbsp;","Buscar Expediente &raquo;","return f_buscar_expediente()");
    echo"</main>";
    
if($_POST['busq_expe'] AND !$_POST['expe_firm'])
{
    $result_pagi=$Db->query("select distinct tx_formato,x_nom_instancia from mp_cons_firm where tx_formato like '%".$_POST['busq_expe']."%-".$_POST['busq_anno']."-%' order by tx_formato");
	echo"<div style=\"width:90%;max-width:800px;margin:auto;\">";
	echo $html->put_title_demand("Expedientes Encontrados");

	$head=['1'=>"Nº",'2'=>"EXPEDIENTE",'3'=>"INSTANCIA",'4'=>""];
	echo $html->put_table_responsive_open();
	
	echo $html->put_table_responsive_header($head);
	$cont=0;
	foreach($result_pagi as $rows)
	{
		$cont++;
		$data=[	'1'=>$cont,
		    	'2'=>$rows['tx_formato'],
		    	'3'=>$rows['x_nom_instancia'],
		    	'4'=>"<a href=\"javascript:f_expediente('$rows[tx_formato]')\"><img src=\"img/icons/info.svg\" width=\"20\">",
		];
		echo $html->put_table_responsive_data($head,$data);
	}

	echo $html->put_table_responsive_close();
}
if($_POST['expe_firm'])
{
    echo"<div style=\"width:90%;max-width:800px;margin:auto;\">";
    echo $html->put_title_demand("Expediente: ".$_POST['expe_firm']);
    
	$head=['1'=>"Nº",'2'=>"DNI",'3'=>"NOMBRES",'4'=>"F.PROGRAMADA",'5'=>"F.FIRMA",'6'=>""];
	echo $html->put_table_responsive_open();
	echo $html->put_table_responsive_header($head);
    $result_pagi=$Db->query("select * from mp_cons_firm where tx_formato='".$_POST['expe_firm']."' order by x_ape_paterno,x_ape_materno,x_nombres,f_programada");
    $cont=0;
    $flag_audi='';
    $flag_fech='';
    foreach($result_pagi as $rows)
    {
        $cont++;
        
		$data=[	'1'=>$cont,
		        '2'=>$rows['tx_doc_id'],
				'3'=>$rows['x_ape_paterno'].' '.$rows['x_ape_materno'].', '.$rows['x_nombres'],
				'4'=>substr($rows['f_programada'],0,10),
				'5'=>substr($rows['f_firma'],0,10),
		    		'6'=>"<a href=\"javascript:f_informacion('$rows[x_observacion]','$rows[x_nom_instancia]')\"><img src=\"img/icons/info.svg\" width=\"20\">",
		];
		echo $html->put_table_responsive_data($head,$data);
    }
    echo"<input type=hidden name=\"cade_vist\" value=\"$cade_vist\">";
    echo $html->put_table_responsive_close();
    echo"</div>";
    
	if($busc_tota_item>0 OR 3==3)
	{
		echo $html->put_separator_demand("30");
		echo"
                        <div align=center class=\"foot\">
                                <center>
                                <div align=center class=\"foot2\">
                                        <div class=\"div_button_foot\" style=\"\">
                                                <button class=\"button_foot\" onclick=\"f_reiniciar()\">Reiniciar Búsqueda</button>
                                        </div>
                                        <div class=\"div_button_foot\"><center>
                                                <button class=\"button_foot\" onclick=\"return f_descargar_seleccion()\">Descargar Seleccionados</button>
                                        </div>
                                </div>
                        </div>
                ";
	}
}
?>
<center>
	</form>
	</body>
</html>
