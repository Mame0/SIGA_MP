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
	
	if($_POST['eliminar_experiencia'])
	{
	    $result=$Db->delete('mp_admi_pers_expe',['iden_expe'=>$_POST['eliminar_experiencia']]);
	}
	
	$result_personal=$Db->select('mp_admi_pers', ['iden_pers'=>$_POST['iden_pers']], '', '', '');
	$_POST['appa_pers']=$result_personal[0]['appa_pers'];
	$_POST['apma_pers']=$result_personal[0]['apma_pers'];
	$_POST['nomb_pers']=$result_personal[0]['nomb_pers'];
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
		<script>
			function f_agregar_experiencia()
			{
			    document.form.iden_expe.value='';
			    document.form.action='personal_experiencia_registro.php';
				document.form.submit();
			}
			function f_editar_experiencia(codi)
			{
			    document.form.iden_expe.value=codi;
			    document.form.action='personal_experiencia_registro.php';
				document.form.submit();
			}
			function f_eliminar_experiencia(codi,nume)
			{
			    if(confirm('Seguro que desea eliminar item Nro '+nume+'?'))
			    {
			        document.form.eliminar_experiencia.value=codi;
				    document.form.action='personal_experiencia.php';
				    document.form.submit();
			    }
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
		echo"Experiencia Laboral<BR>".$_POST['appa_pers']." ".$_POST['apma_pers'].", ".$_POST['nomb_pers'];
	else
		echo"Crear Nuevo Personal";
?>
	</h4></b></center>
		<form name="form" method="post" ENCTYPE="multipart/form-data">
			<input type=hidden name="guardar_personal">
			<input type=hidden name="iden_expe">
			<input type=hidden name="eliminar_experiencia">
			<input type=hidden name="iden_pers" value="<?=$_POST['iden_pers']?>">
			<input type=hidden name="busq_tipo" value="<?=$_POST['busq_tipo']?>">
			<input type=hidden name="busq_dato" value="<?=$_POST['busq_dato']?>">
			<input type=hidden name="codi_depe" value="<?=$_POST['codi_depe']?>">
			<input type=hidden name="busq_pagi_actu" value="<?=$_POST['busq_pagi_actu']?>">
			<input type=hidden name="codi_form" value="<?=$_POST['codi_form']?>">
<?
	$html=new htmlclass;
    
    $arra_options_carg=$Db->get_options('mp_maes_labo_cargos',1,0);
    $arra_options_cond=$Db->get_options('mp_maes_labo_condic_contractual',1,0);
    $arra_options_moti=$Db->get_options('mp_maes_labo_motivo_cese',1,0);
    
    echo"<main>";
	echo $html->put_title_demand("Experiencia Laboral","<a href=\"javascript:f_agregar_experiencia()\">Agregar&nbsp;Experiencia</a>");
	echo"</main>";
	$result_pagi=$Db->query("select * from mp_admi_pers_expe where iden_pers='$_POST[iden_pers]'");
    echo"<div style=\"width:90%;max-width:800px;margin:auto;\">";
    $head=['1'=>"Nº",'2'=>"INSTITUCION",'3'=>"CARGO",'4'=>"DESDE/HASTA",'5'=>"CONDICION",'6'=>"EDIT",'7'=>"ELIM"];
    echo $html->put_table_responsive_open();
    $cont=0;
	echo $html->put_table_responsive_header($head);
	foreach($result_pagi as $rows)
	{
		$cont++;
		$data=[	'1'=>$cont,
			'2'=>$rows['inst_expe'],
			'3'=>$arra_options_carg[$rows['iden_carg']],
			'4'=>$rows['desd_expe'],
			'5'=>$arra_options_cond[$rows['iden_cond']],
			'6'=>"<a href=\"javascript:f_editar_experiencia('$rows[iden_expe]')\"><img src=\"img/icons/edit.svg\" width=\"20\">",
			'7'=>"<a href=\"javascript:f_eliminar_experiencia('$rows[iden_expe]','$cont')\"><img src=\"img/icons/trash.svg\" width=\"20\">",
		];
		echo $html->put_table_responsive_data($head,$data);
	}
    //if($cont==0)
    //	echo $html->put_table_responsive_title("Usuario no tiene Enfermedades");
		
    echo $html->put_table_responsive_close();
    echo"</div>";
    
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
