<?
	require_once '../classes/Db.class.php';
	$Db = new Db();

	$result_pagi=$Db->query("select * from mp_fotocheck_personal where appe_pers=:m_busq order by appe_pers,nomb_pers asc ",[':m_busq'=>$_POST['search']]);
		foreach($result_pagi as $rows)
		{
		    echo"-".$rows['appe_pers'];
		}

?>