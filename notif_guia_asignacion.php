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
			function f_pdf(codi)
			{
			    document.form.iden_guia.value=codi;
				document.form.action='classes/TCPDF/examples/notif_guia_asignacion.php';
				document.form.target="blank";
				document.form.submit();
			}
			function f_ver(codi)
			{
				document.form.action=codi;
				document.form.target="blank";
				document.form.submit();
			}
			function f_nuevo()
			{
				document.form.codi_docu.value='';
				document.form.action='notif_guia_asignacion_nuevo.php';
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
	<center><h3 style="color:#073A6B"><B>GU&Iacute;AS DE ASIGNACI&Oacute;N A MENSAJEROS</B></h3></center>
		<form name="form" method="post">
			<input type=hidden name="codi_docu">
			<input type=hidden name="iden_guia">
			<input type=hidden name="busc_pagi_actu" value="<?=$_POST['busc_pagi_actu']?>">
<?
	$html=new htmlclass;

    /*
	$arra_options_tema[0]="<- Todas ->";
        $result=$Db->select('mp_maes_capacitacion_tema', '', '', '', ['x_nombre'=>'ASC']);
        foreach($result as $rows)
                $arra_options_tema[$rows['n_codigo']]=$rows['x_nombre'];
    */

	echo"<main>";
	echo $html->put_text('text',"&Uacute;ltima&nbsp;Gu&iacute;a:&nbsp;2979&nbsp;2024","Ingrese Nro. Gu&iacute;a",'text_busc_nume',$_POST['text_busc_nume'],'','50','');
	//echo $html->put_text('text',"<a href=\"javascript:f_buscar()\">Click&nbsp;<u>AQUI</u>&nbsp;para&nbsp;Buscar</a>","Ingrese datos (Comod&iacute;n: %)",'busq_dato',$_POST['busq_dato'],'','100','');
	echo $html->put_button_colum("&nbsp;",'Buscar Gu&iacute;a'." &raquo;","return check_buscar()");
	echo"</main>";

if($_POST['text_busc'] OR 5==5)
{
	$busc_item_pagi=50;      //cantidad de items por pagina
	$fech_busq=date("d-m-Y");

	//$result=$Db->query("select * from mp_jurisprudencia_documento where nomb_docu like '%:m_busq%'",[':m_busq'=>$_POST['text_busc']]);
	$result=$Db->query("select * from mp_notif_guia_cabecera where esta_guia=1 AND nume_guia like '%$_POST[text_busc_nume]%'");
	//print_r($result);
	//$prueba=$Db->rowCount();
	$prueba=count($result);
	//echo"<HR>$prueba<HR>";
	$busc_tota_item=0;
	foreach($result as $rows)
	{       
		$busc_tota_item++;
	}
	$busc_tota_pagi=ceil($busc_tota_item/$busc_item_pagi);
	$busc_limi_pagi=($_POST['busc_pagi_actu']-1)*$busc_item_pagi;
	
	$result=$Db->query("select * from mp_maes_personal where codi_carg in (5) order by appa_pers,apma_pers,nomb_pers");
    foreach($result as $rows)
            $arra_options_mens[$rows['iden_pers']]=$rows['appa_pers'].' '.$rows['apma_pers'].', '.$rows['nomb_pers'];

	$result_pagi=$Db->query("select * from mp_notif_guia_cabecera where esta_guia=1 AND nume_guia like '%$_POST[text_busc_nume]%' order by fdig_guia desc limit $busc_limi_pagi,$busc_item_pagi");
	echo"<div style=\"width:90%;max-width:800px;margin:auto;\">";
	echo $html->put_title_demand("FECHA: $fech_busq: $busc_tota_item ENCONTRADOS");
	if($busc_tota_pagi>0)
		echo $html->put_page("1",$_POST['busc_pagi_actu'],$busc_tota_pagi,"");
	$head=['1'=>"Nº",'2'=>"A&ntilde;O",'3'=>"NUMERO DE GUIA",'4'=>"FECHA ASIGNACION",'5'=>"MENSAJERO",'6'=>"",'7'=>"",'8'=>""];
	echo $html->put_table_responsive_open();
	if($busc_tota_item)
	{
		echo $html->put_table_responsive_header($head);
		$cont=$busc_limi_pagi;
		foreach($result_pagi as $rows)
		{
			$cont++;
			$data=[	'1'=>$cont,
				'2'=>$rows['anno_guia'],
				'3'=>$rows['nume_guia'],
				'4'=>$rows['fgen_guia'],
				'5'=>$arra_options_mens[$rows['iden_mens']],
				'6'=>"<a href=\"javascript:f_pdf('".$rows['iden_guia']."')\" alt=\"Ver Video en Youtube\"><img src=\"img/icons/printer.svg\" width=\"20\">",
				'7'=>"<a href=\"javascript:f_ver('".$rows['dire_docu']."')\" alt=\"Ver Video en Youtube\"><img src=\"img/icons/edit.svg\" width=\"20\">",
				'8'=>"<a href=\"javascript:f_ver('".$rows['dire_docu']."')\" alt=\"Ver Video en Youtube\"><img src=\"img/icons/trash.svg\" width=\"20\">",
			];
			    //'3'=>$rows['sumi_docu'],
			echo $html->put_table_responsive_data($head,$data);
		}
	}
	else
		echo $html->put_table_responsive_title("No Existen Capacitaciones");
	echo $html->put_table_responsive_close();
	if($busc_tota_pagi>0)
		echo $html->put_page("2",$_POST['busc_pagi_actu'],$busc_tota_pagi,"");
	echo"</div>";
}
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
                                                <button class=\"button_foot\" onclick=\"f_nuevo()\">Nueva Gu&iacute;a</button>
                                        </div>
                                </div>
                        </div>
                ";
	//}
?>
<center>
	</form>
	</body>
</html>
