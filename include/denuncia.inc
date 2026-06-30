<?
function put_denuncia($codi_denu)
{
	$Db = new Db();
	if($codi_denu)
	{
		$result_demandante=$Db->select('ali_denu_parte',['codi_denu'=>$codi_denu,'tipo_part'=>'1']);
		$result_demandado=$Db->select('ali_denu_parte',['codi_denu'=>$codi_denu,'tipo_part'=>'2']);

		$_POST['codi_ieco_09']=$_POST['codi_ieco_10']=$_POST['codi_ieco_11']='NO';
		$result=$Db->select('ali_denu_parte_ieco',['codi_part'=>$result_demandante[0]['codi_part'],'codi_ieco'=>'9']);
		if($result[0]['cant_ieco']>0)	$_POST['codi_ieco_09']='SI';
		$result=$Db->select('ali_denu_parte_ieco',['codi_part'=>$result_demandante[0]['codi_part'],'codi_ieco'=>'10']);
		if($result[0]['cant_ieco']>0)	$_POST['codi_ieco_10']='SI';
		$result=$Db->select('ali_denu_parte_ieco',['codi_part'=>$result_demandante[0]['codi_part'],'codi_ieco'=>'11']);
		if($result[0]['cant_ieco']>0)	$_POST['codi_ieco_11']='SI';

		$result_ieco_01=$Db->select('ali_denu_parte_ieco',['codi_part'=>$result_demandado[0]['codi_part'],'codi_ieco'=>'1']);
		$result_ieco_02=$Db->select('ali_denu_parte_ieco',['codi_part'=>$result_demandado[0]['codi_part'],'codi_ieco'=>'2']);
		$result_ieco_03=$Db->select('ali_denu_parte_ieco',['codi_part'=>$result_demandado[0]['codi_part'],'codi_ieco'=>'3']);
		$result_ieco_04=$Db->select('ali_denu_parte_ieco',['codi_part'=>$result_demandado[0]['codi_part'],'codi_ieco'=>'4']);
		$result_ieco_05=$Db->select('ali_denu_parte_ieco',['codi_part'=>$result_demandado[0]['codi_part'],'codi_ieco'=>'5']);
		$result_ieco_06=$Db->select('ali_denu_parte_ieco',['codi_part'=>$result_demandado[0]['codi_part'],'codi_ieco'=>'6']);
		$result_ieco_07=$Db->select('ali_denu_parte_ieco',['codi_part'=>$result_demandado[0]['codi_part'],'codi_ieco'=>'7']);
		$result_ieco_08=$Db->select('ali_denu_parte_ieco',['codi_part'=>$result_demandado[0]['codi_part'],'codi_ieco'=>'8']);

		//REPRESENTANTE
		$result_representante=$Db->select('ali_denu_representante',['codi_part'=>$result_demandante[0]['codi_part'],'esta_repr'=>'1']);

		//ALIMENTISTAS
		$result=$Db->select('ali_denu_alimentista',['codi_denu'=>$codi_denu,'esta_alim'=>'1']);
		$cont=0;
                foreach($result as $rows)
                {
                        $cont++;
                        $_POST['codi_alim_'.$cont]=$rows['codi_alim'];
                        $_POST['tdoc_alim_'.$cont]=$rows['tdoc_alim'];
                        $_POST['ndoc_alim_'.$cont]=$rows['ndoc_alim'];
                        $_POST['nomb_alim_'.$cont]=$rows['nomb_alim'];
                        $_POST['appa_alim_'.$cont]=$rows['appa_alim'];
                        $_POST['apma_alim_'.$cont]=$rows['apma_alim'];
                        $_POST['edad_alim_'.$cont]=$rows['edad_alim'];
                        $_POST['rela_alim_'.$cont]=$rows['rela_alim'];
                        $_POST['disc_alim_'.$cont]=$rows['disc_alim'];
                }
                if(!$_POST['cant_alim_acum'])
                        $_POST['cant_alim_acum']=$_POST['cant_alim']=$cont;

		$result_demanda=$Db->select('ali_denu_demanda',['codi_denu'=>$codi_denu]);
	
		$result_nece_01=$Db->select('ali_denu_dema_nece',['codi_denu'=>$codi_denu,'codi_nece'=>'1']);
		$result_nece_02=$Db->select('ali_denu_dema_nece',['codi_denu'=>$codi_denu,'codi_nece'=>'2']);
		$result_nece_03=$Db->select('ali_denu_dema_nece',['codi_denu'=>$codi_denu,'codi_nece'=>'3']);
		$result_nece_04=$Db->select('ali_denu_dema_nece',['codi_denu'=>$codi_denu,'codi_nece'=>'4']);
		$result_nece_05=$Db->select('ali_denu_dema_nece',['codi_denu'=>$codi_denu,'codi_nece'=>'5']);
		$result_nece_06=$Db->select('ali_denu_dema_nece',['codi_denu'=>$codi_denu,'codi_nece'=>'6']);
		$result_nece_07=$Db->select('ali_denu_dema_nece',['codi_denu'=>$codi_denu,'codi_nece'=>'7']);
	}
	echo"
		<main>
	";

	$arra_options_petitorio=$Db->get_options('ali_maes_petitorio');
	$arra_options_sex=$Db->get_options('ali_maes_sexo');
        $arra_options_ginstruccion=$Db->get_options('ali_maes_ginstruccion');
        $arra_options_discapacidad=$Db->get_options('ali_maes_discapacidad');
        $arra_options_rdemandado=$Db->get_options('ali_maes_rdemandado');
        $arra_options_montos=$Db->get_options('ali_maes_montos');
	$arra_options_viveen=$Db->get_options('ali_maes_viveen');

	$html=new htmlclass;
		echo $html->put_title_demand(CONST_TITLE_DEMANDANTE);
		echo"<div id=\"manag2\" class=\"sub_group\" style=\"display:block;column-span:all\"><main>";
			echo $html->put_title_demand(CONST_SUBTITLE_PERSONAL_INFORMATION);
			echo $html->put_info2(CONST_SUBTITLE_DNI,$result_demandante[0]['ndoc_part']);
			echo $html->put_info2(CONST_SUBTITLE_APPA,$result_demandante[0]['appa_part']);
			echo $html->put_info2(CONST_SUBTITLE_APMA,$result_demandante[0]['apma_part']);
			echo $html->put_info2(CONST_SUBTITLE_NOMB,$result_demandante[0]['nomb_part']);
			echo $html->put_info2(CONST_SUBTITLE_CELU,$result_demandante[0]['tele_part']);
			echo $html->put_info2(CONST_SUBTITLE_SEX,$arra_options_sex[$result_demandante[0]['sexo_part']]);
			echo $html->put_info2(CONST_SUBTITLE_GINS,$arra_options_ginstruccion[$result_demandante[0]['gins_part']]);
			echo $html->put_info2(CONST_SUBTITLE_DISC,$arra_options_discapacidad[$result_demandante[0]['disc_part']]);
			echo $html->put_info2(CONST_SUBTITLE_RDEM,$arra_options_rdemandado[$result_demandante[0]['rela_part']]);
			echo $html->put_info2(CONST_SUBTITLE_PCARGO,$result_demandante[0]['pcar_part']);
			echo $html->put_info2(CONST_SUBTITLE_INGR,$arra_options_montos[$result_demandante[0]['ingr_part']]);
			echo $html->put_info2(CONST_SUBTITLE_EGRE,$arra_options_montos[$result_demandante[0]['egre_part']]);

			echo $html->put_title_demand(CONST_SUBTITLE_REAL_ADDRESS);
			echo $html->put_info2(CONST_SUBTITLE_DPTO."-".CONST_SUBTITLE_PROV."-".CONST_SUBTITLE_DIST,$result_demandante[0]['ubig_real']);
			echo $html->put_info2(CONST_SUBTITLE_ADDRESS,$result_demandante[0]['dire_real']);
			echo $html->put_info2(CONST_SUBTITLE_REFERENCE,$result_demandante[0]['refe_real']);

			echo $html->put_title_demand(CONST_SUBTITLE_LAW_ADDRESS);
			echo $html->put_info2(CONST_SUBTITLE_DPTO."-".CONST_SUBTITLE_PROV."-".CONST_SUBTITLE_DIST,$result_demandante[0]['ubig_proc']);
			echo $html->put_info2(CONST_SUBTITLE_ADDRESS,$result_demandante[0]['dire_proc']);
			echo $html->put_info2(CONST_SUBTITLE_REFERENCE,$result_demandante[0]['refe_proc']);

			echo $html->put_title_demand(CONST_SUBTITLE_ECONOMY_INFORMATION);
			echo $html->put_info2(CONST_SUBTITLE_INCOMING,$_POST['codi_ieco_09']);
			echo $html->put_info2(CONST_SUBTITLE_WORK,$_POST['codi_ieco_10']);
			echo $html->put_info2(CONST_SUBTITLE_ANOTHER_INCOMING,$_POST['codi_ieco_11']);

		echo"</main></div>";

		echo $html->put_title_demand(CONST_SUBTITLE_MANAGER_INFORMATION);
		echo"<div id=\"manag2\" class=\"sub_group\" style=\"display:block;column-span:all\"><main>";

			echo $html->put_title_demand(CONST_SUBTITLE_PERSONAL_INFORMATION);
			echo $html->put_info2(CONST_SUBTITLE_DNI,$result_representante[0]['ndoc_repr']);
			echo $html->put_info2(CONST_SUBTITLE_APELLIDOS,$result_representante[0]['appa_repr']." ".$result_representante[0]['apma_repr']);
			echo $html->put_info2(CONST_SUBTITLE_NOMB,$result_representante[0]['nomb_repr']);
			echo $html->put_title_demand(CONST_SUBTITLE_REAL_ADDRESS);
			echo $html->put_info2(CONST_SUBTITLE_DPTO."-".CONST_SUBTITLE_PROV."-".CONST_SUBTITLE_DIST,$result_representante[0]['ubig_real']);
			echo $html->put_info2(CONST_SUBTITLE_ADDRESS,$result_representante[0]['dire_real']);
			echo $html->put_info2(CONST_SUBTITLE_REFERENCE,$result_representante[0]['refe_real']);

			echo $html->put_title_demand(CONST_SUBTITLE_LAW_ADDRESS);
			echo $html->put_info2(CONST_SUBTITLE_DPTO."-".CONST_SUBTITLE_PROV."-".CONST_SUBTITLE_DIST,$result_representante[0]['ubig_proc']);
			echo $html->put_info2(CONST_SUBTITLE_ADDRESS,$result_representante[0]['dire_proc']);
			echo $html->put_info2(CONST_SUBTITLE_REFERENCE,$result_representante[0]['refe_proc']);
			
			
			
		echo"</main></div>";
		echo $html->put_title_demand(CONST_TITLE_DEMANDADO);
		echo"<div id=\"manag2\" class=\"sub_group\" style=\"display:block;column-span:all\"><main>";
			echo $html->put_title_demand(CONST_SUBTITLE_PERSONAL_INFORMATION);
			echo $html->put_info2(CONST_SUBTITLE_DNI,$result_demandado[0]['ndoc_part']);
			echo $html->put_info2(CONST_SUBTITLE_APPA,$result_demandado[0]['appa_part']);
			echo $html->put_info2(CONST_SUBTITLE_APMA,$result_demandado[0]['apma_part']);
			echo $html->put_info2(CONST_SUBTITLE_NOMB,$result_demandado[0]['nomb_part']);
			echo $html->put_info2(CONST_SUBTITLE_CELU,$result_demandado[0]['tele_part']);
			echo $html->put_info2(CONST_SUBTITLE_SEX,$arra_options_sex[$result_demandado[0]['sexo_part']]);
			echo $html->put_info2(CONST_SUBTITLE_GINS,$arra_options_ginstruccion[$result_demandado[0]['gins_part']]);
			echo $html->put_info2(CONST_SUBTITLE_DISC,$arra_options_discapacidad[$result_demandado[0]['disc_part']]);
			echo $html->put_info2(CONST_SUBTITLE_VIVEEN,$arra_options_viveen[$result_demandado[0]['vive_part']]);
			echo $html->put_info2(CONST_SUBTITLE_PCARGO,$result_demandado[0]['pcar_part']);
			echo $html->put_info2(CONST_SUBTITLE_INGR,$arra_options_montos[$result_demandado[0]['ingr_part']]);
			echo $html->put_info2(CONST_SUBTITLE_EGRE,$arra_options_montos[$result_demandado[0]['egre_part']]);

			echo $html->put_title_demand(CONST_SUBTITLE_REAL_ADDRESS);
			echo $html->put_info2(CONST_SUBTITLE_DPTO."-".CONST_SUBTITLE_PROV."-".CONST_SUBTITLE_DIST,$result_demandado[0]['ubig_real']);
			echo $html->put_info2(CONST_SUBTITLE_ADDRESS,$result_demandado[0]['dire_real']);
			echo $html->put_info2(CONST_SUBTITLE_REFERENCE,$result_demandado[0]['refe_real']);

		echo"</main></div>";
		echo $html->put_title_demand(CONST_TITLE_DEMANDADO." - ".CONST_SUBTITLE_ECONOMY_INFORMATION);
		echo"<div id=\"manag2\" class=\"sub_group\" style=\"display:block;column-span:all\"><main>";

			echo $html->put_title_demand(CONST_SUBTITLE_TDEPENDIENTE);
			echo $html->put_info2(CONST_SUBTITLE_ENTERPRISE,$result_ieco_01[0]['det1_ieco']);
			echo $html->put_info2(CONST_SUBTITLE_ADDRESS,$result_ieco_01[0]['det2_ieco']);
			echo $html->put_info2(CONST_SUBTITLE_MONTH_INCOMING,$result_ieco_01[0]['cant_ieco']);

			echo $html->put_title_demand(CONST_SUBTITLE_TINDEPENDIENTE);
			echo $html->put_info2(CONST_SUBTITLE_LABOR,$result_ieco_02[0]['det1_ieco']);
			echo $html->put_info2(CONST_SUBTITLE_MONTH_INCOMING,$result_ieco_02[0]['cant_ieco']);

			echo $html->put_title_demand(CONST_SUBTITLE_NEGOCIO);
			echo $html->put_info2(CONST_SUBTITLE_RUBRO,$result_ieco_03[0]['det1_ieco']);
			echo $html->put_info2(CONST_SUBTITLE_MONTH_INCOMING,$result_ieco_03[0]['cant_ieco']);

			echo $html->put_title_demand(CONST_SUBTITLE_REAL_STATES);
			echo $html->put_info2(CONST_SUBTITLE_DETAILS,$result_ieco_04[0]['det1_ieco']);
			echo $html->put_info2(CONST_SUBTITLE_QANTITY,$result_ieco_04[0]['cant_ieco']);

			echo $html->put_title_demand(CONST_SUBTITLE_VEHICLES);
			echo $html->put_info2(CONST_SUBTITLE_DETAILS,$result_ieco_05[0]['det1_ieco']);
			echo $html->put_info2(CONST_SUBTITLE_QANTITY,$result_ieco_05[0]['cant_ieco']);

			echo $html->put_title_demand(CONST_SUBTITLE_QUANTITY_CHILDREN_INFORMATION);
			echo $html->put_info2(CONST_SUBTITLE_AGES,$result_ieco_06[0]['det1_ieco']);
			echo $html->put_info2(CONST_SUBTITLE_QANTITY,$result_ieco_06[0]['cant_ieco']);

			echo $html->put_title_demand(CONST_SUBTITLE_YOUNG_CHILDREN);
			echo $html->put_info2(CONST_SUBTITLE_AGES,$result_ieco_07[0]['det1_ieco']);
			echo $html->put_info2(CONST_SUBTITLE_QANTITY,$result_ieco_07[0]['cant_ieco']);

			echo $html->put_title_demand(CONST_SUBTITLE_OTHERS_OBLIGATIONS);
			echo $html->put_info2('',$result_ieco_08[0]['det1_ieco']);
	
		echo"</main></div>";
		echo $html->put_title_demand(CONST_SUBTITLE_INFORMACION_ALIMENTISTA);
		echo"<div id=\"manag2\" class=\"sub_group\" style=\"display:block;column-span:all\"><main>";
			$cont=0;
			for($a=1;$a<=$_POST['cant_alim_acum'];$a++)
			{
				$cont++;
				echo $html->put_title_demand(CONST_SUBTITLE_PERSONAL_INFORMATION_ALIMENTISTA." $cont");
				echo $html->put_info2(CONST_SUBTITLE_APPA,$_POST["appa_alim_$a"]);
				echo $html->put_info2(CONST_SUBTITLE_APMA,$_POST["apma_alim_$a"]);
				echo $html->put_info2(CONST_SUBTITLE_NOMB,$_POST["nomb_alim_$a"]);
				echo $html->put_info2(CONST_SUBTITLE_AGE, $_POST["edad_alim_$a"]);
				echo $html->put_info2(CONST_SUBTITLE_RDEM,$arra_options_rdemandado[$_POST["rela_alim_$a"]]);
				echo $html->put_info2(CONST_SUBTITLE_DISC,$arra_options_discapacidad[$_POST["disc_alim_$a"]]);
			}
		echo"</main></div>";

		echo $html->put_title_demand(CONST_SUBTITLE_PETITORIO);
		echo"<div id=\"manag2\" class=\"sub_group\" style=\"display:block;column-span:all\"><main>";

				echo $html->put_title_demand(CONST_SUBTITLE_PETITORIO);
				echo $html->put_info2(CONST_SUBTITLE_TIPO,$arra_options_petitorio[$result_demanda[0]['codi_peti']]);
				echo $html->put_info2(CONST_SUBTITLE_QANTITY,$result_demanda[0]['cant_denu']);

		echo"</main></div>";

		echo $html->put_separator_demand("30");
	echo"
		</main>
	";
}
?>
