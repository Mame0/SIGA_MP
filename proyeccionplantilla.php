<?php
//	require_once 'include/cabecera.php';
//	require_once 'classes/Html.class.php';
	require_once 'classes/Db.class.php';
	$Db = new Db();


if (isset($_GET["dwld"])){
    header("Content-type: application/octet-stream");
    header("Content-Disposition: attachment; filename=".$_GET["nwfile"]);
	$fp = fopen($_GET["rt"].$_GET["dwld"], "rb"); // abre el archivo
	$buffer = fread($fp, filesize($_GET["rt"].$_GET["dwld"])); // escribe el archivo a una variable
	print $buffer; // al "imprimir" se esta enviando el archivo
	fclose($fp); // cierra la lectura
	unlink($_GET["rt"].$_GET["dwld"]);
	exit();
}


	$nropla=0;
	if($_GET['nropla']) {
		$nropla=$_GET['nropla'];
	}
	if ($nropla==1) { $colini="I"; $nromesesvac=12; }
	if ($nropla==2) { $colini="J"; $nromesesvac=11; }
	if ($nropla==3) { $colini="K"; $nromesesvac=10; }
	if ($nropla==4) { $colini="L"; $nromesesvac=9; }
	if ($nropla==5) { $colini="M"; $nromesesvac=8; }
	if ($nropla==6) { $colini="N"; $nromesesvac=7; }
	if ($nropla==7) { $colini="O"; $nromesesvac=6; }
	if ($nropla==8) { $colini="P"; $nromesesvac=5; }
	if ($nropla==9) { $colini="Q"; $nromesesvac=4; }
	if ($nropla==10) { $colini="R"; $nromesesvac=3; }
	if ($nropla==11) { $colini="S"; $nromesesvac=2; }
	if ($nropla==12) { $colini="T"; $nromesesvac=1; }
	$colfin="T";


require_once "spreadsheets/vendor/autoload.php";
$filename = "proyeccion.xlsx";
header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheet‌​ml.sheet");
header('Content-Disposition: attachment; filename="' . $filename. '"');

$spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load('plantillas/plantilla_proyeccion_saldos_'.$nropla.'.xlsx');
$worksheet = $spreadsheet->getActiveSheet();

