<?php
	function get_client_ip()
    {
        $ipaddress = '';
        if (isset($_SERVER['HTTP_CLIENT_IP']) && $_SERVER['HTTP_CLIENT_IP'])
            $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
        else if(isset($_SERVER['HTTP_X_FORWARDED_FOR']) && $_SERVER['HTTP_X_FORWARDED_FOR'])
            $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
        else if(isset($_SERVER['HTTP_X_FORWARDED']) && $_SERVER['HTTP_X_FORWARDED'])
            $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
        else if(isset($_SERVER['HTTP_FORWARDED_FOR']) && $_SERVER['HTTP_FORWARDED_FOR'])
            $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
        else if(isset($_SERVER['HTTP_FORWARDED']) && $_SERVER['HTTP_FORWARDED'])
            $ipaddress = $_SERVER['HTTP_FORWARDED'];
        else if(isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'])
            $ipaddress = $_SERVER['REMOTE_ADDR'];
        else
            $ipaddress = 'UNKNOWN';
        return $ipaddress;
    }
    
    $dire_auop=get_client_ip();
    $pagi= isset($_SERVER["REQUEST_URI"]) ? $_SERVER["REQUEST_URI"] : '';
    $fdig=date("YmdHis");
    
    $iden_oper = isset($_SESSION['iden_oper']) ? $_SESSION['iden_oper'] : '';
    $ndoc_oper = isset($_SESSION['ndoc_oper']) ? $_SESSION['ndoc_oper'] : '';

    if ($iden_oper) {
        $result=$Db->query("insert into mp_admi_acce(dire_acce,iden_oper,ndoc_oper,diip_acce,fdig_acce,esta_acce) values('$pagi','$iden_oper','$ndoc_oper','$dire_auop','$fdig','1')");
    }
?>
