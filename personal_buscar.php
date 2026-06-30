<?
//classes/TCPDF/examples/personal_fotocheck.php
	require_once 'include/cabecera.php';
	require_once 'classes/Html.class.php';
	require_once 'classes/Db.class.php';
	$Db = new Db();
	
	unset($_SESSION['iden_pers_edit']);
	
	if(!isset($_POST['busc_pagi_actu']))
		$_POST['busc_pagi_actu']=1;
	if(!isset($_POST['codi_form']))
	    $_POST['codi_form']=$_GET['codi_form'] ?? null;
	if(!isset($_POST['codi_form']))
	    $_POST['codi_form']=1;
	
	$_POST['flag_admi']=1;
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
			function f_editar_personal(codi)
			{
				document.form.iden_pers_edit.value=codi;
				document.form.action='<?=$_POST['dire_orig']?>';
				document.form.target="";
				document.form.submit();
			}
			function PadLeft(value, length)
			{
				return (value.toString().length < length) ? PadLeft("0" + value, length) : 
				value;
			}
			function f_cancelar_documento()
			{
			    document.form.busq_dato.value='';
				document.form.action='personal_buscar.php';
				document.form.submit();
			}
			function f_nuevo_personal()
			{
			    document.form.crea_pers.value='1';
			    document.form.action='personal_general.php';
				document.form.submit();
			}
			function ajustar_altura()
                        {
                                parent.document.getElementById('body_iframe').height=parent.window.innerHeight-80;
                        }
                        ajustar_altura();
		</script>
	</head>
	<body style="margin-bottom: 30px;">
	<center><B><h3 style="color:#073A6B"><B>INFORMACIÓN PERSONAL</B></h3></B></center>
		<form name="form" method="post">
			<input type=hidden name="iden_pers_edit">
			<input type=hidden name="todo_chek">
			<input type=hidden name="crea_pers">
			<input type=hidden name="flag_admi" value="<?=$_POST['flag_admi']?>">
			<input type=hidden name="dire_orig" value="<?=$_POST['dire_orig']?>">
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
	echo $html->put_title_demand("FORMULARIO DE BÚSQUEDA");
	//echo $html->put_select("Dependencia",'codi_depe',$arra_options_depe,$_POST['codi_depe'],"");
	echo $html->put_select("Tipo",'busq_tipo',$arra_options_tipo,$_POST['busq_tipo'] ?? null,"");
	echo $html->put_text('text',"Ingrese&nbsp;datos&nbsp;(Comod&iacute;n:&nbsp;%)","Ingrese datos (Comod&iacute;n: %)",'busq_dato',$_POST['busq_dato'] ?? null,'','100','');
	echo $html->put_button_colum("&nbsp;","Buscar Personal &raquo;","return f_buscar()");
	echo"</main>";
	//echo"<main>";
	//echo $html->put_select("Formato",'codi_form',$arra_options_form,$_POST['codi_form'],"");
	echo"</main>";
if(isset($_POST['busq_tipo']) AND isset($_POST['busq_dato']))
{
	$busc_item_pagi=100;      //cantidad de items por pagina

	switch($_POST['busq_tipo'])
	{
		case 1:	$parametro="CONCAT(nomb_pers,' ',appa_pers,' ',apma_pers) like '%".$_POST['busq_dato']."%'"; break;
		case 2:	$parametro="ndoc_pers='".$_POST['busq_dato']."'"; break;
	}

	$result=$Db->query("select * from mp_admi_pers where $parametro");
	$busc_tota_item=0;
	foreach($result as $rows)
	{       
		$busc_tota_item++;
	}
	$busc_tota_pagi=ceil($busc_tota_item/$busc_item_pagi);
	$busc_limi_pagi=($_POST['busc_pagi_actu']-1)*$busc_item_pagi;

	$result_pagi=$Db->query("select * from mp_admi_pers where $parametro order by appa_pers,apma_pers,nomb_pers asc limit $busc_limi_pagi,$busc_item_pagi");
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
				'4'=>$arra_options_carg[$rows['iden_carg']],
				'5'=>$arra_options_depe[$rows['iden_depe']],
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
                echo"
                        <div align=center class=\"foot\">
                                <center>
                                <div align=center class=\"foot2\">
                                        <div class=\"div_button_foot\" style=\"\">
                                                <button class=\"button_foot\" onclick=\"f_cancelar_documento()\">&laquo; Cancelar</button>
                                        </div>
                                        <div class=\"div_button_foot\"><center>
                                                <button class=\"button_foot\" onclick=\"return f_nuevo_personal()\">Nuevo Personal &raquo;</button>
                                        </div>
                                </div>
                        </div>
                ";
?>
<center>
	</form>
	</body>
</html>
