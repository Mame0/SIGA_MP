<?
	require_once 'include/cabecera.php';
	require_once 'classes/Html.class.php';
	require_once 'classes/Db.class.php';
	$Db = new Db();

	$fdig=date("YmdHis");

	if($_POST['guardar_personal'])
	{
		$fdig=date(YmdHis);
		if($_POST['codi_pers'])
		{
			$result=$Db->update('mp_personal',
			['pers_apepat'=>$_POST['pers_apepat'],'pers_apemat'=>$_POST['pers_apemat'],'pers_nombres'=> $_POST['pers_nombres'] ,
			'pers_fecnac'=>$_POST['pers_fecnac'],'pers_estciv'=>$_POST['pers_estciv'],'pers_dni'=>$_POST['pers_dni'],
			'pers_lugarnac'=>$_POST['pers_lugarnac'], 'pers_dire'=>$_POST['pers_dire'],'pers_distr'=>$_POST['pers_distr'],
			'pers_refedir'=>$_POST['pers_refedir'], 'pers_tlffijo'=>$_POST['pers_tlffijo'],'pers_celu'=>$_POST['pers_celu'],
			'pers_emailper'=>$_POST['pers_emailper'], 'pers_emailinst'=>$_POST['pers_emailinst'],
			'pers_nomape_per1'=>$_POST['pers_nomape_per1'],'pers_nrocel_per1'=>$_POST['pers_nrocel_per1'],
			'pers_nomape_per2'=>$_POST['pers_nomape_per2'],'pers_nrocel_per2'=>$_POST['pers_nrocel_per2'],
			'pers_grains'=>$_POST['pers_grains'],'pers_prof1'=>$_POST['pers_prof1'],'pers_prof2'=>$_POST['pers_prof2'],
			'pers_nrocole'=>$_POST['pers_nrocole'],'pers_fecing'=>$_POST['pers_fecing'],'pers_cargo'=>$_POST['pers_cargo'],

			'codcargopea'=>$_POST['codcargopea'],'meta'=>$_POST['meta'],'asignacionfamiliar'=>$_POST['asignacionfamiliar'],
			'eps'=>$_POST['eps'],'activo'=>$_POST['activo'],
			'clas_haberes'=>$_POST['clas_haberes'],'clas_benefextra'=>$_POST['clas_benefextra'],'clas_bonofiscal'=>$_POST['clas_bonofiscal'],
			'clas_go'=>$_POST['clas_go'],'clas_25retardo'=>$_POST['clas_25retardo'],'clas_aguinaldo'=>$_POST['clas_aguinaldo'],
			'clas_cafae'=>$_POST['clas_cafae'],'clas_escolaridad'=>$_POST['clas_escolaridad'],'clas_essalud9porc'=>$_POST['clas_essalud9porc'],
			'clas_eps225'=>$_POST['clas_eps225'],'clas_fondopens6porc'=>$_POST['clas_fondopens6porc'],'clas_grati9porc'=>$_POST['clas_grati9porc'],

			'pers_depe'=>$_POST['pers_depe'],'pers_reglab'=>$_POST['pers_reglab'],'pers_plapres'=>$_POST['pers_plapres'],
			'pers_conyuge'=>$_POST['pers_conyuge'],
			'pers_hijo1'=>$_POST['pers_hijo1'],'pers_fechijo1'=>$_POST['pers_fechijo1'],'pers_sexohijo1'=>$_POST['pers_sexohijo1'],
			'pers_hijo2'=>$_POST['pers_hijo2'],'pers_fechijo2'=>$_POST['pers_fechijo2'],'pers_sexohijo2'=>$_POST['pers_sexohijo2'],
			'pers_hijo3'=>$_POST['pers_hijo3'],'pers_fechijo3'=>$_POST['pers_fechijo3'],'pers_sexohijo3'=>$_POST['pers_sexohijo3'],
			'pers_hijo4'=>$_POST['pers_hijo4'],'pers_fechijo4'=>$_POST['pers_fechijo4'],'pers_sexohijo4'=>$_POST['pers_sexohijo4'],
			'pers_hijo5'=>$_POST['pers_hijo5'],'pers_fechijo5'=>$_POST['pers_fechijo5'],'pers_sexohijo5'=>$_POST['pers_sexohijo5'],
			'pers_padre'=>$_POST['pers_padre'],'pers_padredir'=>$_POST['pers_padredir'],
			'pers_madre'=>$_POST['pers_madre'],'pers_madredir'=>$_POST['pers_madredir'],
			'pers_essalud'=>$_POST['pers_essalud'],'pers_centroate'=>$_POST['pers_centroate'],'pers_eps'=>$_POST['pers_eps'],
			'pers_tpsangre'=>$_POST['pers_tpsangre'],'pers_alergenf'=>$_POST['pers_alergenf'],'pers_discap'=>$_POST['pers_discap'],
			'pers_conadis'=>$_POST['pers_conadis'],'pers_otroidi'=>$_POST['pers_otroidi'],
			'pers_hobfut'=>$_POST['pers_hobfut'],'pers_hobbas'=>$_POST['pers_hobbas'],'pers_hobnat'=>$_POST['pers_hobnat'],
			'pers_hobpin'=>$_POST['pers_hobpin'],'pers_hobfro'=>$_POST['pers_hobfro'],'pers_hobbai'=>$_POST['pers_hobbai'],
			'pers_hobcoc'=>$_POST['pers_hobcoc'],'pers_otrahab'=>$_POST['pers_otrahab'] ]  , ['codi_pers'=>$_POST['codi_pers']]  );
		}
		else
		{
			$result=$Db->insert('mp_personal',
			['pers_apepat'=>$_POST['pers_apepat'],'pers_apemat'=>$_POST['pers_apemat'],'pers_nombres'=> $_POST['pers_nombres'] ,
			'pers_fecnac'=>$_POST['pers_fecnac'],'pers_estciv'=>$_POST['pers_estciv'],'pers_dni'=>$_POST['pers_dni'],
			'pers_lugarnac'=>$_POST['pers_lugarnac'], 'pers_dire'=>$_POST['pers_dire'],'pers_distr'=>$_POST['pers_distr'],
			'pers_refedir'=>$_POST['pers_refedir'], 'pers_tlffijo'=>$_POST['pers_tlffijo'],'pers_celu'=>$_POST['pers_celu'],
			'pers_emailper'=>$_POST['pers_emailper'], 'pers_emailinst'=>$_POST['pers_emailinst'],
			'pers_nomape_per1'=>$_POST['pers_nomape_per1'],'pers_nrocel_per1'=>$_POST['pers_nrocel_per1'],
			'pers_nomape_per2'=>$_POST['pers_nomape_per2'],'pers_nrocel_per2'=>$_POST['pers_nrocel_per2'],
			'pers_grains'=>$_POST['pers_grains'],'pers_prof1'=>$_POST['pers_prof1'],'pers_prof2'=>$_POST['pers_prof2'],
			'pers_nrocole'=>$_POST['pers_nrocole'],'pers_fecing'=>$_POST['pers_fecing'],'pers_cargo'=>$_POST['pers_cargo'],

			'codcargopea'=>$_POST['codcargopea'],'meta'=>$_POST['meta'],'asignacionfamiliar'=>$_POST['asignacionfamiliar'],
			'eps'=>$_POST['eps'],'activo'=>$_POST['activo'],
			'clas_haberes'=>$_POST['clas_haberes'],'clas_benefextra'=>$_POST['clas_benefextra'],'clas_bonofiscal'=>$_POST['clas_bonofiscal'],
			'clas_go'=>$_POST['clas_go'],'clas_25retardo'=>$_POST['clas_25retardo'],'clas_aguinaldo'=>$_POST['clas_aguinaldo'],
			'clas_cafae'=>$_POST['clas_cafae'],'clas_escolaridad'=>$_POST['clas_escolaridad'],'clas_essalud9porc'=>$_POST['clas_essalud9porc'],
			'clas_eps225'=>$_POST['clas_eps225'],'clas_fondopens6porc'=>$_POST['clas_fondopens6porc'],'clas_grati9porc'=>$_POST['clas_grati9porc'],

			'pers_depe'=>$_POST['pers_depe'],'pers_reglab'=>$_POST['pers_reglab'],'pers_plapres'=>$_POST['pers_plapres'],
			'pers_conyuge'=>$_POST['pers_conyuge'],
			'pers_hijo1'=>$_POST['pers_hijo1'],'pers_fechijo1'=>$_POST['pers_fechijo1'],'pers_sexohijo1'=>$_POST['pers_sexohijo1'],
			'pers_hijo2'=>$_POST['pers_hijo2'],'pers_fechijo2'=>$_POST['pers_fechijo2'],'pers_sexohijo2'=>$_POST['pers_sexohijo2'],
			'pers_hijo3'=>$_POST['pers_hijo3'],'pers_fechijo3'=>$_POST['pers_fechijo3'],'pers_sexohijo3'=>$_POST['pers_sexohijo3'],
			'pers_hijo4'=>$_POST['pers_hijo4'],'pers_fechijo4'=>$_POST['pers_fechijo4'],'pers_sexohijo4'=>$_POST['pers_sexohijo4'],
			'pers_hijo5'=>$_POST['pers_hijo5'],'pers_fechijo5'=>$_POST['pers_fechijo5'],'pers_sexohijo5'=>$_POST['pers_sexohijo5'],
			'pers_padre'=>$_POST['pers_padre'],'pers_padredir'=>$_POST['pers_padredir'],
			'pers_madre'=>$_POST['pers_madre'],'pers_madredir'=>$_POST['pers_madredir'],
			'pers_essalud'=>$_POST['pers_essalud'],'pers_centroate'=>$_POST['pers_centroate'],'pers_eps'=>$_POST['pers_eps'],
			'pers_tpsangre'=>$_POST['pers_tpsangre'],'pers_alergenf'=>$_POST['pers_alergenf'],'pers_discap'=>$_POST['pers_discap'],
			'pers_conadis'=>$_POST['pers_conadis'],'pers_otroidi'=>$_POST['pers_otroidi'],
			'pers_hobfut'=>$_POST['pers_hobfut'],'pers_hobbas'=>$_POST['pers_hobbas'],'pers_hobnat'=>$_POST['pers_hobnat'],
			'pers_hobpin'=>$_POST['pers_hobpin'],'pers_hobfro'=>$_POST['pers_hobfro'],'pers_hobbai'=>$_POST['pers_hobbai'],
			'pers_hobcoc'=>$_POST['pers_hobcoc'],'pers_otrahab'=>$_POST['pers_otrahab'] ]);
			$_POST['codi_pers']=$Db->lastInsertId();
		}
/*		if($_FILES['file_docu']['name'] AND $_FILES['file_docu']['size']>0)
		{
			if(strstr($_FILES['file_docu']['type'],"pdf") OR strstr($_FILES['file_docu']['type'],"pdf"))
			{
				//subir_archivo('logo',$_FILES['file_docu']['tmp_name'],"pers_".str_pad($_POST['codi_pers'], 6, "0", STR_PAD_LEFT).".pdf","");
				move_uploaded_file($_FILES['file_docu']['tmp_name'],"ftp/docu_".str_pad($_POST['codi_bien'], 6, "0", STR_PAD_LEFT).".pdf");
			}
			else
				echo"<script>alert('ERROR: Archivo no es un PDF');</script>";
		}
*/
		unset($_POST['file_docu']);

		echo"
			<!--<script>alert('".CONST_MENS_REG_OK."');</script>-->
                        <html><body>
                                <form name=\"form\" method=post action=\"datpersonal.php\">
					<input type=hidden name=\"busq_tipo\" value=\"".$_POST['busq_tipo']."\">
					<input type=hidden name=\"busq_dato\" value=\"".$_POST['busq_dato']."\">
					<input type=hidden name=\"busq_pagi_actu\" value=\"".$_POST['busq_pagi_actu']."\">
                                </form>
                                <script>
                                        document.form.submit();
                                </script>
                        </body></html>
		";

	}
	$result_documento=$Db->select('mp_personal', ['codi_pers'=>$_POST['codi_pers']], '', '', '');

	$_POST['pers_apepat']= $result_documento[0]['pers_apepat'] ;
	$_POST['pers_apemat']= $result_documento[0]['pers_apemat'] ;
	$_POST['pers_nombres']= $result_documento[0]['pers_nombres'] ;
	$_POST['pers_fecnac']=$result_documento[0]['pers_fecnac'];
	$_POST['pers_estciv']=$result_documento[0]['pers_estciv'];
	$_POST['pers_dni']=$result_documento[0]['pers_dni'];
	$_POST['pers_lugarnac']= $result_documento[0]['pers_lugarnac'] ;
	$_POST['pers_dire']= $result_documento[0]['pers_dire'] ;
	$_POST['pers_distr']= $result_documento[0]['pers_distr'] ;
	$_POST['pers_refedir']= $result_documento[0]['pers_refedir'] ;
	$_POST['pers_tlffijo']=$result_documento[0]['pers_tlffijo'];
	$_POST['pers_celu']=$result_documento[0]['pers_celu'];
	$_POST['pers_emailper']= $result_documento[0]['pers_emailper'] ;
	$_POST['pers_emailinst']= $result_documento[0]['pers_emailinst'] ;
	$_POST['pers_nomape_per1']= $result_documento[0]['pers_nomape_per1'] ;
	$_POST['pers_nrocel_per1']=$result_documento[0]['pers_nrocel_per1'];
	$_POST['pers_nomape_per2']= $result_documento[0]['pers_nomape_per2'] ;
	$_POST['pers_nrocel_per2']=$result_documento[0]['pers_nrocel_per2'];
	$_POST['pers_grains']= $result_documento[0]['pers_grains'] ;
	$_POST['pers_prof1']= $result_documento[0]['pers_prof1'] ;
	$_POST['pers_prof2']= $result_documento[0]['pers_prof2'] ;
	$_POST['pers_nrocole']=$result_documento[0]['pers_nrocole'];
	$_POST['pers_fecing']=$result_documento[0]['pers_fecing'];
	$_POST['pers_cargo']= $result_documento[0]['pers_cargo'] ;
	$_POST['pers_depe']=$result_documento[0]['pers_depe'];
	$_POST['pers_reglab']=$result_documento[0]['pers_reglab'];
	$_POST['pers_plapres']=$result_documento[0]['pers_plapres'];
	$_POST['pers_conyuge']= $result_documento[0]['pers_conyuge'] ;
	$_POST['pers_hijo1']= $result_documento[0]['pers_hijo1'] ;
	$_POST['pers_fechijo1']=$result_documento[0]['pers_fechijo1'];
	$_POST['pers_sexohijo1']=$result_documento[0]['pers_sexohijo1'];
	$_POST['pers_hijo2']= $result_documento[0]['pers_hijo2'] ;
	$_POST['pers_fechijo2']=$result_documento[0]['pers_fechijo2'];
	$_POST['pers_sexohijo2']=$result_documento[0]['pers_sexohijo2'];
	$_POST['pers_hijo3']= $result_documento[0]['pers_hijo3'] ;
	$_POST['pers_fechijo3']=$result_documento[0]['pers_fechijo3'];
	$_POST['pers_sexohijo3']=$result_documento[0]['pers_sexohijo3'];
	$_POST['pers_hijo4']= $result_documento[0]['pers_hijo4'] ;
	$_POST['pers_fechijo4']=$result_documento[0]['pers_fechijo4'];
	$_POST['pers_sexohijo4']=$result_documento[0]['pers_sexohijo4'];
	$_POST['pers_hijo5']= $result_documento[0]['pers_hijo5'] ;
	$_POST['pers_fechijo5']=$result_documento[0]['pers_fechijo5'];
	$_POST['pers_sexohijo5']=$result_documento[0]['pers_sexohijo5'];
	$_POST['pers_padre']= $result_documento[0]['pers_padre'] ;
	$_POST['pers_padredir']= $result_documento[0]['pers_padredir'] ;
	$_POST['pers_madre']= $result_documento[0]['pers_madre'] ;
	$_POST['pers_madredir']= $result_documento[0]['pers_madredir'] ;
	$_POST['pers_essalud']=$result_documento[0]['pers_essalud'];
	$_POST['pers_centroate']= $result_documento[0]['pers_centroate'] ;
	$_POST['pers_eps']=$result_documento[0]['pers_eps'];
	$_POST['pers_tpsangre']=$result_documento[0]['pers_tpsangre'];
	$_POST['pers_alergenf']= $result_documento[0]['pers_alergenf'] ;
	$_POST['pers_discap']= $result_documento[0]['pers_discap'] ;
	$_POST['pers_conadis']=$result_documento[0]['pers_conadis'];
	$_POST['pers_otroidi']= $result_documento[0]['pers_otroidi'] ;
	$_POST['pers_hobfut']=$result_documento[0]['pers_hobfut'];
	$_POST['pers_hobbas']=$result_documento[0]['pers_hobbas'];
	$_POST['pers_hobnat']=$result_documento[0]['pers_hobnat'];
	$_POST['pers_hobpin']=$result_documento[0]['pers_hobpin'];
	$_POST['pers_hobfro']=$result_documento[0]['pers_hobfro'];
	$_POST['pers_hobbai']=$result_documento[0]['pers_hobbai'];
	$_POST['pers_hobcoc']=$result_documento[0]['pers_hobcoc'];
	$_POST['pers_otrahab']= $result_documento[0]['pers_otrahab'] ;

	$_POST['codcargopea']=$result_documento[0]['codcargopea'];
	$_POST['meta']=$result_documento[0]['meta'];
	$_POST['asignacionfamiliar']=$result_documento[0]['asignacionfamiliar'];
	$_POST['eps']=$result_documento[0]['eps'];
	$_POST['activo']=$result_documento[0]['activo'];
	$_POST['clas_haberes']=$result_documento[0]['clas_haberes'];
	$_POST['clas_benefextra']=$result_documento[0]['clas_benefextra'];
	$_POST['clas_bonofiscal']=$result_documento[0]['clas_bonofiscal'];
	$_POST['clas_go']=$result_documento[0]['clas_go'];
	$_POST['clas_25retardo']=$result_documento[0]['clas_25retardo'];
	$_POST['clas_aguinaldo']=$result_documento[0]['clas_aguinaldo'];
	$_POST['clas_cafae']=$result_documento[0]['clas_cafae'];
	$_POST['clas_escolaridad']=$result_documento[0]['clas_escolaridad'];
	$_POST['clas_essalud9porc']=$result_documento[0]['clas_essalud9porc'];
	$_POST['clas_eps225']=$result_documento[0]['clas_eps225'];
	$_POST['clas_fondopens6porc']=$result_documento[0]['clas_fondopens6porc'];
	$_POST['clas_grati9porc']=$result_documento[0]['clas_grati9porc'];



