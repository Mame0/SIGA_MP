<?
//classes/TCPDF/examples/personal_fotocheck.php
	require_once 'include/cabecera.php';
	require_once 'classes/Html.class.php';
	require_once 'classes/Db.class.php';
	$Db = new Db();
	if(!isset($_POST['busc_pagi_actu']))
		$_POST['busc_pagi_actu']=1;
		
    if (isset($_FILES['nomb_arch']) && $_FILES['nomb_arch']['error'] === UPLOAD_ERR_OK)
    {
        //echo"hola!!!!";
        $fileTmpPath = $_FILES['nomb_arch']['tmp_name'];
        $fileName = $_FILES['nomb_arch']['name'];
        $fileSize = $_FILES['nomb_arch']['size'];
        $fileType = $_FILES['nomb_arch']['type'];
        $fileNameCmps = explode(".", $fileName);
        $fileExtension = strtolower(end($fileNameCmps));
        //Como el archivo cargado puede contener espacios y otros caracteres especiales, es mejor limpiar el nombre del archivo
        $newFileName = md5(time() . $fileName) . '.' . $fileExtension;
$flag_temp=0;
        $allowedfileExtensions = array('txt', 'xxx');
        if (in_array($fileExtension, $allowedfileExtensions)) 
        {
            $tabla = str_replace('"', '', file_get_contents($fileTmpPath));
            // Separar por líneas
            $lineas = explode("\n", $tabla);
            $cant_inse=0;
            $cant_tota=0;
            foreach($lineas as $tmp)
            {
$flag_temp++;
if($flag_temp>0)
{
                // Eliminar posibles espacios y retorno de carro; separar por tabulador
                $datos = explode("\t", trim($tmp));
                $f_programada = $datos[0];
                if($f_programada!='f_programada')
                {
                    $cant_tota++;
                    $f_firma = $datos[1];
                    $x_observacion = $datos[2];
                    $x_ape_paterno = $datos[3];
                    $x_ape_materno = $datos[4];
                    $x_nombres = $datos[5];
                    $c_usuario = $datos[6];
                    $x_nom_instancia = $datos[7];
                    $c_estado = $datos[8];
                    $c_persona = $datos[9];
                    $x_formato = $datos[10];
                    $x_doc_id = $datos[11];
                
                    //echo"<HR>$f_programada - $f_firma - $x_observacion - $x_ape_paterno - $x_ape_materno - $x_nombres - $c_usuario - $x_nom_instancia - $c_estado - $c_persona - $x_formato - $x_doc_id";
                
                    $cont=0;
//                    $result=$Db->query("select * from mp_cons_firm where f_programada='$f_programada' AND f_firma='$f_firma' AND x_formato='$x_formato' AND x_doc_id='$x_doc_id' limit 1");
//                    foreach($result as $rows)
//                        $cont++;
                
                    if($cont==0)
                    {
                        $fdig=date("YmdHis");
                        $result=$Db->query("insert into mp_cons_firm values('','$f_programada','$f_firma','$x_observacion','$x_ape_paterno','$x_ape_materno','$x_nombres','$c_usuario','$x_nom_instancia','$c_estado','$c_persona','$x_formato','$x_doc_id','$_SESSION[iden_oper]','$fdig')");
                        $cant_inse++;
                    }
                }
                //f_programada	f_firma	x_observacion	x_ape_paterno	x_ape_materno	x_nombres	c_usuario	x_nom_instancia	c_estado	c_persona	x_formato	x_doc_id	
}
            }
            if($cant_tota==0)
                echo"<script>alert('ERROR: Archivo no tiene ningun registro')</script>";
            else
            {
                if($cant_inse==0)
                    echo"<script>alert('Archivo tiene $cant_tota registros, pero no se ha importado ninguno')</script>";
                else
                    echo"<script>alert('Archivo tiene $cant_tota registros y se han importado $cant_inse')</script>";
            }
        }
    }
    
    $result=$Db->query("select count(*) as cant from mp_cons_firm");
    foreach($result as $rows)
        $cant_regi=$rows['cant'];
    
    $result=$Db->query("select max(fdig_firm) as cant from mp_cons_firm");
    foreach($result as $rows)
        $ult_regi=substr($rows['cant'],0,4).'/'.substr($rows['cant'],4,2).'/'.substr($rows['cant'],6,2).' '.substr($rows['cant'],8,2).':'.substr($rows['cant'],10,2).':'.substr($rows['cant'],12,2);
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
		    function ajustar_rango()
		    {
		        var tipo=document.form.busq_tipo.value;
		        switch(tipo)
		        {
		            case '1':
		                var desd='<?=date("Y-m-d")?>';
		                var hast='<?=date("Y-m-d")?>';
		                break;
		            case '2':
		                var desd='<?=date('Y-m-d',strtotime("-1 days"))?>';
		                var hast='<?=date('Y-m-d',strtotime("-1 days"))?>';
		                break;
		            case '3':
		                var desd='<?=date('Y-m-01')?>';
		                var hast='<?=date("Y-m-d")?>';
		                break;
		            case '4':
		                var desd='<?=date('Y-m-d',strtotime("-30 days"))?>';
		                var hast='<?=date("Y-m-d")?>';
		                break;
		            case '5':
		                var desd='<?=date('Y-01-01')?>';
		                var hast='<?=date("Y-m-d")?>';
		                break;
		            case '6':
		                var desd='<?=date('Y-01-01',strtotime("-1 years"))?>';
		                var hast='<?=date("Y-12-31",strtotime("-1 years"))?>';
		                break;
		        }
		        document.form.fech_desd.value=desd;
		        document.form.fech_hast.value=hast;
		        document.form.submit();
		        //var tipo='1';
		        //alert(document.form.fech_desd.value);
		    }
			function check_buscar()
			{
				document.form.action='';
				document.form.target="";
				document.form.submit();
			}
			function f_eliminar(codi,nume)
			{
			    if(confirm('Seguro que desea eliminar carpeta '+nume+'?'))
			    {
    			    obse=prompt('Ingrese Motivo');
    			    if(obse)
    			    {
        				document.form.codi_elim.value=codi;
        				document.form.obse_elim.value=obse;
	        			document.form.action='';
		        		document.form.target="";
			        	document.form.submit();
    			    }
    			    else
    			        alert('ERROR: Ingrese Observacion');
			    }
			}
			function f_reporte()
			{
				document.form.action='classes/TCPDF/examples/mpartes_asignaciones.php';
				document.form.target="blank";
				document.form.submit();
			}
			function f_asignar(chec_todo)
			{
			    if(confirm('Seguro que desea asignar carpetas?'))
			    {
			        document.form.chec_todo.value=chec_todo;
			        document.form.action='';
		       		document.form.target="";
			       	document.form.submit();
			    }
			    else
			        return false;
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
	<center><h4 style="color:#073A6B"><B>FIRMA DE SENTENCIADOS<BR>Actualización de Información</B></h4></center>
		<form name="form" method="post" enctype="multipart/form-data" id="fileupform">
			<input type=hidden name="codi_elim">
			<input type=hidden name="obse_elim">
			<input type=hidden name="chec_todo">
			<input type=hidden name="sesi_codi_depe" value="<?=$_SESSION['codi_depe']?>">
			<input type=hidden name="busc_pagi_actu" value="<?=$_POST['busc_pagi_actu']?>">
<?
	$html=new htmlclass;
	
	$anno_actu=date(Y);
    $anno_pasa=date(Y)-1;
	$arra_options_busq[0]="<- Seleccione ->";
	$arra_options_busq[1]="Hoy";
	$arra_options_busq[2]="Ayer";
	$arra_options_busq[3]="Mes actual";
	$arra_options_busq[4]="Últimos 30 días";
	$arra_options_busq[5]="Año $anno_actu";
	$arra_options_busq[6]="Año $anno_pasa";
	$arra_options_busq[7]="Rango de Fechas";
	
	/*
	$arra_options_mpar[0]="<- Todos ->";
	$result=$Db->query("select distinct b.codi_depe,b.nomb_depe from mp_mpar_carpetas a, mp_admi_depe b where a.depe_mpar=b.codi_depe AND a.esta_mpar=1 AND a.codi_depe<>0 AND a.codi_pers<>0");
    foreach($result as $rows)
            $arra_options_mpar[$rows['codi_depe']]=$rows['nomb_depe'];
	*/
	
	echo"<main>";
	echo $html->put_title_demand("Cargar archivo separado por TABS");
	echo $html->put_text('file',"Seleccione&nbsp;Archivo","",'nomb_arch','','','100','');
	echo $html->put_button_colum("&nbsp;",'Subir Registros'." &raquo;","return check_buscar()");
	echo $html->put_title_demand("Información Actual");
	echo $html->put_text('text',"Registros&nbsp;Actuales","0",'cant_regi',$cant_regi,'','100','readonly');
	echo $html->put_text('text',"Ultimo&nbsp;Registro","0",'cant_regi',$ult_regi,'','100','readonly');
	echo $html->put_text('text',"Registros&nbsp;Agregados","0",'cant_inse',$cant_inse,'','100','readonly');
	echo"</main>";
	
	/*
	echo"<main>";
	echo $html->put_select("Mesa&nbsp;de&nbsp;Partes",'sesi_codi_depe',$arra_options_mpar,$_POST['sesi_codi_depe'],"");
	echo"</main>";
	*/
	
	/*
	$result=$Db->select('mp_maes_mpar_tdoc', '', '', '', ['n_codigo'=>'ASC']);
    foreach($result as $rows)
            $arra_options_tdoc[$rows['n_codigo']]=$rows['x_nombre'];
    */

    $result=$Db->query("select iden_oper,logi_oper,ndoc_oper,appa_oper,apma_oper,nomb_oper from mp_admi_oper");
    foreach($result as $rows)
    {
        $nomb=explode(" ",$rows['nomb_oper']);
            $arra_oper[$rows['iden_oper']]=$rows['appa_oper']."<BR>".$nomb[0];
            //$arra_oper[$rows['iden_oper']]=$rows['appa_oper']." ".$rows['apma_oper']."<BR>".$rows['nomb_oper'];
    }
    
    /*
    //$result=$Db->query("select distinct b.iden_pers,b.nomb_pers,b.appa_pers from mp_mpar_carpetas a, mp_maes_personal b where a.codi_pers=b.iden_pers AND a.esta_mpar=1 AND a.codi_depe<>0 AND a.codi_pers<>0 AND a.depe_mpar='".$_SESSION['codi_depe']."'");
    $result=$Db->query("select distinct b.iden_pers,b.nomb_pers,b.appa_pers from mp_mpar_carpetas a, mp_maes_personal b where a.codi_pers=b.iden_pers AND a.esta_mpar=1 AND a.codi_depe<>0 AND a.codi_pers<>0");
    foreach($result as $rows)
    {
        $posi=strpos($rows['nomb_pers'],' ');
        if($posi==0)    $posi=100;
            $arra_nomb_fisc[$rows['iden_pers']]=substr($rows['nomb_pers'],0,$posi)."<br>".$rows['appa_pers'];
    }
    */

    /*
	$busc_item_pagi=40;      //cantidad de items por pagina
	
	//$result=$Db->query("select * from mp_jurisprudencia_documento where nomb_docu like '%:m_busq%'",[':m_busq'=>$_POST['text_busc']]);
	
	$AND_MPAR='';
	if($_GET['pers_gene']==1)
	    $AND_MPAR=" AND iden_oper='$_SESSION[iden_oper]'";
	
	//$result=$Db->query("select * from mp_mpar_carpetas where esta_mpar=1 AND codi_depe<>0 AND codi_pers<>0 AND depe_mpar='".$_SESSION['codi_depe']."' AND fech_asig>='".str_replace("-","",$_POST['fech_desd']).'000000'."' AND fech_asig<='".str_replace("-","",$_POST['fech_hast']).'999999'."' order by fdig_mpar desc");
	$result=$Db->query("select * from mp_cons_audi_oper where esta_auop=1 $AND_MPAR AND fdig_auop>='".str_replace("-","",$_POST['fech_desd']).'000000'."' AND fdig_auop<='".str_replace("-","",$_POST['fech_hast']).'999999'."' order by fdig_auop");

//echo"<HR>select * from mp_mpar_carpetas where esta_mpar=1 AND codi_depe<>0 AND codi_pers<>0 AND depe_mpar='".$_SESSION['codi_depe']."' AND fech_asig>='".str_replace("-","",$_POST['fech_desd']).'000000'."' AND fech_asig<='".str_replace("-","",$_POST['fech_hast']).'999999'."' order by fdig_mpar desc<HR>";	
	$busc_tota_item=0;
	foreach($result as $rows)
	{       
		$busc_tota_item++;
	}
	$busc_tota_pagi=ceil($busc_tota_item/$busc_item_pagi);
	$busc_limi_pagi=($_POST['busc_pagi_actu']-1)*$busc_item_pagi;
	
	$result_pagi=$Db->query("select * from mp_cons_audi_oper where esta_auop=1 $AND_MPAR AND fdig_auop>='".str_replace("-","",$_POST['fech_desd']).'000000'."' AND fdig_auop<='".str_replace("-","",$_POST['fech_hast']).'999999'."' order by fdig_auop limit $busc_limi_pagi,$busc_item_pagi");
	echo"<div style=\"width:90%;max-width:800px;margin:auto;\">";
	echo $html->put_title_demand("Accesos registrados: $busc_tota_item ");
	if($busc_tota_pagi>0)
		echo $html->put_page("1",$_POST['busc_pagi_actu'],$busc_tota_pagi,"");
	$head=['1'=>"Nº",'2'=>"USUARIO",'3'=>"DIR.IP",'4'=>"EXPEDIENTE",'5'=>"ARCHIVO",'6'=>"FECHA"];
	echo $html->put_table_responsive_open();
	if($busc_tota_item)
	{
		echo $html->put_table_responsive_header($head);
		$cont=$busc_limi_pagi;
		foreach($result_pagi as $rows)
		{
			$cont++;
			$rows['fdig_auop']=substr($rows['fdig_auop'],6,2).'/'.substr($rows['fdig_auop'],4,2).'/'.substr($rows['fdig_auop'],0,4)." ".substr($rows['fdig_auop'],8,2).':'.substr($rows['fdig_auop'],10,2);
			$data=[	'1'=>$cont,
				'2'=>strtoupper($arra_oper[$rows['iden_oper']]),
				'3'=>$rows['dire_auop'],
				'4'=>$rows['expe_audi'],
				'5'=>$rows['arch_audi'],
				'6'=>$rows['fdig_auop'],
			];
			    //'4'=>"<a href=\"javascript:f_ver('docu_".str_pad($rows['codi_docu'], 6, "0", STR_PAD_LEFT).".pdf')\"><img src=\"img/pdf_image.gif\" width=\"20\">",
			echo $html->put_table_responsive_data($head,$data);
		}
	}
	else
		echo $html->put_table_responsive_title("<font color=silver>No Existen Accesos");
	echo $html->put_table_responsive_close();
	if($busc_tota_pagi>0)
		echo $html->put_page("2",$_POST['busc_pagi_actu'],$busc_tota_pagi,"");
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
                                                <button class=\"button_foot\" onclick=\"f_reporte()\">Generar PDF</button>
                                        </div>
                                        <div class=\"div_button_foot\"><center>
                                                <button class=\"button_foot\" onclick=\"check_buscar()\">Buscar Carpetas Asignadas</button>
                                        </div>
                                </div>
                        </div>
                ";
	}
?>
<center>
	</form>
	</body>
</html>
