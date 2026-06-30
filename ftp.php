<?
include "classes/SFTP.php"; 
require_once 'classes/Db.class.php';
$Db = new Db();

// set SFTP object, use host, username and password 
$ftp = new SFTP();
$carp=$ftp->SFTP_get_name("5","5",$Db);
echo"<HR>$carp<HR>";
//$ftp->SFTP_Rename("sello3.png", "sello4.png");
die();
/*
if($ftp->connect())
{ 
      print "Connection successful";

print $ftp->pwd();

print_r($ftp->ls());

//$ftp->mkdir("mydir2");
$ftp->rename("sello3.png", "sello4.png");

if($ftp->get("sello2.png", "temporal/sello3.png")) { 
            print "File downloaded"; 
      } else { 
            print "<br />Download failed: " . $ftp->error; 
      } 

if($ftp->put("sello5.png", "remote.png")) { 
            print "Filed uploaded"; 
      } else { 
            print "<br />Upload failed: " . $ftp->error; 
      }


}
*/
?>
