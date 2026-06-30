<?
	require_once 'include/cabecera.php';
	require_once 'classes/Html.class.php';
	require_once 'classes/Db.class.php';
	$Db = new Db();

    // Inicializar variables $_POST para evitar advertencias
    $vars = ['saveinfo', 'pass_ope1', 'pass_ope2', 'fexp_oper', 'rese_oper', 'flag_band', 'iden_oper', 'logi_oper', 'appa_oper', 'apma_oper', 'nomb_oper', 'carg_oper', 'celu_oper', 'mail_oper', 'codi_depe', 'codi_perf', 'esta_oper', 'firm_oper'];
    foreach($vars as $var) {
        if(!isset($_POST[$var])) $_POST[$var] = '';
    }
    
    // Inicializar array de roles
    $arra_oper_role = [];
	
	if(!empty($_POST['saveinfo']))	//guardar
	{
		$fdig=date("YmdHis");
		
		if($_POST['pass_ope1'] AND $_POST['pass_ope1']==$_POST['pass_ope2'])
			$pass_oper=md5($_POST['pass_ope1']);
		
		$fexp_oper=substr($_POST['fexp_oper'],0,4).substr($_POST['fexp_oper'],5,2).substr($_POST['fexp_oper'],8,2);

		if($_POST['rese_oper'])	$_POST['rese_oper']=1;
		if($_POST['flag_band']) $_POST['flag_band']=1;

		if($_POST['iden_oper']==9999)	//nuevo
		{
			$result=$Db->insert('mp_admi_oper',['logi_oper'=>$_POST['logi_oper'],'pass_oper'=>"$pass_oper",'appa_oper'=>$_POST['appa_oper'],'apma_oper'=>$_POST['apma_oper'],'nomb_oper'=>$_POST['nomb_oper'],'carg_oper'=>$_POST['carg_oper'],'celu_oper'=>$_POST['celu_oper'],'mail_oper'=>$_POST['mail_oper'],'codi_depe'=>$_POST['codi_depe'],'codi_perf'=>$_POST['codi_perf'],'flag_band'=>$_POST['flag_band'],'esta_oper'=>$_POST['esta_oper'],'fexp_oper'=>"$fexp_oper",'digi_oper'=>$_SESSION['iden_oper'],'fdig_oper'=>$fdig,'rese_oper'=>$_POST['rese_oper']]);
			$_POST['iden_oper']=$Db->lastInsertId();
		}
		else	//update
		{
			if($pass_oper)
				$result=$Db->update('mp_admi_oper',['logi_oper'=>$_POST['logi_oper'],'pass_oper'=>"$pass_oper",'appa_oper'=>$_POST['appa_oper'],'apma_oper'=>$_POST['apma_oper'],'nomb_oper'=>$_POST['nomb_oper'],'carg_oper'=>$_POST['carg_oper'],'celu_oper'=>$_POST['celu_oper'],'mail_oper'=>$_POST['mail_oper'],'codi_depe'=>$_POST['codi_depe'],'codi_perf'=>$_POST['codi_perf'],'flag_band'=>$_POST['flag_band'],'esta_oper'=>$_POST['esta_oper'],'fexp_oper'=>"$fexp_oper",'digi_oper'=>$_SESSION['iden_oper'],'fdig_oper'=>$fdig,'rese_oper'=>$_POST['rese_oper']],['iden_oper'=>$_POST['iden_oper']]);
			else
				$result=$Db->update('mp_admi_oper',['logi_oper'=>$_POST['logi_oper'],'appa_oper'=>$_POST['appa_oper'],'apma_oper'=>$_POST['apma_oper'],'nomb_oper'=>$_POST['nomb_oper'],'carg_oper'=>$_POST['carg_oper'],'celu_oper'=>$_POST['celu_oper'],'mail_oper'=>$_POST['mail_oper'],'codi_depe'=>$_POST['codi_depe'],'codi_perf'=>$_POST['codi_perf'],'flag_band'=>$_POST['flag_band'],'esta_oper'=>$_POST['esta_oper'],'fexp_oper'=>"$fexp_oper",'digi_oper'=>$_SESSION['iden_oper'],'fdig_oper'=>$fdig,'rese_oper'=>$_POST['rese_oper']],['iden_oper'=>$_POST['iden_oper']]);
		}
		if($_FILES["nuev_firm"]["tmp_name"] AND $_POST['iden_oper'])
		{
			//$nuev_firm="img/temp/29709217.jpg";
			$nuev_firm=$_FILES["nuev_firm"]["tmp_name"];
			$result=$Db->dataUserBlob($_POST['iden_oper'],"$nuev_firm");
		}
		if($_POST['iden_oper'])
		{
			$resultr=$Db->delete('mp_admi_oper_role',['iden_oper'=>$_POST['iden_oper']]);
			$resultr=$Db->select('mp_admi_role',['esta_role'=>1],'','',['nomb_role'=>'ASC']);
			foreach ($resultr as $rows)
			{
				if($_POST['role_'.$rows['iden_role']])
					$resultr=$Db->insert('mp_admi_oper_role',['iden_oper'=>$_POST['iden_oper'],'iden_role'=>$rows['iden_role']]);
			}
		}
		if($result)
		{
			echo"<script>alert('".constant("CONST_MENS_REG_OK")."')</script>";
			unset($_POST);
		}
		else
			echo"<script>alert('".constant("CONST_MENS_REG_ERROR")."')</script>";
	}
	if(!empty($_POST['iden_oper']))
	{
		$result=$Db->select('mp_admi_oper',['iden_oper'=>$_POST['iden_oper']]);
		$_POST['appa_oper']=$result[0]['appa_oper'];
		$_POST['apma_oper']=$result[0]['apma_oper'];
		$_POST['nomb_oper']=$result[0]['nomb_oper'];
		$_POST['codi_depe']=$result[0]['codi_depe'];
		$_POST['codi_perf']=$result[0]['codi_perf'];
		$_POST['flag_band']=$result[0]['flag_band'];
		$_POST['logi_oper']=$result[0]['logi_oper'];
		$_POST['carg_oper']=$result[0]['carg_oper'];
		$_POST['celu_oper']=$result[0]['celu_oper'];
		$_POST['mail_oper']=$result[0]['mail_oper'];
		$_POST['esta_oper']=$result[0]['esta_oper'];
        $fexp = (string)$result[0]['fexp_oper'];
		$_POST['fexp_oper']=substr($fexp,0,4).'-'.substr($fexp,4,2).'-'.substr($fexp,6,2);
		$_POST['rese_oper']=$result[0]['rese_oper'];

		$arra_oper=$Db->selectUserBlob($_POST['iden_oper']);
		$_POST['firm_oper']=$arra_oper['firm_oper'];
	}
