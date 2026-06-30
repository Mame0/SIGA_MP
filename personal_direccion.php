<?
	require_once 'include/cabecera.php';
	require_once 'classes/Html.class.php';
	require_once 'classes/Db.class.php';
	$Db = new Db();
	
	require_once 'include/registrar_acceso.php';

	// Inicializar flag_admi para evitar warnings y simplificar la lógica
	$_POST['flag_admi'] = $_POST['flag_admi'] ?? $_GET['flag_admi'] ?? 0;

	$fdig=date("YmdHis");
	
	if(isset($_POST['iden_pers_edit']) && $_POST['iden_pers_edit'])
	{
		unset($_POST['iden_pers']);
		$_SESSION['iden_pers_edit']=$_POST['iden_pers_edit'];
	}
	
	if(!isset($_POST['iden_pers']))
	{
		if($_POST['flag_admi'] == 1) //si es administrador
		{
			if(isset($_SESSION['iden_pers_edit']) && $_SESSION['iden_pers_edit'])
				$_POST['iden_pers']=$_SESSION['iden_pers_edit'];
			else
			{
				echo"
					<html><body>
					<form name=\"form\" method=post action=\"personal_buscar.php\">
						<input type=hidden name=\"iden_pers\" value=\"".htmlspecialchars($_POST['iden_pers'] ?? '')."\">
						<input type=hidden name=\"flag_admi\" value=\"".$_POST['flag_admi']."\">
						<input type=hidden name=\"dire_orig\" value=\"personal_direccion.php\">
					</form>
					<script>
						document.form.submit();
					</script>
					</body></html>
				";
				exit;
			}
		}
		else
		{
			$result=$Db->query("select * from mp_admi_pers where ndoc_pers='$_SESSION[ndoc_oper]'");
			foreach($result as $rows)
				$_POST['iden_pers']=$rows['iden_pers'];
		}
	}

	if(!empty($_POST['guardar_personal']))
	{
		$fdig=date('YmdHis');
		//$_POST['esta_pers']=1;
		$data = [
			'domi_pers' => $_POST['iden_dist'],
			'iden_tvia' => $_POST['iden_tvia'],
			'dnro_pers' => $_POST['dnro_pers'],
			'dire_pers' => $_POST['dire_pers'],
			'dint_pers' => $_POST['dint_pers'],
			'dpis_pers' => $_POST['dpis_pers'],
			'dlot_pers' => $_POST['dlot_pers'],
			'dman_pers' => $_POST['dman_pers'],
			'dref_pers' => $_POST['dref_pers'],
			'iden_tdom' => $_POST['iden_tdom'],
			// Información de contacto
			'cper_pers' => $_POST['cper_pers'],
			'cins_pers' => $_POST['cins_pers'],
			'eper_pers' => $_POST['eper_pers'],
			'eins_pers' => $_POST['eins_pers'],
		];
		if($_POST['iden_pers'])
		{
			$result=$Db->update('mp_admi_pers', $data, ['iden_pers'=>$_POST['iden_pers']]);
		}
		else
		{
			$data_insert = array_merge($data, [
				'ndoc_pers'=>$_POST['ndoc_pers'],
				'appa_pers'=>$_POST['appa_pers'],
				'apma_pers'=>$_POST['apma_pers'],
				'nomb_pers'=>$_POST['nomb_pers'],
				'codi_depe'=>$_POST['codi_depe'],
				'codi_carg'=>$_POST['codi_carg'],
				'regi_labo'=>$_POST['regi_labo'],
				'fech_ingr'=>$_POST['fech_ingr'],
				'digi_pers'=>$_POST['digi_pers'],
				'esta_pers'=>$_POST['esta_pers'],
			]);
			$result=$Db->insert('mp_admi_pers', $data_insert);
			$_POST['iden_pers']=$Db->lastInsertId();
		}

		echo"
			<html><body>
								<form name=\"form\" method=post action=\"personal_direccion.php\">
					<input type=hidden name=\"iden_pers\" value=\"".($_POST['iden_pers'] ?? '')."\">
					<input type=hidden name=\"busq_tipo\" value=\"".($_POST['busq_tipo'] ?? '')."\">
					<input type=hidden name=\"busq_dato\" value=\"".($_POST['busq_dato'] ?? '')."\">
					<input type=hidden name=\"busq_pagi_actu\" value=\"".($_POST['busq_pagi_actu'] ?? '')."\">
					<input type=hidden name=\"codi_form\" value=\"".($_POST['codi_form'] ?? '')."\">
					<input type=hidden name=\"flag_admi\" value=\"".($_POST['flag_admi'] ?? '')."\">
					<input type=hidden name=\"dire_orig\" value=\"personal_direccion.php\">
								</form>
								<script>
										document.form.submit();
								</script>
						</body></html>
		";

	}
	$result_personal=$Db->select('mp_admi_pers', ['iden_pers'=>$_POST['iden_pers']], '', '', '');
	$_POST['appa_pers']=$result_personal[0]['appa_pers'];
	$_POST['apma_pers']=$result_personal[0]['apma_pers'];
	$_POST['nomb_pers']=$result_personal[0]['nomb_pers'];
	$_POST['domi_pers']=$result_personal[0]['domi_pers'];
	$_POST['iden_tvia']=$result_personal[0]['iden_tvia'];
	$_POST['dnro_pers']=$result_personal[0]['dnro_pers'];
	$_POST['dire_pers']=$result_personal[0]['dire_pers'];
	$_POST['dint_pers']=$result_personal[0]['dint_pers'];
	$_POST['dpis_pers']=$result_personal[0]['dpis_pers'];
	$_POST['dlot_pers']=$result_personal[0]['dlot_pers'];
	$_POST['dman_pers']=$result_personal[0]['dman_pers'];
	$_POST['dref_pers']=$result_personal[0]['dref_pers'];
	$_POST['iden_tdom']=$result_personal[0]['iden_tdom'];	
	$_POST['iden_dpto']=substr($_POST['domi_pers'],0,2);
	$_POST['iden_prov']=substr($_POST['domi_pers'],0,4);
	$_POST['iden_dist']=substr($_POST['domi_pers'],0,6);
	$_POST['cper_pers']=$result_personal[0]['cper_pers'];
	$_POST['cins_pers']=$result_personal[0]['cins_pers'];
	$_POST['eper_pers']=$result_personal[0]['eper_pers'];
	$_POST['eins_pers']=$result_personal[0]['eins_pers'];
	if(!$_POST['iden_dpto'])
		$_POST['iden_dpto']='04';
	if(!$_POST['iden_prov'])
		$_POST['iden_prov']='0401';
