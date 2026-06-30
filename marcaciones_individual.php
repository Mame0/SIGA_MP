<?
//classes/TCPDF/examples/personal_fotocheck.php
	require_once 'include/cabecera.php';
	require_once 'classes/Html.class.php';
	require_once 'classes/Db.class.php';
	$Db = new Db();
	if(!isset($_POST['busc_pagi_actu']))
		$_POST['busc_pagi_actu']=1;
	
	$result=$Db->query("select max(upload_time) maxi_uplo from mp_asis_marcaciones");
	foreach($result as $rows)
	    $maxi_uplo=$rows['maxi_uplo'];

	if(!$_POST['busq_tipo'])
	{
		$_POST['busq_tipo']=1;
		$_POST['fech_desd']=date("Y-m-d");
		$_POST['fech_hast']=date("Y-m-d");
	}
	if(!$_POST['ndoc_trab'])
		$_POST['ndoc_trab']=$_GET['logi_oper'];
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
		    function ajustar_rango()
		    {
		        var tipo=document.form.busq_tipo.value;
		        switch(tipo)
		        {
		            case '1':
		                var desd='<?=date("Y-m-d")?>';
		                var hast='<?=date("Y-m-d")?>';
		                break;
		            case '2':
		                var desd='<?=date('Y-m-d',strtotime("-1 days"))?>';
		                var hast='<?=date('Y-m-d',strtotime("-1 days"))?>';
		                break;
		            case '3':
		                var desd='<?=date('Y-m-01')?>';
		                var hast='<?=date("Y-m-d")?>';
		                break;
		            case '4':
		                var desd='<?=date('Y-m-d',strtotime("-30 days"))?>';
		                var hast='<?=date("Y-m-d")?>';
		                break;
		            case '5':
		                var desd='<?=date('Y-01-01')?>';
		                var hast='<?=date("Y-m-d")?>';
		                break;
		            case '6':
		                var desd='<?=date('Y-01-01',strtotime("-1 years"))?>';
		                var hast='<?=date("Y-12-31",strtotime("-1 years"))?>';
		                break;
		        }
		        document.form.fech_desd.value=desd;
		        document.form.fech_hast.value=hast;
		        //var tipo='1';
		        //alert(document.form.fech_desd.value);
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
			function f_accion_tabla()
			{
				document.form.codi_pers.value='';
				document.form.action='jurisprudencia_registro.php';
				document.form.target="";
				document.form.submit();
			}
			function f_editar(codi)
			{
				document.form.codi_docu.value=codi;
				document.form.action='jurisprudencia_registro.php';
				document.form.target="";
				document.form.submit();
			}
			function f_nuevo()
			{
				document.form.codi_docu.value='';
				document.form.action='jurisprudencia_registro.php';
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
	<center><h4 style="color:#073A6B"><B>REPORTE DE REGISTRO DE MARCACIONES</b><br>(DNI: <?=$_POST['ndoc_trab']?>)</h4></center>
		<form name="form" method="post">
		<input type=hidden name="ndoc_trab" value="<?=$_POST['ndoc_trab']?>">
		<input type=hidden name="busc_pagi_actu" value="<?=$_POST['busc_pagi_actu']?>">
<?
	$html=new htmlclass;
	
	$anno_actu=date(Y);
    $anno_pasa=date(Y)-1;
	$arra_options_busq[0]="<- Seleccione ->";
	$arra_options_busq[1]="Hoy";
	$arra_options_busq[2]="Ayer";
	$arra_options_busq[3]="Mes actual";
	$arra_options_busq[4]="Últimos 30 días";
	$arra_options_busq[5]="Año $anno_actu";
	$arra_options_busq[6]="Año $anno_pasa";
	$arra_options_busq[7]="Rango de Fechas";
	
    
	echo"<main>";
	//echo $html->put_title_demand("Criterios de Búsqueda");
		echo $html->put_select("Rangos&nbsp;Pre&nbsp;Definidos",'busq_tipo',$arra_options_busq,$_POST['busq_tipo']," onchange=\"ajustar_rango()\"");
		echo $html->put_text('date',"Desde","Fecha desde",'fech_desd',$_POST['fech_desd'],'','100','');
		echo $html->put_text('date',"Hasta","Fecha desde",'fech_hast',$_POST['fech_hast'],'','100','');
	echo"</main>";

	// Incluir el archivo de clases necesarias
	require_once('classes/zkteco_biotime.php');

	/*******************************
	 * Paso 1: Autenticación       *
	 *******************************/

	$baseURL = 'http://10.4.100.9:8085/';
	$authURL = $baseURL . 'api-token-auth/';

	// Realizar la autenticación y obtener el token
	$authResponse = API::Authentication($authURL, 'mpfnarequipa', 'mpfnarequipa123');
	$authArray = API::JSON_TO_ARRAY($authResponse);
	$authToken = $authArray['token'];

	// Mostrar respuesta de autenticación y token obtenido
	//echo "<h2>Autenticación</h2>";
	//echo "<pre>";
	//echo "Respuesta de autenticación: " . htmlspecialchars($authResponse) . "\n";
	//echo "Token obtenido: " . htmlspecialchars($authToken) . "\n";
	//echo "</pre>";
	//echo "<hr>";

	/*******************************
	 * Paso 2: Acceso a la API      *
	 *******************************/

	// Definir la URL de la API a la que se desea acceder con el token
	$dataURL = '';

	// Verificar si se ha enviado un valor para emp_code
	if (isset($_POST['ndoc_trab']))
	{
		// Obtener el valor de emp_code desde el formulario o la URL
		$empCode = $_POST['ndoc_trab'];
		$start_time = $_POST['fech_desd'];
		$end_time = $_POST['fech_hast'];
		$end_time = date("Y-m-d", strtotime($end_time. ' + 1 days'));

		// Construir la URL de la API con el valor de emp_code dinámico
		//$dataURL = 'http://10.4.100.9:8085/iclock/api/transactions/?emp_code=' . urlencode($empCode). '&page_size=900';
		$dataURL = 'http://10.4.100.9:8085/iclock/api/transactions/?emp_code=' . urlencode($empCode). '&start_time='.$start_time.'&end_time='.$end_time.'&page_size=900';
		//echo"<HR>$dataURL<HR>";

		// Concatenar el parámetro page=2 a la URL existente
		// $dataURL .= '&page_zise=900';

		// Realizar la solicitud GET a la API utilizando el token obtenido
		$dataResponse = API::GET($dataURL, $authToken);
		$dataArray = API::JSON_TO_ARRAY($dataResponse);

		// Mostrar la respuesta de la API
		//echo "<h2>Consulta de datos</h2>";
		//echo "<pre>";
		//echo "Respuesta de la API: " . htmlspecialchars($dataResponse) . "\n";
		//echo "</pre>";
		//echo "<hr>";

		// Mostrar los datos obtenidos de la API
		//echo "<h2>Datos obtenidos</h2>";
		//echo "<pre>";
		//print_r($dataArray);
		//echo "</pre>";

		echo"<div style=\"width:90%;max-width:800px;margin:auto;\">";
		echo $html->put_title_demand("MARCACIONES ENCONTRADAS");
		$head=['1'=>"Nº",'2'=>"SEDE",'3'=>"FECHA",'4'=>"INGRESO",'5'=>"SALIDA"];
		echo $html->put_table_responsive_open();
		echo $html->put_table_responsive_header($head);

		$cont=0;
		foreach($dataArray['data'] as $rows)
		{
			$area=$rows['area_alias'];
			$fech=substr($rows['punch_time'],0,10);
			$hora=substr($rows['punch_time'],11);
			$arra_asis[$fech][$hora]=$area;
		}
		//print_r($arra_asis);
		foreach($arra_asis as $fech => $arra)
		{
			//print_r($fecha);
			//print_r($rows);
			//echo"<HR>$fech - $hora<HR>";
			$min='24:00:00';
			$max='00:00:00';
			foreach($arra as $hora => $sede)
			{
				//echo"$fech - $hora - $sede<HR>";
				if($hora<$min)
					$min=$hora;
				if($hora>$max)
					$max=$hora;
			}
			if($min==$max)
			{
				if($min<'12:00:00')
					$max='--:--:--';
				else
					$min='--:--:--';
			}
			//echo"$fech - $min - $max - $sede<HR>";
			$cont++;
			$data=[	'1'=>$cont,
				'2'=>$sede,
				'3'=>$fech,
				'4'=>$min,
				'5'=>$max,
			];
			echo $html->put_table_responsive_data($head,$data);
		}
		/*
		foreach($arra_asis as $rows)
		{
			if($fech!=$flag)
			$cont++;
			$data=[	'1'=>$cont,
				'2'=>$rows['area_alias'],
				'3'=>substr($rows['ingr'],0,10),
				'4'=>substr($rows['ingr'],11),
				'5'=>substr($rows['sali'],11),
			];
			echo $html->put_table_responsive_data($head,$data);
		}
		if($cont==0)
			echo $html->put_table_responsive_title("No Existen Marcaciones");
		 */
		echo $html->put_table_responsive_close();
		echo"</div>";
	}


/*
	$busc_item_pagi=50;      //cantidad de items por pagina

	$result=$Db->query("select SUBSTRING(punch_time,1,10) xx,min(punch_time),max(punch_time),area_alias from mp_asis_marcaciones where emp_code=".$_GET['logi_oper']." group by xx");
	$busc_tota_item=0;
	foreach($result as $rows)
	{       
		$busc_tota_item++;
	}
	$busc_tota_pagi=ceil($busc_tota_item/$busc_item_pagi);
	$busc_limi_pagi=($_POST['busc_pagi_actu']-1)*$busc_item_pagi;

	$result_pagi=$Db->query("select SUBSTRING(punch_time,1,10) fech,min(punch_time) ingr,max(punch_time) sali,area_alias from mp_asis_marcaciones where emp_code=".$_GET['logi_oper']." group by fech ORDER BY fech desc limit $busc_limi_pagi,$busc_item_pagi");
	echo"<div style=\"width:90%;max-width:800px;margin:auto;\">";
	echo $html->put_title_demand("RESULTADOS DE B&Uacute;SQUEDA: $busc_tota_item ENCONTRADOS");
	if($busc_tota_pagi>0)
		echo $html->put_page("1",$_POST['busc_pagi_actu'],$busc_tota_pagi,"");
	$head=['1'=>"Nº",'2'=>"SEDE",'3'=>"FECHA",'4'=>"INGRESO",'5'=>"SALIDA"];
	echo $html->put_table_responsive_open();
	if($busc_tota_item)
	{
		echo $html->put_table_responsive_header($head);
		$cont=$busc_limi_pagi;
		foreach($result_pagi as $rows)
		{
			$cont++;
			$data=[	'1'=>$cont,
				'2'=>$rows['area_alias'],
				'3'=>substr($rows['ingr'],0,10),
				'4'=>substr($rows['ingr'],11),
				'5'=>substr($rows['sali'],11),
			];
			echo $html->put_table_responsive_data($head,$data);
		}
	}
	else
		echo $html->put_table_responsive_title("No Existe Jurisprudencia");
	echo $html->put_table_responsive_close();
	if($busc_tota_pagi>0)
		echo $html->put_page("2",$_POST['busc_pagi_actu'],$busc_tota_pagi,"");
	echo"</div>";
*/
	//if($busc_tota_item>0)
	//{
	/*
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
	*/
?>
<center>
	</form>
	</body>
</html>