?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8" />
		<title>SIOJAlimentos</title>
		<link rel="stylesheet" href="css/forms.css" />
		<script>
			function check()
			{
				if(document.form.pass_ope1.value==document.form.pass_ope2.value)
				{
					document.form.saveinfo.value=1;
					return true;
				}
				else
					alert('<?=CONST_MENS_PASS_ERROR?>');
			}
		</script>
	</head>
	<body>
		<form name="form" method="post" enctype="multipart/form-data">
			<input type=hidden name="saveinfo">
		<center>
<h2><?=CONST_TITLE_USER?></h2><br>
		</center>
<?
	//$result=$Db->select(['mp_admi_oper',['n_codi_ginstruccion','x_ginstruccion']],['n_estado'=>1]);
	//--------------
	$result=$Db->select('mp_admi_oper','','','',['appa_oper'=>'ASC']);
	$arra_options_oper[0]="<- Seleccione Operador ->";
	$arra_options_oper[9999]="<- Crear Nuevo Operador ->";
	foreach ($result as $rows)
		$arra_options_oper[$rows['iden_oper']]=strtoupper($rows['appa_oper']." ".$rows['apma_oper']." ".$rows['nomb_oper']);

	//--------------
/*
	$arra_options_dependencia[0]="<- Seleccione Dependencia ->";
	$cresult_dependencia=$Db->query("select d.codi_depe,a.codi_inst,a.sigl_inst,d.sigl_depe,d.nomb_depe from mp_depe_institucion as a, mp_depe_distrito as b, mp_depe_sede as c, mp_depe_dependencia as d where d.codi_sede=c.codi_sede AND c.codi_dist=b.codi_dist AND b.codi_inst=a.codi_inst");
	foreach($cresult_dependencia as $rows)
		$arra_options_dependencia[$rows['codi_depe']]=$rows['sigl_inst']." - ".$rows['sigl_depe']." (".$rows['nomb_depe'].")";
*/
	$arra_options_perfil=$Db->get_options("mp_maes_cargo",1,0);

	$result=$Db->select('mp_admi_oper_role',['iden_oper'=>$_POST['iden_oper']]);
	foreach ($result as $rows)
	{
		$arra_oper_role[$rows['iden_role']]='checked';
	}
	$result=$Db->select('mp_admi_role',['esta_role'=>1],'','',['nomb_role'=>'ASC']);
	$cont_role="<table border=0>";
	foreach ($result as $rows)
    {
        $checked = isset($arra_oper_role[$rows['iden_role']]) ? $arra_oper_role[$rows['iden_role']] : '';
		$cont_role.="<tr><td width=1%><input type=checkbox id=\"role_".$rows['iden_role']."\" name=\"role_".$rows['iden_role']."\" ".$checked."></td><td>".$rows['nomb_role']."</td></tr>";
    }
	$cont_role.="</table>";

	$html=new htmlclass;

