<?
//classes/TCPDF/examples/personal_fotocheck.php
	require_once 'include/cabecera.php';
	require_once 'classes/Html.class.php';
	require_once 'classes/Db.class.php';
	$Db = new Db();
	if(!isset($_POST['busc_pagi_actu']))
		$_POST['busc_pagi_actu']=1;
		
		//echo"<HR>".$_SESSION['codi_depe']."<HR>";
	
    $result=$Db->query("select * from mp_admi_depe where codi_depe='".$_POST['sesi_codi_depe']."' ");
    foreach($result as $rows)
        $nomb_depe=$rows['abre_depe']."<BR>";
        //echo"<HR>".$_SESSION['codi_depe']."- $nomb_depe<HR>";
        
    if(!$_POST['busq_tipo'])
    {
        $_POST['busq_tipo']=1;
        $_POST['fech_desd']=date("Y-m-d");
        $_POST['fech_hast']=date("Y-m-d");
    }
    
    //$fech_desd_sql=str_replace("-","",$_POST['fech_desd']);
    //echo"<HR>".$_POST['fech_desd']." - $fech_desd_sql<HR>";
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
	<center><h2 style="color:#073A6B">REPORTE DE ASIGNACIONES<BR><? echo "$nomb_depe(desde el ".substr($_POST['fech_desd'],8,2).'/'.substr($_POST['fech_desd'],5,2).'/'.substr($_POST['fech_desd'],0,4)." hasta el ".substr($_POST['fech_hast'],8,2).'/'.substr($_POST['fech_hast'],5,2).'/'.substr($_POST['fech_hast'],0,4).")";?></h2></center>
		<form name="form" method="post">
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
	
	$arra_options_mpar[0]="<- Todos ->";
	
	$result=$Db->query("select distinct b.codi_depe,b.nomb_depe from mp_mpar_carpetas a, mp_admi_depe b where a.depe_mpar=b.codi_depe AND a.esta_mpar=1 AND a.codi_depe<>0 AND a.codi_pers<>0");
    foreach($result as $rows)
            $arra_options_mpar[$rows['codi_depe']]=$rows['nomb_depe'];
	
	echo"<main>";
	//echo $html->put_title_demand("Criterios de Búsqueda");
	echo $html->put_select("Rangos&nbsp;Pre&nbsp;Definidos",'busq_tipo',$arra_options_busq,$_POST['busq_tipo']," onchange=\"ajustar_rango()\"");
	echo $html->put_text('date',"Desde","Fecha desde",'fech_desd',$_POST['fech_desd'],'','100','');
	echo $html->put_text('date',"Hasta","Fecha desde",'fech_hast',$_POST['fech_hast'],'','100','');
	echo"</main>";
	
	echo"<main>";
	echo $html->put_select("Mesa&nbsp;de&nbsp;Partes",'sesi_codi_depe',$arra_options_mpar,$_POST['sesi_codi_depe'],"");
	echo"</main>";
	
	$result=$Db->select('mp_maes_mpar_tdoc', '', '', '', ['n_codigo'=>'ASC']);
    foreach($result as $rows)
            $arra_options_tdoc[$rows['n_codigo']]=$rows['x_nombre'];

    //$result=$Db->query("select distinct b.codi_depe,b.nomb_depe from mp_mpar_carpetas a, mp_admi_depe b where a.codi_depe=b.codi_depe AND a.esta_mpar=1 AND a.codi_depe<>0 AND a.codi_pers<>0 AND a.depe_mpar='".$_SESSION['codi_depe']."'");
    $result=$Db->query("select distinct b.codi_depe,b.nomb_depe from mp_mpar_carpetas a, mp_admi_depe b where a.codi_depe=b.codi_depe AND a.esta_mpar=1 AND a.codi_depe<>0 AND a.codi_pers<>0");
    foreach($result as $rows)
            $arra_nomb_depe[$rows['codi_depe']]=$rows['nomb_depe'];
    
    //$result=$Db->query("select distinct b.iden_pers,b.nomb_pers,b.appa_pers from mp_mpar_carpetas a, mp_maes_personal b where a.codi_pers=b.iden_pers AND a.esta_mpar=1 AND a.codi_depe<>0 AND a.codi_pers<>0 AND a.depe_mpar='".$_SESSION['codi_depe']."'");
    $result=$Db->query("select distinct b.iden_pers,b.nomb_pers,b.appa_pers from mp_mpar_carpetas a, mp_maes_personal b where a.codi_pers=b.iden_pers AND a.esta_mpar=1 AND a.codi_depe<>0 AND a.codi_pers<>0");
    foreach($result as $rows)
    {
        $posi=strpos($rows['nomb_pers'],' ');
        if($posi==0)    $posi=100;
            $arra_nomb_fisc[$rows['iden_pers']]=substr($rows['nomb_pers'],0,$posi)."<br>".$rows['appa_pers'];
    }

	$busc_item_pagi=40;      //cantidad de items por pagina

	//$result=$Db->query("select * from mp_jurisprudencia_documento where nomb_docu like '%:m_busq%'",[':m_busq'=>$_POST['text_busc']]);
	
	$AND_MPAR='';
	if($_POST['sesi_codi_depe'])
	    $AND_MPAR=" AND depe_mpar='".$_POST['sesi_codi_depe']."'";
	
	//$result=$Db->query("select * from mp_mpar_carpetas where esta_mpar=1 AND codi_depe<>0 AND codi_pers<>0 AND depe_mpar='".$_SESSION['codi_depe']."' AND fech_asig>='".str_replace("-","",$_POST['fech_desd']).'000000'."' AND fech_asig<='".str_replace("-","",$_POST['fech_hast']).'999999'."' order by fdig_mpar desc");
	$result=$Db->query("select * from mp_mpar_carpetas where esta_mpar=1 AND codi_depe<>0 AND codi_pers<>0 $AND_MPAR AND fech_asig>='".str_replace("-","",$_POST['fech_desd']).'000000'."' AND fech_asig<='".str_replace("-","",$_POST['fech_hast']).'999999'."' order by fdig_mpar desc");

