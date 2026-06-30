<?
	require_once 'include/cabecera.php';
	require_once 'classes/Html.class.php';
	require_once 'classes/Db.class.php';
	$Db = new Db();

	$fdig=date("YmdHis");

	if($_POST['guardar_personal'])
	{
		$fdig=date(YmdHis);

	$horalocal = gmdate('Y-m-d H:i:s', time() + (-5 * 3600));//-5 es la zona horaria de perú
	$solofecha = substr($horalocal,0,10);
	$soloano = substr($solofecha,0,4) ;

		if($_POST['codi_bien'])
		{
/*			$result=$Db->update('mp_bienesinventario',['bien_codpatrimonial'=>$_POST['bien_codpatrimonial'], 'bien_descripcion'=>utf8_decode($_POST['bien_descripcion']),
			'bien_marca'=>utf8_decode($_POST['bien_marca']),'bien_modelo'=> utf8_decode($_POST['bien_modelo'] ),'bien_serie'=>utf8_decode($_POST['bien_serie']),
			'bien_cantidad'=>$_POST['bien_cantidad'],'activo'=>$_POST['activo'] ]  , ['codi_bien'=>$_POST['codi_bien']]  );
*/
		}
		else
		{
			//cabecera
			$result=$Db->insert('mp_horascompensa_cabecera',['comp_personal'=>$_POST['codi_pers'], 'comp_nroexpediente'=>$_POST['comp_nroexpediente'],
			'comp_anoexpediente'=>$_POST['comp_anoexpediente'], 'comp_fecharegistro'=>$solofecha]);
			$codimovi=$Db->lastInsertId();
			//detalle
			for ($xcnt=1;$xcnt<=$_POST['cantreg'];$xcnt++) {
				if (isset($_POST['compinst'.$xcnt])) {
					$result=$Db->insert('mp_horascompensa_detalle',['comp_id'=>$codimovi,'comp_institucion'=>$_POST['compinst'.$xcnt], 'comp_tema'=>$_POST['comptema'.$xcnt],
					'comp_intervalofechas'=>$_POST['compinter'.$xcnt],'comp_modalidad'=>$_POST['compmoda'.$xcnt],
					'comp_horas'=>$_POST['comphora'.$xcnt] ]);
				}
			}


		}

		echo"
			<!--<script>alert('".CONST_MENS_REG_OK."');</script>-->
                        <html><body>
                                <form name=\"form\" method=post action=\"compensa_horas.php\" >
					<input type=hidden name=\"busq_tipo\" value=\"".$_POST['busq_tipo']."\">
					<input type=hidden name=\"busq_dato\" value=\"".$_POST['busq_dato']."\">
					<input type=hidden name=\"busq_pagi_actu\" value=\"".$_POST['busq_pagi_actu']."\">
                                </form>
                                <script>
                                        document.form.submit();
                                </script>
                        </body></html>
		";
exit();
	}
