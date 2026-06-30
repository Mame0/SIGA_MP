<?php
require_once 'include/cabecera.php';
require_once 'classes/Html.class.php';
require_once 'classes/Db.class.php';

$Db = new Db();
$html = new htmlclass();

$tabla_maestra = 'mp_bienes_incautados_estados';
$titulo_pagina = 'Mantenimiento de Estados de Bienes Incautados';
$url_retorno = 'admin_bienes_incautados_estados.php';

// Procesar POST para guardar o actualizar
if (isset($_POST['save'])) {
    $id = $_POST['id'];
    $nombre = $_POST['nombre'];
    $activo = isset($_POST['activo']) ? 1 : 0;

    if ($id) {
        // Actualizar
        $Db->update($tabla_maestra, ['nombre' => $nombre, 'activo' => $activo], ['id' => $id]);
    } else {
        // Insertar
        $Db->insert($tabla_maestra, ['nombre' => $nombre, 'activo' => $activo]);
    }
    echo "<script>window.location.href = '$url_retorno';</script>";
    exit;
}

// Procesar GET para editar o crear
$edit_data = ['id' => '', 'nombre' => '', 'activo' => 1];
if (isset($_GET['edit'])) {
    $id_editar = $_GET['edit'];
    $result = $Db->select($tabla_maestra, ['id' => $id_editar]);
    if ($result) {
        $edit_data = $result[0];
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <title><?php echo $titulo_pagina; ?></title>
    <link rel="stylesheet" href="css/forms.css" />
    <link rel="stylesheet" href="css/table_responsive.css" />
</head>
<body>
    <center><h2><?php echo $titulo_pagina; ?></h2></center>

    <form name="form_maestro" method="post" action="<?php echo $url_retorno; ?>">
        <input type="hidden" name="id" value="<?php echo $edit_data['id']; ?>">
        
        <?php
        echo $html->put_title_demand("Datos del Estado");
        echo "<main style='column-count:2;'>";
        echo $html->put_text('text', 'Nombre del Estado', 'Ingrese el nombre', 'nombre', $edit_data['nombre'], '1', '100', 'required');
        echo $html->put_checkbox('Activo', 'activo', $edit_data['activo'], '', 'Marcar si el estado está activo');
        echo "</main>";
        ?>
        
        <div class="row" style="margin-top:20px;text-align:center;">
            <input type="submit" name="save" class="button_foot" value="Guardar">
            <a href="<?php echo $url_retorno; ?>" class="button_foot" style="text-decoration:none;">Cancelar</a>
        </div>
    </form>

    <hr>

    <?php
    // Listar todos los registros
    $estados = $Db->select($tabla_maestra, [], '', '', ['nombre' => 'ASC']);

    echo $html->put_table_responsive_open();
    echo $html->put_table_responsive_title("Listado de Estados");
    
    $header = ['nombre' => 'Nombre', 'activo' => 'Activo', 'acciones' => 'Acciones'];
    echo $html->put_table_responsive_header($header);

    foreach ($estados as $estado) {
        $data = [];
        $data['nombre'] = $estado['nombre'];
        $data['activo'] = $estado['activo'] ? '<font color="green">SI</font>' : '<font color="red">NO</font>';
        $data['acciones'] = '<a href="'.$url_retorno.'?edit='.$estado['id'].'">Editar</a>';
        
        echo $html->put_table_responsive_data($header, $data);
    }

    echo $html->put_table_responsive_close();
    ?>

</body>
</html>