$worksheet = $spreadsheet->setActiveSheetIndex(0);
for ($fil=6;$fil<=500;$fil++) {
	$meta=$worksheet->getCell("A".$fil)->getValue();
	$clas=$worksheet->getCell("B".$fil)->getValue();
	if ($meta=="") { break; }
	if ($clas!="") {
		$meta=str_pad($meta, 4, "0", STR_PAD_LEFT);

		$sumremu_meta=0;
		$sumbono_meta=0;
		$sumreta_meta=0;
		$sumcafa_meta=0;

		$sumessa_meta=0;
		$sumeps_meta=0;

		$sumfpen_meta=0;
		$sumbeneextra_meta=0;
		$sumasifam_meta=0;
		$sumgope_meta=0;

		$sumgrati728=0;
		$sumgratadd_728=0;


		//plazas vacantes
		$sumremu_vac276=0;
		$sumbono_vac276=0;
		$sumgope_vac276=0;
		$sumessa_vac276=0;


		$sumremu_vac728=0;
		$sumessa_vac728=0;

		$sumesco=0;



		//if ($clas=="2.1.1.4.1.1") {//25% retardo
		//	$result_depe=$Db->query("SELECT sum(escremunerabasica)*0.25 as sum25ret FROM mp_personal left join mp_plan_escalaremunerativa on mp_personal.pers_cargo=mp_plan_escalaremunerativa.n_codigo where meta='".$meta."' and clas_25retardo='".$clas."' ");
		//	$sumreta_meta=$result_depe[0]['sum25ret'];
		//} else
		if ($clas=="2.1.1.1.2.1") {//cafae
			$result_depe=$Db->query("SELECT sum(esccafae) as sumcafae FROM mp_personal left join mp_plan_escalaremunerativa on if(codcargopea<>0, mp_personal.codcargopea, mp_personal.pers_cargo)=mp_plan_escalaremunerativa.n_codigo where activo=1 and meta='".$meta."' and clas_cafae='".$clas."' ");
			$sumcafa_meta=$result_depe[0]['sumcafae'];
		} elseif ($clas=="2.1.3.1.1.5") {//essalud 9 %

			$result_depe=$Db->query("SELECT sum(if(escremunerabasica<930,930*0.09, escremunerabasica*0.09)) as sumessalud9 FROM mp_personal left join mp_plan_escalaremunerativa on if(codcargopea<>0, mp_personal.codcargopea, mp_personal.pers_cargo)=mp_plan_escalaremunerativa.n_codigo where activo=1 and meta='".$meta."' and clas_essalud9porc='".$clas."' and eps=0 and escdecretoley='276' ");
			$sum1_276=$result_depe[0]['sumessalud9'];
			$result_depe=$Db->query("SELECT sum(if(escremunerabasica<930,930*0.0675, escremunerabasica*0.0675)) as sumessalud9 FROM mp_personal left join mp_plan_escalaremunerativa on if(codcargopea<>0, mp_personal.codcargopea, mp_personal.pers_cargo)=mp_plan_escalaremunerativa.n_codigo where activo=1 and meta='".$meta."' and clas_essalud9porc='".$clas."' and eps=1 and escdecretoley='276' ");
			$sum2_276=$result_depe[0]['sumessalud9'];

			$result_depe=$Db->query("SELECT sum((escremunerabasica*0.4 +(if(asignacionfamiliar=1,93,0)) )*0.09) as sumessalud9 FROM mp_personal left join mp_plan_escalaremunerativa on if(codcargopea<>0, mp_personal.codcargopea, mp_personal.pers_cargo)=mp_plan_escalaremunerativa.n_codigo where activo=1 and meta='".$meta."' and clas_essalud9porc='".$clas."' and eps=0 and escdecretoley='728' ");
			$sum1_728=$result_depe[0]['sumessalud9'];
			$result_depe=$Db->query("SELECT sum((escremunerabasica*0.4 +(if(asignacionfamiliar=1,93,0)) )*0.0675) as sumessalud9 FROM mp_personal left join mp_plan_escalaremunerativa on if(codcargopea<>0, mp_personal.codcargopea, mp_personal.pers_cargo)=mp_plan_escalaremunerativa.n_codigo where activo=1 and meta='".$meta."' and clas_essalud9porc='".$clas."' and eps=1 and escdecretoley='728' ");
			$sum2_728=$result_depe[0]['sumessalud9'];


			$sumessa_meta=$sum1_276+$sum2_276 + $sum1_728+$sum2_728;


			//plazas vacantes
			$result_depe=$Db->query("SELECT sum(if(escremunerabasica<930,930*0.09, escremunerabasica*0.09)*nroplazas) as sumessalud9 FROM mp_plan_plazasvacantes left join mp_plan_escalaremunerativa on mp_plan_plazasvacantes.codcargo=mp_plan_escalaremunerativa.n_codigo where meta='".$meta."' and escdecretoley='276' and mesplaza=".$nropla." ");
			$sumessa_vac276=$result_depe[0]['sumessalud9'];
			$result_depe=$Db->query("SELECT sum( ((escremunerabasica*0.4)*0.09) * nroplazas) as sumessalud9 FROM mp_plan_plazasvacantes left join mp_plan_escalaremunerativa on mp_plan_plazasvacantes.codcargo=mp_plan_escalaremunerativa.n_codigo where meta='".$meta."' and escdecretoley='728' and mesplaza=".$nropla." ");
			$sumessa_vac728=$result_depe[0]['sumessalud9'];


		} elseif ($clas=="2.1.3.1.1.1") {//eps 2.25 %

			$result_depe=$Db->query("SELECT sum(if(escremunerabasica<930, 930*0.0225, escremunerabasica*0.0225)) as sumeps225 FROM mp_personal left join mp_plan_escalaremunerativa on if(codcargopea<>0, mp_personal.codcargopea, mp_personal.pers_cargo)=mp_plan_escalaremunerativa.n_codigo where activo=1 and meta='".$meta."' and clas_eps225='".$clas."' and eps=1 and escdecretoley='276' ");
			$sum276=$result_depe[0]['sumeps225'];
			$result_depe=$Db->query("SELECT sum((escremunerabasica*0.4 +(if(asignacionfamiliar=1,93,0)) )*0.0225) as sumeps225 FROM mp_personal left join mp_plan_escalaremunerativa on if(codcargopea<>0, mp_personal.codcargopea, mp_personal.pers_cargo)=mp_plan_escalaremunerativa.n_codigo where activo=1 and meta='".$meta."' and clas_eps225='".$clas."' and eps=1 and escdecretoley='728' ");
			$sum728=$result_depe[0]['sumeps225'];
			//728 primero se saca 40% y a ese 40% recien el 2.25%
			$sumeps_meta = $sum276 + $sum728;


		} elseif ($clas=="2.1.3.1.1.3") {//fondo pension 6%
			$result_depe=$Db->query("SELECT sum(escremunerabasica)*0.06 as sumfonpen6 FROM mp_personal left join mp_plan_escalaremunerativa on if(codcargopea<>0, mp_personal.codcargopea, mp_personal.pers_cargo)=mp_plan_escalaremunerativa.n_codigo where activo=1 and meta='".$meta."' and clas_fondopens6porc='".$clas."' ");
			$sumfpen_meta=$result_depe[0]['sumfonpen6'];

		} elseif ($clas=="2.1.1.9.1.1") {//gratificacion 728 es un sueldo completo

			$result_depe=$Db->query("SELECT sum(escremunerabasica) as sumescrem FROM mp_personal left join mp_plan_escalaremunerativa on if(codcargopea<>0, mp_personal.codcargopea, mp_personal.pers_cargo)=mp_plan_escalaremunerativa.n_codigo where activo=1 and meta='".$meta."' and clas_aguinaldo='".$clas."' and escdecretoley=728 ");
			$sumremu728=$result_depe[0]['sumescrem'];

			$result_depe=$Db->query("SELECT ((count(pers_dni) * 930) * 0.1) as sumasgfam FROM
			mp_personal left join mp_plan_escalaremunerativa on if(codcargopea<>0, mp_personal.codcargopea, mp_personal.pers_cargo)=mp_plan_escalaremunerativa.n_codigo
			where activo=1 and meta='".$meta."' and asignacionfamiliar=1 and clas_aguinaldo='".$clas."' and escdecretoley=728 ");
			$sumasifam728=$result_depe[0]['sumasgfam'];

			$sumgrati728 = $sumremu728 + $sumasifam728;

		} elseif ($clas=="2.1.1.9.1.3") {//ESCOLARIDAD

			$result_depe=$Db->query("SELECT (count(pers_dni) * 400)  as sumesc FROM
			mp_personal left join mp_plan_escalaremunerativa on mp_personal.pers_cargo=mp_plan_escalaremunerativa.n_codigo
			where activo=1 and meta='".$meta."' and clas_escolaridad='".$clas."' ");
			$sumesco=$result_depe[0]['sumesc'];

		} elseif ($clas=="2.1.1.9.3.99") {//gratificacion 9% 728

			$result_depe=$Db->query("SELECT sum((escremunerabasica*0.4 +(if(asignacionfamiliar=1,93,0)) )*0.09) as sumgrat9 FROM mp_personal left join mp_plan_escalaremunerativa on if(codcargopea<>0, mp_personal.codcargopea, mp_personal.pers_cargo)=mp_plan_escalaremunerativa.n_codigo where activo=1 and meta='".$meta."' and clas_grati9porc='".$clas."' and eps=0 and escdecretoley='728' ");
			$sumgratadd1_728=$result_depe[0]['sumgrat9'];
			$result_depe=$Db->query("SELECT sum((escremunerabasica*0.4 +(if(asignacionfamiliar=1,93,0)) )*0.0675) as sumgrat9 FROM mp_personal left join mp_plan_escalaremunerativa on if(codcargopea<>0, mp_personal.codcargopea, mp_personal.pers_cargo)=mp_plan_escalaremunerativa.n_codigo where activo=1 and meta='".$meta."' and clas_grati9porc='".$clas."' and eps=1 and escdecretoley='728' ");
			$sumgratadd2_728=$result_depe[0]['sumgrat9'];

			$sumgratadd_728=$sumgratadd1_728+$sumgratadd2_728;

		} elseif ($clas=="2.1.1.9.1.2") {//aguinaldo - se saca en base a su cargo titular

			$result_depe=$Db->query("SELECT sum(escaguinaldo) as sumaguinaldo FROM mp_personal left join mp_plan_escalaremunerativa on mp_personal.pers_cargo=mp_plan_escalaremunerativa.n_codigo where activo=1 and meta='".$meta."' and clas_aguinaldo='".$clas."' and codcargopea<>5 and escdecretoley=276 ");
			$sumagui1=$result_depe[0]['sumaguinaldo'];
			$result_depe=$Db->query("SELECT sum(escaguinaldo) as sumaguinaldo FROM mp_personal left join mp_plan_escalaremunerativa on mp_personal.codcargopea=mp_plan_escalaremunerativa.n_codigo where activo=1 and meta='".$meta."' and clas_aguinaldo='".$clas."' and codcargopea=5 and escdecretoley=276 ");
			$sumagui2=$result_depe[0]['sumaguinaldo'];
			$sumagui_meta=$sumagui1+$sumagui2;


		} elseif ($clas=="2.1.1.4.2.2") {//gastos operativos - se saca en base a su cargo titular
			$result_depe=$Db->query("SELECT sum(escgastosope) as sumgastosope FROM mp_personal left join mp_plan_escalaremunerativa on mp_personal.pers_cargo=mp_plan_escalaremunerativa.n_codigo where activo=1 and meta='".$meta."' and clas_go='".$clas."' and codcargopea<>5 ");
			$sumgope1=$result_depe[0]['sumgastosope'];
			$result_depe=$Db->query("SELECT sum(escgastosope) as sumgastosope FROM mp_personal left join mp_plan_escalaremunerativa on mp_personal.codcargopea=mp_plan_escalaremunerativa.n_codigo where activo=1 and meta='".$meta."' and clas_go='".$clas."' and codcargopea=5 ");
			$sumgope2=$result_depe[0]['sumgastosope'];
			$sumgope_meta=$sumgope1+$sumgope2;


			//2.1.1.4.2.2 PLAZAS VACANTES
			$result_depe=$Db->query("SELECT sum(escgastosope*nroplazas) as sumgastosope FROM mp_plan_plazasvacantes left join mp_plan_escalaremunerativa on mp_plan_plazasvacantes.codcargo=mp_plan_escalaremunerativa.n_codigo where meta='".$meta."' and escdecretoley=276 and mesplaza=".$nropla." ");
			$sumgope_vac=$result_depe[0]['sumgastosope'];


		} else {
			$result_depe=$Db->query("SELECT sum(escremunerabasica) as sumescrem FROM mp_personal left join mp_plan_escalaremunerativa on if(codcargopea<>0, mp_personal.codcargopea, mp_personal.pers_cargo)=mp_plan_escalaremunerativa.n_codigo where activo=1 and meta='".$meta."' and clas_haberes='".$clas."' ");
			$sumremu_meta=$result_depe[0]['sumescrem'];

			$result_depe=$Db->query("SELECT sum(escbonificajurisdiccional) as sumbobjur FROM mp_personal left join mp_plan_escalaremunerativa on if(codcargopea<>0, mp_personal.codcargopea, mp_personal.pers_cargo)=mp_plan_escalaremunerativa.n_codigo where activo=1 and meta='".$meta."' and clas_bonofiscal='".$clas."' ");
			$sumbono_meta=$result_depe[0]['sumbobjur'];

			$result_depe=$Db->query("SELECT sum(escremunerabasica)*0.25 as sum25ret FROM mp_personal left join mp_plan_escalaremunerativa on if(codcargopea<>0, mp_personal.codcargopea, mp_personal.pers_cargo)=mp_plan_escalaremunerativa.n_codigo where activo=1 and meta='".$meta."' and clas_25retardo='".$clas."' ");
			$sumreta_meta=$result_depe[0]['sum25ret'];

			$result_depe=$Db->query("SELECT sum(escbenefextra) as sumbenefextra FROM mp_personal left join mp_plan_escalaremunerativa on if(codcargopea<>0, mp_personal.codcargopea, mp_personal.pers_cargo)=mp_plan_escalaremunerativa.n_codigo where activo=1 and meta='".$meta."' and clas_benefextra='".$clas."' ");
			$sumbeneextra_meta=$result_depe[0]['sumbenefextra'];


			//para plazas vacantes haberes, bonofiscal
			//2.1.1.4.1.1    2.1.1.4.2.1
			if ($clas=="2.1.1.4.1.1") {
				$result_depe=$Db->query("SELECT sum(escremunerabasica*nroplazas) as sumescrem FROM mp_plan_plazasvacantes left join mp_plan_escalaremunerativa on mp_plan_plazasvacantes.codcargo=mp_plan_escalaremunerativa.n_codigo where meta='".$meta."' and escdecretoley=276 and mesplaza=".$nropla." ");
				$sumremu_vac276=$result_depe[0]['sumescrem'];
			}
			if ($clas=="2.1.1.4.2.1") {
				$result_depe=$Db->query("SELECT sum(escbonificajurisdiccional*nroplazas) as sumbobjur FROM mp_plan_plazasvacantes left join mp_plan_escalaremunerativa on mp_plan_plazasvacantes.codcargo=mp_plan_escalaremunerativa.n_codigo where meta='".$meta."' and escdecretoley=276 and mesplaza=".$nropla." ");
				$sumbono_vac276=$result_depe[0]['sumbobjur'];
			}

			if ($clas=="2.1.1.1.1.5" || $clas=="2.1.1.3.1.2") {
			//2.1.1.1.1.5  o  2.1.1.3.1.2
			$result_depe=$Db->query("SELECT sum(escremunerabasica*nroplazas) as sumescrem FROM mp_plan_plazasvacantes left join mp_plan_escalaremunerativa on mp_plan_plazasvacantes.codcargo=mp_plan_escalaremunerativa.n_codigo where meta='".$meta."' and escdecretoley=728 and mesplaza=".$nropla." ");
			$sumremu_vac728=$result_depe[0]['sumescrem'];
			}

		}

		if ($clas=="2.1.1.1.1.4" || $clas=="2.1.1.1.1.5" || $clas=="2.1.1.3.1.2") {//asignafamiliar
			$result_depe=$Db->query("SELECT ((count(pers_dni) * 930) * 0.1) as sumasgfam FROM
			mp_personal left join mp_plan_escalaremunerativa on if(codcargopea<>0, mp_personal.codcargopea, mp_personal.pers_cargo)=mp_plan_escalaremunerativa.n_codigo
			where activo=1 and meta='".$meta."' and asignacionfamiliar=1 and clas_haberes='".$clas."' ");
			$sumasifam_meta=$result_depe[0]['sumasgfam'];
		}


		foreach(range($colini,$colfin) as $columnID) {
//			$worksheet->getCell($columnID.$fil)->setValue($sumremu_meta + $sumbono_meta + $sumreta_meta + $sumcafa_meta + $sumessa_meta + $sumeps_meta + $sumfpen_meta + $sumbeneextra_meta + $sumasifam_meta + $sumgope_meta);
			if ( ($colfin==$columnID || $columnID=="O")  &&  $clas=="2.1.1.9.1.2") {
				$worksheet->getCell($columnID.$fil)->setValue($sumagui_meta);

			} elseif ( ($colfin==$columnID || $columnID=="O")  &&  $clas=="2.1.1.9.1.1") {//gratificacion 728 es un sueldo completo incluyendo asigna familiar
				$worksheet->getCell($columnID.$fil)->setValue($sumgrati728);

			} elseif ( ($colfin==$columnID || $columnID=="O")  &&  $clas=="2.1.1.9.3.99") {//gratificacion 9% 728
				$worksheet->getCell($columnID.$fil)->setValue($sumgratadd_728);

			} else {

				$worksheet->getCell($columnID.$fil)->setValue($sumremu_meta + $sumbono_meta + $sumreta_meta + $sumcafa_meta + $sumessa_meta + $sumeps_meta + $sumfpen_meta + $sumbeneextra_meta + $sumasifam_meta + $sumgope_meta );

			}

			if ( ($columnID=="I" && $nropla==1)  &&  $clas=="2.1.1.9.1.3") {//escolaridad
				$worksheet->getCell($columnID.$fil)->setValue( $sumesco );
			}

		}

		$worksheet->getCell("U".$fil)->setValue( ($sumremu_vac276 + $sumbono_vac276 + $sumgope_vac276   +   $sumessa_vac276) * $nromesesvac );
		$worksheet->getCell("V".$fil)->setValue( ($sumremu_vac728   +   $sumessa_vac728) * $nromesesvac );

	}
}

$writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
//$writer->save("php://output");
$writer->save("temp/".$filename);

?>
