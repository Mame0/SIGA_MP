<?
//classes/TCPDF/examples/personal_fotocheck.php
	require_once 'include/cabecera.php';
	require_once 'classes/Html.class.php';
	require_once 'classes/Db.class.php';
	$Db = new Db();
	if(!isset($_POST['busc_pagi_actu']))
		$_POST['busc_pagi_actu']=1;
	if(!isset($_POST['codi_form']) || !$_POST['codi_form'])
	    $_POST['codi_form']=(isset($_GET['codi_form']) ? $_GET['codi_form'] : '');
	if(!$_POST['codi_form'])
	    $_POST['codi_form']=1;

	if(!isset($_POST['codi_exam']) || !$_POST['codi_exam'])
	{
	    $result=$Db->query("select * from mp_concurso_examen where acti_exam='1' AND esta_exam='1' order by fech_exam limit 1");
	    foreach($result as $rows)
	    {
	        $_POST['codi_exam']=$rows['codi_exam'];
	        $_POST['fech_exam']=$rows['fech_exam'];
	        $_POST['nomb_exam']=$rows['nomb_exam'];
	    }
	    
	    $result=$Db->query("select a.codi_proc,b.codi_plaz from mp_concurso_proceso=a,mp_concurso_plazas=b where a.codi_proc=b.codi_proc AND a.codi_exam='".$_POST['codi_exam']."' AND a.esta_proc='1' AND b.esta_plaz='1'");
	    $_POST['list_plaz']='';
	    foreach($result as $rows)
	        $_POST['list_plaz'].=",".$rows['codi_plaz'];
	    $_POST['list_plaz']=substr($_POST['list_plaz'],1);
	}
	$result=$Db->query("select * from mp_concurso_plazas where codi_plaz in(".$_POST['list_plaz'].")");
	foreach($result as $rows)
	{       
		$arra_options_plaza[$rows['codi_plaz']]=$rows['nomb_plaz'];
	}
	
	if(isset($_POST['elim_asis']) && $_POST['elim_asis'])
	{
	    $result=$Db->query("update mp_concurso_postulantes set regi_asis='',fdig_asis='' where codi_post='".$_POST['elim_asis']."'");
	}
	
	if(!empty($_POST['regi_post']) AND $_POST['docu_post'])	//guardar
	{
	    
	    unset($_POST['codi_post']);
	    $result=$Db->query("select * from mp_concurso_postulantes where docu_post='".$_POST['docu_post']."' AND codi_plaz in (".$_POST['list_plaz'].") AND esta_post='1'");
	    foreach($result as $rows)
	    {
	        $_POST['codi_post']=$rows['codi_post'];
	        $mens_post_agre="<font color=silver><u>POSTULANTE</u>:</font> ".$rows['appa_post'].' '.$rows['apma_post'].', '.$rows['nomb_post'].'\n'."<font color=silver><u>PLAZA</u>:</font> ".$arra_options_plaza[$rows['codi_plaz']];
	    }
//echo"<script>alert('EOK ".$_POST['codi_post']."')</script>";
        $fdig=date("YmdHis");
	    if($_POST['codi_post'])
	    {
	        //$result=$Db->update('mp_concurso_postulantes',['regi_asis'=>'1','fdig_asis'=>"$fdig"],['codi_post'=>$_POST['codi_post']]);  //revisar, no funciona esta linea
	        $result=$Db->query("update mp_concurso_postulantes set regi_asis='1',fdig_asis='".$fdig."' where codi_post='".$_POST['codi_post']."'");
	        echo"<script>
	            parent.document.getElementById('div-mensajes').innerHTML = '$mens_post_agre';
	            setTimeout(function(){
                    parent.document.getElementById('div-mensajes').innerHTML ='';
                }, 3000);
	        </script>";  
	    }
	    else
	        //echo"<script>f_mensaje('<FONT COLOR=SILVER><U>ERROR</U>:</FONT> DOCUMENTO NO REGISTRADOx');</script>";
	        echo"<script>
	            parent.document.getElementById('div-mensajes').innerHTML = '<FONT COLOR=SILVER><U>ERROR</U>:</FONT> DOCUMENTO NO REGISTRADO';
	            setTimeout(function(){
                    parent.document.getElementById('div-mensajes').innerHTML ='';
                }, 2000);
	        </script>";
	        //echo"<script>alert('ERROR: Documento no registrado')</script>";
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
		
		<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
		
		<script>
			function f_registrar()
			{
			    document.form.regi_post.value='1';
				document.form.action='';
				document.form.target="";
				document.form.submit();
			}
			function f_listado(proc,plaz)
			{
				document.form.codi_plaz.value=plaz;
				document.form.codi_proc.value=proc;
				document.form.action='classes/TCPDF/examples/concurso_listado.php';
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
	    
	<center><h2 style="color:#073A6B">RESUMEN DE ASISTENCIA <BR>[<?=$_POST['fech_exam']?>] <?=$_POST['nomb_exam']?></h2></center>
		<form name="form" method="post">
			<input type=hidden name="regi_post">
			<input type=hidden name="elim_asis">
			<input type=hidden name="codi_plaz">
			<input type=hidden name="codi_proc">
			<input type=hidden name="busc_pagi_actu" value="<?=$_POST['busc_pagi_actu']?>">
			<input type=hidden name="codi_form" value="<?=$_POST['codi_form']?>">
			<input type=hidden name="codi_exam" value="<?=$_POST['codi_exam']?>">
			<input type=hidden name="fech_exam" value="<?=$_POST['fech_exam']?>">
			<input type=hidden name="nomb_exam" value="<?=$_POST['nomb_exam']?>">
			<input type=hidden name="list_plaz" value="<?=$_POST['list_plaz']?>">
<?
	$html=new htmlclass;
	
	$busc_item_pagi=1000;      //cantidad de items por pagina
	
	$result=$Db->select('mp_maes_concurso_regimen', '', '', '', ['x_nombre'=>'ASC']);
	foreach($result as $rows)
		$arra_options_regi[$rows['n_codigo']]=$rows['x_nombre'];
	
	$result=$Db->query("select a.codi_proc proc, a.regi_proc regi, a.nume_proc nume, a.anno_proc anno,b.codi_plaz plaz,b.nomb_plaz npla from mp_concurso_proceso=a,mp_concurso_plazas=b where a.codi_proc=b.codi_proc AND b.codi_plaz in (".$_POST['list_plaz'].")");
	foreach($result as $rows)
	{
	    $arra_proc[$rows['proc']]=$arra_options_regi[$rows['regi']]." Nro. ".$rows['nume']."-".$rows['anno'];
	    $arra_plaz[$rows['plaz']]=$rows['npla'];
	}
	
	$result=$Db->query("select b.codi_plaz plaz,count(*) cant from mp_concurso_plazas=b,mp_concurso_postulantes=c where b.codi_plaz=c.codi_plaz AND b.codi_plaz in (".$_POST['list_plaz'].") AND regi_asis='1' AND b.esta_plaz='1' AND c.esta_post='1' group by b.codi_plaz");
	$arra_plaz_asis = [];
	foreach($result as $rows)
	    $arra_plaz_asis[$rows['plaz']]=$rows['cant'];
	
	$result=$Db->query("select a.codi_proc,b.codi_plaz,count(*) from mp_concurso_proceso=a,mp_concurso_plazas=b,mp_concurso_postulantes=c where a.codi_proc=b.codi_proc AND b.codi_plaz=c.codi_plaz AND a.codi_exam='".$_POST['codi_exam']."' AND a.esta_proc='1' AND b.esta_plaz='1' AND c.esta_post='1' group by a.codi_proc,b.codi_plaz");
	$busc_tota_item=0;
	foreach($result as $rows)
	{       
		$busc_tota_item++;
	}

	$busc_tota_pagi=ceil($busc_tota_item/$busc_item_pagi);
	$busc_limi_pagi=($_POST['busc_pagi_actu']-1)*$busc_item_pagi;

	$result_pagi=$Db->query("select a.codi_proc proc,b.codi_plaz plaz,count(*) cant from mp_concurso_proceso=a,mp_concurso_plazas=b,mp_concurso_postulantes=c where a.codi_proc=b.codi_proc AND b.codi_plaz=c.codi_plaz AND a.codi_exam='".$_POST['codi_exam']."' AND a.esta_proc='1' AND b.esta_plaz='1' AND c.esta_post='1' group by a.codi_proc,b.codi_plaz limit $busc_limi_pagi,$busc_item_pagi");
	echo"<div style=\"width:90%;max-width:800px;margin:auto;\">";
	echo $html->put_title_demand("CANTIDAD DE PLAZAS: $busc_tota_item");

	if($busc_tota_pagi>0  OR 5==5)
		echo $html->put_page("1",$_POST['busc_pagi_actu'],$busc_tota_pagi,"");
	$head=['1'=>"Nº",'2'=>"EXAMEN",'3'=>"PROCESO",'4'=>"PLAZA",'5'=>"POSTUL.",'6'=>"ASIST.",'7'=>"AUSENT.",'8'=>"LISTADO"];
	echo $html->put_table_responsive_open();
	$tota_post=0;
	$tota_asis=0;
	$tota_ause=0;
	if($busc_tota_item OR 5==5)
	{
		echo $html->put_table_responsive_header($head);
		$cont=$busc_limi_pagi;
		foreach($result_pagi as $rows)
		{
			$cont++;
			$asis='NO';
			if(isset($rows['regi_asis']) && $rows['regi_asis'])
			    $asis='SI';
			$data=[	'1'=>$cont,
				'2'=>$_POST['fech_exam'],
				'3'=>$arra_proc[$rows['proc']],
				'4'=>$arra_plaz[$rows['plaz']],
				'5'=>$rows['cant'],
				'6'=>number_format((isset($arra_plaz_asis[$rows['plaz']]) ? $arra_plaz_asis[$rows['plaz']] : 0),0),
				'7'=>number_format($rows['cant']-(isset($arra_plaz_asis[$rows['plaz']]) ? $arra_plaz_asis[$rows['plaz']] : 0),0),
				'8'=>"<a href=\"javascript:f_listado('$rows[proc]','$rows[plaz]')\"><img src=\"img/pdf_image.gif\" width=\"20\">",
			];
			$tota_post+=$rows['cant'];
			$tota_asis+=(isset($arra_plaz_asis[$rows['plaz']]) ? $arra_plaz_asis[$rows['plaz']] : 0);
			echo $html->put_table_responsive_data($head,$data);
		}
	}
	else
		echo $html->put_table_responsive_title("No Existen Postulantes");
	
	if($cont>0)
	{
		$cont++;
		$data=[	'1'=>'',
			'2'=>'',
			'3'=>'',
			'4'=>'<B>TOTAL',
			'5'=>'<B>'.number_format($tota_post,0),
			'6'=>'<B>'.number_format($tota_asis,0),
			'7'=>'<B>'.number_format($tota_post-$tota_asis,0),
			'8'=>'',
		];
		echo $html->put_table_responsive_data($head,$data);
	}
		
	echo $html->put_table_responsive_close();
	if($busc_tota_pagi>0  OR 5==5)
		echo $html->put_page("2",$_POST['busc_pagi_actu'],$busc_tota_pagi,"");
	echo"</div>";
	if($busc_tota_item>0 AND 5==6)
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
?>
<center>
    <script>document.form.docu_post.focus();</script>
	</form>
	</body>
</html>