?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8" />
		<title></title>
		<link rel="stylesheet" href="css/forms_demanda.css" />
		<link rel="stylesheet" href="css/forms_foot.css" />
		<link rel="stylesheet" href="css/forms_column.css" />
		<SCRIPT language="JavaScript" src="js/cadenas.js"></SCRIPT>
		<SCRIPT language="JavaScript" src="js/denuncia.js"></SCRIPT>
		
		<script type="text/javascript">
			$(document).ready(function(){
				let ubigeoData = [];
				const dptoSelect = $('#iden_dpto');
				const provSelect = $('#iden_prov');
				const distSelect = $('#iden_dist');
				
				const dptoGuardado = '<?=$_POST['iden_dpto']?>';
				const provGuardada = '<?=$_POST['iden_prov']?>';
				const distGuardado = '<?=$_POST['iden_dist']?>';

				// 1. Cargar toda la data de Ubigeo una sola vez al inicio
				$.getJSON('personal_ubigeo.php?Accion=GetTodoElUbigeo', function(data) {
					ubigeoData = data;
					cargarDepartamentos();
				});

				function cargarDepartamentos() {
					let departamentos = [...new Map(ubigeoData.map(item => [item['cdep'], item])).values()];
					dptoSelect.empty().append(new Option("<- Departamento ->", ""));
					departamentos.forEach(dpto => {
						dptoSelect.append(new Option(dpto.depa, dpto.cdep));
					});
					if (dptoGuardado) {
						dptoSelect.val(dptoGuardado).trigger('change');
					}
				}

				dptoSelect.on('change', function() {
					const selectedDpto = $(this).val();
					let provincias = ubigeoData.filter(item => item.cdep === selectedDpto);
					let provinciasUnicas = [...new Map(provincias.map(item => [item['cpro'], item])).values()];
					
					provSelect.empty().append(new Option("<- Provincia ->", ""));
					distSelect.empty().append(new Option("<- Distrito ->", ""));
					
					provinciasUnicas.forEach(prov => {
						provSelect.append(new Option(prov.prov, prov.cdep + prov.cpro));
					});
					if (provGuardada) {
						provSelect.val(provGuardada).trigger('change');
					}
				});

				provSelect.on('change', function() {
					const selectedProv = $(this).val();
					let distritos = ubigeoData.filter(item => (item.cdep + item.cpro) === selectedProv);

					distSelect.empty().append(new Option("<- Distrito ->", ""));
					
					distritos.forEach(dist => {
						distSelect.append(new Option(dist.dist, dist.cdep + dist.cpro + dist.cdis));
					});
					if (distGuardado) {
						distSelect.val(distGuardado);
					}
				});
			});
		</script>
		
		<script>
			function f_guardar_personal()
			{
                // ** NUEVA VALIDACIÓN **
                if (!document.getElementById('commitment_checkbox').checked) {
                    alert('Por favor, debe aceptar el compromiso de actualización de datos para poder guardar.');
                    return false;
                }

			   // Validación de campos obligatorios
			   if(document.form.iden_dpto.value=='') {
				   alert('Seleccione Departamento');
				   document.form.iden_dpto.focus();
				   return false;
			   }
			   if(document.form.iden_prov.value=='') {
				   alert('Seleccione Provincia');
				   document.form.iden_prov.focus();
				   return false;
			   }
			   if(document.form.iden_dist.value=='') {
				   alert('Seleccione Distrito');
				   document.form.iden_dist.focus();
				   return false;
			   }
			   if(document.form.dire_pers.value=='') {
				   alert('Ingrese Dirección');
				   document.form.dire_pers.focus();
				   return false;
			   }
			   if(document.form.iden_tdom.value=='') {
				   alert('Seleccione Tipo de Domicilio');
				   document.form.iden_tdom.focus();
				   return false;
			   }
			   if(document.form.cper_pers.value=='') {
				   alert('Ingrese Celular Personal');
				   document.form.cper_pers.focus();
				   return false;
			   }
			   if(document.form.eper_pers.value=='') {
				   alert('Ingrese Correo Personal');
				   document.form.eper_pers.focus();
				   return false;
			   }
			   if(confirm('Seguro que desea Guardar')) {
				   document.form.guardar_personal.value='1';
				   document.form.submit();
			   } else {
				   return false;
			   }
			}
			function f_cancelar_documento()
			{
				document.form.action='personal_buscar.php';
				document.form.submit();
			}
			function ajustar_altura()
						{
								parent.document.getElementById('body_iframe').height=parent.window.innerHeight-80;
						}
						ajustar_altura();
		</script>
	</head>
	<body style="margin-bottom: 30px;">
	<center><h4 style="color:#073a6b"><b>
