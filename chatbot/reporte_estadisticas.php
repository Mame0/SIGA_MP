<?php
/**
 * Reporte visual de estadísticas para la tesis
 * Muestra gráficos y datos útiles para presentar
 */

require_once 'config.php';
require_once CHATBOT_DB_CLASS_PATH;

$db = new Db();

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de Estadísticas - Chatbot</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: #f8fafc;
            padding: 20px;
        }
        .container { max-width: 1200px; margin: 0 auto; }
        h1 { color: #1e293b; margin-bottom: 30px; }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        .stat-card {
            background: white;
            padding: 24px;
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        .stat-card h3 {
            color: #64748b;
            font-size: 14px;
            font-weight: 500;
            margin-bottom: 8px;
        }
        .stat-card .value {
            color: #1e293b;
            font-size: 32px;
            font-weight: 700;
        }
        .stat-card .label {
            color: #94a3b8;
            font-size: 12px;
            margin-top: 4px;
        }
        table {
            width: 100%;
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #e2e8f0;
        }
        th {
            background: #667eea;
            color: white;
            font-weight: 600;
        }
        tr:hover { background: #f8fafc; }
        .section {
            background: white;
            padding: 24px;
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        .section h2 {
            color: #1e293b;
            margin-bottom: 16px;
            font-size: 20px;
        }
        .btn {
            display: inline-block;
            padding: 10px 20px;
            background: #667eea;
            color: white;
            text-decoration: none;
            border-radius: 8px;
            margin-right: 10px;
        }
        .btn:hover { background: #5568d3; }
    </style>
</head>
<body>
    <div class="container">
        <h1>📊 Reporte de Estadísticas del Chatbot</h1>
        
        <?php
        // Obtener totales generales
        $totales = $db->query("
            SELECT 
                COUNT(DISTINCT sesion_id) as total_conversaciones,
                COUNT(*) as total_mensajes,
                COUNT(DISTINCT DATE(fecha_creacion)) as dias_activos
            FROM mp_chatbot_historial
        ");
        
        $totalConv = $totales[0]['total_conversaciones'] ?? 0;
        $totalMsg = $totales[0]['total_mensajes'] ?? 0;
        $diasActivos = $totales[0]['dias_activos'] ?? 0;
        $promedioPorDia = $diasActivos > 0 ? round($totalConv / $diasActivos, 1) : 0;
        ?>
        
        <div class="stats-grid">
            <div class="stat-card">
                <h3>Total de Conversaciones</h3>
                <div class="value"><?php echo $totalConv; ?></div>
                <div class="label">Usuarios únicos atendidos</div>
            </div>
            
            <div class="stat-card">
                <h3>Total de Mensajes</h3>
                <div class="value"><?php echo $totalMsg; ?></div>
                <div class="label">Intercambios totales</div>
            </div>
            
            <div class="stat-card">
                <h3>Días Activos</h3>
                <div class="value"><?php echo $diasActivos; ?></div>
                <div class="label">Días con actividad</div>
            </div>
            
            <div class="stat-card">
                <h3>Promedio Diario</h3>
                <div class="value"><?php echo $promedioPorDia; ?></div>
                <div class="label">Conversaciones por día</div>
            </div>
        </div>
        
        <div class="section">
            <h2>📅 Estadísticas por Día</h2>
            <?php
            $estadisticasDiarias = $db->query("
                SELECT 
                    DATE(fecha_creacion) as fecha,
                    COUNT(DISTINCT sesion_id) as conversaciones,
                    COUNT(*) as mensajes,
                    ROUND(COUNT(*) / COUNT(DISTINCT sesion_id), 1) as mensajes_por_conv
                FROM mp_chatbot_historial
                GROUP BY DATE(fecha_creacion)
                ORDER BY fecha DESC
                LIMIT 30
            ");
            
            if (count($estadisticasDiarias) > 0) {
                echo "<table>";
                echo "<tr><th>Fecha</th><th>Conversaciones</th><th>Mensajes</th><th>Mensajes/Conversación</th></tr>";
                foreach ($estadisticasDiarias as $row) {
                    echo "<tr>";
                    echo "<td>" . date('d/m/Y', strtotime($row['fecha'])) . "</td>";
                    echo "<td>" . $row['conversaciones'] . "</td>";
                    echo "<td>" . $row['mensajes'] . "</td>";
                    echo "<td>" . $row['mensajes_por_conv'] . "</td>";
                    echo "</tr>";
                }
                echo "</table>";
            } else {
                echo "<p>No hay datos disponibles.</p>";
            }
            ?>
        </div>
        
        <div class="section">
            <h2>❓ Preguntas Más Frecuentes (Top 10)</h2>
            <?php
            $preguntasFrecuentes = $db->query("
                SELECT 
                    usuario_mensaje,
                    COUNT(*) as veces
                FROM mp_chatbot_historial
                GROUP BY usuario_mensaje
                ORDER BY veces DESC
                LIMIT 10
            ");
            
            if (count($preguntasFrecuentes) > 0) {
                echo "<table>";
                echo "<tr><th>Pregunta</th><th>Veces Preguntado</th></tr>";
                foreach ($preguntasFrecuentes as $row) {
                    $pregunta = strlen($row['usuario_mensaje']) > 100 
                        ? substr($row['usuario_mensaje'], 0, 100) . '...' 
                        : $row['usuario_mensaje'];
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($pregunta) . "</td>";
                    echo "<td><strong>" . $row['veces'] . "</strong></td>";
                    echo "</tr>";
                }
                echo "</table>";
            } else {
                echo "<p>No hay datos disponibles.</p>";
            }
            ?>
        </div>
        
        <div style="margin-top: 30px;">
            <a href="generar_estadisticas.php" class="btn">Actualizar Estadísticas</a>
            <a href="index.php" class="btn">Volver al Chatbot</a>
        </div>
    </div>
</body>
</html>
