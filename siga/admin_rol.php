<?php
	//require_once 'include/languages/english.php';
	require_once 'include/cabecera.php';
	require_once 'classes/Html.class.php';
	require_once 'classes/Db.class.php';
	$Db = new Db();
	
	if(!empty($_POST['saveinfo']))	//guardar
	{
		$fdig=date("YmdHis");
		if($_POST['iden_role']==9999)	//insert
		{
			$result=$Db->insert('mp_admi_role',['nomb_role'=>$_POST['nomb_role'],'codi_inst'=>$_POST['codi_inst'],'esta_role'=>$_POST['esta_role']]);
			$_POST['iden_role']=$Db->lastInsertId();
		}
		else	//update
		{
			$result=$Db->update('mp_admi_role',['nomb_role'=>$_POST['nomb_role'],'codi_inst'=>$_POST['codi_inst'],'esta_role'=>$_POST['esta_role']],['iden_role'=>$_POST['iden_role']]);
		}
		
		//seccion de permisos a menus
		$result=$Db->delete('mp_admi_role_subm',['iden_role'=>$_POST['iden_role']]);	//limpia permisos
		$result=$Db->select('mp_admi_subm',['esta_subm'=>1]);	//recorre submenus
		foreach($result as $rows)
		{
			if(isset($_POST['chek_subm_'.$rows['iden_subm']]) && $_POST['chek_subm_'.$rows['iden_subm']])
			{
				$result=$Db->insert('mp_admi_role_subm',['iden_role'=>$_POST['iden_role'],'iden_subm'=>$rows['iden_subm'],'esta_perm'=>1,'digi_perm'=>$_SESSION['iden_oper'],'fdig_perm'=>$fdig]);
			}
		}
		
		//barre todos los permisos para verificar que todos los padres esten
		function verif_father($iden_role,$iden_subm,$arra_role_subm,$Db)
		{
			$result=$Db->select(['mp_admi_subm',['iden_padr']],['iden_subm'=>$iden_subm]);
			if(!in_array($result[0]['iden_padr'],$arra_role_subm) AND $result[0]['iden_padr']>0)
			{
				$arra_role_subm[$result[0]['iden_padr']]=$result[0]['iden_padr'];
				$result=$Db->insert('mp_admi_role_subm',['iden_role'=>$iden_role,'iden_subm'=>$result[0]['iden_padr'],'esta_perm'=>'0','digi_perm'=>$_SESSION['iden_oper']]);
				$arra_role_subm=verif_father($iden_role,$result[0]['iden_padr'],$arra_role_subm,$Db);
			}
			return $arra_role_subm;
		}
		$result=$Db->select(['mp_admi_role_subm',['iden_subm']],['iden_role'=>$_POST['iden_role']]);
		foreach($result as $rows)
			$arra_role_subm[$rows['iden_subm']]=$rows['iden_subm'];
		$result=$Db->select(['mp_admi_role_subm',['iden_subm']],['iden_role'=>$_POST['iden_role']]);
		foreach($result as $rows)
		{
			$arra_role_subm=verif_father($_POST['iden_role'],$rows['iden_subm'],$arra_role_subm,$Db);
		}
		
		unset($_POST);
	}
	if(!empty($_POST['iden_role']))
	{
		//$result=$Db->select(['mp_admi_role',['nomb_role','esta_role']],['iden_role'=>$_POST[iden_role]]);
		$result=$Db->select('mp_admi_role',['iden_role'=>$_POST['iden_role']]);
		$_POST['nomb_role']=$result[0]['nomb_role'];
		$_POST['codi_inst']=$result[0]['codi_inst'];
		$_POST['esta_role']=$result[0]['esta_role'];
	}
?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8" />
		<title>SIOJAlimentos</title>
		<link rel="stylesheet" href="css/forms.css" />
		<script>
			function sele_todo(subm)
			{
				//if(eval('document.form.chek_menu_'+menu+'_'+subm+'.checked'))
				if(document.getElementById(subm).checked)
				{
					for(x=1;x<document.form.length;x++)
					{
						if(document.form.elements[x].id.indexOf(subm+'_')>=0)
							document.form.elements[x].checked=true;
					}
				}
				else
				{
					for(x=1;x<document.form.length;x++)
						if(document.form.elements[x].id.indexOf(subm+'_')>=0)
							document.form.elements[x].checked=false;
				}
			}
			function check()
			{
				document.form.saveinfo.value=1;
				return true;
			}
		</script>
	</head>
	<body>
		<form name="form" method="post">
			<input type=hidden name="saveinfo">
		<center>
