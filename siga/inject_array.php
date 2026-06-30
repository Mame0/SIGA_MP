<?php
$file = 'c:\\xampp\\htdocs\\siga\\chatbot_potencial_humano\\api.php';
$content = file_get_contents($file);

$startPos = strpos($content, '$opcionesEstaticas = array (');
$endPos = strpos($content, ");\n\n// ────────────────────────────────────────────────────────────────────────", $startPos);

if ($startPos !== false && $endPos !== false) {
    // The exact text we want to inject:
    $newArray = "\$opcionesEstaticas = array (
  'Volver al menú principal' => \$menuPrincipal,
  '🏠 Volver al menú principal' => \$menuPrincipal,
  '1. CONTROL DE ASISTENCIA' => '1. Boleta de Permiso
2. Compensación por trabajo en sobretiempo por toda la jornada laboral
3. Autorización de ingreso en días de descanso obligatorio, feriados y días no laborables.
4. Récord de Asistencia
5. Fecha de cierre de asistencia
6. Comunicados
7. Contacto en casos de problemas con el reloj biométrico
8. Derivación de Documentos mediante Carpeta Electrónica Administrativa CEA
9. Volver al menú principal',
  '9. Volver al menú principal' => \$menuPrincipal,
  
  '1. Boleta de Permiso' => 'Todas las boletas de permiso que se ejecuten a primera hora de la jornada laboral, es decir que inicien a partir de las 08:00 horas deberán de ser presentadas en vigilancia de cada sede o carpeta electrónica administrativa - CEA para servidores que laboran en Provincias, el día anterior al permiso debidamente firmado por su jefe inmediato, deben de marcar (x) acorde al motivo del permiso:
1. Permiso Particular
2. Comisión de Servicios
3. Compensación
4. Atención de Salud
5. Registro fuera del horario establecido
6. Omisión de Registro de Marcación
🔙 Volver a Asistencia
🏠 Volver al menú principal',
  
  '1. Permiso Particular' => '* Si inicia después de la hora de ingreso, la boleta debe presentarse en el día.
* Debe efectuar registro de marcación antes y después de la ejecución del permiso.
* Son establecidas por horas.
* No puede superar las 8 horas en el día, ni en el mes.
[📎 Descargar formato de boleta de permiso](https://drive.google.com/file/d/1VaQ6mmBpQOn3RtMWFWm_MunNmfjIyDmI/view?usp=drive_link)
🔙 Volver a Boleta de Permiso
🏠 Volver al menú principal',

  '2. Comisión de Servicios' => '* Si inicia después de la hora de ingreso, la boleta debe presentarse en el día.
* Debe efectuar registro de marcación antes y después de la ejecución del permiso.
* El trabajador deberá presentar al retorno del permiso, el documento debidamente sellado, consignando la FIRMA, FECHA y HORA de la atención por la persona de la entidad a donde se desplazó
[📎 Ver modelo](https://drive.google.com/file/d/1OfhvRb-0MQKACH6vuhCnJ1wlnKfS7eyt/view?usp=drive_link)
[📎 Descargar formato de boleta de permiso](https://drive.google.com/file/d/1VaQ6mmBpQOn3RtMWFWm_MunNmfjIyDmI/view?usp=drive_link)
🔙 Volver a Boleta de Permiso
🏠 Volver al menú principal',

  '3. Compensación' => '* Para la presentación con boleta de permiso son establecidas por horas y se debe adjuntar el formato correspondiente del sobretiempo. El mismo que debió ser presentado con anterioridad al permiso solicitado acorde al procedimiento de “trabajos en sobretiempo”.
* No debe de superar las 16 horas al mes.
* Debe efectuar registro de marcación antes y después de la ejecución del permiso.
* Acorde al convenio colectivo, puede presentarse el mismo día hasta en dos oportunidades no consecutivas en el mes en caso el permiso sea al inicio de la jornada.
[📎 Ver modelo](https://drive.google.com/file/d/16Ax25zHx2TqNM44sLYChpQRqHICJFOC8/view?usp=drive_link)
[📎 Descargar formato de boleta de permiso](https://drive.google.com/file/d/1VaQ6mmBpQOn3RtMWFWm_MunNmfjIyDmI/view?usp=drive_link)
[📎 Descargar autorización en sobretiempo](https://drive.google.com/file/d/1NuYeLG0JEmCIX-ATn--bHrrm5Ajz0s-w/view?usp=drive_link)
🔙 Volver a Boleta de Permiso
🏠 Volver al menú principal',

  '4. Atención de Salud' => '* Permiso por cita médica del servidor: 
1. Se otorga hasta un máximo de 04 horas y en caso de exámenes especiales hasta por 07 horas. 
2. Requiere la presentación de la boleta de permiso debidamente autorizada y la constancia de atención médica. 
3. En caso de no adjuntar la constancia de atención médica, será considerado como permiso particular.
* Permiso por atención médica de emergencia del familiar directo: 
1. El permiso se concede únicamente en casos de EMERGENCIA, se otorga hasta cuatro (4) horas al día, y en caso de exámenes especiales hasta siete (7) horas dentro de la jornada laboral. 
2. Requiere la presentación de la boleta de permiso debidamente autorizada y la constancia de atención médica del ÁREA DE EMERGENCIA, como sustento de haber sido atendida en dicha área, caso contrario será considerado como permiso particular
[📎 Descargar formato de boleta de permiso](https://drive.google.com/file/d/1VaQ6mmBpQOn3RtMWFWm_MunNmfjIyDmI/view?usp=drive_link)
🔙 Volver a Boleta de Permiso
🏠 Volver al menú principal',

  '5. Registro fuera del horario establecido' => '* La boleta de permiso se presenta en el mismo día o hasta 24 horas posteriores al día de la incidencia cuando el servidor tiene registro de marcación de ingreso entre las 8:16 y 8:59 Horas de lunes a viernes (jornada laboral).
* Se pueden presentar hasta cinco (05) boletas en el mes.
* Debe consignar “x” si es primera, segunda o tercera boleta; y, de ser cuarta o quinta boleta, debe consignar en el detalle.
[📎 Descargar formato de boleta de permiso](https://drive.google.com/file/d/1VaQ6mmBpQOn3RtMWFWm_MunNmfjIyDmI/view?usp=drive_link)
[📎 Comunicado de registro fuera de hora convenio colectivo](https://drive.google.com/drive/folders/1VsQ2MrAg8OX2LlJYMA-6P1eQ9i3vLZXZ)
🔙 Volver a Boleta de Permiso
🏠 Volver al menú principal',

  '6. Omisión de Registro de Marcación' => '* Acorde al convenio colectivo debe ser presentado hasta 48 horas siguientes de ocurrido el hecho.
* Justifica la marcación de asistencia de ingreso o salida de lunes a viernes (jornada laboral) y de manera excepcional por única vez en el mes.
[📎 Descargar formato de boleta de permiso](https://drive.google.com/file/d/1VaQ6mmBpQOn3RtMWFWm_MunNmfjIyDmI/view?usp=drive_link)
[📎 Modelo de boleta por omisión](https://drive.google.com/file/d/1OfhvRb-0MQKACH6vuhCnJ1wlnKfS7eyt/view?usp=drive_link)
🔙 Volver a Boleta de Permiso
🏠 Volver al menú principal',

  '2. Compensación por trabajo en sobretiempo por toda la jornada laboral' => '* Para la contabilización del sobretiempo el trabajador deberá laborar como mínimo (01) hora y máxima (05) horas por día.
* La compensación podrá hacerse efectivo a partir del día siguiente de haberse realizado dicho trabajo hasta treinta (30) días hábiles posteriores, previa autorización del jefe inmediato
* Debe de presentarse hasta un (01) día anterior hábil como plazo máximo.
* No debe de superarse las 16 horas al mes
* La solicitud debe contar con el visto bueno de su jefe inmediato
* Presentarlo mediante la Carpeta Electrónica Administrativa (CEA) – Área de Potencial Humano Arequipa - Bustinza Sierra Fanny en caso de personal que labora en Arequipa. Flores Mamani Eliant personal que labora en provincias. Los escritos sólo deberán de ser enviados al personal indicado.
[📎 Modelo Solicitud Compensación 728](https://drive.google.com/file/d/16Ax25zHx2TqNM44sLYChpQRqHICJFOC8/view?usp=drive_link)
[📎 Modelo Solicitud Compensación CAS](https://drive.google.com/file/d/16Ax25zHx2TqNM44sLYChpQRqHICJFOC8/view?usp=drive_link)
[📎 Modelo de llenado de formato de sobretiempo](https://drive.google.com/file/d/1Lvh43r3nDECcelt5lPtRPpnkVPOQ7mkO/view?usp=drive_link)
[📎 Autorización en Sobretiempo](https://drive.google.com/file/d/1NuYeLG0JEmCIX-ATn--bHrrm5Ajz0s-w/view?usp=drive_link)
🔙 Volver a Asistencia
🏠 Volver al menú principal',

  '3. Autorización de ingreso en días de descanso obligatorio, feriados y días no laborables.' => 'Debe ser solicitado por el jefe inmediato, acreditando la necesidad de servicio ante la Gerencia Administrativa, el mismo debe estar suscrito por los servidores, se presenta hasta un (01) día hábil antes de la realización del trabajo, para el acceso a las instalaciones.
[📎 Modelo de solicitud de autorización de ingreso](https://docs.google.com/document/d/1T4KgMSd9cHtQc-czXMf2oJSkIc82wwsI/edit?usp=drive_link&ouid=111203226793113830230&rtpof=true&sd=true)
🔙 Volver a Asistencia
🏠 Volver al menú principal',

  '4. Récord de Asistencia' => 'Para visualizar y revisar tu récord de asistencia, sigue estos pasos:
👉 Ingresa al sistema institucional: https://Sistemas2.mpfn.gob.pe
1. Selecciona Sistema de Récord de Asistencia – ASIST
2. Selecciona Periodo Asistencial
[🎥 Ver video tutorial](https://drive.google.com/drive/folders/1abhH2FikOVARPnx2w0n3leTnITKqX8de)
⚠️ Soporte técnico
Si no visualizas el módulo ASIST dentro de Sistemas2, comunícate con:
📞 Mesa de Ayuda – Área de Tecnologías de la Información
937 461 856
🔙 Volver a Asistencia
🏠 Volver al menú principal',

  '5. Fecha de cierre de asistencia' => '* Cierre para planilla de MAYO 2026 
Incidencias del 26 de marzo al 25 de abril 
* Cierre para planilla de JUNIO 2026 
Incidencias del 26 de abril al 25 de mayo
* Cierre para planilla de JULIO 2026 
Incidencias del 26 de mayo al 25 de junio
* Cierre para planilla de AGOSTO 2026 
Incidencias del 26 de junio al 25 de julio
* Cierre para planilla de SETIEMBRE 2026 
Incidencias del 26 de julio al 25 de agosto
* Cierre para planilla de OCTUBRE 2026 
Incidencias del 26 de agosto al 25 de setiembre
* Cierre para planilla de NOVIEMBRE 2026 
Incidencias del 26 de setiembre al 25 de octubre
* Cierre para planilla de DICIEMBRE 2026 
Incidencias del 26 de octubre al 25 de noviembre
* Cierre para planilla de ENERO 2027 
Incidencias del 26 de noviembre al 25 de diciembre
🔙 Volver a Asistencia
🏠 Volver al menú principal',

  '6. Comunicados' => 'A continuación verás todos los comunicados vigentes:
[📎 Flyer Convenio Colectivo 2025-2026 Asistencia](https://drive.google.com/drive/folders/1VsQ2MrAg8OX2LlJYMA-6P1eQ9i3vLZXZ)
[📎 Facilidades sobre el registro de asistencia 20 FEB](https://drive.google.com/drive/folders/1VsQ2MrAg8OX2LlJYMA-6P1eQ9i3vLZXZ)
[📎 Recuperación horas dejadas de laborar Tolerancia 23 FEB](https://drive.google.com/drive/folders/1VsQ2MrAg8OX2LlJYMA-6P1eQ9i3vLZXZ)
[📎 Facilidades sobre el registro de asistencia 11 MAR](https://drive.google.com/drive/folders/1VsQ2MrAg8OX2LlJYMA-6P1eQ9i3vLZXZ)
[📎 Facilidades sobre el registro de asistencia 12 MAR](https://drive.google.com/drive/folders/1VsQ2MrAg8OX2LlJYMA-6P1eQ9i3vLZXZ)
🔙 Volver a Asistencia
🏠 Volver al menú principal',

  '7. Contacto en casos de problemas con el reloj biométrico' => 'En caso de que se registren casos de cortes de luz y tenga problemas con la marcación, deberá comunicarse a: 📞 959371597
🔙 Volver a Asistencia
🏠 Volver al menú principal',

  '8. Derivación de Documentos mediante Carpeta Electrónica Administrativa CEA' => 'En el Asunto colocar: MOTIVO - APELLIDOS Y NOMBRES
Derivar al siguiente personal:
* Bustinza Sierra, Fanny (para el personal que labora en Arequipa)
* Flores Mamani, Eliant Yanira (para el personal que labora en provincias)
🔙 Volver a Asistencia
🏠 Volver al menú principal',

  '🔙 Volver a Asistencia' => '1. Boleta de Permiso
2. Compensación por trabajo en sobretiempo por toda la jornada laboral
3. Autorización de ingreso en días de descanso obligatorio, feriados y días no laborables.
4. Récord de Asistencia
5. Fecha de cierre de asistencia
6. Comunicados
7. Contacto en casos de problemas con el reloj biométrico
8. Derivación de Documentos mediante Carpeta Electrónica Administrativa CEA
9. Volver al menú principal',

  '🔙 Volver a Boleta de Permiso' => 'i. Permiso Particular
ii. Comisión de Servicios
iii. Compensación
iv. Atención de Salud
v. Registro fuera del horario establecido
vi. Omisión de Registro de Marcación
🔙 Volver a Asistencia
🏠 Volver al menú principal',

// ---------------------- 2. LICENCIAS ------------------------
  '2. LICENCIAS' => '1. Licencias con goce de haber
2. Licencias sin goce de haber
3. Volver al menú principal',
  '3. Volver al menú principal' => \$menuPrincipal,

  '1. Licencias con goce de haber' => '1. Licencia por enfermedad
2. Licencia por maternidad
3. Licencia por paternidad
4. Licencia por fallecimiento de familiar directo
5. Licencia por enfermedad grave de familiar directo
6. Licencia por onomástico
7. Licencia por cita médica
🔙 Volver a Licencias
🏠 Volver al menú principal',

  '2. Licencias sin goce de haber' => '1. Licencia sin goce de haber
🔙 Volver a Licencias
🏠 Volver al menú principal',

  '1. Licencia por enfermedad' => 'Requisitos de acuerdo al establecimiento de salud que emite el certificado médico:
1. Seguro Social de Salud - EsSalud
* Formato Interno de descanso médico 📎
* Copia fedateada del Certificado de Incapacidad Temporal para el Trabajo - CITT
2. Ministerio de Salud del Perú - MINSA, Clínicas Privadas, Entidades Prestadoras de Salud - EPS, Sanidad de las Fuerzas Armadas, Sanidad de la Policía Nacional del Perú o Consultorios Médicos Particulares
* Formato Interno de descanso médico 📎
* Copia fedateada del descanso médico o Certificado Médico en especie valorada del Colegio Médico del Perú
* Copia fedateada del comprobante de pago de la atención médica, de corresponder
* Copia fedateada de la receta médica
🔙 Volver a Licencias con goce
🏠 Volver al menú principal',

  '2. Licencia por maternidad' => 'Requisitos de acuerdo al establecimiento de salud que emite el certificado médico:
1. Seguro Social de Salud - EsSalud
* Formato Interno de descanso médico 📎
* Copia fedateada del Certificado de Incapacidad Temporal para el Trabajo - CITT por Maternidad
2. Ministerio de Salud del Perú - MINSA, Clínicas Privadas, Entidades Prestadoras de Salud - EPS, Sanidad de las Fuerzas Armadas, Sanidad de la Policía Nacional del Perú o Consultorios Médicos Particulares
* Formato Interno de descanso médico 📎
* Copia fedateada del descanso médico o Certificado Médico en especie valorada
Consideraciones a tener en cuenta respecto a las solicitudes de licencia por enfermedad y maternidad:
1. El expediente de licencia por enfermedad debe ser presentado dentro de las cuarenta y ocho (48) horas de su emisión, pudiendo delegar a un familiar o a un tercero cercano para el trámite correspondiente.
2. La presentación es en forma física a través de la mesa de partes de Gerencia y Presidencia (Ventanilla N° 08)
🔙 Volver a Licencias con goce
🏠 Volver al menú principal',

  '3. Licencia por paternidad' => 'Ver Cartilla Informativa 📎
Requisitos:
1. La solicitud será presentada mediante la Carpeta Electrónica Administrativa (CEA) – Área de Potencial Humano Arequipa-Pacheco Apaza, Gonzalo Alexander (sólo al personal indicado).
2. Copia fedateada del acta de nacido vivo o partida de nacimiento del menor de corresponder.
3. Copia del informe u orden de alta de la madre o del recién nacido de corresponder.
🔙 Volver a Licencias con goce
🏠 Volver al menú principal',

  '4. Licencia por fallecimiento de familiar directo' => 'Ver Cartilla Informativa 📎
Requisitos:
1. La solicitud será presentada mediante la Carpeta Electrónica Administrativa (CEA) – Área de Potencial Humano Arequipa-Pacheco Apaza, Gonzalo Alexander (sólo al personal indicado).
2. Adjuntar el acta o certificado de Defunción
3. Documento que acredite la vinculación de parentesco
🔙 Volver a Licencias con goce
🏠 Volver al menú principal',

  '5. Licencia por enfermedad grave de familiar directo' => 'Ver Cartilla Informativa 📎
Requisitos:
1. Escrito presentado mediante la Carpeta Electrónica Administrativa (CEA) – Área de Potencial Humano Arequipa-Pacheco Apaza, Gonzalo Alexander (sólo al personal indicado).
2. Documento que acredite la vinculación de parentesco
3. 📎 Formato de Certificado Médico Ley N° 30012
🔙 Volver a Licencias con goce
🏠 Volver al menú principal',

  '6. Licencia por onomástico' => 'Suspensión o reprogramación
* Se suspende solo por necesidad de servicio y su programación puede diferirse hasta (07) siete días calendarios posteriores de la fecha programada.
* El escrito de reprogramación se presenta hasta con 24 horas de antelación de la fecha programada, y con visto bueno del jefe inmediato.
* Escrito presentado mediante la Carpeta Electrónica Administrativa (CEA) – Área de Potencial Humano Arequipa-Pacheco Apaza, Gonzalo Alexander (sólo al personal indicado).
🔙 Volver a Licencias con goce
🏠 Volver al menú principal',

  '7. Licencia por cita médica' => 'Requisitos
* Escrito indicando lugar, fecha y hora de la cita médica, presentado mediante la Carpeta Electrónica Administrativa (CEA) – Área de Potencial Humano Arequipa-Pacheco Apaza, Gonzalo Alexander (sólo al personal indicado).
* Una vez culminada la atención remitir la constancia de atención señalando el número de expediente de la anterior solicitud.
🔙 Volver a Licencias con goce
🏠 Volver al menú principal',

  '1. Licencia sin goce de haber' => 'Ver Cartilla Informativa 📎
Requisitos
1. Solicitud con el visto bueno de su jefe inmediato
2. Presentar solicitud 5 días hábiles antes del inicio de la licencia
3. Escrito presentado mediante la Carpeta Electrónica Administrativa (CEA) – Área de Potencial Humano Arequipa-Pacheco Apaza, Gonzalo Alexander (sólo al personal indicado).
🔙 Volver a Licencias sin goce
🏠 Volver al menú principal',

  '🔙 Volver a Licencias con goce' => '1. Licencia por enfermedad
2. Licencia por maternidad
3. Licencia por paternidad
4. Licencia por fallecimiento de familiar directo
5. Licencia por enfermedad grave de familiar directo
6. Licencia por onomástico
7. Licencia por cita médica
🔙 Volver a Licencias
🏠 Volver al menú principal',

  '🔙 Volver a Licencias sin goce' => '1. Licencia sin goce de haber
🔙 Volver a Licencias
🏠 Volver al menú principal',

  '🔙 Volver a Licencias' => '1. Licencias con goce de haber
2. Licencias sin goce de haber
3. Volver al menú principal',

// ---------------------- 3. BIENESTAR DE PERSONAL ------------------------
  '3. BIENESTAR DE PERSONAL' => '1. TRÁMITES ANTE ESSALUD Y EL ÁREA DE POTENCIAL HUMANO
2. SEGUROS
🏠 Volver al menú principal',

  '1. TRÁMITES ANTE ESSALUD Y EL ÁREA DE POTENCIAL HUMANO' => '1. REGISTRO DE DERECHOHABIENTES
2. 🏥 SOLICITUD DE SUBSIDIO
3. 💵Solicitud de pago diferencial
4. Postergación de la licencia por maternidad
🔙 Volver a Bienestar de personal
🏠 Volver al menú principal',

  '1. REGISTRO DE DERECHOHABIENTES' => '“Para que los familiares accedan a EsSalud, el trabajador debe presentar documentos según el tipo de familiar 👇” 
Ver Cartilla Informativa 📎
i. Requisitos
* Solicitud dirigida al Área de Potencial Humano 
* Formulario 1010 (02 juegos, solo firmar, sin llenar)
* Formato de política de privacidad para el tratamiento de datos personales. (01 juego, solo firmar, sin llenar)
* Copia DNI Titular
* Documentos según tipo de familiar (ver cartilla)
* 📞 950054080 para consultas
ii. 📍 Lugar de presentación
Mesa de Partes de Gerencia y Presidencia (Ventanilla N° 8) - Sede la Paz 320.
iii. 📎 Descargar formatos
* Formulario 1010
* Formato de política de privacidad para el tratamiento de datos personales
🔙 Volver a Trámites EsSalud
🏠 Volver al menú principal',

  '2. 🏥 SOLICITUD DE SUBSIDIO' => 'Las prestaciones económicas comprenden los subsidios por incapacidad temporal, maternidad y lactancia.
Ver Cartilla Informativa 📎
Requisitos subsidio por incapacidad y maternidad
* Solicitud dirigida al Área de Potencial Humano
* Se deberá adjuntar
* Certificado de Incapacidad Temporal para Trabajo original
* Copia de DNI (01 copia)
* Formulario 1040 (02 juegos, solo firmar, sin llenar)
* Formato de carta poder (02 juegos, solo firmar, sin llenar)
* Formato de política de privacidad para el tratamiento de datos personales. (02 juegos, solo firmar, sin llenar)
1. 📍 Lugar de presentación:
Mesa de Partes de Gerencia y Presidencia (Ventanilla N° 8) - Sede la Paz 320.
2. Descarga de formatos
* 📎 Formulario 1040
* 📎 Formato de carta poder
* 📎 Formato de política de privacidad para el tratamiento de datos personales. 
🔙 Volver a Trámites EsSalud
🏠 Volver al menú principal',

  '3. 💵Solicitud de pago diferencial' => 'Para iniciar el trámite, debes presentar el formato correspondiente adjuntando:
Requisitos
* Solicitud de Pago Diferencial (Formato)
* Copia Formulario 1040 con sello de EsSalud u Hoja de consulta NIT EsSalud (proporcionado por el personal a cargo Lic. Elizabeth Ticona Rojas).
* Recibo de pago electrónico de la entidad bancaria “voucher”.
1. 📍 Lugar de presentación
Mesa de Partes de Gerencia y Presidencia (Ventanilla N° 8) - Sede la Paz 320
2. 📎 Descargar formato
📥 Descargar formato de solicitud
🔙 Volver a Trámites EsSalud
🏠 Volver al menú principal',

  '4. Postergación de la licencia por maternidad' => 'Para iniciar el trámite, debes presentar:
Requisitos
* Solicitud Simple
* Adjunta Anexo 18.
1. 📍 Lugar de presentación
Mesa de Partes de Gerencia y Presidencia (Ventanilla N° 8) - Sede la Paz 320
2. 📎 Descargar formato
* 📎 Anexo 18
🔙 Volver a Trámites EsSalud
🏠 Volver al menú principal',

  '2. SEGUROS' => '1. ENTIDAD PRESTADORA DE SALUD – EPS
2. SEGUROS ADICIONALES
🔙 Volver a Bienestar de personal
🏠 Volver al menú principal',

  '1. ENTIDAD PRESTADORA DE SALUD – EPS' => 'Ver Cartilla Informativa 📎
1. Requisitos:
* Solicitud dirigida al Área de Potencial Humano
* Formato de Afiliación a EPS
* Presentar documentos según el tipo de familiar a afiliar, debiendo adjuntar :
* Copia de DNI del titular y de los derechohabientes a afiliar
* Acta de Nacimiento en caso de afiliar a hijos.
* Acta de matrimonio en caso de afiliar a su cónyuge.
2. 📍 Lugar de presentación
Mesa de Partes de Gerencia y Presidencia (Ventanilla N° 8) - Sede la Paz 320
3. Descarga de formatos
📎 Formato de Afiliación a EPS
🔙 Volver a Seguros
🏠 Volver al menú principal',

  '2. SEGUROS ADICIONALES' => 'Ver Cartilla Informativa 📎
1. SEGURO +VIDA (ESSALUD)
* Requisitos
* Solicitud Simple
* Formulario 6012 de Afiliación a +Vida
* Carta Poder (02 juegos, solo firmar, sin llenar)
* Formato de política de privacidad para el tratamiento de datos personales. (02 juegos, solo firmar, sin llenar)
* Formulario 1010 (02 juegos, solo firmar, sin llenar)
* Autorización Descuentos por planilla
* 📍 Lugar de presentación
* Mesa de Partes de Gerencia y Presidencia (Ventanilla N° 8) - Sede la Paz 320
* Descarga de formatos
* 📎 Formulario 6012
* 📎 Carta Poder
* 📎 Formato de política de privacidad para el tratamiento de datos personales
* 📎 Formulario 1010
* 📎 Autorización Descuentos por planilla
2. SEGURO DE ACCIDENTES PERSONALES
El trabajador puede atenderse en la red de clínicas dentro de las 48 horas de ocurrido el accidente, mostrando su DNI y Formato en original de Declaración de Accidentes.
📎 Relación de Clínicas afiliadas
3. SEGURO VIDA LEY
El servidor del régimen laboral  D. Leg. 728 debe presentar y/o actualizar su Declaración Jurada de Beneficiarios.
📎 Formato de Declaración
🔙 Volver a Seguros
🏠 Volver al menú principal',

  '🔙 Volver a Trámites EsSalud' => '1. REGISTRO DE DERECHOHABIENTES
2. 🏥 SOLICITUD DE SUBSIDIO
3. 💵Solicitud de pago diferencial
4. Postergación de la licencia por maternidad
🔙 Volver a Bienestar de personal
🏠 Volver al menú principal',

  '🔙 Volver a Seguros' => '1. ENTIDAD PRESTADORA DE SALUD – EPS
2. SEGUROS ADICIONALES
🔙 Volver a Bienestar de personal
🏠 Volver al menú principal',

  '🔙 Volver a Bienestar de personal' => '1. TRÁMITES ANTE ESSALUD Y EL ÁREA DE POTENCIAL HUMANO
2. SEGUROS
🏠 Volver al menú principal',

// ---------------------- 4. CREDENCIAL Y FOTOCHECK ------------------------
  '4. CREDENCIAL Y FOTOCHECK' => '1. Credencial – Carreras Especiales
2. Fotocheck
🏠 Volver al menú principal',

  '1. Credencial – Carreras Especiales' => 'Para solicitar tu credencial, puedes hacerlo en los siguientes casos:
1. Nuevo
Debes presentar el formato de solicitud de credencial adjuntando:
* 2 fotografías tamaño carnet (fondo blanco, blusa o camisa blanca y terno oscuro)
* Copia simple de DNI
* Copia simple de Acta de Juramentación
* Copia simple Resolución de designación
2. Robo – Pérdida
Debes presentar el formato de solicitud de credencial adjuntando:
* 2 fotografías tamaño carnet (fondo blanco, blusa o camisa blanca y terno oscuro)
* Copia simple de DNI
* Copia simple de Acta de Juramentación
* Copia simple de Resolución de designación 
* Copia de la denuncia policial
3. Caducidad/deterioro
* 2 fotografías tamaño carnet (fondo blanco, blusa o camisa blanca y terno oscuro)
* Copia simple de DNI
* Copia simple Acta de Juramentación
* Copia simple de Resolución de designación 
* Copia de credencial caducada 
1. 📎 Descargar formato
2. 📍 Lugar de presentación
Mesa de Partes de Gerencia y Presidencia (Ventanilla N° 8) - Sede la Paz 320.
🔙 Volver a Credencial
🏠 Volver al menú principal',

  '2. Fotocheck' => '1. Emisión
La emisión del fotocheck se realiza de oficio por el Área de Potencial Humano.
📢 El área se comunicará contigo para informarte la fecha y modalidad de entrega.
2. Devolución
Cuando finalice tu vínculo laboral, deberás devolver el fotocheck junto con la entrega de cargo.
📍 Presentación:
Mesa de Partes de Gerencia y Presidencia (Ventanilla N° 8) - Sede la Paz 320.
3. Robo – Pérdida
Para solicitar fotocheck por este motivo, deberás presentar un escrito dirijo al Área de Potencial Humano, adjuntando:
📄 Copia de la Denuncia Policial por pérdida de fotocheck.
📢 El Área de Potencial Humano se comunicará contigo para informarte la fecha y modalidad de entrega.
La denuncia policial puede ser presentada  en forma  digital en el siguiente enlace: 
Pasos a seguir para generar denuncia policial Dígital:
1. Ingresar a la página web de la Policía Nacional del Perú: www.policia.gob.pe
2. Buscar la sección “SERVICIOS EN LÍNEA”
3. Elegir la opción “DENUNCIA POLICIAL DIGITAL”
4. Seleccionar “NUEVO TRÁMITE”
🔙 Volver a Credencial
🏠 Volver al menú principal',

  '🔙 Volver a Credencial' => '1. Credencial – Carreras Especiales
2. Fotocheck
🏠 Volver al menú principal',

// ---------------------- 5. CONSTANCIAS Y CERTIFICADOS ------------------------
  '5. EMISIÓN DE CONSTANCIAS Y CERTIFICADOS DE TRABAJO' => 'Selecciona el trámite que deseas realizar:
1. 📄Solicitar Constancia de Trabajo
2. 📜Solicitar Certificado de Trabajo
🏠 Volver al menú principal',

  '1. 📄Solicitar Constancia de Trabajo' => '¿Cómo deseas solicitarla?
1. 💻A través del sistema institucional
2. 📝Mediante solicitud escrita
3. 🎥Ver video tutorial
4. Descargar modelo de solicitud
🔙 Volver a Constancias
🏠 Volver al menú principal',

  '1. 💻A través del sistema institucional' => 'Puedes realizar tu solicitud ingresando a:
👉 https://Sistemas2.mpfn.gob.pe
Pasos:
* Ingresa con tu usuario institucional.
* Seleccione el Módulo de Emisión de Constancias
* Elige el tipo de constancia (Simple, Histórica, Personalizada).
* Envía tu solicitud.
* Recibirás la confirmación en tu correo registrado
📌 Importante:
Si tienes problemas de acceso, comunícate con la Mesa de Ayuda del Área de Tecnologías de la Información:
📞 937 461 856
🔙 Volver a Constancias
🏠 Volver al menú principal',

  '2. 📝Mediante solicitud escrita' => 'Si no puedes acceder al sistema, puedes presentar una solicitud simple 
presentado por CEA –Oficina General de Potencial Humano
📧 Correo electrónico: mesap.gerencia.aqp@mpfn.gob.pe
[📎 Descargar modelo de solicitud (WORD)](https://docs.google.com/document/d/1TajA-HSIIZiuv3C6ikZqk0gfAA_H4gWs/edit?usp=drive_link&ouid=111203226793113830230&rtpof=true&sd=true)
🔙 Volver a Constancias
🏠 Volver al menú principal',

  '3. 🎥Ver video tutorial' => 'Puedes revisar el tutorial para realizar tu solicitud en el sistema institucional:
📺 Ver video
🔙 Volver a Constancias
🏠 Volver al menú principal',

  '4. Descargar modelo de solicitud' => '📎 [Descargar modelo de solicitud (WORD)](https://docs.google.com/document/d/1TajA-HSIIZiuv3C6ikZqk0gfAA_H4gWs/edit?usp=drive_link&ouid=111203226793113830230&rtpof=true&sd=true)
🔙 Volver a Constancias
🏠 Volver al menú principal',

  '2. 📜Solicitar Certificado de Trabajo' => 'El certificado de trabajo se emite al término del vínculo laboral.
Requisitos:
📝 Solicitud simple
¿Cómo presentarlo?
Puedes hacerlo mediante:
📍 Mesa de Partes de Gerencia y Presidencia (Ventanilla N° 8) - Sede la Paz 320.
📧 Correo electrónico: mesap.gerencia.aqp@mpfn.gob.pe
[📎 Descargar modelo de solicitud (WORD)](https://docs.google.com/document/d/1tOVt_4nDTDkXifd-mLaAMDcjUJjeUVyk/edit?usp=drive_link&ouid=111203226793113830230&rtpof=true&sd=true)
🔙 Volver a Constancias
🏠 Volver al menú principal',

  '🔙 Volver a Constancias' => 'Selecciona el trámite que deseas realizar:
1. 📄Solicitar Constancia de Trabajo
2. 📜Solicitar Certificado de Trabajo
🏠 Volver al menú principal',

// ---------------------- 6. VACACIONES ------------------------
  '6. VACACIONES' => '1. Programación de vacaciones anual
2. Reprogramación de vacaciones
3. Adelanto de vacaciones
🏠 Volver al menú principal',

  '1. Programación de vacaciones anual' => 'Se realiza una vez al año, generalmente a fin de año.
La programación se realiza a través de Sistemas2.mpfn.gob.pe
🎥 Videotutorial:
* Para jefes: [Ver videotutorial jefe](https://drive.google.com/drive/folders/1abhH2FikOVARPnx2w0n3leTnITKqX8de)
* Para trabajadores: [Ver videotutorial trabajador](https://drive.google.com/drive/folders/1abhH2FikOVARPnx2w0n3leTnITKqX8de)
🔙 Volver a Vacaciones
🏠 Volver al menú principal',

  '2. Reprogramación de vacaciones' => '1. Solicitud de reprogramación
2. Recomendaciones
3. Modelo de Solicitud
🔙 Volver a Vacaciones
🏠 Volver al menú principal',

  '1. Solicitud de reprogramación' => '* Presentar el escrito hasta el quinto día hábil anterior al inicio de tus vacaciones.
* Debe contar con V°B° del Jefe Inmediato y firma del solicitante.
* Presentarlo mediante la Carpeta Electrónica Administrativa (CEA) – Área de Potencial Humano Arequipa-Patiño Juarez Karla (sólo al personal indicado).
* IMPORTANTE: En el asunto colocar: Reprogramación de Vacaciones -(Apellidos y Nombres)
🔙 Volver a Reprogramación
🏠 Volver al menú principal',

  '2. Recomendaciones' => '* La suma de todos los periodos fraccionados no puede superar 30 días calendarios.
* No se pueden tomar más de 4 días hábiles por semana de los 7 días hábiles fraccionables.
* Si el periodo inicia o termina un viernes, los sábados y domingos siguientes también se computan.
* El acuerdo de fraccionamiento debe ser previo al disfrute de las vacaciones y debe incluir las fechas originales y nuevas.
🔙 Volver a Reprogramación
🏠 Volver al menú principal',

  '3. Modelo de Solicitud' => '📎 [Descargar modelo de solicitud (WORD)](https://docs.google.com/document/d/1TajA-HSIIZiuv3C6ikZqk0gfAA_H4gWs/edit?usp=drive_link&ouid=111203226793113830230&rtpof=true&sd=true)
🔙 Volver a Reprogramación
🏠 Volver al menú principal',

  '3. Adelanto de vacaciones' => 'Para verificar si puedes acceder al adelanto de vacaciones, comunícate al:
📞 949 305 573
🔙 Volver a Vacaciones
🏠 Volver al menú principal',

  '🔙 Volver a Reprogramación' => '1. Solicitud de reprogramación
2. Recomendaciones
3. Modelo de Solicitud
🔙 Volver a Vacaciones
🏠 Volver al menú principal',

  '🔙 Volver a Vacaciones' => '1. Programación de vacaciones anual
2. Reprogramación de vacaciones
3. Adelanto de vacaciones
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

  'a. ✅ Sí, tengo firma digital' => '* Ingresa al sistema oficial: https://apps1.contraloria.gob.pe/ddjj/
* Inicia sesión.
* Completa los campos solicitados.
* Firma
* Una vez firmado, comunicarse con el personal Encargado al número 959860944
🔙 Volver a DJ Bienes
🏠 Volver al menú principal',

  'b. ❌ No, tengo firma digital' => '* Ingresa al sistema oficial: https://apps1.contraloria.gob.pe/ddjj/
* Inicia sesión.
* Completa los campos solicitados.
* Imprime 3 ejemplares (1 cargo, 2 serán enviados a Lima)
* Firma todas las hojas
* Acércate al Área de Potencial Humano a fin de entregar los formatos.
🔙 Volver a DJ Bienes
🏠 Volver al menú principal',

  'c. Video Tutorial' => '🎥 Puedes ver el video tutorial aquí:
https://www.youtube.com/watch?v=j1UzD122NlA
🔙 Volver a DJ Bienes
🏠 Volver al menú principal',

  '🔙 Volver a DJ Bienes' => 'a. ✅ Sí, tengo firma digital
b. ❌ No, tengo firma digital
c. Video Tutorial
🏠 Volver al menú principal',

  '8. DECLARACIÓN JURADA DE INTERESES' => 'Se encuentran obligados a presentar DECLARACIÓN JURADA DE INTERESES, quienes ocupen los cargos o desarrollen las funciones establecidas en el Art. 3 de la Ley 31227.
La declaración jurada de intereses se presenta en la siguiente oportunidad:
a) Al inicio: Dentro de los quince (15) días hábiles de haber sido elegido/a, nombrado/a, designado/a, contratado/a o similares. 
b) Periódica: Durante los primeros quince (15) días hábiles, después de doce (12) meses de ejercida la labor.
c) Al cese: Dentro de los quince (15) días hábiles de haberse extinguido el vínculo laboral o contractual.
Para registrar tu Declaración Jurada de Intereses en el sistema de la Contraloría General de la República del Perú, sigue estos pasos:
a. ✅ Sí, tengo firma digital (Intereses)
b. ❌ No, tengo firma digital (Intereses)
🏠 Volver al menú principal',

  'a. ✅ Sí, tengo firma digital (Intereses)' => '* Ingresa al sistema oficial: https://appdji.contraloria.gob.pe/djic/
* Inicia sesión.
* Completa los campos solicitados.
* 🎥 Puedes ver el video tutorial aquí: https://www.youtube.com/watch?v=TNK0fJbIU_8
* Descargar Manual del Declarante.
🔙 Volver a DJ Intereses
🏠 Volver al menú principal',

  'b. ❌ No, tengo firma digital (Intereses)' => 'Para obtener soporte, comunícate con:
📞 Mesa de Ayuda – Área de Tecnologías de la Información
Número: 937461856
🔙 Volver a DJ Intereses
🏠 Volver al menú principal',

  '🔙 Volver a DJ Intereses' => 'a. ✅ Sí, tengo firma digital (Intereses)
b. ❌ No, tengo firma digital (Intereses)
🏠 Volver al menú principal',

  '9. BOLETAS DE PAGO' => 'Para visualizar y descargar tu boleta de pago, sigue estos pasos:
👉 Ingresa al sistema institucional: https://Sistemas2.mpfn.gob.pe
a. 📂 Dentro del sistema:
b. 🎥 ¿Necesitas ayuda?
c. ⚠️ Soporte técnico
🏠 Volver al menú principal',

  'a. 📂 Dentro del sistema:' => '1. Selecciona Sistema de Gestión de Documentos Laborales – SIGEDOL.
2. Elige una de las siguientes opciones:
* 📌 Pendientes: Verás las boletas que aún no has visualizado.
* 📁 Históricas: Verás las boletas ya visualizadas.
3. Selecciona la boleta que deseas consultar.
4. Descárgala en tu dispositivo.
5. Si deseas conocer el detalle de los conceptos puedes comunicarte al 959 860 944
🔙 Volver a Boletas de pago
🏠 Volver al menú principal',

  'b. 🎥 ¿Necesitas ayuda?' => '📺 Ver video tutorial
🔙 Volver a Boletas de pago
🏠 Volver al menú principal',

  'c. ⚠️ Soporte técnico' => 'Si no visualizas el sistema SIGEDOL dentro de Sistemas2, comunícate con:
📞 Mesa de Ayuda – Área de Tecnologías de la Información
937 461 856
🔙 Volver a Boletas de pago
🏠 Volver al menú principal'
";

    $content = substr_replace($content, $newArray, $startPos, $endPos - $startPos);
    file_put_contents($file, $content);
    echo "Injection successful.";
} else {
    echo "Could not find start or end positions.";
}
