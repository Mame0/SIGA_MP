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
		<title>SIOJAlimentos</title>
		<link rel="stylesheet" href="css/forms_demanda.css" />
		<link rel="stylesheet" href="css/forms_foot.css" />
		<link rel="stylesheet" href="css/forms_column.css" />
		<SCRIPT language="JavaScript" src="js/cadenas.js"></SCRIPT>
		<SCRIPT language="JavaScript" src="js/denuncia.js"></SCRIPT>
		<script>
			function muestraprop(codcomp) {
				document.form.codi_comp.value=codcomp;
				document.form.action='compras_ver_postu.php';
				document.form.target="";
				document.form.submit();
			}
			function ampliavig(codcomp) {
				document.form.codi_comp.value=codcomp;
				document.form.action='compras_amplia.php';
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
				document.form.action='ftp/'+codi;
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
	<center><h2 style="color:#073A6B">PROCESOS DE COMPRAS VIGENTES</h2></center>
		<form name="form" method="post">
			<input type=hidden name="codi_docu">
			<input type=hidden name="codi_comp">
			<input type=hidden name="busc_pagi_actu" value="<?=$_POST['busc_pagi_actu']?>">
<?
	$html=new htmlclass;


	$horalocal = gmdate('Y-m-d H:i:s', time() + (-5 * 3600));//-5 es la zona horaria de perú
	$solofecha = substr($horalocal,0,10);
	$soloano = substr($solofecha,0,4) ;

        $result=$Db->select('mp_maes_comp_rubro', '', '', '', '');
        foreach($result as $rows)
                $arra_options_rubro[$rows['n_codigo']]=$rows['x_nombre'];

	$busc_item_pagi=40;      //cantidad de items por pagina

//	$result=$Db->query("select codi_comp,nomb_comp,inic_comp,fina_comp,codi_rubr from mp_comp_compras WHERE esta_comp=1 AND substring(NOW(),1,10)>=inic_comp AND substring(NOW(),1,10)<=fina_comp order by inic_comp");
	$result=$Db->query("select codi_comp,nomb_comp,inic_comp,fina_comp,codi_rubr from mp_comp_compras WHERE esta_comp=1 order by inic_comp desc");
	$busc_tota_item=0;
	foreach($result as $rows)
	{
		$busc_tota_item++;
	}
	$busc_tota_pagi=ceil($busc_tota_item/$busc_item_pagi);
	$busc_limi_pagi=($_POST['busc_pagi_actu']-1)*$busc_item_pagi;

	$result_pagi=$Db->query("select codi_comp,nomb_comp,inic_comp,fina_comp,mp_comp_compras.codi_rubr, comp_cerr, mp_comp_proveedores.nomb_prov
	from mp_comp_compras left join mp_comp_proveedores on mp_comp_compras.codi_gana=mp_comp_proveedores.codi_prov
	WHERE esta_comp=1
	order by inic_comp desc limit $busc_limi_pagi,$busc_item_pagi");

	echo"<div style=\"width:90%;max-width:800px;margin:auto; \">";//overflow-x:auto;

	if($busc_tota_pagi>0)
		echo $html->put_page("1",$_POST['busc_pagi_actu'],$busc_tota_pagi,"");
	$head=['1'=>"NÂº",'2'=>"NOMBRE",'3'=>"DESDE",'4'=>"HASTA",'5'=>"RUBRO",'6'=>"TDR",'7'=>"NRO.POST.",'8'=>"POSTULANTES",'9'=>"ESTADO&nbsp;VIGENCIA",'10'=>"RESULTADO"];
	echo $html->put_table_responsive_open();
	if($busc_tota_item)
	{
		echo $html->put_table_responsive_header($head);
		$cont=$busc_limi_pagi;
		foreach($result_pagi as $rows)
		{
			$rescnt=$Db->query("SELECT count(*) as cantpostu FROM `mp_comp_procprov` WHERE proc_codi='".$rows['codi_comp']."' ");
			$cant = $rescnt[0]["cantpostu"];

			if ($rows['comp_cerr']==0) {
				$est_vig = (($rows['fina_comp']<$solofecha)?"<input type='button' id='amp".$rows["codi_comp"]."' name='amp".$rows["codi_comp"]."' value='Ampliar' onclick='ampliavig(".$rows["codi_comp"].")'>":"Vigente");
				$resultado="-";
			} else {
				$est_vig = "CERRADO";
				$resultado=(($rows['nomb_prov']=="")?"SIN GANADOR":$rows['nomb_prov']);
			}


			$cont++;
			$data=[	'1'=>$cont,
				'2'=>$rows['nomb_comp'],
				'3'=>$rows['inic_comp'],
				'4'=>$rows['fina_comp'],
				'5'=>$arra_options_rubro[$rows['codi_rubr']],
				'6'=>"<a href=\"javascript:f_ver('comp_".str_pad($rows['codi_comp'], 6, "0", STR_PAD_LEFT).".pdf')\"><img src=\"img/pdf_image.gif\" width=\"20\">",
				'7'=> $cant ,
				'8'=>(($cant>=2)?"<input type='button' id='ver".$rows["codi_comp"]."' name='ver".$rows["codi_comp"]."' value='Mostrar' onclick='muestraprop(".$rows["codi_comp"].")'>":"Insuficientes") ,
				'9'=> $est_vig,
				'10'=> $resultado,
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

?>
<center>
	</form>
	</body>
</html>
