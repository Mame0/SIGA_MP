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
			function f_reiniciar_formulario()
			{
				//document.form.action='classes/TCPDF/examples/personal_fotocheck.php';
				document.form.action='';
				document.form.target="";
				document.form.codi_repo.value='';
                document.form.codi_loca.value='';
                document.form.codi_depe.value='';
                document.form.codi_regi.value='';
                document.form.codi_carg.value='';
                document.form.codi_sexo.value='';
                document.form.codi_hijo.value='';
                document.form.edad_desd.value='';
                document.form.edad_hast.value='';
                document.form.codi_colu.value='';
				document.form.submit();
			}
			function f_generar_reporte()
			{
				document.form.action='';
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
	<center><h3 style="color:#073A6B"><b>REPORTE GENERAL DE PERSONAL</b></h3></center>
		<form name="form" method="post">
			<input type=hidden name="codi_pers">
			<input type=hidden name="todo_chek">
			<input type=hidden name="busc_pagi_actu" value="<?=$_POST['busc_pagi_actu']?>">
			<input type=hidden name="codi_form" value="<?=$_POST['codi_form']?>">
<?
	$html=new htmlclass;

    $arra_options_hijo[0]="<- Ambos ->";
    $arra_options_hijo[1]="Si tiene hijos";
    $arra_options_hijo[2]="No tiene hijos";
    
	//temporal
	$arra_options_sindi[0]="<- Ambos ->";
    $arra_options_sindi[1]="Si es Sindicalizado";
	$arra_options_sindi[2]="No es Sindicalizado";

	$arra_options_conad[0]="<- Ambos ->";
	$arra_options_conad[1]="Si tiene Conadis";
	$arra_options_conad[2]="No tiene Conadis";

	$arra_options_presu[0]="<- Ambos ->";
	$arra_options_presu[1]="Arequipa";
	$arra_options_presu[2]="Lima";
	//
    $arra_options_colu[1]="DNI";
    $arra_options_colu[2]="Apellidos y Nombres";
    $arra_options_colu[3]="Sexo";
    $arra_options_colu[4]="Lugar de Nacimiento";
    $arra_options_colu[5]="Fecha de Nacimiento";
    $arra_options_colu[6]="Edad";
    $arra_options_colu[7]="Nro Hijos";
    $arra_options_colu[8]="Estado Civil";
    $arra_options_colu[9]="Dirección";
    $arra_options_colu[10]="Teléfono";
    $arra_options_colu[11]="Correo Institucional";
    $arra_options_colu[12]="Correo Personal";
    $arra_options_colu[13]="Local";
    $arra_options_colu[14]="Dependencia";
    $arra_options_colu[15]="Régimen";
    $arra_options_colu[16]="Cargo";
    $arra_options_colu[17]="Fecha de Ingreso";

    $arra_options_repo[0]="<- Seleccione Reporte ->";
    $result=$Db->select('mp_admi_pers_repo', '', '', '', ['nomb_repo'=>'ASC']);
    foreach($result as $rows)
        $arra_options_repo[$rows['iden_repo']]=utf8_encode(utf8_decode($rows['nomb_repo']));
    
    $arra_options_loca[0]="<- Todos ->";
    $result=$Db->select('mp_admi_loca', '', '', '', ['nom1_loca'=>'ASC']);
    foreach($result as $rows)
        $arra_options_loca[$rows['codi_loca']]=utf8_encode(utf8_decode($rows['nom1_loca']));

	$arra_options_depe[0]="<- Todas ->";
    $result=$Db->select('mp_maes_fotocheck_dependencia', '', '', '', ['x_nombre'=>'ASC']);
    foreach($result as $rows)
        $arra_options_depe[$rows['n_codigo']]=utf8_encode(utf8_decode($rows['x_nombre']));
    
    $arra_options_regi[0]="<- Todos ->";
    $result=$Db->select('mp_maes_regimen_laboral', '', '', '', ['x_nombre'=>'ASC']);
    foreach($result as $rows)
        $arra_options_regi[$rows['n_codigo']]=utf8_encode(utf8_decode($rows['x_nombre']));
    
    $arra_options_carg[0]="<- Todos ->";
    $result=$Db->select('mp_maes_cargo', '', '', '', ['x_nombre'=>'ASC']);
    foreach($result as $rows)
        $arra_options_carg[$rows['n_codigo']]=utf8_encode(utf8_decode($rows['x_nombre']));
    
    $arra_options_sexo[0]="<- Ambos ->";
    $result=$Db->select('mp_maes_sexo', '', '', '', ['x_nombre'=>'ASC']);
    foreach($result as $rows)
        $arra_options_sexo[$rows['n_codigo']]=utf8_encode(utf8_decode($rows['x_nombre']));

	//temporal
	$arra_options_moda[0]="<- Todos ->";
    $result=$Db->select('mp_maes_modalidad_trabajo', '', '', '', ['x_nombre'=>'ASC']);
    foreach($result as $rows)
        $arra_options_moda[$rows['n_codigo']]=utf8_encode(utf8_decode($rows['x_nombre']));

$busc_tipo=1;	//1 nombre - 2 ndoc - 3 esca - 4 marc
$arra_options_tipo=array(1=>"Por Apellidos y Nombres","DNI");

	echo"<main>";
	echo $html->put_title_demand("Reportes Guardados");
	echo $html->put_select("Nombre",'codi_repo',$arra_options_repo,$_POST['codi_repo'],"");
	echo"</main><main>";
	echo $html->put_title_demand("Criterios de Búsqueda");
	echo $html->put_select_buscador("Local",'codi_loca',$arra_options_loca,$_POST['codi_loca'],"");
	echo $html->put_select("Dependencia",'codi_depe',$arra_options_depe,$_POST['codi_depe'],"multiple");
	echo $html->put_select("Régimen",'codi_regi',$arra_options_regi,$_POST['codi_regi'],"");
	echo"</main><main>";
	echo $html->put_select_buscador("Cargo",'codi_carg',$arra_options_carg,$_POST['codi_carg'],"");
	echo $html->put_select("Sexo",'codi_sexo',$arra_options_sexo,$_POST['codi_sexo'],"");
	echo $html->put_select("Tiene&nbsp;Hijos",'codi_hijo',$arra_options_hijo,$_POST['codi_hijo'],"");
	echo"</main><main>";
	echo $html->put_text('number',"Edad (Desde)","Ingrese rango inicial",'edad_desd',$_POST['edad_desd'],'','2','');
	echo $html->put_text('number',"Edad (Hasta)","Ingrese rango final",'edad_hast',$_POST['edad_hast'],'','2','');
    echo $html->put_select("Seleccione&nbsp;Columnas",'codi_colu',$arra_options_colu,$_POST['codi_colu'],"multiple");	
	echo"</main><main>";
	//temporal
	echo $html->put_select("Sindicalizado",'codi_sind',$arra_options_sindi,$_POST['codi_sind'],"");
	echo $html->put_select("Modalidad&nbsp;de&nbsp;Trabajo",'codi_moda',$arra_options_moda,$_POST['codi_moda'],"");
	echo $html->put_select("Presupuesto",'codi_presu',$arra_options_presu,$_POST['codi_presu'],"");
	echo"</main><main>";
	echo $html->put_select("Conadis",'codi_conad',$arra_options_conad,$_POST['codi_conad'],"");
	echo"</main><main>";
	
	echo $html->put_title_demand("Nombre de Reporte");
	echo $html->put_text('text',"Nombre","Ingrese nombre de reporte",'nomb_repo',$_POST['nomb_repo'],'','50','');
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
}
	if($busc_tota_item>0 OR 5==5)
	{
		echo $html->put_separator_demand("30");
		echo"
                        <div align=center class=\"foot\">
                                <center>
                                <div align=center class=\"foot2\">
                                        <div class=\"div_button_foot\" style=\"\">
                                                <button class=\"button_foot\" onclick=\"f_reiniciar_formulario()\">Reiniciar Formulario</button>
                                        </div>
                                        <div class=\"div_button_foot\"><center>
                                                <button class=\"button_foot\" onclick=\"f_generar_reporte()\">Generar Reporte</button>
                                        </div>
                                </div>
                        </div>
                ";
	}
?>

    <!-- Para el select de checkboxes-->
    <link rel="stylesheet" type="text/css" href="css/example-styles.css">
    <!--<link rel="stylesheet" type="text/css" href="css/demo-styles.css">-->
    
    <script type="text/javascript" src="js/jquery/jquery-2.2.4.min.js"></script>
    <script type="text/javascript" src="js/jquery/jquery.multi-select.js"></script>
    <script type="text/javascript">
    $(function(){
        $('#people').multiSelect();
        $('#codi_colu').multiSelect();
        $('#codi_depe').multiSelect();
    });
    </script>


<center>
	</form>
	</body>
</html>
