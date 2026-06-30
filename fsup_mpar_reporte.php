<?
//classes/TCPDF/examples/personal_fotocheck.php
	require_once 'include/cabecera.php';
	require_once 'classes/Html.class.php';
	require_once 'classes/Db.class.php';
	$Db = new Db();
	if(!isset($_POST['busc_pagi_actu']))
		$_POST['busc_pagi_actu']=1;
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
			function f_buscar()
			{
			    if(document.form.fech_desd.value=='')
			    {
			        alert('ERROR: Seleccione Rango de Fecha');
			        document.form.fech_desd.focus();
			        return false;
			    }
			    else
			    {
			        if(document.form.fech_hast.value=='')
			        {
			            alert('ERROR: Seleccione Rango de Fecha');
			            document.form.fech_hast.focus();
			            return false;
			        }
			        else
			        {
			            document.form.action='';
				        document.form.target="";
				        document.form.submit();
			        }
			    }
				
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
				document.form.action='personal_registro.php';
				document.form.target="";
				document.form.submit();
			}
			function f_editar_personal(codi)
			{
				document.form.codi_pers.value=codi;
				document.form.action='personal_registro.php';
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
	<center><h2 style="color:#073A6B">Fiscalías Superiores - Reportes de Casos</h2></center>
		<form name="form" method="post">
			<input type=hidden name="codi_pers">
			<input type=hidden name="todo_chek">
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
	
	$result=$Db->select('mp_fotocheck_personal','', '', '','');
	foreach($result as $rows)
		$arra_options_fisc[$rows['codi_pers']]=utf8_encode($rows['appe_pers'].", ".$rows['nomb_pers']);
		
	$arra_options_tipo=$Db->get_options("mp_maes_fsup_mpar_tipo");
	$arra_options_depe=$Db->get_options("mp_maes_fotocheck_dependencia");

	echo"<main>";
	echo $html->put_title_demand("Criterios de Búsqueda");
	echo $html->put_select("Rangos&nbsp;Pre&nbsp;Definidos",'busq_tipo',$arra_options_busq,$_POST['busq_tipo']," onchange=\"ajustar_rango()\"");
	//echo $html->put_title_demand("Rangos de Fecha");
	//echo $html->put_title_demand($html->put_select("",'busq_tipo',$arra_options_busq,$_POST['busq_tipo']," onchange=\"ajustar_rango()\""));
	//echo $html->put_select("Dependencia",'codi_depe',$arra_options_depe,$_POST['codi_depe'],"");
	//echo $html->put_select("Tipo",'busq_tipo',$arra_options_tipo,$_POST['busq_tipo'],"");
	echo $html->put_text('date',"Desde","Fecha desde",'fech_desd',$_POST['fech_desd'],'','100','');
	echo $html->put_text('date',"Hasta","Fecha desde",'fech_hast',$_POST['fech_hast'],'','100','');
	echo"</main><main>";
	//echo $html->put_title_demand("");
	//echo $html->put_separator_demand("20");
	//echo $html->put_button_colum("",'Buscar',"alert('Prueba')");
	echo"</main>";
if($_POST['fech_desd'])
{
	$busc_item_pagi=40;      //cantidad de items por pagina
	
	$_POST['fech_desd']=str_replace("-","",$_POST['fech_desd']);
	$_POST['fech_hast']=str_replace("-","",$_POST['fech_hast']);
	
	//$_POST['fech_desd']="20220303";
	//$_POST['fech_hast']="20220304";
	
	$result=$Db->query("select * from mp_fsup_mpar_ingreso where SUBSTRING(ingr_fdig,1,8)>=:m_desd AND SUBSTRING(ingr_fdig,1,8)<=:m_hast",[':m_desd'=>$_POST['fech_desd'],':m_hast'=>$_POST['fech_hast']]);
	$busc_tota_item=0;
	foreach($result as $rows)
	{       
		$busc_tota_item++;
	}
	$busc_tota_pagi=ceil($busc_tota_item/$busc_item_pagi);
	$busc_limi_pagi=($_POST['busc_pagi_actu']-1)*$busc_item_pagi;

	$result_pagi=$Db->query("select * from mp_fsup_mpar_ingreso where SUBSTRING(ingr_fdig,1,8)>=:m_desd AND SUBSTRING(ingr_fdig,1,8)<=:m_hast order by ingr_fdig asc limit $busc_limi_pagi,$busc_item_pagi",[':m_desd'=>$_POST['fech_desd'],':m_hast'=>$_POST['fech_hast']]);

	echo"<div style=\"width:90%;max-width:800px;margin:auto;\">";
	echo $html->put_title_demand("RESULTADOS DE B&Uacute;SQUEDA: $busc_tota_item ENCONTRADOS");

	if($busc_tota_pagi>0)
		echo $html->put_page("1",$_POST['busc_pagi_actu'],$busc_tota_pagi,"Nuevo Personal");
	$head=['1'=>"Nº",'2'=>"CARPETA",'3'=>"DEPENDENCIA",'4'=>"FISCAL",'5'=>"TIPO",'6'=>"FOLIOS",'7'=>"FECHA"];
	echo $html->put_table_responsive_open();
	if($busc_tota_item)
	{
		echo $html->put_table_responsive_header($head);
		$cont=$busc_limi_pagi;
		foreach($result_pagi as $rows)
		{
			$d="";
			if(!$rows['esta_pers'])
				$d="<del>";
			$f="<a href=\"javascript:alert('ERROR: Usuario no tiene foto')\"><img src=\"img/icons/x-circle.svg\" width=\"20\">";
			$g="<a href=\"javascript:alert('ERROR: Usuario no esta habilitado para imprimir fotocheck')\"><img src=\"img/icons/x-circle.svg\" width=\"20\">";
			$c="disabled";
			$e="<font color=silver>";
			if(file_exists("classes/TCPDF/examples/fotos/".$rows['ndni_pers'].".jpg"))
			{
				$f="<a href=\"classes/TCPDF/examples/fotos/".$rows['ndni_pers'].".jpg\" target=\"blank\"><img src=\"img/icons/check-circle.svg\" width=\"20\"></a>";
				//$e="";
				if($rows['habi_impr']==1)
				    $c=$e="";
			}
			if($rows['habi_impr']==1)
			    $g="<a href=\"javascript:alert('Usuario SI esta habilitado para imprimir fotocheck')\"><img src=\"img/icons/check-circle.svg\" width=\"20\"></a>";
			$cont++;
			$data=[	'1'=>$cont,
				'2'=>str_pad($rows['carp_depe'],4,"0",STR_PAD_LEFT)."-".str_pad($rows['carp_anno'],4,"0",STR_PAD_LEFT)."-".str_pad($rows['carp_caso'],4,"0",STR_PAD_LEFT)."-".str_pad($rows['carp_cuad'],4,"0",STR_PAD_LEFT),
				'3'=>$arra_options_depe[$rows['orig_depe']],
				'4'=>$arra_options_fisc[$rows['orig_fisc']],
				'5'=>$arra_options_tipo[$rows['orig_tipo']],
				'6'=>$rows['ingr_foli'],
				'7'=>substr($rows['ingr_fdig'],6,2)."/".substr($rows['ingr_fdig'],4,2)."/".substr($rows['ingr_fdig'],0,4),
			];
			echo $html->put_table_responsive_data($head,$data);
		}
	}
	else
		echo $html->put_table_responsive_title("No Existen Casos");
	echo $html->put_table_responsive_close();
	if($busc_tota_pagi>0)
		echo $html->put_page("2",$_POST['busc_pagi_actu'],$busc_tota_pagi,"Nuevo Personal");
	echo"</div>";
	
		
	
}
    echo $html->put_separator_demand("30");
		echo"
                        <div align=center class=\"foot\">
                                <center>
                                <div align=center class=\"foot2\">
                                        <div class=\"div_button_foot\" style=\"\">
                                                <button class=\"button_foot\" onclick=\"f_cancelar()\">Cancelar</button>
                                        </div>
                                        <div class=\"div_button_foot\"><center>
                                                <button class=\"button_foot\" onclick=\"return f_buscar()\">Buscar</button>
                                        </div>
                                </div>
                        </div>
                ";
?>
<center>
	</form>
	</body>
</html>
