<?
//classes/TCPDF/examples/personal_fotocheck.php
	require_once 'include/cabecera.php';
	require_once 'classes/Html.class.php';
	require_once 'classes/Db.class.php';
	$Db = new Db();
	if(!isset($_POST['busc_pagi_actu']))
		$_POST['busc_pagi_actu']=1;
	function formato_expediente($expe)
	{
	    $expe=substr($expe,4,5).'-'.substr($expe,0,4);
	    return $expe;
	}
	function size_as_kb($yoursize)
	{
		if($yoursize < 1024) {
			return "{$yoursize} bytes";
		} elseif($yoursize < 1048576) {
			$size_kb = round($yoursize/1024);
			return "{$size_kb} KB";
		} else {
			$size_mb = round($yoursize/1048576, 1);
		return "{$size_mb} MB";
		}
	}
	function get_client_ip()
	{
        $ipaddress = '';
        if (getenv('HTTP_CLIENT_IP'))
            $ipaddress = getenv('HTTP_CLIENT_IP');
        else if(getenv('HTTP_X_FORWARDED_FOR'))
            $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
        else if(getenv('HTTP_X_FORWARDED'))
            $ipaddress = getenv('HTTP_X_FORWARDED');
        else if(getenv('HTTP_FORWARDED_FOR'))
            $ipaddress = getenv('HTTP_FORWARDED_FOR');
        else if(getenv('HTTP_FORWARDED'))
           $ipaddress = getenv('HTTP_FORWARDED');
        else if(getenv('REMOTE_ADDR'))
            $ipaddress = getenv('REMOTE_ADDR');
        else
            $ipaddress = 'UNKNOWN';
        return $ipaddress;
	}
	$dire_auop=get_client_ip();
	$dire_auop_modi="dire_".$dire_auop;
	
	$hoy=date("Ymd");
	//echo"select * from mp_cons_audi_oper where iden_oper='$_SESSION[iden_oper]' AND fdig_auop like '$hoy%'";
	$result=$Db->query("select * from mp_cons_audi_oper where iden_oper='$_SESSION[iden_oper]' AND fdig_auop like '$hoy%'");
	$arra_arch_vist['prueba']='';
	foreach($result as $rows)
	{
	    //echo"<HR>".$rows['anno_audi'].$rows['mess_audi'].$rows['expe_audi'].$rows['audi_audi'].$rows['arch_audi']."<HR>";
		$arra_arch_vist[$rows['iden_auop']]=$rows['anno_audi'].$rows['mess_audi'].$rows['expe_audi'].$rows['audi_audi'].$rows['arch_audi'];
	}

	//obtener maximo y minimo
	$result=$Db->query("select min(CONCAT(anno_audi,'/',mess_audi)) as mini from mp_cons_audi");
	foreach($result as $rows)
		$fech_mini=$rows['mini'];
	$result=$Db->query("select max(CONCAT(anno_audi,'/',mess_audi)) as maxi from mp_cons_audi");
	foreach($result as $rows)
		$fech_maxi=$rows['maxi'];
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
			function f_buscar()
			{
				document.form.action='';
				document.form.target="";
				document.form.submit();
			}
			function f_buscar_expediente()
			{
			    if(document.form.busq_anno.selectedIndex==0)
			    {
			        alert('ERROR: Seleccione Año');
			        document.form.busq_anno.focus();
			        return false;
			    }
			    else
			    {
    			    if(document.form.busq_expe.value=='')
	    		    {
		    	        alert('ERROR: Ingrese Expediente');
			            document.form.busq_expe.focus();
			            return false;
			        }
    			    else
	    		    {
	    		        document.form.expe_audi.value='';
		    		    document.form.action='';
			    	    document.form.target="";
				        document.form.submit();
			        }
			    }
			}
			function f_expediente(expe)
			{
			    document.form.expe_audi.value=expe;
			    document.form.submit();
			}
			function f_seleccion_todo(source)
			{
		                checkboxes = document.getElementsByName('chec_arch');
		                for(var i=0, n=checkboxes.length;i<n;i++)
		                {
		                    checkboxes[i].checked = source.checked;
		                }
			}
			function f_descargar(iden,arch,hidd)
			{
				if(eval('document.form.'+hidd+'.value')=='0')
				{
					if(confirm('Se va a registrar su acceso al archivo '+arch+'\nDesea continuar la descarga?'))
					{
						document.form.iden_audi.value=iden;
						document.form.regi_audi.value='1';
						document.form.desc_audi.value='1';
						eval('document.form.'+hidd+'.value=1');
						document.form.action='consulta_audios_descargar.php';
						document.form.target='';
						document.form.submit();
					}
				}
				else
				{
					document.form.iden_audi.value=iden;
					document.form.regi_audi.value='0';
					document.form.desc_audi.value='1';
					eval('document.form.'+hidd+'.value=1');
					document.form.action='consulta_audios_descargar.php';
					document.form.target='';
					document.form.submit();
					
					//window.open(acti, 'Download');
				}
			}
			function f_descargar_seleccion()
			{
				nume_sele=0;
				cade_sele='0';
				checkboxes = document.getElementsByName('chec_arch');
				for(var i=0, n=checkboxes.length;i<n;i++)
				{
					//checkboxes[i].checked = source.checked;
					//alert(i+' : '+checkboxes[i].value+' : '+checkboxes[i].checked);
					if(checkboxes[i].checked==true)
					{
						cade_sele=cade_sele+','+checkboxes[i].value;
						nume_sele++;
					}
				}
				if(nume_sele>0)
				{
					if(confirm('Se va a registrar su acceso al archivo '+arch+'\nDesea continuar la descarga?'))
					{
						document.form.cade_sele.value=cade_sele;
						document.form.action='consulta_audios_descarga_masiva.php';
						document.form.submit();
					}
					else
						return false;
				}
				else
				{
					alert('ERROR: No ha seleccionado archivos');
					return false;
				}
			}
			//function f_advertencia(sede,anno,mess,expe,audi,arch,boto,hidd,carp)
			function f_advertencia(iden,arch,boto,hidd)
			{
				if(eval('document.form.'+hidd+'.value')=='0')
				{
					document.getElementById(boto).pause();
					if(confirm('Se va a registrar su acceso al archivo '+arch+'\nDesea continuar la descarga?'))
					{
						document.form.iden_audi.value=iden;
						document.form.regi_audi.value='1';
						document.form.desc_audi.value='0';
						eval('document.form.'+hidd+'.value=1');
						document.getElementById(boto).play();

						document.form.action='consulta_audios_descargar.php';
						document.form.target='frame_registro';
						document.form.submit();
					}
				}
			}
			function f_reiniciar()
			{
			    document.form.arch_audi.value='';
			    document.form.expe_audi.value='';
			    document.form.busq_anno.selectedIndex='0';
			    document.form.busq_expe.value='';
				document.form.action='';
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
	<center><h4 style="color:#073A6B"><B>AUDIOS Y ACTAS DE AUDIENCIAS DEL P.J.<BR>(Desde <?=$fech_mini?> hasta <?=$fech_maxi?>)</B></h4></center>
		<form name="form" method="post">
			<input type=hidden name="codi_pers">
			<input type=hidden name="todo_chek">
			<input type=hidden name="iden_audi">
			<input type=hidden name="regi_audi">
			<input type=hidden name="desc_audi">
			<input type=hidden name="arch_audi">
			<input type=hidden name="cade_sele">
			<input type=hidden name="iden_oper" value="<?=$_SESSION['iden_oper']?>">
			<input type=hidden name="ndoc_oper" value="<?=$_SESSION['ndoc_oper']?>">
			<input type=hidden name="expe_audi" value="<?=$_POST['expe_audi']?>">
			<input type=hidden name="file">
			<input type=hidden name="dire_auop" value="<?=$dire_auop?>">
			<input type=hidden name="busc_pagi_actu" value="<?=$_POST['busc_pagi_actu']?>">
			<input type=hidden name="codi_form" value="<?=$_POST['codi_form']?>">

<?
	$html=new htmlclass;
	$arra_options_anno[0]="<- Seleccione Año ->";
	$result=$Db->query("select distinct SUBSTRING(expe_audi,1,4) as anno from mp_cons_audi where esta_audi='1' order by anno");
	foreach($result as $rows)
		$arra_options_anno[$rows['anno']]=$rows['anno'];
	
	$arra_options_mess[0]="<- Seleccione Mes ->";
	$arra_options_mess[1]="Enero";
	$arra_options_mess[2]="Febrero";
	$arra_options_mess[3]="Marzo";
	$arra_options_mess[4]="Abril";
	$arra_options_mess[5]="Mayo";
	$arra_options_mess[6]="Junio";
	$arra_options_mess[7]="Julio";
	$arra_options_mess[8]="Agosto";
	$arra_options_mess[9]="Setiembre";
	$arra_options_mess[10]="Octubre";
	$arra_options_mess[11]="Noviembre";
	$arra_options_mess[12]="Diciembre";
	
/*
	$arra_options_expe[0]="<- Seleccione Expediente ->";
	$result=$Db->query("select distinct expe_audi from mp_cons_audi where sede_audi='".$_POST['codi_sede']."' AND anno_audi='".$_POST['busq_anno']."' AND mess_audi='".str_pad($_POST['busq_mess'],2,'0',STR_PAD_LEFT)."' AND esta_audi='1' order by expe_audi");
	foreach($result as $rows)
		$arra_options_expe[$rows['expe_audi']]=substr($rows['expe_audi'],4,6)."-".substr($rows['expe_audi'],0,4);
	
	$arra_options_audi[0]="<- Seleccione Audio ->";
	$result=$Db->query("select distinct audi_audi from mp_cons_audi where sede_audi='".$_POST['codi_sede']."' AND anno_audi='".$_POST['busq_anno']."' AND mess_audi='".str_pad($_POST['busq_mess'],2,'0',STR_PAD_LEFT)."' AND expe_audi='".$_POST['busq_expe']."' AND esta_audi='1' order by expe_audi");
	foreach($result as $rows)
		$arra_options_audi[$rows['audi_audi']]=$rows['audi_audi'];
*/

	echo"<main>";
	echo $html->put_title_demand("Formulario de B&uacute;squeda");
	echo $html->put_select("A&ntilde;o",'busq_anno',$arra_options_anno,$_POST['busq_anno'],"");
	echo $html->put_text('text',"Expediente","Ingrese Expediente",'busq_expe',$_POST['busq_expe'],'','15','');
	echo $html->put_button_colum("&nbsp;","Buscar Expediente &raquo;","return f_buscar_expediente()");
    echo"</main>";
if($_POST['busq_expe'] AND !$_POST['expe_audi'])
{
    $result_pagi=$Db->query("select distinct expe_audi from mp_cons_audi where SUBSTRING(expe_audi,1,9) like '".$_POST['busq_anno']."%".$_POST['busq_expe']."%' AND esta_audi='1' order by expe_audi");
	echo"<div style=\"width:90%;max-width:800px;margin:auto;\">";
	echo $html->put_title_demand("Expedientes Encontrados");

	$result=$Db->query("select * from mp_cons_audi_sede");
	foreach($result as $rows)
		$arra_sede[$rows['codi_sede']]=$rows['nomb_sede'];

	$head=['1'=>"Nº",'2'=>"AÑO",'3'=>"EXPEDIENTE",'4'=>"PROVINCIA",'5'=>"SEDE",'6'=>""];
	echo $html->put_table_responsive_open();
	
	echo $html->put_table_responsive_header($head);
	$cont=0;
	foreach($result_pagi as $rows)
	{
		$cont++;
		$data=[	'1'=>$cont,
		    	'2'=>substr($rows['expe_audi'],0,4),
		    	'3'=>substr($rows['expe_audi'],4,5),
		    	'4'=>"AREQUIPA",
		    	'5'=>$arra_sede[substr($rows['expe_audi'],11,2)],
		    	'6'=>"<a href=\"javascript:f_expediente('$rows[expe_audi]')\"><img src=\"img/icons/eye.svg\" width=\"20\">",
		];
		echo $html->put_table_responsive_data($head,$data);
	}

	echo $html->put_table_responsive_close();
}
if($_POST['expe_audi'])
{
    echo"<div style=\"width:90%;max-width:800px;margin:auto;\">";
    echo $html->put_title_demand("Expediente: ".formato_expediente($_POST['expe_audi']));
    
	//$head=['1'=>"Nº",'2'=>"AÑO-MES",'3'=>"AUDIO",'4'=>"ARCHIVO",'5'=>"<input type=\"checkbox\" name=\"todo_chec\" id=\"todo_chec\" onclick=\"f_seleccion_todo(this)\">"];
	$head=['1'=>"Nº",'2'=>"AÑO-MES",'3'=>"AUDIO",'4'=>"ARCHIVO",'5'=>"DESCARGAR"];
	echo $html->put_table_responsive_open();
	echo $html->put_table_responsive_header($head);
    $result_pagi=$Db->query("select iden_audi,anno_audi,mess_audi,expe_audi,audi_audi,arch_audi from mp_cons_audi where expe_audi='".$_POST['expe_audi']."' AND esta_audi='1' order by anno_audi,mess_audi,audi_audi,SUBSTRING(arch_audi,-3,3),arch_audi");
    $cont=0;
    $con2=0;
    $flag_audi='';
    $flag_fech='';
    foreach($result_pagi as $rows)
    {
        $con2++;
        if(substr($rows['arch_audi'],-3)=='mp3' OR substr($rows['arch_audi'],-3)=='MP3')
            $icon="audio.svg";
        else
            $icon="document.svg";
        
        $most_audi=$rows['audi_audi'];
        if($flag_audi==$rows['audi_audi'])
        {
            $most_audi='';
            $most_cont='';
        }
        else
        {
            $cont++;
            $most_cont=$cont;
            $flag_audi=$rows['audi_audi'];
        }
        
        $colo='';
        $cade=$rows['anno_audi'].$rows['mess_audi'].$rows['expe_audi'].$rows['audi_audi'].$rows['arch_audi'];
        $vist=array_search($cade,$arra_arch_vist);
        if($vist)
        {
            $colo="<font color=darkseagreen>";
            $cade_vist.=",".$rows['iden_audi'].",";
        }
        $colo='';
        
        $most_fech=$rows['anno_audi'].'-'.$rows['mess_audi'];
        if($flag_fech==$rows['anno_audi'].'-'.$rows['mess_audi'])
            $most_fech='';
        else
            $flag_fech=$rows['anno_audi'].'-'.$rows['mess_audi'];

	$audi="../audios/audios/0401/$rows[anno_audi]/$rows[mess_audi]/$rows[expe_audi]/$rows[audi_audi]/$rows[arch_audi]";
	if(!file_exists($audi))
		$audi="../audios1/audios/0401/$rows[anno_audi]/$rows[mess_audi]/$rows[expe_audi]/$rows[audi_audi]/$rows[arch_audi]";

	$arch_most=$rows['arch_audi'];
	if(substr($rows['arch_audi'],-3)=='mp3' OR substr($rows['arch_audi'],-3)=='MP3')
		$arch_most="<audio id=\"audio$con2\" controls controlsList=\"nodownload\" onplay=\"f_advertencia('$rows[iden_audi]','$rows[arch_audi]','audio$con2','audio_$con2')\"><source src=\"$audi\" type=\"audio/mp3\"></audio><BR><font size=-1>$rows[arch_audi]";

	echo"<input type=hidden name=\"audio_$con2\" value=\"0\">";
        
		$data=[	'1'=>$most_cont,
				'2'=>$most_fech,
				'3'=>$most_audi,
				'4'=>"<table width=100% style=\"border-collapse: revert\"><tr><td width=1%><img src=\"img/icons/$icon\" width=\"20\"></td><td style=\"text-align: left\">$colo$arch_most</td></tr>
				</table>",
				'5'=>"<a href=\"javascript:f_descargar('$rows[iden_audi]','$rows[arch_audi]','audio_$con2')\"><font color=black><img src=\"img/icons/download.svg\" width=\"20\">".size_as_kb(filesize($audi)),
		];
				//'5'=>"<input type=\"checkbox\" name=\"chec_arch\" value=\"$rows[iden_audi]\">",
		echo $html->put_table_responsive_data($head,$data);
    }
    echo"<input type=hidden name=\"cade_vist\" value=\"$cade_vist\">";
    echo $html->put_table_responsive_close();
    echo"</div>";
  
/* 
	$result_pagi=$Db->query("select distinct arch_audi from mp_cons_audi where sede_audi='".$_POST['codi_sede']."' AND anno_audi='".$_POST['busq_anno']."' AND mess_audi='".str_pad($_POST['busq_mess'],2,'0',STR_PAD_LEFT)."' AND expe_audi='".$_POST['busq_expe']."' AND audi_audi='".$_POST['busq_audi']."' AND esta_audi='1' order by arch_audi");
	echo"<div style=\"width:90%;max-width:800px;margin:auto;\">";
	echo $html->put_title_demand("Audios Encontrados");

	$head=['1'=>"Nº",'2'=>"NOMBRE DE ARCHIVO",'3'=>""];
	echo $html->put_table_responsive_open();
	
		echo $html->put_table_responsive_header($head);
		$cont=0;
		foreach($result_pagi as $rows)
		{
			if(substr($rows['arch_audi'],-3)=='mp3' OR substr($rows['arch_audi'],-3)=='MP3')
			{
				$cont++;
				echo"<input type=hidden name=\"audio_$cont\" value=\"0\">";
				$_POST['busq_mess']=str_pad($_POST['busq_mess'], 2, "0", STR_PAD_LEFT);
			    	$audi="https://10.4.100.4/audios/audios/$_POST[codi_sede]/$_POST[busq_anno]/$_POST[busq_mess]/$_POST[busq_expe]/$_POST[busq_audi]/$rows[arch_audi]";
			    	//$audi="audios/$_POST[codi_sede]/$_POST[busq_anno]/$_POST[busq_mess]/$_POST[busq_expe]/$_POST[busq_audi]/$rows[arch_audi]";
				$data=[	'1'=>$cont,
					'2'=>$rows['arch_audi'],
					'3'=>"<table border=0><tr><td><audio id=\"audio$cont\" controls controlsList=\"nodownload\" onplay=\"f_advertencia('$_POST[codi_sede]','$_POST[busq_anno]','$_POST[busq_mess]','$_POST[busq_expe]','$_POST[busq_audi]','$rows[arch_audi]','audio$cont','audio_$cont','$_POST[busq_expe]')\"><source src=\"$audi\" type=\"audio/mp3\"></audio></td><td>
<a href=\"javascript:f_descargar('$_POST[codi_sede]','$_POST[busq_anno]','$_POST[busq_mess]','$_POST[busq_expe]','$_POST[busq_audi]','$rows[arch_audi]','audio_$cont')\"><img src=\"img/icons/download.svg\" width=\"20\"></td></tr></table>
",
				];
				echo $html->put_table_responsive_data($head,$data);
			}
		}

	echo $html->put_table_responsive_close();
	echo $html->put_title_demand("Actas Encontradas");
	$head=['1'=>"Nº",'2'=>"NOMBRE DE ARCHIVO",'3'=>"DESCARGAR"];
	echo $html->put_table_responsive_open();
	echo $html->put_table_responsive_header($head);
		foreach($result_pagi as $rows)
		{
			//if(substr($rows['arch_audi'],-3)!='mp3' AND substr($rows['arch_audi'],-3)!='MP3')
			if(substr($rows['arch_audi'],-3)!='mp3')
			{
				$cont++;
				echo"<input type=hidden name=\"audio_$cont\" value=\"0\">";
				$_POST['busq_mess']=str_pad($_POST['busq_mess'], 2, "0", STR_PAD_LEFT);
				$data=[	'1'=>$cont,
					'2'=>$rows['arch_audi'],
					'3'=>"<a href=\"javascript:f_descargar('$_POST[codi_sede]','$_POST[busq_anno]','$_POST[busq_mess]','$_POST[busq_expe]','$_POST[busq_audi]','$rows[arch_audi]','audio_$cont')\"><img src=\"img/icons/download.svg\" width=\"20\">",
				];
				echo $html->put_table_responsive_data($head,$data);
			}
		}
		
	echo $html->put_table_responsive_close();
	if($busc_tota_pagi>0)
		echo $html->put_page("2",$_POST['busc_pagi_actu'],$busc_tota_pagi,"Nuevo Personal");
	echo"</div>";
*/
	if($busc_tota_item>0)
	{
		echo $html->put_separator_demand("30");
		echo"
                        <div align=center class=\"foot\">
                                <center>
                                <div align=center class=\"foot2\">
                                        <div class=\"div_button_foot\" style=\"\">
                                                <button class=\"button_foot\" onclick=\"f_reiniciar()\">Reiniciar Búsqueda</button>
                                        </div>
                                        <div class=\"div_button_foot\"><center>
                                                <button class=\"button_foot\" onclick=\"return f_descargar_seleccion()\">Descargar Seleccionados</button>
                                        </div>
                                </div>
                        </div>
                ";
	}
}
?>
<center>
	<iframe id="frame_registro" name="frame_registro" title="Oculto" style="border:none;" width="0" height="0" src=""></iframe>
	</form>
	</body>
</html>
