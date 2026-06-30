<?
	require_once 'include/cabecera.php';
	require_once 'classes/Html.class.php';
	require_once 'classes/Db.class.php';
	$Db = new Db();

	$fdig=date("YmdHis");
	
	if(empty($_POST['iden_pers']))
	{
	    echo"
            <html><body>
                <form name=\"form\" method=post action=\"personal_familiares.php\">
                    <input type=hidden name=\"iden_pers\" value=\"".($_POST['iden_pers'] ?? '')."\">
					<input type=hidden name=\"codi_form\" value=\"".($_POST['codi_form'] ?? '')."\">
					<input type=hidden name=\"flag_admi\" value=\"".($_POST['flag_admi'] ?? '')."\">
                </form>
                <script>document.form.submit();</script>
            </body></html>
		";
	}

	if(!empty($_POST['guardar_personal']))
	{
		$fdig=date('YmdHis');
        $_POST['fnac_fami']=str_replace("-","",$_POST['fnac_fami'] ?? '');
		if(!empty($_POST['iden_pers']) && !empty($_POST['iden_fami']))
		{
			$result=$Db->update('mp_admi_pers_fami',['iden_pers'=>$_POST['iden_pers'],'iden_tipo'=>$_POST['iden_tipo'],'appa_fami'=>$_POST['appa_fami'],'apma_fami'=>$_POST['apma_fami'],'nomb_fami'=>$_POST['nomb_fami'],'iden_tdoc'=>$_POST['iden_tdoc'],'ndoc_fami'=>$_POST['ndoc_fami'],'iden_sexo'=>$_POST['iden_sexo'],'fnac_fami'=>$_POST['fnac_fami'],'vive_fami'=>$_POST['vive_fami'],'iden_ocup'=>$_POST['iden_ocup'],'iden_pais'=>$_POST['iden_pais'],'lnac_fami'=>$_POST['iden_dist'],'iden_tent'=>$_POST['iden_tent'],'iden_regi'=>$_POST['iden_regi'],'digi_fami'=>$_SESSION['iden_oper'],'fdig_fami'=>$fdig,'esta_fami'=>'1'],['iden_fami'=>$_POST['iden_fami']]);
		}
		else
		{
			$result=$Db->insert('mp_admi_pers_fami',['iden_pers'=>$_POST['iden_pers'],'iden_tipo'=>$_POST['iden_tipo'],'appa_fami'=>$_POST['appa_fami'],'apma_fami'=>$_POST['apma_fami'],'nomb_fami'=>$_POST['nomb_fami'],'iden_tdoc'=>$_POST['iden_tdoc'],'ndoc_fami'=>$_POST['ndoc_fami'],'iden_sexo'=>$_POST['iden_sexo'],'fnac_fami'=>$_POST['fnac_fami'],'vive_fami'=>$_POST['vive_fami'],'iden_ocup'=>$_POST['iden_ocup'],'iden_pais'=>$_POST['iden_pais'],'lnac_fami'=>($_POST['lnac_fami'] ?? null),'iden_tent'=>$_POST['iden_tent'],'iden_regi'=>$_POST['iden_regi'],'digi_fami'=>$_SESSION['iden_oper'],'fdig_fami'=>$fdig,'esta_fami'=>'1'],['iden_fami'=>$_POST['iden_fami']]);
			$_POST['iden_fami']=$Db->lastInsertId();
		}

		echo"
			<!--<script>alert('".CONST_MENS_REG_OK."');</script>-->
                        <html><body>
                                <form name=\"form\" method=post action=\"personal_familiares.php\">
                    <input type=hidden name=\"iden_pers\" value=\"".($_POST['iden_pers'] ?? '')."\">
					<input type=hidden name=\"busq_tipo\" value=\"".($_POST['busq_tipo'] ?? '')."\">
					<input type=hidden name=\"busq_dato\" value=\"".($_POST['busq_dato'] ?? '')."\">
					<input type=hidden name=\"busq_pagi_actu\" value=\"".($_POST['busq_pagi_actu'] ?? '')."\">
					<input type=hidden name=\"codi_form\" value=\"".($_POST['codi_form'] ?? '')."\">
					<input type=hidden name=\"flag_admi\" value=\"".($_POST['flag_admi'] ?? '')."\">
                    <input type=hidden name=\"dire_orig\" value=\"personal_familiares.php\">
                                </form>
                                <script>
                                        document.form.submit();
                                </script>
                        </body></html>
		";

	}
	$result_personal=$Db->select('mp_admi_pers', ['iden_pers'=>$_POST['iden_pers'] ?? null], '', '', '');
	$_POST['appa_pers']=$result_personal[0]['appa_pers'] ?? null;
	$_POST['apma_pers']=$result_personal[0]['apma_pers'] ?? null;
	$_POST['nomb_pers']=$result_personal[0]['nomb_pers'] ?? null;
	
	//echo"<HR>".($_POST['iden_fami'] ?? '')."<HR>";
	
	if(!empty($_POST['iden_fami']))
	{
	    $result_personal=$Db->select('mp_admi_pers_fami', ['iden_pers'=>$_POST['iden_pers'],'iden_fami'=>$_POST['iden_fami']], '', '', '');
	    $_POST['iden_tipo']=$result_personal[0]['iden_tipo'] ?? null;
	    $_POST['appa_fami']=$result_personal[0]['appa_fami'] ?? null;
	    $_POST['apma_fami']=$result_personal[0]['apma_fami'] ?? null;
	    $_POST['nomb_fami']=$result_personal[0]['nomb_fami'] ?? null;
	    $_POST['iden_tdoc']=$result_personal[0]['iden_tdoc'] ?? null;
	    $_POST['ndoc_fami']=$result_personal[0]['ndoc_fami'] ?? null;
	    $_POST['iden_sexo']=$result_personal[0]['iden_sexo'] ?? null;
	    $_POST['fnac_fami']=!empty($result_personal[0]['fnac_fami']) ? substr($result_personal[0]['fnac_fami'],0,4).'-'.substr($result_personal[0]['fnac_fami'],4,2).'-'.substr($result_personal[0]['fnac_fami'],6,2) : null;
	    $_POST['vive_fami']=$result_personal[0]['vive_fami'] ?? null;
	    $_POST['iden_ocup']=$result_personal[0]['iden_ocup'] ?? null;
	    
	    $_POST['lnac_fami']=$result_personal[0]['lnac_fami'] ?? null;
	    $_POST['iden_tent']=$result_personal[0]['iden_tent'] ?? null;
	    $_POST['iden_regi']=$result_personal[0]['iden_regi'] ?? null;
	    $_POST['iden_pais']=$result_personal[0]['iden_pais'] ?? null;
	    
	    $_POST['iden_dpto']=substr($_POST['lnac_fami'] ?? '',0,2);
	    $_POST['iden_prov']=substr($_POST['lnac_fami'] ?? '',0,4);
	    $_POST['iden_dist']=substr($_POST['lnac_fami'] ?? '',0,6);
	
	    if(empty($_POST['iden_dpto']))
    	    $_POST['iden_dpto']='04';
        if(empty($_POST['iden_prov']))
	        $_POST['iden_prov']='0401';
	}
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
		<!--
		<link rel='stylesheet prefetch' href='https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css'>
        <link rel='stylesheet prefetch' href='https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.11.2/css/bootstrap-select.min.css'>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/js/bootstrap.min.js"></script>
        <script src="//cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.6.3/js/bootstrap-select.min.js"></script>
        -->
        
        <script type="text/javascript">
            $(document).ready(function(){
                $.ajax({
                    url:'personal_ubigeo.php?Accion=GetDepartamentos',
                    success:function(Datos){
                        for (x=0;x<Datos.length;x++)
                        {
                            var sele='';
                            var dpto='<?= $_POST['iden_dpto'] ?? '' ?>';
                            if(dpto==Datos[x].IdDepartamento)
                                sele='selected';
                            $("#iden_dpto").append("<option value='"+Datos[x].IdDepartamento+"' "+sele+">"+Datos[x].Departamento+"</option>");
                            //$("#iden_dpto").append(new Option(Datos[x].Departamento, Datos[x].IdDepartamento));
                        }
                    }
                });
                $('#iden_dpto').change(function(){
                    $('#iden_prov,#iden_dist').empty();
                    $.getJSON('personal_ubigeo.php',{Accion:'GetProvincias',IdDepartamento:$('#iden_dpto option:selected').val()}, function(Datos){
                        for(x=0;x<Datos.length;x++)
                        {
                            var sele='';
                            var prov='<?= $_POST['iden_prov'] ?? '' ?>';
                            if(prov==Datos[x].IdProvincia)
                                sele='selected';
                            $("#iden_prov").append("<option value='"+Datos[x].IdProvincia+"' "+sele+">"+Datos[x].Provincia+"</option>");
                            //$("#iden_prov").append(new Option(Datos[x].Provincia, Datos[x].IdProvincia));
                        }
                    })
                });
                $('#iden_prov').change(function(){
                    $('#iden_dist').empty();
                    $.getJSON('personal_ubigeo.php',{Accion:'GetDistritos',IdProvincia:$('#iden_prov option:selected').val()}, function(Datos){
                        for(x=0;x<Datos.length;x++)
                        {
                            var sele='';
                            var dist='<?= $_POST['iden_dist'] ?? '' ?>';
                            if(dist==Datos[x].IdDistrito)
                                sele='selected';
                            $("#iden_dist").append("<option value='"+Datos[x].IdDistrito+"' "+sele+">"+Datos[x].Distrito+"</option>");
                            //$("#iden_dist").append(new Option(Datos[x].Distrito, Datos[x].IdDistrito));
                        }
                    })
                });
                setTimeout(function() { $('#iden_dpto').trigger("change"); }, 500);
                setTimeout(function() { $('#iden_prov').trigger("change"); }, 800);
                //$('#iden_dpto').trigger("change");
                //$('#iden_dpto').change();
            })
        </script>
        
		<script>
			function f_guardar_personal()
			{
				if(document.form.appa_fami.value=='')
				{
					alert('Ingrese Apellido Paterno');
					document.form.appa_fami.focus();
					return false;
				}
				else
				{
					if(document.form.apma_fami.value=='')
					{
						alert('Ingrese Apellido Materno');
						document.form.apma_fami.focus();
						return false;
					}
					else
					{
					    if(document.form.nomb_fami.value=='')
					    {
    						alert('Ingrese Nombres');
						    document.form.nomb_fami.focus();
						    return false;
					    }
					    else
					    {
					        if(document.form.iden_tipo.selectedIndex=='0')
					        {
					            alert('Seleccione Parentesco');
						        document.form.iden_tipo.focus();
						        return false;
					        }
					        else
					        {
					            if(document.form.iden_sexo.selectedIndex=='0')
					            {
    					            alert('Seleccione Sexo');
						            document.form.iden_sexo.focus();
						            return false;
					            }
					            else
					            {
					                if(document.form.fnac_fami.value=='')
					                {
    						            alert('Ingrese Fecha de Nacimiento');
						                document.form.fnac_fami.focus();
						                return false;
					                }
					                else
					                {
						                if(confirm('Seguro que desea Guardar'))
						                {
    						                document.form.guardar_personal.value='1';
	    					                document.form.submit();
		    			                }
			    		                else
				    		                return false;
					                }
					            }
					        }
					    }  
					}
				}
			}
			function f_cancelar_documento()
			{
				document.form.action='personal_familiares.php';
				document.form.submit();
			}
			function f_conyugue()
			{
			    if(document.form.iden_tipo.selectedIndex==1 || document.form.iden_tipo.selectedIndex==2)
			        document.getElementById("div_conyugue").style.display = "block";
			    else
			        document.getElementById("div_conyugue").style.display = "none";
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
	if(!empty($_POST['iden_pers']))
		echo"Datos Familiares<BR>".($_POST['appa_pers'] ?? '')." ".($_POST['apma_pers'] ?? '').", ".($_POST['nomb_pers'] ?? '');
	else
		echo"Crear Nuevo Personal";
?>
	</h4></b></center>
		<form name="form" method="post" ENCTYPE="multipart/form-data">
			<input type=hidden name="guardar_personal">
			<input type=hidden name="iden_pers" value="<?= $_POST['iden_pers'] ?? '' ?>">
			<input type=hidden name="iden_fami" value="<?= $_POST['iden_fami'] ?? '' ?>">
			<input type=hidden name="busq_tipo" value="<?= $_POST['busq_tipo'] ?? '' ?>">
			<input type=hidden name="busq_dato" value="<?= $_POST['busq_dato'] ?? '' ?>">
			<input type=hidden name="codi_depe" value="<?= $_POST['codi_depe'] ?? '' ?>">
			<input type=hidden name="busq_pagi_actu" value="<?= $_POST['busq_pagi_actu'] ?? '' ?>">
			<input type=hidden name="codi_form" value="<?= $_POST['codi_form'] ?? '' ?>">
			<input type=hidden name="flag_admi" value="<?= $_POST['flag_admi'] ?? '' ?>">
			<input type=hidden name="dire_orig" value="personal_familiares.php">
			<main>
<?
	$html=new htmlclass;

    /*
    $arra_options_depe[0]="<- Seleccione ->";
    $result=$Db->query("select * from mp_admi_depe");
    foreach($result as $rows)
        $arra_options_depe[$rows['codi_depe']]=utf8_encode(utf8_decode($rows['nomb_depe']));
    */
    $arra_options_vive[1]="SI";
    $arra_options_vive[0]="NO";
    $arra_options_pare=$Db->get_options('mp_maes_tipo_familiar',1,0);
    $arra_options_tdoc=$Db->get_options('mp_maes_tdocumento',1,0);
    $arra_options_sexo=$Db->get_options('mp_maes_sexo',1,0);
    $arra_options_ocup=$Db->get_options('mp_maes_ocupacion',1,0);
    
    $arra_options_pais=$Db->get_options('mp_maes_pais',1,0);
    $arra_options_tent=$Db->get_options('mp_maes_tipo_entidad',1,0);
    $arra_options_regi=$Db->get_options('mp_maes_regimen_laboral',1,0);
    $arra_options_dpto = [];
    $arra_options_prov = [];
    $arra_options_dist = [];
    
    if(empty($_POST['iden_pais']))
        $_POST['iden_pais']=348;
    
    if(!empty($_POST['iden_fami']))
	    echo $html->put_title_demand("Editar Información de Familiar [".($_POST['appa_fami'] ?? '')." ".($_POST['apma_fami'] ?? '').", ".($_POST['nomb_fami'] ?? '')."]");
	else
	    echo $html->put_title_demand("Agregar Nuevo Familiar");
	echo $html->put_text('text',"Apellido&nbsp;Paterno&nbsp;(*)","Ingrese Apellido Paterno",'appa_fami',$_POST['appa_fami'] ?? '','','50','');
	echo $html->put_text('text',"Apellido&nbsp;Materno&nbsp;(*)","Ingrese Apellido Materno",'apma_fami',$_POST['apma_fami'] ?? '','','50','');
	echo $html->put_text('text',"Nombres&nbsp;(*)","Ingrese Nombres",'nomb_fami',$_POST['nomb_fami'] ?? '','','50','');
	echo"</main><main>";
	echo $html->put_select("Parentesco&nbsp;(*)",'iden_tipo',$arra_options_pare,$_POST['iden_tipo'] ?? '',' onchange="f_conyugue()"');
	echo $html->put_select("Tipo&nbsp;Documento",'iden_tdoc',$arra_options_tdoc,$_POST['iden_tdoc'] ?? '','');
	echo $html->put_text('text',"Nro.&nbsp;Documento","Ingrese Número",'ndoc_fami',$_POST['ndoc_fami'] ?? '','','20','');
	echo"</main><main>";
	echo $html->put_select("Sexo&nbsp;(*)",'iden_sexo',$arra_options_sexo,$_POST['iden_sexo'] ?? '','');
	echo $html->put_text('date',"Fecha&nbsp;de&nbsp;Nacimiento&nbsp;(*)","Ingrese Fecha",'fnac_fami',$_POST['fnac_fami'] ?? '','','20','');
	echo $html->put_select("Ocupación",'iden_ocup',$arra_options_ocup,$_POST['iden_ocup'] ?? '','');
	echo"</main><main>";
	echo $html->put_select("Vive&nbsp;(*)",'vive_fami',$arra_options_vive,$_POST['vive_fami'] ?? '','');
    
    echo"<div id='div_conyugue' style='display:none;'>";
	echo $html->put_title_demand("Datos adicionales de C&oacute;nyugue");
	echo $html->put_select("Entindad&nbsp;en&nbsp;la&nbsp;que&nbsp;Presta&nbsp;Servicio",'iden_tent',$arra_options_tent,$_POST['iden_tent'] ?? '','');
	echo $html->put_select("R&eacutegimen&nbsp;Laboral",'iden_regi',$arra_options_regi,$_POST['iden_regi'] ?? '','');
	echo $html->put_select("Pais&nbsp;de&nbsp;Nacimiento",'iden_pais',$arra_options_pais,$_POST['iden_pais'] ?? '','');
	echo $html->put_select("Departamento&nbsp;de&nbsp;Nacimiento",'iden_dpto',$arra_options_dpto,$_POST['iden_dpto'] ?? '','');
	echo $html->put_select("Provincia&nbsp;de&nbsp;Nacimiento",'iden_prov',$arra_options_prov,$_POST['iden_prov'] ?? '','');
	echo $html->put_select("Distrito&nbsp;de&nbsp;Nacimiento",'iden_dist',$arra_options_dist,$_POST['iden_dist'] ?? '','');
	echo"</div>";
	
	echo"</main>";

	echo $html->put_separator_demand("30");

                echo"
                        <div align=center class=\"foot\">
                                <center>
                                <div align=center class=\"foot2\">
                                        <div class=\"div_button_foot\" style=\"\">
                                                <button class=\"button_foot\" onclick=\"f_cancelar_documento()\">&laquo; Cancelar</button>
                                        </div>
                                        <div class=\"div_button_foot\"><center>
                                                <button class=\"button_foot\" onclick=\"return f_guardar_personal()\">Guardar &raquo;</button>
                                        </div>
                                </div>
                        </div>
                ";
?>
<center>
	</form>
	</body>
</html>
