<?
//classes/TCPDF/examples/personal_fotocheck.php
	require_once 'include/cabecera.php';
	require_once 'classes/Html.class.php';
	require_once 'classes/Db.class.php';
	$Db = new Db();
	if(!isset($_POST['busc_pagi_actu']))
		$_POST['busc_pagi_actu']=1;
	if(!$_POST['codi_form'])
	    $_POST['codi_form']=$_GET['codi_form'];
	if(!$_POST['codi_form'])
	    $_POST['codi_form']=1;
	switch($_POST['codi_form'])
	{
	    case 1: $nomb_form="Administrativo";  break;
	    case 2: $nomb_form="Fiscal";  break;
	    case 3: $nomb_form="Secigra";  break;
	}
	function get_client_ip() {
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
			function f_generar_fotocheck(tipo)
			{
				document.form.action='classes/TCPDF/examples/personal_fotocheck.php';
				document.form.todo_chek.value=tipo;
				document.form.target="blank";
				document.form.submit();
			}
			function f_accion_tabla()
			{
				document.form.codi_pers.value='';
				document.form.action='fotocheck_personal_registro.php';
				document.form.target="";
				document.form.submit();
			}
			function f_descargar(sede,anno,mess,expe,audi,arch)
			{
			    acti='10.4.100.4/audios/audios/'+sede+'/'+anno+'/'+mess+'/'+expe+'/'+audi+'/'+arch;
			    alert(acti);
				document.form.action=acti;
				document.form.target="blank";
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
	
		<form name="form" method="post">
			<input type=hidden name="codi_pers">
			<input type=hidden name="todo_chek">
			<input type=hidden name="busc_pagi_actu" value="<?=$_POST['busc_pagi_actu']?>">
			<input type=hidden name="codi_form" value="<?=$_POST['codi_form']?>">


<!--
<input type=text name="sede_audi" id="sede_audi">	

<input type="text" placeholder="Ingresa mensaje" name="message">
    <input type="submit" value="Haz clic para enviar">
    
    <input type=button name="Click" value="Click aqui aqui " onclick="Audios01.contentWindow.postMessage('hola hola', '*');">
    -->
    
	
	<script>
    window.addEventListener('message', function(event) {
        if(event.data.substr(0,5)=='dire_')
            Audios02.contentWindow.document.form.dire_auop.value=event.data.substr(5);
        else
        {
            Audios02.contentWindow.document.form.arch_audi.value=event.data;
            Audios02.contentWindow.document.form.submit();
        }
      //alert(`El Padre Recibí ${event.data} de ${event.origin}`);
    });
  </script>
  
  <!--
  <script>
    form.onsubmit = function() {
      Audios02.contentWindow.postMessage(this.message.value, '*');
      return false;
    };
  </script>
  -->
  
  
			
			<iframe id="Audios01" referrerpolicy="no-referrer" title="Actas y Audios" style="border:none;" width="100%" height="100%" src="https://10.4.100.4/siga/consulta_audios.php"></iframe>
			<iframe id="Audios02" title="Oculto" style="border:none;" width="0" height="0" src="consulta_audios_registro.php"></iframe>

<?
/*
    echo"<center><h4 style=\"color:#073A6B\"><B>AUDIOS Y ACTAS DE AUDIENCIAS DEL P.J.</B></h4></center>";
	$html=new htmlclass;

    $arra_options_sede[0]="<- Seleccione Sede ->";
    $arra_options_sede['0401']="Plaza España";
    //$arra_options_sede[2]="Paucarpata";
    //$arra_options_sede[3]="Mariano Melgar";

	$arra_options_anno[0]="<- Seleccione Año ->";
	$result=$Db->query("select distinct anno_audi from mp_cons_audi where esta_audi='1' order by anno_audi");
	foreach($result as $rows)
		$arra_options_anno[$rows['anno_audi']]=$rows['anno_audi'];
	
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
	
	$arra_options_expe[0]="<- Seleccione Expediente ->";
	$result=$Db->query("select distinct expe_audi from mp_cons_audi where sede_audi='".$_POST['codi_sede']."' AND anno_audi='".$_POST['busq_anno']."' AND mess_audi='".str_pad($_POST['busq_mess'],2,'0',STR_PAD_LEFT)."' AND esta_audi='1' order by expe_audi");
	foreach($result as $rows)
		$arra_options_expe[$rows['expe_audi']]=substr($rows['expe_audi'],4,6)."-".substr($rows['expe_audi'],0,4);
	
	$arra_options_audi[0]="<- Seleccione Audio ->";
	$result=$Db->query("select distinct audi_audi from mp_cons_audi where sede_audi='".$_POST['codi_sede']."' AND anno_audi='".$_POST['busq_anno']."' AND mess_audi='".str_pad($_POST['busq_mess'],2,'0',STR_PAD_LEFT)."' AND expe_audi='".$_POST['busq_expe']."' AND esta_audi='1' order by expe_audi");
	foreach($result as $rows)
		$arra_options_audi[$rows['audi_audi']]=$rows['audi_audi'];

	echo"<main>";
	echo $html->put_title_demand("Formulario de B&uacute;squeda");
	echo $html->put_select("Sede&nbsp;P.J.",'codi_sede',$arra_options_sede,$_POST['codi_sede'],"onchange=\"document.form.submit()\"");
	echo $html->put_select("A&ntilde;o",'busq_anno',$arra_options_anno,$_POST['busq_anno'],"onchange=\"document.form.submit()\"");
	echo $html->put_select("Mes",'busq_mess',$arra_options_mess,$_POST['busq_mess'],"onchange=\"document.form.submit()\"");
	echo"</main><main>";
	echo $html->put_select("Expedientes",'busq_expe',$arra_options_expe,$_POST['busq_expe'],"onchange=\"document.form.submit()\"");
	echo $html->put_select("Audios",'busq_audi',$arra_options_audi,$_POST['busq_audi'],"onchange=\"document.form.submit()\"");
	//echo"</main><main>";
	//echo $html->put_text('text',"<a href=\"javascript:f_buscar()\">Click&nbsp;<u>AQUI</u>&nbsp;para&nbsp;Buscar</a>","Ingrese datos (Comod&iacute;n: %)",'busq_dato',$_POST['busq_dato'],'','100','');
	echo"</main>";
	//echo $html->put_select("Formato",'codi_form',$arra_options_form,$_POST['codi_form'],"");
if($_POST['busq_audi'])
{
	$result_pagi=$Db->query("select distinct arch_audi from mp_cons_audi where sede_audi='".$_POST['codi_sede']."' AND anno_audi='".$_POST['busq_anno']."' AND mess_audi='".str_pad($_POST['busq_mess'],2,'0',STR_PAD_LEFT)."' AND expe_audi='".$_POST['busq_expe']."' AND audi_audi='".$_POST['busq_audi']."' AND esta_audi='1' order by arch_audi");
	echo"<div style=\"width:90%;max-width:800px;margin:auto;\">";
	echo $html->put_title_demand("Archivos Encontrados");

	$head=['1'=>"Nº",'2'=>"NOMBRE DE ARCHIVO",'3'=>"VER/ESCUCHAR",'4'=>"DESCARGAR"];
	echo $html->put_table_responsive_open();
	
		echo $html->put_table_responsive_header($head);
		$cont=0;
		foreach($result_pagi as $rows)
		{
			$cont++;
			$data=[	'1'=>$cont,
				'2'=>$rows['arch_audi'],
				'3'=>"<a href=\"javascript:f_ver('$rows[arch_audi]')\"><img src=\"img/icons/eye.svg\" width=\"20\">",
				'4'=>"<a href=\"javascript:f_descargar('$_POST[codi_sede]','$_POST[busq_anno]','$_POST[busq_mess]','$_POST[busq_expe]','$_POST[busq_audi]','$rows[arch_audi]')\"><img src=\"img/icons/download.svg\" width=\"20\">",
			];
			echo $html->put_table_responsive_data($head,$data);
		}
		
	echo $html->put_table_responsive_close();
	if($busc_tota_pagi>0)
		echo $html->put_page("2",$_POST['busc_pagi_actu'],$busc_tota_pagi,"Nuevo Personal");
	echo"</div>";
	if($busc_tota_item>0)
	{
		echo $html->put_separator_demand("30");
		echo"
                        <div align=center class=\"foot\">
                                <center>
                                <div align=center class=\"foot2\">
                                        <div class=\"div_button_foot\" style=\"\">
                                                <button class=\"button_foot\" onclick=\"f_generar_fotocheck('2')\">Imprimir Seleccionados (check)</button>
                                        </div>
                                        <div class=\"div_button_foot\"><center>
                                                <button class=\"button_foot\" onclick=\"f_generar_fotocheck('1')\">Imprimir toda la B&uacute;squeda</button>
                                        </div>
                                </div>
                        </div>
                ";
	}
}
*/
?>
<center>
	</form>
	</body>
</html>
