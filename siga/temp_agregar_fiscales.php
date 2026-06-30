<?
	require_once 'classes/Db.class.php';
	$Db = new Db();

	$result=$Db->query("select * from temp_fiscales");
	foreach($result as $rows)
	{
	    $resultf=$Db->query("select * from mp_admi_oper where ndoc_oper='".$rows['dni']."'");
	    $codi_oper=$resultf[0]['iden_oper'];
        if(!$codi_oper) //si no existe lo creamos
        {
            //falta decodificar el paswword
            //$resulti=$Db->query("insert into mp_admi_oper values('','$rows[dni]','".md5($rows['dni'])."','$rows[dni]','$rows[appa]','$rows[apma]','$rows[nomb]','$rows[carg]','','$rows[celu]','$rows[mail]','','','9','1','20300101','1','20220928053600','0')");
            //$codi_oper=$Db->lastInsertId();
            echo"<BR>USUARIO AGREGADO: $codi_oper";
        }
        if($codi_oper)
        {
            //para agregar los roles
            $resultr=$Db->query("select * from mp_admi_oper_role where iden_oper='$codi_oper' AND iden_role='16'");
            if(!$resultr[0]['iden_oper'])
            {
                //$result_role=$Db->query("insert into mp_admi_oper_role values('$codi_oper','16')");
                echo"<BR>ROL AGREGADO: $codi_oper";
            }
        }
        else
        {
               echo"<BR>ERROR: $rows[dni] - $rows[nomb] $rows[appa] $rows[apma]";
               //echo"<BR>ssss";
        }
	}	
//ROLES: 15 Administrador y 16 Usuario

?>
