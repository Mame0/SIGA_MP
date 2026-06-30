<?
//classes/TCPDF/examples/personal_fotocheck.php
	require_once 'include/cabecera.php';
	require_once 'classes/Html.class.php';
	require_once 'classes/Db.class.php';
	$Db = new Db();

	if($_POST['agre_depe'] AND $_POST['codi_orig'] AND $_POST['codi_dest'])
	{
		$fdig=date(YmdHis);
		
	    $result=$Db->query("insert into mp_mpar_asignacion values('".$_POST['codi_orig']."','".$_POST['codi_dest']."')");

	}
	if($_POST['codi_orig_elim'] AND $_POST['codi_dest_elim'])
	{
	    $result=$Db->query("delete from mp_mpar_asignacion where codi_orig='".$_POST['codi_orig_elim']."' AND codi_dest='".$_POST['codi_dest_elim']."'");
	    //echo"delete from mp_mpar_asignacion where codi_orig='".$_POST['codi_orig']."' AND codi_dest='".$_POST['codi_dest']."'";
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
			function f_eliminar(orig,dest)
			{
				document.form.codi_orig_elim.value=orig;
				document.form.codi_dest_elim.value=dest;
				document.form.action='mpartes_mantenimiento_asignacion.php';
				document.form.target="";
				document.form.submit();
			}
			function f_agregar()
			{
			    if(document.getElementById("codi_orig").selectedIndex==0 || document.getElementById("codi_dest").selectedIndex==0)
			    {
			        alert('ERROR: Seleccione Mesa de Partes y Despacho Fiscal');   
			        return false;
			    }
			    else
			    {
				    document.form.agre_depe.value='1';
    				document.form.action='mpartes_mantenimiento_asignacion.php';
	    			document.form.target="";
		    		document.form.submit();
			    }
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
	<center><h2 style="color:#073A6B">REPORTE DE ASIGNACIONES</h2></center>
		<form name="form" method="post">
			<input type=hidden name="agre_depe">
			<input type=hidden name="codi_orig_elim">
			<input type=hidden name="codi_dest_elim">
			<input type=hidden name="busc_pagi_actu" value="<?=$_POST['busc_pagi_actu']?>">
<?
	$html=new htmlclass;

/*
	$arra_options_orig[0]="<- Seleccione ->";
    $result=$Db->query("select b.codi_depe,b.nomb_depe,b.abre_depe from mp_mpar_mpartes a,mp_admi_depe b where a.codi_depe=b.codi_depe order by nomb_depe");
    foreach($result as $rows)
            $arra_options_orig[$rows['codi_depe']]=$rows['abre_depe'];
    
    $arra_options_dest[0]="<- Seleccione ->";
    $result=$Db->query("select b.codi_depe,b.nomb_depe,b.abre_depe from mp_mpar_despachos a,mp_admi_depe b where a.codi_depe=b.codi_depe order by nomb_depe");
    foreach($result as $rows)
            $arra_options_dest[$rows['codi_depe']]=$rows['abre_depe'];

	echo"<main style='column-count:3;'>";
	echo $html->put_title_demand("Seleccione Fecha");
	echo $html->put_select("Mesa&nbsp;de&nbsp;Partes",'codi_orig',$arra_options_orig,$_POST['codi_espe'],"");
	echo $html->put_select("Despacho&nbsp;Fiscal",'codi_dest',$arra_options_dest,$_POST['busq_tipo'],"");
	//echo $html->put_text('text','Código',"Ingrese Carpeta",'codi_mpar_agre',$_POST['codi_mpar_agre'],'','50','');
	//echo $html->put_text('text',"<a href=\"javascript:f_buscar()\">Click&nbsp;<u>AQUI</u>&nbsp;para&nbsp;Buscar</a>","Ingrese datos (Comod&iacute;n: %)",'busq_dato',$_POST['busq_dato'],'','100','');
	echo $html->put_button_colum("&nbsp;","Agregar &raquo;","return f_agregar()");
	echo"</main>";
*/

//if($_POST['text_busc'])
//{
    echo"<main style='column-count:1;'>";
    echo $html->put_title_demand("Asignaciones Realizadas");
    
	$result1=$Db->query("select distinct b.codi_depe,b.nomb_depe,b.abre_depe from mp_mpar_carpetas a,mp_admi_depe b where a.depe_mpar=b.codi_depe order by nomb_depe");
	echo"<BR><table border=0>";
	$separador="<td width=1%>&nbsp;&nbsp;&nbsp;&nbsp;</td><td width=1%>&nbsp;&nbsp;&nbsp;&nbsp;</td>";
	$colu_inic=20;
	foreach($result1 as $rows1)
	{   
	    $colu=$colu_inic-0;
	    //if(strlen($rows1['abre_depe'])>70)  $rows1['abre_depe']=substr($rows1['abre_depe'],0,70).'...'; 
	    echo"<tr><td widht=1%><img src=\"img/icons/book-open.svg\" width=\"20\"></td><td colspan=$colu style=\"font-size:small\"><b>".$rows1['abre_depe']."</td></tr>";
		$result2=$Db->query("select distinct b.codi_depe,b.nomb_depe,b.abre_depe from mp_mpar_carpetas a,mp_admi_depe b where a.codi_depe=b.codi_depe AND a.depe_mpar='".$rows1['codi_depe']."' order by nomb_depe");
		$tota=0;
		foreach($result2 as $rows2)
		{
		    $tota++;
		    $colu=$colu_inic-1;
		    //if(strlen($rows2['abre_depe'])>70)  $rows2['abre_depe']=substr($rows2['abre_depe'],0,70).'...';
		    echo"<tr width=1%>$separador<td widht=1%><img src=\"img/icons/home.svg\" width=\"20\"></td><td colspan=$colu style=\"font-size:small\">".$rows2['abre_depe']."</td></tr>";
		    $result3=$Db->query("select a.codi_pers,b.nomb_pers,b.appa_pers,b.apma_pers,count(*) cant_asig from mp_mpar_carpetas a,mp_maes_personal b where a.codi_pers=b.iden_pers AND a.codi_depe='".$rows2['codi_depe']."' group by a.codi_pers order by codi_carg desc");
		    foreach($result3 as $rows3)
		    {
		        $colu=$colu_inic-2;
		        echo"<tr width=1%>$separador$separador<td width=1%><img src=\"img/icons/user.svg\" width=\"20\"></td><td colspan=$colu style=\"font-size:small\">".$rows3['nomb_pers'].' '.$rows3['appa_pers'].' '.$rows3['apma_pers']." <font color=red><b>(".$rows3['cant_asig'].")</td></tr>";
		    }
		}
		if($tota==0)
		    echo"<tr><td width=1%>&nbsp;&nbsp;&nbsp;&nbsp;</td><td colspan=$colu style=\"font-size:small\"><i><font color=silver>No existen Despachos Fiscales Asignados</td></tr>";
	}
	echo"</table>";
	echo"</main>";

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
                                                <button class=\"button_foot\" onclick=\"f_nuevo()\">Guardar</button>
                                        </div>
                                </div>
                        </div>
                ";
*/
	//}
?>
<center>
	</form>
	</body>
</html>
