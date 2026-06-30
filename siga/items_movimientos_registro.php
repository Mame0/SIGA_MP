<?
	require_once 'include/cabecera.php';
	require_once 'classes/Html.class.php';
	require_once 'classes/Db.class.php';
	$Db = new Db();

	$fdig=date("YmdHis");

	if($_POST['guardar_pago'])
	{
		$porciones = explode("|", $_POST['codi_item']);
		$coditem = $porciones[0];
		$nrocont = $porciones[1];


		$fdig=date(YmdHis);
		if($_POST['codi_movitem'])
		{
			$result=$Db->update('mp_movs_item',['codi_item'=>$coditem,'codi_loca'=>$_POST['codi_loca'],
			'nro_contr'=>$nrocont,'cicl_fact'=>$_POST['cicl_fact'],'nro_reci'=>$_POST['nro_reci'],'fech_vcto'=>$_POST['fech_vcto'],
			'fech_pago'=>$_POST['fech_pago'],'mont_pago'=>$_POST['mont_pago'] ]  , ['codi_movitem'=>$_POST['codi_movitem']]  );
		}
		else
		{
			$result=$Db->insert('mp_movs_item',['codi_item'=>$coditem,'codi_loca'=>$_POST['codi_loca'],
			'nro_contr'=>$nrocont,'cicl_fact'=>$_POST['cicl_fact'],'nro_reci'=>$_POST['nro_reci'],'fech_vcto'=>$_POST['fech_vcto'],
			'fech_pago'=>$_POST['fech_pago'],'mont_pago'=>$_POST['mont_pago'] ] );
			$_POST['codi_movitem']=$Db->lastInsertId();
		}

		echo"
			<!--<script>alert('".CONST_MENS_REG_OK."');</script>-->
                        <html><body>
                                <form name=\"form\" method=post action=\"items_movimientos.php\">
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
	$result_documento=$Db->select('mp_movs_item', ['codi_movitem'=>$_POST['codi_movitem']], '', '', '');
	$_POST['codi_item']=$result_documento[0]['codi_item'];
	$_POST['codi_loca']=$result_documento[0]['codi_loca'];
	$_POST['nro_contr']=$result_documento[0]['nro_contr'];
	$_POST['cicl_fact']=$result_documento[0]['cicl_fact'];
	$_POST['nro_reci']=$result_documento[0]['nro_reci'];
	$_POST['fech_vcto']=$result_documento[0]['fech_vcto'];
	$_POST['fech_pago']=$result_documento[0]['fech_pago'];
	$_POST['mont_pago']=$result_documento[0]['mont_pago'];

?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8" />
		<title>ITEMS CONTRATADOS</title>
		<link rel="stylesheet" href="css/forms_demanda.css" />
		<link rel="stylesheet" href="css/forms_foot.css" />
		<link rel="stylesheet" href="css/forms_column.css" />
		<SCRIPT language="JavaScript" src="js/cadenas.js"></SCRIPT>
		<SCRIPT language="JavaScript" src="js/denuncia.js"></SCRIPT>
		<script>
			function f_guardar()
			{
					if(document.form.codi_loca.value==0) {
						alert('Seleccione local');
						document.form.codi_loca.focus();
						return false;
					}
					if(document.form.codi_item.value==0) {
						alert('Seleccione item a pagar');
						document.form.codi_item.focus();
						return false;
					}
					if(document.form.cicl_fact.value=='') {
						alert('Ingrese ciclo de facturacion');
						document.form.cicl_fact.focus();
						return false;
					}
					if(document.form.nro_reci.value=='') {
						alert('Ingrese Nro Recibo');
						document.form.nro_reci.focus();
						return false;
					}
					if(document.form.fech_vcto.value=='') {
						alert('Ingrese la fecha de vencimiento');
						document.form.fech_vcto.focus();
						return false;
					}
					if(document.form.fech_pago.value=='') {
						alert('Ingrese la fecha de pago');
						document.form.fech_pago.focus();
						return false;
					}
					if(document.form.mont_pago.value=='') {
						alert('Ingrese el monto de pago');
						document.form.mont_pago.focus();
						return false;
					}

					if(confirm('Seguro que desea Guardar'))
					{
						document.form.guardar_pago.value='1';
						document.form.submit();
					}
					else
						return false;
			}
			function f_cancelar()
			{
				document.form.action='items_movimientos.php';
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

	$arra_options_loca[0]="<- Seleccione ->";
	$result=$Db->query("select distinct mp_admi_loca.codi_loca, mp_admi_loca.nom1_loca
	from mp_maes_item_contr inner join mp_admi_loca on mp_maes_item_contr.codi_loca=mp_admi_loca.codi_loca
	order by nom1_loca ");
	foreach($result as $rows) {
			$arra_options_loca[$rows['codi_loca']]=$rows['nom1_loca'];
	}


	$cantitem=0;
	$arra_options_itemcontr[0][1]="";
	$result=$Db->query("select mp_maes_item_contr.*, mp_maes_item.x_nombre
	from mp_maes_item_contr inner join mp_maes_item on mp_maes_item_contr.codi_item=mp_maes_item.n_codigo order by codi_loca, x_nombre");
	foreach($result as $rows) {
		$cantitem++;
			$arra_options_itemcontr[$cantitem][1]=$rows['codi_item'];
			$arra_options_itemcontr[$cantitem][2]=$rows['codi_loca'];
			$arra_options_itemcontr[$cantitem][3]=$rows['x_nombre'];
			$arra_options_itemcontr[$cantitem][4]=$rows['nro_contr'];
	}


	if($_POST['codi_movitem'])
		echo"Editar Informaci&oacute;n - Item Contratado : <i>" . $arra_options_itemcontr[$_POST['codi_item']] . "</i>";
	else
		echo"REGISTRAR NUEVOS SERVICIOS CONTRATADOS";
?>
	</h2></center>
		<form name="form" method="post" ENCTYPE="multipart/form-data" autocomplete="off">
			<input type=hidden name="guardar_pago">
			<input type=hidden name="codi_movitem" value="<?=$_POST['codi_movitem']?>">
			<input type=hidden name="busq_pagi_actu" value="<?=$_POST['busq_pagi_actu']?>">

<?
	$html=new htmlclass;


	echo "<main style='column-count:2;'>";
	echo $html->put_select("Local",'codi_loca',$arra_options_loca,$_POST['codi_loca'],' onchange=" muestraitem(); " ');
	echo $html->put_select("Item&nbsp;/&nbsp;Servicio",'codi_item',$arra_options_itemcontr,$_POST['codi_item'],'');
	echo "</main>";

	echo "<main style='column-count:3;'>";
	echo $html->put_text('text',"Ciclo&nbsp;Facturaci&oacute;n","",'cicl_fact',$_POST['cicl_fact'],'','30','');
	echo $html->put_text('text',"Nro&nbsp;Recibo","",'nro_reci',$_POST['nro_reci'],'','20','');
	echo $html->put_text('date',"Fec.&nbsp;Vcto","",'fech_vcto',$_POST['fech_vcto'],'','10','');
	echo "</main>";


	echo "<main style='column-count:3;'>";
	echo $html->put_text('date',"Fec.&nbsp;pago","",'fech_pago',$_POST['fech_pago'],'','10','');
	echo $html->put_text('number',"Monto&nbsp;Pago","",'mont_pago',$_POST['mont_pago'],'','10',' min="0" step=".01" ');
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


<script>
var listaitemcontr=<?php echo json_encode($arra_options_itemcontr); ?>;
var cantiitems=<?php echo $cantitem; ?>;

function muestraitem() {
	var coddep = document.getElementById("codi_loca").value;

	document.getElementById("codi_item").innerHTML= "";

	var x = document.getElementById("codi_item");
	var option = document.createElement("option");
	option.text = "<- Seleccione ->";
	option.value = 0;
	x.add(option);

	for(i = 1; i <= cantiitems; i++) {
		if (listaitemcontr[i][2]==coddep ) {
			var option = document.createElement("option");
			option.text = listaitemcontr[i][3] + " - " + listaitemcontr[i][4] ;
			option.value = listaitemcontr[i][1] + "|" + listaitemcontr[i][4];
			x.add(option);
		}
	}

}
</script>

<?
if ($_POST['codi_loca']!=0) {
?>
<script>
muestraitem();
document.getElementById("codi_item").value=<? echo $_POST['codi_item'] . "|" . $_POST['nro_contr']; ?>;

alert ("<? echo $_POST['codi_item'] . "|" . $_POST['nro_contr']; ?>");
</script>
<?
}
?>
