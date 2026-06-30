<?
//classes/TCPDF/examples/personal_fotocheck.php
	require_once 'include/cabecera.php';
	require_once 'classes/Html.class.php';
	require_once 'classes/Db.class.php';
	$Db = new Db();
	if(!isset($_POST['busc_pagi_actu']))
		$_POST['busc_pagi_actu']=1;
		
    if(!$_POST['iden_loca'])
	{
	    $result=$Db->query("select b.codi_loca,b.nom1_loca from mp_admi_depe=a, mp_admi_loca=b where a.codi_loca=b.codi_loca AND a.codi_depe='".$_SESSION['codi_depe']."'");
	    foreach($result as $rows)
	    {
	        $_POST['iden_loca']=$rows['codi_loca'];
	        $_POST['nomb_loca']=$rows['nom1_loca'];
	    }
	}

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
		
		<style>
    .tabla2 {
      width: 1%;
      border-collapse: separate;
    }
    .tabla2 th, .tabla2 td {
      border: 0px solid black;
      padding: 1px; /* Adds 15px padding inside each cell */
      text-align: center;
    }
  </style>
  
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
				document.form.action='classes/TCPDF/examples/visitas_reporte.php';
				document.form.target="_blank";
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
	<center><h3 style="color:#073A6B"><B>REPORTE DE REGISTRO DE VISITAS</B><BR><? echo "$nomb_depe(desde el ".substr($_POST['fech_desd'],8,2).'/'.substr($_POST['fech_desd'],5,2).'/'.substr($_POST['fech_desd'],0,4)." hasta el ".substr($_POST['fech_hast'],8,2).'/'.substr($_POST['fech_hast'],5,2).'/'.substr($_POST['fech_hast'],0,4).")";?></h3></center>
		<form name="form" method="post">
			<input type=hidden name="codi_elim">
			<input type=hidden name="obse_elim">
			<input type=hidden name="chec_todo">
			<input type=hidden name="sesi_codi_depe" value="<?=$_SESSION['codi_depe']?>">
			<input type=hidden name="busc_pagi_actu" value="<?=$_POST['busc_pagi_actu']?>">
