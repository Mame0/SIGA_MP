<?php
ob_start();
	require_once 'include/cabecera.php';
	require_once 'classes/Html.class.php';
	require_once 'classes/Db.class.php';
	$Db = new Db();
	
	require_once 'spreadsheets/vendor/autoload.php';

        use PhpOffice\PhpSpreadsheet\Spreadsheet;
        use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
        use PhpOffice\PhpSpreadsheet\Style\Fill;
        use PhpOffice\PhpSpreadsheet\Style\Alignment;
        use PhpOffice\PhpSpreadsheet\Style\Border;

	if(!isset($_POST['busc_pagi_actu']))
		$_POST['busc_pagi_actu']=1;
        
    if(!isset($_POST['busq_tipo']) || !$_POST['busq_tipo'])
    {
        $_POST['busq_tipo']=1;
        $_POST['fech_desd']=date("Y-m-d");
        $_POST['fech_hast']=date("Y-m-d");
    }
    
    if(!isset($_POST['codi_inve']))
	{
	    $result=$Db->query("select * from mp_inve_mant where acti_inve='1' AND esta_inve='1' order by fech_inve limit 1");
	    foreach($result as $rows)
	    {
	        $_POST['codi_inve']=$rows['codi_inve'];
	        $_POST['fech_inve']=$rows['fech_inve'];
	        $_POST['nomb_inve']=$rows['nomb_inve'];
	    }
	}

    $busc_tota_item=0;
    if(isset($_POST['codi_inve'])) {
	    $result=$Db->query("select count(*) as cant from mp_inve_view_regi where codi_inve='".$_POST['codi_inve']."' AND fdig_regi>='".str_replace("-","",$_POST['fech_desd']).'000000'."' AND fdig_regi<='".str_replace("-","",$_POST['fech_hast']).'999999'."'");
	    foreach($result as $rows)
	        $busc_tota_item=$rows['cant'];
    }
	
	if(isset($_POST['desc_exce']) && $_POST['desc_exce'])
	{
	    // ==================== EXPORTAR A EXCEL XLSX ====================
        ob_end_clean();
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Reporte de Inventario');
    
        // TÍTULO PRINCIPAL
        //$sheet->mergeCells('A1:L1');
        $sheet->setCellValue('A1', 'MINISTERIO PÚBLICO - DISTRITO FISCAL DE AREQUIPA');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(11)->getColor()->setARGB('000000');
        $sheet->getStyle('A1')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFFFFF');
        //$sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        //$sheet->getRowDimension(1)->setRowHeight(25);
    
        // SUBTÍTULO
        //$sheet->mergeCells('A2:L2');
        $sheet->setCellValue('A2', 'REPORTE DE INVENTARIO DE BIENES MUEBLES 2025 [DEL '.$_POST['fech_desd'].' AL '.$_POST['fech_hast'].']');
        $sheet->getStyle('A2')->getFont()->setBold(true)->setSize(11)->getColor()->setARGB('000000');
        $sheet->getStyle('A2')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFFFFF');
        //$sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        //$sheet->getRowDimension(2)->setRowHeight(20);
    
        // INFORMACIÓN DEL REPORTE
        //$sheet->mergeCells('A3:L3');
        $sheet->setCellValue('A3', 'Fecha de generación: ' . date('d/m/Y H:i:s') . ' | Total de registros: ' . $busc_tota_item);
        $sheet->getStyle('A3')->getFont()->setBold(true)->setSize(11);
        $sheet->getStyle('A3')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFFFFF');
        //$sheet->getStyle('A3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    
        // ENCABEZADOS
        $headers = ['nro.', 'codigo_patrimonial', 'codigo_barra', 'descripcion', 'marca', 'modelo', 'nro_serie', 'color', 'estado', 'medidas', 'fecha_alta', 'ubicac_fisica','usuario','docum_identidad','observaciones','nombre_depend','nombre_sede','uso','inventario_local','inventario_dependencia','inventario_usuario','inventario_estado','inventario_uso','inventario_are','inventario_observacion','inventario_cambio_local','inventario_cambio_dependencia','inventario_cambio_usuario','inventario_operador','inventario_fecha'];
        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($col . '4', $header);
            $col++;
        }
    
        $sheet->getStyle('A4:AD4')->getFont()->setBold(true)->getColor()->setARGB('FFFFFF');
        $sheet->getStyle('A4:R4')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('4A90E2');
        $sheet->getStyle('S4:AD4')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('43DD3F');
        $sheet->getStyle('A4:AD4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A4:AD4')->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        
        //VOLCAR DATOS A ARRAYS
        $result=$Db->query("select * from mp_admi_loca");
	    foreach($result as $rows)
	        $arra_loca[$rows['codi_loca']]=$rows['nom1_loca'];
	   
	    $result=$Db->query("select * from mp_admi_depe");
	    foreach($result as $rows)
	        $arra_depe[$rows['codi_depe']]=$rows['nomb_depe'];
	    
	    $result=$Db->query("select * from mp_admi_pers");
	    foreach($result as $rows)
	        $arra_pers[$rows['iden_pers']]=$rows['appa_pers'].' '.$rows['apma_pers'].', '.$rows['nomb_pers'];
	    
	    $result=$Db->query("select * from mp_admi_oper");
	    foreach($result as $rows)
	        $arra_oper[$rows['iden_oper']]=$rows['appa_oper'].' '.$rows['apma_oper'].', '.$rows['nomb_oper'];
    
        // DATOS
        $row = 5;
        $cont = 1;
        $datos=$Db->query("select * from mp_inve_view_regi where codi_inve='".$_POST['codi_inve']."' AND fdig_regi>='".str_replace("-","",$_POST['fech_desd']).'000000'."' AND fdig_regi<='".str_replace("-","",$_POST['fech_hast']).'999999'."' order by fdig_regi desc");
        foreach ($datos as $data)
        {
            $sheet->setCellValue('A' . $row, $cont++);
            //$sheet->setCellValueExplicit('B' . $row, $data['codigo_patrimonial'], DataType::TYPE_STRING);
            $sheet->setCellValue('B' . $row, $data['codigo_patrimonial']);
            $sheet->setCellValue('C' . $row, strtoupper($data['codigo_barra']));
            $sheet->setCellValue('D' . $row, strtoupper($data['descripcion']));
            $sheet->setCellValue('E' . $row, $data['marca']);
            $sheet->setCellValue('F' . $row, $data['modelo']);
            $sheet->setCellValue('G' . $row, $data['nro_serie']);
            $sheet->setCellValue('H' . $row, $data['color']);
            $sheet->setCellValue('I' . $row, $data['nombre']);
            $sheet->setCellValue('J' . $row, $data['medidas']);
            $sheet->setCellValue('K' . $row, $data['fecha_alta']);
            $sheet->setCellValue('L' . $row, $data['ubicac_fisica']);
            $sheet->setCellValue('M' . $row, $data['usuario']);
            $sheet->setCellValue('N' . $row, $data['docum_identidad']);
            $sheet->setCellValue('O' . $row, $data['observaciones']);
            $sheet->setCellValue('P' . $row, $data['nombre_depend']);
            $sheet->setCellValue('Q' . $row, $data['nombre_sede']);
            $sheet->setCellValue('R' . $row, $data['uso']);
            $sheet->setCellValue('S' . $row, (isset($arra_loca[$data['codi_loca']]) ? $arra_loca[$data['codi_loca']] : ''));
            $sheet->setCellValue('T' . $row, (isset($arra_depe[$data['codi_depe']]) ? $arra_depe[$data['codi_depe']] : ''));
            $sheet->setCellValue('U' . $row, (isset($arra_pers[$data['usua_inve']]) ? $arra_pers[$data['usua_inve']] : ''));
            $sheet->setCellValue('V' . $row, $data['iest_regi']);
            $sheet->setCellValue('W' . $row, $data['iuso_regi']);
            $sheet->setCellValue('X' . $row, $data['iare_regi']);
            $sheet->setCellValue('Y' . $row, $data['iobs_regi']);
            $sheet->setCellValue('Z' . $row,  (isset($arra_loca[$data['iubi_regi']]) ? $arra_loca[$data['iubi_regi']] : ''));
            $sheet->setCellValue('AA' . $row, (isset($arra_depe[$data['idep_regi']]) ? $arra_depe[$data['idep_regi']] : ''));
            $sheet->setCellValue('AB' . $row, (isset($arra_pers[$data['iusu_regi']]) ? $arra_pers[$data['iusu_regi']] : ''));
            $sheet->setCellValue('AC' . $row, (isset($arra_oper[$data['digi_regi']]) ? $arra_oper[$data['digi_regi']] : ''));
            $fdig=substr($data['fdig_regi'],0,4).'/'.substr($data['fdig_regi'],4,2).'/'.substr($data['fdig_regi'],6,2).' '.substr($data['fdig_regi'],8,2).':'.substr($data['fdig_regi'],10,2).':'.substr($data['fdig_regi'],12,2);
            $sheet->setCellValue('AD' . $row, $fdig);
            //$sheet->setCellValue('L' . $row, !empty($data['fing_pers']) ? date('d/m/Y', strtotime($data['fing_pers'])) : '-');
        
            if ($cont % 2 == 0) {
                $sheet->getStyle('A' . $row . ':AD' . $row)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('F9F9F9');
            }
        
            $row++;
        }
    
        $sheet->getStyle('A4:AD' . ($row - 1))->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        
        $sheet->getStyle('R4:R' . ($row - 1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('V4:X' . ($row - 1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    
        // Ajustar columnas
        $sheet->getColumnDimension('A')->setWidth(5);
        $sheet->getColumnDimension('B')->setWidth(20);
        $sheet->getColumnDimension('C')->setWidth(17);
        $sheet->getColumnDimension('D')->setWidth(30);
        $sheet->getColumnDimension('E')->setWidth(15);
        $sheet->getColumnDimension('F')->setWidth(15);
        $sheet->getColumnDimension('G')->setWidth(15);
        $sheet->getColumnDimension('H')->setWidth(15);
        $sheet->getColumnDimension('I')->setWidth(15);
        $sheet->getColumnDimension('J')->setWidth(15);
        $sheet->getColumnDimension('K')->setWidth(15);
        $sheet->getColumnDimension('L')->setWidth(30);
        $sheet->getColumnDimension('M')->setWidth(30);
        $sheet->getColumnDimension('N')->setWidth(20);
        $sheet->getColumnDimension('O')->setWidth(30);
        $sheet->getColumnDimension('P')->setWidth(30);
        $sheet->getColumnDimension('Q')->setWidth(30);
        $sheet->getColumnDimension('R')->setWidth(10);
        $sheet->getColumnDimension('S')->setWidth(30);
        $sheet->getColumnDimension('T')->setWidth(30);
        $sheet->getColumnDimension('U')->setWidth(30);
        $sheet->getColumnDimension('V')->setWidth(17);
        $sheet->getColumnDimension('W')->setWidth(17);
        $sheet->getColumnDimension('X')->setWidth(17);
        $sheet->getColumnDimension('Y')->setWidth(60);
        $sheet->getColumnDimension('Z')->setWidth(30);
        $sheet->getColumnDimension('AA')->setWidth(30);
        $sheet->getColumnDimension('AB')->setWidth(30);
        $sheet->getColumnDimension('AC')->setWidth(30);
        $sheet->getColumnDimension('AD')->setWidth(20);
    
        $filename = 'reporte_personal_' . date('Ymd_His') . '.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
    
        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
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
		<script>
		    function f_descargar()
		    {
		        document.form.desc_exce.value='1';
		        document.form.submit();
		    }
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
		        document.form.action='';
		        document.form.submit();
		        //var tipo='1';
		        //alert(document.form.fech_desd.value);
		    }
			function check_buscar()
			{
				document.form.action='';
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
	<center><h4 style="color:#073A6B"><b>DESCARGAR INVENTARIO<BR>[<?=(isset($_POST['fech_inve']) ? $_POST['fech_inve'] : '')?>] <?=(isset($_POST['nomb_inve']) ? $_POST['nomb_inve'] : '')?></h4></b></center>
		<form name="form" method="post">
			<input type=hidden name="desc_exce">
			<input type=hidden name="codi_inve" value="<?=(isset($_POST['codi_inve']) ? $_POST['codi_inve'] : '')?>">
			<input type=hidden name="nomb_inve" value="<?=(isset($_POST['nomb_inve']) ? $_POST['nomb_inve'] : '')?>">
			<input type=hidden name="fech_inve" value="<?=(isset($_POST['fech_inve']) ? $_POST['fech_inve'] : '')?>">
			<input type=hidden name="sesi_codi_depe" value="<?=(isset($_SESSION['codi_depe']) ? $_SESSION['codi_depe'] : '')?>">
			<input type=hidden name="busc_pagi_actu" value="<?=(isset($_POST['busc_pagi_actu']) ? $_POST['busc_pagi_actu'] : '')?>">
<?
	$html=new htmlclass;
	
	$anno_actu=date("Y");
    $anno_pasa=date("Y")-1;
	$arra_options_busq[0]="<- Seleccione ->";
	$arra_options_busq[1]="Hoy";
	$arra_options_busq[2]="Ayer";
	$arra_options_busq[3]="Mes actual";
	$arra_options_busq[4]="Últimos 30 días";
	$arra_options_busq[5]="Año $anno_actu";
	$arra_options_busq[6]="Año $anno_pasa";
	$arra_options_busq[7]="Rango de Fechas";
	
	/*
	$arra_options_mpar[0]="<- Todos ->";
	$result=$Db->query("select distinct b.codi_depe,b.nomb_depe from mp_mpar_carpetas a, mp_admi_depe b where a.depe_mpar=b.codi_depe AND a.esta_mpar=1 AND a.codi_depe<>0 AND a.codi_pers<>0");
    foreach($result as $rows)
            $arra_options_mpar[$rows['codi_depe']]=$rows['nomb_depe'];
	*/
	
	echo"<main>";
	//echo $html->put_title_demand("Criterios de Búsqueda");
	echo $html->put_select("Rangos&nbsp;Pre&nbsp;Definidos",'busq_tipo',$arra_options_busq,(isset($_POST['busq_tipo']) ? $_POST['busq_tipo'] : '')," onchange=\"ajustar_rango()\"");
	echo $html->put_text('date',"Desde","Fecha desde",'fech_desd',(isset($_POST['fech_desd']) ? $_POST['fech_desd'] : ''),'','100','');
	echo $html->put_text('date',"Hasta","Fecha desde",'fech_hast',(isset($_POST['fech_hast']) ? $_POST['fech_hast'] : ''),'','100','');
	echo"</main>";
	
	/*
	echo"<main>";
	echo $html->put_select("Mesa&nbsp;de&nbsp;Partes",'sesi_codi_depe',$arra_options_mpar,$_POST['sesi_codi_depe'],"");
	echo"</main>";
	*/

    /*
    $result=$Db->query("select iden_oper,logi_oper,ndoc_oper,appa_oper,apma_oper,nomb_oper from mp_admi_oper");
    foreach($result as $rows)
    {
        $nomb=explode(" ",$rows['nomb_oper']);
            $arra_oper[$rows['iden_oper']]=$rows['appa_oper']."<BR>".$nomb[0];
            //$arra_oper[$rows['iden_oper']]=$rows['appa_oper']." ".$rows['apma_oper']."<BR>".$rows['nomb_oper'];
    }
    */

	$busc_item_pagi=100;      //cantidad de items por pagina
	
	// $busc_tota_item is already calculated above
	
	$busc_tota_pagi=ceil($busc_tota_item/$busc_item_pagi);
	$busc_limi_pagi=($_POST['busc_pagi_actu']-1)*$busc_item_pagi;
	
	$result_pagi=$Db->query("select * from mp_inve_view_regi where codi_inve='".(isset($_POST['codi_inve']) ? $_POST['codi_inve'] : '')."' AND fdig_regi>='".str_replace("-","",(isset($_POST['fech_desd']) ? $_POST['fech_desd'] : '')).'000000'."' AND fdig_regi<='".str_replace("-","",(isset($_POST['fech_hast']) ? $_POST['fech_hast'] : '')).'999999'."' order by fdig_regi desc limit $busc_limi_pagi,$busc_item_pagi");
	echo"<div style=\"width:90%;max-width:800px;margin:auto;\">";
	echo $html->put_title_demand("Bienes inventariados: $busc_tota_item ");
	if($busc_tota_pagi>0)
		echo $html->put_page("1",$_POST['busc_pagi_actu'],$busc_tota_pagi,"");
	$head=['1'=>"Nº",'2'=>"CÓDIGO",'3'=>"DESCRIPCIÓN",'4'=>"MARCA/MOD",'5'=>"USUARIO",'6'=>"FECHA"];
	echo $html->put_table_responsive_open();
	if($busc_tota_item)
	{
		echo $html->put_table_responsive_header($head);
		$cont=$busc_limi_pagi;
		foreach($result_pagi as $rows)
		{
			$cont++;
			$rows['fdig_regi']=substr($rows['fdig_regi'],6,2).'/'.substr($rows['fdig_regi'],4,2).'/'.substr($rows['fdig_regi'],0,4)." ".substr($rows['fdig_regi'],8,2).':'.substr($rows['fdig_regi'],10,2);
			$data=[	'1'=>$cont,
				'2'=>$rows['codigo_patrimonial']."<BR>B: ".$rows['codigo_barra'],
				'3'=>$rows['descripcion'],
				'4'=>"MARCA: ".$rows['marca']."<BR>MODELO: ".$rows['modelo']."<BR>SERIE: ".$rows['nro_serie'],
				'5'=>$rows['usuario'],
				'6'=>$rows['fdig_regi'],
			];
			    //'4'=>"<a href=\"javascript:f_ver('docu_".str_pad($rows['codi_docu'], 6, "0", STR_PAD_LEFT).".pdf')\"><img src=\"img/pdf_image.gif\" width=\"20\">",
			echo $html->put_table_responsive_data($head,$data);
		}
	}
	else
		echo $html->put_table_responsive_title("<font color=silver>No Existen Accesos");
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
                                                <button class=\"button_foot\" onclick=\"f_reporte()\">Recargar</button>
                                        </div>
                                        <div class=\"div_button_foot\"><center>
                                                <button class=\"button_foot\" onclick=\"f_descargar()\">Descargar Excel</button>
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
