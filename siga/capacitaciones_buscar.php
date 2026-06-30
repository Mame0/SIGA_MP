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
				document.form.action=codi;
				document.form.target="blank";
				document.form.submit();
			}
			function f_accion_tabla()
			{
				document.form.codi_pers.value='';
				document.form.action='capacitaciones_registro.php';
				document.form.target="";
				document.form.submit();
			}
			function f_editar(codi)
			{
				document.form.codi_docu.value=codi;
				document.form.action='capacitaciones_registro.php';
				document.form.target="";
				document.form.submit();
			}
			function f_nuevo()
			{
				document.form.codi_docu.value='';
				document.form.action='capacitaciones_registro.php';
				document.form.target="";
				document.form.submit();
			}
			function PadLeft(value, length)
			{
				return (value.toString().length < length) ? PadLeft("0" + value, length) : 
				value;
			}
			
			function f_ver_youtube(vide)
			{
                // Reemplaza esto con el código de inserción real de tu video de YouTube
                var codigoEmbed = '<iframe width="560" height="315" src="https://www.youtube.com/embed/'+vide+'" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>';
                var ventana = window.open("", "YouTube Popup", "width=600,height=400");
                ventana.document.write('<html><head><title>Reproductor de YouTube</title></head><body>' + codigoEmbed + '</body></html>');
                ventana.document.close();
            }
			
			function ajustar_altura()
                        {
                                parent.document.getElementById('body_iframe').height=parent.window.innerHeight-80;
                        }
                        ajustar_altura();
		</script>
	</head>
	<body style="margin-bottom: 30px;">
	<center><h2 style="color:#073A6B">REPOSITORIO DE CAPACITACIONES</h2></center>
		<form name="form" method="post">
			<input type=hidden name="codi_docu">
			<input type=hidden name="busc_pagi_actu" value="<?=$_POST['busc_pagi_actu']?>">
<?
	$html=new htmlclass;

	$arra_options_tema[0]="<- Todas ->";
        $result=$Db->select('mp_maes_capacitacion_tema', '', '', '', ['x_nombre'=>'ASC']);
        foreach($result as $rows)
                $arra_options_tema[$rows['n_codigo']]=$rows['x_nombre'];

$busc_tipo=1;	//1 nombre - 2 ndoc - 3 esca - 4 marc
$arra_options_tipo=array(1=>"Por Apellidos y Nombres","DNI","Escalafon","C&oacute;digo de Marcado");

    $result=$Db->query("select * from mp_admi_oper_role where iden_oper='".$_SESSION['iden_oper']."' AND (iden_role='21' OR iden_role='2')");
    $admi_capa=$result[0]['iden_oper'];

	echo"<main>";
	echo $html->put_title_demand("FORMULARIO DE BUSQUEDA");
	echo $html->put_select("Tema",'codi_tema',$arra_options_tema,$_POST['codi_tema'],"");
	//echo $html->put_select("Criterios",'busq_tipo',$arra_options_tipo,$_POST['busq_tipo'],"");
	echo $html->put_text('text',"Nombre","Ingrese Texto a Buscar",'text_busc',$_POST['text_busc'],'','50','');
	//echo $html->put_text('text',"<a href=\"javascript:f_buscar()\">Click&nbsp;<u>AQUI</u>&nbsp;para&nbsp;Buscar</a>","Ingrese datos (Comod&iacute;n: %)",'busq_dato',$_POST['busq_dato'],'','100','');
	echo $html->put_button_colum("&nbsp;",'Buscar Capacitaciones'." &raquo;","return check_buscar()");
	echo"</main>";

if($_POST['text_busc'] OR 5==5)
{
	$busc_item_pagi=40;      //cantidad de items por pagina

	//$result=$Db->query("select * from mp_jurisprudencia_documento where nomb_docu like '%:m_busq%'",[':m_busq'=>$_POST['text_busc']]);
	$result=$Db->query("select * from mp_capacitacion_documento where nomb_docu like '%$_POST[text_busc]%'");
	$busc_tota_item=0;
	foreach($result as $rows)
	{       
		$busc_tota_item++;
	}
	$busc_tota_pagi=ceil($busc_tota_item/$busc_item_pagi);
	$busc_limi_pagi=($_POST['busc_pagi_actu']-1)*$busc_item_pagi;

	$result_pagi=$Db->query("select * from mp_capacitacion_documento where nomb_docu like '%$_POST[text_busc]%' order by fech_docu desc limit $busc_limi_pagi,$busc_item_pagi");
	echo"<div style=\"width:90%;max-width:800px;margin:auto;\">";
	echo $html->put_title_demand("RESULTADOS DE B&Uacute;SQUEDA: $busc_tota_item ENCONTRADOS");
	if($busc_tota_pagi>0)
		echo $html->put_page("1",$_POST['busc_pagi_actu'],$busc_tota_pagi,"");
	//$head=['1'=>"Nº",'2'=>"NOMBRE",'3'=>"SUMILLA",'4'=>"FECHA",'5'=>"",'6'=>"",'7'=>""];
	if($admi_capa)
	    $head=['1'=>"Nº",'2'=>"NOMBRE",'4'=>"FECHA",'5'=>"",'6'=>"",'7'=>""];
	else
	    $head=['1'=>"Nº",'2'=>"NOMBRE",'4'=>"FECHA",'5'=>"",'6'=>""];
	echo $html->put_table_responsive_open();
	if($busc_tota_item)
	{
		echo $html->put_table_responsive_header($head);
		$cont=$busc_limi_pagi;
		foreach($result_pagi as $rows)
		{
			$cont++;
			$data=[	'1'=>$cont,
				'2'=>$rows['nomb_docu'],
				'4'=>$rows['fech_docu'],
				'5'=>"<a href=\"javascript:f_ver_youtube('".$rows['dire_docu']."')\" alt=\"Ver Video en Youtube\"><img src=\"img/icons/youtube.svg\" width=\"20\">",
				'6'=>"<a href=\"javascript:f_ver('".$rows['driv_docu']."')\"><img src=\"img/icons/cloud.svg\" width=\"20\">",
				'7'=>"<a href=\"javascript:f_editar('$rows[codi_docu]')\"><img src=\"img/icons/edit.svg\" width=\"20\">",
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
	if($admi_capa)
	{
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
	}
?>
<center>
	</form>
	</body>
</html>
