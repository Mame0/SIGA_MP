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
				document.form.action='compras_proveedores_registro.php';
				document.form.target="";
				document.form.submit();
			}
			function f_editar(codi)
			{
				document.form.codi_prov.value=codi;
				document.form.action='compras_proveedores_registro.php';
				document.form.target="";
				document.form.submit();
			}
			function f_nuevo()
			{
				document.form.codi_prov.value='';
				document.form.action='compras_proveedores_registro.php';
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
	<center><h2 style="color:#073A6B">PROVEEDORES REGISTRADOS</h2></center>
		<form name="form" method="post">
			<input type=hidden name="codi_prov">
			<input type=hidden name="busc_pagi_actu" value="<?=$_POST['busc_pagi_actu']?>">
<?
	$html=new htmlclass;

	$arra_options_rubro[0]="<- Todas ->";
        $result=$Db->select('mp_maes_comp_rubro', '', '', '', ['x_nombre'=>'ASC']);
        foreach($result as $rows)
                $arra_options_rubro[$rows['n_codigo']]=$rows['x_nombre'];

$arra_options_estado=array(0=>"Inactivo",1=>"Activo");

	echo"<main>";
	echo $html->put_title_demand("FORMULARIO DE BUSQUEDA");
	echo $html->put_select("Rubro",'codi_rubr',$arra_options_rubro,$_POST['codi_rubr'],"");
	//echo $html->put_select("Criterios",'busq_tipo',$arra_options_tipo,$_POST['busq_tipo'],"");
	echo $html->put_text('text','Nombre',"Ingrese Nombre",'text_busc',$_POST['text_busc'],'','50','');
	//echo $html->put_text('text',"<a href=\"javascript:f_buscar()\">Click&nbsp;<u>AQUI</u>&nbsp;para&nbsp;Buscar</a>","Ingrese datos (Comod&iacute;n: %)",'busq_dato',$_POST['busq_dato'],'','100','');
	echo $html->put_button_colum("&nbsp;","Buscar Proveedor &raquo;","return check_buscar()");
	echo"</main>";

if($_POST['text_busc'])
{
	$busc_item_pagi=40;      //cantidad de items por pagina

	//$result=$Db->query("select * from mp_comp_proveedores where nomb_prov like '%:m_busq%'",[':m_busq'=>$_POST['text_busc']]);
	$result=$Db->query("select * from mp_comp_proveedores where nomb_prov like '%$_POST[text_busc]%'");
	$busc_tota_item=0;
	foreach($result as $rows)
	{
		$busc_tota_item++;
	}
	$busc_tota_pagi=ceil($busc_tota_item/$busc_item_pagi);
	$busc_limi_pagi=($_POST['busc_pagi_actu']-1)*$busc_item_pagi;

	$result_pagi=$Db->query("select * from mp_comp_proveedores where nomb_prov like '%$_POST[text_busc]%' order by nomb_prov asc limit $busc_limi_pagi,$busc_item_pagi");
	echo"<div style=\"width:90%;max-width:800px;margin:auto;\">";
	echo $html->put_title_demand("RESULTADOS DE B&Uacute;SQUEDA: $busc_tota_item ENCONTRADOS");
	if($busc_tota_pagi>0)
		echo $html->put_page("1",$_POST['busc_pagi_actu'],$busc_tota_pagi,"");
	$head=['1'=>"Nº",'2'=>"RUC",'3'=>"NOMBRE",'4'=>"RUBRO",'5'=>"ESTADO",'6'=>" "];
	echo $html->put_table_responsive_open();
	if($busc_tota_item)
	{
		echo $html->put_table_responsive_header($head);
		$cont=$busc_limi_pagi;
		foreach($result_pagi as $rows)
		{
			$cont++;
			$data=[	'1'=>$cont,
				'2'=>$rows['nruc_prov'],
				'3'=>$rows['nomb_prov'],
				'4'=>$arra_options_rubro[$rows['codi_rubr']],
				'5'=>$arra_options_estado[$rows['esta_prov']],
				'6'=>"<a href=\"javascript:f_editar('$rows[codi_prov]')\"><img src=\"img/icons/edit.svg\" width=\"20\">",
			];
			echo $html->put_table_responsive_data($head,$data);
		}
	}
	else
		echo $html->put_table_responsive_title("No Existe Proveedores");
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
