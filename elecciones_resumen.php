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
			function f_editar(codi)
			{
				document.form.codi_difu.value=codi;
				document.form.regresar_reporte.value='1';
				document.form.action='elecciones_difusion_nuevo.php';
				document.form.target="";
				document.form.submit();
			}
			function f_nuevo()
			{
				document.form.codi_difu.value='';
				document.form.regresar_reporte.value='1';
				document.form.action='classes/TCPDF/examples/elecciones2022_formatoA.php';
				document.form.target="blank";
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
	<center><font style="color:#073A6B;font-weight: bold;"><?=$_POST['nomb_elec']?><BR><font style="font-size: 20;">RESUMEN</font><br>(<?=date("d/m/Y H:i:s")?> horas)</font></center><BR><BR>
		<form name="form" method="post">
			<input type=hidden name="codi_difu">
			<input type=hidden name="regresar_reporte">
			<input type=hidden name="busc_pagi_actu" value="<?=$_POST['busc_pagi_actu']?>">
			<input type=hidden name="iden_oper" value="<?=$_SESSION['iden_oper']?>">
<?
	$html=new htmlclass;

	$busc_item_pagi=40;      //cantidad de items por pagina

	//$result=$Db->query("select * from mp_jurisprudencia_documento where nomb_docu like '%:m_busq%'",[':m_busq'=>$_POST['text_busc']]);

	$busc_tota_pagi=ceil($busc_tota_item/$busc_item_pagi);
	$busc_limi_pagi=($_POST['busc_pagi_actu']-1)*$busc_item_pagi;
	
	$result=$Db->query("select distinct cpro,prov from ubig_reni WHERE cdep='04'");
	foreach($result as $rows)
		$arra_options_prov[$rows['cpro']]=$rows['prov'];
		
	$result=$Db->select('mp_maes_elecciones_difusion', '', '', '', ['n_codigo'=>'ASC']);
	foreach($result as $rows)
		$arra_options_tdif[$rows['n_codigo']]=$rows['x_nombre'];
	
	$arra_options_inst=$Db->get_options('mp_maes_elecciones_instituciones');
	
	$result=$Db->query("select codi_inst,SUBSTRING(ubig_coor,1,4) as prov_dist,count(*) as cant_coor from mp_elec_coordinaciones where codi_elec='".$_POST['codi_elec']."' AND esta_coor='1' group by codi_inst,prov_dist");
	foreach($result as $rows)
	{
	    $arra_cant_coor[$rows['codi_inst']][$rows['prov_dist']]=$rows['cant_coor'];
	}
	
	echo"<div style=\"width:90%;max-width:800px;margin:auto;\">";

    echo $html->put_title_demand("Resúmen de Coordinaciones Interinstitucionales");
	$result=$Db->query("select * from mp_maes_elecciones_instituciones where n_estado='1' order by x_nombre");
	$head=['1'=>"",'2'=>"<IMG src=\"img/prov_0401.png\" width='20'>",'3'=>"<IMG src=\"img/prov_0402.png\" width=20>",'4'=>"<IMG src=\"img/prov_0403.png\" width=20>",'5'=>"<IMG src=\"img/prov_0404.png\" width=20>",'6'=>"<IMG src=\"img/prov_0405.png\" width=20>",'7'=>"<IMG src=\"img/prov_0406.png\" width=20>",'8'=>"<IMG src=\"img/prov_0407.png\" width=20>",'9'=>"<IMG src=\"img/total.png\" width=20>"];
	echo $html->put_table_responsive_open();
	echo $html->put_table_responsive_header($head);
	unset($tota,$subt,$arra_tota);
	foreach($result as $rows)
	{
		$cont++;
		$subt=$arra_cant_coor[$rows['n_codigo']]['0401']+$arra_cant_coor[$rows['n_codigo']]['0402']+$arra_cant_coor[$rows['n_codigo']]['0403']+$arra_cant_coor[$rows['n_codigo']]['0404']+$arra_cant_coor[$rows['n_codigo']]['0405']+$arra_cant_coor[$rows['n_codigo']]['0406']+$arra_cant_coor[$rows['n_codigo']]['0407'];
		$arra_tota['0401']+=$arra_cant_coor[$rows['n_codigo']]['0401'];
		$arra_tota['0402']+=$arra_cant_coor[$rows['n_codigo']]['0402'];
		$arra_tota['0403']+=$arra_cant_coor[$rows['n_codigo']]['0403'];
		$arra_tota['0404']+=$arra_cant_coor[$rows['n_codigo']]['0404'];
		$arra_tota['0405']+=$arra_cant_coor[$rows['n_codigo']]['0405'];
		$arra_tota['0406']+=$arra_cant_coor[$rows['n_codigo']]['0406'];
		$arra_tota['0407']+=$arra_cant_coor[$rows['n_codigo']]['0407'];
		$tota+=$subt;
		$data=[	'1'=>$rows['x_nombre'],
			'2'=>number_format($arra_cant_coor[$rows['n_codigo']]['0401'],0),
			'3'=>number_format($arra_cant_coor[$rows['n_codigo']]['0402'],0),
			'4'=>number_format($arra_cant_coor[$rows['n_codigo']]['0403'],0),
			'5'=>number_format($arra_cant_coor[$rows['n_codigo']]['0404'],0),
			'6'=>number_format($arra_cant_coor[$rows['n_codigo']]['0405'],0),
			'7'=>number_format($arra_cant_coor[$rows['n_codigo']]['0406'],0),
			'8'=>number_format($arra_cant_coor[$rows['n_codigo']]['0407'],0),
			'9'=>"<b>$subt",
		];
		echo $html->put_table_responsive_data($head,$data);
		//'7'=>"<div class='Table'><div class='Row'><div class='Cell'><p>$rows[cant_homb]</p></div><div class='Cell'><p><img src='img/icons/man_color.svg' height='20'></p></div><div class='Cell'><p>&nbsp;</p></div><div class='Cell'><p><img src='img/icons/woman_color.svg' height='20'></p></div>            <div class='Cell'><p>$rows[cant_muje]</p></div></div></div>",
	}
		$cont++;
		$data=[	'1'=>"<b>TOTAL",
				'2'=>"<b>".$arra_tota['0401'],
				'3'=>"<b>".$arra_tota['0402'],
				'4'=>"<b>".$arra_tota['0403'],
				'5'=>"<b>".$arra_tota['0404'],
				'6'=>"<b>".$arra_tota['0405'],
				'7'=>"<b>".$arra_tota['0406'],
				'8'=>"<b>".$arra_tota['0407'],
				'9'=>"<b>$tota",
			];
			echo $html->put_table_responsive_data($head,$data);

	//if($busc_tota_pagi>0)
	//	echo $html->put_page("2",$_POST['busc_pagi_actu'],$busc_tota_pagi,"");
	echo"</main>";
	echo $html->put_table_responsive_close();
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
                                                <button class=\"button_foot\" onclick=\"f_nuevo()\">Formato A</button>
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
