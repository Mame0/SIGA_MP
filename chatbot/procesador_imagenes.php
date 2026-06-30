<?php
/**
 * Procesador de imágenes del directorio
 * Lee todas las imágenes de la carpeta imagenes_directorio
 */

echo "<h1>Procesador de Imágenes del Directorio</h1>";
echo "<style>
    body{font-family:sans-serif;padding:20px;} 
    img{max-width:800px;border:1px solid #ddd;margin:10px 0;}
    .success{color:green;} 
    .error{color:red;}
    .info{background:#e3f2fd;padding:10px;margin:10px 0;border-left:4px solid #2196f3;}
</style>";

$carpetaImagenes = 'imagenes_directorio';

if (!is_dir($carpetaImagenes)) {
    die("<p class='error'>Error: La carpeta '$carpetaImagenes' no existe. Por favor créala primero.</p>");
}

// Buscar todas las imágenes
$imagenes = glob($carpetaImagenes . '/*.{jpg,jpeg,png,JPG,JPEG,PNG}', GLOB_BRACE);

if (empty($imagenes)) {
    echo "<div class='info'>";
    echo "<h2>📁 Carpeta lista, esperando imágenes...</h2>";
    echo "<p>La carpeta <code>$carpetaImagenes</code> está creada pero vacía.</p>";
    echo "<p><strong>Por favor:</strong></p>";
    echo "<ol>";
    echo "<li>Abre el Explorador de Windows</li>";
    echo "<li>Ve a: <code>c:\\xampp\\htdocs\\siga\\chatbot\\$carpetaImagenes\\</code></li>";
    echo "<li>Copia tus 15 imágenes del directorio ahí</li>";
    echo "<li>Recarga esta página</li>";
    echo "</ol>";
    echo "</div>";
    exit;
}

// Ordenar imágenes
sort($imagenes);

echo "<h2 class='success'>✓ Se encontraron " . count($imagenes) . " imágenes</h2>";

echo "<div class='info'>";
echo "<p><strong>Instrucciones:</strong></p>";
echo "<ol>";
echo "<li>Revisa las imágenes abajo para verificar que estén todas</li>";
echo "<li>Si faltan imágenes, agrégalas a la carpeta y recarga</li>";
echo "<li>Una vez que estén todas, <strong>toma capturas de pantalla</strong> de esta página</li>";
echo "<li>Sube las capturas aquí en el chat (en lotes de 5 si es necesario)</li>";
echo "<li>Yo extraeré los datos y generaré el SQL</li>";
echo "</ol>";
echo "</div>";

echo "<hr>";

// Mostrar todas las imágenes
foreach ($imagenes as $index => $imagen) {
    $numero = $index + 1;
    echo "<h3>Imagen $numero: " . basename($imagen) . "</h3>";
    echo "<img src='$imagen' alt='Página $numero'>";
    echo "<hr>";
}

echo "<p><a href='procesador_imagenes.php'>Recargar</a> | <a href='index.php'>Volver al Chatbot</a></p>";
