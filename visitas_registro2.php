<?
//classes/TCPDF/examples/personal_fotocheck.php
	require_once 'include/cabecera.php';
	require_once 'classes/Html.class.php';
	require_once 'classes/Db.class.php';
	$Db = new Db();
	
	function dividirNombreCompleto($nombreCompleto)
	{
        // Eliminar espacios extra y dividir por espacios ok
        $partes = explode(' ', trim($nombreCompleto));
        $nombres = [];
        $apellidos = [];

        // Lógica para identificar nombres y apellidos (simplificada)
        if (count($partes) >= 3) { // Tiene al menos 2 apellidos y 1 nombre
            $apellidos[] = array_shift($partes); // Primero es apellido paterno
            $apellidos[] = array_shift($partes); // Segundo es apellido materno
            $nombres = $partes; // Resto son nombres
        } elseif (count($partes) == 2) { // Tiene un solo apellido
            $apellidos[] = array_shift($partes); // El primero es apellido
            $nombres = $partes; // El segundo es nombre
        } else { // Solo tiene un nombre o está vacío
            $nombres = $partes;
        }

        // Reordenar apellidos para el orden tradicional (Paterno, Materno) si hay dos
        if (count($apellidos) == 2) {
            $apellidoPaterno = $apellidos[0]; // El primer extraído
            $apellidoMaterno = $apellidos[1]; // El segundo extraído
        } else {
            $apellidoPaterno = $apellidos[0] ?? '';
            $apellidoMaterno = '';
        }

        return [
            'nomb' => implode(' ', $nombres),
            'appa' => $apellidoPaterno,
            'apma' => $apellidoMaterno
        ];
    }
	
	if(!isset($_POST['busc_pagi_actu']))
		$_POST['busc_pagi_actu']=1;

	if(!$_POST['codi_loca'])
	{
	    $result=$Db->query("select b.codi_loca,b.nom1_loca from mp_admi_depe=a, mp_admi_loca=b where a.codi_loca=b.codi_loca AND a.codi_depe='".$_SESSION['codi_depe']."'");
	    foreach($result as $rows)
	    {
	        $_POST['codi_loca']=$rows['codi_loca'];
	        $_POST['nomb_loca']=$rows['nom1_loca'];
	    }
	}
	
	$fech=date("Y-m-d");
	$fdig=date("YmdHis");
	$hora=date("H:i:s");

    if(!empty($_POST['ndoc_visi']) AND $_POST['tipo_regi']==2)  //si se debe guardar el formulario completo
    {
        //echo"<HR>Hola<HR>";
        if(!empty($_POST['iden_visi'])) //update
            $result=$Db->update("mp_visi_registro",['appa_visi'=>$_POST['appa_visi']??'','apma_visi'=>$_POST['apma_visi']??'','nomb_visi'=>$_POST['nomb_visi']??'','tipo_visi'=>(int)($_POST['tipo_visi']??4),'iden_depe'=>(int)($_POST['iden_depe']??0),'iden_pers'=>(int)($_POST['iden_pers']??0),'piso_visi'=>(int)($_POST['piso_visi']??0),'obse_visi'=>$_POST['obse_visi']??''],['iden_visi'=>$_POST['iden_visi']]);
        else
            $result=$Db->insert("mp_visi_registro",['tdoc_visi'=>(int)($_POST['tdoc_visi']??1),'ndoc_visi'=>$_POST['ndoc_visi']??'','nomb_visi'=>$_POST['nomb_visi']??'','appa_visi'=>$_POST['appa_visi']??'','apma_visi'=>$_POST['apma_visi']??'','tipo_visi'=>(int)($_POST['tipo_visi']??4),'fech_visi'=>$fech,'ingr_visi'=>$hora,'sali_visi'=>'00:00:00','iden_loca'=>(int)($_POST['codi_loca']??0),'iden_depe'=>(int)($_POST['iden_depe']??0),'iden_pers'=>(int)($_POST['iden_pers']??0),'piso_visi'=>(int)($_POST['piso_visi']??0),'iden_empr'=>(int)($_POST['iden_empr']??0),'obse_visi'=>$_POST['obse_visi']??'','digi_visi'=>(int)($_SESSION['iden_oper']??0),'fdig_visi'=>$fdig,'esta_visi'=>'1']);
        unset($_POST['ndoc_visi'],$_POST['iden_visi']);
    }
	elseif(!empty($_POST['ndoc_visi']) AND $_POST['regi_visi'])	//guardar
	{
	    //TIPO
	    //1 TRABAJADOR
	    //2 SECIGRA
	    //3 VOLUNTARIO
	    //4 VISITANTE
	    
	    //BUSCAMOS SU ULTIMA VISITA
	    $result=$Db->query("select * from mp_visi_registro where ndoc_visi='".$_POST['ndoc_visi']."' AND iden_loca='".$_POST['codi_loca']."' AND esta_visi>0 ORDER BY fdig_visi desc limit 1");
	    foreach($result as $rows)
	    {
	        $_POST['iden_visi']=$rows['iden_visi'];
	        
	        $_POST['tdoc_visi']=$rows['tdoc_visi'];
	        $_POST['ndoc_visi']=$rows['ndoc_visi'];
	        $_POST['nomb_visi']=$rows['nomb_visi'];
	        $_POST['appa_visi']=$rows['appa_visi'];
	        $_POST['apma_visi']=$rows['apma_visi'];
	        $_POST['tipo_visi']=$rows['tipo_visi'];
	        $_POST['codi_loca']=$rows['iden_loca'];
	        $_POST['iden_depe']=$rows['iden_depe'];
	        $_POST['iden_pers']=$rows['iden_pers'];
	        $_POST['piso_visi']=$rows['piso_visi'];
	        $_POST['iden_empr']=$rows['iden_empr'];
	        $_POST['obse_visi']=$rows['obse_visi'];

	        $_POST['fech_visi']=$rows['fech_visi'];
	        $_POST['ingr_visi']=$rows['ingr_visi'];
	        $_POST['sali_visi']=$rows['sali_visi'];
        }
        
        if($_POST['iden_visi']>0)   //SI YA TIENE UNA VISITA ANTERIOR
        {
            if($_POST['fech_visi']==$fech AND $_POST['sali_visi']=='00:00:00')  //SI SU ULTIMA VISITA ES HOY Y NO TIENE REGISTRO DE SALIDA
            {
                $result=$Db->update("mp_visi_registro",['sali_visi'=>$hora,'esta_visi'=>'2'],['iden_visi'=>$_POST['iden_visi']]);   //REGISTAMOS SU SALIDA
                $mensaje="REGISTRO DE SALIDA EXITOSO [DNI: ".$_POST['ndoc_visi']." ".$_POST['appa_visi']." ".$_POST['apma_visi'].", ".$_POST['nomb_visi']."]";
                unset($_POST['ndoc_visi'],$_POST['iden_visi']);
            }
            else    //SINO... REGISTRAMOS SU INGRESO CON LOS MISMOS DATOS DE SU ULTIMA VISITA
            {
                $result=$Db->insert("mp_visi_registro",['tdoc_visi'=>(int)($_POST['tdoc_visi']??1),'ndoc_visi'=>$_POST['ndoc_visi']??'','nomb_visi'=>$_POST['nomb_visi']??'','appa_visi'=>$_POST['appa_visi']??'','apma_visi'=>$_POST['apma_visi']??'','tipo_visi'=>(int)($_POST['tipo_visi']??4),'fech_visi'=>$fech,'ingr_visi'=>$hora,'sali_visi'=>'00:00:00','iden_loca'=>(int)($_POST['codi_loca']??0),'iden_depe'=>(int)($_POST['iden_depe']??0),'iden_pers'=>(int)($_POST['iden_pers']??0),'piso_visi'=>(int)($_POST['piso_visi']??0),'iden_empr'=>(int)($_POST['iden_empr']??0),'obse_visi'=>$_POST['obse_visi']??'','digi_visi'=>(int)($_SESSION['iden_oper']??0),'fdig_visi'=>$fdig,'esta_visi'=>'1']);
                $mensaje="REGISTRO DE INGRESO EXITOSO [DNI: ".$_POST['ndoc_visi']." ".$_POST['appa_visi']." ".$_POST['apma_visi'].", ".$_POST['nomb_visi']."]";
                unset($_POST['ndoc_visi'],$_POST['iden_visi']);
            }
        }
        else    //SINO... BUSCAMOS SI EXISTE
        {
            $_POST['tipo_visi']=0;

            // PRIORIDAD 1: Buscar en mp_visitantes (Visitantes externos - tipo 4)
            try {
                $result=$Db->query("select * from mp_visitantes where ndoc_visi='".$_POST['ndoc_visi']."' AND esta_visi=1");
                foreach($result as $rows)
                {
                    $_POST['iden_pers']=$rows['codi_pers'];
                    $_POST['appa_visi']=$rows['appa_visi'];
                    $_POST['apma_visi']=$rows['apma_visi'];
                    $_POST['nomb_visi']=$rows['nomb_visi'];
                    $_POST['iden_depe']=$rows['codi_depe'];
                    $_POST['tipo_visi']=4;  //TIPO VISITANTE EXTERNO
                }
            } catch (Exception $e) {}

            // PRIORIDAD 2: Buscar en mp_admi_pers (Trabajadores - tipo 1)
            if($_POST['tipo_visi']==0)
            {
                try {
                    $result=$Db->query("select * from mp_admi_pers where ndoc_pers='".$_POST['ndoc_visi']."'");
                    foreach($result as $rows)
                    {
                        $_POST['iden_pers']=$rows['iden_pers'];
                        $_POST['appa_visi']=$rows['appa_pers'];
                        $_POST['apma_visi']=$rows['apma_pers'];
                        $_POST['nomb_visi']=$rows['nomb_pers'];
                        $_POST['iden_depe']=$rows['iden_depe'];
                        $_POST['tipo_visi']=1;  //TIPO TRABAJADOR
                    }
                } catch (Exception $e) {}
            }

            // PRIORIDAD 3: Buscar en mp_fotocheck_secigra (SECIGRA - tipo 2)
            if($_POST['tipo_visi']==0)
            {
                try {
                    $result=$Db->query("select * from mp_fotocheck_secigra where ndni_pers='".$_POST['ndoc_visi']."'");
                    foreach($result as $rows)
                    {
                        $nomb = dividirNombreCompleto($rows['appe_pers']." ".$rows['nomb_pers']);
                        $_POST['iden_pers']=$rows['codi_pers'];
                        $_POST['appa_visi']=$nomb['appa'];
                        $_POST['apma_visi']=$nomb['apma'];
                        $_POST['nomb_visi']=$nomb['nomb'];
                        $_POST['tipo_visi']=2;  //TIPO SECIGRISTA
                    }
                } catch (Exception $e) {}
            }

            // PRIORIDAD 4: Buscar en mp_voluntariado (Voluntarios - tipo 3)
            if($_POST['tipo_visi']==0)
            {
                try {
                    $result=$Db->query("select * from mp_voluntariado where docu_volu='".$_POST['ndoc_visi']."'");
                    foreach($result as $rows)
                    {
                        $nomb = dividirNombreCompleto($rows['nomb_volu']);
                        $_POST['iden_pers']=$rows['codi_volu'];
                        $_POST['appa_visi']=$nomb['appa'];
                        $_POST['apma_visi']=$nomb['apma'];
                        $_POST['nomb_visi']=$nomb['nomb'];
                        $_POST['tipo_visi']=3;  //TIPO VOLUNTARIO
                    }
                } catch (Exception $e) {}
            }

            // SI SE ENCONTRÓ EN ALGUNA TABLA → registrar ingreso automáticamente
            if($_POST['tipo_visi']>0)
            {
                $result=$Db->insert("mp_visi_registro",['tdoc_visi'=>(int)($_POST['tdoc_visi']??1),'ndoc_visi'=>$_POST['ndoc_visi']??'','nomb_visi'=>$_POST['nomb_visi']??'','appa_visi'=>$_POST['appa_visi']??'','apma_visi'=>$_POST['apma_visi']??'','tipo_visi'=>(int)($_POST['tipo_visi']??4),'fech_visi'=>$fech,'ingr_visi'=>$hora,'sali_visi'=>'00:00:00','iden_loca'=>(int)($_POST['codi_loca']??0),'iden_depe'=>(int)($_POST['iden_depe']??0),'iden_pers'=>(int)($_POST['iden_pers']??0),'piso_visi'=>(int)($_POST['piso_visi']??0),'iden_empr'=>(int)($_POST['iden_empr']??0),'obse_visi'=>$_POST['obse_visi']??'','digi_visi'=>(int)($_SESSION['iden_oper']??0),'fdig_visi'=>$fdig,'esta_visi'=>'1']);
                $mensaje="REGISTRO DE INGRESO EXITOSO [DNI: ".$_POST['ndoc_visi']." ".$_POST['appa_visi']." ".$_POST['apma_visi'].", ".$_POST['nomb_visi']."]";
                unset($_POST['ndoc_visi']);
            }
            else
            {
                // DNI DESCONOCIDO: mostrar formulario con tipo=4 (Visitante) por defecto
                // NO se hace unset de ndoc_visi para que el formulario aparezca pre-llenado con el DNI
                $_POST['tipo_visi']=4;  // Pre-seleccionar "Visitante"
                // Dejar nombre, dependencia y pers vacíos para que el operador los complete
                
                // === INTEGRACION API DNI (decolecta.com) ===
                // 1. Registrate en https://decolecta.com/
                // 2. Copia tu Token y pegalo aquí abajo:
                $token_api = 'sk_15268.lJWDajZuv2HfxesE71nIbtyI79VblYVC';
                
                if (strlen($_POST['ndoc_visi']) == 8 && $token_api != '') {
                    if (!function_exists('curl_init')) {
                        $mensaje = "ERROR: La extensión CURL no está habilitada en su hosting.";
                    } else {
                        $curl = curl_init();
                        curl_setopt_array($curl, array(
                          CURLOPT_URL => 'https://api.decolecta.com/v1/reniec/dni?numero=' . $_POST['ndoc_visi'],
                          CURLOPT_RETURNTRANSFER => true,
                          CURLOPT_SSL_VERIFYPEER => 0,
                          CURLOPT_ENCODING => '',
                          CURLOPT_MAXREDIRS => 2,
                          CURLOPT_TIMEOUT => 0,
                          CURLOPT_FOLLOWLOCATION => true,
                          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                          CURLOPT_CUSTOMREQUEST => 'GET',
                          CURLOPT_HTTPHEADER => array(
                            'Content-Type: application/json',
                            'Authorization: Bearer ' . $token_api
                          ),
                        ));
                        
                        $response = curl_exec($curl);
                        $err = curl_error($curl);
                        curl_close($curl);
                        
                        $persona = json_decode($response);
                        
                        if(isset($persona->first_name)) {
                            $_POST['nomb_visi'] = $persona->first_name;
                            $_POST['appa_visi'] = $persona->first_last_name;
                            $_POST['apma_visi'] = $persona->second_last_name;
                            
                            $mensaje = "DATOS OBTENIDOS DE RENIEC CORRECTAMENTE";
                        } else {
                            if ($err) {
                                $mensaje = "Error de conexión (CURL): " . $err;
                            } else {
                                $mensaje = "Error API RENIEC: " . $response;
                            }
                        }
                    }
                }
                // =========================================
            }
        }
	}
	
	if(!empty($mensaje))
	{
	    echo"<script>
	            parent.document.getElementById('header').style.background='#45BE00';
	            parent.document.getElementById('div-mensajes').innerHTML = '$mensaje';
	            setTimeout(function(){
                    parent.document.getElementById('div-mensajes').innerHTML ='';
                    parent.document.getElementById('header').style.background='#073A6B';
                }, 6000);
	    </script>";
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
		
		<style>
    .tabla2 {
      width: 1%;
      border-collapse: separate;
    }
    .tabla2 th, .tabla2 td {
      border: 0px solid black;
      padding: 1px;
      text-align: center;
    }
    /* Columnas cortas: no partir nunca */
    .table_responsive th:nth-child(1), .table_responsive td:nth-child(1),
    .table_responsive th:nth-child(2), .table_responsive td:nth-child(2),
    .table_responsive th:nth-child(4), .table_responsive td:nth-child(4),
    .table_responsive th:nth-child(5), .table_responsive td:nth-child(5),
    .table_responsive th:nth-child(8), .table_responsive td:nth-child(8)
    { white-space: nowrap; }
    /* Columnas de texto: pueden partir, con espacio mínimo */
    .table_responsive th:nth-child(3), .table_responsive td:nth-child(3) { min-width: 110px; }
    .table_responsive th:nth-child(6), .table_responsive td:nth-child(6) { min-width: 110px; }
    .table_responsive th:nth-child(7), .table_responsive td:nth-child(7) { min-width: 110px; }
    .table_responsive th:nth-child(8), .table_responsive td:nth-child(8) { text-align: center; }
  </style>
		
		<script>
		    function f_mensaje(mens)
		    {
		        parent.document.getElementById('div-mensajes').innerHTML = mens;
		    }
			function f_registrar(tipo)
			{
			    if(document.form.ndoc_visi.value=='')
			    {
			        alert('Ingrese Documento de Identidad');
			        //swal("Oops!", "Something went wrong on the page!", "error");
			        document.form.ndoc_visi.focus();
			        return false;
			    }
			    else
			    {
			        document.form.regi_visi.value='1';
			        document.form.tipo_regi.value=tipo;
				    document.form.action='';
				    document.form.target="";
				    document.form.submit();
			    }
			}
			function f_editar(codi)
			{
			    document.form.iden_visi.value=codi;
			    document.form.tipo_regi.value='2';
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

			// Auto-focus en el campo DNI para uso con lector de barras
			function f_focus_ndoc(delay)
			{
			    var ms = delay || 100;
			    setTimeout(function(){
			        var campo = document.getElementById('ndoc_visi');
			        if(campo) { campo.focus(); campo.select(); }
			    }, ms);
			}
			window.onload = function() { f_focus_ndoc(200); };
		</script>

	</head>
	<body style="margin-bottom: 30px;">
	    
	<center><h3 style="color:#073A6B"><b>REGISTRO DE VISITAS <BR>[SEDE: <?=$_POST['nomb_loca']?>] [FECHA: <?=$fech?>]</b></h3></center>
		<form name="form" method="post">
			<input type=hidden name="regi_visi">
			<input type=hidden name="tipo_regi">
			<input type=hidden name="busc_pagi_actu" value="<?=$_POST['busc_pagi_actu']?>">
			<input type=hidden name="codi_loca" value="<?=$_POST['codi_loca']?>">
			<input type=hidden name="nomb_loca" value="<?=$_POST['nomb_loca']?>">
			<input type=hidden name="iden_visi">
<?
	$html=new htmlclass;
	
	$arra_options_tvis['0']="<- Seleccione Tipo ->";
	$result=$Db->select('mp_maes_visi_tipo', '', '', '', ['x_nombre'=>'ASC']);
	foreach($result as $rows)
		$arra_options_tvis[$rows['n_codigo']]=$rows['x_nombre'];
		
	$result=$Db->select('mp_maes_tdocumento', '', '', '', ['n_codigo'=>'ASC']);
	foreach($result as $rows)
		$arra_options_tdoc[$rows['n_codigo']]=$rows['x_nombre'];
	
	$arra_options_depe_nomb[0]="<- Seleccione Dependencia ->";
	$result=$Db->query("select * from mp_admi_depe where codi_loca='".$_POST['codi_loca']."' AND esta_depe=1 order by nomb_depe");
	foreach($result as $rows)
	{
		$arra_options_depe_sigl[$rows['codi_depe']]=$rows['sigl_depe'];
		$arra_options_depe_nomb[$rows['codi_depe']]=$rows['nomb_depe'];
	}
	
	$arra_options_piso['0']="<- Seleccione Piso ->";
	$arra_options_piso[1]="1er Piso";
	$arra_options_piso[2]="2do Piso";
	$arra_options_piso[3]="3er Piso";
	$arra_options_piso[-1]="Sotano 1";
	$arra_options_piso[-2]="Sotano 2";
	
	if(!empty($_POST['ndoc_visi']) OR !empty($_POST['iden_visi']))     //SI ES QUE NO SE HA ENCONTRADO EL VISITANTE SE MUESTRA EL FORMULARIO DE REGISTRO
	{
	    if(!empty($_POST['iden_visi']))
	    {
	        echo"<input type=hidden name=\"iden_visi\" value=\"".$_POST['iden_visi']."\">";
	        $result=$Db->query("select * from mp_visi_registro where iden_visi='".$_POST['iden_visi']."'");
	        foreach($result as $rows)
	        {
	            $_POST['tdoc_visi']=$rows['tdoc_visi'];
	            $_POST['ndoc_visi']=$rows['ndoc_visi'];
	            $_POST['nomb_visi']=$rows['nomb_visi'];
    	        $_POST['appa_visi']=$rows['appa_visi'];
	            $_POST['apma_visi']=$rows['apma_visi'];
	            $_POST['tipo_visi']=$rows['tipo_visi'];
    	        $_POST['iden_depe']=$rows['iden_depe'];
	            $_POST['iden_pers']=$rows['iden_pers'];
	            $_POST['piso_visi']=$rows['piso_visi'];
    	        $_POST['obse_visi']=$rows['obse_visi'];
            }
	    }	    
	    
	    $arra_options_pers['0']="<- Seleccione Personal ->";
	    $result=$Db->query("select iden_pers,ndoc_pers,appa_pers,apma_pers,nomb_pers from mp_admi_pers=a,mp_admi_depe=b where a.iden_depe=b.codi_depe AND codi_loca='".$_POST['codi_loca']."' AND esta_depe=1 order by appa_pers,apma_pers");
	    foreach($result as $rows)
	        $arra_options_pers[$rows['iden_pers']]=$rows['appa_pers']." ".$rows['apma_pers'].", ".$rows['nomb_pers'];
	    
	    if (empty($_POST['iden_visi'])) {
            $_POST['tdoc_visi'] = $_POST['tdoc_visi'] ?? 1;
            $_POST['appa_visi'] = $_POST['appa_visi'] ?? '';
            $_POST['apma_visi'] = $_POST['apma_visi'] ?? '';
            $_POST['nomb_visi'] = $_POST['nomb_visi'] ?? '';
            $_POST['tipo_visi'] = $_POST['tipo_visi'] ?? 4;
            $_POST['iden_depe'] = $_POST['iden_depe'] ?? 0;
            $_POST['iden_pers'] = $_POST['iden_pers'] ?? 0;
            $_POST['piso_visi'] = $_POST['piso_visi'] ?? 0;
            $_POST['obse_visi'] = $_POST['obse_visi'] ?? '';
        }

	    echo"<main>";
	    echo $html->put_select("Tipo&nbsp;de&nbsp;Documento",'tdoc_visi',$arra_options_tdoc,$_POST['tdoc_visi'],"");
	    echo $html->put_text('text',"Nro.&nbsp;Documento","Ingrese Nro.",'ndoc_visi',$_POST['ndoc_visi'],'','15','');
	    echo"</main><main>";
	    echo $html->put_text('text',"Apellido&nbsp;Paterno","Ingrese Ap. Paterno",'appa_visi',$_POST['appa_visi'],'','50','');
	    echo $html->put_text('text',"Apellido&nbsp;Materno","Ingrese Ap. Materno",'apma_visi',$_POST['apma_visi'],'','50','');
	    echo $html->put_text('text',"Nombres","Ingrese Nombres",'nomb_visi',$_POST['nomb_visi'],'','50','');
	    echo"</main><main>";
	    echo $html->put_select("Tipo",'tipo_visi',$arra_options_tvis,$_POST['tipo_visi'],"");
	    echo $html->put_select_buscador("Dependencia",'iden_depe',$arra_options_depe_nomb,$_POST['iden_depe'],"");
	    echo $html->put_select_buscador("Autoriza",'iden_pers',$arra_options_pers,$_POST['iden_pers'],"");
	    echo"</main><main>";
	    echo $html->put_select("Piso",'piso_visi',$arra_options_piso,$_POST['piso_visi'],"");
	    echo $html->put_textarea("Observaciones",'obse_visi',$_POST['obse_visi'],'');
	    echo $html->put_button_colum("&nbsp;","Registrar Ingreso &raquo;","return f_registrar('2')");
	}
	else
	{
    	echo"<main>";
    	echo"<input type=hidden name=\"tdoc_visi\" value=\"1\">"; // DNI por defecto
    	echo $html->put_text('text',"Nro.&nbsp;Documento","Ingrese Nro.",'ndoc_visi','','','15','');
    	echo $html->put_button_colum("&nbsp;","Registrar Ingreso &raquo;","return f_registrar('1')");
    	echo"</main>";

    	echo"</div>";
	
	
	
	
    	$busc_item_pagi=20;      //cantidad de items por pagina
	
    	$result=$Db->query("select count(*) tota from mp_visi_registro where fech_visi='$fech' AND esta_visi>0");
    	$busc_tota_item=0;
    	foreach($result as $rows)
    	{       
		    $busc_tota_item=$rows['tota'];
	    }

	    $busc_tota_pagi=ceil($busc_tota_item/$busc_item_pagi);
	    $busc_limi_pagi=($_POST['busc_pagi_actu']-1)*$busc_item_pagi;

	    $result_pagi=$Db->query("select * from mp_visi_registro where fech_visi='$fech' AND esta_visi>0 order by iden_visi desc limit $busc_limi_pagi,$busc_item_pagi");

	    // Cargar nombres del personal para columna AUTORIZA
	    $arra_options_pers_nomb = [];
	    $result_pers=$Db->query("select iden_pers,appa_pers,apma_pers,nomb_pers from mp_admi_pers=a,mp_admi_depe=b where a.iden_depe=b.codi_depe AND codi_loca='".$_POST['codi_loca']."' AND esta_depe=1");
	    foreach($result_pers as $rp)
	        $arra_options_pers_nomb[$rp['iden_pers']] = $rp['appa_pers'].' '.$rp['apma_pers'].', '.$rp['nomb_pers'];

	    echo"<div style=\"width:90%;max-width:800px;margin:0 auto;\">";
	    echo $html->put_title_demand("CANTIDAD DE VISITAS: $busc_tota_item VISITANTES");

	    if($busc_tota_pagi>0  OR 5==5)
    		echo $html->put_page("1",$_POST['busc_pagi_actu'],$busc_tota_pagi,"");
    	$head=['1'=>"Nº",'2'=>"DOCUMENTO",'3'=>"NOMBRES",'4'=>"TIPO",'5'=>"INGRESO<BR>SALIDA",'6'=>"DESTINO",'7'=>"AUTORIZA",'8'=>"EDIT"];
    	echo $html->put_table_responsive_open();
    	if($busc_tota_item OR 5==5)
    	{
		    echo $html->put_table_responsive_header($head);
		    $cont=$busc_limi_pagi;
    		foreach($result_pagi as $rows)
	    	{
		    	$cont++;
		    	$data=[	'1'=>$cont,
			    	'2'=>$rows['ndoc_visi'],
    				'3'=>utf8_encode(utf8_decode(strtoupper($rows['appa_visi'].' '.$rows['apma_visi'].',<BR>'.$rows['nomb_visi']))),
	    			'4'=>$arra_options_tvis[$rows['tipo_visi']],
		    		'5'=>"<table class=\"tabla2\"><tr><td><img src=\"img/icons/download.svg\" width=\"20\"></td><td>".$rows['ingr_visi']."</td></tr><tr><td><img src=\"img/icons/upload.svg\" width=\"20\"></td><td>".$rows['sali_visi']."</td></tr></table>",
			    	'6'=>$arra_options_depe_sigl[$rows['iden_depe']],
				    '7'=>utf8_encode(utf8_decode(
                                isset($arra_options_pers_nomb[$rows['iden_pers']])
                                    ? strtoupper($arra_options_pers_nomb[$rows['iden_pers']])
                                    : '-'
                            )),
				    '8'=>"<a href=\"javascript:f_editar(".$rows['iden_visi'].")\" ><img src=\"img/icons/edit.svg\" width=\"20\">",
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
	}
	
	
	
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
    <script>
        // Listener para lector de barras: Enter registra automáticamente
        var campoDni = document.getElementById('ndoc_visi');
        if(campoDni) {
            campoDni.addEventListener('keydown', function(event) {
                if (event.key === 'Enter') {
                    event.preventDefault();
                    f_registrar(1);
                }
            });
        }
    </script>
	</form>
	</body>
</html>