?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8" />
		<title>PERSONAL</title>
		<link rel="stylesheet" href="css/forms_demanda.css" />
		<link rel="stylesheet" href="css/forms_foot.css" />
		<link rel="stylesheet" href="css/forms_column.css" />
		<SCRIPT language="JavaScript" src="js/cadenas.js"></SCRIPT>
		<SCRIPT language="JavaScript" src="js/denuncia.js"></SCRIPT>
		<script>
			function f_guardar()
			{
//				if(document.form.codi_espe.selectedIndex=='0')
//				{
//					alert('Seleccione Especialidad');
//					document.form.codi_espe.focus();
//					return false;
//				}
//				else
//				{
					if(document.form.pers_apepat.value=='') {
						alert('Ingrese Apellido Paterno');
						document.form.pers_apepat.focus();
						return false;
					}
					if(document.form.pers_apemat.value=='') {
						alert('Ingrese Apellido Materno');
						document.form.pers_apemat.focus();
						return false;
					}
					if(document.form.pers_nombres.value=='') {
						alert('Ingrese Nombres');
						document.form.pers_nombres.focus();
						return false;
					}

							if(confirm('Seguro que desea Guardar')) {
								document.form.guardar_personal.value='1';
								document.form.submit();
							} else {
								return false;
							}

//				}
			}
			function f_cancelar()
			{
				document.form.action='datpersonal.php';
				document.form.submit();
			}
			function ajustar_altura()
                        {
                                //parent.document.getElementById('body_iframe').height=parent.window.innerHeight-80;
                        }
                        ajustar_altura();
		</script>
	</head>
	<body style="margin-bottom: 30px;">
	<center><h2 style="color:#073A6B">
