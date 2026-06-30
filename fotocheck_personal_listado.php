<?
//classes/TCPDF/examples/personal_fotocheck.php
	require_once 'include/cabecera.php';
	require_once 'classes/Html.class.php';
	require_once 'classes/Db.class.php';
	$Db = new Db();
	if(!isset($_POST['busc_pagi_actu']))
		$_POST['busc_pagi_actu']=1;
	if(!$_POST['codi_form'])
	    $_POST['codi_form']=$_GET['codi_form'];
	if(!$_POST['codi_form'])
	    $_POST['codi_form']=1;
	switch($_POST['codi_form'])
	{
	    case 1: $nomb_form="Administrativo";  break;
	    case 2: $nomb_form="Fiscal";  break;
	    case 3: $nomb_form="Secigra";  break;
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
			function f_buscar()
			{
				document.form.action='';
				document.form.target="";
				document.form.submit();
			}
			function f_generar_fotocheck(tipo)
			{
				document.form.action='classes/TCPDF/examples/personal_fotocheck.php';
				document.form.todo_chek.value=tipo;
				document.form.target="blank";
				document.form.submit();
			}
			function f_accion_tabla()
			{
				document.form.codi_pers.value='';
				document.form.action='fotocheck_personal_registro.php';
				document.form.target="";
				document.form.submit();
			}
			function f_editar_personal(codi)
			{
				document.form.codi_pers.value=codi;
				document.form.action='fotocheck_personal_registro.php';
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
	<center><h2 style="color:#073A6B">LISTADO DE PERSONAL DEL DF AREQUIPA (<?=$nomb_form?>)</h2></center>
		<form name="form" method="post">
			<input type=hidden name="codi_pers">
			<input type=hidden name="todo_chek">
			<input type=hidden name="busc_pagi_actu" value="<?=$_POST['busc_pagi_actu']?>">
			<input type=hidden name="codi_form" value="<?=$_POST['codi_form']?>">
<?
	$html=new htmlclass;

    $arra_options_form[1]="Administrativo";
    $arra_options_form[2]="Secigrista";
    $arra_options_form[3]="Fiscal";


	$arra_options_depe[0]="<- Todas ->";
        $result=$Db->select('mp_maes_fotocheck_dependencia', '', '', '', ['x_nombre'=>'ASC']);
        foreach($result as $rows)
                $arra_options_depe[$rows['n_codigo']]=utf8_encode(utf8_decode($rows['x_nombre']));

$busc_tipo=1;	//1 nombre - 2 ndoc - 3 esca - 4 marc
$arra_options_tipo=array(1=>"Por Apellidos y Nombres","DNI");

	echo"<main>";
	echo $html->put_title_demand("BUSQUEDA DE PERSONAL ".strtoupper($nomb_form));
	echo $html->put_select("Dependencia",'codi_depe',$arra_options_depe,$_POST['codi_depe'],"");
	echo $html->put_select("Tipo",'busq_tipo',$arra_options_tipo,$_POST['busq_tipo'],"");
	echo $html->put_text('text',"<a href=\"javascript:f_buscar()\">Click&nbsp;<u>AQUI</u>&nbsp;para&nbsp;Buscar</a>","Ingrese datos (Comod&iacute;n: %)",'busq_dato',$_POST['busq_dato'],'','100','');
	echo"</main>";
	//echo"<main>";
	//echo $html->put_select("Formato",'codi_form',$arra_options_form,$_POST['codi_form'],"");
	echo"</main>";
if($_POST['busq_tipo'])
{
	$busc_item_pagi=1000;      //cantidad de items por pagina

	$buscar_depe="";
	if($_POST['codi_depe'])
		$buscar_depe=" AND codi_depe='".$_POST['codi_depe']."'";
	switch($_POST['busq_tipo'])
	{
		case 1:	$parametro="CONCAT(appe_pers,' ',nomb_pers) like :m_busq $buscar_depe"; break;
		case 2:	$parametro="ndni_pers=:m_busq $buscar_depe"; break;
	}
	
	switch($_POST['codi_form'])
	{
	    case 1: $nomb_tabl="mp_fotocheck_personal";  
	            $parametro.=" AND codi_carg NOT IN (16,17,18,19,20)";
	            break;
	    case 2: $nomb_tabl="mp_fotocheck_personal"; 
	            $parametro.=" AND codi_carg IN (16,17,18,19,20)";
	            break;
	    case 3: $nomb_tabl="mp_fotocheck_secigra";  
	            $parametro.=" AND esta_pers=1";
	            break;
	}

	$result=$Db->query("select * from $nomb_tabl where $parametro",[':m_busq'=>$_POST['busq_dato']]);
	$busc_tota_item=0;
	foreach($result as $rows)
	{       
		$busc_tota_item++;
	}
	$busc_tota_pagi=ceil($busc_tota_item/$busc_item_pagi);
	$busc_limi_pagi=($_POST['busc_pagi_actu']-1)*$busc_item_pagi;

	$result_pagi=$Db->query("select * from $nomb_tabl where $parametro order by appe_pers,nomb_pers asc limit $busc_limi_pagi,$busc_item_pagi",[':m_busq'=>$_POST['busq_dato']]);
	echo"<div style=\"width:90%;max-width:800px;margin:auto;\">";
	echo $html->put_title_demand("RESULTADOS DE B&Uacute;SQUEDA: $busc_tota_item ENCONTRADOS");

	if($busc_tota_pagi>0)
		echo $html->put_page("1",$_POST['busc_pagi_actu'],$busc_tota_pagi,"Nuevo Personal");
	$head=['1'=>"Nº",'2'=>"IMPR.",'3'=>"DNI",'4'=>"APELLIDOS",'5'=>"NOMBRES",'6'=>"FOTO",'7'=>"Hab.",'8'=>"Impr.",'9'=>"EDIT"];
	echo $html->put_table_responsive_open();
	if($busc_tota_item)
	{
		echo $html->put_table_responsive_header($head);
		$cont=$busc_limi_pagi;
		foreach($result_pagi as $rows)
		{
			$d="";
			if(!$rows['esta_pers'])
				$d="<del>";
			$f="<a href=\"javascript:alert('ERROR: Usuario no tiene foto')\"><img src=\"img/icons/x-circle.svg\" width=\"20\">";
			$g="<a href=\"javascript:alert('ERROR: Usuario no esta habilitado para imprimir fotocheck')\"><img src=\"img/icons/x-circle.svg\" width=\"20\">";
			$i="<a href=\"javascript:alert('Fotocheck sin Imprimir')\"><img src=\"img/icons/x-circle.svg\" width=\"20\">";
$f=$g=$i="NO";
			$c="disabled";
			$e="<font color=silver>";
			if(file_exists("classes/TCPDF/examples/fotos/".$rows['ndni_pers'].".jpg"))
			{
				$f="<a href=\"classes/TCPDF/examples/fotos/".$rows['ndni_pers'].".jpg\" target=\"blank\"><img src=\"img/icons/check-circle.svg\" width=\"20\"></a>";
$f="SI";
				//$e="";
				if($rows['habi_impr']==1)
				    $c=$e="";
			}
			if($rows['habi_impr']==1)
			{
			    $g="<a href=\"javascript:alert('Usuario SI esta habilitado para imprimir fotocheck')\"><img src=\"img/icons/check-circle.svg\" width=\"20\"></a>";
$g="SI";
			}
		    if($rows['esta_impr']==1)
		    {
			    $i="<a href=\"javascript:alert('Fotocheck Impreso')\"><img src=\"img/icons/check-circle.svg\" width=\"20\"></a>";
$i="SI";
			    $c="disabled";
		    }
			$cont++;
			$data=[	'1'=>$cont,
				'2'=>"<input type=checkbox $c name=\"chek_pers_".$rows['codi_pers']."\">",
				'3'=>$e.$d.$rows['ndni_pers'],
				'4'=>$e.$d.utf8_encode(utf8_decode(strtoupper($rows['appe_pers']))),
				'5'=>$e.$d.utf8_encode(utf8_decode(strtoupper($rows['nomb_pers']))),
				'6'=>$f,
				'7'=>$g,
				'8'=>$i,
				'9'=>"<a href=\"javascript:f_editar_personal('$rows[codi_pers]')\"><img src=\"img/icons/edit.svg\" width=\"20\">",
			];
			echo $html->put_table_responsive_data($head,$data);
		}
	}
	else
		echo $html->put_table_responsive_title("No Existe Personal");
		
	echo"	
	<input type=\"text\" id=\"dName\"/>	
	
	<script>
// (C) ATTACH AUTOCOMPLETE TO INPUT FIELD
ac.attach({
  target: document.getElementById(\"dName\"),
  data: \"include/search_dependencia.php\"
});
</script>

	";
	
	echo $html->put_table_responsive_close();
	if($busc_tota_pagi>0)
		echo $html->put_page("2",$_POST['busc_pagi_actu'],$busc_tota_pagi,"Nuevo Personal");
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
