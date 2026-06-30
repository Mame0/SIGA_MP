<?php
	if(isset($_POST['username']))
	{
		require_once 'classes/Db.class.php';
		$Db = new Db();

		$result=$Db->select('mp_admi_oper',['logi_oper'=>$_POST['username'],'pass_oper'=>md5($_POST['password'])]);
		if(isset($result[0]['iden_oper']))
		{
			session_start();

			$result_conf=$Db->select('mp_admi_conf');
			foreach($result_conf as $rows)
				$_SESSION[$rows['nomb_conf']]=$rows['valo_conf'];

			$_SESSION['iden_oper']=$result[0]['iden_oper'];
			$_SESSION['logi_oper']=$result[0]['logi_oper'];
			$_SESSION['appa_oper']=$result[0]['appa_oper'];
			$_SESSION['apma_oper']=$result[0]['apma_oper'];
			$_SESSION['nomb_oper']=$result[0]['nomb_oper'];
			$_SESSION['ndoc_oper']=$result[0]['ndoc_oper'];
			$_SESSION['codi_depe']=$result[0]['codi_depe'];
			$_SESSION['codi_perf']=$result[0]['codi_perf'];
			$_SESSION['flag_band']=$result[0]['flag_band'];
			
			header('location: home.php');
		}
		else
			header('location: index.php');
	}
	else
		header('location: index.php');
		
?>
