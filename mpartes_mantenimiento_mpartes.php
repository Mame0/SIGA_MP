<?
//classes/TCPDF/examples/personal_fotocheck.php
	require_once 'include/cabecera.php';
	require_once 'classes/Html.class.php';
	require_once 'classes/Db.class.php';
	$Db = new Db();

	if($_POST['actu_depe'])
	{
		$fdig=date(YmdHis);
		
		$result=$Db->query("delete from mp_mpar_mpartes");
	
	    $result=$Db->query("select * from mp_admi_depe");
        foreach($result as $rows)	
		{
		    $vari="chec_depe_".$rows['codi_depe'];
		    if($_POST[$vari])
		    {
		       // echo"<HR>$vari";
			    $result=$Db->insert('mp_mpar_mpartes',['codi_depe'=>$rows['codi_depe']]);
		    }
		}

	}
	
	
    $result=$Db->query("select * from mp_mpar_mpartes");
    foreach($result as $rows)
        $arra_depe[$rows['codi_depe']]='checked';
        //echo"<HR>".$_SESSION['codi_depe']."- $nomb_depe<HR>";
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
			function f_editar(codi)
			{
				document.form.codi_docu.value=codi;
				document.form.action='jurisprudencia_registro.php';
				document.form.target="";
				document.form.submit();
			}
			function f_nuevo()
			{
				document.form.actu_depe.value='1';
				document.form.action='mpartes_mantenimiento_mpartes.php';
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
	<center><h2 style="color:#073A6B">AGREGAR MESA DE PARTES</h2></center>
		<form name="form" method="post">
			<input type=hidden name="actu_depe">
			<input type=hidden name="busc_pagi_actu" value="<?=$_POST['busc_pagi_actu']?>">
<?
	$html=new htmlclass;

	$arra_options_espe[0]="<- Todas ->";
        $result=$Db->select('mp_maes_jurisprudencia_especialidad', '', '', '', ['x_nombre'=>'ASC']);
        foreach($result as $rows)
                $arra_options_espe[$rows['n_codigo']]=$rows['x_nombre'];

$busc_tipo=1;	//1 nombre - 2 ndoc - 3 esca - 4 marc
$arra_options_tipo=array(1=>"Por Apellidos y Nombres","DNI","Escalafon","C&oacute;digo de Marcado");

	echo"<main style='column-count:1;'>";
	echo $html->put_title_demand("Seleccione Dependencias de Mesa de Partes");
	//echo $html->put_select("Especialidad",'codi_espe',$arra_options_espe,$_POST['codi_espe'],"");
	//echo $html->put_select("Criterios",'busq_tipo',$arra_options_tipo,$_POST['busq_tipo'],"");
	//echo $html->put_text('text','Código',"Ingrese Carpeta",'codi_mpar_agre',$_POST['codi_mpar_agre'],'','50','');
	//echo $html->put_text('text',"<a href=\"javascript:f_buscar()\">Click&nbsp;<u>AQUI</u>&nbsp;para&nbsp;Buscar</a>","Ingrese datos (Comod&iacute;n: %)",'busq_dato',$_POST['busq_dato'],'','100','');
	//echo $html->put_button_colum("&nbsp;","Ingresar Carpeta &raquo;","return check_buscar()");
	//echo"</main>";

//if($_POST['text_busc'])
//{
	$busc_item_pagi=40;      //cantidad de items por pagina

	$result=$Db->query("select * from mp_jurisprudencia_documento where nomb_docu like '%:m_busq%'",[':m_busq'=>$_POST['text_busc']]);

	$result1=$Db->query("select * from mp_admi_depe where codi_padr=0 AND esta_depe=1 order by nomb_depe");
	echo"<BR><table border=0>";
	$separador="<td width=1%><center>I</td><td width=1%>&nbsp;&nbsp;&nbsp;&nbsp;</td>";
	$colu_inic=20;
	foreach($result1 as $rows1)
	{   
	    $colu=$colu_inic-0;
	    if(strlen($rows1['abre_depe'])>70)  $rows1['abre_depe']=substr($rows1['abre_depe'],0,70).'...'; 
	    echo"<tr><td><input type=checkbox name=\"chec_depe_".$rows1['codi_depe']."\" ".$arra_depe[$rows1['codi_depe']]."></td><td colspan=$colu style=\"font-size:small\">".$rows1['abre_depe']."</td></tr>";
		$result2=$Db->query("select * from mp_admi_depe where codi_padr='".$rows1['codi_depe']."' AND esta_depe=1 order by nomb_depe");
		foreach($result2 as $rows2)
		{
		    $colu=$colu_inic-1;
		    if(strlen($rows2['abre_depe'])>70)  $rows2['abre_depe']=substr($rows2['abre_depe'],0,70).'...';
		    echo"<tr width=1%>$separador<td widht=1%><input type=checkbox name=\"chec_depe_".$rows2['codi_depe']."\" ".$arra_depe[$rows2['codi_depe']]."></td><td colspan=$colu style=\"font-size:small\">".$rows2['abre_depe']."</td></tr>";
		    $result3=$Db->query("select * from mp_admi_depe where codi_padr='".$rows2['codi_depe']."' AND esta_depe=1 order by nomb_depe");
	    	foreach($result3 as $rows3)
    		{
		        $colu=$colu_inic-2;
		        if(strlen($rows3['abre_depe'])>70)  $rows3['abre_depe']=substr($rows3['abre_depe'],0,70).'...';
		        echo"<tr>$separador$separador<td width=1%><input type=checkbox name=\"chec_depe_".$rows3['codi_depe']."\" ".$arra_depe[$rows3['codi_depe']]."></td><td width=100% colspan=$colu style=\"font-size:small\">".$rows3['abre_depe']."</td></tr>";
		        $result4=$Db->query("select * from mp_admi_depe where codi_padr='".$rows3['codi_depe']."' AND esta_depe=1 order by nomb_depe");
	    	    foreach($result4 as $rows4)
    		    {
		            $colu=$colu_inic-3;
		            if(strlen($rows4['abre_depe'])>70)  $rows4['abre_depe']=substr($rows4['abre_depe'],0,70).'...';
    		        echo"<tr>$separador$separador$separador<td width=1%><input type=checkbox name=\"chec_depe_".$rows4['codi_depe']."\" ".$arra_depe[$rows4['codi_depe']]."></td><td width=100% colspan=$colu style=\"font-size:small\">".$rows4['abre_depe']."</td></tr>";
    		        $result5=$Db->query("select * from mp_admi_depe where codi_padr='".$rows4['codi_depe']."' AND esta_depe=1 order by nomb_depe");
    	    	    foreach($result5 as $rows5)
        		    {
		                $colu=$colu_inic-4;
		                if(strlen($rows5['abre_depe'])>70)  $rows5['abre_depe']=substr($rows5['abre_depe'],0,70).'...';
    		            echo"<tr>$separador$separador$separador$separador<td width=1%><input type=checkbox name=\"chec_depe_".$rows5['codi_depe']."\" ".$arra_depe[$rows5['codi_depe']]."></td><td width=100% colspan=$colu style=\"font-size:small\">".$rows5['abre_depe']."</td></tr>";
    		            
    		            $result6=$Db->query("select * from mp_admi_depe where codi_padr='".$rows5['codi_depe']."' AND esta_depe=1 order by nomb_depe");
        	    	    foreach($result6 as $rows6)
            		    {
    		                $colu=$colu_inic-5;
    		                if(strlen($rows6['abre_depe'])>70)  $rows6['abre_depe']=substr($rows6['abre_depe'],0,70).'...';
        		            echo"<tr>$separador$separador$separador$separador$separador<td width=1%><input type=checkbox name=\"chec_depe_".$rows6['codi_depe']."\" ".$arra_depe[$rows6['codi_depe']]."></td><td width=100% colspan=$colu style=\"font-size:small\">".$rows6['abre_depe']."</td></tr>";
            		    }
    		            
    		            
    		            
	    	        }
	    	    }
		    }
		}
	}
	echo"</table>";
	echo"</main>";


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
	//}
?>
<center>
	</form>
	</body>
</html>
