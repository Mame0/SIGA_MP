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
			function f_acta(acta)
			{
			    if(acta==1)
			        alert('No existe acta');
			    else
			    {
				    document.form.action=acta;
				    document.form.target="blank";
				    document.form.submit();
			    }
			}
			function f_editar(codi)
			{
				document.form.codi_aler.value=codi;
				document.form.regresar_reporte.value='1';
				document.form.action='elecciones_alertas_ver.php';
				document.form.target="";
				document.form.submit();
			}
			function f_lesionados(codi)
			{
				document.form.codi_aler.value=codi;
				document.form.regresar_reporte.value='1';
				document.form.action='elecciones_alertas_lesionados.php';
				document.form.target="";
				document.form.submit();
			}
			function f_formato(codi)
			{
				document.form.codi_aler.value=codi;
				document.form.regresar_reporte.value='1';
				document.form.action='classes/TCPDF/examples/elecciones2022_formatoB.php';
				document.form.target="blank";
				document.form.submit();
			}
			function f_nuevo()
			{
				document.form.codi_aler.value='';
				document.form.regresar_reporte.value='1';
				document.form.action='elecciones_alertas_nuevo.php';
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
	<center><font style="color:#073A6B;font-weight: bold;"><?=$_POST['nomb_elec']?><BR><font style="font-size: 20;">FORMATO B<BR>ALERTAS U OCURRENCIAS</font><br>REPORTE</font></center>
		<form name="form" method="post">
			<input type=hidden name="codi_aler">
			<input type=hidden name="regresar_reporte">
			<input type=hidden name="busc_pagi_actu" value="<?=$_POST['busc_pagi_actu']?>">
<?
	$html=new htmlclass;
	
	echo"<div style=\"width:90%;max-width:800px;margin:auto;\">";
	
	$result=$Db->query("select codi_tale,SUBSTRING(ubig_aler,1,4) as prov_dist,count(*) as cant_prev from mp_elec_alertas where codi_elec='".$_POST['codi_elec']."' AND esta_aler='1' group by codi_tale,prov_dist");
	foreach($result as $rows)
	{
	    $arra_cant_aler[$rows['codi_tale']][$rows['prov_dist']]=$rows['cant_prev'];
	}

    echo $html->put_title_demand("RES&Uacute;MEN");
	$result=$Db->query("select * from mp_maes_elecciones_alertas_tipo where n_estado='1' order by n_codigo");
	$head=['1'=>"",'2'=>"<IMG src=\"img/prov_0401.png\" width='20'>",'3'=>"<IMG src=\"img/prov_0402.png\" width=20>",'4'=>"<IMG src=\"img/prov_0403.png\" width=20>",'5'=>"<IMG src=\"img/prov_0404.png\" width=20>",'6'=>"<IMG src=\"img/prov_0405.png\" width=20>",'7'=>"<IMG src=\"img/prov_0406.png\" width=20>",'8'=>"<IMG src=\"img/prov_0407.png\" width=20>",'9'=>"<IMG src=\"img/prov_0408.png\" width=20>",'10'=>"<IMG src=\"img/total.png\" width=20>"];
	echo $html->put_table_responsive_open();
	echo $html->put_table_responsive_header($head);
	unset($tota,$subt,$arra_tota);
	foreach($result as $rows)
	{
		$cont++;
		$subt=$arra_cant_aler[$rows['n_codigo']]['0401']+$arra_cant_aler[$rows['n_codigo']]['0402']+$arra_cant_aler[$rows['n_codigo']]['0403']+$arra_cant_aler[$rows['n_codigo']]['0404']+$arra_cant_aler[$rows['n_codigo']]['0405']+$arra_cant_aler[$rows['n_codigo']]['0406']+$arra_cant_aler[$rows['n_codigo']]['0407']+$arra_cant_aler[$rows['n_codigo']]['0408'];
		$arra_tota['0401']+=$arra_cant_aler[$rows['n_codigo']]['0401'];
		$arra_tota['0402']+=$arra_cant_aler[$rows['n_codigo']]['0402'];
		$arra_tota['0403']+=$arra_cant_aler[$rows['n_codigo']]['0403'];
		$arra_tota['0404']+=$arra_cant_aler[$rows['n_codigo']]['0404'];
		$arra_tota['0405']+=$arra_cant_aler[$rows['n_codigo']]['0405'];
		$arra_tota['0406']+=$arra_cant_aler[$rows['n_codigo']]['0406'];
		$arra_tota['0407']+=$arra_cant_aler[$rows['n_codigo']]['0407'];
		$arra_tota['0408']+=$arra_cant_aler[$rows['n_codigo']]['0408'];
		$tota+=$subt;
		if(strlen($rows['x_nombre'])>54)
		    $rows['x_nombre']=substr($rows['x_nombre'],0,54)."...";
		$data=[	'1'=>"<div style=\"text-align: -webkit-left;\">".$rows['x_nombre'],
			'2'=>number_format($arra_cant_aler[$rows['n_codigo']]['0401'],0),
			'3'=>number_format($arra_cant_aler[$rows['n_codigo']]['0402'],0),
			'4'=>number_format($arra_cant_aler[$rows['n_codigo']]['0403'],0),
			'5'=>number_format($arra_cant_aler[$rows['n_codigo']]['0404'],0),
			'6'=>number_format($arra_cant_aler[$rows['n_codigo']]['0405'],0),
			'7'=>number_format($arra_cant_aler[$rows['n_codigo']]['0406'],0),
			'8'=>number_format($arra_cant_aler[$rows['n_codigo']]['0407'],0),
			'9'=>number_format($arra_cant_aler[$rows['n_codigo']]['0408'],0),
			'10'=>"<b>$subt",
		];
		echo $html->put_table_responsive_data($head,$data);
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
			'9'=>"<b>".$arra_tota['0408'],
			'10'=>"<b>$tota",
	];
	echo $html->put_table_responsive_data($head,$data);
	echo"</main>";
	echo $html->put_table_responsive_close();

	$busc_item_pagi=40;      //cantidad de items por pagina

	//$result=$Db->query("select * from mp_jurisprudencia_documento where nomb_docu like '%:m_busq%'",[':m_busq'=>$_POST['text_busc']]);
	
	$result=$Db->query("select codi_aler,codi_tale,fech_aler,ubig_aler from mp_elec_alertas WHERE codi_elec='".$_POST['codi_elec']."' AND esta_aler='1'");
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
		
	$result=$Db->select('mp_maes_elecciones_alertas_tipo', '', '', '', ['n_codigo'=>'ASC']);
	foreach($result as $rows)
	{
	    if(strlen($rows['x_nombre'])>30)
	        $rows['x_nombre']=substr($rows['x_nombre'],0,30).'...';
		$arra_options_tale[$rows['n_codigo']]=$rows['x_nombre'];
	}
	
	$result=$Db->query("select iden_oper,logi_oper,ndoc_oper,appa_oper,apma_oper,nomb_oper from mp_admi_oper");
	foreach($result as $rows)
	{
		$arra_usua[$rows['iden_oper']]=$rows['nomb_oper']." ".$rows['appa_oper'];
	}

	$result_pagi=$Db->query("select codi_aler,codi_tale,fech_aler,ubig_aler,esta_aler,digi_aler from mp_elec_alertas WHERE codi_elec='".$_POST['codi_elec']."' AND esta_aler='1' order by codi_aler limit $busc_limi_pagi,$busc_item_pagi");
	echo $html->put_title_demand("RESULTADOS DE B&Uacute;SQUEDA: $busc_tota_item ENCONTRADOS");
	if($busc_tota_pagi>0)
		echo $html->put_page("1",$_POST['busc_pagi_actu'],$busc_tota_pagi,"");
	$head=['1'=>"NRO.",'2'=>"TIPO",'3'=>"FECHA",'4'=>"UBICACION",'5'=>"FISCAL",'6'=>"VER",'7'=>"ACTA",'8'=>"FORM"];
	echo $html->put_table_responsive_open();
	if($busc_tota_item)
	{
		echo $html->put_table_responsive_header($head);
		$cont=$busc_limi_pagi;
		foreach($result_pagi as $rows)
		{
			$cont++;
			$rows['fech_aler']=substr($rows['fech_aler'],6,2).'/'.substr($rows['fech_aler'],4,2).'/'.substr($rows['fech_aler'],0,4);
			$del="";
			if($rows['esta_aler']==0)
			    $del="<del><font color=red>";
			
			$acta="actas/aler_".str_pad($rows['codi_aler'], 6, "0", STR_PAD_LEFT).".pdf";
			$imag="img/pdf_image.gif";
			if(!file_exists($acta))
			{
			    $acta="1";
			    $imag="img/pdf_black.png";
			}
			
			$data=[	'1'=>$del.$cont,
				'2'=>$del.$arra_options_tale[$rows['codi_tale']],
				'3'=>$del.$rows['fech_aler'],
				'4'=>$del.$arra_options_prov[$rows['ubig_aler']],
				'5'=>$del.$arra_usua[$rows['digi_aler']],
				'6'=>"<a href=\"javascript:f_editar('$rows[codi_aler]')\"><img src=\"img/icons/eye.svg\" width=\"20\">",
				'7'=>"<a href=\"javascript:f_acta('$acta')\"><img src=\"$imag\" width=\"20\">",
				'8'=>"<a href=\"javascript:f_formato('$rows[codi_aler]')\"><img src=\"img/pdf_image.gif\" width=\"20\">",
			];
			echo $html->put_table_responsive_data($head,$data);
		}
	}
	else
		echo $html->put_table_responsive_title("No Existen Registros".$_POST['anno_elec']);
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
                                        <!--<div class=\"div_button_foot\"><center>
                                                <button class=\"button_foot\" onclick=\"f_nuevo()\">Nueva Alerta</button>
                                        </div>-->
                                </div>
                        </div>
                ";
	//}
	
?>
<center>
	</form>
	</body>
</html>
