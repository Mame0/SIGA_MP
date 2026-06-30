<?
	require_once 'include/cabecera.php';
	require_once 'classes/Html.class.php';
	require_once 'classes/Db.class.php';
	$Db = new Db();

	$fdig=date("YmdHis");
	
	if(!$_POST['iden_pers'])
	{
	    $result=$Db->query("select * from mp_admi_pers where ndoc_pers='$_SESSION[ndoc_oper]'");
        foreach($result as $rows)
            $_POST['iden_pers']=$rows['iden_pers'];
	}

	if($_POST['guardar_personal'])
	{
		$fdig=date(YmdHis);
		//$_POST['esta_pers']=1;
		if($_POST['iden_pers'])
		{
			$result=$Db->update('mp_admi_pers',['domi_pers'=>$_POST['iden_dist'],'iden_tvia'=>$_POST['iden_tvia'],'dnro_pers'=>$_POST['dnro_pers'],'dire_pers'=>$_POST['dire_pers'],'dint_pers'=>$_POST['dint_pers'],'dpis_pers'=>$_POST['dpis_pers'],'dlot_pers'=>$_POST['dlot_pers'],'dman_pers'=>$_POST['dman_pers'],'dref_pers'=>$_POST['dref_pers'],'iden_tdom'=>$_POST['iden_tdom']],['iden_pers'=>$_POST['iden_pers']]);
		}
		else
		{
			$result=$Db->insert('mp_admi_pers',['ndoc_pers'=>$_POST['ndoc_pers'],'appa_pers'=>$_POST['appa_pers'],'apma_pers'=>$_POST['apma_pers'],'nomb_pers'=>$_POST['nomb_pers'],'codi_depe'=>$_POST['codi_depe'],'codi_carg'=>$_POST['codi_carg'],'regi_labo'=>$_POST['regi_labo'],'fech_ingr'=>$_POST['fech_ingr'],'digi_pers'=>$_POST['digi_pers'],'esta_pers'=>$_POST['esta_pers']]);
			$_POST['iden_pers']=$Db->lastInsertId();
		}

		echo"
			<!--<script>alert('".CONST_MENS_REG_OK."');</script>-->
                        <html><body>
                                <form name=\"form\" method=post action=\"personal_direccion.php\">
                    <input type=hidden name=\"iden_pers\" value=\"".$_POST['iden_pers']."\">
					<input type=hidden name=\"busq_tipo\" value=\"".$_POST['busq_tipo']."\">
					<input type=hidden name=\"busq_dato\" value=\"".$_POST['busq_dato']."\">
					<input type=hidden name=\"busq_pagi_actu\" value=\"".$_POST['busq_pagi_actu']."\">
					<input type=hidden name=\"codi_form\" value=\"".$_POST['codi_form']."\">
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
                            var dpto='<?=$_POST['iden_dpto']?>';
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
                            var prov='<?=$_POST['iden_prov']?>';
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
                            var dist='<?=$_POST['iden_dist']?>';
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
				if(document.form.iden_dpto.value=='')
				{
					alert('Seleccione Departamento');
					document.form.iden_dpto.focus();
					return false;
				}
				else
				{
					if(document.form.iden_prov.value=='')
					{
						alert('Seleccione Provincia');
						document.form.iden_prov.focus();
						return false;
					}
					else
					{
						if(document.form.iden_dist.value=='')
						{
							alert('Seleccione Distrito');
							document.form.iden_dist.focus();
							return false;
						}
						else
						{
							if(document.form.dire_pers.value=='')
						    {
    							alert('Ingrese Direcci®Æn');
	    						document.form.dire_pers.focus();
		    					return false;
			    			}
				    		else
					    	{
						    	if(document.form.iden_tdom.value=='')
						        {
							        alert('Seleccione Tipo de Domicilio');
							        document.form.iden_tdom.focus();
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
			function f_cancelar_documento()
			{
				document.form.action='personal_mantenimiento.php';
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
			<input type=hidden name="busq_tipo" value="<?=$_POST['busq_tipo']?>">
			<input type=hidden name="busq_dato" value="<?=$_POST['busq_dato']?>">
			<input type=hidden name="codi_depe" value="<?=$_POST['codi_depe']?>">
			<input type=hidden name="busq_pagi_actu" value="<?=$_POST['busq_pagi_actu']?>">
			<input type=hidden name="codi_form" value="<?=$_POST['codi_form']?>">
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

	//echo $html->put_title_demand("Domicilio");
	echo $html->put_select("Departamento",'iden_dpto',$arra_options_dptox,$_POST['iden_dpto'],"");
	echo $html->put_select("Provincia",'iden_prov',$arra_options_prov,$_POST['iden_prov'],"");
	echo $html->put_select("Distrito",'iden_dist',$arra_options_dist,$_POST['iden_dist'],"");
	echo"</main><main>";
	echo $html->put_select("V√≠a",'iden_tvia',$arra_options_tvia,$_POST['iden_tvia'],"");
	echo $html->put_text('text',"Nro","Ingrese Nro.",'dnro_pers',$_POST['dnro_pers'],'','100','');
	echo $html->put_text('text',"Direcci√≥n","Ingrese direcci√≥m",'dire_pers',$_POST['dire_pers'],'','100','');
	echo"</main><main>";
	echo $html->put_text('text',"Block/Departamento/Interior","Ingrese Nro.",'dint_pers',$_POST['dint_pers'],'','100','');
	echo $html->put_select("Nro.&nbsp;Piso",'dpis_pers',$arra_options_piso,$_POST['dpis_pers'],"");
	echo $html->put_text('text',"Lote","Ingrese Lote",'dlot_pers',$_POST['dlot_pers'],'','10','');
	echo"</main><main>";
	echo $html->put_select("Manzana",'dman_pers',$arra_options_manz,$_POST['dman_pers'],"");
	echo $html->put_text('text',"Referencia","Ingrese Referencia",'dref_pers',$_POST['dref_pers'],'','100','');
	echo $html->put_select("Tipo",'iden_tdom',$arra_options_tdom,$_POST['iden_tdom'],"");
	
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
