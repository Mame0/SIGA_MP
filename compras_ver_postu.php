<?
	require_once 'include/cabecera.php';
	require_once 'classes/Html.class.php';
	require_once 'classes/Db.class.php';
	$Db = new Db();
	if(!isset($_POST['busc_pagi_actu']))
		$_POST['busc_pagi_actu']=1;

	if(isset($_POST['proc_auto'])) {

		$codi_comp=$_POST['codi_comp'];
		$result=$Db->query("select * from mp_comp_procprov WHERE proc_auto='".$_POST['proc_auto']."' ");
		$proc_prov=$result[0]["proc_prov"];

			$result=$Db->update('mp_comp_compras',['codi_gana'=>$proc_prov,'comp_cerr'=>1 ] , ['codi_comp'=>$codi_comp] );
			$result=$Db->update('mp_comp_procprov',['proc_resu'=>1 ] , ['proc_auto'=>$_POST['proc_auto'] ] );

		echo"
			<!--<script>alert('".CONST_MENS_REG_OK."');</script>-->
                        <html><body>
                                <form name=\"form\" method=post action=\"compras_seguimiento.php\">
					<input type=hidden name=\"busq_tipo\" value=\"".$_POST['busq_tipo']."\">
					<input type=hidden name=\"busq_dato\" value=\"".$_POST['busq_dato']."\">
					<input type=hidden name=\"busq_pagi_actu\" value=\"".$_POST['busq_pagi_actu']."\">
                                </form>
                                <script>
                                        document.form.submit();
                                </script>
                        </body></html>
		";

		exit();
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
			function f_cancelar()
			{
				document.form.action='compras_seguimiento.php';
				document.form.target="";
				document.form.submit();
			}
			function guardawin(codprop) {
				document.form.proc_auto.value=codprop;
				document.form.guarda_win.value=1;
				document.form.action='compras_ver_postu.php';
				document.form.target="";
				document.form.submit();
			}
			function check_buscar()
			{
				document.form.action='';
				document.form.target="";
				document.form.submit();
			}
			function f_ver(codi)
			{
				document.form.action='../provee/prop_pdfs/'+codi;
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
	</head>
	<body style="margin-bottom: 30px;">
	<center><h2 style="color:#073A6B">POSTULANTES A PROCESO</h2></center>
		<form name="form" method="post">
			<input type=hidden name="proc_auto">
			<input type=hidden name="guarda_win">
			<input type=hidden name="codi_comp" value="<?=$_POST['codi_comp']?>">
			<input type=hidden name="busc_pagi_actu" value="<?=$_POST['busc_pagi_actu']?>">
<?
	$html=new htmlclass;


	$horalocal = gmdate('Y-m-d H:i:s', time() + (-5 * 3600));//-5 es la zona horaria de perú
	$solofecha = substr($horalocal,0,10);
	$soloano = substr($solofecha,0,4) ;


	$result=$Db->query("select codi_comp,nomb_comp,inic_comp,fina_comp,codi_rubr,comp_cerr from mp_comp_compras WHERE codi_comp='".$_POST['codi_comp']."' ");
	$nomcomp=$result[0]["nomb_comp"];
	$codrubr=$result[0]["codi_rubr"];
	$fecinic=$result[0]["inic_comp"];
	$fecfina=$result[0]["fina_comp"];
	$compcer=$result[0]["comp_cerr"];



        $result=$Db->select('mp_comp_lugarentrega', '', '', '', '');
        foreach($result as $rows)
                $arra_options_lugent[$rows['n_codigo']]=$rows['x_nombre'];



        $result=$Db->select('mp_maes_comp_rubro', '', '', '', '');
        foreach($result as $rows)
                $arra_options_rubro[$rows['n_codigo']]=$rows['x_nombre'];

	$busc_item_pagi=40;      //cantidad de items por pagina

	$result=$Db->query("select * from mp_comp_procprov WHERE proc_codi='".$_POST['codi_comp']."' order by proc_fech ");
	$busc_tota_item=0;
	foreach($result as $rows)
	{
		$busc_tota_item++;
	}
	$busc_tota_pagi=ceil($busc_tota_item/$busc_item_pagi);
	$busc_limi_pagi=($_POST['busc_pagi_actu']-1)*$busc_item_pagi;

	$result_pagi=$Db->query("select * from mp_comp_procprov WHERE proc_codi='".$_POST['codi_comp']."' order by proc_fech limit $busc_limi_pagi,$busc_item_pagi");

	echo"<div style=\"width:90%;max-width:800px;margin:auto; \">";//overflow-x:auto;
	echo "<b>Descripci&oacute;n : </b>".$nomcomp."<br>
	<b>Rubro : </b>". $arra_options_rubro[$codrubr] ."<br>
	<b>Vigencia : </b>". $fecinic . " - " . $fecfina ."<br>
	<b>Postulantes : </b>". $busc_tota_item;


	if($busc_tota_pagi>0)
		echo $html->put_page("1",$_POST['busc_pagi_actu'],$busc_tota_pagi,"");
	$head=['1'=>"NÂº",'2'=>"FECHA&nbsp;POSTUL&Oacute;",'3'=>"LUGAR&nbsp;ENTREGA",'4'=>"PLAZO&nbsp(d&iacute;as)",'5'=>"MONTO",'6'=>"VER&nbsp;PDF",'7'=>"INC.IGV?",'8'=>"RESULTADO"];
	echo $html->put_table_responsive_open();
	if($busc_tota_item)
	{
		echo $html->put_table_responsive_header($head);
		$cont=$busc_limi_pagi;
		foreach($result_pagi as $rows)
		{
			$cont++;

			if ($compcer==0) {
				$resu="<input type='button' id='win".$rows['proc_auto']."' name='win".$rows['proc_auto']."' value='Esta propuesta GANO' onclick='guardawin(".$rows['proc_auto'].")'>";
			} else {
				$resu=(($rows['proc_resu']==1)?"GANADOR":"-");
			}

			$data=[	'1'=>$cont,
				'2'=>$rows['proc_fech'],
				'3'=> $arra_options_lugent[$rows['proc_luge']] ,
				'4'=>$rows['proc_dias'],
				'5'=>$rows['proc_mont'],
				'6'=>"<a href=\"javascript:f_ver('prop".$rows['proc_auto'].".pdf')\"><img src=\"img/pdf_image.gif\" width=\"20\">",
				'7'=>(($rows['proc_incigv']==1)?"SI":"NO"),
				'8'=>$resu,
			];
			echo $html->put_table_responsive_data($head,$data);
		}
	}
	else
		echo $html->put_table_responsive_title("No Existen Procesos Vigentes");
	echo $html->put_table_responsive_close();
	if($busc_tota_pagi>0)
		echo $html->put_page("2",$_POST['busc_pagi_actu'],$busc_tota_pagi,"");
	echo"</div>";


	echo $html->put_separator_demand("30");

                echo"
                        <div align=center class=\"foot\">
                                <center>
                                <div align=center class=\"foot2\">
                                        <div class=\"div_button_foot\" style=\"\">
                                                <button class=\"button_foot\" onclick=\"f_cancelar()\">&laquo; Cancelar</button>
                                        </div>
                                </div>
                        </div>
                ";


?>
<center>
	</form>
	</body>
</html>

