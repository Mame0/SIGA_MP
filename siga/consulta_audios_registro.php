<?
//classes/TCPDF/examples/personal_fotocheck.php
	require_once 'include/cabecera.php';
	require_once 'classes/Html.class.php';
	require_once 'classes/Db.class.php';
	function get_client_ip() {
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
	if($_POST['arch_audi'])
	{
	    $Db = new Db();
	    $dire=$_POST['dire_auop'];
	    if(!$dire)
	        $dire=get_client_ip();
        $fdig=date("YmdHis");
        echo"$fdig<BR>$_POST[arch_audi]";
        $arra_arch = explode("/", $_POST['arch_audi']);
        $sede=$arra_arch[1];
        $anno=$arra_arch[2];
        $mess=$arra_arch[3];
        $expe=$arra_arch[4];
        $audi=$arra_arch[5];
        $arch=$arra_arch[6];
	    $result=$Db->query("insert into mp_cons_audi_oper values('','$sede','$anno','$mess','$expe','$audi','$arch','$_SESSION[iden_oper]','1','$dire','$fdig','1')");
	}
?>
<html>
<body>
<script>
    window.addEventListener('message', function(event) {
      alert(`Recibí ccccc ${event.data} de ${event.origin}`);
    });
  </script>
<form name=form method=POST>
    <input type=hidden name="sede_audi" id="sede_audi">
    <input type=hidden name="anno_audi" id="anno_audi">
    <input type=hidden name="mess_audi" id="mess_audi">
    <input type=hidden name="expe_audi" id="expe_audi">
    <input type=hidden name="audi_audi" id="audi_audi">
    <input type=hidden name="arch_audi" id="arch_audi">
    <input type=hidden name="dire_auop" id="dire_auop">
    <!--<input type=button name="Click" value="Click aqui 1" onclick="window.parent.postMessage('Audios02.document.form.sede_audi.value=1', '*');">-->
</form>
</body>
</html>