<h2><?=CONST_TITLE_ROLES?></h2><br>
		</center>
<?
	//$result=$Db->select(['mp_admi_role',['n_codi_ginstruccion','x_ginstruccion']],['n_estado'=>1]);
	$result=$Db->select('mp_admi_role','','','',['nomb_role'=>'ASC']);
	$arra_options_rol[0]="<- ".CONST_OPTION_SELECT." ->";
	$arra_options_rol[9999]="<- ".CONST_OPTION_NEW." ->";
	foreach ($result as $rows)
		$arra_options_rol[$rows['iden_role']]=$rows['nomb_role'];

	$result=$Db->select('mp_depe_institucion','','','',['nomb_inst'=>'ASC']);
	$arra_options_inst[0]="<- ".CONST_OPTION_SELECT." ->";
	if($result)
	{
		foreach ($result as $rows)
			$arra_options_inst[$rows['codi_inst']]=$rows['nomb_inst'].(isset($esta) ? $esta : '');
	}

	$html=new htmlclass;
	echo $html->put_select(CONST_SUBTITLE_ROL,'iden_role',$arra_options_rol,$_POST['iden_role'],"onchange=\"document.form.submit()\" required");
	if(!empty($_POST['iden_role']))
	{
		echo $html->put_title(CONST_SUBTITLE_BASIC_INFORMATION);
		echo $html->put_text('text',CONST_SUBTITLE_NAME,CONST_PLACEHOLDER_NAME,'nomb_role',$_POST['nomb_role'],'2','30','required');
		echo $html->put_select(CONST_SUBTITLE_INSTITUCION,'codi_inst',$arra_options_inst,$_POST['codi_inst'],"");
		echo $html->put_select_estado(CONST_SUBTITLE_STATE,'esta_role',$_POST['esta_role'],CONST_OPTION_ENABLE,CONST_OPTION_DISABLE);
		echo $html->put_title(CONST_SUBTITLE_MENU_ACCESS);

		//ARBOL DE MENUS
		//echo"<div><table width=100% border=0 cellspacing=2 cellpadding=2>";
echo"<div style=\"margin-left: 30px; margin-top: 2px; margin-bottom: 2px;\">";

		//recuperamos los permisos
		$result=$Db->select(['mp_admi_role_subm',['iden_subm']],['iden_role'=>$_POST['iden_role'],'esta_perm'=>'1']);
		foreach ($result as $rows)
			$arra_perm[$rows['iden_subm']]='checked';
		////////////////////////
			
		$result=$Db->select('mp_admi_subm','','','',['orde_subm'=>'ASC']);
		foreach ($result as $rows)
			$arra_subm[$rows['iden_menu']][$rows['iden_padr']][$rows['iden_subm']]=$rows['nomb_subm'];
		$separator="&nbsp;&nbsp;";
		$result_menu=$Db->select('mp_admi_menu','','','',['orde_menu'=>'ASC']);
		foreach ($result_menu as $rows_menu)
		{
			$menu1='chek_'.str_pad($rows_menu['iden_menu'],4,'0',STR_PAD_LEFT);
echo"<header><table border=0><tr><td width=1%><input type=checkbox id=\"$menu1\" onclick=\"sele_todo('$menu1')\" name=\"chek_menu_".$rows_menu['iden_menu']."\"></td><td><b>".(defined($rows_menu['nomb_menu']) ? constant($rows_menu['nomb_menu']) : $rows_menu['nomb_menu'])."</td></tr></table></header>";
			if(isset($arra_subm[$rows_menu['iden_menu']][0]))
			{
				foreach($arra_subm[$rows_menu['iden_menu']][0] as $codi => $nomb)
				{
echo"<main>";
					$menu2=$menu1.'_'.str_pad($codi,4,'0',STR_PAD_LEFT);
					$perm1 = isset($arra_perm[$codi]) ? $arra_perm[$codi] : '';
echo"<section style=\"margin-left: 30px; margin-top: 4px; margin-bottom: 4px;\"><input type=checkbox id=\"$menu2\" onclick=\"sele_todo('$menu2')\" name=\"chek_subm_$codi\" $perm1>&nbsp;".(defined($nomb) ? constant($nomb) : $nomb)."";
					if(isset($arra_subm[$rows_menu['iden_menu']][$codi]))
					{
						foreach($arra_subm[$rows_menu['iden_menu']][$codi] as $codi => $nomb)
						{
							$menu3=$menu2.'_'.str_pad($codi,4,'0',STR_PAD_LEFT);
							$perm2 = isset($arra_perm[$codi]) ? $arra_perm[$codi] : '';
echo"<section style=\"margin-left: 30px; margin-top: 4px; margin-bottom: 4px;\"><input type=checkbox id=\"$menu3\" onclick=\"sele_todo('$menu3')\" name=\"chek_subm_$codi\" $perm2>&nbsp;".(defined($nomb) ? constant($nomb) : $nomb)."";
							if(isset($arra_subm[$rows_menu['iden_menu']][$codi]))
							{
								foreach($arra_subm[$rows_menu['iden_menu']][$codi] as $codi => $nomb)
								{
									$menu4=$menu3.'_'.str_pad($codi,4,'0',STR_PAD_LEFT);
									$perm3 = isset($arra_perm[$codi]) ? $arra_perm[$codi] : '';
echo"<article style=\"margin-left: 30px; margin-top: 4px; margin-bottom: 4px;\"><input type=checkbox id=\"$menu4\" onclick=\"sele_todo('$menu4')\" name=\"chek_subm_$codi\" $perm3>&nbsp;".(defined($nomb) ? constant($nomb) : $nomb)."</article>";
								}
							}
							echo"</section>";
						}
					}
					echo"</section>";
echo"</main>";
				}
			}
		}
		echo"</div>";

		//ARBOL DE MENUS
		/*
		echo"<div><table width=100% border=0 cellspacing=2 cellpadding=2>";
		$result=$Db->select('mp_admi_subm','','','',['orde_subm'=>'ASC']);
		foreach ($result as $rows)
			$arra_subm[$rows['iden_menu']][$rows['iden_padr']][$rows['iden_subm']]=$rows['nomb_subm'];
		$separator="&nbsp;&nbsp;";
		$result_menu=$Db->select('mp_admi_menu','','','',['orde_menu'=>'ASC']);
		foreach ($result_menu as $rows_menu)
		{
			$menu1=str_pad($rows_menu['iden_menu'],4,'0',STR_PAD_LEFT);
echo"<tr><td width=1px><input type=checkbox id=\"$menu1\" name=\"$rows_menu[iden_menu]\"></td><td colspan=9 width=100%>".constant($rows_menu[nomb_menu])."</td></tr>";
			foreach($arra_subm[$rows_menu['iden_menu']][0] as $codi => $nomb)
			{
				$menu2=$menu1.'_'.str_pad($codi,4,'0',STR_PAD_LEFT);
echo"<tr><td width=1%></td><td width=1%>$separator</td><td width=1%><input type=checkbox id=\"$menu2\" name=\"$codi\"></td><td colspan=7 width=100%>".constant($nomb)."</td></tr>";
				foreach($arra_subm[$rows_menu['iden_menu']][$codi] as $codi => $nomb)
				{
					$menu3=$menu2.'_'.str_pad($codi,4,'0',STR_PAD_LEFT);
echo"<tr><td width=1% colspan=3><td width=1%>$separator</td></td><td width=1%><input type=checkbox id=\"$menu3\" name=\"$codi\"></td><td colspan=6 width=100%>".constant($nomb)."</td></tr>";
					foreach($arra_subm[$rows_menu['iden_menu']][$codi] as $codi => $nomb)
					{
						$menu4=$menu3.'_'.str_pad($codi,4,'0',STR_PAD_LEFT);
echo"<tr><td width=1% colspan=5><td width=1%>$separator</td></td><td width=1%><input type=checkbox id=\"$menu4\" name=\"$codi\"></td><td colspan=5 width=100%>".constant($nomb)."</td></tr>";
					}
				}
			}
		}
		echo"</table></div>";
		*/





		//echo $html->put_number('Prueba','Poner Prueba','NOmbre','','10','');
		echo"<BR><BR>";
		//echo $html->put_button(CONST_BUTTON_RESET);
		echo $html->put_submit(CONST_BUTTON_SAVE,'check()');
	}
?>
		</form>
	</body>
</html>
