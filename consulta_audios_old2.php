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
	    $expe=substr($expe,4).'-'.substr($expe,0,4);
	    return $expe;
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
	foreach($result as $rows)
	{
	    //echo"<HR>".$rows['anno_audi'].$rows['mess_audi'].$rows['expe_audi'].$rows['audi_audi'].$rows['arch_audi']."<HR>";
		$arra_arch_vist[$rows['iden_auop']]=$rows['anno_audi'].$rows['mess_audi'].$rows['expe_audi'].$rows['audi_audi'].$rows['arch_audi'];
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
			function f_descargar(sede,anno,mess,expe,audi,arch,hidd)
			{
			    	acti='audios/'+sede+'/'+anno+'/'+mess+'/'+expe+'/'+audi+'/'+arch;
				if(eval('document.form.'+hidd+'.value')=='0')
				{
					if(confirm('Se va a registrar su acceso al archivo de la carpeta Nro. '+expe+'\nDesea continuar?'))
					{
						window.parent.postMessage('<?=$dire_auop_modi?>', '*');
						window.parent.postMessage(acti, '*');
						document.form.arch_audi.value=arch;
						eval('document.form.'+hidd+'.value=1');

						//window.open(acti, 'Download');

						document.form.file.value=acti;
						document.form.action='consulta_audios_descargar.php';
						document.form.submit();
						
						//document.form.action='consulta_audios_registro.php';
						//document.form.target="parent.Audios02";
						//document.form.submit();
					}
				}
				else
				{
					//document.form.action=acti;
					//document.form.target="blank";
					//document.form.submit();
					eval('document.form.'+hidd+'.value=1');

					document.form.file.value=acti;
					document.form.action='consulta_audios_descargar.php';
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
                    if(confirm('Se va a registrar su acceso a '+nume_sele+' archivos seleccionados\nDesea continuar?'))
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
			function f_advertencia(sede,anno,mess,expe,audi,arch,boto,hidd,carp)
			{
			    	acti='audios/'+sede+'/'+anno+'/'+mess+'/'+expe+'/'+audi+'/'+arch;
				if(eval('document.form.'+hidd+'.value')=='0')
				{
					document.getElementById(boto).pause();
					if(confirm('Se va a registrar su acceso al audio de la carpeta Nro. '+carp+'\nDesea continuar?'))
					{
						window.parent.postMessage('<?=$dire_auop_modi?>', '*');
						window.parent.postMessage(acti, '*');
						document.form.arch_audi.value=arch;
						eval('document.form.'+hidd+'.value=1');
						document.getElementById(boto).play();
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
	<center><h4 style="color:#073A6B"><B>AUDIOS Y ACTAS DE AUDIENCIAS DEL P.J.</B></h4></center>
		<form name="form" method="post">
			<input type=hidden name="codi_pers">
			<input type=hidden name="todo_chek">
			<input type=hidden name="arch_audi">
			<input type=hidden name="cade_sele">
			<input type=hidden name="iden_oper" value="<?=$_SESSION['iden_oper']?>">
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
/*
	echo $html->put_title_demand("Formulario de B&uacute;squeda");
	echo $html->put_select("Sede&nbsp;P.J.",'codi_sede',$arra_options_sede,$_POST['codi_sede'],"onchange=\"f_recargar()\"");
	echo $html->put_select("A&ntilde;o",'busq_anno',$arra_options_anno,$_POST['busq_anno'],"onchange=\"f_recargar()\"");
	echo $html->put_select("Mes",'busq_mess',$arra_options_mess,$_POST['busq_mess'],"onchange=\"f_recargar()\"");
	echo"</main><main>";
	echo $html->put_select("Expedientes",'busq_expe',$arra_options_expe,$_POST['busq_expe'],"onchange=\"f_recargar()\"");
	echo $html->put_select("Audios",'busq_audi',$arra_options_audi,$_POST['busq_audi'],"onchange=\"f_recargar()\"");
	echo"</main>";
*/
if($_POST['busq_expe'] AND !$_POST['expe_audi'])
{
    $result_pagi=$Db->query("select distinct expe_audi from mp_cons_audi where expe_audi like '".$_POST['busq_anno']."%".$_POST['busq_expe']."%' AND esta_audi='1' order by expe_audi");
	echo"<div style=\"width:90%;max-width:800px;margin:auto;\">";
	echo $html->put_title_demand("Expedientes Encontrados");

	$head=['1'=>"Nº",'2'=>"EXPEDIENTE",'3'=>""];
	echo $html->put_table_responsive_open();
	
	echo $html->put_table_responsive_header($head);
	$cont=0;
	foreach($result_pagi as $rows)
	{
		$cont++;
		$data=[	'1'=>$cont,
		    	'2'=>formato_expediente($rows['expe_audi']),
		    	'3'=>"<a href=\"javascript:f_expediente('$rows[expe_audi]')\"><img src=\"img/icons/eye.svg\" width=\"20\">",
		];
		echo $html->put_table_responsive_data($head,$data);
	}

	echo $html->put_table_responsive_close();
}
if($_POST['expe_audi'])
{
    echo"<div style=\"width:90%;max-width:800px;margin:auto;\">";
    echo $html->put_title_demand("Expediente: ".formato_expediente($_POST['expe_audi']));
    
	$head=['1'=>"Nº",'2'=>"AÑO-MES",'3'=>"AUDIO",'4'=>"ARCHIVO",'5'=>"<input type=\"checkbox\" name=\"todo_chec\" id=\"todo_chec\" onclick=\"f_seleccion_todo(this)\">"];
	echo $html->put_table_responsive_open();
	echo $html->put_table_responsive_header($head);
    $result_pagi=$Db->query("select iden_audi,anno_audi,mess_audi,expe_audi,audi_audi,arch_audi from mp_cons_audi where expe_audi='".$_POST['expe_audi']."' AND esta_audi='1' order by anno_audi,mess_audi,audi_audi,SUBSTRING(arch_audi,-3,3),arch_audi");
    $cont=0;
    $flag_audi='';
    $flag_fech='';
    foreach($result_pagi as $rows)
    {
        //$cont++;
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
        
        $most_fech=$rows['anno_audi'].'-'.$rows['mess_audi'];
        if($flag_fech==$rows['anno_audi'].'-'.$rows['mess_audi'])
            $most_fech='';
        else
            $flag_fech=$rows['anno_audi'].'-'.$rows['mess_audi'];
        
		$data=[	'1'=>$most_cont,
				'2'=>$most_fech,
				'3'=>$most_audi,
				'4'=>"<table width=100% style=\"border-collapse: revert\"><tr><td width=1%><img src=\"img/icons/$icon\" width=\"20\"></td><td style=\"text-align: left\">$colo$rows[arch_audi]</td></tr></table>",
				'5'=>"<input type=\"checkbox\" name=\"chec_arch\" value=\"$rows[iden_audi]\">",
		];
		echo $html->put_table_responsive_data($head,$data);
    }
    echo"<input type=hidden name=\"cade_vist\" value=\"$cade_vist\">";
    echo $html->put_table_responsive_close();
    echo"</div>";
    
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
	if($busc_tota_item>0 OR 3==3)
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
	</form>
	</body>
</html>