/*
	$result=$Db->select('mp_depe_institucion','','','',['nomb_inst'=>'ASC']);
	$arra_options_inst[0]="<- ".CONST_OPTION_SELECT." ->";
	foreach ($result as $rows)
	{
		$esta='';
		if(!$rows['esta_inst'])
			$esta=' (Inactivo)';
		$arra_options_inst[$rows['codi_inst']]=$rows['nomb_inst'].$esta;
	}
*/

$result=$Db->select('mp_admi_depe','','','',['codi_depe'=>'ASC']);
	foreach ($result as $rows)
	{
		$arra_depe[$rows['codi_padr']][$rows['codi_depe']]=$rows['nomb_depe'];
	}

	$separator="&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";

	$result_menu=$Db->select('mp_admi_menu','','','',['orde_menu'=>'ASC']);
	$arra_options_padr[0]="<- ".CONST_OPTION_SELECT." ->";
	$arra_options_depe[0]="<- ".CONST_OPTION_SELECT." ->";
	$arra_options_depe[9999]="<- ".CONST_OPTION_NEW." ->";
    if(isset($arra_depe[0])) {
    	foreach($arra_depe[0] as $codi => $nomb)
    	{
    		$arra_options_depe[$codi]=$nomb;
    		$arra_options_padr[$codi]="$separator".$nomb;
            if(isset($arra_depe[$codi])) {
        		foreach($arra_depe[$codi] as $codi => $nomb)
        		{
        			$arra_options_depe[$codi]="$separator".$nomb;
        			$arra_options_padr[$codi]="$separator$separator".$nomb;
                    if(isset($arra_depe[$codi])) {
            			foreach($arra_depe[$codi] as $codi => $nomb)
            			{
            				$arra_options_depe[$codi]="$separator$separator".$nomb;
            				$arra_options_padr[$codi]="$separator$separator$separator".$nomb;
            			}
                    }
        		}
            }
    	}
    }
	
	//OTRA OPCION PARA MOSTRAR DEPENDENCIAS
	$arra_options_depe2[0]="<- Seleccione ->";
    /*
    $result=$Db->query("select * from mp_admi_depe");
    foreach($result as $rows)
        $arra_options_depe2[$rows['codi_depe']]=utf8_encode(utf8_decode($rows['nomb_depe']));
    */
    $result1=$Db->query("select * from mp_admi_depe where codi_padr=0 AND esta_depe=1 order by nomb_depe");
	$separador="|&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
	foreach($result1 as $rows1)
	{   
	    if(strlen($rows1['abre_depe'])>70)  $rows1['abre_depe']=substr($rows1['abre_depe'],0,70).'...'; 
	    $arra_options_depe2[$rows1['codi_depe']]=$rows1['abre_depe'];
		$result2=$Db->query("select * from mp_admi_depe where codi_padr='".$rows1['codi_depe']."' AND esta_depe=1 order by nomb_depe");
		foreach($result2 as $rows2)
		{
		    if(strlen($rows2['abre_depe'])>70)  $rows2['abre_depe']=substr($rows2['abre_depe'],0,70).'...';
		    $arra_options_depe2[$rows2['codi_depe']]=$separador.$rows2['abre_depe'];
		    $result3=$Db->query("select * from mp_admi_depe where codi_padr='".$rows2['codi_depe']."' AND esta_depe=1 order by nomb_depe");
	    	foreach($result3 as $rows3)
    		{
		        if(strlen($rows3['abre_depe'])>70)  $rows3['abre_depe']=substr($rows3['abre_depe'],0,70).'...';
		        $arra_options_depe2[$rows3['codi_depe']]=$separador.$separador.$rows3['abre_depe'];
		        $result4=$Db->query("select * from mp_admi_depe where codi_padr='".$rows3['codi_depe']."' AND esta_depe=1 order by nomb_depe");
	    	    foreach($result4 as $rows4)
    		    {
		            if(strlen($rows4['abre_depe'])>70)  $rows4['abre_depe']=substr($rows4['abre_depe'],0,70).'...';
		            $arra_options_depe2[$rows4['codi_depe']]=$separador.$separador.$separador.$rows4['abre_depe'];
    		        $result5=$Db->query("select * from mp_admi_depe where codi_padr='".$rows4['codi_depe']."' AND esta_depe=1 order by nomb_depe");
    	    	    foreach($result5 as $rows5)
        		    {
		                if(strlen($rows5['abre_depe'])>70)  $rows5['abre_depe']=substr($rows5['abre_depe'],0,70).'...';
		                $arra_options_depe2[$rows5['codi_depe']]=$separador.$separador.$separador.$separador.$rows5['abre_depe'];
    		            $result6=$Db->query("select * from mp_admi_depe where codi_padr='".$rows5['codi_depe']."' AND esta_depe=1 order by nomb_depe");
        	    	    foreach($result6 as $rows6)
            		    {
    		                if(strlen($rows6['abre_depe'])>70)  $rows6['abre_depe']=substr($rows6['abre_depe'],0,70).'...';
    		                $arra_options_depe2[$rows6['codi_depe']]=$separador.$separador.$separador.$separador.$separador.$rows6['abre_depe'];
        		            //echo"<tr>$separador$separador$separador$separador$separador<td width=1%><input type=checkbox name=\"chec_depe_".$rows6['codi_depe']."\" ".$arra_depe[$rows6['codi_depe']]."></td><td width=100% colspan=$colu style=\"font-size:small\">".$rows6['abre_depe']."</td></tr>";
            		    }
    		            
    		            
    		            
	    	        }
	    	    }
		    }
		}
	}


	echo $html->put_select(CONST_SUBTITLE_USER,'iden_oper',$arra_options_oper,$_POST['iden_oper'],"onchange=\"document.form.submit()\" required");
	if(!empty($_POST['iden_oper']))
	{
		//-------------------------------
		echo $html->put_title(CONST_SUBTITLE_BASIC_INFORMATION);
		echo $html->put_text('text',CONST_SUBTITLE_LOGIN,CONST_PLACEHOLDER_LOGIN,'logi_oper',$_POST['logi_oper'],'','20','required pattern="[A-Za-z0-9]+" title="Solo letras y números"');
		echo $html->put_text('text',CONST_SUBTITLE_APPA,CONST_PLACEHOLDER_APPA,'appa_oper',$_POST['appa_oper'],'','50','required title="Solo letras"');
		echo $html->put_text('text',CONST_SUBTITLE_APMA,CONST_PLACEHOLDER_APMA,'apma_oper',$_POST['apma_oper'],'','50','required title="Solo letras"');
		echo $html->put_text('text',CONST_SUBTITLE_NAME,CONST_PLACEHOLDER_NAME,'nomb_oper',$_POST['nomb_oper'],'','50','required title="Solo letras"');
		echo $html->put_text('text',CONST_SUBTITLE_CARG,CONST_PLACEHOLDER_CARG,'carg_oper',$_POST['carg_oper'],'','50','title="Solo letras"');
		echo $html->put_text('text',CONST_SUBTITLE_CELU,CONST_PLACEHOLDER_CELU,'celu_oper',$_POST['celu_oper'],'','20','pattern="[0-9]+" title="Solo Números"');
		echo $html->put_text('email',CONST_SUBTITLE_EMAIL,CONST_PLACEHOLDER_EMAIL,'mail_oper',$_POST['mail_oper'],'','40','required pattern="[^@\s]+@[^@\s]+\.[^@\s]+" title="Invalid Email Address"');
		//echo $html->put_select(CONST_SUBTITLE_DEPENDENCIA,'codi_depe',$arra_options_padr,$_POST['codi_depe'],"");
		echo $html->put_select(CONST_SUBTITLE_DEPENDENCIA,'codi_depe',$arra_options_depe2,$_POST['codi_depe'],"");
		echo $html->put_select(CONST_SUBTITLE_PERFIL,'codi_perf',$arra_options_perfil,$_POST['codi_perf'],"");
		echo $html->put_select_estado(CONST_SUBTITLE_STATE,'esta_oper',$_POST['esta_oper'],CONST_OPTION_ENABLE,CONST_OPTION_DISABLE);
		//-------------------------------
		echo $html->put_title(CONST_SUBTITLE_SECURITY_INFORMATION);
		echo $html->put_text('password',CONST_SUBTITLE_PASS_NEW,CONST_PLACEHOLDER_PASS_NEW,'pass_ope1','','6','20','pattern="[A-Za-z0-9]+" title="Solo letras y números"');
		echo $html->put_text('password',CONST_SUBTITLE_PAS2_NEW,CONST_PLACEHOLDER_PAS2_NEW,'pass_ope2','','6','20','pattern="[A-Za-z0-9]+" title="Solo letras y números"');
		echo $html->put_text('date',CONST_SUBTITLE_EXPIRE,'','fexp_oper',$_POST['fexp_oper'],'','','required');
		echo $html->put_image(CONST_SUBTITLE_FIRMA_ACTUAL,'data:image/jpeg;base64,'.base64_encode((string)$_POST['firm_oper']),'');
		echo $html->put_upload_file(CONST_SUBTITLE_SUBIR_FIRMA,'nuev_firm','','');
		//-------------------------------
		echo $html->put_title(CONST_SUBTITLE_SYSTEM_ACCESS);
		echo $html->put_others(CONST_SUBTITLE_ROL,"$cont_role");
		//-------------------------------
		echo $html->put_title("Acceso de Bandeja Personal");
		echo $html->put_checkbox("&nbsp;",'flag_band',$_POST['flag_band'],'',' Ver Bandeja Personal');
		echo"<BR><BR>";
		echo $html->put_submit(CONST_BUTTON_SAVE,'check()');
	}
	//echo"$_POST[firm_oper]";
	//echo '<img src="data:image/jpeg;base64,'.base64_encode($_POST[firm_oper]) .'" />';
?>
		</form>
	</body>
</html>
