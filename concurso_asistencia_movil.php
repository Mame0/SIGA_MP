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
	}
	if($_POST['codi_exam'])
	{
        $result=$Db->query("select a.codi_proc,b.codi_plaz from mp_concurso_proceso=a,mp_concurso_plazas=b where a.codi_proc=b.codi_proc AND a.codi_exam='".$_POST['codi_exam']."' AND a.esta_proc='1' AND b.esta_plaz='1'");
        $_POST['list_plaz']='';
        foreach($result as $rows)
	        $_POST['list_plaz'].=",".$rows['codi_plaz'];
        $_POST['list_plaz']=substr($_POST['list_plaz'],1);
        
	    if(isset($_POST['codi_plaz']) && $_POST['codi_plaz'])
	        $_POST['list_plaz_dupl']=$_POST['codi_plaz'];
	    else
	        $_POST['list_plaz_dupl']=$_POST['list_plaz'];

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
	    $veri_dupl=0;
	    $plaz_repe="";
	    $result=$Db->query("select * from mp_concurso_postulantes where docu_post='".$_POST['docu_post']."' AND codi_plaz in (".$_POST['list_plaz_dupl'].") AND esta_post='1'");
	    foreach($result as $rows)
	    {
	        $veri_dupl++;
	        $plaz_repe.=" - ".$arra_options_plaza[$rows['codi_plaz']];
	        $_POST['codi_post']=$rows['codi_post'];
	        $mens_post_agre="<font color=silver><u>POSTULANTE</u>:</font> ".$rows['appa_post'].' '.$rows['apma_post'].', '.$rows['nomb_post'].'\n'."<font color=silver><u>PLAZA</u>:</font> ".$arra_options_plaza[$rows['codi_plaz']];
	    }
//echo"<script>alert('EOK ".$_POST['codi_post']."')</script>";
        $fdig=date("YmdHis");
	    if(isset($_POST['codi_post']) && $_POST['codi_post'] AND $veri_dupl<2)
	    {
	        $result=$Db->update('mp_concurso_postulantes',['regi_asis'=>'1','fdig_asis'=>"$fdig"],['codi_post'=>$_POST['codi_post']]);  //revisar, no funciona esta linea
	        //$result=$Db->query("update mp_concurso_postulantes set regi_asis='1',fdig_asis='".$fdig."' where codi_post='".$_POST['codi_post']."'");
	        echo"<script>
	            parent.document.getElementById('header').style.background='#45BE00';
	            parent.document.getElementById('div-mensajes').innerHTML = '$mens_post_agre';
	            setTimeout(function(){
                    parent.document.getElementById('div-mensajes').innerHTML ='';
                    parent.document.getElementById('header').style.background='#073A6B';
                }, 6000);
	        </script>";  
	    }
	    else
	    {
	        //echo"<script>f_mensaje('<FONT COLOR=SILVER><U>ERROR</U>:</FONT> DOCUMENTO NO REGISTRADOx');</script>";
	        if($veri_dupl>1)   
	            $mens="POSTULANTE DUPLICADO - SELECCIONE PLAZA $plaz_repe";
	        else
	        {
	            if($_POST['codi_plaz'])
	                $mens="DOCUMENTO NO REGISTRADO EN PLAZA ".$arra_options_plaza[$_POST['codi_plaz']];
	            else
	                $mens="DOCUMENTO NO REGISTRADO EN NINGUNA PLAZA";
	        }
	        echo"<script>
	            parent.document.getElementById('header').style.background='#FF0000';
	            parent.document.getElementById('div-mensajes').innerHTML = '<FONT COLOR=SILVER><U>ERROR</U>:</FONT> $mens';
	            setTimeout(function(){
                    parent.document.getElementById('div-mensajes').innerHTML ='';
                    parent.document.getElementById('header').style.background='#073A6B';
                }, 6000);
	        </script>";
	        //echo"<script>alert('ERROR: Documento no registrado')</script>";
	    }
	}
	
