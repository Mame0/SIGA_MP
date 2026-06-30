<?
$base = 'localhost';
	$dbuser='mpfnarequipa_siga';
	$dbpass='Mpfn2020aqp';
	$dbnomb='mpfnarequipa_siga';
$connli = mysqli_connect($base,$dbuser,$dbpass);
mysqli_select_db($connli,$dbnomb);
echo (mysqli_error($connli));


//cambiar por codigo de dependencia
$codi_depe=22;



$n=0;
$sql="";
$table="datasgf";
$handle = fopen ("uploads/".$_POST["nomfile"] , "r" );
while (($data = fgetcsv($handle, 1000, "\t")) !== FALSE) {
	$n++;
	if ($n>1) {
		if ($n>2) { $sql.=","; }
		$sql.="('$data[0]', '$data[1]', '$data[2]', '". date('Y-m-d H:i:s', strtotime(str_replace('/', '-', $data[3]))) ."', '". date('Y-m-d H:i:s', strtotime(str_replace('/', '-', $data[4]))) ."', '". date('Y-m-d H:i:s', strtotime(str_replace('/', '-', $data[5]))) ."', '$data[6]', '$data[7]', '$data[8]', '$data[9]', '$data[10]', '$data[11]', '$data[12]', '". (($data[13]=="")?"0000-00-00 00:00:00": date('Y-m-d H:i:s', strtotime(str_replace('/', '-', $data[13]))) ) ."',".$codi_depe.")";

	}
}
//echo $sql;

	$import = "INSERT INTO $table
	(id_fiscal, no_fiscal, id_unico, fe_denuncia, fe_ing_caso, fe_asig, id_etapa, de_etapa, id_estado, de_estado, st_acumulado, tx_tipo_caso, condicion, fe_conclusion, id_depe)
	VALUES
	".$sql." ";
	mysqli_query($connli, $import) or die(mysqli_error($connli));


/*
	$import = "INSERT INTO $table
	(id_fiscal, no_fiscal, id_unico, fe_denuncia, fe_ing_caso, fe_asig, id_etapa, de_etapa, id_estado, de_estado, st_acumulado, tx_tipo_caso, condicion, fe_conclusion)
	VALUES
	('$data[0]', '$data[1]', '$data[2]', '$data[3]', '$data[4]', '$data[5]', '$data[6]', '$data[7]', '$data[8]', '$data[9]', '$data[10]', '$data[11]', '$data[12]', '$data[13]')";
	mysqli_query($connli, $import) or die(mysqli_error($connli));
*/


fclose($handle);


echo "x";
?>