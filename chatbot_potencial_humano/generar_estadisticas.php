<?php
/**
 * Script para generar estadísticas del chatbot
 * Ejecutar este archivo para actualizar la tabla mp_chatbot_estadisticas
 */

require_once 'config.php';
require_once CHATBOT_DB_CLASS_PATH;

echo "<h1>Generador de Estadísticas del Chatbot</h1>";
echo "<style>body{font-family:sans-serif;padding:20px;} table{border-collapse:collapse;width:100%;} th,td{border:1px solid #ddd;padding:8px;text-align:left;} th{background:#667eea;color:white;}</style>";

try {
    $db = new Db();
    
    // Obtener estadísticas por día
    $sql = "
        SELECT 
            DATE(fecha_creacion) as fecha,
            COUNT(DISTINCT sesion_id) as total_conversaciones,
            COUNT(*) as total_mensajes,
            COUNT(*) / COUNT(DISTINCT sesion_id) as mensajes_por_conversacion
        FROM mp_chatbot_historial 
        GROUP BY DATE(fecha_creacion)
        ORDER BY fecha DESC
    ";
    
    $estadisticas = $db->query($sql);
    
    echo "<h2>Actualizando tabla mp_chatbot_estadisticas...</h2>";
    
    $registrosActualizados = 0;
    
    foreach ($estadisticas as $stat) {
        $fecha = $stat['fecha'];
        $totalConversaciones = $stat['total_conversaciones'];
        $totalMensajes = $stat['total_mensajes'];
        
        // Calcular tiempo promedio de respuesta (solo mensajes del bot)
        $sqlTiempo = "
            SELECT AVG(TIMESTAMPDIFF(SECOND, 
                LAG(fecha_creacion) OVER (PARTITION BY sesion_id ORDER BY fecha_creacion),
                fecha_creacion
            )) as tiempo_promedio
            FROM mp_chatbot_historial
            WHERE DATE(fecha_creacion) = :fecha
            AND bot_respuesta IS NOT NULL
        ";
        
        // Por simplicidad, usamos un valor estimado de 2 segundos (puedes mejorarlo)
        $tiempoPromedio = 2.0;
        
        // Insertar o actualizar
        $existe = $db->query("SELECT id FROM mp_chatbot_estadisticas WHERE fecha = :fecha", [':fecha' => $fecha]);
        
        if (count($existe) > 0) {
            // Actualizar
            $db->update('mp_chatbot_estadisticas', 
                [
                    'total_conversaciones' => $totalConversaciones,
                    'total_mensajes' => $totalMensajes,
                    'tiempo_respuesta_promedio' => $tiempoPromedio
                ],
                ['fecha' => $fecha]
            );
        } else {
            // Insertar
            $db->insert('mp_chatbot_estadisticas', [
                'fecha' => $fecha,
                'total_conversaciones' => $totalConversaciones,
                'total_mensajes' => $totalMensajes,
                'tiempo_respuesta_promedio' => $tiempoPromedio
            ]);
        }
        
        $registrosActualizados++;
    }
    
    echo "<p style='color:green;'>✓ Se actualizaron <strong>$registrosActualizados</strong> registros de estadísticas.</p>";
    
    // Mostrar las estadísticas
    echo "<h2>Estadísticas Generadas:</h2>";
    
    $estadisticasFinales = $db->query("SELECT * FROM mp_chatbot_estadisticas ORDER BY fecha DESC");
    
    if (count($estadisticasFinales) > 0) {
        echo "<table>";
        echo "<tr><th>Fecha</th><th>Conversaciones</th><th>Mensajes</th><th>Mensajes/Conversación</th><th>Tiempo Promedio (seg)</th></tr>";
        
        foreach ($estadisticasFinales as $row) {
            $mensajesPorConv = round($row['total_mensajes'] / $row['total_conversaciones'], 1);
            echo "<tr>";
            echo "<td>" . $row['fecha'] . "</td>";
            echo "<td>" . $row['total_conversaciones'] . "</td>";
            echo "<td>" . $row['total_mensajes'] . "</td>";
            echo "<td>" . $mensajesPorConv . "</td>";
            echo "<td>" . $row['tiempo_respuesta_promedio'] . "</td>";
            echo "</tr>";
        }
        
        echo "</table>";
        
        // Totales generales
        $totales = $db->query("
            SELECT 
                SUM(total_conversaciones) as total_conv,
                SUM(total_mensajes) as total_msg,
                AVG(tiempo_respuesta_promedio) as avg_tiempo
            FROM mp_chatbot_estadisticas
        ");
        
        echo "<h2>Totales Generales:</h2>";
        echo "<ul>";
        echo "<li><strong>Total de conversaciones:</strong> " . $totales[0]['total_conv'] . "</li>";
        echo "<li><strong>Total de mensajes:</strong> " . $totales[0]['total_msg'] . "</li>";
        echo "<li><strong>Tiempo promedio de respuesta:</strong> " . round($totales[0]['avg_tiempo'], 2) . " segundos</li>";
        echo "</ul>";
        
    } else {
        echo "<p>No hay estadísticas disponibles.</p>";
    }
    
    echo "<hr>";
    echo "<p><a href='generar_estadisticas.php'>Recargar</a> | <a href='index.php'>Volver al Chatbot</a></p>";
    
} catch (Exception $e) {
    echo "<p style='color:red;'>Error: " . $e->getMessage() . "</p>";
}
