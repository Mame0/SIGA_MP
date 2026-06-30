<?
	require_once 'include/cabecera.php';
	require_once 'classes/Html.class.php';
	require_once 'classes/Db.class.php';
	$Db = new Db();
	
	if($_POST['iden_oper']=='9999' AND empty($_POST['saveinfo']))
	{
		unset($_POST['logi_oper'],$_POST['ndoc_oper'],$_POST['appa_oper'],$_POST['apma_oper'],$_POST['nomb_oper'],$_POST['carg_oper'],$_POST['celu_oper'],$_POST['mail_oper'],$_POST['codi_perf'],$_POST['esta_oper'],$_POST['pass_ope1'],$_POST['pass_ope2'],$_POST['fexp_oper'],$_POST['nuev_firm']);
		$_POST['fexp_oper']='2030-12-31';
	}

	if(!empty($_POST['saveinfo']))	//guardar
	{
		$resultr=$Db->select('mp_admi_oper',['esta_role'=>1],'','',['nomb_role'=>'ASC']);
		$result=$Db->query("select * from mp_admi_oper where logi_oper=:m_logi AND iden_oper<>:m_oper",[':m_logi'=>$_POST['logi_oper'],':m_oper'=>$_POST['iden_oper']]);
		if(sizeof($result)>0)
			echo"<script>alert('ERROR: Login de Usuario ya existe')</script>";
		else
		{
			$fdig=date("YmdHis");
		
			if($_POST['pass_ope1'] AND $_POST['pass_ope1']==$_POST['pass_ope2'])
				$pass_oper=md5($_POST['pass_ope1']);
		
			$fexp_oper=substr($_POST['fexp_oper'],0,4).substr($_POST['fexp_oper'],5,2).substr($_POST['fexp_oper'],8,2);

			if($_POST['rese_oper'])	$_POST['rese_oper']=1;
			if($_POST['flag_band']) $_POST['flag_band']=1;

			if($_POST['iden_oper']==9999)	//nuevo
			{
				$result=$Db->insert('mp_admi_oper',['logi_oper'=>$_POST['logi_oper'],'pass_oper'=>"$pass_oper",'ndoc_oper'=>$_POST['ndoc_oper'],'appa_oper'=>$_POST['appa_oper'],'apma_oper'=>$_POST['apma_oper'],'nomb_oper'=>$_POST['nomb_oper'],'carg_oper'=>$_POST['carg_oper'],'celu_oper'=>$_POST['celu_oper'],'mail_oper'=>$_POST['mail_oper'],'codi_depe'=>$_POST['codi_depe'],'codi_perf'=>$_POST['codi_perf'],'flag_band'=>$_POST['flag_band'],'esta_oper'=>$_POST['esta_oper'],'fexp_oper'=>"$fexp_oper",'digi_oper'=>$_SESSION['iden_oper'],'fdig_oper'=>$fdig,'rese_oper'=>$_POST['rese_oper']]);
				$_POST['iden_oper']=$Db->lastInsertId();
			}
			else	//update
			{
				if($pass_oper)
					$result=$Db->update('mp_admi_oper',['logi_oper'=>$_POST['logi_oper'],'pass_oper'=>"$pass_oper",'ndoc_oper'=>$_POST['ndoc_oper'],'appa_oper'=>$_POST['appa_oper'],'apma_oper'=>$_POST['apma_oper'],'nomb_oper'=>$_POST['nomb_oper'],'carg_oper'=>$_POST['carg_oper'],'celu_oper'=>$_POST['celu_oper'],'mail_oper'=>$_POST['mail_oper'],'codi_depe'=>$_POST['codi_depe'],'codi_perf'=>$_POST['codi_perf'],'flag_band'=>$_POST['flag_band'],'esta_oper'=>$_POST['esta_oper'],'fexp_oper'=>"$fexp_oper",'digi_oper'=>$_SESSION['iden_oper'],'fdig_oper'=>$fdig,'rese_oper'=>$_POST['rese_oper']],['iden_oper'=>$_POST['iden_oper']]);
				else
					$result=$Db->update('mp_admi_oper',['logi_oper'=>$_POST['logi_oper'],'ndoc_oper'=>$_POST['ndoc_oper'],'appa_oper'=>$_POST['appa_oper'],'apma_oper'=>$_POST['apma_oper'],'nomb_oper'=>$_POST['nomb_oper'],'carg_oper'=>$_POST['carg_oper'],'celu_oper'=>$_POST['celu_oper'],'mail_oper'=>$_POST['mail_oper'],'codi_depe'=>$_POST['codi_depe'],'codi_perf'=>$_POST['codi_perf'],'flag_band'=>$_POST['flag_band'],'esta_oper'=>$_POST['esta_oper'],'fexp_oper'=>"$fexp_oper",'digi_oper'=>$_SESSION['iden_oper'],'fdig_oper'=>$fdig,'rese_oper'=>$_POST['rese_oper']],['iden_oper'=>$_POST['iden_oper']]);
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
	}
	if(!empty($_POST['iden_oper']) AND $_POST['iden_oper']!='9999')
	{
		$result=$Db->select('mp_admi_oper',['iden_oper'=>$_POST[iden_oper]]);
		$_POST['ndoc_oper']=$result[0]['ndoc_oper'];
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
		$_POST['fexp_oper']=substr($result[0]['fexp_oper'],0,4).'-'.substr($result[0]['fexp_oper'],4,2).'-'.substr($result[0]['fexp_oper'],6,2);
		$_POST['rese_oper']=$result[0]['rese_oper'];

		$arra_oper=$Db->selectUserBlob($_POST[iden_oper]);
		$_POST['firm_oper']=$arra_oper['firm_oper'];
	}
?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8" />
		<title>SIOJAlimentos</title>
		<link rel="stylesheet" href="css/forms.css" />
		<SCRIPT language="JavaScript" src="js/cadenas.js"></SCRIPT>
		<SCRIPT language="JavaScript" src="js/denuncia.js"></SCRIPT>
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
			function f_actualizar_cambios(oper)
			{
				if(oper==9999)
				{
					document.form.logi_oper.value=document.form.ndoc_oper.value;
					document.form.pass_ope1.value=document.form.ndoc_oper.value;
					document.form.pass_ope2.value=document.form.ndoc_oper.value;
				}
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


	$result=$Db->select('mp_admi_oper_role',['iden_oper'=>$_POST['iden_oper']]);
	foreach ($result as $rows)
	{
		$arra_oper_role[$rows['iden_role']]='checked';
	}
	$result=$Db->select('mp_admi_role',['codi_inst'=>$_POST['codi_inst'],'esta_role'=>1],'','',['nomb_role'=>'ASC']);
	$cont_role="<table border=0>";
	foreach ($result as $rows)
		$cont_role.="<tr><td width=1%><input type=checkbox id=\"role_$rows[iden_role]\" name=\"role_$rows[iden_role]\" {$arra_oper_role[$rows[iden_role]]}></td><td>$rows[nomb_role]</td></tr>";
	$cont_role.="</table>";

	$html=new htmlclass;

	/*
	if($_SESSION['iden_oper']==1)
		$result=$Db->select('mp_depe_institucion','','','',['nomb_inst'=>'ASC']);
	else
	{
		$cresult_dependencia=$Db->query("select d.codi_depe,a.codi_inst,a.sigl_inst,d.sigl_depe,d.nomb_depe from mp_depe_institucion as a, mp_depe_distrito as b, mp_depe_sede as c, mp_depe_dependencia as d where d.codi_sede=c.codi_sede AND c.codi_dist=b.codi_dist AND b.codi_inst=a.codi_inst AND d.codi_depe=:m_depe",[':m_depe'=>$_SESSION['codi_depe']]);
		$result=$Db->select('mp_depe_institucion',['codi_inst'=>$cresult_dependencia[0]['codi_inst']],'','',['nomb_inst'=>'ASC']);
	}
	$arra_options_inst[0]="<- ".CONST_OPTION_SELECT." ->";
	foreach ($result as $rows)
	{
		$esta='';
		if(!$rows['esta_inst'])
			$esta=' (Inactivo)';
		$arra_options_inst[$rows['codi_inst']]=$rows['nomb_inst'].$esta;
	}
	echo $html->put_select(CONST_SUBTITLE_INSTITUCION,'codi_inst',$arra_options_inst,$_POST['codi_inst'],"onchange=\"document.form.submit()\" required");
	if(!empty($_POST['codi_inst']))
	{
		$result=$Db->select('mp_depe_distrito',['codi_inst'=>$_POST[codi_inst]],'','',['nomb_dist'=>'ASC']);
		$arra_options_dist[0]="<- ".CONST_OPTION_SELECT." ->";
		$cant=0;
                foreach ($result as $rows)
                {
                        $esta='';
			$cant++;
                        if(!$rows['esta_dist'])
                                $esta=' (Inactivo)';
                        $arra_options_dist[$rows['codi_dist']]=$rows['nomb_dist'].$esta;
			$flag_sele=$rows['codi_dist'];
                }
		if($cant==1 AND empty($_POST['codi_dist']))
			$_POST['codi_dist']=$flag_sele;
                echo $html->put_select(CONST_SUBTITLE_DISTRITO,'codi_dist',$arra_options_dist,$_POST['codi_dist'],"onchange=\"document.form.submit()\" required");
                if(!empty($_POST['codi_dist']))
                {
                        $result=$Db->select('mp_depe_sede',['codi_dist'=>$_POST[codi_dist]],'','',['nomb_sede'=>'ASC']);
                        $arra_options_sede[0]="<- ".CONST_OPTION_SELECT." ->";
			$cant=0;
                        foreach ($result as $rows)
                        {
                                $esta='';
				$cant++;
                                if(!$rows['esta_sede'])
                                        $esta=' (Inactivo)';
                                $arra_options_sede[$rows['codi_sede']]=$rows['nomb_sede'].$esta;
				$flag_sele=$rows['codi_sede'];
                        }
			if($cant==1 AND empty($_POST['codi_sede']))
				$_POST['codi_sede']=$flag_sele;
                        echo $html->put_select(CONST_SUBTITLE_SEDE,'codi_sede',$arra_options_sede,$_POST['codi_sede'],"onchange=\"document.form.submit()\" required");
                        if(!empty($_POST['codi_sede']))
                        {
                                $result=$Db->select('mp_depe_dependencia',['codi_sede'=>$_POST[codi_sede]],'','',['nomb_depe'=>'ASC']);
                                $arra_options_depe[0]="<- ".CONST_OPTION_SELECT." ->";
				$cant=0;
                                foreach ($result as $rows)
                                {
                                        $esta='';
					$cant++;
                                        if(!$rows['esta_depe'])
                                                $esta=' (Inactivo)';
                                        $arra_options_depe[$rows['codi_depe']]=$rows['nomb_depe'].$esta;
					$flag_sele=$rows['codi_depe'];
                                }
				if($cant==1 AND empty($_POST['codi_depe']))
					$_POST['codi_depe']=$flag_sele;
                                echo $html->put_select(CONST_SUBTITLE_DEPENDENCIA,'codi_depe',$arra_options_depe,$_POST['codi_depe'],"onchange=\"document.form.submit()\" required");
	*/
	
	// CORRECCION: Usar tabla mp_admi_depe directamente ya que las otras tablas no existen
	$arra_options_depe[0]="<- ".CONST_OPTION_SELECT." ->";
	$result=$Db->select('mp_admi_depe',['esta_depe'=>1],'','',['nomb_depe'=>'ASC']);
	foreach ($result as $rows)
	{
		$arra_options_depe[$rows['codi_depe']]=$rows['nomb_depe'];
	}
	echo $html->put_select(CONST_SUBTITLE_DEPENDENCIA,'codi_depe',$arra_options_depe,$_POST['codi_depe'],"onchange=\"document.form.submit()\" required");

                                if(!empty($_POST['codi_depe']))
                                {
					$result=$Db->select('mp_admi_oper',['codi_depe'=>$_POST['codi_depe']],'','',['appa_oper'=>'ASC']);
					$arra_options_oper[0]="<- Seleccione Operador ->";
					$arra_options_oper[9999]="<- Crear Nuevo Operador ->";
					foreach ($result as $rows)
						$arra_options_oper[$rows['iden_oper']]=$rows['appa_oper']." ".$rows['apma_oper']." ".$rows['nomb_oper'];

					echo $html->put_select(CONST_SUBTITLE_USER,'iden_oper',$arra_options_oper,$_POST['iden_oper'],"onchange=\"document.form.submit()\" required");
					if(!empty($_POST['iden_oper']))
					{
						//--------------
						//$arra_options_dependencia[0]="<- Seleccione Dependencia ->";
						//$cresult_dependencia=$Db->query("select d.codi_depe,a.codi_inst,a.sigl_inst,d.sigl_depe,d.nomb_depe from mp_depe_institucion as a, mp_depe_distrito as b, mp_depe_sede as c, mp_depe_dependencia as d where d.codi_sede=c.codi_sede AND c.codi_dist=b.codi_dist AND b.codi_inst=a.codi_inst");
						//foreach($cresult_dependencia as $rows)
						//	$arra_options_dependencia[$rows['codi_depe']]=$rows['sigl_inst']." - ".$rows['sigl_depe']." (".$rows['nomb_depe'].")";

						$arra_options_perfil[0]="<- Seleccione Perfil ->";
						//$cresult_perfil=$Db->query("select a.codi_inst,a.sigl_inst,b.codi_perf,b.nomb_perf from mp_depe_institucion as a, mp_depe_perfil as b where b.codi_inst=a.codi_inst order by a.sigl_inst,b.nomb_perf");
						$cresult_perfil=$Db->query("select a.codi_inst,a.sigl_inst,b.codi_perf,b.nomb_perf from mp_depe_institucion as a, mp_depe_perfil as b where b.codi_inst=a.codi_inst AND a.codi_inst=:m_inst order by a.sigl_inst,b.nomb_perf",['m_inst'=>$_POST['codi_inst']]);
						foreach($cresult_perfil as $rows)
							$arra_options_perfil[$rows['codi_perf']]=$rows['sigl_inst']." - ".$rows['nomb_perf'];

						//-------------------------------
						echo $html->put_title(CONST_SUBTITLE_BASIC_INFORMATION);
						echo $html->put_text('text',CONST_SUBTITLE_DNI,CONST_PLACEHOLDER_DNI,'ndoc_oper',$_POST['ndoc_oper'],'8','8','required pattern="[0-9]+" title="Solo Numeros" onchange="f_actualizar_cambios(\''.$_POST['iden_oper'].'\');" onblur="f_WebService_user()"');
						echo $html->put_text('text',CONST_SUBTITLE_LOGIN,CONST_PLACEHOLDER_LOGIN,'logi_oper',$_POST['logi_oper'],'','20','required pattern="[A-Za-z0-9]+" title="Solo letras y números"');
						echo $html->put_text('text',CONST_SUBTITLE_APPA,CONST_PLACEHOLDER_APPA,'appa_oper',$_POST['appa_oper'],'','50','required pattern="[A-Za-z ]+" title="Solo letras"');
						echo $html->put_text('text',CONST_SUBTITLE_APMA,CONST_PLACEHOLDER_APMA,'apma_oper',$_POST['apma_oper'],'','50','required pattern="[A-Za-z ]+" title="Solo letras"');
						echo $html->put_text('text',CONST_SUBTITLE_NAME,CONST_PLACEHOLDER_NAME,'nomb_oper',$_POST['nomb_oper'],'','50','required pattern="[A-Za-z ]+" title="Solo letras"');
						echo $html->put_text('text',CONST_SUBTITLE_CARG,CONST_PLACEHOLDER_CARG,'carg_oper',$_POST['carg_oper'],'','50','pattern="[A-Za-z ]+" title="Solo letras"');
						echo $html->put_text('text',CONST_SUBTITLE_CELU,CONST_PLACEHOLDER_CELU,'celu_oper',$_POST['celu_oper'],'','20','pattern="[0-9]+" title="Solo Números"');
						echo $html->put_text('email',CONST_SUBTITLE_EMAIL,CONST_PLACEHOLDER_EMAIL,'mail_oper',$_POST['mail_oper'],'','40','pattern="[^@\s]+@[^@\s]+\.[^@\s]+" title="Invalid Email Address"');
						//echo $html->put_select(CONST_SUBTITLE_DEPENDENCIA,'codi_depe',$arra_options_dependencia,$_POST['codi_depe'],"");
						echo $html->put_select(CONST_SUBTITLE_PERFIL,'codi_perf',$arra_options_perfil,$_POST['codi_perf'],"");
						echo $html->put_select_estado(CONST_SUBTITLE_STATE,'esta_oper',$_POST['esta_oper'],CONST_OPTION_ENABLE,CONST_OPTION_DISABLE);
						//-------------------------------
						echo $html->put_title(CONST_SUBTITLE_SECURITY_INFORMATION);
						echo $html->put_text('password',CONST_SUBTITLE_PASS_NEW,CONST_PLACEHOLDER_PASS_NEW,'pass_ope1','','6','20','pattern="[A-Za-z0-9]+" title="Solo letras y números"');
						echo $html->put_text('password',CONST_SUBTITLE_PAS2_NEW,CONST_PLACEHOLDER_PAS2_NEW,'pass_ope2','','6','20','pattern="[A-Za-z0-9]+" title="Solo letras y números"');
						echo $html->put_text('date',CONST_SUBTITLE_EXPIRE,'','fexp_oper',$_POST['fexp_oper'],'','','required');
						echo $html->put_image(CONST_SUBTITLE_FIRMA_ACTUAL,'data:image/jpeg;base64,'.base64_encode($_POST[firm_oper]),'');
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
				}
			}
		}
	}
	//echo"$_POST[firm_oper]";
	//echo '<img src="data:image/jpeg;base64,'.base64_encode($_POST[firm_oper]) .'" />';
?>
		</form>
	</body>
</html>
