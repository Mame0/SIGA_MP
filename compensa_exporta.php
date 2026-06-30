<?
	require_once 'include/cabecera.php';
	require_once 'classes/Html.class.php';
	require_once 'classes/Db.class.php';
	$Db = new Db();


require_once "spreadsheets/vendor/autoload.php";
$filename = "compensacion_horas.xlsx";
header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheet‌​ml.sheet");
header('Content-Disposition: attachment; filename="' . $filename. '"');

$spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load('plantillas/plantilla_compensacion_horas.xlsx');
$worksheet = $spreadsheet->getActiveSheet();

	$result_pagi=$Db->query("select mp_personal.*, esccargo
	from mp_personal left join mp_plan_escalaremunerativa on mp_personal.pers_cargo=mp_plan_escalaremunerativa.n_codigo
	order by pers_apepat asc, pers_apemat asc, pers_nombres asc ");
	$nfil=0;
	$nfil2=0;
	$nfil3=0;
	$table01[1][1] = "";
	$table02[1][1] = "";
	$table03[1][1] = "";

	$table04[1][1] = "";
	$table05[1][1] = "";


	$table06[1][1] = "";
	$table07[1][1] = "";


	$codpers[1] = 0;

	$cods[1][1] = "";
	$cods[2][1] = "";
	foreach($result_pagi as $rows) {
		if ($rows['activo']==1) {
			$nfil++;
			$table01[$nfil][1] = "'" . $rows['pers_dni'] ;
			$table01[$nfil][2] = utf8_encode($rows['pers_apepat'] . " " . $rows['pers_apemat'] . " " . $rows['pers_nombres']);
			$table01[$nfil][3] = $rows['esccargo'] ;
			$table01[$nfil][4] = $rows['pers_reglab'] ;
			$table01[$nfil][5] = ($rows['activo']==1)?"ACTIVO":"INACTIVO" ;
			$table01[$nfil][6] = $rows['pers_fecing'] ;

			$cods[1][$rows['pers_dni']] = $nfil;
		} else {
			$nfil2++;
			$table02[$nfil2][1] = "'" . $rows['pers_dni'] ;
			$table02[$nfil2][2] = utf8_encode($rows['pers_apepat'] . " " . $rows['pers_apemat'] . " " . $rows['pers_nombres']);
			$table02[$nfil2][3] = $rows['esccargo'] ;
			$table02[$nfil2][4] = $rows['pers_reglab'] ;
			$table02[$nfil2][5] = ($rows['activo']==1)?"ACTIVO":"INACTIVO" ;
			$table02[$nfil2][6] = $rows['pers_fecing'] ;

			$cods[2][$rows['pers_dni']] = $nfil2;
		}
/*
		$nfil3++;
		$table03[$nfil3][1] = "'" . $rows['pers_dni'] ;
		$table03[$nfil3][2] = utf8_encode($rows['pers_apepat'] . " " . $rows['pers_apemat'] . " " . $rows['pers_nombres']);
		$table03[$nfil3][3] = utf8_encode($rows['esccargo']) ;
		$table03[$nfil3][4] = utf8_encode($rows['pers_reglab']) ;
		$table03[$nfil3][5] = ($rows['activo']==1)?"ACTIVO":"INACTIVO" ;
		$table03[$nfil3][6] = $rows['pers_fecing'] ;
		$table03[$nfil3][7] = "" ;
*/

		$nfil4++;
		$table04[$nfil4][1] = "'" . $rows['pers_dni'] ;
		$table04[$nfil4][2] = utf8_encode($rows['pers_apepat'] . " " . $rows['pers_apemat'] . " " . $rows['pers_nombres']);
		$table04[$nfil4][3] = utf8_encode($rows['esccargo']) ;
		$table04[$nfil4][4] = utf8_encode($rows['pers_reglab']) ;
		$table04[$nfil4][5] = ($rows['activo']==1)?"ACTIVO":"INACTIVO" ;
		$table04[$nfil4][6] = $rows['pers_fecing'] ;
		$table04[$nfil4][7] = "" ;
		$codpers[$nfil4] = $rows['codi_pers'] ;

		$nfil5++;
		$table05[$nfil5][1] = "'" . $rows['pers_dni'] ;
		$table05[$nfil5][2] = utf8_encode($rows['pers_apepat'] . " " . $rows['pers_apemat'] . " " . $rows['pers_nombres']);
		$table05[$nfil5][3] = utf8_encode($rows['esccargo']) ;
		$table05[$nfil5][4] = utf8_encode($rows['pers_reglab']) ;
		$table05[$nfil5][5] = ($rows['activo']==1)?"ACTIVO":"INACTIVO" ;
		$table05[$nfil5][6] = $rows['pers_fecing'] ;
		$table05[$nfil5][7] = "" ;

	}

	$result=$Db->query("select mp_personal.*, esccargo, mp_horascompensa_vacaciones.*
	from (mp_personal left join mp_plan_escalaremunerativa on mp_personal.pers_cargo=mp_plan_escalaremunerativa.n_codigo)
	left join mp_horascompensa_vacaciones on mp_personal.codi_pers=mp_horascompensa_vacaciones.vaca_personal
	order by pers_apepat asc, pers_apemat asc, pers_nombres asc, vaca_fecemision");
	foreach($result as $rows) {
		$nfil3++;
		$table03[$nfil3][1] = "'" . $rows['pers_dni'] ;
		$table03[$nfil3][2] = utf8_encode($rows['pers_apepat'] . " " . $rows['pers_apemat'] . " " . $rows['pers_nombres']);
		$table03[$nfil3][3] = utf8_encode($rows['esccargo']) ;
		$table03[$nfil3][4] = utf8_encode($rows['pers_reglab']) ;
		$table03[$nfil3][5] = ($rows['activo']==1)?"ACTIVO":"INACTIVO" ;
		$table03[$nfil3][6] = $rows['pers_fecing'] ;
		$table03[$nfil3][7] = "" ;

		$table03[$nfil3][8] = $rows['vaca_expcea'];
		$table03[$nfil3][9] = $rows['vaca_anocea'];
		$table03[$nfil3][10] = utf8_encode($rows['vaca_resolucion']);
		$table03[$nfil3][11] = $rows['vaca_fecemision'];
		$table03[$nfil3][12] = utf8_encode($rows['vaca_periodo']);
		$table03[$nfil3][13] = $rows['vaca_fechaini'];
		$table03[$nfil3][14] = $rows['vaca_fechafin'];

		$dayini=substr($rows['vaca_fechaini'],8,2);
		$dayfin=substr($rows['vaca_fechafin'],8,2);

		$sumahoras=0;
		if ($rows['vaca_expcea']!="") {
			for ($dia=1; $dia<intval($dayini);$dia++) {
				$table03[$nfil3][14+$dia]="";
			}
			for ($dia=intval($dayini); $dia<=intval($dayfin);$dia++) {
				if ($rows['vaca_incluyesabdom']==1) {
					$table03[$nfil3][14+$dia]=8;
					$sumahoras=$sumahoras+8;
				} else {
					$fecha=substr($rows['vaca_fechaini'],0,8) . str_pad($dia, 2, "0", STR_PAD_LEFT);
					$diasem=date("w", strtotime($fecha));
					if ($diasem==6 || $diasem==0) {
						$table03[$nfil3][14+$dia]="";
					} else {
						$table03[$nfil3][14+$dia]=8;
						$sumahoras=$sumahoras+8;
					}
				}
			}
			for ($dia=(intval($dayfin)+1); $dia<31;$dia++) {
				$table03[$nfil3][14+$dia]="";
			}
		} else {
			for ($dia=1; $dia<=30;$dia++) {
				$table03[$nfil3][14+$dia]="";
			}
		}
		$table03[$nfil3][14+31] = $sumahoras;
		if ($rows['activo']==1) {
			$filaper=$cods[1][$rows['pers_dni']];
			$table06[$filaper][1]= intval($table06[$filaper][1]) + $sumahoras;
		} else {
			$filaper=$cods[2][$rows['pers_dni']];
			$table07[$filaper][1]= intval($table07[$filaper][1]) + $sumahoras;
		}
		if ($rows['vaca_expcea']!="") {
			$table03[$nfil3][14+32] = ($rows['vaca_incluyesabdom']==1)?"INCLUYE SAB Y DOM":"NO INCLUYE SAB Y DOM";
		}

	}

	for ($fil=1;$fil<=$nfil4;$fil++) {
		$cod=$codpers[$fil];
		$result=$Db->query("select mp_horascompensa_cabecera.* from mp_horascompensa_cabecera where comp_personal='".$cod."' order by comp_nroexpediente");
		$totexp=0;
		$acuhra=0;
		$nrocol=6;
		foreach($result as $rows) {
			$totexp++;
			$idcomp=$rows['comp_autogen'];

			$resdet=$Db->query("select sum(comp_horas) as canthoras from mp_horascompensa_detalle where comp_id='".$idcomp."' ");
			$canthoras = $resdet[0]['canthoras'];
			$acuhra=$acuhra + $canthoras ;
			$nrocol=$nrocol+2;
			$table04[$fil][$nrocol] = $result[0]['comp_nroexpediente'] . "-" . $result[0]['comp_anoexpediente'];
			$table04[$fil][$nrocol+1] = $canthoras;
		}
		if ($totexp<15) {
			for ($exp=($totexp+1);$exp<=15;$exp++) {
				$nrocol=$nrocol+2;
				$table04[$fil][$nrocol] = "";
				$table04[$fil][$nrocol+1] = "";
			}
		}
		$nrocol=$nrocol+2;
		$table04[$fil][$nrocol] = $acuhra;

		$dni=substr($table04[$fil][1],1,8);
		if ($table04[$fil][5]=="ACTIVO") {
			$filaper=$cods[1][ $dni ];
			$table06[$filaper][2]=$table06[$filaper][2] + $acuhra;
		} else {
			$filaper=$cods[2][ $dni ];
			$table07[$filaper][2]=$table07[$filaper][2] + $acuhra;
		}

	}

	$interval[1][1]="2021-11-05";
	$interval[1][2]="2021-11-25";
	$interval[2][1]="2021-11-26";
	$interval[2][2]="2021-12-25";
	$interval[3][1]="2021-12-26";
	$interval[3][2]="2022-01-31";
	$interval[4][1]="2022-02-01";
	$interval[4][2]="2022-02-28";
	$interval[5][1]="";
	$interval[5][2]="";
	$interval[6][1]="";
	$interval[6][2]="";
	$interval[7][1]="";
	$interval[7][2]="";
	$interval[8][1]="";
	$interval[8][2]="";
	$interval[9][1]="";
	$interval[9][2]="";
	$interval[10][1]="";
	$interval[10][2]="";
	$interval[11][1]="";
	$interval[11][2]="";
	$interval[12][1]="";
	$interval[12][2]="";
	$interval[13][1]="";
	$interval[13][2]="";


	for ($fil=1;$fil<=$nfil5;$fil++) {
		$cod=$codpers[$fil];
		$sumamin=0;
		for ($nco=1;$nco<=13;$nco++) {
			$colt=7+$nco;
			if ($interval[$nco][1]!="") {
				$result=$Db->query("select sum(TIME_TO_SEC(sobr_horas)/60) as totmin from mp_horascompensa_sobretiempo where sobr_personal='".$cod."' and sobr_fecha<='".$interval[$nco][2]."' and sobr_fecha>='".$interval[$nco][1]."'  ");
				if ($result[0]['totmin']!=0) {
					$sumamin=$sumamin+$result[0]['totmin'];
					$horas = floor($result[0]['totmin'] / 60);
					$minutos = floor($result[0]['totmin'] - ($horas * 60)) ;
					$table05[$fil][$colt] = str_pad($horas, 2, "0", STR_PAD_LEFT) . ":" . str_pad($minutos, 2, "0", STR_PAD_LEFT);
				} else {
					$table05[$fil][$colt] = "";
				}
			} else {
					$table05[$fil][$colt] = "";
			}
		}
		if ($sumamin!=0) {
			$horas = floor($sumamin / 60);
			$minutos = floor($sumamin - ($horas * 60)) ;
			$table05[$fil][21] = str_pad($horas, 2, "0", STR_PAD_LEFT) . ":" . str_pad($minutos, 2, "0", STR_PAD_LEFT);

			$dni=substr($table05[$fil][1],1,8);
			if ($table05[$fil][5]=="ACTIVO") {
				$filaper=$cods[1][ $dni ];
				$table06[$filaper][3]= $table05[$fil][21];
			} else {
				$filaper=$cods[2][ $dni ];
				$table07[$filaper][3]= $table05[$fil][21];
			}
		}
	}


	$worksheet = $spreadsheet->setActiveSheetIndex(0);
	$worksheet->fromArray($table01, null, "A3");
	$worksheet->fromArray($table06, null, "P3");
	$worksheet = $spreadsheet->setActiveSheetIndex(1);
	$worksheet->fromArray($table02, null, "A4");
	$worksheet->fromArray($table07, null, "Q4");

	$worksheet = $spreadsheet->setActiveSheetIndex(2);
	$worksheet->fromArray($table03, null, "A4");
	$worksheet = $spreadsheet->setActiveSheetIndex(3);
	$worksheet->fromArray($table04, null, "A3");
	$worksheet = $spreadsheet->setActiveSheetIndex(4);
	$worksheet->fromArray($table05, null, "A3");

	$writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
	$writer->save("php://output");

?>
