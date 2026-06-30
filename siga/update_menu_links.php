<?php
$file = 'c:\\xampp\\htdocs\\siga\\chatbot_potencial_humano\\api.php';
$content = file_get_contents($file);

// Add is_main_menu flag to greetings
$search_saludos = "        responderJSON([
            'respuesta' => \"¡Hola! Soy tu asistente de Potencial Humano. Elige un tema o escribe tu consulta:\\n\\n\" . \$menuPrincipal,
            'claves_validas' => array_keys(\$opcionesEstaticas)
        ]);";
$replace_saludos = "        responderJSON([
            'respuesta' => \"¡Hola! Soy tu asistente de Potencial Humano. Elige un tema o escribe tu consulta:\\n\\n\" . \$menuPrincipal,
            'claves_validas' => array_keys(\$opcionesEstaticas),
            'is_main_menu' => true
        ]);";
$content = str_replace($search_saludos, $replace_saludos, $content);

// Add is_main_menu flag to menu principal regex
$search_menu = "    // CASO 1: Cualquier variación de \"Volver al menú principal\"
    if (preg_match('/volver al men[uú] principal/ui', \$mensajeLimpio)) {
        \$_SESSION['estado'] = 'inicio';
        responderJSON([
            'respuesta' => \$menuPrincipal,
            'claves_validas' => array_keys(\$opcionesEstaticas)
        ]);
    }";
$replace_menu = "    // CASO 1: Cualquier variación de \"Volver al menú principal\"
    if (preg_match('/volver al men[uú] principal/ui', \$mensajeLimpio)) {
        \$_SESSION['estado'] = 'inicio';
        responderJSON([
            'respuesta' => \$menuPrincipal,
            'claves_validas' => array_keys(\$opcionesEstaticas),
            'is_main_menu' => true
        ]);
    }";
$content = str_replace($search_menu, $replace_menu, $content);

// Fix the Formato Interno links
$content = str_replace('• Formato Interno de descanso médico 📎', '[📎 Formato Interno de descanso médico](#)', $content);
$content = str_replace('• Ver Cartilla Informativa 📎', '[📎 Ver Cartilla Informativa](#)', $content);
$content = str_replace('• Copia fedateada del Certificado de Incapacidad Temporal para el Trabajo - CITT', '• Copia fedateada del Certificado de Incapacidad Temporal para el Trabajo - CITT', $content); // No change here, just to be careful.

file_put_contents($file, $content);
echo "Updated api.php successfully!";
