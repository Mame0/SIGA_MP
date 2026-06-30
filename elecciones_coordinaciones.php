<?
//classes/TCPDF/examples/personal_fotocheck.php
	require_once 'include/cabecera.php';
	require_once 'classes/Html.class.php';
	require_once 'classes/Db.class.php';
	$Db = new Db();
	if(!isset($_POST['busc_pagi_actu']))
		$_POST['busc_pagi_actu']=1;
		
	$result=$Db->query("select codi_elec,nomb_elec from mp_elec_config WHERE habi_elec='1'");
	foreach($result as $rows)
	{
	    $_POST['nomb_elec']=$rows['nomb_elec'];
	    $_POST['codi_elec']=$rows['codi_elec'];
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
				document.form.codi_coor.value=codi;
				document.form.regresar_reporte.value='1';
				document.form.action='elecciones_coordinaciones_nuevo.php';
				document.form.target="";
				document.form.submit();
			}
			function f_acta(acta)
			{
				document.form.action=acta;
				document.form.target="blank";
				document.form.submit();
			}
			function f_nuevo()
			{
				document.form.codi_coor.value='';
				document.form.regresar_reporte.value='1';
				document.form.action='elecciones_coordinaciones_nuevo.php';
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
		
		<style type="text/css">
        .Table
        {
            display: table;
        }
        .Title
        {
            display: table-caption;
            text-align: center;
            font-weight: bold;
            font-size: larger;
        }
        .Heading
        {
            display: table-row;
            font-weight: bold;
            text-align: center;
        }
        .Row
        {
            display: table-row;
        }
        .Cell
        {
            display: table-cell;
            border: none;
            border-width: thin;
            padding-left: 1px;
            padding-right: 1px;
            vertical-align: middle;
        }
    </style>
	</head>
	<body style="margin-bottom: 30px;">
	<center><font style="color:#073A6B;font-weight: bold;"><?=$_POST['nomb_elec']?><BR><font style="font-size: 20;">COORDINACIONES INTERINSTITUCIONALES</font><br>USUARIO: <?=$_SESSION['logi_oper']?></font></center>
		<form name="form" method="post">
			<input type=hidden name="codi_coor">
			<input type=hidden name="regresar_reporte">
			<input type=hidden name="busc_pagi_actu" value="<?=$_POST['busc_pagi_actu']?>">
<?
	$html=new htmlclass;

	$busc_item_pagi=40;      //cantidad de items por pagina

	//$result=$Db->query("select * from mp_jurisprudencia_documento where nomb_docu like '%:m_busq%'",[':m_busq'=>$_POST['text_busc']]);
	
	$result=$Db->query("select codi_coor,codi_inst,fech_coor,ubig_coor from mp_elec_coordinaciones WHERE codi_elec='".$_POST['codi_elec']."' AND codi_usua='".$_SESSION['logi_oper']."'");
	$busc_tota_item=0;
	foreach($result as $rows)
	{       
		$busc_tota_item++;
	}
	
	$busc_tota_pagi=ceil($busc_tota_item/$busc_item_pagi);
	$busc_limi_pagi=($_POST['busc_pagi_actu']-1)*$busc_item_pagi;
	
	$result=$Db->query("select distinct cdep,cpro,cdis,prov,dist from ubig_reni WHERE cdep='04' AND cpro<>'00' AND cdis<>'00' order by cpro,cdis");
	foreach($result as $rows)
	{
	    $c=$rows['cdep'].$rows['cpro'].$rows['cdis'];
		$arra_options_prov[$c]=$rows['prov']." - ".$rows['dist'];
	}
		
	$result=$Db->select('mp_maes_elecciones_instituciones', '', '', '', ['n_codigo'=>'ASC']);
	foreach($result as $rows)
		$arra_options_inst[$rows['n_codigo']]=$rows['x_nombre'];

	$result_pagi=$Db->query("select codi_coor,codi_inst,fech_coor,ubig_coor from mp_elec_coordinaciones WHERE codi_elec='".$_POST['codi_elec']."' AND codi_usua='".$_SESSION['logi_oper']."' order by fdig_coor limit $busc_limi_pagi,$busc_item_pagi");
	echo"<div style=\"width:90%;max-width:800px;margin:auto;\">";
	echo $html->put_title_demand("RESULTADOS DE B&Uacute;SQUEDA: $busc_tota_item ENCONTRADOS");
	if($busc_tota_pagi>0)
		echo $html->put_page("1",$_POST['busc_pagi_actu'],$busc_tota_pagi,"");
	$head=['1'=>"NRO.",'2'=>"INSTITUCION",'3'=>"FECHA",'4'=>"PROVINCIA",'5'=>"EDITAR",'6'=>"ACTA"];
	echo $html->put_table_responsive_open();
	if($busc_tota_item)
	{
		echo $html->put_table_responsive_header($head);
		$cont=$busc_limi_pagi;
		foreach($result_pagi as $rows)
		{
			$cont++; 
			
			$rows['fech_coor']=substr($rows['fech_coor'],6,2).'/'.substr($rows['fech_coor'],4,2).'/'.substr($rows['fech_coor'],0,4);
			
			$acta="actas/coor_".str_pad($rows['codi_coor'], 6, "0", STR_PAD_LEFT).".pdf";
			$imag="img/pdf_image.gif";
			if(!file_exists($acta))
			{
			    $acta="1";
			    $imag="img/pdf_black.png";
			}
			
			$data=[	'1'=>$cont,
				'2'=>$arra_options_inst[$rows['codi_inst']],
				'3'=>$rows['fech_coor'],
				'4'=>$arra_options_prov[$rows['ubig_coor']],
				'5'=>"<a href=\"javascript:f_editar('$rows[codi_coor]')\"><img src=\"img/icons/edit.svg\" width=\"20\">",
				'6'=>"<a href=\"javascript:f_acta('$acta')\"><img src=\"$imag\" width=\"20\">",
			];
			//'6'=>"<table media='only screen and (max-width: 768px)' class='nueva' border=0 cellpadding=0 cellspacing=0 style=\"padding:0;spacing:0\"><tr><td cellpadding=0 cellspacing=0 style=\"padding:0;spacing:0\">1</td><td style=\"padding:0;spacing:0\"><a href=\"javascript:f_editar('$rows[codi_docu]')\"><img src=\"img/icons/man_color.svg\" height=\"20\"></a></td><td style=\"padding:0;spacing:0\">&nbsp;&nbsp;</td><td style=\"padding:0;spacing:0\"><a href=\"javascript:f_editar('$rows[codi_docu]')\"><img src=\"img/icons/woman_color.svg\" height=\"20\"></a></td><td style=\"padding:0;spacing:0\">0</td></tr></table>",
			echo $html->put_table_responsive_data($head,$data);
			
		}
	}
	else
		echo $html->put_table_responsive_title("No Existen Coordinaciones ".$_POST['anno_elec']);
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
                                                <button class=\"button_foot\" onclick=\"document.form.reset()\">Actualizar</button>
                                        </div>
                                        <div class=\"div_button_foot\"><center>
                                                <button class=\"button_foot\" onclick=\"f_nuevo()\">Agregar Nueva</button>
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
