<?
//classes/TCPDF/examples/personal_fotocheck.php
	require_once 'include/cabecera.php';
	require_once 'classes/Html.class.php';
	require_once 'classes/Db.class.php';
	$Db = new Db();
	if(!isset($_POST['busc_pagi_actu']))
		$_POST['busc_pagi_actu']=1;
		
		//echo"<HR>".$_SESSION['codi_depe']."<HR>";
	
	if(!empty($_POST['codi_elim']))
	{
	    $obse="Usuario: ".$_SESSION['logi_oper']."\nFecha: ".date("d/m/Y H:i:s")."\nObservación: ".$_POST['obse_elim'];
	    $result=$Db->query("update mp_mpar_carpetas set esta_mpar='0',obse_mpar='$obse' where codi_mpar='".$_POST['codi_elim']."' AND esta_mpar='1'");
	}
	
	if(isset($_POST['chec_todo']) && $_POST['chec_todo'])
	{
		$fdig=date("YmdHis");
		
		//ANTES DE ASIGNAR, ACTUALIZA LA TABLA mp_mpar_despachos
		    //guarda los flag=1 para regresarlos despues
		    $arra_flag = [];
		    $result=$Db->query("select * from mp_mpar_despachos where flag_desp='1'");
		    foreach($result as $rows)
    		    $arra_flag[$rows['codi_depe']][$rows['codi_pers']]=1;
    		
    		$list_depe = '';
    		$result=$Db->query("select distinct codi_depe from mp_mpar_despachos");
    		foreach($result as $rows)
    		    $list_depe.=",".$rows['codi_depe'];
    		$list_depe=substr($list_depe,1);
		
		    $result=$Db->query("delete from mp_mpar_despachos");
	
            if ($list_depe) {
    	        $result=$Db->query("select * from mp_admi_depe where codi_depe in ($list_depe)");
                foreach($result as $rows)	
    		    {
    		        $result1=$Db->query("select * from mp_maes_personal where codi_depe='".$rows['codi_depe']."' AND codi_carg>=17 AND codi_carg<=20 AND esta_pers='1' order by codi_carg desc");
    			    foreach($result1 as $rows1)
    			    {
    			        $flag_val = isset($arra_flag[$rows['codi_depe']][$rows1['iden_pers']]) ? $arra_flag[$rows['codi_depe']][$rows1['iden_pers']] : 0;
    					$result2=$Db->query("insert into mp_mpar_despachos values('".$rows['codi_depe']."','".$rows1['iden_pers']."','".$flag_val."')");
    			    }
    		    }
            }
		//////////////////////////////////////
		
		//genera una cadena con las dependencias asignadas
		$cade_depe='';
		$result=$Db->query("select * from mp_mpar_asignacion a, mp_mpar_despachos b where a.codi_dest=b.codi_depe AND a.codi_orig='".$_SESSION['codi_depe']."'");
		$fisc_tota=0;   //fiscales en total asignadas
		$fisc_disp=0;   //fiscales disponibles para ser asignados
		foreach($result as $rows)
		{
		    $fisc_tota++;
		    $cade_depe.=",".$rows['codi_depe']."_".$rows['codi_pers'];
		    if($rows['flag_desp']==0)
		        $fisc_disp++;
		}
		$cade_depe=substr($cade_depe,1);
		
		echo"<HR>$fisc_tota<HR>".$_SESSION['codi_depe']."<HR>";
		
		if($fisc_tota>0)
		{
		    $result=$Db->query("select * from mp_mpar_carpetas where esta_mpar='1' AND codi_depe='0' AND codi_pers='0' AND depe_mpar='".$_SESSION['codi_depe']."' order by rand()");
    		//echo"<HR>select * from mp_mpar_carpetas where esta_mpar='1' AND codi_depe='0' AND codi_pers='0' AND depe_mpar='".$_SESSION['codi_depe']."' order by rand()";
    		foreach($result as $rows)
    		{
    		    //verificamos si existen disponibles
    		    if($fisc_disp==0)
    		    {
    		        //ponemos todos los flag a CERO
    		        $result4=$Db->query("select * from mp_mpar_asignacion a, mp_mpar_despachos b where a.codi_dest=b.codi_depe AND a.codi_orig='".$_SESSION['codi_depe']."'");
    		        foreach($result4 as $rows4)
    		        {
    		            $result5=$Db->query("update mp_mpar_despachos set flag_desp='0' where codi_depe='".$rows4['codi_depe']."' AND codi_pers='".$rows4['codi_pers']."'");
    		        }
    		        $fisc_disp=$fisc_tota;  //reiniciamos
    		    }
    		    
    		    $result2=$Db->query("select * from mp_mpar_asignacion a, mp_mpar_despachos b where a.codi_dest=b.codi_depe AND a.codi_orig='".$_SESSION['codi_depe']."' AND flag_desp=0 order by rand() limit 1");
    		    //echo"<HR>select * from mp_mpar_asignacion a, mp_mpar_despachos b where a.codi_dest=b.codi_depe AND a.codi_orig='".$_SESSION['codi_depe']."' AND flag_desp=0 order by rand() limit 1";
    		    foreach($result2 as $rows2)
    		    {
    		        //registra lo escogido
    		        $result3=$Db->query("update mp_mpar_carpetas set codi_depe='".$rows2['codi_depe']."',codi_pers='".$rows2['codi_pers']."',fech_asig='$fdig',digi_asig='".$_SESSION['iden_oper']."' where codi_mpar='".$rows['codi_mpar']."'");
    		        //pone el flag en 1 para que no vuelva a ser escogido
    		        $result3=$Db->query("update mp_mpar_despachos set flag_desp='1' where codi_depe='".$rows2['codi_depe']."' AND codi_pers='".$rows2['codi_pers']."'");
    		        //disminuye en 1 la disponibilidad
    		        $fisc_disp--;
    		    }
    		}
		}
		/*
		if($ya_existe==0)
		{
			$result=$Db->insert('mp_mpar_carpetas',['mpar_cbar'=>$_POST['codi_mpar_agre'],'depe_mpar'=>$_SESSION['codi_depe'],'esta_mpar'=>'1','digi_mpar'=>$_SESSION['iden_oper'],'fdig_mpar'=>"$fdig"]);
			$_POST['codi_mpar']=$Db->lastInsertId();
		}
		else
		{
		    echo"<script>alert('ERROR: Carpeta ya fue ingresada');</script>";
		}
		unset($_POST['codi_mpar'],$_POST['codi_mpar_agre']);

		echo"
		                <html><body>
                                <form name=\"form\" method=post action=\"mpartes_lectura_general.php\">
					<input type=hidden name=\"busq_pagi_actu\" value=\"".$_POST['busq_pagi_actu']."\">
                                </form>
                                <script>
                                        document.form.submit();
                                </script>
                        </body></html>
		";
        */
	}
	
	$nomb_depe = '';
    $result=$Db->query("select * from mp_admi_depe where codi_depe='".$_SESSION['codi_depe']."' ");
    foreach($result as $rows)
        $nomb_depe=$rows['abre_depe'];
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
	<center><h2 style="color:#073A6B">INGRESO DE MESA DE PARTES<BR><?=$nomb_depe?></h2></center>
		<form name="form" method="post">
			<input type=hidden name="codi_elim">
			<input type=hidden name="obse_elim">
			<input type=hidden name="chec_todo">
			<input type=hidden name="busc_pagi_actu" value="<?=$_POST['busc_pagi_actu']?>">