<?
	if($_POST['codi_pers'])
		//echo"Editar Informaci&oacute;n Exp. ".$_POST['expe_docu'];
		echo"Editar Informaci&oacute;n - Datos Personales "; //.$_POST['nume_regi'];
	else
		echo"Registrar Nuevos Datos Personales";
?>
	</h2></center>
		<form name="form" method="post" ENCTYPE="multipart/form-data">
			<input type=hidden name="guardar_personal">
			<input type=hidden name="codi_pers" value="<?=$_POST['codi_pers']?>">

			<input type=hidden name="busq_pagi_actu" value="<?=$_POST['busq_pagi_actu']?>">

<?
	$html=new htmlclass;

/*
	echo "<main style='column-count:2;'>";
	echo $html->put_text('text',"Nro.&nbsp;Registro&nbsp;Alm.","Ingrese Nro. Registro",'nume_regi',$_POST['nume_regi'],'','16','');
	echo $html->put_text('text',"A&ntilde;o","",'anno_regi',$_POST['anno_regi'],'','4','style="max-width:100px;"');
	echo "</main>";
*/
	echo "<main style='column-count:3;'>";
	echo $html->put_title_demand("DATOS PERSONALES");

	echo $html->put_text('text',"Apellido&nbsp;Paterno","Ingrese A.Paterno",'pers_apepat',$_POST['pers_apepat'],'','30','');
	echo $html->put_text('text',"Apellido&nbsp;Materno","Ingrese A.Materno",'pers_apemat',$_POST['pers_apemat'],'','30','');
	echo $html->put_text('text',"Nombres","Ingrese Nombres",'pers_nombres',$_POST['pers_nombres'],'','30','');
	echo "</main>";

	echo "<main style='column-count:3;'>";
	echo $html->put_text('text',"Fecha&nbsp;Nacimiento","",'pers_fecnac',$_POST['pers_fecnac'],'','10','');
	$arra_options_esta[""]="<- Seleccione ->";
	$arra_options_esta["S"]= "Soltero(a)" ;
	$arra_options_esta["C"]= "Casado(a)" ;
	$arra_options_esta["V"]= "Viudo(a)" ;
	$arra_options_esta["D"]= "Divorciado(a)" ;
	echo $html->put_select("Estado&nbsp;Civil",'pers_estciv',$arra_options_esta,$_POST['pers_estciv'],'');
	echo $html->put_text('text',"Nro.DNI","DNI",'pers_dni',$_POST['pers_dni'],'','8','');
	echo "</main>";

	echo "<main style='column-count:1;'>";
	echo $html->put_text('text',"Lugar&nbsp;de&nbsp;Nacimiento","",'pers_lugarnac',$_POST['pers_lugarnac'],'','30','style="max-width:400px;"');
	echo "</main>";

	echo "<main style='column-count:1;'>";
	echo $html->put_text('text',"Direcci&oacute;n","Direcci&oacute;n",'pers_dire',$_POST['pers_dire'],'','50','style="max-width:600px;"');
	echo "</main>";
	echo "<main style='column-count:1;'>";
	echo $html->put_text('text',"Distrito","Distrito",'pers_distr',$_POST['pers_distr'],'','30','style="max-width:400px;"');
	echo "</main>";
	echo "<main style='column-count:1;'>";
	echo $html->put_text('text',"Referencia&nbsp;domiciliaria","Referencia",'pers_refedir',$_POST['pers_refedir'],'','50','style="max-width:600px;"');
	echo "</main>";

	echo "<main style='column-count:2;'>";
	echo $html->put_text('text',"Tel&eacute;fono&nbsp;fijo","Tlf.Fijo",'pers_tlffijo',$_POST['pers_tlffijo'],'','12','');
	echo $html->put_text('text',"Tel&eacute;fono&nbsp;celular","Celular",'pers_celu',$_POST['pers_celu'],'','12','');
	echo "</main>";

	echo "<main style='column-count:2;'>";
	echo $html->put_text('text',"EMail&nbsp;personal","E-Mail personal",'pers_emailper',$_POST['pers_emailper'],'','50','');
	echo $html->put_text('text',"EMail&nbsp;institucional","E-Mail institucional",'pers_emailinst',$_POST['pers_emailinst'],'','50','');
	echo "</main>";

	echo "<main style='column-count:2;'>";
	echo $html->put_text('text',"Nombres&nbsp;Apellidos&nbsp;(Persona&nbsp;1)","Persona 1",'pers_nomape_per1',$_POST['pers_nomape_per1'],'','50','');
	echo $html->put_text('text',"Nro&nbsp;Celular&nbsp;(Persona&nbsp;1)","",'pers_nrocel_per1',$_POST['pers_nrocel_per1'],'','12','');
	echo "</main>";
	echo "<main style='column-count:2;'>";
	echo $html->put_text('text',"Nombres&nbsp;Apellidos&nbsp;(Persona&nbsp;2)","Persona 2",'pers_nomape_per2',$_POST['pers_nomape_per2'],'','50','');
	echo $html->put_text('text',"Nro&nbsp;Celular&nbsp;(Persona&nbsp;2)","",'pers_nrocel_per2',$_POST['pers_nrocel_per2'],'','12','');
	echo "</main>";

	echo "<main style='column-count:3;'>";
	echo $html->put_text('text',"Grado&nbsp;Instrucci&oacute;n","Grado instrucci&oacute;n",'pers_grains',$_POST['pers_grains'],'','30','');
	echo $html->put_text('text',"Profesi&oacute;n","Profesi&oacute;n 1",'pers_prof1',$_POST['pers_prof1'],'','30','');
	echo $html->put_text('text',"Otra&nbsp;Profesi&oacute;n","Profesi&oacute;n 2",'pers_prof2',$_POST['pers_prof2'],'','30','');
	echo "</main>";

	echo "<main style='column-count:3;'>";
	echo $html->put_text('text',"Nro&nbsp;Colegiatura","",'pers_nrocole',$_POST['pers_nrocole'],'','10','');

	$arra_options_activo[0]="NO";
	$arra_options_activo[1]="SI";
	echo $html->put_select("Activo",'activo',$arra_options_activo,$_POST['activo'],'');
	echo "</main>";

	echo "<main style='column-count:1;'>";
	$arra_options_depe[0]="<- Seleccione ->";
        $result=$Db->select('mp_admi_depe', '', '', '', ['codi_depe'=>'ASC']);
        foreach($result as $rows)
                $arra_options_depe[$rows['codi_depe']]= $rows['nomb_depe'] ;
	echo $html->put_select("Dependencia&nbsp;actual",'pers_depe',$arra_options_depe,$_POST['pers_depe'],'style="max-width:600px;"');
	echo "</main>";

	echo "<main style='column-count:2;'>";
	echo $html->put_text('text',"Regimen&nbsp;Laboral","",'pers_reglab',$_POST['pers_reglab'],'','20','');
	echo $html->put_text('text',"Plaza&nbsp;con&nbsp;Presupuesto&nbsp;de:","",'pers_plapres',$_POST['pers_plapres'],'','30','');
	echo "</main>";



	echo "<main style='column-count:3;'>";
	echo $html->put_title_demand("<b>DATOS PARA PROYECCION DE SALDOS - PLANILLA</b>");
	echo $html->put_text('text',"Fecha&nbsp;Ingreso&nbsp;Instituci&oacute;n","",'pers_fecing',$_POST['pers_fecing'],'','10','');

	$arra_options_cargo[0]="<- Seleccione ->";
	$result=$Db->select('mp_plan_escalaremunerativa', '', '', '', ['esccargo'=>'ASC']);
	foreach($result as $rows)
			$arra_options_cargo[$rows['n_codigo']]= $rows['esccargo'] ;
	echo $html->put_select("Cargo",'pers_cargo',$arra_options_cargo,$_POST['pers_cargo'],'');
	echo $html->put_select("Cargo&nbsp;PEA",'codcargopea',$arra_options_cargo,$_POST['codcargopea'],'');
	echo "</main>";

	echo "<main style='column-count:3;'>";
	echo $html->put_text('text',"META","",'meta',$_POST['meta'],'','4','');

	$arra_options_asifam[0]="NO";
	$arra_options_asifam[1]="SI";
	echo $html->put_select("Asignaci&oacute;n&nbsp;Familiar",'asignacionfamiliar',$arra_options_asifam,$_POST['asignacionfamiliar'],'');

	$arra_options_eps[0]="NO";
	$arra_options_eps[1]="SI";
	echo $html->put_select("EPS",'eps',$arra_options_eps,$_POST['asignacioneps'],'');
	echo "</main>";

	echo "<main style='column-count:3;'>";
	echo $html->put_text('text',"Clasificador&nbsp;haberes","",'clas_haberes',$_POST['clas_haberes'],'','20','');
	echo $html->put_text('text',"Clasificador&nbsp;benef.extra","",'clas_benefextra',$_POST['clas_benefextra'],'','20','');
	echo $html->put_text('text',"Clasificador&nbsp;bono&nbsp;fiscal","",'clas_bonofiscal',$_POST['clas_bonofiscal'],'','20','');
	echo "</main>";

	echo "<main style='column-count:3;'>";
	echo $html->put_text('text',"Clasificador&nbsp;gastos&nbsp;operativos","",'clas_go',$_POST['clas_go'],'','20','');
	echo $html->put_text('text',"Clasificador&nbsp;25%&nbsp;retardo","",'clas_25retardo',$_POST['clas_25retardo'],'','20','');
	echo $html->put_text('text',"Clasificador&nbsp;aguinaldo","",'clas_aguinaldo',$_POST['clas_aguinaldo'],'','20','');
	echo "</main>";

	echo "<main style='column-count:3;'>";
	echo $html->put_text('text',"Clasificador&nbsp;CAFAE","",'clas_cafae',$_POST['clas_cafae'],'','20','');
	echo $html->put_text('text',"Clasificador&nbsp;escolaridad","",'clas_escolaridad',$_POST['clas_escolaridad'],'','20','');
	echo $html->put_text('text',"Clasificador&nbsp;ESSALUD","",'clas_essalud9porc',$_POST['clas_essalud9porc'],'','20','');
	echo "</main>";

	echo "<main style='column-count:3;'>";
	echo $html->put_text('text',"Clasificador&nbsp;EPS","",'clas_eps225',$_POST['clas_eps225'],'','20','');
	echo $html->put_text('text',"Clasificador&nbsp;fondo&nbsp;pensiones","",'clas_fondopens6porc',$_POST['clas_fondopens6porc'],'','20','');
	echo $html->put_text('text',"Clasificador&nbsp;gratificaci&oacute;n&nbsp;9%","",'clas_grati9porc',$_POST['clas_grati9porc'],'','20','');
	echo "</main>";











	echo "<main style='column-count:1;'>";
	echo $html->put_title_demand("OTROS DATOS");
	echo $html->put_text('text',"Nombres&nbsp;Apellidos&nbsp;(Conyuge)","Conyuge",'pers_conyuge',$_POST['pers_conyuge'],'','50','style="max-width:600px;"');
	echo "</main>";

	echo "<main style='column-count:3;'>";
	echo $html->put_text('text',"Nombres&nbsp;Apellidos&nbsp;(Hijo&nbsp;1)","Hijo 1",'pers_hijo1',$_POST['pers_hijo1'],'','50','');
	echo $html->put_text('text',"Fecha&nbsp;Nacimiento&nbsp;(Hijo&nbsp;1)","",'pers_fechijo1',$_POST['pers_fechijo1'],'','10','');
	$arra_options_sex1[""]="<- Seleccione ->";
	$arra_options_sex1["M"]= "Masculino" ;
	$arra_options_sex1["F"]= "Femenino" ;
	echo $html->put_select("Sexo&nbsp;(Hijo&nbsp;1)",'pers_sexohijo1',$arra_options_sex1,$_POST['pers_sexohijo1'],'');
	echo "</main>";

	echo "<main style='column-count:3;'>";
	echo $html->put_text('text',"Nombres&nbsp;Apellidos&nbsp;(Hijo&nbsp;2)","Hijo 2",'pers_hijo2',$_POST['pers_hijo2'],'','50','');
	echo $html->put_text('text',"Fecha&nbsp;Nacimiento&nbsp;(Hijo&nbsp;2)","",'pers_fechijo2',$_POST['pers_fechijo2'],'','10','');
	$arra_options_sex2[""]="<- Seleccione ->";
	$arra_options_sex2["M"]= "Masculino" ;
	$arra_options_sex2["F"]= "Femenino" ;
	echo $html->put_select("Sexo&nbsp;(Hijo&nbsp;2)",'pers_sexohijo2',$arra_options_sex2,$_POST['pers_sexohijo2'],'');
	echo "</main>";

	echo "<main style='column-count:3;'>";
	echo $html->put_text('text',"Nombres&nbsp;Apellidos&nbsp;(Hijo&nbsp;3)","Hijo 3",'pers_hijo3',$_POST['pers_hijo3'],'','50','');
	echo $html->put_text('text',"Fecha&nbsp;Nacimiento&nbsp;(Hijo&nbsp;3)","",'pers_fechijo3',$_POST['pers_fechijo3'],'','10','');
	$arra_options_sex3[""]="<- Seleccione ->";
	$arra_options_sex3["M"]= "Masculino" ;
	$arra_options_sex3["F"]= "Femenino" ;
	echo $html->put_select("Sexo&nbsp;(Hijo&nbsp;3)",'pers_sexohijo3',$arra_options_sex3,$_POST['pers_sexohijo3'],'');
	echo "</main>";

	echo "<main style='column-count:3;'>";
	echo $html->put_text('text',"Nombres&nbsp;Apellidos&nbsp;(Hijo&nbsp;4)","Hijo 4",'pers_hijo4',$_POST['pers_hijo4'],'','50','');
	echo $html->put_text('text',"Fecha&nbsp;Nacimiento&nbsp;(Hijo&nbsp;4)","",'pers_fechijo4',$_POST['pers_fechijo4'],'','10','');
	$arra_options_sex4[""]="<- Seleccione ->";
	$arra_options_sex4["M"]= "Masculino" ;
	$arra_options_sex4["F"]= "Femenino" ;
	echo $html->put_select("Sexo&nbsp;(Hijo&nbsp;4)",'pers_sexohijo4',$arra_options_sex4,$_POST['pers_sexohijo4'],'');
	echo "</main>";

	echo "<main style='column-count:3;'>";
	echo $html->put_text('text',"Nombres&nbsp;Apellidos&nbsp;(Hijo&nbsp;5)","Hijo 5",'pers_hijo5',$_POST['pers_hijo5'],'','50','');
	echo $html->put_text('text',"Fecha&nbsp;Nacimiento&nbsp;(Hijo&nbsp;5)","",'pers_fechijo5',$_POST['pers_fechijo5'],'','10','');
	$arra_options_sex5[""]="<- Seleccione ->";
	$arra_options_sex5["M"]= "Masculino" ;
	$arra_options_sex5["F"]= "Femenino" ;
	echo $html->put_select("Sexo&nbsp;(Hijo&nbsp;5)",'pers_sexohijo5',$arra_options_sex5,$_POST['pers_sexohijo5'],'');
	echo "</main>";


	echo "<main style='column-count:2;'>";
	echo $html->put_text('text',"Nombres&nbsp;Apellidos&nbsp;(Padre)","Padre",'pers_padre',$_POST['pers_padre'],'','50','');
	echo $html->put_text('text',"Direcci&oacute;n&nbsp;(indicar&nbsp;distrito)","Direcci&oacute;n padre",'pers_padredir',$_POST['pers_padredir'],'','50','');
	echo "</main>";

	echo "<main style='column-count:2;'>";
	echo $html->put_text('text',"Nombres&nbsp;Apellidos&nbsp;(Madre)","Madre",'pers_madre',$_POST['pers_madre'],'','50','');
	echo $html->put_text('text',"Direcci&oacute;n&nbsp;(indicar&nbsp;distrito)","Direcci&oacute;n madre",'pers_madredir',$_POST['pers_madredir'],'','50','');
	echo "</main>";

	echo "<main style='column-count:3;'>";
	$arra_options_essa[""]="<- Seleccione ->";
	$arra_options_essa["S"]= "SI" ;
	$arra_options_essa["N"]= "NO" ;
	echo $html->put_select("Pertenece&nbsp;ESSALUD?",'pers_essalud',$arra_options_essa,$_POST['pers_essalud'],'');
	echo $html->put_text('text',"Centro&nbsp;Atenci&oacute;n&nbsp;o&nbsp;policl&iacute;nico","Centro de atenci&oacute;n o policl&iacute;nico",'pers_centroate',$_POST['pers_centroate'],'','50','');
	$arra_options_eps[""]="<- Seleccione ->";
	$arra_options_eps["S"]= "SI" ;
	$arra_options_eps["N"]= "NO" ;
	echo $html->put_select("Pertenece&nbsp;EPS?",'pers_eps',$arra_options_eps,$_POST['pers_eps'],'');
	echo "</main>";

	echo "<main style='column-count:2;'>";
	echo $html->put_text('text',"Tipo&nbsp;Sangre","",'pers_tpsangre',$_POST['pers_tpsangre'],'','10','style="max-width:100px;"');
	echo $html->put_text('text',"Alergias&nbsp;enfermedades&nbsp;cronicas?","",'pers_alergenf',$_POST['pers_alergenf'],'','30','');
	echo "</main>";

	echo "<main style='column-count:2;'>";
	echo $html->put_text('text',"Alguna&nbsp;discapacidad?&nbsp;(especifique)","",'pers_discap',$_POST['pers_discap'],'','50','');
	$arra_options_cona[""]="<- Seleccione ->";
	$arra_options_cona["S"]= "SI" ;
	$arra_options_cona["N"]= "NO" ;
	echo $html->put_select("Inscrito&nbsp;CONADIS?",'pers_conadis',$arra_options_cona,$_POST['pers_conadis'],'');
	echo "</main>";

	echo "<main style='column-count:1;'>";
	echo $html->put_text('text',"Habla&nbsp;otro&nbsp;idioma?&nbsp;(Cual?)","Otro idioma?",'pers_otroidi',$_POST['pers_otroidi'],'','30','style="max-width:400px;"');
	echo "</main>";


	echo "<main style='column-count:3;'>";
	$arra_options_hobfut[""]="<- Seleccione ->";
	$arra_options_hobfut["S"]= "SI" ;
	$arra_options_hobfut["N"]= "NO" ;
	echo $html->put_select("Hobbie&nbsp;Football?",'pers_hobfut',$arra_options_hobfut,$_POST['pers_hobfut'],'');
	$arra_options_hobbas[""]="<- Seleccione ->";
	$arra_options_hobbas["S"]= "SI" ;
	$arra_options_hobbas["N"]= "NO" ;
	echo $html->put_select("Hobbie&nbsp;Basketball?",'pers_hobbas',$arra_options_hobbas,$_POST['pers_hobbas'],'');
	$arra_options_hobnat[""]="<- Seleccione ->";
	$arra_options_hobnat["S"]= "SI" ;
	$arra_options_hobnat["N"]= "NO" ;
	echo $html->put_select("Hobbie&nbsp;Nataci&oacute;n?",'pers_hobnat',$arra_options_hobnat,$_POST['pers_hobnat'],'');
	echo "</main>";

	echo "<main style='column-count:3;'>";
	$arra_options_hobpin[""]="<- Seleccione ->";
	$arra_options_hobpin["S"]= "SI" ;
	$arra_options_hobpin["N"]= "NO" ;
	echo $html->put_select("Hobbie&nbsp;Ping&nbsp;Pong?",'pers_hobpin',$arra_options_hobpin,$_POST['pers_hobpin'],'');
	$arra_options_hobfro[""]="<- Seleccione ->";
	$arra_options_hobfro["S"]= "SI" ;
	$arra_options_hobfro["N"]= "NO" ;
	echo $html->put_select("Hobbie&nbsp;Fronton?",'pers_hobfro',$arra_options_hobfro,$_POST['pers_hobfro'],'');
	$arra_options_hobbai[""]="<- Seleccione ->";
	$arra_options_hobbai["S"]= "SI" ;
	$arra_options_hobbai["N"]= "NO" ;
	echo $html->put_select("Hobbie&nbsp;Baile?",'pers_hobbai',$arra_options_hobbai,$_POST['pers_hobbai'],'');
	echo "</main>";

	echo "<main style='column-count:2;'>";
	$arra_options_hobcoc[""]="<- Seleccione ->";
	$arra_options_hobcoc["S"]= "SI" ;
	$arra_options_hobcoc["N"]= "NO" ;
	echo $html->put_select("Hobbie&nbsp;Cocina?",'pers_hobcoc',$arra_options_hobcoc,$_POST['pers_hobcoc'],'');
	echo $html->put_text('text',"Otra&nbsp;habilidad&nbsp;personal?","",'pers_otrahab',$_POST['pers_otrahab'],'','50','');
	echo "</main>";


	echo $html->put_separator_demand("30");

                echo"
                        <div align=center class=\"foot\">
                                <center>
                                <div align=center class=\"foot2\">
                                        <div class=\"div_button_foot\" style=\"\">
                                                <button class=\"button_foot\" onclick=\"f_cancelar()\">&laquo; Cancelar</button>
                                        </div>
                                        <div class=\"div_button_foot\"><center>
                                                <button class=\"button_foot\" onclick=\"return f_guardar()\">Guardar &raquo;</button>
                                        </div>
                                </div>
                        </div>
                ";
?>
<center>
	</form>
	</body>
</html>
