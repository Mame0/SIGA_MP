<?php

    class FileDownloader
    {
        private $basePath;

        public function __construct($basePath = 'downloads') {
            $this->basePath = $basePath;
        }

        public function download($file) {
            $fileName = basename($file);
            $filePath = $this->basePath . '/' . $fileName;

	    $filePath=$file;

            if (file_exists($filePath)) {
                header('Content-Description: File Transfer');
                header('Content-Type: application/octet-stream');
                header('Content-Disposition: attachment; filename="' . $fileName . '"');
                header('Content-Length: ' . filesize($filePath));

                readfile($filePath);
                exit;
            } else {
                http_response_code(404);
                die();
            }
        }
    }
	require_once 'classes/Db.class.php';
	$Db = new Db();

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


	$result=$Db->query("select * from mp_cons_audi where iden_audi='$_POST[iden_audi]'");
        foreach($result as $rows)
        {
            $audi="../audios/audios/0401/$rows[anno_audi]/$rows[mess_audi]/$rows[expe_audi]/$rows[audi_audi]/$rows[arch_audi]";
	    if(!file_exists($audi))
            	$audi="../audios1/audios/0401/$rows[anno_audi]/$rows[mess_audi]/$rows[expe_audi]/$rows[audi_audi]/$rows[arch_audi]";
            if($_POST['regi_audi'])
            {
                $fdig=date("YmdHis");
		$result_regi=$Db->query("insert into mp_cons_audi_oper(sede_audi,anno_audi,mess_audi,expe_audi,audi_audi,arch_audi,iden_oper,ndoc_oper,acce_audi,dire_auop,fdig_auop,esta_auop) values('0401','$rows[anno_audi]','$rows[mess_audi]','$rows[expe_audi]','$rows[audi_audi]','$rows[arch_audi]','$_POST[iden_oper]','$_POST[ndoc_oper]','1','$dire_auop','$fdig','1')");
            }
	}

	if($_POST['desc_audi'])
	{
		if (isset($audi))
		{
			$downloader = new FileDownloader();
			$downloader->download($audi);
		}
		else
		{
			//echo 'Archivo no seleccionado.';
		}	
	}
	else
	{
                //header('Content-Description: File Transfer');
                //header('Content-Type: application/octet-stream');
                header('Content-Type:');
	}
?>
