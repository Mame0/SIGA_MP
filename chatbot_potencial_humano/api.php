<?php
/**
 * API Backend del Chatbot
 * Recibe mensajes del frontend y devuelve respuestas de la IA
 */

// Cargar configuración PRIMERO (antes de usar cualquier constante)
require_once 'config.php';
require_once CHATBOT_DB_CLASS_PATH;
require_once 'ChatbotAI.php';

// Configuración de errores
error_reporting(E_ALL);
ini_set('display_errors', CHATBOT_DEBUG_MODE ? 1 : 0);

// Headers CORS
if (CHATBOT_ALLOW_CORS) {
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: POST, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type');
}

header('Content-Type: application/json; charset=utf-8');

// Manejar preflight OPTIONS
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Solo aceptar POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Método no permitido']);
    exit;
}

// Función para responder JSON
function responderJSON($data, $httpCode = 200) {
    http_response_code($httpCode);
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

try {
    // Leer el cuerpo de la solicitud
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);
    
    if (!$data) {
        responderJSON(['error' => 'Datos inválidos'], 400);
    }
    
    // Validar mensaje
    $mensaje = isset($data['mensaje']) ? trim($data['mensaje']) : '';
    
    if (empty($mensaje)) {
        responderJSON(['error' => 'El mensaje no puede estar vacío'], 400);
    }
    
    if (strlen($mensaje) > CHATBOT_MAX_LENGTH) {
        responderJSON(['error' => 'El mensaje es demasiado largo'], 400);
    }
    
    // Obtener o crear sesión
    session_start();
    if (!isset($_SESSION['chatbot_sesion_id'])) {
        $_SESSION['chatbot_sesion_id'] = uniqid('chat_', true);
    }
    $sesionId = $_SESSION['chatbot_sesion_id'];
    
    // Rate limiting simple (opcional)
    if (!isset($_SESSION['chatbot_contador'])) {
        $_SESSION['chatbot_contador'] = 0;
        $_SESSION['chatbot_hora_inicio'] = time();
    }
    
    // Resetear contador cada hora
    if (time() - $_SESSION['chatbot_hora_inicio'] > 3600) {
        $_SESSION['chatbot_contador'] = 0;
        $_SESSION['chatbot_hora_inicio'] = time();
    }
    
    $_SESSION['chatbot_contador']++;
    
    if ($_SESSION['chatbot_contador'] > CHATBOT_RATE_LIMIT) {
        responderJSON([
            'error' => 'Has excedido el límite de mensajes por hora. Por favor, intenta más tarde.'
        ], 429);
    }
    
    // Conectar a la base de datos
    $db = new Db();
    
    // Crear instancia del chatbot
    $chatbot = new ChatbotAI($db);
    
    // Obtener historial de conversación
    $historial = $chatbot->obtenerHistorial($sesionId, 5); // Últimos 5 mensajes
    
    // --- ESTRICTO CONTROL DE MENÚ POR PHP (BYPASS DE IA) ---
// Si el usuario hace clic en una opción predefinida, respondemos instantáneamente
$menuPrincipal = '1. CONTROL DE ASISTENCIA
2. LICENCIAS
3. BIENESTAR DE PERSONAL
4. CREDENCIAL Y FOTOCHECK
5. EMISIÓN DE CONSTANCIAS Y CERTIFICADOS DE TRABAJO
6. VACACIONES
7. DECLARACIÓN JURADA DE INGRESOS Y DE BIENES Y RENTAS
8. DECLARACIÓN JURADA DE INTERESES
9. BOLETAS DE PAGO
10. ENTREGA DE CARGO';

$opcionesEstaticas = array (
  'Volver al menú principal' => $menuPrincipal,
  '🏠 Volver al menú principal' => $menuPrincipal,
  '1. CONTROL DE ASISTENCIA' => 'a. Boleta de Permiso
b. Compensación por trabajo en sobretiempo por toda la jornada laboral
c. Autorización de ingreso en días de descanso obligatorio, feriados y días no laborables.
d. Récord de Asistencia
e. Fecha de cierre de asistencia
f. Comunicados
g. Contacto en casos de problemas con el reloj biométrico
h. Derivación de Documentos mediante Carpeta Electrónica Administrativa CEA
i. Volver al menú principal.',
  'i. Volver al menú principal.' => $menuPrincipal,

  'a. Boleta de Permiso' => 'Todas las boletas de permiso que se ejecuten a primera hora de la jornada laboral, es decir que inicien a partir de las 08:00 horas deberán de ser presentadas en vigilancia de cada sede o carpeta electrónica administrativa - CEA para servidores que laboran en Provincias, el día anterior al permiso debidamente firmado por su jefe inmediato, deben de marcar (x) acorde al motivo del permiso:
i. Permiso Particular
ii. Comisión de Servicios
iii. Compensación
iv. Atención de Salud
v. Registro fuera del horario establecido
vi. Omisión de Registro de Marcación
🔙 Volver a Asistencia
🏠 Volver al menú principal',

  'i. Permiso Particular' => '• Si inicia después de la hora de ingreso, la boleta debe presentarse en el día.
• Debe efectuar registro de marcación antes y después de la ejecución del permiso.
• Son establecidas por horas.
• No puede superar las 8 horas en el día, ni en el mes.
[📎 Descargar formato de boleta de permiso](https://drive.google.com/file/d/1VaQ6mmBpQOn3RtMWFWm_MunNmfjIyDmI/view?usp=drive_link)
🔙 Volver a Boleta de Permiso
🏠 Volver al menú principal',

  'ii. Comisión de Servicios' => '• Si inicia después de la hora de ingreso, la boleta debe presentarse en el día.
• Debe efectuar registro de marcación antes y después de la ejecución del permiso.
• El trabajador deberá presentar al retorno del permiso, el documento debidamente sellado, consignando la FIRMA, FECHA y HORA de la atención por la persona de la entidad a donde se desplazó
[📎 Ver modelo](https://drive.google.com/file/d/1OfhvRb-0MQKACH6vuhCnJ1wlnKfS7eyt/view?usp=drive_link) 
[📎 Descargar formato de boleta de permiso](https://drive.google.com/file/d/1VaQ6mmBpQOn3RtMWFWm_MunNmfjIyDmI/view?usp=drive_link)
🔙 Volver a Boleta de Permiso
🏠 Volver al menú principal',

  'iii. Compensación' => '• Para la presentación con boleta de permiso son establecidas por horas y se debe adjuntar el formato correspondiente del sobretiempo. El mismo que debió ser presentado con anterioridad al permiso solicitado acorde al procedimiento de “trabajos en sobretiempo”.
• No debe de superar las 16 horas al mes.
• Debe efectuar registro de marcación antes y después de la ejecución del permiso.
• Acorde al convenio colectivo, puede presentarse el mismo día hasta en dos oportunidades no consecutivas en el mes en caso el permiso sea al inicio de la jornada.
[📎 Ver modelo](https://drive.google.com/file/d/16Ax25zHx2TqNM44sLYChpQRqHICJFOC8/view?usp=drive_link)
[📎 Descargar formato de boleta de permiso](https://drive.google.com/file/d/1VaQ6mmBpQOn3RtMWFWm_MunNmfjIyDmI/view?usp=drive_link)
[📎 Descargar autorización en sobretiempo](https://drive.google.com/file/d/1NuYeLG0JEmCIX-ATn--bHrrm5Ajz0s-w/view?usp=drive_link)
🔙 Volver a Boleta de Permiso
🏠 Volver al menú principal',

  'iv. Atención de Salud' => '• Permiso por cita médica del servidor: 
    a. Se otorga hasta un máximo de 04 horas y en caso de exámenes especiales hasta por 07 horas. 
    b. Requiere la presentación de la boleta de permiso debidamente autorizada y la constancia de atención médica. 
    c. En caso de no adjuntar la constancia de atención médica, será considerado como permiso particular.
• Permiso por atención médica de emergencia del familiar directo: 
    a. El permiso se concede únicamente en casos de EMERGENCIA, se otorga hasta cuatro (4) horas al día, y en caso de exámenes especiales hasta siete (7) horas dentro de la jornada laboral. 
    b. Requiere la presentación de la boleta de permiso debidamente autorizada y la constancia de atención médica del ÁREA DE EMERGENCIA, como sustento de haber sido atendida en dicha área, caso contrario será considerado como permiso particular
[📎 Descargar formato de boleta de permiso](https://drive.google.com/file/d/1VaQ6mmBpQOn3RtMWFWm_MunNmfjIyDmI/view?usp=drive_link)
🔙 Volver a Boleta de Permiso
🏠 Volver al menú principal',

  'v. Registro fuera del horario establecido' => '• La boleta de permiso se presenta en el mismo día o hasta 24 horas posteriores al día de la incidencia cuando el servidor tiene registro de marcación de ingreso entre las 8:16 y 8:59 Horas de lunes a viernes (jornada laboral).
• Se pueden presentar hasta cinco (05) boletas en el mes.
• Debe consignar “x” si es primera, segunda o tercera boleta; y, de ser cuarta o quinta boleta, debe consignar en el detalle.
[📎 Descargar formato de boleta de permiso](https://drive.google.com/file/d/1VaQ6mmBpQOn3RtMWFWm_MunNmfjIyDmI/view?usp=drive_link)
[📎 Comunicado de registro fuera de hora convenio colectivo](https://drive.google.com/file/d/1PdtlC0TK_29XCQNeCXFxKwDMQHEUdLg3/view?usp=drive_link)
🔙 Volver a Boleta de Permiso
🏠 Volver al menú principal',

  'vi. Omisión de Registro de Marcación' => '• Acorde al convenio colectivo debe ser presentado hasta 48 horas siguientes de ocurrido el hecho.
• Justifica la marcación de asistencia de ingreso o salida de lunes a viernes (jornada laboral) y de manera excepcional por única vez en el mes.
[📎 Descargar formato de boleta de permiso](https://drive.google.com/file/d/1VaQ6mmBpQOn3RtMWFWm_MunNmfjIyDmI/view?usp=drive_link)
[📎 Modelo de boleta por omisión](https://drive.google.com/file/d/1Eat2z7zPWaHChnKvH75R6kfFmMtiFHXc/view?usp=drive_link)
🔙 Volver a Boleta de Permiso
🏠 Volver al menú principal',

  'b. Compensación por trabajo en sobretiempo por toda la jornada laboral' => '• Para la contabilización del sobretiempo el trabajador deberá laborar como mínimo (01) hora y máxima (05) horas por día.
• La compensación podrá hacerse efectivo a partir del día siguiente de haberse realizado dicho trabajo hasta treinta (30) días hábiles posteriores, previa autorización del jefe inmediato
• Debe de presentarse hasta un (01) día anterior hábil como plazo máximo.
• No debe de superarse las 16 horas al mes
• La solicitud debe contar con el visto bueno de su jefe inmediato
• Presentarlo mediante la Carpeta Electrónica Administrativa (CEA) – Área de Potencial Humano Arequipa - Bustinza Sierra Fanny en caso de personal que labora en Arequipa.
Flores Mamani Eliant personal que labora en provincias
Los escritos sólo deberán de ser enviados al personal indicado.
[📎 Modelo Solicitud Compensación 728](https://docs.google.com/document/d/1A3pUQoFrNGy2L8twcAOUYD0Q-aeOUHNx/edit?usp=sharing&ouid=111203226793113830230&rtpof=true&sd=true)
[📎 Modelo Solicitud Compensación CAS](https://docs.google.com/document/d/1Z88rPo26ESJR43P3OEJqVq7X20462RNI/edit?usp=drive_link&ouid=111203226793113830230&rtpof=true&sd=true)
[📎 Modelo de llenado de formato de sobretiempo](https://drive.google.com/file/d/1Lvh43r3nDECcelt5lPtRPpnkVPOQ7mkO/view?usp=drive_link)
[📎 Autorización en Sobretiempo](https://drive.google.com/file/d/1NuYeLG0JEmCIX-ATn--bHrrm5Ajz0s-w/view?usp=drive_link)
🔙 Volver a Asistencia
🏠 Volver al menú principal',

  'c. Autorización de ingreso en días de descanso obligatorio, feriados y días no laborables.' => 'Debe ser solicitado por el jefe inmediato, acreditando la necesidad de servicio ante la Gerencia Administrativa, el mismo debe estar suscrito por los servidores, se presenta hasta un (01) día hábil antes de la realización del trabajo, para el acceso a las instalaciones.
[📎 Modelo de solicitud de autorización de ingreso](https://docs.google.com/document/d/1T4KgMSd9cHtQc-czXMf2oJSkIc82wwsI/edit?usp=drive_link&ouid=111203226793113830230&rtpof=true&sd=true)
🔙 Volver a Asistencia
🏠 Volver al menú principal',

  'd. Récord de Asistencia' => 'Para visualizar y revisar tu récord de asistencia, sigue estos pasos:
👉 Ingresa al sistema institucional: [https://Sistemas2.mpfn.gob.pe](https://sistemas2.mpfn.gob.pe/Sistemas2/login.aspx)
i. Selecciona Sistema de Récord de Asistencia – ASIST
ii. Selecciona Periodo Asistencial
[iii. 🎥 Ver video tutorial](https://drive.google.com/file/d/1mw25yjpB_MhgEwiGxawatADUUGXzRvyi/view?usp=drive_link)
iv. ⚠️ Soporte técnico
Si no visualizas el módulo ASIST dentro de Sistemas2, comunícate con:
📞 Mesa de Ayuda – Área de Tecnologías de la Información
[937 461 856](https://wa.me/51937461856?text=Hola%20quiero%20más%20información)
🔙 Volver a Asistencia
🏠 Volver al menú principal',

  'e. Fecha de cierre de asistencia' => '• Cierre para planilla de MAYO 2026 
Incidencias del 26 de marzo al 25 de abril 
• Cierre para planilla de JUNIO 2026 
Incidencias del 26 de abril al 25 de mayo
• Cierre para planilla de JULIO 2026 
Incidencias del 26 de mayo al 25 de junio
• Cierre para planilla de AGOSTO 2026 
Incidencias del 26 de junio al 25 de julio
• Cierre para planilla de SETIEMBRE 2026 
Incidencias del 26 de julio al 25 de agosto
• Cierre para planilla de OCTUBRE 2026 
Incidencias del 26 de agosto al 25 de setiembre
• Cierre para planilla de NOVIEMBRE 2026 
Incidencias del 26 de setiembre al 25 de octubre
• Cierre para planilla de DICIEMBRE 2026 
Incidencias del 26 de octubre al 25 de noviembre
• Cierre para planilla de ENERO 2027 
Incidencias del 26 de noviembre al 25 de diciembre
🔙 Volver a Asistencia
🏠 Volver al menú principal',

  'f. Comunicados' => 'A continuación verás todos los comunicados vigentes:
[i. 📎 Flyer Convenio Colectivo 2025-2026 Asistencia](https://drive.google.com/file/d/1PdtlC0TK_29XCQNeCXFxKwDMQHEUdLg3/view?usp=drive_link)
[ii. 📎 Facilidades sobre el registro de asistencia 20 FEB](https://drive.google.com/file/d/1pjiXBI_sy_J9w2H5fe9Y-7Xw5lLgrCS3/view?usp=drive_link)
[iii. 📎 Recuperación horas dejadas de laborar Tolerancia 23 FEB](https://drive.google.com/file/d/1NduefHEUAUA0k4p-s1Eg4rKLIv8HHydH/view?usp=drive_link)
[iv. 📎 Facilidades sobre el registro de asistencia 11 MAR](https://drive.google.com/file/d/1cP_j0XMa9Yz_XOLu5SFDWneSqwDKvgos/view?usp=drive_link)
[v. 📎 Facilidades sobre el registro de asistencia 12 MAR](https://drive.google.com/file/d/1ogsrzwVAr-W-5BuL52lIB0-Nh2GyyTEg/view?usp=drive_link)
🔙 Volver a Asistencia
🏠 Volver al menú principal',

  'g. Contacto en casos de problemas con el reloj biométrico' => 
  'En caso de que se registren casos de cortes de luz y tenga problemas con la marcación, deberá comunicarse a: 
  [959371597](https://wa.me/51959371597?text=Hola%20quiero%20informar%20problemas%20con%20el%20reloj%20biométrico)
🔙 Volver a Asistencia
🏠 Volver al menú principal',

  'h. Derivación de Documentos mediante Carpeta Electrónica Administrativa CEA' => 'En el Asunto colocar: MOTIVO - APELLIDOS Y NOMBRES
Derivar al siguiente personal:
• Bustinza Sierra, Fanny (para el personal que labora en Arequipa)
• Flores Mamani, Eliant Yanira (para el personal que labora en provincias)
🔙 Volver a Asistencia
🏠 Volver al menú principal',

  '🔙 Volver a Asistencia' => 'a. Boleta de Permiso
b. Compensación por trabajo en sobretiempo por toda la jornada laboral
c. Autorización de ingreso en días de descanso obligatorio, feriados y días no laborables.
d. Récord de Asistencia
e. Fecha de cierre de asistencia
f. Comunicados
g. Contacto en casos de problemas con el reloj biométrico
h. Derivación de Documentos mediante Carpeta Electrónica Administrativa CEA
i. Volver al menú principal.',

  '🔙 Volver a Boleta de Permiso' => 'i. Permiso Particular
ii. Comisión de Servicios
iii. Compensación
iv. Atención de Salud
v. Registro fuera del horario establecido
vi. Omisión de Registro de Marcación
🔙 Volver a Asistencia
🏠 Volver al menú principal',

// ---------------------- 2. LICENCIAS ------------------------
  '2. LICENCIAS' => 'a. Licencias con goce de haber
b. Licencias sin goce de haber
c. Volver al menú principal',
  'c. Volver al menú principal' => $menuPrincipal,

  'a. Licencias con goce de haber' => 'i. Licencia por enfermedad
ii. Licencia por maternidad
iii. Licencia por paternidad
iv. Licencia por fallecimiento de familiar directo
v. Licencia por enfermedad grave de familiar directo
vi. Licencia por onomástico
vii. Licencia por cita médica
🔙 Volver a Licencias
🏠 Volver al menú principal',

  'b. Licencias sin goce de haber' => 'i. Licencia sin goce de haber
🔙 Volver a Licencias
🏠 Volver al menú principal',

  'i. Licencia por enfermedad' => '• Requisitos de acuerdo al establecimiento de salud que emite el certificado médico:
a. Seguro Social de Salud - EsSalud
[📎 Formato Interno de descanso médico](https://drive.google.com/file/d/1C8qetbrFvLhnHwiDTU8hk5bui_EQeaqL/view?usp=drive_link)
• Copia fedateada del Certificado de Incapacidad Temporal para el Trabajo - CITT
b. Ministerio de Salud del Perú - MINSA, Clínicas Privadas, Entidades Prestadoras de Salud - EPS, Sanidad de las Fuerzas Armadas, Sanidad de la Policía Nacional del Perú o Consultorios Médicos Particulares
[📎 Formato Interno de descanso médico](https://drive.google.com/file/d/1C8qetbrFvLhnHwiDTU8hk5bui_EQeaqL/view?usp=drive_link)
• Copia fedateada del descanso médico o Certificado Médico en especie valorada del Colegio Médico del Perú
• Copia fedateada del comprobante de pago de la atención médica, de corresponder
• Copia fedateada de la receta médica
🔙 Volver a Licencias con goce
🏠 Volver al menú principal',

  'ii. Licencia por maternidad' => '• Requisitos de acuerdo al establecimiento de salud que emite el certificado médico:
a. Seguro Social de Salud - EsSalud
[📎 Formato Interno de descanso médico](https://drive.google.com/file/d/1C8qetbrFvLhnHwiDTU8hk5bui_EQeaqL/view?usp=drive_link)
• Copia fedateada del Certificado de Incapacidad Temporal para el Trabajo - CITT por Maternidad
b. Ministerio de Salud del Perú - MINSA, Clínicas Privadas, Entidades Prestadoras de Salud - EPS, Sanidad de las Fuerzas Armadas, Sanidad de la Policía Nacional del Perú o Consultorios Médicos Particulares
[📎 Formato Interno de descanso médico](https://drive.google.com/file/d/1C8qetbrFvLhnHwiDTU8hk5bui_EQeaqL/view?usp=drive_link)
• Copia fedateada del descanso médico o Certificado Médico en especie valorada
• Consideraciones a tener en cuenta respecto a las solicitudes de licencia por enfermedad y maternidad:
1. El expediente de licencia por enfermedad debe ser presentado dentro de las cuarenta y ocho (48) horas de su emisión, pudiendo delegar a un familiar o a un tercero cercano para el trámite correspondiente.
2. La presentación es en forma física a través de la mesa de partes de Gerencia y Presidencia (Ventanilla N° 08)
🔙 Volver a Licencias con goce
🏠 Volver al menú principal',

  'iii. Licencia por paternidad' => '[📎 Ver Cartilla Informativa](https://drive.google.com/file/d/1ve7-k7SF2dSaV9qIt60SW0nQL_Sf7-Ma/view?usp=sharing)
• Requisitos:
a. La solicitud será presentada mediante la Carpeta Electrónica Administrativa (CEA) – Área de Potencial Humano Arequipa-Pacheco Apaza, Gonzalo Alexander (sólo al personal indicado).
b. Copia fedateada del acta de nacido vivo o partida de nacimiento del menor de corresponder.
c. Copia del informe u orden de alta de la madre o del recién nacido de corresponder.
🔙 Volver a Licencias con goce
🏠 Volver al menú principal',

  'iv. Licencia por fallecimiento de familiar directo' => '[📎 Ver Cartilla Informativa](https://drive.google.com/file/d/1gClYEZg7lCo7n3GZCu-7pUbfuyxhl3-M/view?usp=sharing)
• Requisitos:
a. La solicitud será presentada mediante la Carpeta Electrónica Administrativa (CEA) – Área de Potencial Humano Arequipa-Pacheco Apaza, Gonzalo Alexander (sólo al personal indicado).
b. Adjuntar el acta o certificado de Defunción
c. Copia fedateada del Documento que acredite la vinculación de parentesco
🔙 Volver a Licencias con goce
🏠 Volver al menú principal',

  'v. Licencia por enfermedad grave de familiar directo' => '[📎 Ver Cartilla Informativa](https://drive.google.com/file/d/1wR7xXfJBppMQBzv95eRW5SWPJo8mQkTE/view?usp=sharing)
• Requisitos:
a. Escrito presentado mediante la Carpeta Electrónica Administrativa (CEA) – Área de Potencial Humano Arequipa-Pacheco Apaza, Gonzalo Alexander (sólo al personal indicado).
b. Documento que acredite la vinculación de parentesco
c. [📎 Formato de Certificado Médico Ley N° 30012](https://drive.google.com/file/d/1h1JhooSTC3DGUqZd2aXy34X2YLMt2Spb/view?usp=sharing)
🔙 Volver a Licencias con goce
🏠 Volver al menú principal',

  'vi. Licencia por onomástico' => 'Suspensión o reprogramación
• Se suspende solo por necesidad de servicio y su programación puede diferirse hasta (07) siete días calendarios posteriores de la fecha programada.
• El escrito de reprogramación se presenta hasta con 24 horas de antelación de la fecha programada, y con visto bueno del jefe inmediato.
• Escrito presentado mediante la Carpeta Electrónica Administrativa (CEA) – Área de Potencial Humano Arequipa-Pacheco Apaza, Gonzalo Alexander (sólo al personal indicado).
🔙 Volver a Licencias con goce
🏠 Volver al menú principal',

  'vii. Licencia por cita médica' => '• Requisitos
• Escrito indicando lugar, fecha y hora de la cita médica, presentado mediante la Carpeta Electrónica Administrativa (CEA) – Área de Potencial Humano Arequipa-Pacheco Apaza, Gonzalo Alexander (sólo al personal indicado).
• Una vez culminada la atención remitir la constancia de atención señalando el número de expediente de la anterior solicitud.
🔙 Volver a Licencias con goce
🏠 Volver al menú principal',

  'i. Licencia sin goce de haber' => '[📎 Ver Cartilla Informativa](https://drive.google.com/file/d/1JjgQaj54BnIHuISbPRtmWwnYzt3Mc73R/view?usp=sharing)
• Requisitos
a. Solicitud con el visto bueno de su jefe inmediato
b. Presentar solicitud 5 días hábiles antes del inicio de la licencia
c. Escrito presentado mediante la Carpeta Electrónica Administrativa (CEA) – Área de Potencial Humano Arequipa-Pacheco Apaza, Gonzalo Alexander (sólo al personal indicado).
🔙 Volver a Licencias sin goce
🏠 Volver al menú principal',

  '🔙 Volver a Licencias con goce' => 'i. Licencia por enfermedad
ii. Licencia por maternidad
iii. Licencia por paternidad
iv. Licencia por fallecimiento de familiar directo
v. Licencia por enfermedad grave de familiar directo
vi. Licencia por onomástico
vii. Licencia por cita médica
🔙 Volver a Licencias
🏠 Volver al menú principal',

  '🔙 Volver a Licencias sin goce' => 'i. Licencia sin goce de haber
🔙 Volver a Licencias
🏠 Volver al menú principal',

  '🔙 Volver a Licencias' => 'a. Licencias con goce de haber
b. Licencias sin goce de haber
c. Volver al menú principal',

// ---------------------- 3. BIENESTAR DE PERSONAL ------------------------
  '3. BIENESTAR DE PERSONAL' => 'a. TRÁMITES ANTE ESSALUD Y EL ÁREA DE POTENCIAL HUMANO
b. SEGUROS
c. Volver al menú principal',
  'c. Volver al menú principal' => $menuPrincipal,

  'a. TRÁMITES ANTE ESSALUD Y EL ÁREA DE POTENCIAL HUMANO' => 'i. REGISTRO DE DERECHOHABIENTES
ii. 🏥 SOLICITUD DE SUBSIDIO
iii. 💵Solicitud de pago diferencial
iv. Postergación de la licencia por maternidad
🔙 Volver a Bienestar de personal
🏠 Volver al menú principal',

  'i. REGISTRO DE DERECHOHABIENTES' => '“Para que los familiares accedan a EsSalud, el trabajador debe presentar documentos según el tipo de familiar 👇” 
[📎 Ver Cartilla Informativa](https://drive.google.com/file/d/1W8GX3DYnT-YIJbEdpzVNQi6nV7c7CoJm/view?usp=drive_link)
• Requisitos
a. Solicitud dirigida al Área de Potencial Humano 
b. Formulario 1010 (02 juegos, solo firmar, sin llenar)
c. Formato de política de privacidad para el tratamiento de datos personales. (01 juego, solo firmar, sin llenar)
d. Copia DNI Titular
e. Documentos según tipo de familiar (ver cartilla)
f. 📞 [950054080](https://wa.me/51950054080?text=Hola%20quiero%20más%20información) para consultas 
• 📍 Lugar de presentación
Mesa de Partes de Gerencia y Presidencia (Ventanilla N° 8) - Sede la Paz 320.
• 📎 Descargar formatos
[📎 Formulario 1010](https://drive.google.com/file/d/1vA6sg-_-CCl3Ynzwkcpmf6-62LQLE29H/view?usp=drive_link)
[📎 Formato de política de privacidad para el tratamiento de datos personales](https://drive.google.com/file/d/1A175vNlnjQtiak7LJGQeqMm4dLgKJ5xu/view?usp=sharing)
🔙 Volver a Trámites EsSalud
🏠 Volver al menú principal',

  'ii. 🏥 SOLICITUD DE SUBSIDIO' => 'Las prestaciones económicas comprenden los subsidios por incapacidad temporal, maternidad y lactancia.
[📎 Ver Cartilla Informativa](https://drive.google.com/file/d/1E7LzFiqV4cUnWyKmG_-GpfTUJNbrGNtv/view?usp=sharing)
• Requisitos subsidio por incapacidad y maternidad
a. Solicitud dirigida al Área de Potencial Humano
b. Se deberá adjuntar:
- Certificado de Incapacidad Temporal para Trabajo original
- Copia de DNI (01 copia)
- Formulario 1040 (02 juegos, solo firmar, sin llenar)
- Formato de carta poder (02 juegos, solo firmar, sin llenar)
- Formato de política de privacidad para el tratamiento de datos personales. (02 juegos, solo firmar, sin llenar)
• 📍 Lugar de presentación:
Mesa de Partes de Gerencia y Presidencia (Ventanilla N° 8) - Sede la Paz 320.
• Descarga de formatos
[📎 Formulario 1040](https://drive.google.com/file/d/17PIbnotugh7Fc21f6twEAfLSyDAHAW-1/view?usp=sharing)
[📎 Formato de carta poder](https://drive.google.com/file/d/1AjTaufySMLl8-nhBL00CpXGH7XwLEtmq/view?usp=sharing)
[📎 Formato de política de privacidad para el tratamiento de datos personales.](https://drive.google.com/file/d/1A175vNlnjQtiak7LJGQeqMm4dLgKJ5xu/view?usp=sharing)
🔙 Volver a Trámites EsSalud
🏠 Volver al menú principal',

  'iii. 💵Solicitud de pago diferencial' => 'Para iniciar el trámite, debes presentar el formato correspondiente adjuntando:
• Requisitos
a. Solicitud de Pago Diferencial (Formato)
b. Copia Formulario 1040 con sello de EsSalud u Hoja de consulta NIT EsSalud (proporcionado por el personal a cargo Lic. Elizabeth Ticona Rojas).
c. Recibo de pago electrónico de la entidad bancaria “voucher”.
• 📍 Lugar de presentación
Mesa de Partes de Gerencia y Presidencia (Ventanilla N° 8) - Sede la Paz 320
• 📎 Descargar formato
[📥 Descargar formato de solicitud](https://drive.google.com/file/d/1GmyhMH7p4mbplzaRBHfumBrVw6jXQzGX/view?usp=sharing)
🔙 Volver a Trámites EsSalud
🏠 Volver al menú principal',

  'iv. Postergación de la licencia por maternidad' => 'Para iniciar el trámite, debes presentar:
• Requisitos
a. Solicitud Simple
b. Adjunta Anexo 18.
• 📍 Lugar de presentación
Mesa de Partes de Gerencia y Presidencia (Ventanilla N° 8) - Sede la Paz 320
• 📎 Descargar formato
[📎 Anexo 18](https://drive.google.com/file/d/1tfS5oayY23bP_TfiIkeO5t_TsjYhYqkt/view?usp=sharing)
🔙 Volver a Trámites EsSalud
🏠 Volver al menú principal',

  'b. SEGUROS' => 'i. ENTIDAD PRESTADORA DE SALUD – EPS
ii. SEGUROS ADICIONALES
🔙 Volver a Bienestar de personal
🏠 Volver al menú principal',

  'i. ENTIDAD PRESTADORA DE SALUD – EPS' => '[📎 Ver Cartilla Informativa](https://drive.google.com/file/d/1hDMPsumMqMyPGBRG28F7MLHYYcrlLsNq/view?usp=sharing)
• Requisitos:
a. Solicitud dirigida al Área de Potencial Humano
b. Formato de Afiliación a EPS
c. Presentar documentos según el tipo de familiar a afiliar, debiendo adjuntar :
- Copia de DNI del titular y de los derechohabientes a afiliar
- Acta de Nacimiento en caso de afiliar a hijos.
- Acta de matrimonio en caso de afiliar a su cónyuge.
• 📍 Lugar de presentación
Mesa de Partes de Gerencia y Presidencia (Ventanilla N° 8) - Sede la Paz 320
• Descarga de formatos
[📎 Formato de Afiliación a EPS](https://drive.google.com/file/d/1nn4awIKBzJWoiR5ANM1ZUX7gjuJa8zFM/view?usp=sharing)
🔙 Volver a Seguros
🏠 Volver al menú principal',

  'ii. SEGUROS ADICIONALES' => '[📎 Ver Cartilla Informativa](https://drive.google.com/file/d/1kSOmwhCfDPSvTGSMI_-X1U8bHVDjvtE8/view?usp=sharing)
a. SEGURO +VIDA (ESSALUD)
b. SEGURO DE ACCIDENTES PERSONALES
c. SEGURO VIDA LEY
🔙 Volver a Seguros
🏠 Volver al menú principal',

  'a. SEGURO +VIDA (ESSALUD)' => '• Requisitos
a. Solicitud Simple
b. Formulario 6012 de Afiliación a +Vida
c. Carta Poder (02 juegos, solo firmar, sin llenar)
d. Formato de política de privacidad para el tratamiento de datos personales. (02 juegos, solo firmar, sin llenar)
e. Formulario 1010 (02 juegos, solo firmar, sin llenar)
f. Autorización Descuentos por planilla
• 📍 Lugar de presentación
Mesa de Partes de Gerencia y Presidencia (Ventanilla N° 8) - Sede la Paz 320
• Descarga de formatos
[📎 Formulario 6012](https://drive.google.com/file/d/1v5fATbNqJ9H8b6kOHydqjITuKQSh9-Eq/view?usp=sharing)
[📎 Carta Poder](https://drive.google.com/file/d/1AjTaufySMLl8-nhBL00CpXGH7XwLEtmq/view?usp=sharing)
[📎 Formato de política de privacidad para el tratamiento de datos personales](https://drive.google.com/file/d/1A175vNlnjQtiak7LJGQeqMm4dLgKJ5xu/view?usp=sharing)
[📎 Formulario 1010](https://drive.google.com/file/d/1vA6sg-_-CCl3Ynzwkcpmf6-62LQLE29H/view?usp=sharing)
[📎 Autorización Descuentos por planilla](https://drive.google.com/file/d/1-3lcfEpiSSpfTtrrxEKC1vG4tpcGiddh/view?usp=sharing)
🔙 Volver a Seguros Adicionales
🏠 Volver al menú principal',

  'b. SEGURO DE ACCIDENTES PERSONALES' => 'El trabajador puede atenderse en la red de clínicas dentro de las 48 horas de ocurrido el accidente, mostrando su DNI y Formato en original de Declaración de Accidentes.
[📎 Relación de Clínicas afiliadas](https://drive.google.com/file/d/18KzYR9s3SttSE__UTE8vl9z7JWgqd3Qe/view?usp=sharing)
🔙 Volver a Seguros Adicionales
🏠 Volver al menú principal',

  'c. SEGURO VIDA LEY' => 'El servidor del régimen laboral  D. Leg. 728 debe presentar y/o actualizar su Declaración Jurada de Beneficiarios.
[📎 Formato de Declaración](https://drive.google.com/file/d/1lJen6D06YoSiJAmWHlA0Ah7CGALxjYO6/view?usp=sharing)
🔙 Volver a Seguros Adicionales
🏠 Volver al menú principal',

  '🔙 Volver a Seguros Adicionales' => '[📎 Ver Cartilla Informativa](https://drive.google.com/file/d/1kSOmwhCfDPSvTGSMI_-X1U8bHVDjvtE8/view?usp=sharing)
a. SEGURO +VIDA (ESSALUD)
b. SEGURO DE ACCIDENTES PERSONALES
c. SEGURO VIDA LEY
🔙 Volver a Seguros
🏠 Volver al menú principal',

  '🔙 Volver a Trámites EsSalud' => 'i. REGISTRO DE DERECHOHABIENTES
ii. 🏥 SOLICITUD DE SUBSIDIO
iii. 💵Solicitud de pago diferencial
iv. Postergación de la licencia por maternidad
🔙 Volver a Bienestar de personal
🏠 Volver al menú principal',

  '🔙 Volver a Seguros' => 'i. ENTIDAD PRESTADORA DE SALUD – EPS
ii. SEGUROS ADICIONALES
🔙 Volver a Bienestar de personal
🏠 Volver al menú principal',

  '🔙 Volver a Bienestar de personal' => 'a. TRÁMITES ANTE ESSALUD Y EL ÁREA DE POTENCIAL HUMANO
b. SEGUROS
🏠 Volver al menú principal',

// ---------------------- 4. CREDENCIAL Y FOTOCHECK ------------------------
  '4. CREDENCIAL Y FOTOCHECK' => 'a. Credencial – Carreras Especiales
b. Fotocheck
c. Volver al menú principal',

  'a. Credencial – Carreras Especiales' => 'Para solicitar tu credencial, puedes hacerlo en los siguientes casos:
i. Nuevo
ii. Robo – Pérdida
iii. Caducidad/deterioro
[iv.📎 Descargar formato](https://drive.google.com/file/d/1iMz7m6ChbAuQ9z-yYMKbTYWxNAPJjcdU/view?usp=sharing)
[v.📍 Lugar de presentación](#) Mesa de Partes de Gerencia y Presidencia (Ventanilla N° 8) - Sede la Paz 320.
🔙 Volver a Credencial
🏠 Volver al menú principal',

  'i. Nuevo' => 'Debes presentar el formato de solicitud de credencial adjuntando:
• 2 fotografías tamaño carnet (fondo blanco, blusa o camisa blanca y terno oscuro)
• Copia simple de DNI
• Copia simple de Acta de Juramentación
• Copia simple Resolución de designación
🔙 Volver a Credencial – Carreras Especiales
🏠 Volver al menú principal',

  'ii. Robo – Pérdida' => 'Debes presentar el formato de solicitud de credencial adjuntando:
• 2 fotografías tamaño carnet (fondo blanco, blusa o camisa blanca y terno oscuro)
• Copia simple de DNI
• Copia simple de Acta de Juramentación
• Copia simple de Resolución de designación 
• Copia de la denuncia policial
🔙 Volver a Credencial – Carreras Especiales
🏠 Volver al menú principal',

  'iii. Caducidad/deterioro' => '• 2 fotografías tamaño carnet (fondo blanco, blusa o camisa blanca y terno oscuro)
• Copia simple de DNI
• Copia simple Acta de Juramentación
• Copia simple de Resolución de designación 
• Copia de credencial caducada 
🔙 Volver a Credencial – Carreras Especiales
🏠 Volver al menú principal',



  'b. Fotocheck' => 'i. Emisión
ii. Devolución
iii. Robo - Pérdida
🔙 Volver a Credencial
🏠 Volver al menú principal',

  'i. Emisión' => 'La emisión del fotocheck se realiza de oficio por el Área de Potencial Humano.
📢 El área se comunicará contigo para informarte la fecha y modalidad de entrega.
🔙 Volver a Fotocheck
🏠 Volver al menú principal',

  'ii. Devolución' => 'Cuando finalice tu vínculo laboral, deberás devolver el fotocheck junto con la entrega de cargo.
📍 Presentación:
Mesa de Partes de Gerencia y Presidencia (Ventanilla N° 8) - Sede la Paz 320.
🔙 Volver a Fotocheck
🏠 Volver al menú principal',

  'iii. Robo - Pérdida' => 'Para solicitar fotocheck por este motivo, deberás presentar un escrito dirijo al Área de Potencial Humano, adjuntando:
📄 Copia de la Denuncia Policial por pérdida de fotocheck.
📢 El Área de Potencial Humano se comunicará contigo para informarte la fecha y modalidad de entrega.
La denuncia policial puede ser presentada  en forma  digital en el siguiente enlace: 
Pasos a seguir para generar denuncia policial Dígital:
1. Ingresar a la página web de la Policía Nacional del Perú: [www.policia.gob.pe](http://www.policia.gob.pe)
2. Buscar la sección “SERVICIOS EN LÍNEA”
3. Elegir la opción “DENUNCIA POLICIAL DIGITAL”
4. Seleccionar “NUEVO TRÁMITE”
🔙 Volver a Fotocheck
🏠 Volver al menú principal',

  '🔙 Volver a Credencial – Carreras Especiales' => 'Para solicitar tu credencial, puedes hacerlo en los siguientes casos:
i. Nuevo
ii. Robo – Pérdida
iii. Caducidad/deterioro
• 📎 Descargar formato
[📎 Descargar formato](#)
• 📍 Lugar de presentación
Mesa de Partes de Gerencia y Presidencia (Ventanilla N° 8) - Sede la Paz 320.
🔙 Volver a Credencial
🏠 Volver al menú principal',

  '🔙 Volver a Fotocheck' => 'i. Emisión
ii. Devolución
iii. Robo - Pérdida
🔙 Volver a Credencial
🏠 Volver al menú principal',

  '🔙 Volver a Credencial' => 'a. Credencial – Carreras Especiales
b. Fotocheck
🏠 Volver al menú principal',

// ---------------------- 5. CONSTANCIAS Y CERTIFICADOS ------------------------
  '5. EMISIÓN DE CONSTANCIAS Y CERTIFICADOS DE TRABAJO' => 'Selecciona el trámite que deseas realizar:
a. 📄Solicitar Constancia de Trabajo
b. 📜Solicitar Certificado de Trabajo
c. Volver al menú principal',

  'a. 📄Solicitar Constancia de Trabajo' => '¿Cómo deseas solicitarla?
i. 💻A través del sistema institucional
ii. 📝Mediante solicitud escrita
iii. 🎥Ver video tutorial
iv. Descargar modelo de solicitud
🔙 Volver a Constancias y Certificados
🏠 Volver al menú principal',

  'i. 💻A través del sistema institucional' => 'Puedes realizar tu solicitud ingresando a:
👉 [https://Sistemas2.mpfn.gob.pe](https://Sistemas2.mpfn.gob.pe)
Pasos:
• Ingresa con tu usuario institucional.
• Seleccione el Módulo de Emisión de Constancias
• Elige el tipo de constancia (Simple, Histórica, Personalizada).
• Envía tu solicitud.
• Recibirás la confirmación en tu correo registrado
📌 Importante:
Si tienes problemas de acceso, comunícate con la Mesa de Ayuda del Área de Tecnologías de la Información:
📞 [937 461 856](https://wa.me/51937461856?text=Hola%20quiero%20más%20información)
🔙 Volver a Constancia de Trabajo
🏠 Volver al menú principal',

  'ii. 📝Mediante solicitud escrita' => 'Si no puedes acceder al sistema, puedes presentar una solicitud simple 
presentado por CEA –Oficina General de Potencial Humano
📧 Correo electrónico: [mesap.gerencia.aqp@mpfn.gob.pe](mailto:mesap.gerencia.aqp@mpfn.gob.pe)
[📎 Descargar modelo de solicitud (WORD)](https://docs.google.com/document/d/1TajA-HSIIZiuv3C6ikZqk0gfAA_H4gWs/edit?usp=drive_link&ouid=111203226793113830230&rtpof=true&sd=true)
🔙 Volver a Constancia de Trabajo
🏠 Volver al menú principal',

  'iii. 🎥Ver video tutorial' => 'Puedes revisar el tutorial para realizar tu solicitud en el sistema institucional:
📺 [Ver video](https://drive.google.com/file/d/1SJp8oKjZ9xY64R0D7p6_iVpHD-GlJBV6/view?usp=sharing)
🔙 Volver a Constancia de Trabajo
🏠 Volver al menú principal',

  'iv. Descargar modelo de solicitud' => '📎 [Descargar modelo de solicitud (WORD)](https://docs.google.com/document/d/1TajA-HSIIZiuv3C6ikZqk0gfAA_H4gWs/edit?usp=drive_link&ouid=111203226793113830230&rtpof=true&sd=true)
🔙 Volver a Constancia de Trabajo
🏠 Volver al menú principal',

  'b. 📜Solicitar Certificado de Trabajo' => 'El certificado de trabajo se emite al término del vínculo laboral.
i. Requisitos:
📝 Solicitud simple
ii. ¿Cómo presentarlo?
Puedes hacerlo mediante:
📍 Mesa de Partes de Gerencia y Presidencia (Ventanilla N° 8) - Sede la Paz 320.
📧 Correo electrónico: [mesap.gerencia.aqp@mpfn.gob.pe](mailto:mesap.gerencia.aqp@mpfn.gob.pe)
[📎 Descargar modelo de solicitud (WORD)](https://docs.google.com/document/d/1tOVt_4nDTDkXifd-mLaAMDcjUJjeUVyk/edit?usp=drive_link&ouid=111203226793113830230&rtpof=true&sd=true)
🔙 Volver a Constancias y Certificados
🏠 Volver al menú principal',

  '🔙 Volver a Constancias y Certificados' => 'Selecciona el trámite que deseas realizar:
a. 📄Solicitar Constancia de Trabajo
b. 📜Solicitar Certificado de Trabajo
c. Volver al menú principal',

  '🔙 Volver a Constancia de Trabajo' => '¿Cómo deseas solicitarla?
i. 💻A través del sistema institucional
ii. 📝Mediante solicitud escrita
iii. 🎥Ver video tutorial
iv. Descargar modelo de solicitud
🔙 Volver a Constancias y Certificados
🏠 Volver al menú principal',

// ---------------------- 6. VACACIONES ------------------------
  '6. VACACIONES' => 'a. Programación de vacaciones anual
b. Reprogramación de vacaciones
c. Adelanto de vacaciones
🏠 Volver al menú principal',

  'a. Programación de vacaciones anual' => 'Se realiza una vez al año, generalmente a fin de año.
La programación se realiza a través de [Sistemas2.mpfn.gob.pe](https://Sistemas2.mpfn.gob.pe)
🎥 Videotutorial:
• Para jefes: [Ver videotutorial jefe](https://drive.google.com/file/d/1ZpH9e7SKLFjZGnPTrTjoHnEuaTuCyV3j/view?usp=drive_link)
• Para trabajadores: [Ver videotutorial trabajador](https://drive.google.com/file/d/1jJ1pv6rH8WFCG05fkawqScLvjcNhku-l/view?usp=drive_link)
🔙 Volver a Vacaciones
🏠 Volver al menú principal',

  'b. Reprogramación de vacaciones' => 'i. Solicitud de reprogramación
ii. Recomendaciones
iii. Modelo de Solicitud
🔙 Volver a Vacaciones
🏠 Volver al menú principal',

  'i. Solicitud de reprogramación' => '• Presentar el escrito hasta el quinto día hábil anterior al inicio de tus vacaciones.
• Debe contar con V°B° del Jefe Inmediato y firma del solicitante.
• Presentarlo mediante la Carpeta Electrónica Administrativa (CEA) – Área de Potencial Humano Arequipa-Patiño Juarez Karla (sólo al personal indicado).
• IMPORTANTE: En el asunto colocar: Reprogramación de Vacaciones -(Apellidos y Nombres)
🔙 Volver a Reprogramación
🏠 Volver al menú principal',

  'ii. Recomendaciones' => '• La suma de todos los periodos fraccionados no puede superar 30 días calendarios.
• No se pueden tomar más de 4 días hábiles por semana de los 7 días hábiles fraccionables.
• Si el periodo inicia o termina un viernes, los sábados y domingos siguientes también se computan.
• El acuerdo de fraccionamiento debe ser previo al disfrute de las vacaciones y debe incluir las fechas originales y nuevas.
🔙 Volver a Reprogramación
🏠 Volver al menú principal',

  'iii. Modelo de Solicitud' => '[📎 Descargar modelo de solicitud (WORD)](https://docs.google.com/document/d/1BnjKRJHrkFb1R0_D0bmlP2n5H19p8QRK/edit?usp=sharing&ouid=111203226793113830230&rtpof=true&sd=true)
🔙 Volver a Reprogramación
🏠 Volver al menú principal',

  'c. Adelanto de vacaciones' => 'Para verificar si puedes acceder al adelanto de vacaciones, comunícate al:
📞 [949 305 573](https://wa.me/51949305573?text=Hola%20quiero%20más%20información)
🔙 Volver a Vacaciones
🏠 Volver al menú principal',

  '🔙 Volver a Reprogramación' => 'i. Solicitud de reprogramación
ii. Recomendaciones
iii. Modelo de Solicitud
🔙 Volver a Vacaciones
🏠 Volver al menú principal',

  '🔙 Volver a Vacaciones' => 'a. Programación de vacaciones anual
b. Reprogramación de vacaciones
c. Adelanto de vacaciones
🏠 Volver al menú principal',

// ---------------------- 7. DECLARACIONES ------------------------
  '7. DECLARACIÓN JURADA DE INGRESOS Y DE BIENES Y RENTAS' => 'Se encuentran obligados a presentar DECLARACIÓN JURADA DE INGRESOS, BIENES Y RENTAS, quienes ocupen los cargos o desarrollen las funciones establecidas en el Art. 2 de la Ley  27482.
La Declaración Jurada de Ingresos, Bienes y Rentas se presenta en la siguiente oportunidad: 
a) Al inicio: Dentro de los quince (15) días hábiles siguientes a la fecha en que se inicia gestión, cargo o labor.
b) Periódica: Durante los primeros quince (15) días hábiles, después de cumplir doce (12) meses en dicha gestión
c) Al cesar: Dentro de los quince (15) días hábiles siguientes a la fecha en que se cesó en la gestión, cargo o labor.
Para registrar tu Declaración Jurada de Ingresos Bienes y Rentas en el sistema de la Contraloría General de la República del Perú, sigue estos pasos:
a. ✅ Sí, tengo firma digital
b. ❌ No, tengo firma digital
c. Video Tutorial
🏠 Volver al menú principal',

  'a. ✅ Sí, tengo firma digital' => '• Ingresa al sistema oficial:
[https://apps1.contraloria.gob.pe/ddjj/](https://apps1.contraloria.gob.pe/ddjj/)
• Inicia sesión.
• Completa los campos solicitados.
• Firma
• Una vez firmado, comunicarse con el personal Encargado al número [959860944](https://wa.me/51959860944?text=Hola%20quiero%20más%20información)
🔙 Volver a DJ Bienes
🏠 Volver al menú principal',

  'b. ❌ No, tengo firma digital' => '• Ingresa al sistema oficial:
[https://apps1.contraloria.gob.pe/ddjj/](https://apps1.contraloria.gob.pe/ddjj/)
• Inicia sesión.
• Completa los campos solicitados.
• Imprime 3 ejemplares (1 cargo, 2 serán enviados a Lima)
• Firma todas las hojas
• Acércate al Área de Potencial Humano a fin de entregar los formatos.
🔙 Volver a DJ Bienes
🏠 Volver al menú principal',

  'c. Video Tutorial' => '🎥 Puedes ver el video tutorial aquí:
[https://www.youtube.com/watch?v=j1UzD122NlA](https://www.youtube.com/watch?v=j1UzD122NlA)
🔙 Volver a DJ Bienes
🏠 Volver al menú principal',

  '🔙 Volver a DJ Bienes' => 'a. ✅ Sí, tengo firma digital
b. ❌ No, tengo firma digital
c. Video Tutorial
🏠 Volver al menú principal',

  '8. DECLARACIÓN JURADA DE INTERESES' => 'Se encuentran obligados a presentar DECLARACIÓN JURADA DE INTERESES, quienes ocupen los cargos o desarrollen las funciones establecidas en el Art. 3 de la Ley 31227.

La declaración jurada de intereses se presenta en la siguiente oportunidad: 
a) Al inicio: Dentro de los quince (15) días hábiles de haber sido elegido/a, nombrado/a, designado/a, contratado/a o similares. 
b) Periódica: Durante los primeros quince (15) días hábiles, después de doce (12) meses de ejercida la labor. Sin perjuicio de lo anterior, en caso de que se produzca algún hecho relevante que deba ser informado, el sujeto obligado presenta una actualización de su declaración jurada de intereses, en el plazo de quince (15) días hábiles de producido el referido hecho. 
c) Al cese: Dentro de los quince (15) días hábiles de haberse extinguido el vínculo laboral o contractual, siendo requisito para la entrega de cargo, conformidad de servicios o similares.

Para registrar tu Declaración Jurada de Intereses en el sistema de la Contraloría General de la República del Perú, sigue estos pasos:
a. ✅ Sí, tengo firma digital (Intereses)
b. ❌ No, tengo firma digital (Intereses)
🏠 Volver al menú principal',

  'a. ✅ Sí, tengo firma digital (Intereses)' => '• Ingresa al sistema oficial:
[https://appdji.contraloria.gob.pe/djic/](https://appdji.contraloria.gob.pe/djic/)
• Inicia sesión.
• Completa los campos solicitados.
• 🎥 Puedes ver el video tutorial aquí:
[https://www.youtube.com/watch?v=TNK0fJbIU_8](https://www.youtube.com/watch?v=TNK0fJbIU_8)
• [Descargar Manual del Declarante](https://drive.google.com/file/d/10KJ5Fb_A1uczvH7zHPY6hpbuFEK3pqsP/view?usp=sharing)
🔙 Volver a DJ Intereses
🏠 Volver al menú principal',

  'b. ❌ No, tengo firma digital (Intereses)' => 'Para obtener soporte, comunícate con:
📞 Mesa de Ayuda – Área de Tecnologías de la Información
Número: [937461856](https://wa.me/51937461856?text=Hola%20quiero%20más%20información)
🔙 Volver a DJ Intereses
🏠 Volver al menú principal',

  '🔙 Volver a DJ Intereses' => 'a. ✅ Sí, tengo firma digital (Intereses)
b. ❌ No, tengo firma digital (Intereses)
🏠 Volver al menú principal',

  '9. BOLETAS DE PAGO' => 'Para visualizar y descargar tu boleta de pago, sigue estos pasos:
👉 Ingresa al sistema institucional:
[https://Sistemas2.mpfn.gob.pe](https://Sistemas2.mpfn.gob.pe)
a. 📂 Dentro del sistema:
b. 🎥 ¿Necesitas ayuda?
c. ⚠️ Soporte técnico
🏠 Volver al menú principal',

  'a. 📂 Dentro del sistema:' => 'i. Selecciona Sistema de Gestión de Documentos Laborales – SIGEDOL.
ii. Elige una de las siguientes opciones:
• 📌 Pendientes: Verás las boletas que aún no has visualizado.
• 📁 Históricas: Verás las boletas ya visualizadas.
iii. Selecciona la boleta que deseas consultar.
iv. Descárgala en tu dispositivo.
v. Si deseas conocer el detalle de los conceptos puedes comunicarte al 📞 [959 860 944](https://wa.me/51959860944?text=Hola%20quiero%20más%20información)
🔙 Volver a Boletas de pago
🏠 Volver al menú principal',

  'b. 🎥 ¿Necesitas ayuda?' => '[📺 Ver video tutorial](https://drive.google.com/file/d/19LjmeQlm-sqaNU8yOap0kiReXrvBpzxN/view?usp=sharing)
🔙 Volver a Boletas de pago
🏠 Volver al menú principal',

  'c. ⚠️ Soporte técnico' => 'Si no visualizas el sistema SIGEDOL dentro de Sistemas2, comunícate con:
📞 Mesa de Ayuda – Área de Tecnologías de la Información
📞 [937461856](https://wa.me/51937461856?text=Hola%20quiero%20más%20información)
🔙 Volver a Boletas de pago
🏠 Volver al menú principal',

  '🔙 Volver a Boletas de pago' => 'a. 📂 Dentro del sistema:
b. 🎥 ¿Necesitas ayuda?
c. ⚠️ Soporte técnico
🏠 Volver al menú principal',

// ---------------------- 10. ENTREGA DE CARGO ------------------------
  '10. ENTREGA DE CARGO' => 'A continuación te presentamos:
• [Resolución de Gerencia General N° 71-2021-MP-FN-GG📎](#)
• [Directiva de Entrega, Recepción y Transferencia de Cargo📎](#)
🏠 Volver al menú principal'
);

// ────────────────────────────────────────────────────────────────────────
    // FUNCIÓN DE NORMALIZACIÓN: elimina prefijos de letra (a., b.),
    // números romanos (i., ii., iii., iv., v., vi., vii.),
    // números (1.-12.), emojis iniciales y espacios.
    // Permite comparar solo el texto central de la opción.
    // ────────────────────────────────────────────────────────────────────────
    function normalizarOpcion(string $texto): string {
        $t = mb_strtolower(trim($texto));
        // Quitar emojis UTF-8 del inicio
        $t = preg_replace('/^[\x{1F000}-\x{1FFFF}\x{2600}-\x{27FF}\x{FE00}-\x{FEFF}\x{00A9}\x{00AE}]+\s*/u', '', $t);
        // Quitar prefijos: 1-12. | a-z. | romanos: vi{0,2} iv ix v xi{0,2} etc.
        $t = preg_replace('/^(?:1[0-2]|[1-9]|[a-z]|vi{0,2}|iv|ix|xi{0,2}|v?i{1,3})\.\s+/u', '', $t);
        // Segunda pasada de emoji (por si quedó después del prefijo)
        $t = preg_replace('/^[\x{1F000}-\x{1FFFF}\x{2600}-\x{27FF}\x{FE00}-\x{FEFF}\x{00A9}\x{00AE}]+\s*/u', '', $t);
        // Quitar 🔙 y 🏠 sueltos
        $t = str_replace(['🔙', '🏠'], '', $t);
        return trim($t);
    }

    $mensajeNormalizado = trim($mensaje);
    $mensajeLimpio = mb_strtolower($mensajeNormalizado);

    // Buscar si el usuario hizo clic en un botón exacto
    $esRespuestaEstatica = false;

    // CASO 1: Cualquier variación de "Volver al menú principal"
    if (preg_match('/volver al men[uú] principal/ui', $mensajeLimpio)) {
        $resultado = [
            'exito' => true,
            'respuesta' => $opcionesEstaticas['Volver al menú principal'],
            'error' => '',
            'is_main_menu' => true
        ];
        $esRespuestaEstatica = true;
        $tiempoRespuesta = 10;
    }

    // CASO 2: Buscar por coincidencia exacta o texto normalizado
    if (!$esRespuestaEstatica) {
        $mensajeNorm = normalizarOpcion($mensaje);
        foreach ($opcionesEstaticas as $key => $respuesta) {
            $keyLimpia = mb_strtolower(trim($key));
            // 2a. Coincidencia exacta (sin distinción de mayúsculas)
            if ($keyLimpia === $mensajeLimpio) {
                $resultado = ['exito' => true, 'respuesta' => $respuesta, 'error' => ''];
                $esRespuestaEstatica = true;
                $tiempoRespuesta = 10;
                break;
            }
            // 2b. Coincidencia normalizada (sin prefijo letra/número/emoji)
            $keyNorm = normalizarOpcion($key);
            if (!empty($keyNorm) && strlen($keyNorm) > 3 && $keyNorm === $mensajeNorm) {
                $resultado = ['exito' => true, 'respuesta' => $respuesta, 'error' => ''];
                $esRespuestaEstatica = true;
                $tiempoRespuesta = 10;
                break;
            }
        }
    }
    
    // Solo llamar a la IA si no fue un clic de menú exacto
    if (!$esRespuestaEstatica) {
        $tiempoInicio = microtime(true);
        $resultado = $chatbot->obtenerRespuesta($mensaje, $historial);
        $tiempoRespuesta = round((microtime(true) - $tiempoInicio) * 1000); // En milisegundos
    }
    // --- FIN DE CONTROL ESTRICTO ---
    
    if (!$resultado['exito']) {
        // Si falla la IA, dar una respuesta de respaldo
        $respuestaRespaldo = "Lo siento, estoy experimentando dificultades técnicas en este momento. " .
                            "Por favor, intenta nuevamente en unos momentos o contacta directamente con nuestra oficina.";
        
        if (CHATBOT_DEBUG_MODE) {
            // Log the error intentionally to Apache logs
            error_log("DETALLE ERROR IA: " . print_r($resultado, true));
        }

        responderJSON([
            'respuesta' => $respuestaRespaldo,
            'tiempo_respuesta' => $tiempoRespuesta,
            'modo_respaldo' => true,
            'error_tecnico' => CHATBOT_DEBUG_MODE ? $resultado['error'] : null
        ]);
    }
    
    $respuestaBot = $resultado['respuesta'];
    
    // Guardar en la base de datos
    $ipUsuario = $_SERVER['REMOTE_ADDR'] ?? null;
    $chatbot->guardarConversacion($sesionId, $mensaje, $respuestaBot, $ipUsuario);
    
    // Responder al frontend
    responderJSON([
        'respuesta' => $respuestaBot,
        'tiempo_respuesta' => $tiempoRespuesta,
        'sesion_id' => CHATBOT_DEBUG_MODE ? $sesionId : null,
        'proveedor' => CHATBOT_DEBUG_MODE ? PROVEEDOR_IA_ACTIVO : null,
        'claves_validas' => array_keys($opcionesEstaticas),
        'is_main_menu' => isset($resultado['is_main_menu']) ? $resultado['is_main_menu'] : false
    ]);
    
} catch (Exception $e) {
    if (CHATBOT_DEBUG_MODE) {
        responderJSON([
            'error' => 'Error del servidor',
            'detalle' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ], 500);
    } else {
        responderJSON([
            'error' => 'Error del servidor. Por favor, intenta nuevamente.'
        ], 500);
    }
}
