<?
	require_once 'include/cabecera.php';
	//require_once 'classes/Html.class.php';
	require_once 'classes/Db.class.php';
	$Db = new Db();
    $Accion=$_REQUEST['Accion'];
    
    if(is_callable($Accion))
    {
        $Accion($Db);
    }
    function GetDepartamentos($Db)
    {
        header('Content-Type: application/json');
        $Departamentos = array();
        $Departamentos[0]['Departamento']="<- Departamento ->";
        $Departamentos[0]['IdDepartamento']="0";
        $result=$Db->query("select distinct cdep as IdDepartamento,depa as Departamento from mp_admi_ubig_reni order by depa");
        foreach($result as $rows)
            $Departamentos[]=$rows;
        echo json_encode($Departamentos);
    }
    function GetProvincias($Db)
    {
        header('Content-Type: application/json');
        $Provincias = array();
        $Provincias[0]['Provincia']="<- Provincia ->";
        $Provincias[0]['IdProvincia']="0";
        $result=$Db->query("select distinct CONCAT(cdep,cpro) as IdProvincia,prov as Provincia from mp_admi_ubig_reni where cdep=".$_REQUEST['IdDepartamento']." AND cpro>0 order by prov");
        foreach($result as $rows)
            $Provincias[]=$rows;
        echo json_encode($Provincias);
    }
    function GetDistritos($Db)
    {
        header('Content-Type: application/json');
        $Distritos = array();
        $Distritos[0]['Distrito']="<- Distrito ->";
        $Distritos[0]['IdDistrito']="0";
        $result=$Db->query("select distinct CONCAT(cdep,cpro,cdis) as IdDistrito,dist as Distrito from mp_admi_ubig_reni where CONCAT(cdep,cpro)=".$_REQUEST['IdProvincia']." AND cpro>0 AND cdis>0 order by dist");
        foreach($result as $rows)
            $Distritos[]=$rows;
        echo json_encode($Distritos);
    }
    function GetTodoElUbigeo($Db)
    {
    header('Content-Type: application/json');
    $query = "SELECT cdep, depa, cpro, prov, cdis, dist 
              FROM mp_admi_ubig_reni 
              WHERE cpro != '00' AND cdis != '00' 
              ORDER BY depa, prov, dist";
    $result = $Db->query($query);
    
    $ubigeo_completo = [];
    foreach ($result as $row) {
        $ubigeo_completo[] = $row;
    }
    echo json_encode($ubigeo_completo);
    }
?>