<?
	$html=new htmlclass;
	
	$anno_actu=date("Y");
    $anno_pasa=date("Y")-1;
	$arra_options_busq[0]="<- Seleccione ->";
	$arra_options_busq[1]="Hoy";
	$arra_options_busq[2]="Ayer";
	$arra_options_busq[3]="Mes actual";
	$arra_options_busq[4]="Últimos 30 días";
	$arra_options_busq[5]="Año $anno_actu";
	$arra_options_busq[6]="Año $anno_pasa";
	$arra_options_busq[7]="Rango de Fechas";
	
	$arra_options_mpar[0]="<- Todos ->";
	
	$arra_options_tvis['0']="<- Seleccione Tipo ->";
	$result=$Db->select('mp_maes_visi_tipo', '', '', '', ['x_nombre'=>'ASC']);
	foreach($result as $rows)
		$arra_options_tvis[$rows['n_codigo']]=$rows['x_nombre'];
		
	$result=$Db->select('mp_maes_tdocumento', '', '', '', ['n_codigo'=>'ASC']);
	foreach($result as $rows)
		$arra_options_tdoc[$rows['n_codigo']]=$rows['x_nombre'];
	
	$arra_options_depe_nomb[0]="<- Seleccione Dependencia ->";
	$result=$Db->query("select * from mp_admi_depe where codi_loca='".$_POST['iden_loca']."' AND esta_depe=1 order by nomb_depe");
	foreach($result as $rows)
	{
		$arra_options_depe_sigl[$rows['codi_depe']]=$rows['sigl_depe'];
		$arra_options_depe_nomb[$rows['codi_depe']]=$rows['nomb_depe'];
	}
	
    $result=$Db->select('mp_admi_loca', '', '', '', ['codi_loca'=>'ASC']);
	foreach($result as $rows)
		$arra_options_loca[$rows['codi_loca']]=$rows['nom1_loca'];
	
	if(!isset($_POST['busq_pala'])) $_POST['busq_pala'] = '';

	echo"<main>";
	echo $html->put_select("Local",'iden_loca',$arra_options_loca,$_POST['iden_loca'],"");
	echo $html->put_select("Rangos&nbsp;Pre&nbsp;Definidos",'busq_tipo',$arra_options_busq,$_POST['busq_tipo']," onchange=\"ajustar_rango()\"");
	echo $html->put_text('date',"Desde","Fecha desde",'fech_desd',$_POST['fech_desd'],'','100','');
	echo $html->put_text('date',"Hasta","Fecha desde",'fech_hast',$_POST['fech_hast'],'','100','');
	echo $html->put_text('text',"Búsqueda","DNI, Nombres o Apellidos",'busq_pala',$_POST['busq_pala'],'','200','');
	echo"</main>";

	$busc_item_pagi=100;      //cantidad de items por pagina

	$busc_pala_sql = "";
	if(isset($_POST['busq_pala']) && trim($_POST['busq_pala']) != '') {
	    $pala = trim($_POST['busq_pala']);
	    $busc_pala_sql = " AND (ndoc_visi LIKE '%$pala%' OR nomb_visi LIKE '%$pala%' OR appa_visi LIKE '%$pala%' OR apma_visi LIKE '%$pala%')";
	}

	$result=$Db->query("select * from mp_visi_registro where iden_loca='".$_POST['iden_loca']."' AND esta_visi>0 AND fech_visi>='".$_POST['fech_desd']."' AND fech_visi<='".$_POST['fech_hast']."'".$busc_pala_sql." order by fdig_visi");
	$busc_tota_item=0;
	foreach($result as $rows)
	{       
		$busc_tota_item++;
	}
	$busc_tota_pagi=ceil($busc_tota_item/$busc_item_pagi);
	$busc_limi_pagi=($_POST['busc_pagi_actu']-1)*$busc_item_pagi;

	$result_pagi=$Db->query("select * from mp_visi_registro where iden_loca='".$_POST['iden_loca']."' AND esta_visi>0 AND fech_visi>='".$_POST['fech_desd']."' AND fech_visi<='".$_POST['fech_hast']."'".$busc_pala_sql." order by iden_visi desc limit $busc_limi_pagi,$busc_item_pagi");

	// Cargar personal para columna AUTORIZA
	$arra_pers_nomb = [];
	$result_pers = $Db->query("select iden_pers,appa_pers,apma_pers,nomb_pers from mp_admi_pers");
	foreach($result_pers as $rp)
	    $arra_pers_nomb[$rp['iden_pers']] = strtoupper($rp['appa_pers'].' '.$rp['apma_pers'].', '.$rp['nomb_pers']);

	echo"<div style=\"width:90%;max-width:800px;margin:auto;\">";

	echo $html->put_title_demand("Registro de Visitas: $busc_tota_item");
	if($busc_tota_pagi>0)
		echo $html->put_page("1",$_POST['busc_pagi_actu'],$busc_tota_pagi,"");
	$head=['1'=>"Nº",'2'=>"DOCUMENTO",'3'=>"NOMBRE",'4'=>"FECHA",'5'=>"DESTINO",'6'=>"AUTORIZA"];
	echo $html->put_table_responsive_open();
	if($busc_tota_item)
	{
		echo $html->put_table_responsive_header($head);
		$cont=$busc_limi_pagi;
		foreach($result_pagi as $rows)
		{
			$cont++;
			//$rows['fdig_mpar']=substr($rows['fdig_mpar'],6,2).'/'.substr($rows['fdig_mpar'],4,2).'/'.substr($rows['fdig_mpar'],0,4)." ".substr($rows['fdig_mpar'],8,2).':'.substr($rows['fdig_mpar'],10,2);
			//$rows['fech_asig']=substr($rows['fech_asig'],6,2).'/'.substr($rows['fech_asig'],4,2).'/'.substr($rows['fech_asig'],0,4)." ".substr($rows['fech_asig'],8,2).':'.substr($rows['fech_asig'],10,2);
			$data=[	'1'=>$cont,
				'2'=>$rows['ndoc_visi'],
				'3'=>utf8_encode(utf8_decode(strtoupper($rows['appa_visi'].' '.$rows['apma_visi'].',<BR>'.$rows['nomb_visi']))),
				'4'=>"<table class=\"tabla2\"><tr><td><img src=\"img/icons/calendar.svg\" width=\"16\"></td><td style=\"font-weight:bold;\">".str_replace("-","/",$rows['fech_visi'])."</td></tr><tr><td><img src=\"img/icons/download.svg\" width=\"16\"></td><td style=\"color:#008000;font-weight:bold;\">".$rows['ingr_visi']."</td></tr><tr><td><img src=\"img/icons/upload.svg\" width=\"16\"></td><td style=\"color:".($rows['sali_visi']=='00:00:00'?'#888888':'#0056b3').";font-weight:bold;\">".$rows['sali_visi']."</td></tr></table>",
				'5'=>utf8_encode(utf8_decode($arra_options_depe_sigl[$rows['iden_depe']] ?? '')),
				'6'=>utf8_encode(utf8_decode($arra_pers_nomb[$rows['iden_pers']] ?? '-')),
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
                                                <button class=\"button_foot\" onclick=\"check_buscar()\">Buscar Visitas</button>
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
