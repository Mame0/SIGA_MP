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
			function f_ver(codi)
			{
				document.form.action='ftp/'+codi;
				document.form.target="blank";
				document.form.submit();
			}
			function f_accion_tabla()
			{
				document.form.codi_pers.value='';
				document.form.action='jurisprudencia_registro.php';
				document.form.target="";
				document.form.submit();
			}
			function f_editar(codi)
			{
				document.form.codi_docu.value=codi;
				document.form.action='jurisprudencia_registro.php';
				document.form.target="";
				document.form.submit();
			}
			function f_nuevo()
			{
				document.form.codi_docu.value='';
				document.form.action='jurisprudencia_registro.php';
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
	<center><h2 style="color:#073A6B">CONFIGURAR PROCESO ELECTORAL</h2></center>
		<form name="form" method="post">
			<input type=hidden name="codi_docu">
			<input type=hidden name="busc_pagi_actu" value="<?=$_POST['busc_pagi_actu']?>">
<?
	$html=new htmlclass;

	$arra_options_anno[0]="<- Seleccione ->";
	$arra_options_anno[2020]="2020";
	$arra_options_anno[2021]="2021";
	$arra_options_anno[2022]="2022";
	$arra_options_anno[2023]="2023";
	if(!$_POST['anno_elec'])
	    $_POST['anno_elec']=date(Y);
	
	echo"<main>";
	echo $html->put_title_demand("SELECCIONE AÑO");
	echo $html->put_select("",'anno_elec',$arra_options_anno,$_POST['anno_elec']," ONCHANGE=\"document.form.submit()\"");
	echo"</main>";

	$busc_item_pagi=40;      //cantidad de items por pagina

	//$result=$Db->query("select * from mp_jurisprudencia_documento where nomb_docu like '%:m_busq%'",[':m_busq'=>$_POST['text_busc']]);
	
	$result=$Db->query("select codi_elec,nomb_elec,anno_elec,fech_elec,habi_elec,digi_elec,fdig_elec,esta_elec from mp_elec_config WHERE anno_elec=".$_POST['anno_elec']."");
	$busc_tota_item=0;
	foreach($result as $rows)
	{       
		$busc_tota_item++;
	}
	$busc_tota_pagi=ceil($busc_tota_item/$busc_item_pagi);
	$busc_limi_pagi=($_POST['busc_pagi_actu']-1)*$busc_item_pagi;

	$result_pagi=$Db->query("select codi_elec,nomb_elec,anno_elec,fech_elec,habi_elec,digi_elec,fdig_elec,esta_elec from mp_elec_config WHERE anno_elec=".$_POST['anno_elec']." order by fech_elec limit $busc_limi_pagi,$busc_item_pagi");
	echo"<div style=\"width:90%;max-width:800px;margin:auto;\">";
	echo $html->put_title_demand("RESULTADOS DE B&Uacute;SQUEDA: $busc_tota_item ENCONTRADOS");
	if($busc_tota_pagi>0)
		echo $html->put_page("1",$_POST['busc_pagi_actu'],$busc_tota_pagi,"");
	$head=['1'=>"Nº",'2'=>"NOMBRE",'3'=>"AÑO",'4'=>"FECHA",'5'=>"HABILITADO",'6'=>"EDITAR"];
	echo $html->put_table_responsive_open();
	if($busc_tota_item)
	{
		echo $html->put_table_responsive_header($head);
		$cont=$busc_limi_pagi;
		foreach($result_pagi as $rows)
		{
			$cont++;
			$data=[	'1'=>$cont,
				'2'=>$rows['nomb_elec'],
				'3'=>$rows['anno_elec'],
				'4'=>substr($rows['fech_elec'],6,2)."-".substr($rows['fech_elec'],4,2)."-".substr($rows['fech_elec'],0,4),
				'5'=>$rows['habi_elec'],
			];
			echo $html->put_table_responsive_data($head,$data);
		}
	}
	else
		echo $html->put_table_responsive_title("No Existen Procesos Electorales ".$_POST['anno_elec']);
	echo $html->put_table_responsive_close();
	if($busc_tota_pagi>0)
		echo $html->put_page("2",$_POST['busc_pagi_actu'],$busc_tota_pagi,"");
	echo"</div>";
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
                                                <button class=\"button_foot\" onclick=\"f_nuevo()\">Agregar Nuevo</button>
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