?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8" />
		<title></title>
		<link rel="stylesheet" href="css/forms_demanda.css" />
		<link rel="stylesheet" href="css/forms_foot.css" />
		<link rel="stylesheet" href="css/forms_column.css" />
		<SCRIPT language="JavaScript" src="js/cadenas.js"></SCRIPT>
		<SCRIPT language="JavaScript" src="js/denuncia.js"></SCRIPT>
		
		<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
		
		<script>
		    function f_mensaje(mens)
		    {
		        parent.document.getElementById('div-mensajes').innerHTML = mens;
		    }
			function f_registrar()
			{
			    document.form.regi_post.value='1';
				document.form.action='';
				document.form.target="";
				document.form.submit();
			}
			function f_eliminar(docu,appa,apma,nomb,codi)
			{
			    if(confirm('Seguro que desea eliminar asistencia de: \n '+nomb+' '+appa+' '+apma+' ['+docu+']'))
			    {
			        document.form.elim_asis.value=codi;
			        document.form.submit();
			    }
			    else
			        swal("Oops!", "Something went wrong on the page!", "error");
			}
			function f_generar_fotocheck(tipo)
			{
				document.form.action='classes/TCPDF/examples/voluntariado_fotocheck.php';
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
	    
	<center><h4 style="color:#073A6B"><b>CONTROL DE ASISTENCIA <BR>[<?=$_POST['fech_exam']?>] <?=$_POST['nomb_exam']?></h4></b></center>
		<form name="form" method="post">
			<input type=hidden name="regi_post">
			<input type=hidden name="elim_asis">
			<input type=hidden name="busc_pagi_actu" value="<?=$_POST['busc_pagi_actu']?>">
			<input type=hidden name="codi_form" value="<?=$_POST['codi_form']?>">
			<input type=hidden name="codi_exam" value="<?=$_POST['codi_exam']?>">
			<input type=hidden name="fech_exam" value="<?=$_POST['fech_exam']?>">
			<input type=hidden name="nomb_exam" value="<?=$_POST['nomb_exam']?>">
			<input type=hidden name="list_plaz" value="<?=$_POST['list_plaz']?>">
<?
	$html=new htmlclass;
	
	$result=$Db->select('mp_maes_concurso_regimen', '', '', '', ['x_nombre'=>'ASC']);
	foreach($result as $rows)
		$arra_options_regi[$rows['n_codigo']]=$rows['x_nombre'];
	
	$result=$Db->select('mp_concurso_proceso','','','',['codi_proc'=>'ASC']);
	$arra_options_proc[0]="<- ".CONST_OPTION_SELECT." ->";
	foreach ($result as $rows)
		$arra_options_proc[$rows['codi_proc']]=$arra_options_regi[$rows['regi_proc']]." ".$rows['nume_proc']."-".$rows['anno_proc'];
		
	//$result=$Db->select('mp_concurso_plazas','','','',['codi_plaz'=>'ASC']);
	$result=$Db->query("select * from mp_concurso_plazas where codi_plaz in(".$_POST['list_plaz'].")");
	$arra_options_plaz[0]="<- Todas ->";
	foreach ($result as $rows)
		$arra_options_plaz[$rows['codi_plaz']]=$arra_options_proc[$rows['codi_proc']]." - ".$rows['nomb_plaz'];
	
	echo"<main>";
	//echo $html->put_title_demand("INGRESE DOCUMENTO");
	echo $html->put_select("Plaza",'codi_plaz',$arra_options_plaz,(isset($_POST['codi_plaz']) ? $_POST['codi_plaz'] : ''),"");
	echo $html->put_text('text',"DNI","Ingrese DNI",'docu_post','','','15','');
	echo $html->put_button_colum("&nbsp;","Registrar Asistencia &raquo;","return f_registrar()");
	
	echo"</main>";
	//echo"<main>";
	//echo $html->put_select("Formato",'codi_form',$arra_options_form,$_POST['codi_form'],"");
	echo"</main>";

/*
	$busc_item_pagi=1000;      //cantidad de items por pagina
	
	$result=$Db->query("select * from mp_concurso_postulantes where regi_asis='1' AND codi_plaz in(".$_POST['list_plaz'].")");
	//$result=$Db->query("select * from mp_concurso_postulantes where codi_plaz='".."'");
	$busc_tota_item=0;
	foreach($result as $rows)
	{       
		$busc_tota_item++;
	}

	$busc_tota_pagi=ceil($busc_tota_item/$busc_item_pagi);
	$busc_limi_pagi=($_POST['busc_pagi_actu']-1)*$busc_item_pagi;

	$result_pagi=$Db->query("select * from mp_concurso_postulantes where regi_asis='1' AND codi_plaz in(".$_POST['list_plaz'].") order by appa_post,apma_post,nomb_post asc limit $busc_limi_pagi,$busc_item_pagi");

	echo"<div style=\"width:90%;max-width:800px;margin:auto;\">";
	echo $html->put_title_demand("CANTIDAD DE REGISTROS: $busc_tota_item ASISTENTES");

	if($busc_tota_pagi>0  OR 5==5)
		echo $html->put_page("1",$_POST['busc_pagi_actu'],$busc_tota_pagi,"");
	$head=['1'=>"Nº",'2'=>"DOCUMENTO",'3'=>"AP.PATERNO",'4'=>"AP.MATERNO",'5'=>"NOMBRES",'6'=>"ASISTENCIA",'7'=>"PLAZA",'8'=>"ELIM"];
	echo $html->put_table_responsive_open();
	if($busc_tota_item OR 5==5)
	{
		echo $html->put_table_responsive_header($head);
		$cont=$busc_limi_pagi;
		foreach($result_pagi as $rows)
		{
			$cont++;
			$asis='NO';
			if($rows['regi_asis'])
			    $asis='SI';
			$data=[	'1'=>$cont,
				'2'=>$rows['docu_post'],
				'3'=>utf8_encode(utf8_decode(strtoupper($rows['appa_post']))),
				'4'=>utf8_encode(utf8_decode(strtoupper($rows['apma_post']))),
				'5'=>utf8_encode(utf8_decode(strtoupper($rows['nomb_post']))),
				'6'=>$asis,
				'7'=>$arra_options_plaza[$rows['codi_plaz']],
				'8'=>"<a href=\"javascript:f_eliminar('$rows[docu_post]','$rows[appa_post]','$rows[apma_post]','$rows[nomb_post]','$rows[codi_post]')\"><img src=\"img/delete.png\" width=\"20\">",
			];
			echo $html->put_table_responsive_data($head,$data);
		}
	}
	else
		echo $html->put_table_responsive_title("No Existen Postulantes");
		
	echo $html->put_table_responsive_close();
	if($busc_tota_pagi>0  OR 5==5)
		echo $html->put_page("2",$_POST['busc_pagi_actu'],$busc_tota_pagi,"");
	echo"</div>";
*/
	if(isset($busc_tota_item) && $busc_tota_item>0 AND 5==6)
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