<?
	$html=new htmlclass;

	$arra_options_espe[0]="<- Todas ->";
        $result=$Db->select('mp_maes_jurisprudencia_especialidad', '', '', '', ['x_nombre'=>'ASC']);
        foreach($result as $rows)
                $arra_options_espe[$rows['n_codigo']]=$rows['x_nombre'];

    $result=$Db->select('mp_maes_mpar_tdoc', '', '', '', ['n_codigo'=>'ASC']);
    foreach($result as $rows)
            $arra_options_tdoc[$rows['n_codigo']]=$rows['x_nombre'];

	$busc_item_pagi=40;      //cantidad de items por pagina

	//$result=$Db->query("select * from mp_jurisprudencia_documento where nomb_docu like '%:m_busq%'",[':m_busq'=>$_POST['text_busc']]);
	$result=$Db->query("select * from mp_mpar_carpetas where esta_mpar=1 AND codi_depe=0 AND codi_pers=0 AND depe_mpar='".$_SESSION['codi_depe']."' order by fdig_mpar desc");
	$busc_tota_item=0;
	foreach($result as $rows)
	{       
		$busc_tota_item++;
	}
	$busc_tota_pagi=ceil($busc_tota_item/$busc_item_pagi);
	$busc_limi_pagi=($_POST['busc_pagi_actu']-1)*$busc_item_pagi;

	$result_pagi=$Db->query("select * from mp_mpar_carpetas where esta_mpar=1 AND codi_depe=0 AND codi_pers=0 AND depe_mpar='".$_SESSION['codi_depe']."' order by fdig_mpar desc limit $busc_limi_pagi,$busc_item_pagi");
	echo"<div style=\"width:90%;max-width:800px;margin:auto;\">";
	echo $html->put_title_demand("CARPETAS SIN ASIGNAR: $busc_tota_item DISPONIBLES");
	if($busc_tota_pagi>0)
		echo $html->put_page("1",$_POST['busc_pagi_actu'],$busc_tota_pagi,"");
	$head=['1'=>"Nº",'2'=>"CODIGO",'3'=>"DOCUMENTO",'4'=>"F.INGRESO",'5'=>"USUARIO",'6'=>"DESPACHO",'7'=>"FISCAL",'8'=>"&nbsp;"];
	echo $html->put_table_responsive_open();
	if($busc_tota_item)
	{
		echo $html->put_table_responsive_header($head);
		$cont=$busc_limi_pagi;
		foreach($result_pagi as $rows)
		{
			$cont++;
			$rows['fdig_mpar']=substr($rows['fdig_mpar'],6,2).'/'.substr($rows['fdig_mpar'],4,2).'/'.substr($rows['fdig_mpar'],0,4).' '.substr($rows['fdig_mpar'],8,2).':'.substr($rows['fdig_mpar'],10,2);
			$data=[	'1'=>$cont,
				'2'=>$rows['anno_mpar'].'-'.str_pad($rows['nume_mpar'], 4, '0', STR_PAD_LEFT),
                '3'=>(isset($arra_options_tdoc[$rows['tdoc_mpar']]) ? $arra_options_tdoc[$rows['tdoc_mpar']] : '')."<BR>".$rows['mpar_cbar'],
                '4'=>$rows['fdig_mpar'],
				'5'=>$_SESSION['logi_oper'],
				'6'=>'<font color=silver><i>Pendiente',
				'7'=>'<font color=silver><i>Pendiente',
				'8'=>"<a href=\"javascript:f_eliminar('$rows[codi_mpar]','$rows[mpar_cbar]')\"><img src=\"img/delete.png\" width=\"20\">",
			];
			    //'2'=>"<input type=checkbox name=\"chec_mpar_".$rows['codi_mpar']."\">",
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
                                                <button class=\"button_foot\" onclick=\"document.form.submit()\">Recargar Reporte</button>
                                        </div>
                                        <div class=\"div_button_foot\"><center>
                                                <button class=\"button_foot\" onclick=\"f_asignar('2')\">Asignar Aleatoriamente</button>
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
