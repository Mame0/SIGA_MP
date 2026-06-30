<?
require_once("SFTP.php");
class SFTP_alimentos extends SFTP
{
	function SFTP_alimentos($host,$user,$pass)
	{
		//parent::SFTP($host,$user,$pass);
		if($this->connect())
			echo"!OK";
		else
			echo"ERROR";
	}
	function sftp_Upload()
	{
	}
}
?>