/*
	$result_documento=$Db->select('mp_bienesinventario', ['codi_bien'=>$_POST['codi_bien']], '', '', '');
	$_POST['bien_codpatrimonial']=$result_documento[0]['bien_codpatrimonial'];
	$_POST['bien_descripcion']=$result_documento[0]['bien_descripcion'];
	$_POST['bien_marca']=$result_documento[0]['bien_marca'];
	$_POST['bien_modelo']=utf8_encode( $result_documento[0]['bien_modelo'] );
	$_POST['bien_serie']=$result_documento[0]['bien_serie'];
	$_POST['bien_cantidad']=$result_documento[0]['bien_cantidad'];
	$_POST['activo']=$result_documento[0]['activo'];
*/
?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8" />
		<title>COMPENSACION DE HORAS</title>
		<link rel="stylesheet" href="css/forms_demanda.css" />
		<link rel="stylesheet" href="css/forms_foot.css" />
		<link rel="stylesheet" href="css/forms_column.css" />
		<SCRIPT language="JavaScript" src="js/cadenas.js"></SCRIPT>
		<SCRIPT language="JavaScript" src="js/denuncia.js"></SCRIPT>

		<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>

		<script>
			function f_guardar() {
				if (document.getElementById("codi_pers").value=="0") {
					alert ("SELECCIONE USUARIO DE LA LISTA");
					return false;
				}
				if (document.getElementById("comp_nroexpediente").value=="") {
					alert ("INGRESE EL NRO DE EXPEDIENTE");
					return false;
				}
				if (document.getElementById("comp_anoexpediente").value=="") {
					alert ("INGRESE EL AÑO DE EXPEDIENTE");
					return false;
				}

				var numenv=0;
				var cods="";
				for (ncan=1; ncan<=cantit; ncan++) {
					if (document.getElementById("tr" + ncan)) {
						numenv++;
					}
				}
				if (numenv==0) {
					alert ("Ingrese algun concepto por capacitacion");
					return false;
				}

				if(confirm('Seguro que desea Guardar')) {
					document.form.guardar_personal.value='1';
					document.form.submit();
				} else {
					return false;
				}
			}
			function f_cancelar()
			{
				document.form.action='movs_inventario.php';
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
	<center><h2 style="color:#073A6B">
<?
	if($_POST['codi_bien'])
		echo"Editar Informaci&oacute;n - Bienes Inventario ".$_POST['bien_codpatrimonial'];
	else
		$tpmov=(($_POST['codi_pers']==1)?"S":"I");

		echo "REGISTRAR DE HORAS COMPENSADAS POR CAPACITACI&Oacute;N" ;
?>
	</h2></center>
		<form name="form" method="post" autocomplete="off">
			<input type=hidden name="guardar_personal">
			<input type=hidden name="codi_bien" value="<?=$_POST['codi_bien']?>">
			<input type=hidden name="busq_tipo" value="<?=$_POST['busq_tipo']?>">
			<input type=hidden name="busq_dato" value="<?=$_POST['busq_dato']?>">
			<input type=hidden name="busq_pagi_actu" value="<?=$_POST['busq_pagi_actu']?>">

			<input type=hidden name="tpmov" value="<?=$tpmov?>">

<link href="css/table_responsive.css" rel="stylesheet" type="text/css">
<?
	$html=new htmlclass;

	$arra_options_pers[0]="<- Seleccione ->";
	$result=$Db->select('mp_personal', '', '', '', ['pers_apepat'=>'ASC', 'pers_apemat'=>'ASC', 'pers_nombres'=>'ASC']);
	foreach($result as $rows)
		$arra_options_pers[$rows['codi_pers']]= $rows['pers_apepat']." ".$rows['pers_apemat']." ".$rows['pers_nombres'] ;

	echo "<main style='column-count:3;'>";
	echo $html->put_select("Usuario:",'codi_pers',$arra_options_pers,"","");
	echo $html->put_text('text',"Nro.&nbsp;Expediente&nbsp;CEA: ","",'comp_nroexpediente','','','6',' style="width:100px;" ');
	echo $html->put_text('text',"A&ntilde;o&nbsp;Expediente&nbsp;CEA: ","",'comp_anoexpediente','','','4',' style="width:100px;" ');
	echo "</main>";

	echo "<main style='column-count:2;'>";
	echo $html->put_title_demand("INGRESE LOS CONCEPTOS DE LAS CAPACITACIONES");
	echo $html->put_text('text',"Instituci&oacute;n: ","Lugar donde se capacit&oacute;",'comp_institucion','','','50','');
	echo $html->put_text('text',"Tema: ","En que se capacit&oacute;",'comp_tema','','','70','');
	echo "</main>";
	echo "<main style='column-count:4; column-width:0px;'>";
	echo $html->put_text('text',"Intervalo&nbsp;Fechas: ","Fechas capacitaci&oacute;n",'comp_intervalofechas','','','30','');
	$arra_options_moda["0"]="<- Seleccione ->";
	$arra_options_moda["P"]="Presencial";
	$arra_options_moda["V"]="Virtual";
	echo $html->put_select("Modalidad",'comp_modalidad',$arra_options_moda,"","");
	echo $html->put_text('text',"Horas&nbsp;Compensar: ","",'comp_horas','','','3','');
	echo $html->put_button_colum("&nbsp;","Adicinar a Lista &raquo;","return adicionar_lista()");
	echo "</main>";


	echo"<div style=\"width:90%;max-width:800px;margin:auto;\">";
	echo '
	<table class="table_responsive" id="detalledoc" align="center" width=100% border="1" cellpadding="0" cellspacing="0" style="border-collapse: collapse;">
	<thead>
	<tr >
	  <td ><b>#</b></td>
	  <td ><b>Instituci&oacute;n</b></td>
	  <td ><b>Tema</b></td>
	  <td ><b>Intervalo Fechas</b></td>
	  <td ><b>Modalidad</b></td>
	  <td ><b>Horas</b></td>
	  <td ><b>Eliminar</b></td>
	</tr>
	</thead>
	</table>';
	echo '<input type="hidden" id="cantreg" name="cantreg" value="">';
	echo"</div>";



	echo $html->put_separator_demand("30");

                echo"
                        <div align=center class=\"foot\">
                                <center>
                                <div align=center class=\"foot2\">
                                        <!--<div class=\"div_button_foot\" style=\"\">
                                                <button class=\"button_foot\" onclick=\"f_cancelar()\">&laquo; Cancelar</button>
                                        </div>-->
                                        <div class=\"div_button_foot\"><center>
                                                <button class=\"button_foot\" onclick=\"return f_guardar()\">Registrar &raquo;</button>
                                        </div>
                                </div>
                        </div>
                ";
?>
<center>
	</form>
	</body>
</html>


<script language="Javascript">
var tpmov="<? echo $tpmov; ?>";
function eliitem(linitem) {
	$('#tr' + linitem).remove();
}

var cantit=0;
function adicionar_lista() {
	var respuesta="";
    if (event.keyCode == 13) {
		event.preventDefault();
		return false;
	}
	event.preventDefault();

	if ( document.getElementById("comp_institucion").value == "") {
		alert ("INGRESE LA INSTITUCION DONDE SE CAPACITO");
		return false;
	}
	if ( document.getElementById("comp_tema").value == "") {
		alert ("INGRESE EL TEMA DE LA CAPACITACION");
		return false;
	}
	if ( document.getElementById("comp_intervalofechas").value == 0) {
		alert ("INGRESE FECHAS QUE DURO ESTA CAPACITACION");
		return false;
	}
	if ( document.getElementById("comp_modalidad").value == "0") {
		alert ("INGRESE LA MODALIDAD DE LA CAPACITACION");
		return false;
	}
	if ( document.getElementById("comp_horas").value == "") {
		alert ("INGRESE LA CANTIDAD DE HORAS DE LA CAPACITACION");
		return false;
	}
	var compinst=document.getElementById("comp_institucion").value;
	var comptema=document.getElementById("comp_tema").value;
	var compinter=document.getElementById("comp_intervalofechas").value;
	var compmoda=document.getElementById("comp_modalidad").value;
	var comphora=document.getElementById("comp_horas").value;

	var lamodali="";
	if (compmoda=="P") { lamodali="Presencial";}
	if (compmoda=="V") { lamodali="Virtual";}

	cantit=cantit+1;
	document.getElementById("cantreg").value=cantit;

	row = "<tr id='tr" + cantit + "'>" +
	  "<td>" + cantit + " </td>" +
	  "<td>" + compinst + " <input name='compinst" + cantit + "' id='compinst" + cantit + "' type='hidden' value='" + compinst + "' >" +
	  "<td>" + comptema + " <input name='comptema" + cantit + "' id='comptema" + cantit + "' type='hidden' value='" + comptema + "' >" +
	  "<td>" + compinter + " <input name='compinter" + cantit + "' id='compinter" + cantit + "' type='hidden' value='" + compinter + "' >" +
	  "<td>" + lamodali + " <input name='compmoda" + cantit + "' id='compmoda" + cantit + "' type='hidden' value='" + compmoda + "' >" +
	  "<td>" + comphora + " <input name='comphora" + cantit + "' id='comphora" + cantit + "' type='hidden' value='" + comphora + "' >" +
	  "</td>" +
	  "<td align='center'><button onclick='eliitem("+ cantit +");' type='button'><img src='img/icons/delete.svg' width='20px'></button></td>" +
	"</tr>";
	$('#detalledoc').append(row);
	document.getElementById("comp_institucion").value="";
	document.getElementById("comp_tema").value="";
	document.getElementById("comp_intervalofechas").value="";
	document.getElementById("comp_modalidad").value="";
	document.getElementById("comp_horas").value="";
}

</script>