//echo"<HR>select * from mp_mpar_carpetas where esta_mpar=1 AND codi_depe<>0 AND codi_pers<>0 AND depe_mpar='".$_SESSION['codi_depe']."' AND fech_asig>='".str_replace("-","",$_POST['fech_desd']).'000000'."' AND fech_asig<='".str_replace("-","",$_POST['fech_hast']).'999999'."' order by fdig_mpar desc<HR>";	
	$busc_tota_item=0;
	foreach($result as $rows)
	{       
		$busc_tota_item++;
	}
	$busc_tota_pagi=ceil($busc_tota_item/$busc_item_pagi);
	$busc_limi_pagi=($_POST['busc_pagi_actu']-1)*$busc_item_pagi;

	$result_pagi=$Db->query("select * from mp_mpar_carpetas where esta_mpar=1 AND codi_depe<>0 AND codi_pers<>0 $AND_MPAR AND fech_asig>='".str_replace("-","",$_POST['fech_desd']).'000000'."' AND fech_asig<='".str_replace("-","",$_POST['fech_hast']).'999999'."' order by fdig_mpar limit $busc_limi_pagi,$busc_item_pagi");
	echo"<div style=\"width:90%;max-width:800px;margin:auto;\">";
	echo $html->put_title_demand("Carpetas Asignadas: $busc_tota_item ");
	if($busc_tota_pagi>0)
		echo $html->put_page("1",$_POST['busc_pagi_actu'],$busc_tota_pagi,"");
	$head=['1'=>"Nº",'2'=>"CODIGO",'3'=>"DOCUMENTO",'4'=>"F.INGRESO",'5'=>"F.ASIGNACION",'6'=>"DESPACHO",'7'=>"FISCAL",'8'=>"&nbsp;"];
	echo $html->put_table_responsive_open();
	if($busc_tota_item)
	{
		echo $html->put_table_responsive_header($head);
		$cont=$busc_limi_pagi;
		foreach($result_pagi as $rows)
		{
			$cont++;
			$rows['fdig_mpar']=substr($rows['fdig_mpar'],6,2).'/'.substr($rows['fdig_mpar'],4,2).'/'.substr($rows['fdig_mpar'],0,4)." ".substr($rows['fdig_mpar'],8,2).':'.substr($rows['fdig_mpar'],10,2);
			$rows['fech_asig']=substr($rows['fech_asig'],6,2).'/'.substr($rows['fech_asig'],4,2).'/'.substr($rows['fech_asig'],0,4)." ".substr($rows['fech_asig'],8,2).':'.substr($rows['fech_asig'],10,2);
			$data=[	'1'=>$cont,
				'2'=>$rows['anno_mpar'].'-'.str_pad($rows['nume_mpar'], 4, '0', STR_PAD_LEFT),
				'3'=>$arra_options_tdoc[$rows['tdoc_mpar']]."<BR>".$rows['mpar_cbar'],
				'4'=>$rows['fdig_mpar'],
				'5'=>$rows['fech_asig'],
				'6'=>$arra_nomb_depe[$rows['codi_depe']],
				'7'=>$arra_nomb_fisc[$rows['codi_pers']],
			];
			    //'4'=>"<a href=\"javascript:f_ver('docu_".str_pad($rows['codi_docu'], 6, "0", STR_PAD_LEFT).".pdf')\"><img src=\"img/pdf_image.gif\" width=\"20\">",
			echo $html->put_table_responsive_data($head,$data);
		}
	}
	else
		echo $html->put_table_responsive_title("<font color=silver>No Existen Carpetas");
	echo $html->put_table_responsive_close();
	if($busc_tota_pagi>0)
		echo $html->put_page("2",$_POST['busc_pagi_actu'],$busc_tota_pagi,"");
	echo"</div>";
	
	//if($busc_tota_item>0)
	//{
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
	//}
?>
<center>
	</form>
	</body>
</html>
