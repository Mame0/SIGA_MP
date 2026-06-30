<?
//classes/TCPDF/examples/personal_fotocheck.php
	require_once 'include/cabecera.php';
	require_once 'classes/Html.class.php';
	require_once 'classes/Db.class.php';
	$Db = new Db();
	if(!isset($_POST['busc_pagi_actu']))
		$_POST['busc_pagi_actu']=1;
	
	$fdig=date("YmdHis");
	
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


    if(isset($_POST['codi_loca_repo']))
	{
	    $result=$Db->query("select * from mp_admi_loca where codi_loca='".$_POST['codi_loca_repo']."'");
	    $flag_usua=0;
	    foreach($result as $rows)
	    {
	        $flag_usua++;
	        $_POST['loca_inve']=$rows['codi_loca'];
	        $_POST['loca_dato']="<BR><u>LOCAL</u>: ".$rows['nom1_loca'];
	        $_POST['loca_lati']=$rows['lati_loca'];
	        $_POST['loca_long']=$rows['long_loca'];
	    }
	}
	
	if(isset($_POST['codi_depe_repo']))
	{
	    $result=$Db->query("select * from mp_admi_depe where codi_depe='".$_POST['codi_depe_repo']."'");
	    $flag_usua=0;
	    foreach($result as $rows)
	    {
	        $flag_usua++;
	        $_POST['nomb_depe']=$rows['nomb_depe'];
	        $_POST['depe_dato']="<BR><u>DEPENDENCIA</u>: ".$rows['nomb_depe'];
	    }
	}

    if(isset($_POST['usua_inve_repo']))
	{
	    $result=$Db->query("select * from mp_admi_pers where iden_pers='".$_POST['usua_inve_repo']."'");
	    $flag_usua=0;
	    foreach($result as $rows)
	    {
	        $flag_usua++;
	        $_POST['nomb_pers']="[".$rows['ndoc_pers']."] ".$rows['appa_pers']." ".$rows['apma_pers'].", ".$rows['nomb_pers'];
	        $_POST['pers_dato']="<BR><u>USUARIO</u>: ".$_POST['nomb_pers'];
	        $ndoc_pers_sele=$rows['ndoc_pers'];
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
			function f_recargar()
			{
				document.form.submit();
			}
			function f_consolidar()
			{
			    document.form.action='patrimonio_inventario_local_consolidar.php';
				document.form.submit();
			}
			function f_buscar_avanzado()
			{
			    document.form.action='patrimonio_buscar_bienes.php';
				document.form.submit();
			}
			function f_cambiar_local()
			{
			    document.form.loca_inve.value='';
			    document.form.loca_dato.value='';
			    document.form.action='';
				document.form.target="";
				document.form.submit();
			}
			function f_nueva_busqueda()
			{
			    document.form.codi_patr.value='';
			    //document.form.codi_patr_regi.value='';
				document.form.submit();
			}
			function f_cambiar_orden(orde)
			{
			    document.form.orde_inve.value=orde;
				document.form.action='';
				document.form.target="";
				document.form.submit();
			}
			function f_eliminar(codi,patr,barr,nomb)
			{
			    if(confirm('Seguro que desea eliminar inventario de: \n '+nomb))
			    {
			        document.form.elim_inve.value=codi;
			        document.form.submit();
			    }
			    //else
			     //   swal("Oops!", "Something went wrong on the page!", "error");
			}
			function f_registrar(codi)
			{
			    document.form.codi_patr.value=codi;
			    //document.form.codi_patr_regi.value=codi;
			    document.form.submit();
			}
			function f_registrar_repetido(codi)
			{
			    if(confirm('Bien ya fue inventariado\nDesea volver a registrarlo?'))
			    {
			        document.form.codi_patr.value=codi;
			        document.form.submit();
			    }
			    else
			        return false
			}
			
			function f_observacion(codi,obse)
			{
			    nuev_obse=window.prompt('Ingrese Observacion',obse);
			    if(nuev_obse)
			    {
			        document.form.obse_inve.value=nuev_obse;
			        document.form.codi_regi.value=codi;
			        document.form.submit();
			    }
			    else
			        return false;
			}
			function f_detalle(loca,depe,usua)
			{
			    document.form.codi_loca_repo.value=loca;
			    document.form.codi_depe_repo.value=depe;
			    document.form.usua_inve_repo.value=usua;
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
	    
	<center><h4 style="color:#073A6B"><b>REPORTE DE INVENTARIO<BR>[<?=(isset($_POST['fech_inve'])?$_POST['fech_inve']:'')?>] <?=(isset($_POST['nomb_inve'])?$_POST['nomb_inve']:'')?> <?=(isset($_POST['loca_dato'])?$_POST['loca_dato']:'')?> <?=(isset($_POST['depe_dato'])?$_POST['depe_dato']:'')?> <?=(isset($_POST['pers_dato'])?$_POST['pers_dato']:'')?></h4></b></center>
		<form name="form" method="post">
			<input type=hidden name="regi_inve">
			<input type=hidden name="lati_inve">
			<input type=hidden name="long_inve">
			<input type=hidden name="elim_inve">
			<input type=hidden name="obse_inve">
			<input type=hidden name="codi_regi">
			<input type=hidden name="usua_inve_repo">
			<input type=hidden name="codi_loca_repo">
			<input type=hidden name="codi_depe_repo">
			<input type=hidden name="busc_pagi_actu" value="<?=$_POST['busc_pagi_actu']?>">
			<input type=hidden name="codi_inve" value="<?=(isset($_POST['codi_inve'])?$_POST['codi_inve']:'')?>">
			<input type=hidden name="fech_inve" value="<?=(isset($_POST['fech_inve'])?$_POST['fech_inve']:'')?>">
			<input type=hidden name="nomb_inve" value="<?=(isset($_POST['nomb_inve'])?$_POST['nomb_inve']:'')?>">
			<input type=hidden name="loca_inve" value="<?=(isset($_POST['loca_inve'])?$_POST['loca_inve']:'')?>">
			<input type=hidden name="loca_dato" value="<?=(isset($_POST['loca_dato'])?$_POST['loca_dato']:'')?>">
			<input type=hidden name="loca_lati" value="<?=(isset($_POST['loca_lati'])?$_POST['loca_lati']:'')?>">
			<input type=hidden name="loca_long" value="<?=(isset($_POST['loca_long'])?$_POST['loca_long']:'')?>">
			<input type=hidden name="ulti_lect" value="<?=(isset($_POST['codi_patr'])?$_POST['codi_patr']:'')?>">
			<input type=hidden name="orde_inve" value="<?=(isset($_POST['orde_inve'])?$_POST['orde_inve']:'')?>">
<?
	$html=new htmlclass;
	
	   	//OBTENER PERSONAL
        $arra_options_pers[]="<- Seleccione Usuario ->";
        $result=$Db->query("select * from mp_admi_pers as a, mp_inve_regi as b where a.iden_pers=b.usua_inve AND codi_inve='".$_POST['codi_inve']."' AND esta_pers='1' order by appa_pers,apma_pers,nomb_pers");
    	foreach($result as $rows)
    	{
    	    $arra_options_pers[$rows['ndoc_pers']]=" [".$rows['ndoc_pers']."] ".$rows['appa_pers']." ".$rows['apma_pers'].", ".$rows['nomb_pers'];
    	    $arra_options_per2[$rows['iden_pers']]=" [".$rows['ndoc_pers']."] ".$rows['appa_pers']." ".$rows['apma_pers'].", ".$rows['nomb_pers'];
    	}
    	//FIN OBTENER PERSONAL
	
        //OBTENER LOCAL
        $arra_options_loca[]="<- Seleccione Local ->";
        $result=$Db->query("select * from mp_admi_loca as a, mp_inve_regi as b where a.codi_loca=b.codi_loca AND codi_inve='".$_POST['codi_inve']."' AND esta_loca='1' order by nom1_loca");
    	foreach($result as $rows)
    	    $arra_options_loca[$rows['codi_loca']]=$rows['nom1_loca']." [".$rows['dire_loca']."]";
    	//FIN OBTENER LOCAL
	
    	//OBTENER DEPENDENCIA
        $arra_options_depe[]="<- Seleccione Dependencia ->";
        $result=$Db->query("select * from mp_admi_depe as a, mp_inve_regi as b where a.codi_depe=b.codi_depe AND codi_inve='".$_POST['codi_inve']."' AND esta_depe='1' order by nomb_depe");
    	foreach($result as $rows)
    	    $arra_options_depe[$rows['codi_depe']]=$rows['nomb_depe'];
    	//FIN OBTENER DEPENDENCIA
    	
    	$arra_options_tipo['0']='<- Seleccione ->';
    	$arra_options_tipo['1']='Resúmen General';
    	$arra_options_tipo['2']='Reporte por Local';
    	$arra_options_tipo['3']='Reporte por Dependencia';
    	$arra_options_tipo['4']='Reporte por Usuario';
    	$arra_options_tipo['5']='Reporte por Inventariador';

        echo"<main>";
        echo $html->put_select_buscador("Tipo",'tipo_repo',$arra_options_tipo,(isset($_POST['tipo_repo'])?$_POST['tipo_repo']:'')," onchange=\"document.form.submit()\"");
        //echo $html->put_select_buscador("Local",'loca_inve_busc',$arra_options_loca,$_POST['loca_inve_busc']," onchange=\"document.form.submit()\"");
        //echo $html->put_select_buscador(CONST_SUBTITLE_DEPENDENCIA,'codi_depe',$arra_options_depe,$_POST['codi_depe'],"onchange=\"document.form.submit()\"");
        //echo $html->put_select_buscador('Usuario','codi_pers',$arra_options_pers,$_POST['codi_pers'],"onchange=\"document.form.submit()\"");
        echo"</main>";

    //if($_POST['codi_pers'] OR $_POST['codi_depe'] OR $_POST['loca_inve_busc'])
    if(isset($_POST['tipo_repo']) && $_POST['tipo_repo'] OR 5==5)
    {
    	$busc_item_pagi=50;      //cantidad de items por pagina
        
        $sql = "";
        if(isset($_POST['usua_inve_repo']) && $_POST['usua_inve_repo'])
            $sql="select * from mp_inve_view_regi where codi_inve='".$_POST['codi_inve']."' AND codi_loca='".$_POST['codi_loca_repo']."' AND codi_depe='".$_POST['codi_depe_repo']."' AND usua_inve='".$_POST['usua_inve_repo']."'";
        else
        {
            if(isset($_POST['tipo_repo'])) {
                switch($_POST['tipo_repo'])
                {
                    case 1: $sql="select codi_loca,codi_depe,usua_inve,count(*) as cant from mp_inve_view_regi where codi_inve='".$_POST['codi_inve']."' group by codi_loca,codi_depe,usua_inve";   break;
                    case 2: $sql="select codi_loca,count(*) as cant from mp_inve_view_regi where codi_inve='".$_POST['codi_inve']."' group by codi_loca";   break;
                    case 3: $sql="select codi_loca,codi_depe,count(*) as cant from mp_inve_view_regi where codi_inve='".$_POST['codi_inve']."' group by codi_loca,codi_depe";   break;
                    case 4: $sql="select codi_loca,codi_depe,usua_inve,count(*) as cant from mp_inve_view_regi where codi_inve='".$_POST['codi_inve']."' group by codi_loca,codi_depe,usua_inve";   break;
                    case 5: $sql="select digi_regi,SUBSTRING(fdig_regi,1,6) as fech,count(*) as cant from mp_inve_view_regi where codi_inve='".$_POST['codi_inve']."' group by digi_regi,fech";   break;
                }
            }
        }
        
        if($sql) {
            $result=$Db->query($sql);
        } else {
            $result = [];
        }
    	//$result=$Db->query("select codi_loca,codi_depe,usua_inve,count(*) from mp_inve_view_regi where codi_inve='".$_POST['codi_inve']."' group by codi_loca,codi_depe,usua_inve");
    	$busc_tota_item=0;
    	foreach($result as $rows)
    	{       
    		$busc_tota_item++;
    	}

        	$busc_tota_pagi=ceil($busc_tota_item/$busc_item_pagi);
        	$busc_limi_pagi=($_POST['busc_pagi_actu']-1)*$busc_item_pagi;

        	$result_pagi=$Db->query("$sql limit $busc_limi_pagi,$busc_item_pagi");

    	    echo"<div style=\"width:90%;max-width:800px;margin:auto;\">";
    	    //echo $html->put_title_demand("BIENES ASIGNADOS: $busc_tota_item BIENES");

        	if($busc_tota_pagi>0  OR 5==5)
        		echo $html->put_page("1",$_POST['busc_pagi_actu'],$busc_tota_pagi,"");
        		
        	if(isset($_POST['usua_inve_repo']) && $_POST['usua_inve_repo'])
    	        $head=['1'=>"Nº",'2'=>"CODIGO",'3'=>"DESCRIPCIÓN",'4'=>"MARCA/MOD",'6'=>"USUARIO",'7'=>"FECHA"];
    	    else
    	    {
    	        if(isset($_POST['tipo_repo'])) {
                    switch($_POST['tipo_repo'])
                    {
                        case 1: $head=['1'=>"Nº",'2'=>"LOCAL",'3'=>"DEPENDENCIA",'4'=>"USUARIO",'5'=>"BIENES"]; break;
                        case 2: $head=['1'=>"Nº",'2'=>"LOCAL",'5'=>"BIENES"]; break;
                        case 3: $head=['1'=>"Nº",'2'=>"LOCAL",'3'=>"DEPENDENCIA",'5'=>"BIENES"]; break;
                        case 4: $head=['1'=>"Nº",'2'=>"LOCAL",'3'=>"DEPENDENCIA",'4'=>"USUARIO",'5'=>"BIENES"]; break;
                        case 5: $head=['1'=>"Nº",'2'=>"INVENTARIADOR",'3'=>"FECHA",'5'=>"BIENES"]; break;
                    }
                }
    	    }
    	        //$head=['1'=>"Nº",'2'=>"LOCAL",'3'=>"DEPENDENCIA",'4'=>"USUARIO",'5'=>"BIENES"];
    	        
        	echo $html->put_table_responsive_open();
        	if($busc_tota_item OR 5==5)
    	    {
        		echo $html->put_table_responsive_header($head);
        		$cont=$busc_limi_pagi;
    	    	foreach($result_pagi as $rows)
    		    {
        			$cont++;
        			
        			if(isset($_POST['usua_inve_repo']) && $_POST['usua_inve_repo'])
        			{
        			    $data=[	'1'=>$colo.$cont,
    				        '2'=>$rows['codigo_patrimonial']."<BR>B: ".$rows['codigo_barra'],
    				        '3'=>$rows['descripcion'],
    				        '4'=>"MARCA: ".$rows['marca']."<BR>MODELO: ".$rows['modelo']."<BR>SERIE: ".$rows['nro_serie'],
    				        
    				        '6'=>$rows['usuario'],
    				        '7'=>substr($rows['fdig_regi'],0,4)."/".substr($rows['fdig_regi'],4,2)."/".substr($rows['fdig_regi'],6,2),
        			    ];
        			}
        			else
        			{
        			    if(isset($_POST['tipo_repo'])) {
                            switch($_POST['tipo_repo'])
                            {
                                case 1: $data=[	'1'=>$cont,
                                        '2'=>$arra_options_loca[$rows['codi_loca']],
                                        '3'=>$arra_options_depe[$rows['codi_depe']],  
                                        '4'=>$arra_options_per2[$rows['usua_inve']],
                                        '5'=>"<a href=\"javascript:f_detalle('".$rows['codi_loca']."','".$rows['codi_depe']."','".$rows['usua_inve']."')\">".$rows['cant']."</a>",
                                    ];
                                    break;
                                case 2: $data=[	'1'=>$cont,
                                        '2'=>$arra_options_loca[$rows['codi_loca']],
                                        '5'=>"<a href=\"javascript:f_detalle('".$rows['codi_loca']."','".$rows['codi_depe']."','".$rows['usua_inve']."')\">".$rows['cant']."</a>",
                                    ];
                                    break;
                                case 3: $data=[	'1'=>$cont,
                                        '2'=>$arra_options_loca[$rows['codi_loca']],
                                        '3'=>$arra_options_depe[$rows['codi_depe']],  
                                        '5'=>"<a href=\"javascript:f_detalle('".$rows['codi_loca']."','".$rows['codi_depe']."','".$rows['usua_inve']."')\">".$rows['cant']."</a>",
                                    ];
                                    break;
                                case 4: $data=[	'1'=>$cont,
                                        '2'=>$arra_options_loca[$rows['codi_loca']],
                                        '3'=>$arra_options_depe[$rows['codi_depe']],  
                                        '4'=>$arra_options_per2[$rows['usua_inve']],
                                        '5'=>"<a href=\"javascript:f_detalle('".$rows['codi_loca']."','".$rows['codi_depe']."','".$rows['usua_inve']."')\">".$rows['cant']."</a>",
                                    ];
                                    break;
                                case 5: $data=[	'1'=>$cont,
                                        '2'=>$arra_options_loca[$rows['digi_regi']],
                                        '3'=>$arra_options_depe[$rows['fech']],  
                                        '5'=>"<a href=\"javascript:f_detalle('".$rows['codi_loca']."','".$rows['codi_depe']."','".$rows['usua_inve']."')\">".$rows['cant']."</a>",
                                    ];
                                    break;
                            }
                        }
        			}
    		    	echo $html->put_table_responsive_data($head,$data);
        		}
        	}
    	    else
    		    echo $html->put_table_responsive_title("Usuario no tiene bienes asignados");
		
        	echo $html->put_table_responsive_close();
        	if($busc_tota_pagi>0  OR 5==5)
    	    	echo $html->put_page("2",$_POST['busc_pagi_actu'],$busc_tota_pagi,"");
        	echo"</div>";
    }	
	
	if(isset($_POST['loca_inve']) && $_POST['loca_inve'] AND isset($_POST['codi_depe']) && $_POST['codi_depe'] AND isset($_POST['codi_pers']) && $_POST['codi_pers'] AND isset($_POST['codi_patr_regi']) && $_POST['codi_patr_regi'] AND isset($_POST['codi_patr']) && $_POST['codi_patr'])
	{
		echo $html->put_separator_demand("30");
		echo"
                        <div align=center class=\"foot\">
                                <center>
                                <div align=center class=\"foot2\">
                                        <div class=\"div_button_foot\" style=\"\">
                                            <button class=\"button_foot\" onclick=\"f_nueva_busqueda()\">Cancelar</button>
                                        </div>
                                        <div class=\"div_button_foot\"><center>
                                            <button class=\"button_foot\" onclick=\"return f_registro_inventario('".$_POST['codi_patr_regi']."')\">Registrar</button>
                                        </div>
                                </div>
                        </div>
        ";
	}
?>
<center>
    <script>
        function poner_focus()
        {
            //alert('Hola');
            document.form.codi_patr.focus();
        }
        document.form.codi_patr.focus();
        navigator.geolocation.getCurrentPosition(function(position){
            let lat = position.coords.latitude;
            let long = position.coords.longitude;
            document.form.lati_inve.value=lat;
            document.form.long_inve.value=long;
        });
        //setTimeout(poner_focus,4000);
    </script>
	</form>
	</body>
</html>
