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
	<center><h2 style="color:#073A6B">REPORTE DE INGRESO JURISPRUDENCIA</h2></center>
		<form name="form" method="post">
			<input type=hidden name="codi_docu">
			<input type=hidden name="busc_pagi_actu" value="<?=$_POST['busc_pagi_actu']?>">
<?
	$html=new htmlclass;

	$arra_options_espe[0]="<- Todas ->";
        $result=$Db->select('mp_maes_jurisprudencia_especialidad', '', '', '', ['x_nombre'=>'ASC']);
        foreach($result as $rows)
                $arra_options_espe[$rows['n_codigo']]=$rows['x_nombre'];
                
        $arra_options_oper[0]="Todos";
        $result=$Db->select('mp_admi_oper', '', '', '', '');
        foreach($result as $rows)
                $arra_options_oper[$rows['iden_oper']]=$rows['nomb_oper']." ".$rows['appa_oper']." ".$rows['apma_oper'];

$busc_tipo=1;	//1 nombre - 2 ndoc - 3 esca - 4 marc
$arra_options_tipo=array(1=>"Por Apellidos y Nombres","DNI","Escalafon","C&oacute;digo de Marcado");

	echo"<main>";
	echo $html->put_title_demand("FORMULARIO DE BUSQUEDA");
	echo $html->put_select("Especialidad",'codi_espe',$arra_options_espe,$_POST['codi_espe']," ONCHANGE=\"document.form.submit()\"");
	echo"</main>";

	$busc_item_pagi=40;      //cantidad de items por pagina

	//$result=$Db->query("select * from mp_jurisprudencia_documento where nomb_docu like '%:m_busq%'",[':m_busq'=>$_POST['text_busc']]);
	
	$AND_ESPE="";
	if($_POST['codi_espe']>0)
	    $AND_ESPE=" AND codi_espe='".$_POST['codi_espe']."' ";
	
	$result=$Db->query("select codi_espe,digi_docu,SUBSTRING(fdig_docu,1,6) fdig,count(*) from mp_jurisprudencia_documento WHERE 5=5 $AND_ESPE group by codi_espe,digi_docu,fdig;");
	$busc_tota_item=0;
	foreach($result as $rows)
	{       
		$busc_tota_item++;
	}
	$busc_tota_pagi=ceil($busc_tota_item/$busc_item_pagi);
	$busc_limi_pagi=($_POST['busc_pagi_actu']-1)*$busc_item_pagi;

	$result_pagi=$Db->query("select codi_espe,digi_docu,SUBSTRING(fdig_docu,1,6) fdig,count(*) cant from mp_jurisprudencia_documento WHERE 5=5 $AND_ESPE group by codi_espe,digi_docu,fdig limit $busc_limi_pagi,$busc_item_pagi");
	echo"<div style=\"width:90%;max-width:800px;margin:auto;\">";
	echo $html->put_title_demand("RESULTADOS DE B&Uacute;SQUEDA: $busc_tota_item ENCONTRADOS");
	if($busc_tota_pagi>0)
		echo $html->put_page("1",$_POST['busc_pagi_actu'],$busc_tota_pagi,"");
	$head=['1'=>"Nº",'2'=>"ESPECIALIDAD",'3'=>"USUARIO",'4'=>"FECHA",'5'=>"CANTIDAD"];
	echo $html->put_table_responsive_open();
	if($busc_tota_item)
	{
		echo $html->put_table_responsive_header($head);
		$cont=$busc_limi_pagi;
		foreach($result_pagi as $rows)
		{
			$cont++;
			$data=[	'1'=>$cont,
				'2'=>$arra_options_espe[$rows['codi_espe']],
				'3'=>$arra_options_oper[$rows['digi_docu']],
				'4'=>substr($rows['fdig'],0,4)."-".substr($rows['fdig'],4,2),
				'5'=>$rows['cant'],
			];
			echo $html->put_table_responsive_data($head,$data);
		}
	}
	else
		echo $html->put_table_responsive_title("No Existe Jurisprudencia");
	echo $html->put_table_responsive_close();
	if($busc_tota_pagi>0)
		echo $html->put_page("2",$_POST['busc_pagi_actu'],$busc_tota_pagi,"");
	echo"</div>";
	//if($busc_tota_item>0)
	//{
	/*
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
	*/
?>
<center>
	</form>
	</body>
</html>