<?
	if($_POST['iden_pers'])
		echo"Datos de Domicilio<BR>".$_POST['appa_pers']." ".$_POST['apma_pers'].", ".$_POST['nomb_pers'];
	else
		echo"Crear Nuevo Personal";
?>
	</h4></b></center>
		<form name="form" method="post" ENCTYPE="multipart/form-data">
			<input type=hidden name="guardar_personal">
			<input type=hidden name="iden_pers" value="<?=$_POST['iden_pers']?>">
			<input type=hidden name="flag_admi" value="<?=$_POST['flag_admi']?>">
			<input type=hidden name="dire_orig" value="personal_direccion.php">
			<main>
<?
	$html=new htmlclass;

	$arra_options_tdom=$Db->get_options('mp_maes_tipo_domicilio',1,0);
	$arra_options_tvia=$Db->get_options('mp_maes_tipo_via',1,0);
	$arra_options_piso=array(0=>'<- Seleccione Piso ->',1=>1,2=>2,3=>3,4=>4,5=>5,6=>6,7=>7,8=>8,9=>9,10=>10,11=>11,12=>12,13=>13,14=>14,15=>15,16=>16,17=>17,18=>18,19=>19,20=>20);
	$arra_options_manz=array(0=>'<- Seleccione Manzana ->','A'=>'A','B'=>'B','C'=>'C','D'=>'D','E'=>'E','F'=>'F','G'=>'G','H'=>'H');
	
	$arra_options_dpto[0]="<- Departamento ->";
	$result=$Db->query("select distinct cdep,depa from mp_admi_ubig_reni order by depa");
	foreach($result as $rows)
		$arra_options_dpto[$rows['cdep']]=utf8_encode(utf8_decode($rows['depa']));

   // Se inicializan vacíos porque se cargan dinámicamente con JavaScript
   $arra_options_dptox = [];
   $arra_options_prov = [];
   $arra_options_dist = [];
   echo $html->put_select("Departamento(*)",'iden_dpto',$arra_options_dptox,$_POST['iden_dpto'],"");
   echo $html->put_select("Provincia(*)",'iden_prov',$arra_options_prov,$_POST['iden_prov'],"");
   echo $html->put_select("Distrito(*)",'iden_dist',$arra_options_dist,$_POST['iden_dist'],"");
   echo"</main><main>";
   echo $html->put_select("Vía",'iden_tvia',$arra_options_tvia,$_POST['iden_tvia'],"");
   echo $html->put_text('text',"Nro","Ingrese Nro.",'dnro_pers',$_POST['dnro_pers'],'','100','');
   echo $html->put_text('text',"Dirección(*)","Ingrese direccióm",'dire_pers',$_POST['dire_pers'],'','100','');
   echo"</main><main>";
   echo $html->put_text('text',"Block/Departamento/Interior","Ingrese Nro.",'dint_pers',$_POST['dint_pers'],'','100','');
   echo $html->put_select("Nro.&nbsp;Piso",'dpis_pers',$arra_options_piso,$_POST['dpis_pers'],"");
   echo $html->put_text('text',"Lote","Ingrese Lote",'dlot_pers',$_POST['dlot_pers'],'','10','');
   echo"</main><main>";
   echo $html->put_select("Manzana",'dman_pers',$arra_options_manz,$_POST['dman_pers'],"");
   echo $html->put_text('text',"Referencia","Ingrese Referencia",'dref_pers',$_POST['dref_pers'],'','100','');
   echo $html->put_select("Tipo",'iden_tdom',$arra_options_tdom,$_POST['iden_tdom'],"");
   
   echo $html->put_title_demand("Información de Contacto");
   echo $html->put_text('text',"Celular&nbsp;Personal(*)","Ingrese Celular Personal",'cper_pers',$_POST['cper_pers'],'','20','');
   echo $html->put_text('text',"Celular&nbsp;Institucional","Ingrese Celular Institucional",'cins_pers',$_POST['cins_pers'],'','20','');
   echo"</main><main>";
   echo $html->put_text('text',"Correo&nbsp;Personal(*)","Ingrese Correo Personal",'eper_pers',$_POST['eper_pers'],'','50','');
   echo $html->put_text('text',"Correo&nbsp;Institucional","Ingrese Correo Institucional",'eins_pers',$_POST['eins_pers'],'','50','');
   echo"</main>";
	

	echo $html->put_separator_demand("30");

    // MENSAJE DE COMPROMISO Y PRIVACIDAD CON CHECKBOX
    echo '
        <div style="max-width: 700px; margin: 20px auto; padding: 15px; background-color: #f7f7f7; border-radius: 5px; border-top: 1px solid #ddd;">
            <input type="checkbox" id="commitment_checkbox" style="vertical-align: top; margin-top: 4px; margin-right: 10px;">
            <label for="commitment_checkbox" style="display: inline-block; width: 90%; font-size: 0.85em; color: #444; text-align: justify;">
                <b>Me comprometo a presentar la información actualizada a mi legajo y a reportar cualquier cambio de manera oportuna.</b>
                <br><br>
                <small>(Este sistema cumple con las directrices establecidas en la Ley de Protección de Datos Personales N° 29733 y su reglamento. Implementamos medidas técnicas y organizativas para evitar la alteración, pérdida, tratamiento o acceso no autorizado de su información.)</small>
            </label>
        </div>
    ';

	if($_POST['flag_admi']==1) //si es administrador
	{
				echo"
						<div align=center class=\"foot\">
								<center>
								<div align=center class=\"foot2\">
										<div class=\"div_button_foot\" style=\"\">
												<button class=\"button_foot\" onclick=\"f_cancelar_documento()\">&laquo; Nueva B&uacute;squeda</button>
										</div>
										<div class=\"div_button_foot\"><center>
												<button id=\"submitBtn\" class=\"button_foot\" onclick=\"return f_guardar_personal()\">Guardar &raquo;</button>
										</div>
								</div>
						</div>
				";
	}
	else
	{
				echo"
						<div align=center class=\"foot\">
								<center>
								<div align=center class=\"foot2\">
										<div class=\"div_button_foot\" style=\"\">
												<button class=\"button_foot\" onclick=\"reset()\">&laquo; Cancelar</button>
										</div>
										<div class=\"div_button_foot\"><center>
												<button id=\"submitBtn\" class=\"button_foot\" onclick=\"return f_guardar_personal()\">Guardar &raquo;</button>
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