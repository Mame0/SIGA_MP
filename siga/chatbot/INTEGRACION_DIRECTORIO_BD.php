<?php
/**
 * OPCIÓN 1: Integración con Base de Datos
 * Modificar ChatbotAI.php para consultar el directorio
 */

// AGREGAR ESTE MÉTODO A LA CLASE ChatbotAI (después de la línea 200 aprox)

/**
 * Busca información en el directorio de despachos
 * @param string $consulta - Texto de búsqueda del usuario
 * @return string - Información encontrada o vacío
 */
public function buscarEnDirectorio($consulta) {
    try {
        // Detectar si pregunta por un despacho, correo o teléfono
        $palabrasClave = ['despacho', 'fiscalía', 'correo', 'email', 'teléfono', 'telefono', 'contacto', 'número'];
        
        $esBusquedaDirectorio = false;
        foreach ($palabrasClave as $palabra) {
            if (stripos($consulta, $palabra) !== false) {
                $esBusquedaDirectorio = true;
                break;
            }
        }
        
        if (!$esBusquedaDirectorio) {
            return '';
        }
        
        // Buscar en la base de datos
        $sql = "SELECT nombre, correo, telefono, anexo, horario, observaciones 
                FROM mp_chatbot_directorio 
                WHERE activo = 1 
                AND (
                    nombre LIKE :busqueda 
                    OR observaciones LIKE :busqueda
                    OR tipo LIKE :busqueda
                )
                LIMIT 5";
        
        $resultados = $this->db->query($sql, [':busqueda' => '%' . $consulta . '%']);
        
        if (count($resultados) > 0) {
            $info = "\n\n📋 INFORMACIÓN DEL DIRECTORIO:\n\n";
            
            foreach ($resultados as $row) {
                $info .= "• " . $row['nombre'] . "\n";
                if ($row['correo']) {
                    $info .= "  📧 Correo: " . $row['correo'] . "\n";
                }
                if ($row['telefono']) {
                    $info .= "  📞 Teléfono: " . $row['telefono'];
                    if ($row['anexo']) {
                        $info .= " (Anexo: " . $row['anexo'] . ")";
                    }
                    $info .= "\n";
                }
                if ($row['horario']) {
                    $info .= "  🕐 Horario: " . $row['horario'] . "\n";
                }
                if ($row['observaciones']) {
                    $info .= "  ℹ️ " . $row['observaciones'] . "\n";
                }
                $info .= "\n";
            }
            
            return $info;
        }
        
        return '';
        
    } catch (Exception $e) {
        if (CHATBOT_DEBUG_MODE) {
            error_log("Error al buscar en directorio: " . $e->getMessage());
        }
        return '';
    }
}

// MODIFICAR EL MÉTODO obtenerRespuesta (línea 25 aprox)
// ANTES:
/*
public function obtenerRespuesta($mensajeUsuario, $historial = []) {
    if ($this->proveedor === 'gemini') {
        return $this->consultarGemini($mensajeUsuario, $historial);
    }
*/

// DESPUÉS:
/*
public function obtenerRespuesta($mensajeUsuario, $historial = []) {
    // NUEVO: Buscar en el directorio primero
    $infoDirectorio = $this->buscarEnDirectorio($mensajeUsuario);
    
    if ($this->proveedor === 'gemini') {
        return $this->consultarGemini($mensajeUsuario, $historial, $infoDirectorio);
    }
*/

// MODIFICAR consultarGemini para recibir el parámetro extra (línea 40 aprox)
// CAMBIAR:
// private function consultarGemini($mensajeUsuario, $historial) {
// POR:
// private function consultarGemini($mensajeUsuario, $historial, $infoDirectorio = '') {

// Y dentro de consultarGemini, DESPUÉS de agregar el historial (línea 55 aprox), AGREGAR:
/*
    // Agregar información del directorio si existe
    if (!empty($infoDirectorio)) {
        $promptCompleto .= $infoDirectorio . "\n\n";
    }
*/
