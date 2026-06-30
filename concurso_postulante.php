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
				document.form.action='classes/TCPDF/examples/voluntariado_fotocheck.php';
				document.form.todo_chek.value=tipo;
				document.form.target="blank";
				document.form.submit();
			}
			function f_accion_tabla()
			{
				document.form.codi_pers.value='';
				document.form.action='personal_registro.php';
				document.form.target="";
				document.form.submit();
			}
			function f_editar_personal(codi)
			{
				document.form.codi_post.value=codi;
				document.form.action='concurso_registro.php';
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
	<center><h2 style="color:#073A6B">LISTADO DE POSTULANTES</h2></center>
		<form name="form" method="post">
			<input type=hidden name="codi_post">
			<input type=hidden name="todo_chek">
			<input type=hidden name="codi_plaz">
			<input type=hidden name="busc_pagi_actu" value="<?=$_POST['busc_pagi_actu']?>">
			<input type=hidden name="codi_form" value="<?=$_POST['codi_form']?>">
<?
	$html=new htmlclass;
	
	$busc_tipo=1;	//1 nombre - 2 ndoc - 3 esca - 4 marc
    $arra_options_tipo=array(1=>"Por Apellidos y Nombres","DNI");
    
    $result=$Db->select('mp_maes_concurso_regimen', '', '', '', ['x_nombre'=>'ASC']);
	foreach($result as $rows)
		$arra_options_regi[$rows['n_codigo']]=$rows['x_nombre'];

	$result=$Db->select('mp_concurso_examen','','','',['fech_exam'=>'ASC']);
	$arra_options_exam[0]="<- ".CONST_OPTION_SELECT." ->";
	foreach ($result as $rows)
		$arra_options_exam[$rows['codi_exam']]="[".$rows['fech_exam']."] ";
	
	$result=$Db->select('mp_concurso_proceso','','','',['codi_proc'=>'ASC']);
	$arra_options_proc[0]="<- ".CONST_OPTION_SELECT." ->";
	foreach ($result as $rows)
		$arra_options_proc[$rows['codi_proc']]=$arra_options_exam[$rows['codi_exam']]." ".$arra_options_regi[$rows['regi_proc']]." Nro. ".$rows['nume_proc']."-".$rows['anno_proc'];
	
	$arra_options_carg[0]="<- Seleccione ->";
	$result=$Db->select('mp_maes_fotocheck_cargo', '', '', '', ['x_nombre'=>'ASC']);
	foreach($result as $rows)
		$arra_options_carg[$rows['n_codigo']]=$rows['x_nombre'];
		
	$result=$Db->select('mp_concurso_plazas','','','',['codi_plaz'=>'ASC']);
	$arra_options_plaz[0]="<- ".CONST_OPTION_SELECT." ->";
	foreach ($result as $rows)
		$arra_options_plaz[$rows['codi_plaz']]=$arra_options_proc[$rows['codi_proc']]." - ".$rows['nomb_plaz']." - [".$arra_options_carg[$rows['codi_carg']]."]";

	echo"<main>";
	echo $html->put_title_demand("BUSQUEDA DE POSTULANTES");
	echo $html->put_select("Plaza",'codi_plaz',$arra_options_plaz,(isset($_POST['codi_plaz']) ? $_POST['codi_plaz'] : ''),"onchange=\"document.form.submit()\" required");
	echo $html->put_select("Tipo",'busq_tipo',$arra_options_tipo,(isset($_POST['busq_tipo']) ? $_POST['busq_tipo'] : ''),"");
	echo $html->put_text('text',"<a href=\"javascript:f_buscar()\">Click&nbsp;<u>AQUI</u>&nbsp;para&nbsp;Buscar</a>","Ingrese datos (Comod&iacute;n: %)",'busq_dato',(isset($_POST['busq_dato']) ? $_POST['busq_dato'] : ''),'','100','');
	echo"</main>";
	//echo"<main>";
	//echo $html->put_select("Formato",'codi_form',$arra_options_form,$_POST['codi_form'],"");
	echo"</main>";
if(isset($_POST['codi_plaz']) && $_POST['codi_plaz'])
{
	$busc_item_pagi=1000;      //cantidad de items por pagina
	if($_POST['busq_dato'])
	{
	    switch($_POST['busq_tipo'])
	    {
    		case 1:	$parametro="AND CONCAT(appa_post,' ',apma_post,' ',nomb_post) like '".$_POST['busq_dato']."'"; break;
		    case 2:	$parametro="AND docu_post='".$_POST['busq_dato']."'"; break;
	    }
	}
	$result=$Db->query("select * from mp_concurso_postulantes where codi_plaz='".$_POST['codi_plaz']."' $parametro");
	//$result=$Db->query("select * from mp_concurso_postulantes where codi_plaz='".."'");
	$busc_tota_item=0;
	foreach($result as $rows)
	{       
		$busc_tota_item++;
	}

	$busc_tota_pagi=ceil($busc_tota_item/$busc_item_pagi);
	$busc_limi_pagi=($_POST['busc_pagi_actu']-1)*$busc_item_pagi;

	$result_pagi=$Db->query("select * from mp_concurso_postulantes where codi_plaz='".$_POST['codi_plaz']."' $parametro order by appa_post,apma_post,nomb_post asc limit $busc_limi_pagi,$busc_item_pagi");

	echo"<div style=\"width:90%;max-width:800px;margin:auto;\">";
	echo $html->put_title_demand("RESULTADOS DE B&Uacute;SQUEDA: $busc_tota_item ENCONTRADOS");

	if($busc_tota_pagi>0  OR 5==5)
		echo $html->put_page("1",$_POST['busc_pagi_actu'],$busc_tota_pagi,"Nuevo Postulante");
	$head=['1'=>"Nº",'2'=>"IMPR.",'3'=>"DOCUMENTO",'4'=>"AP.PATERNO",'6'=>"AP.MATERNO",'7'=>"NOMBRES",'8'=>"ASISTENCIA",'9'=>"ESTADO",'10'=>"EDIT"];
	echo $html->put_table_responsive_open();
	if($busc_tota_item OR 5==5)
	{
		echo $html->put_table_responsive_header($head);
		$cont=$busc_limi_pagi;
		foreach($result_pagi as $rows)
		{
			$cont++;
			
			$asis='NO';
			if($rows['regi_asis'])
			    $asis='SI';
			
			$esta='';
			if(!$rows['esta_post'])
			    $esta='<font color=silver>';
			    
			$data=[	'1'=>$cont,
				'2'=>"<input type=checkbox $c name=\"chek_pers_".$rows['codi_volu']."\">",
				'3'=>$esta.$rows['docu_post'],
				'4'=>$esta.utf8_encode(utf8_decode(strtoupper($rows['appa_post']))),
				'6'=>$esta.utf8_encode(utf8_decode(strtoupper($rows['apma_post']))),
				'7'=>$esta.utf8_encode(utf8_decode(strtoupper($rows['nomb_post']))),
				'8'=>$esta.$asis,
				'9'=>$esta.$rows['esta_post'],
				'10'=>"<a href=\"javascript:f_editar_personal('$rows[codi_post]')\"><img src=\"img/icons/edit.svg\" width=\"20\">",
			];
			echo $html->put_table_responsive_data($head,$data);
		}
	}
	else
		echo $html->put_table_responsive_title("No Existen Postulantes");
		
	echo"	
	
	<script>
// (C) ATTACH AUTOCOMPLETE TO INPUT FIELD
ac.attach({
  target: document.getElementById(\"dName\"),
  data: \"include/search_dependencia.php\"
});
</script>

	";
	
	echo $html->put_table_responsive_close();
	if($busc_tota_pagi>0  OR 5==5)
		echo $html->put_page("2",$_POST['busc_pagi_actu'],$busc_tota_pagi,"Nuevo Postulante");
	echo"</div>";
	if($busc_tota_item>0)
	{
		echo $html->put_separator_demand("30");
		echo"
                        <div align=center class=\"foot\">
                                <center>
                                <div align=center class=\"foot2\">
                                        <div class=\"div_button_foot\" style=\"\">
                                                <button class=\"button_foot\" onclick=\"f_generar_fotocheck('2')\">Imprimir Seleccionados (check)</button>
                                        </div>
                                        <div class=\"div_button_foot\"><center>
                                                <button class=\"button_foot\" onclick=\"f_generar_fotocheck('1')\">Imprimir toda la B&uacute;squeda</button>
                                        </div>
                                </div>
                        </div>
                ";
	}
}
?>
<center>
	</form>
	</body>
</html>
