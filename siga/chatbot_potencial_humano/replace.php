<?php
$file = 'c:\\xampp\\htdocs\\siga\\chatbot_potencial_humano\\api.php';
$content = file_get_contents($file);

// Find the start and end of $opcionesEstaticas
$startStr = '$opcionesEstaticas = [';
$endStr = '];';

$startPos = strpos($content, $startStr);
$endPos = strpos($content, $endStr, $startPos);

if ($startPos === false || $endPos === false) {
    echo "Could not find \$opcionesEstaticas array in $file\n";
    exit(1);
}

// Prepare the new block
$newBlock = <<<'PHPBLOCK'
$menuPrincipal = "1. CONTROL DE ASISTENCIA\n2. LICENCIAS\n3. TRÁMITES ANTE ESSALUD Y EL ÁREA DE POTENCIAL HUMANO\n4. FOTOCHECK Y CREDENCIAL\n5. EMISIÓN DE CONSTANCIAS Y CERTIFICADOS DE TRABAJO\n6. ESTADO DE TRÁMITE DE BBSS\n7. VACACIONES\n8. RENOVACIÓN CONTRACTUAL\n9. VINCULACIÓN Y DESVINCULACIÓN DE PERSONAL\n10. DECLARACION JURADA DE INGRESOS Y DE BIENES Y RENTAS\n11. DECLARACIONES JURADAS DE INTERESES\n12. BOLETAS DE PAGO";

    $opcionesEstaticas = [
        "Volver al menú principal" => $menuPrincipal,
        "🏠 Volver al menú principal" => $menuPrincipal,
        
        "1. CONTROL DE ASISTENCIA" => "CONTROL DE ASISTENCIA\n(En desarrollo)\n🏠 Volver al menú principal",
        "2. LICENCIAS" => "a. Licencias con goce de haber\nb. Licencias sin goce de haber\n🏠 Volver al menú principal",
        "3. TRÁMITES ANTE ESSALUD Y EL ÁREA DE POTENCIAL HUMANO" => "a. 🏥 SOLICITUD DE SUBSIDIO\nb. 💵 Solicitud de pago diferencial\n🏠 Volver al menú principal",
        "4. FOTOCHECK Y CREDENCIAL" => "a. Credencial – Carreras Especiales\nb. Fotocheck\n🏠 Volver al menú principal",
        "5. EMISIÓN DE CONSTANCIAS Y CERTIFICADOS DE TRABAJO" => "Selecciona el trámite que deseas realizar:\na. 📄 Solicitar Constancia de Trabajo\nb. 📜 Solicitar Certificado de Trabajo\n🏠 Volver al menú principal",
        "6. ESTADO DE TRÁMITE DE BBSS" => "a. Previamente debe de haber efectuado la respectiva entrega de Cargo según Directiva Entrega, recepción y transferencia de cargo del personal del Ministerio Público – Fiscalía de la Nación\nSi presentó la misma puede comunicarse al número 957390290, para consultar el estado del mismo.\n🏠 Volver al menú principal",
        "7. VACACIONES" => "a. Programación de vacaciones anual\nb. Reprogramación de vacaciones\nc. Adelanto de vacaciones\n🏠 Volver al menú principal",
        "8. RENOVACIÓN CONTRACTUAL" => "RENOVACIÓN CONTRACTUAL\n(En desarrollo)\n🏠 Volver al menú principal",
        "9. VINCULACIÓN Y DESVINCULACIÓN DE PERSONAL" => "a. Entrega de Cargo\n🏠 Volver al menú principal",
        "10. DECLARACION JURADA DE INGRESOS Y DE BIENES Y RENTAS" => "Para registrar tu Declaración Jurada de Intereses en el sistema de la Contraloría General de la República del Perú, sigue estos pasos:\na. ✅ Sí, tengo firma digital (Bienes)\nb. ❌ No, tengo firma digital (Bienes)\nc. Video Tutorial (Bienes)\n🏠 Volver al menú principal",
        "11. DECLARACIONES JURADAS DE INTERESES" => "Para registrar tu Declaración Jurada de Intereses en el sistema de la Contraloría General de la República del Perú, sigue estos pasos:\na. ✅ Sí, tengo firma digital\nb. ❌ No, tengo firma digital\n🏠 Volver al menú principal",
        "12. BOLETAS DE PAGO" => "Para visualizar y descargar tu boleta de pago, elige una opción:\n👉 Recuerda ingresar al sistema institucional: https://Sistemas2.mpfn.gob.pe\na. 📂 Pasos dentro del sistema SIGEDOL\nb. 🎥 Ver Video Tutorial\nc. ⚠️ Soporte técnico\n🏠 Volver al menú principal",
        
        // RESPUESTAS DE SEGUNDO NIVEL
        "a. Licencias con goce de haber" => "i. Licencia por enfermedad\nii. Licencia por maternidad\niii. Licencia por paternidad\niv. Licencia por fallecimiento\nv. Licencia por enfermedad grave de familiar directo\nvi. Licencia por onomástico\nvii. Licencia por cita médica\n🔙 Volver a Licencias\n🏠 Volver al menú principal",
        "b. Licencias sin goce de haber" => "i. Licencia sin goce de haber\n🔙 Volver a Licencias\n🏠 Volver al menú principal",
        
        "i. Licencia por enfermedad" => "Presenta el certificado médico en el Área de Potencial Humano para tramitar la licencia por enfermedad.\n🔙 Volver a Licencias con goce\n🏠 Volver al menú principal",
        "ii. Licencia por maternidad" => "La licencia por maternidad comprende 98 días (49 prenatal + 49 postnatal). Comunícate con Potencial Humano para iniciar el trámite.\n🔙 Volver a Licencias con goce\n🏠 Volver al menú principal",
        "iii. Licencia por paternidad" => "La licencia por paternidad comprende 10 días hábiles a partir del alumbramiento. Comunícate con Potencial Humano.\n🔙 Volver a Licencias con goce\n🏠 Volver al menú principal",
        "iv. Licencia por fallecimiento" => "5 días hábiles por fallecimiento de familiar directo. Presenta certificado de defunción en Potencial Humano.\n🔙 Volver a Licencias con goce\n🏠 Volver al menú principal",
        "v. Licencia por enfermedad grave de familiar directo" => "Hasta 30 días por enfermedad grave de familiar directo. Presenta documentación médica en Potencial Humano.\n🔙 Volver a Licencias con goce\n🏠 Volver al menú principal",
        "vi. Licencia por onomástico" => "1 día por onomástico del trabajador. Coordínalo con tu jefe inmediato.\n🔙 Volver a Licencias con goce\n🏠 Volver al menú principal",
        "vii. Licencia por cita médica" => "Las horas de cita médica se descuentan o compensan según el reglamento interno.\n🔙 Volver a Licencias con goce\n🏠 Volver al menú principal",
        "i. Licencia sin goce de haber" => "Presenta solicitud al Área de Potencial Humano con al menos 5 días de anticipación.\n🔙 Volver a Licencias sin goce\n🏠 Volver al menú principal",
        
        "a. 🏥 SOLICITUD DE SUBSIDIO" => "i. Requisitos\nDeberá presentar una solicitud dirigida al Área de Potencial Humano a fin de iniciar el trámite de subsidio por enfermedad o maternidad.\nSe deberá adjuntar:\n• Certificado de Incapacidad Temporal para Trabajo original\n• Copia de DNI (01 copias)\n• Formulario 1040 (04 juegos debidamente suscritos)\n• Formato de carta poder (02 juegos debidamente suscritos)\n• Formato de política de privacidad para el tratamiento de datos personales. (02 juegos debidamente suscritos)\nii. 📍 Lugar de presentación:\nMesa de Partes física – Sede La Paz\niii. Descarga de formatos\n• [Formulario 1040](/siga/chatbot_potencial_humano/formatos/formulario_1040.pdf)\n• [Formato de carta poder](/siga/chatbot_potencial_humano/formatos/carta_poder.pdf)\n• [Formato de política de privacidad](/siga/chatbot_potencial_humano/formatos/politica_privacidad.pdf)\n🔙 Volver a Trámites EsSalud\n🏠 Volver al menú principal",
        "b. 💵 Solicitud de pago diferencial" => "Para iniciar el trámite, debes presentar el formato correspondiente adjuntando:\ni. Requisitos\n• Solicitud de Pago Diferencial (Formato)\n• Copia Formulario 1040 con sello de EsSalud u Hoja de consulta NIT EsSalud (proporcionado por el personal a cargo Lic. Elizabeth Ticona Rojas).\n• Recibo de pago electrónico de la entidad bancaria “voucher”.\nii. 📍 Lugar de presentación\nMesa de Partes física – Sede La Paz\niii. 📎 Descargar formato\n[Descargar formato de solicitud](/siga/chatbot_potencial_humano/formatos/formato_pago_diferencial.pdf)\n🔙 Volver a Trámites EsSalud\n🏠 Volver al menú principal",
        
        "a. Credencial – Carreras Especiales" => "Para solicitar tu credencial, puedes hacerlo en los siguientes casos:\ni. Nuevo\nDebes presentar el formato correspondiente adjuntando:\n• 2 fotografías tamaño carnet\n• Copia de DNI\n• Acta de Juramentación\n• Resolución de designación\nii. Robo – Pérdida\nDebes presentar el formato correspondiente adjuntando:\n• 2 fotografías tamaño carnet\n• Copia de DNI\n• Acta de Juramentación\n• Resolución de designación\n• Denuncia Policial correspondiente\niii. Caducidad/deterioro\n• 2 fotografías tamaño carnet\n• Copia de DNI\n• Acta de Juramentación\n• Resolución de designación\n• Copia de credencial\niv. 📎 Descargar formato\nv. 📍 Lugar de presentación\nMesa de Partes física – Sede La Paz\n🔙 Volver a Fotocheck\n🏠 Volver al menú principal",
        "b. Fotocheck" => "i. Emisión\nLa emisión del fotocheck se realiza de oficio por el Área de Potencial Humano.\n📢 El área se comunicará contigo para informarte la fecha y modalidad de entrega.\nii. Devolución\nCuando finalice tu vínculo laboral, deberás devolver el fotocheck junto con la entrega de cargo.\n📍 Presentación:\nMesa de Partes física – Sede La Paz\niii. Robo – Pérdida\nPara solicitar fotocheck por este motivo, deberás presentar un escrito adjuntando:\n📄 Denuncia Policial correspondiente\n📢 El Área de Potencial Humano se comunicará contigo para informarte la fecha y modalidad de entrega.\n🔙 Volver a Fotocheck\n🏠 Volver al menú principal",
        
        "a. 📄 Solicitar Constancia de Trabajo" => "¿Cómo deseas solicitarla?\ni. 💻 A través del sistema institucional\nPuedes realizar tu solicitud ingresando a:\n👉 https://Sistemas2.mpfn.gob.pe\nPasos:\n• Ingresa con tu usuario institucional.\n• Seleccione el Modulo de Emisión de Constancias\n• Elige el tipo de constancia (Simple, Histórica, Personalizada).\n• Envía tu solicitud.\n• Recibirás la confirmación en tu correo registrado\n📌 Importante:\nSi tienes problemas de acceso, comunícate con la Mesa de Ayuda del Área de Tecnologías de la Información:\n📞 937 461 856\nii. 📝 Mediante solicitud escrita\nSi no puedes acceder al sistema, puedes presentar una solicitud simple \npresentado por CEA –Área de Potencial Humano Arequipa – Gonzalo Pacheco Apaza Gonzalo\n📧 Correo electrónico: mesap.gerencia.aqp@mpfn.gob.pe\n📎 Descargar modelo de solicitud (WORD)\niii. 🎥 Ver video tutorial\nPuedes revisar el tutorial para realizar tu solicitud en el sistema institucional:\n📺 Ver video\niv. Descargar modelo de solicitud\n📎 Descargar modelo de solicitud (WORD)\n🔙 Volver a Constancias\n🏠 Volver al menú principal",
        "b. 📜 Solicitar Certificado de Trabajo" => "El certificado de trabajo se emite al término del vínculo laboral.\ni. Requisitos:\n📝 Solicitud simple\nii. ¿Cómo presentarlo?\nPuedes hacerlo mediante:\n📍 Mesa de Partes física – Sede La Paz\n📧 Correo electrónico: mesap.gerencia.aqp@mpfn.gob.pe\n📎 Descargar modelo de solicitud (WORD)\n🔙 Volver a Constancias\n🏠 Volver al menú principal",
        
        "a. Programación de vacaciones anual" => "Se realiza una vez al año, generalmente a fin de año.\nLa programación se realiza a través de Sistemas2.mpfn.gob.pe\n🎥 Videotutorial:\n• Para jefes: [Ver videotutorial jefe](https://drive.google.com/drive/folders/1abhH2FikOVARPnx2w0n3leTnITKqX8de)\n• Para trabajadores: [Ver videotutorial trabajador](https://drive.google.com/drive/folders/1abhH2FikOVARPnx2w0n3leTnITKqX8de)\n🔙 Volver a Vacaciones\n🏠 Volver al menú principal",
        "b. Reprogramación de vacaciones" => "i. Solicitud de reprogramación\n• Presentar el escrito hasta el quinto día hábil anterior al inicio de tus vacaciones.\n• Debe contar con V°B° del Jefe Inmediato y firma del solicitante.\n• Presentarlo mediante la Carpeta Electrónica Administrativa (CEA) – - Área de Potencial Humano Arequipa (sólo al personal indicado).\n• En el asunto colocar: Reprogramación de Vacaciones -(Apellidos y Nombres)\nii. Recomendaciones\n• La suma de todos los periodos fraccionados no puede superar 30 días calendarios.\n• No se pueden tomar más de 4 días hábiles por semana de los 7 días hábiles fraccionables.\n• Si el periodo inicia o termina un viernes, los sábados y domingos siguientes también se computan.\n• El acuerdo de fraccionamiento debe ser previo al disfrute de las vacaciones y debe incluir las fechas originales y nuevas.\niii. Modelo de Solicitud\n📎 Descargar modelo de solicitud (WORD)\n🔙 Volver a Vacaciones\n🏠 Volver al menú principal",
        "c. Adelanto de vacaciones" => "Para verificar si puedes acceder al adelanto de vacaciones, comunícate al:\n📞 949 305 573\n🔙 Volver a Vacaciones\n🏠 Volver al menú principal",
        
        "a. Entrega de Cargo" => "i. Directiva de Entrega de cargo:\nAplica al cese, traslado o cambio de funciones.\nDebes presentar el acta de entrega en el Área de Potencial Humano.\n📎 Solicita el formato de entrega al Área de Potencial Humano.\n🔙 Volver a Vinculación de personal\n🏠 Volver al menú principal",
        "i. Directiva de Entrega de cargo" => "La directiva de entrega aplica al cese, traslado o cambio de funciones.\nPresenta el acta de entrega en Potencial Humano.\n📎 Solicita el formato al Área de Potencial Humano.\n🔙 Volver a Entrega de Cargo\n🏠 Volver al menú principal",
        
        // DECLARACION BIENES
        "a. ✅ Sí, tengo firma digital (Bienes)" => "• Ingresa al sistema oficial: https://apps1.contraloria.gob.pe/ddjj/ o https://appdji.contraloria.gob.pe/djic/\n• Inicia sesión.\n• Completa los campos solicitados.\n• Firma\n• Una vez firmado, envía una captura de pantalla al número 959860944\n• 🎥 Puedes ver el video tutorial aquí: https://www.youtube.com/watch?v=TNK0fJbIU_8\n🔙 Volver a DJ de Ingresos\n🏠 Volver al menú principal",
        "b. ❌ No, tengo firma digital (Bienes)" => "• Ingresa al sistema oficial: https://apps1.contraloria.gob.pe/ddjj/ o comunícate con TI 937461856\n• Inicia sesión.\n• Completa los campos solicitados.\n• Imprime 3 ejemplares (1 cargo, 2 serán enviados a Lima)\n• Firma todas las hojas\n• Acércate al Área de Potencial Humano a fin de entregar los formatos.\n🔙 Volver a DJ de Ingresos\n🏠 Volver al menú principal",
        "c. Video Tutorial (Bienes)" => "🎥 Puedes ver el video tutorial aquí:\nhttps://www.youtube.com/watch?v=j1UzD122NlA\n🔙 Volver a DJ de Ingresos\n🏠 Volver al menú principal",
        
        // DECLARACIONES JURADAS DE INTERESES
        "a. ✅ Sí, tengo firma digital" => "• Ingresa al sistema oficial: https://apps1.contraloria.gob.pe/ddjj/ o https://appdji.contraloria.gob.pe/djic/\n• Inicia sesión.\n• Completa los campos solicitados.\n• Firma\n• Una vez firmado, envía una captura de pantalla al número 959860944\n• 🎥 Puedes ver el video tutorial aquí: https://www.youtube.com/watch?v=TNK0fJbIU_8\n🔙 Volver a DJ de Intereses\n🏠 Volver al menú principal",
        "b. ❌ No, tengo firma digital" => "• Ingresa al sistema oficial: https://apps1.contraloria.gob.pe/ddjj/ o comunícate con TI 937461856\n• Inicia sesión.\n• Completa los campos solicitados.\n• Imprime 3 ejemplares (1 cargo, 2 serán enviados a Lima)\n• Firma todas las hojas\n• Acércate al Área de Potencial Humano a fin de entregar los formatos.\n🔙 Volver a DJ de Intereses\n🏠 Volver al menú principal",
        
        // BOLETAS DE PAGO
        "a. 📂 Pasos dentro del sistema SIGEDOL" => "i. Selecciona Sistema de Gestión de Documentos Laborales – SIGEDOL.\nii. Elige una de las siguientes opciones:\n• 📌 Pendientes: Verás las boletas que aún no has visualizado.\n• 📁 Históricas: Verás las boletas ya visualizadas.\niii. Selecciona la boleta que deseas consultar.\niv. Descárgala en tu dispositivo.\nv. Si deseas conocer el detalle de los conceptos puedes comunicarte al 959 860 944\n🔙 Volver a Boletas de pago\n🏠 Volver al menú principal",
        "b. 🎥 Ver Video Tutorial" => "📺 Ver video tutorial\n🔙 Volver a Boletas de pago\n🏠 Volver al menú principal",
        "c. ⚠️ Soporte técnico" => "Si no visualizas el sistema SIGEDOL dentro de Sistemas2, comunícate con:\n📞 Mesa de Ayuda – Área de Tecnologías de la Información 937 461 856\n🔙 Volver a Boletas de pago\n🏠 Volver al menú principal"
    ];

    // REFERENCIAS DE RETROCESO (Volver atrás dinámicos)
    $opcionesEstaticas["🔙 Volver a Licencias"] = $opcionesEstaticas["2. LICENCIAS"];
    $opcionesEstaticas["🔙 Volver a Licencias con goce"] = $opcionesEstaticas["a. Licencias con goce de haber"];
    $opcionesEstaticas["🔙 Volver a Licencias sin goce"] = $opcionesEstaticas["b. Licencias sin goce de haber"];
    $opcionesEstaticas["🔙 Volver a Trámites EsSalud"] = $opcionesEstaticas["3. TRÁMITES ANTE ESSALUD Y EL ÁREA DE POTENCIAL HUMANO"];
    $opcionesEstaticas["🔙 Volver a Fotocheck"] = $opcionesEstaticas["4. FOTOCHECK Y CREDENCIAL"];
    $opcionesEstaticas["🔙 Volver a Constancias"] = $opcionesEstaticas["5. EMISIÓN DE CONSTANCIAS Y CERTIFICADOS DE TRABAJO"];
    $opcionesEstaticas["🔙 Volver a Vacaciones"] = $opcionesEstaticas["7. VACACIONES"];
    $opcionesEstaticas["🔙 Volver a Vinculación de personal"] = $opcionesEstaticas["9. VINCULACIÓN Y DESVINCULACIÓN DE PERSONAL"];
    $opcionesEstaticas["🔙 Volver a Entrega de Cargo"] = $opcionesEstaticas["a. Entrega de Cargo"];
    $opcionesEstaticas["🔙 Volver a DJ de Ingresos"] = $opcionesEstaticas["10. DECLARACION JURADA DE INGRESOS Y DE BIENES Y RENTAS"];
    $opcionesEstaticas["🔙 Volver a DJ de Intereses"] = $opcionesEstaticas["11. DECLACIONES JURADAS DE INTERESES"]; // wait, the original string was "11. DECLARACIONES JURADAS DE INTERESES"
    $opcionesEstaticas["🔙 Volver a Boletas de pago"] = $opcionesEstaticas["12. BOLETAS DE PAGO"];
PHPBLOCK;

$newBlock = str_replace("DECLACIONES", "DECLARACIONES", $newBlock);

$newContent = substr($content, 0, $startPos) . $newBlock . "\n" . substr($content, $endPos + 2);

file_put_contents($file, $newContent);
echo "Replaced array.\n";

?>
