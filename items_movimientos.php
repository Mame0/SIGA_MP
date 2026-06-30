<?

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
		<title>Movimientos por pago de Items</title>
		<link rel="stylesheet" href="css/forms_demanda.css" />
		<link rel="stylesheet" href="css/forms_foot.css" />
		<link rel="stylesheet" href="css/forms_column.css" />
		<SCRIPT language="JavaScript" src="js/cadenas.js"></SCRIPT>
		<SCRIPT language="JavaScript" src="js/denuncia.js"></SCRIPT>
		<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
		<script>
			function f_editar(codi)
			{
				document.form.codi_movitem.value=codi;
				document.form.action='items_movimientos_registro.php';
				document.form.target="";
				document.form.submit();
			}
			function f_nuevo()
			{
				document.form.codi_movitem.value='';
				document.form.action='items_movimientos_registro.php';
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
	<center><h2 style="color:#073A6B">MOVIMIENTOS POR PAGOS DE ITEMS/SERVICIOS</h2></center>
		<form name="form" method="post">
			<input type=hidden name="codi_movitem">
			<input type=hidden name="busc_pagi_actu" value="<?=$_POST['busc_pagi_actu']?>">
<?
	$html=new htmlclass;

	$busc_item_pagi=40;      //cantidad de items por pagina
	$result=$Db->query("select * from mp_movs_item ");
	$busc_tota_item=0;
	foreach($result as $rows) {
		$busc_tota_item++;
	}
	$busc_tota_pagi=ceil($busc_tota_item/$busc_item_pagi);
	$busc_limi_pagi=($_POST['busc_pagi_actu']-1)*$busc_item_pagi;


	$arra_options_loca[0]="";
	$result=$Db->select('mp_admi_loca', '', '', '', ['codi_loca'=>'ASC']);
	foreach($result as $rows)
			$arra_options_loca[$rows['codi_loca']]=$rows['nom1_loca'];

	$result_pagi=$Db->query("select mp_movs_item.*, mp_maes_item.x_nombre
	from mp_movs_item left join mp_maes_item on mp_movs_item.codi_item=mp_maes_item.n_codigo
	order by codi_movitem desc limit $busc_limi_pagi,$busc_item_pagi");

	echo"<div style=\"width:90%;max-width:800px;margin:auto;\">";
	echo $html->put_title_demand("MOVIMIENTOS POR PAGO DE ITEMS CONTRATADOS: $busc_tota_item ENCONTRADOS");
	if($busc_tota_pagi>0)
		echo $html->put_page("1",$_POST['busc_pagi_actu'],$busc_tota_pagi,"");
	$head=['1'=>"#",'2'=>"ITEM CONTRATADO",'3'=>"NRO CONTRATO",'4'=>"LOCAL",'5'=>"CICLO FACTURACION",'6'=>"NRO RECIBO",'7'=>"FEC.VCTO",'8'=>"FEC.PAGO",'9'=>"MONTO",'10'=>"EDITAR"];
	echo $html->put_table_responsive_open();
	if($busc_tota_item)
	{
		echo $html->put_table_responsive_header($head);
		$cont=$busc_limi_pagi;
		foreach($result_pagi as $rows) {
			$elmes="";
			$cont++;
			$data=[	'1'=>$cont,
				'2'=> $rows['x_nombre'],
				'3'=> $rows['nro_contr'],
				'4'=> $arra_options_loca[$rows['codi_loca']],
				'5'=> $rows['cicl_fact'],
				'6'=> $rows['nro_reci'],
				'7'=> $rows['fech_vcto'],
				'8'=> $rows['fech_pago'],
				'9'=> $rows['mont_pago'],
				'10'=>"<a href=\"javascript:f_editar('$rows[codi_movitem]')\"><img src=\"img/icons/edit.svg\" width=\"20\">",
			];
			echo $html->put_table_responsive_data($head,$data);
		}
	}
	else
		echo $html->put_table_responsive_title("No existen movimientos");
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
                                        <div class=\"div_button_foot\"><center>
                                                <button class=\"button_foot\" onclick=\"f_nuevo()\">Agregar Nuevo</button>
                                        </div>
                                </div>
                        </div>
                ";

	//}
?>
<div id='cargadorvacio'></div>

<center>
	</form>
	</body>
</html>
