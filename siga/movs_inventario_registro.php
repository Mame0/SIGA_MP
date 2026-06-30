<?
	require_once 'include/cabecera.php';
	require_once 'classes/Html.class.php';
	require_once 'classes/Db.class.php';
	$Db = new Db();

	$fdig=date("YmdHis");

	if($_GET['codpatribusca']) {
		$result=$Db->select('mp_bienesinventario', ['bien_codpatrimonial'=>$_GET['codpatribusca']], '', '', '');
		$busc_tota_item=0;
		foreach($result as $rows) {
			$busc_tota_item++;
		}
		if ($busc_tota_item==0) {
			echo ":(";
			exit();
		}
		$codbien=$result[0]['codi_bien'];
		$descri=$result[0]['bien_descripcion'];
		$marca=$result[0]['bien_marca'];
		$modelo=$result[0]['bien_modelo'];
		$serie=$result[0]['bien_serie'];
		$canti=$result[0]['bien_cantidad'];

		echo $codbien."|".$descri."|".$marca."|".$modelo."|".$serie."|".$canti;
		exit();
	}



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
			$result=$Db->query("select movi_nroxanno from mp_bienes_movcabecera where year(movi_fecha)='".$soloano."' order by movi_nroxanno desc limit 1");
			$ultnumano=0;
			$busc_tota_item=0;
			foreach($result as $rows) {
				$busc_tota_item++;
				$ultnumano=$rows['movi_nroxanno'];
			}
			$ultnumano++;

			//cabecera
			$result=$Db->insert('mp_bienes_movcabecera',['movi_nroxanno'=>$ultnumano, 'movi_fecha'=>$solofecha,
			'movi_pers'=>$_POST['movi_pers'],'movi_depe'=> $_POST['movi_depe'] ,
			'movi_referencia'=>utf8_decode($_POST['movi_referencia']), 'movi_elaboradopor'=>$_POST['movi_elaboradopor'],'movi_tipo_is'=>$_POST['tpmov'] ]);
			//$_POST['codi_movi']=$Db->lastInsertId();
			$codimovi=$Db->lastInsertId();

			//detalle
			for ($xcnt=1;$xcnt<=$_POST['cantreg'];$xcnt++) {
				if (isset($_POST['codbar'.$xcnt])) {
					$result=$Db->insert('mp_bienes_movdetalle',['codi_movi'=>$codimovi, 'codi_bien'=>$_POST['codbar'.$xcnt],
					'bien_estado'=>$_POST['estado'.$xcnt],'bien_cantidad'=> 1,
					'movi_tipo_is'=>$_POST['tpmov'] ]);

					$result=$Db->query("select bien_cantidad from mp_bienesinventario where codi_bien='".$_POST['codbar'.$xcnt]."' ");
					$cantactual=$result[0]['bien_cantidad'];
					if ($_POST['tpmov']=="I") {
						$cantactual++;
					} else {
						$cantactual=$cantactual-1;
					}
					$result=$Db->update('mp_bienesinventario',['bien_cantidad'=>$cantactual]  , ['codi_bien'=>$_POST['codbar'.$xcnt] ]  );

				}
			}


		}

		echo"
			<!--<script>alert('".CONST_MENS_REG_OK."');</script>-->
                        <html><body>
                                <form name=\"form\" method=post action=\"movs_inventario_registro.php\" >
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
	$result_documento=$Db->select('mp_bienesinventario', ['codi_bien'=>$_POST['codi_bien']], '', '', '');
	$_POST['bien_codpatrimonial']=$result_documento[0]['bien_codpatrimonial'];
	$_POST['bien_descripcion']=$result_documento[0]['bien_descripcion'];
	$_POST['bien_marca']=$result_documento[0]['bien_marca'];
	$_POST['bien_modelo']=utf8_encode( $result_documento[0]['bien_modelo'] );
	$_POST['bien_serie']=$result_documento[0]['bien_serie'];
	$_POST['bien_cantidad']=$result_documento[0]['bien_cantidad'];
	$_POST['activo']=$result_documento[0]['activo'];

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

		<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>

		<script>
			function f_guardar() {
				if (document.getElementById("movi_pers").value==0) {
					alert ("SELECCIONE USUARIO");
					return false;
				}
				if (document.getElementById("movi_depe").value==0) {
					alert ("SELECCIONE DEPENDENCIA");
					return false;
				}
				if (document.getElementById("movi_elaboradopor").value=="") {
					alert ("INGRESE QUIEN ELABORA ACTA DE TRANSFERENCIA");
					return true;
				}

				var numenv=0;
				var cods="";
				for (ncan=1; ncan<=cantit; ncan++) {
					if (document.getElementById("tr" + ncan)) {
						numenv++;
					}
				}
				if (numenv==0) {
					alert ("Ingrese algun codigo patrimonial");
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

		echo"REGISTRAR MOVIMIENTO DE ". (($_POST['codi_pers']==1)?"SALIDA":"INGRESO") ;
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
        foreach($result as $rows) {
                $arra_options_pers[$rows['codi_pers']]= $rows['pers_apepat']." ".$rows['pers_apemat']." ".$rows['pers_nombres'] ;
                $arra_options_persdepe[$rows['codi_pers']]= $rows['pers_depe'] ;
		}

	$cantdepe=0;
	$arra_options_depe[0]="<- Seleccione ->";
        $result=$Db->select('mp_admi_depe', '', '', '', ['codi_depe'=>'ASC']);
        foreach($result as $rows) {
                $arra_options_depe[$rows['codi_depe']]= $rows['abre_depe'] ;
                $cantdepe++;
                $arra_options_depecod[$cantdepe][1]= $rows['codi_depe'] ;
                $arra_options_depecod[$cantdepe][2]= $rows['codi_padr'] ;
		}

	echo "<main style='column-count:2;'>";
	echo $html->put_title_demand("MOVIMIENTOS DE BIENES INVENTARIO (". (($_POST['codi_pers']==1)?"SALIDA":"INGRESO") .")");
	echo $html->put_select("Usuario",'movi_pers',$arra_options_pers,$_POST['movi_pers'],' onchange=" muestradepe(); " ');
	echo "<div id='dependencia' style='font-size:12px;'></div>";

	echo $html->put_select("Area&nbsp;/&nbsp;Dependencia",'movi_depe',$arra_options_depe,$_POST['movi_depe'],'');
	echo "</main>";
	echo "<main style='column-count:2;'>";
	echo $html->put_text('text',"Referencia: ","",'movi_referencia',$_POST['movi_referencia'],'','100','');
	echo $html->put_text('text',"Elaborado&nbsp;por: ","",'movi_elaboradopor',$_POST['movi_elaboradopor'],'','70','');
	echo "</main>";


	echo "<main style='column-count:1;'>";
	echo "<hr>";
	echo "</main>";
	echo "<main style='column-count:2;'>";
	echo $html->put_text('text',"C&oacute;d.&nbsp;patrimonial: ","",'codpatri','','','10',' onkeypress="return runScript(event)" ');
	echo "</main>";


	echo"<div style=\"width:90%;max-width:800px;margin:auto;\">";
	echo '
	<table class="table_responsive" id="detalledoc" align="center" width=100% border="1" cellpadding="0" cellspacing="0" style="border-collapse: collapse;">
	<thead>
	<tr >
	  <td ><b>#</b></td>
	  <td ><b>Cod.&nbsp;Patrimonial</b></td>
	  <td ><b>Descripci&oacute;n</b></td>
	  <td ><b>Marca</b></td>
	  <td ><b>Modelo</b></td>
	  <td ><b>Serie</b></td>
	  <td ><b>Estado&nbsp;bien</b></td>
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


<script language="Javascript">
var tpmov="<? echo $tpmov; ?>";
function eliitem(linitem) {
	$('#tr' + linitem).remove();

//	regtotal();
}


var cantit=0;
function runScript(e) {
	var respuesta="";
    if (e.keyCode == 13) {
	e.preventDefault();

        if ( document.getElementById("codpatri").value == "") {
        	return false;
        }
        var codpatri=document.getElementById("codpatri").value;
        var xcod=document.getElementById("codpatri").value;

		for (xcnt=1;xcnt<=cantit;xcnt++) {
			if (document.getElementById("tr" + xcnt)) {
				codbar=document.getElementById("codpat"+ xcnt).value;
				if (codbar==xcod) {
					alert("EL CODIGO "+ xcod +" YA ESTA EN LA LISTA DE BIENES A TRANSFERIR");
					document.getElementById("codpatri").value="";
					return false;
				}
			}
		}



		$.ajax({
			type: "GET",
			url: "movs_inventario_registro.php",
			cache: false,
			data: { codpatribusca: xcod }
		}).done(function( respuesta2 ) {
			var html2=respuesta2.trim();
			if (html2==":(") {
				alert("EL CODIGO PATRIMONIAL "+ xcod +" NO ES VALIDO");
				document.getElementById("codpatri").value="";
				return false;
			} else {

			var respuesta = html2.split("|");
			var tipodoc="";
			cantit=cantit+1;

			document.getElementById("cantreg").value=cantit;

			if (tpmov=="I" && respuesta[5]==1) {
				alert("EL CODIGO PATRIMONIAL "+ xcod +"\nDESCRIPCION: "+respuesta[1]+"\nYA ESTA EN ALMACEN Y NO SE PUEDE REGISTRAR SU INGRESO NUEVAMENTE");
				document.getElementById("codpatri").value="";
				return false;
			}
			if (tpmov=="S" && respuesta[5]==0) {
				alert("EL CODIGO PATRIMONIAL "+ xcod +"\nDESCRIPCION: "+respuesta[1]+"\nYA HA SIDO TRANSFERIDO Y NO SE PUEDE REGISTRAR SU SALIDA NUEVAMENTE");
				document.getElementById("codpatri").value="";
				return false;
			}



			row = "<tr id='tr" + cantit + "'>" +
			  "<td>" + cantit + " </td>" +
			  "<td>" + xcod + " <input name='codpat" + cantit + "' id='codpat" + cantit + "' type='hidden' value='" + xcod + "' >" +
			  "<input name='codbar" + cantit + "' id='codbar" + cantit + "' type='hidden' value='" + respuesta[0] + "' ></td>" +
			  "<td>" + respuesta[1] + "</td>" +
			  "<td>" + respuesta[2] + "</td>" +
			  "<td>" + respuesta[3] + "</td>" +
			  "<td>" + respuesta[4] + "</td>" +
			  "<td>" +
			  "<select name='estado"+cantit+"' id='estado"+cantit+"'>"+
            	"<option value=''><p>Selec...</p></option>"+
            	"<option value='B'><p>Bueno</p></option>"+
            	"<option value='R'><p>Regular</p></option>"+
            	"<option value='M'><p>Malo</p></option>"+
              "</select>"+
			  "</td>" +
			  "<td align='center'><button onclick='eliitem("+ cantit +");' type='button'><img src='img/icons/delete.svg' width='20px'></button></td>" +
			"</tr>";
			$('#detalledoc').append(row);

				document.getElementById("codpatri").value="";

			}
		});
    }
}



var listadepe=<?php echo json_encode($arra_options_depe); ?>;
var depeprinc=<?php echo json_encode($arra_options_persdepe); ?>;

var listacods=<?php echo json_encode($arra_options_depecod); ?>;
var canticods=<?php echo $cantdepe; ?>;

function muestradepe() {
	var indexper = document.getElementById("movi_pers").value;
	var coddepeprin=depeprinc[indexper];

	if (coddepeprin==0) {
		document.getElementById("dependencia").innerHTML = "";
	} else {
		document.getElementById("dependencia").innerHTML = listadepe[coddepeprin] ;
	}

	document.getElementById("movi_depe").innerHTML= "";

	var nrel=0;

	var x = document.getElementById("movi_depe");
	var option = document.createElement("option");
	option.text = "<- Seleccione ->";
	option.value = 0;
	x.add(option);

	if (coddepeprin==0) {
		for(i = 1; i <= canticods; i++) {
			var option = document.createElement("option");
			option.text = listadepe[listacods[i][1]];
			option.value = listacods[i][1];
			x.add(option);
		}
	} else {
		for(i = 1; i <= canticods; i++) {
			if (listacods[i][2]==coddepeprin ) {
				var option = document.createElement("option");
				option.text = listadepe[listacods[i][1]];
				option.value = listacods[i][1];
				x.add(option);

				nrel=nrel+1;
			}
		}
		if (nrel==0) {
			var option = document.createElement("option");
			option.text = listadepe[coddepeprin];
			option.value = coddepeprin;
			x.add(option);
			document.getElementById("movi_depe").value=coddepeprin;
		}
	}


return false;

	var respuesta="";
    if (e.keyCode == 13) {
	e.preventDefault();

        if ( document.getElementById("codpatri").value == "") {
        	return false;
        }
        var codpatri=document.getElementById("codpatri").value;
        var xcod=document.getElementById("codpatri").value;

		for (xcnt=1;xcnt<=cantit;xcnt++) {
			if (document.getElementById("tr" + xcnt)) {
				codbar=document.getElementById("codpat"+ xcnt).value;
				if (codbar==xcod) {
					alert("EL CODIGO "+ xcod +" YA ESTA EN LA LISTA DE BIENES A TRANSFERIR");
					document.getElementById("codpatri").value="";
					return false;
				}
			}
		}



		$.ajax({
			type: "GET",
			url: "movs_inventario_registro.php",
			cache: false,
			data: { codpatribusca: xcod }
		}).done(function( respuesta2 ) {
			var html2=respuesta2.trim();
			if (html2==":(") {
				alert("EL CODIGO PATRIMONIAL "+ xcod +" NO ES VALIDO");
				document.getElementById("codpatri").value="";
				return false;
			} else {

			var respuesta = html2.split("|");
			var tipodoc="";
			cantit=cantit+1;

			document.getElementById("cantreg").value=cantit;

			if (tpmov=="I" && respuesta[5]==1) {
				alert("EL CODIGO PATRIMONIAL "+ xcod +"\nDESCRIPCION: "+respuesta[1]+"\nYA ESTA EN ALMACEN Y NO SE PUEDE REGISTRAR SU INGRESO NUEVAMENTE");
				document.getElementById("codpatri").value="";
				return false;
			}
			if (tpmov=="S" && respuesta[5]==0) {
				alert("EL CODIGO PATRIMONIAL "+ xcod +"\nDESCRIPCION: "+respuesta[1]+"\nYA HA SIDO TRANSFERIDO Y NO SE PUEDE REGISTRAR SU SALIDA NUEVAMENTE");
				document.getElementById("codpatri").value="";
				return false;
			}



			row = "<tr id='tr" + cantit + "'>" +
			  "<td>" + cantit + " </td>" +
			  "<td>" + xcod + " <input name='codpat" + cantit + "' id='codpat" + cantit + "' type='hidden' value='" + xcod + "' >" +
			  "<input name='codbar" + cantit + "' id='codbar" + cantit + "' type='hidden' value='" + respuesta[0] + "' ></td>" +
			  "<td>" + respuesta[1] + "</td>" +
			  "<td>" + respuesta[2] + "</td>" +
			  "<td>" + respuesta[3] + "</td>" +
			  "<td>" + respuesta[4] + "</td>" +
			  "<td>" +
			  "<select name='estado"+cantit+"' id='estado"+cantit+"'>"+
            	"<option value=''><p>Selec...</p></option>"+
            	"<option value='B'><p>Bueno</p></option>"+
            	"<option value='R'><p>Regular</p></option>"+
            	"<option value='M'><p>Malo</p></option>"+
              "</select>"+
			  "</td>" +
			  "<td align='center'><button onclick='eliitem("+ cantit +");' type='button'><img src='img/icons/delete.svg' width='20px'></button></td>" +
			"</tr>";
			$('#detalledoc').append(row);

				document.getElementById("codpatri").value="";

			}
		});
    }
}



</script>