<?php
/**
 * Extractor de texto del PDF del directorio
 * Genera el SQL automáticamente
 */

// Intentar usar diferentes métodos para extraer texto del PDF

echo "<h1>Extractor de Directorio PDF</h1>";
echo "<style>body{font-family:sans-serif;padding:20px;} pre{background:#f5f5f5;padding:10px;overflow:auto;} .success{color:green;} .error{color:red;}</style>";

$pdfFile = 'directorio.pdf';

if (!file_exists($pdfFile)) {
    die("<p class='error'>Error: No se encontró el archivo directorio.pdf</p>");
}

echo "<h2>Método 1: Intentando extraer con pdftotext (si está instalado)</h2>";

// Intentar con pdftotext (herramienta de línea de comandos)
$output = shell_exec("pdftotext $pdfFile -");

if ($output && strlen($output) > 100) {
    echo "<p class='success'>✓ Texto extraído exitosamente con pdftotext</p>";
    echo "<h3>Contenido extraído:</h3>";
    echo "<pre>" . htmlspecialchars(substr($output, 0, 2000)) . "...</pre>";
    
    // Guardar en archivo de texto
    file_put_contents('directorio_extraido.txt', $output);
    echo "<p class='success'>✓ Guardado en directorio_extraido.txt</p>";
    
} else {
    echo "<p class='error'>✗ pdftotext no está disponible</p>";
    
    echo "<h2>Método 2: Información del PDF</h2>";
    echo "<p>Tamaño del archivo: " . filesize($pdfFile) . " bytes</p>";
    
    echo "<h2>Solución Alternativa</h2>";
    echo "<p>Por favor, realiza UNO de estos pasos:</p>";
    echo "<ol>";
    echo "<li><strong>Opción A (Recomendada):</strong> Convierte el PDF a imágenes:
        <ul>
            <li>Abre el PDF</li>
            <li>Toma capturas de pantalla de cada página</li>
            <li>Sube las imágenes aquí en el chat (puedes arrastrarlas)</li>
        </ul>
    </li>";
    echo "<li><strong>Opción B:</strong> Usa una herramienta online:
        <ul>
            <li>Ve a: <a href='https://www.ilovepdf.com/es/pdf_a_texto' target='_blank'>ilovepdf.com/es/pdf_a_texto</a></li>
            <li>Sube directorio.pdf</li>
            <li>Descarga el archivo .txt</li>
            <li>Copia el contenido aquí</li>
        </ul>
    </li>";
    echo "<li><strong>Opción C:</strong> Copia el texto manualmente:
        <ul>
            <li>Abre el PDF</li>
            <li>Selecciona todo el texto (Ctrl+A)</li>
            <li>Copia (Ctrl+C)</li>
            <li>Pégalo en un mensaje aquí</li>
        </ul>
    </li>";
    echo "</ol>";
}

echo "<hr>";
echo "<p><a href='index.php'>Volver al Chatbot</a></p>";
