<?
//	require_once 'include/cabecera.php';
//	require_once 'classes/Html.class.php';
	require_once 'classes/Db.class.php';
	$Db = new Db();
	function formato_expediente($expe)
	{
	    $expe=substr($expe,4).'-'.substr($expe,0,4);
	    return $expe;
	}
	function get_client_ip()
	{
        $ipaddress = '';
        if (getenv('HTTP_CLIENT_IP'))
            $ipaddress = getenv('HTTP_CLIENT_IP');
        else if(getenv('HTTP_X_FORWARDED_FOR'))
            $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
        else if(getenv('HTTP_X_FORWARDED'))
            $ipaddress = getenv('HTTP_X_FORWARDED');
        else if(getenv('HTTP_FORWARDED_FOR'))
            $ipaddress = getenv('HTTP_FORWARDED_FOR');
        else if(getenv('HTTP_FORWARDED'))
           $ipaddress = getenv('HTTP_FORWARDED');
        else if(getenv('REMOTE_ADDR'))
            $ipaddress = getenv('REMOTE_ADDR');
        else
            $ipaddress = 'UNKNOWN';
        return $ipaddress;
	}
	$dire_auop=get_client_ip();
	if($_POST['cade_sele'])
	{
	    $zip = new ZipArchive();
        $zipname=$_POST['expe_audi']."_archivos.zip";
        if($zip->open($zipname,ZipArchive::CREATE) !== TRUE)
        {
            exit("No se pudo crear el archivo ZIP");
        }
        
	    $result=$Db->query("select * from mp_cons_audi where iden_audi in (".$_POST['cade_sele'].")");
	    $cont=0;
        foreach($result as $rows)
        {
            $cont++;
            $audi="http://10.4.100.4/audios/audios/0401/$rows[anno_audi]/$rows[mess_audi]/$rows[expe_audi]/$rows[audi_audi]/$rows[arch_audi]";
            $audi="../audios/audios/0401/$rows[anno_audi]/$rows[mess_audi]/$rows[expe_audi]/$rows[audi_audi]/$rows[arch_audi]";
            $audi="temp_".$cont.".txt";
            
            //$zip->addFile($audi,$rows['arch_audi']);
            $zip->addFile($audi,$audi);
            
            $visi=strstr($_POST['cade_vist'],",$rows[iden_audi],");
            if(!$visi)
            {
                $fdig=date("YmdHis");
//echo"<HR><HR>";
//echo"insert into mp_cons_audi_oper values('','0401','$rows[anno_audi]','$rows[mess_audi]','$rows[expe_audi]','$rows[audi_audi]','$rows[arch_audi]','$_POST[iden_oper]','1','$dire_auop','$fdig','1')";
		        $result_regi=$Db->query("insert into mp_cons_audi_oper(sede_audi,anno_audi,mess_audi,expe_audi,audi_audi,arch_audi,iden_oper,ndoc_oper,acce_audi,dire_auop,fdig_auop,esta_auop) values('0401','$rows[anno_audi]','$rows[mess_audi]','$rows[expe_audi]','$rows[audi_audi]','$rows[arch_audi]','$_POST[iden_oper]','$_SESSION[ndoc_oper]','1','$dire_auop','$fdig','1')");
            }
        }
        $zip->close();
        
        if($cont==1)
        {
            header('Content-Description: File Transfer');
            header('Content-Type: application/zip');
            header('Content-Disposition: attachment; filename="'.$audi.'"');
            header('Content-Length:'.filesize($audi));
            header("Pragma: no-cache");
            header("Expires:0");
            readfile($audi);
        }
        else
        {
            header('Content-Description: File Transfer');
            header('Content-Type: application/zip');
            header('Content-Disposition: attachment; filename="'.$zipname.'"');
            header('Content-Length:'.filesize($zipname));
            header("Pragma: no-cache");
            header("Expires:0");
            readfile($zipname);
        }
	}
?>
