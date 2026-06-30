<?
//classes/TCPDF/examples/personal_fotocheck.php
	require_once 'include/cabecera.php';
	require_once 'classes/Html.class.php';
	require_once 'classes/Db.class.php';
	$Db = new Db();
	if(!isset($_POST['busc_pagi_actu']))
		$_POST['busc_pagi_actu']=1;
	if(!isset($_POST['codi_form']) || !$_POST['codi_form'])
	    $_POST['codi_form']=(isset($_GET['codi_form']) ? $_GET['codi_form'] : '');
	if(!$_POST['codi_form'])
	    $_POST['codi_form']=1;
	switch($_POST['codi_form'])
	{
	    case 1: $nomb_form="Administrativo";  break;
	    case 2: $nomb_form="Fiscal";  break;
	    case 3: $nomb_form="Secigra";  break;
	}
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
			function f_generar_fotocheck(tipo)
			{
				document.form.action='classes/TCPDF/examples/personal_fotocheck.php';
				document.form.todo_chek.value=tipo;
				document.form.target="blank";
				document.form.submit();
			}
			function f_accion_tabla()
			{
				document.form.iden_pers.value='';
				document.form.action='potencial_mantenimiento_registro.php';
				document.form.target="";
				document.form.submit();
			}
			function f_editar_personal(codi)
			{
				document.form.iden_pers.value=codi;
				document.form.action='potencial_mantenimiento_registro.php';
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
	<center><h2 style="color:#073A6B">LISTADO DE PERSONAL DEL DF AREQUIPA</h2></center>
		<form name="form" method="post">
			<input type=hidden name="iden_pers">
			<input type=hidden name="todo_chek">
			<input type=hidden name="busc_pagi_actu" value="<?=$_POST['busc_pagi_actu']?>">
			<input type=hidden name="codi_form" value="<?=$_POST['codi_form']?>">
<?
	$html=new htmlclass;

    $result=$Db->select('mp_maes_cargo', '', '', '', ['x_nombre'=>'ASC']);
    foreach($result as $rows)
        $arra_options_carg[$rows['n_codigo']]=utf8_encode(utf8_decode($rows['x_nombre']));
    
    $result=$Db->query("select * from mp_admi_depe");
    foreach($result as $rows)
        $arra_options_depe[$rows['codi_depe']]=utf8_encode(utf8_decode($rows['nomb_depe']));

$busc_tipo=1;	//1 nombre - 2 ndoc - 3 esca - 4 marc
$arra_options_tipo=array(1=>"Por Apellidos y Nombres","DNI");

	echo"<main>";
	echo $html->put_title_demand("BUSQUEDA DE PERSONAL");
	//echo $html->put_select("Dependencia",'codi_depe',$arra_options_depe,$_POST['codi_depe'],"");
	echo $html->put_select("Tipo",'busq_tipo',$arra_options_tipo,(isset($_POST['busq_tipo']) ? $_POST['busq_tipo'] : ''),"");
	echo $html->put_text('text',"Ingrese&nbsp;datos&nbsp;(Comod&iacute;n:&nbsp;%)","Ingrese datos (Comod&iacute;n: %)",'busq_dato',(isset($_POST['busq_dato']) ? $_POST['busq_dato'] : ''),'','100','');
	echo $html->put_button_colum("&nbsp;","Buscar Personal &raquo;","return f_buscar()");
	echo"</main>";
	//echo"<main>";
	//echo $html->put_select("Formato",'codi_form',$arra_options_form,$_POST['codi_form'],"");
	echo"</main>";
if(isset($_POST['busq_tipo']) && $_POST['busq_tipo'])
{
	$busc_item_pagi=100;      //cantidad de items por pagina

	switch($_POST['busq_tipo'])
	{
		case 1:	$parametro="CONCAT(nomb_pers,' ',appa_pers,' ',apma_pers) like '%".$_POST['busq_dato']."%'"; break;
		case 2:	$parametro="ndoc_pers='".$_POST['busq_dato']."'"; break;
	}

	$result=$Db->query("select * from mp_maes_personal where $parametro");
	$busc_tota_item=0;
	foreach($result as $rows)
	{       
		$busc_tota_item++;
	}
	$busc_tota_pagi=ceil($busc_tota_item/$busc_item_pagi);
	$busc_limi_pagi=($_POST['busc_pagi_actu']-1)*$busc_item_pagi;

	$result_pagi=$Db->query("select * from mp_maes_personal where $parametro order by appa_pers,apma_pers,nomb_pers asc limit $busc_limi_pagi,$busc_item_pagi");
	echo"<div style=\"width:90%;max-width:800px;margin:auto;\">";
	echo $html->put_title_demand("RESULTADOS DE B&Uacute;SQUEDA: $busc_tota_item ENCONTRADOS");

	if($busc_tota_pagi>0)
		echo $html->put_page("1",$_POST['busc_pagi_actu'],$busc_tota_pagi,"Nuevo Personal");
	$head=['1'=>"Nº",'2'=>"DNI",'3'=>"NOMBRES",'4'=>"CARGO",'5'=>"DEPENDENCIA",'6'=>"EDIT"];
	echo $html->put_table_responsive_open();
	if($busc_tota_item)
	{
		echo $html->put_table_responsive_header($head);
		$cont=$busc_limi_pagi;
		foreach($result_pagi as $rows)
		{
			$cont++;
			$data=[	'1'=>$cont,
				'2'=>$rows['ndoc_pers'],
				'3'=>utf8_encode(utf8_decode(strtoupper($rows['appa_pers']." ".$rows['apma_pers'].",<br>".$rows['nomb_pers']))),
				'4'=>$arra_options_carg[$rows['codi_carg']],
				'5'=>$arra_options_depe[$rows['codi_depe']],
				'6'=>"<a href=\"javascript:f_editar_personal('$rows[iden_pers]')\"><img src=\"img/icons/edit.svg\" width=\"20\">",
			];
			echo $html->put_table_responsive_data($head,$data);
		}
	}
	else
		echo $html->put_table_responsive_title("No Existe Personal");
		
	echo"	
	
	


	";
	
	echo $html->put_table_responsive_close();
	if($busc_tota_pagi>0)
		echo $html->put_page("2",$_POST['busc_pagi_actu'],$busc_tota_pagi,"Nuevo Personal");
	echo"</div>";
	
	
}
?>
<center>
	</form>
	</body>
</html>
