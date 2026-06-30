<?php
$file = 'c:\\xampp\\htdocs\\siga\\chatbot_potencial_humano\\api.php';
$content = file_get_contents($file);

$replacements = [
// 2. LICENCIAS
"  '2. LICENCIAS' => 'Selecciona el tipo de licencia:
a. Licencias con goce de haber
b. Licencias sin goce de haber
🏠 Volver al menú principal'," => "  '2. LICENCIAS' => 'Selecciona el tipo de licencia:
1. Licencias con goce de haber
2. Licencias sin goce de haber
🏠 Volver al menú principal',",

"  'a. Licencias con goce de haber' => 'i. Licencia por enfermedad
ii. Licencia por maternidad
iii. Licencia por paternidad
iv. Licencia por fallecimiento de familiar directo
v. Licencia por enfermedad grave de familiar directo
vi. Licencia por onomástico
vii. Licencia por cita médica
🔙 Volver a Licencias
🏠 Volver al menú principal'," => "  '1. Licencias con goce de haber' => '1. Licencia por enfermedad
2. Licencia por maternidad
3. Licencia por paternidad
4. Licencia por fallecimiento de familiar directo
5. Licencia por enfermedad grave de familiar directo
6. Licencia por onomástico
7. Licencia por cita médica
🔙 Volver a Licencias
🏠 Volver al menú principal',",

"  'b. Licencias sin goce de haber' => 'i. Licencia sin goce de haber
🔙 Volver a Licencias
🏠 Volver al menú principal'," => "  '2. Licencias sin goce de haber' => '1. Licencia sin goce de haber
🔙 Volver a Licencias
🏠 Volver al menú principal',",

"  'i. Licencia por enfermedad' => 'Requisitos según el establecimiento de salud:
* EsSalud: Formato interno, Copia fedateada de CITT.
* MINSA/Clínicas/Privados: Formato interno, Copia fedateada de descanso médico, comprobante de pago y receta médica.
⚠️ Presentar dentro de las 48 horas de emisión en Mesa de Partes (Ventanilla 8).
🔙 Volver a Licencias con goce
🏠 Volver al menú principal'," => "  '1. Licencia por enfermedad' => 'Requisitos de acuerdo al establecimiento de salud que emite el certificado médico:
1. Seguro Social de Salud - EsSalud
* Formato Interno de descanso médico 📎
* Copia fedateada del Certificado de Incapacidad Temporal para el Trabajo - CITT
2. Ministerio de Salud del Perú - MINSA, Clínicas Privadas, Entidades Prestadoras de Salud - EPS, Sanidad de las Fuerzas Armadas, Sanidad de la Policía Nacional del Perú o Consultorios Médicos Particulares
* Formato Interno de descanso médico 📎
* Copia fedateada del descanso médico o Certificado Médico en especie valorada del Colegio Médico del Perú
* Copia fedateada del comprobante de pago de la atención médica, de corresponder
* Copia fedateada de la receta médica
🔙 Volver a Licencias con goce
🏠 Volver al menú principal',",

"  'ii. Licencia por maternidad' => 'Requisitos según el establecimiento de salud:
* EsSalud: Formato interno, Copia fedateada de CITT por Maternidad.
* MINSA/Privados: Formato interno, Copia fedateada del descanso médico.
⚠️ Presentar dentro de las 48 horas en Mesa de Partes (Ventanilla 8).
🔙 Volver a Licencias con goce
🏠 Volver al menú principal'," => "  '2. Licencia por maternidad' => 'Requisitos de acuerdo al establecimiento de salud que emite el certificado médico:
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
🏠 Volver al menú principal',",

"  'iii. Licencia por paternidad' => 'Ver Cartilla Informativa 📎
Requisitos:
* Solicitud mediante CEA a Arequipa-Pacheco Apaza, Gonzalo Alexander.
* Copia fedateada del acta de nacido vivo o partida.
* Copia del informe de alta de la madre o recién nacido.
🔙 Volver a Licencias con goce
🏠 Volver al menú principal'," => "  '3. Licencia por paternidad' => 'Ver Cartilla Informativa 📎
Requisitos:
1. La solicitud será presentada mediante la Carpeta Electrónica Administrativa (CEA) – Área de Potencial Humano Arequipa-Pacheco Apaza, Gonzalo Alexander (sólo al personal indicado).
2. Copia fedateada del acta de nacido vivo o partida de nacimiento del menor de corresponder.
3. Copia del informe u orden de alta de la madre o del recién nacido de corresponder.
🔙 Volver a Licencias con goce
🏠 Volver al menú principal',",

"  'iv. Licencia por fallecimiento de familiar directo' => 'Ver Cartilla Informativa 📎
Requisitos:
* Solicitud mediante CEA a Arequipa-Pacheco Apaza, Gonzalo Alexander.
* Acta o certificado de Defunción.
* Documento que acredite el parentesco.
🔙 Volver a Licencias con goce
🏠 Volver al menú principal'," => "  '4. Licencia por fallecimiento de familiar directo' => 'Ver Cartilla Informativa 📎
Requisitos:
1. La solicitud será presentada mediante la Carpeta Electrónica Administrativa (CEA) – Área de Potencial Humano Arequipa-Pacheco Apaza, Gonzalo Alexander (sólo al personal indicado).
2. Adjuntar el acta o certificado de Defunción
3. Documento que acredite la vinculación de parentesco
🔙 Volver a Licencias con goce
🏠 Volver al menú principal',",

"  'v. Licencia por enfermedad grave de familiar directo' => 'Ver Cartilla Informativa 📎
Requisitos:
* Solicitud mediante CEA a Arequipa-Pacheco Apaza, Gonzalo Alexander.
* Documento que acredite parentesco.
* Formato de Certificado Médico Ley N° 30012 📎
🔙 Volver a Licencias con goce
🏠 Volver al menú principal'," => "  '5. Licencia por enfermedad grave de familiar directo' => 'Ver Cartilla Informativa 📎
Requisitos:
1. Escrito presentado mediante la Carpeta Electrónica Administrativa (CEA) – Área de Potencial Humano Arequipa-Pacheco Apaza, Gonzalo Alexander (sólo al personal indicado).
2. Documento que acredite la vinculación de parentesco
3. 📎 Formato de Certificado Médico Ley N° 30012
🔙 Volver a Licencias con goce
🏠 Volver al menú principal',",

"  'vi. Licencia por onomástico' => 'Suspensión o reprogramación:
* Se difiere hasta 7 días calendarios posteriores si hay necesidad de servicio.
* Presentar solicitud 24 horas antes mediante CEA a Arequipa-Pacheco Apaza, Gonzalo Alexander con V°B° del jefe.
🔙 Volver a Licencias con goce
🏠 Volver al menú principal'," => "  '6. Licencia por onomástico' => 'Suspensión o reprogramación
* Se suspende solo por necesidad de servicio y su programación puede diferirse hasta (07) siete días calendarios posteriores de la fecha programada.
* El escrito de reprogramación se presenta hasta con 24 horas de antelación de la fecha programada, y con visto bueno del jefe inmediato.
* Escrito presentado mediante la Carpeta Electrónica Administrativa (CEA) – Área de Potencial Humano Arequipa-Pacheco Apaza, Gonzalo Alexander (sólo al personal indicado).
🔙 Volver a Licencias con goce
🏠 Volver al menú principal',",

"  'vii. Licencia por cita médica' => 'Requisitos:
* Solicitud en CEA a Arequipa-Pacheco Apaza, Gonzalo Alexander indicando lugar, fecha y hora.
* Culminada la atención, remitir la constancia señalando el número de expediente anterior.
🔙 Volver a Licencias con goce
🏠 Volver al menú principal'," => "  '7. Licencia por cita médica' => 'Requisitos
* Escrito indicando lugar, fecha y hora de la cita médica, presentado mediante la Carpeta Electrónica Administrativa (CEA) – Área de Potencial Humano Arequipa-Pacheco Apaza, Gonzalo Alexander (sólo al personal indicado).
* Una vez culminada la atención remitir la constancia de atención señalando el número de expediente de la anterior solicitud.
🔙 Volver a Licencias con goce
🏠 Volver al menú principal',",

"  'i. Licencia sin goce de haber' => 'Ver Cartilla Informativa 📎
Requisitos:
* Solicitud con V°B° del jefe inmediato, presentada 5 días hábiles antes.
* Enviar mediante CEA a Arequipa-Pacheco Apaza, Gonzalo Alexander.
🔙 Volver a Licencias sin goce
🏠 Volver al menú principal'," => "  '1. Licencia sin goce de haber' => 'Ver Cartilla Informativa 📎
Requisitos
1. Solicitud con el visto bueno de su jefe inmediato
2. Presentar solicitud 5 días hábiles antes del inicio de la licencia
3. Escrito presentado mediante la Carpeta Electrónica Administrativa (CEA) – Área de Potencial Humano Arequipa-Pacheco Apaza, Gonzalo Alexander (sólo al personal indicado).
🔙 Volver a Licencias sin goce
🏠 Volver al menú principal',",

"  '🔙 Volver a Licencias con goce' => 'i. Licencia por enfermedad
ii. Licencia por maternidad
iii. Licencia por paternidad
iv. Licencia por fallecimiento
v. Licencia por enfermedad grave de familiar directo
vi. Licencia por onomástico
vii. Licencia por cita médica
🔙 Volver a Licencias
🏠 Volver al menú principal'," => "  '🔙 Volver a Licencias con goce' => '1. Licencia por enfermedad
2. Licencia por maternidad
3. Licencia por paternidad
4. Licencia por fallecimiento de familiar directo
5. Licencia por enfermedad grave de familiar directo
6. Licencia por onomástico
7. Licencia por cita médica
🔙 Volver a Licencias
🏠 Volver al menú principal',",

"  '🔙 Volver a Licencias sin goce' => 'i. Licencia sin goce de haber
🔙 Volver a Licencias
🏠 Volver al menú principal'," => "  '🔙 Volver a Licencias sin goce' => '1. Licencia sin goce de haber
🔙 Volver a Licencias
🏠 Volver al menú principal',",

"  '🔙 Volver a Licencias' => 'a. Licencias con goce de haber
b. Licencias sin goce de haber
🏠 Volver al menú principal'," => "  '🔙 Volver a Licencias' => '1. Licencias con goce de haber
2. Licencias sin goce de haber
🏠 Volver al menú principal',",

// 3. BIENESTAR DE PERSONAL
"  '3. BIENESTAR DE PERSONAL' => 'a. TRÁMITES ANTE ESSALUD Y EL ÁREA DE POTENCIAL HUMANO
b. SEGUROS
🏠 Volver al menú principal'," => "  '3. BIENESTAR DE PERSONAL' => '1. TRÁMITES ANTE ESSALUD Y EL ÁREA DE POTENCIAL HUMANO
2. SEGUROS
🏠 Volver al menú principal',",

"  'a. TRÁMITES ANTE ESSALUD Y EL ÁREA DE POTENCIAL HUMANO' => 'i. REGISTRO DE DERECHOHABIENTES
ii. 🏥 SOLICITUD DE SUBSIDIO
iii. 💵 Solicitud de pago diferencial
iv. Postergación de la licencia por maternidad
🔙 Volver a Bienestar de personal
🏠 Volver al menú principal'," => "  '1. TRÁMITES ANTE ESSALUD Y EL ÁREA DE POTENCIAL HUMANO' => '1. REGISTRO DE DERECHOHABIENTES
2. 🏥 SOLICITUD DE SUBSIDIO
3. 💵Solicitud de pago diferencial
4. Postergación de la licencia por maternidad
🔙 Volver a Bienestar de personal
🏠 Volver al menú principal',",

"  'i. REGISTRO DE DERECHOHABIENTES' => '\"Para que los familiares accedan a EsSalud, el trabajador debe presentar documentos según el tipo de familiar 👇\"
Ver Cartilla Informativa 📎
Requisitos:
* Formulario 1010 y Formato de política de privacidad (firmados).
* Copia DNI Titular.
📍 Presentación: Mesa de Partes (Ventanilla N° 8)
📞 950054080 para consultas
🔙 Volver a Trámites EsSalud
🏠 Volver al menú principal'," => "  '1. REGISTRO DE DERECHOHABIENTES' => '“Para que los familiares accedan a EsSalud, el trabajador debe presentar documentos según el tipo de familiar 👇” 
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
🏠 Volver al menú principal',",

"  'ii. 🏥 SOLICITUD DE SUBSIDIO' => 'Prestaciones por incapacidad temporal, maternidad y lactancia.
Ver Cartilla Informativa 📎
Requisitos:
* CITT original, Copia de DNI.
* Formulario 1040, Carta poder, Formato de privacidad (juegos firmados).
📍 Presentación: Mesa de Partes (Ventanilla N° 8)
🔙 Volver a Trámites EsSalud
🏠 Volver al menú principal'," => "  '2. 🏥 SOLICITUD DE SUBSIDIO' => 'Las prestaciones económicas comprenden los subsidios por incapacidad temporal, maternidad y lactancia.
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
🏠 Volver al menú principal',",

"  'iii. 💵 Solicitud de pago diferencial' => 'Requisitos:
* Formato de Solicitud de Pago Diferencial.
* Copia Formulario 1040 con sello o NIT EsSalud (por Lic. Elizabeth Ticona Rojas).
* Recibo electrónico bancario “voucher”.
📍 Presentación: Mesa de Partes (Ventanilla N° 8)
🔙 Volver a Trámites EsSalud
🏠 Volver al menú principal'," => "  '3. 💵Solicitud de pago diferencial' => 'Para iniciar el trámite, debes presentar el formato correspondiente adjuntando:
Requisitos
* Solicitud de Pago Diferencial (Formato)
* Copia Formulario 1040 con sello de EsSalud u Hoja de consulta NIT EsSalud (proporcionado por el personal a cargo Lic. Elizabeth Ticona Rojas).
* Recibo de pago electrónico de la entidad bancaria “voucher”.
1. 📍 Lugar de presentación
Mesa de Partes de Gerencia y Presidencia (Ventanilla N° 8) - Sede la Paz 320
2. 📎 Descargar formato
📥 Descargar formato de solicitud
🔙 Volver a Trámites EsSalud
🏠 Volver al menú principal',",

"  'iv. Postergación de la licencia por maternidad' => 'Requisitos:
* Solicitud Simple
* Anexo 18
📍 Presentación: Mesa de Partes (Ventanilla N° 8)
🔙 Volver a Trámites EsSalud
🏠 Volver al menú principal'," => "  '4. Postergación de la licencia por maternidad' => 'Para iniciar el trámite, debes presentar:
Requisitos
* Solicitud Simple
* Adjunta Anexo 18.
1. 📍 Lugar de presentación
Mesa de Partes de Gerencia y Presidencia (Ventanilla N° 8) - Sede la Paz 320
2. 📎 Descargar formato
* 📎 Anexo 18
🔙 Volver a Trámites EsSalud
🏠 Volver al menú principal',",

"  '🔙 Volver a Trámites EsSalud' => 'i. REGISTRO DE DERECHOHABIENTES
ii. 🏥 SOLICITUD DE SUBSIDIO
iii. 💵 Solicitud de pago diferencial
iv. Postergación de la licencia por maternidad
🔙 Volver a Bienestar de personal
🏠 Volver al menú principal'," => "  '🔙 Volver a Trámites EsSalud' => '1. REGISTRO DE DERECHOHABIENTES
2. 🏥 SOLICITUD DE SUBSIDIO
3. 💵Solicitud de pago diferencial
4. Postergación de la licencia por maternidad
🔙 Volver a Bienestar de personal
🏠 Volver al menú principal',",

"  'b. SEGUROS' => 'i. ENTIDAD PRESTADORA DE SALUD – EPS
ii. SEGUROS ADICIONALES
🔙 Volver a Bienestar de personal
🏠 Volver al menú principal'," => "  '2. SEGUROS' => '1. ENTIDAD PRESTADORA DE SALUD – EPS
2. SEGUROS ADICIONALES
🔙 Volver a Bienestar de personal
🏠 Volver al menú principal',",

"  'i. ENTIDAD PRESTADORA DE SALUD – EPS' => 'Ver Cartilla Informativa 📎
Requisitos: Solicitud, Formato de Afiliación EPS, Copia de DNI y actas correspondientes.
📍 Presentación: Mesa de Partes (Ventanilla N° 8)
🔙 Volver a Seguros
🏠 Volver al menú principal'," => "  '1. ENTIDAD PRESTADORA DE SALUD – EPS' => 'Ver Cartilla Informativa 📎
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
🏠 Volver al menú principal',",

"  'ii. SEGUROS ADICIONALES' => '1. SEGURO +VIDA (ESSALUD)
Solicitud, Formulario 6012, Carta Poder, Autorización Descuentos, Formato Privacidad.
2. SEGURO DE ACCIDENTES PERSONALES
Atención en red de clínicas dentro de las 48h con DNI y Formato de Accidentes.
3. SEGURO VIDA LEY
Servidor 728 debe actualizar su Declaración Jurada de Beneficiarios.
🔙 Volver a Seguros
🏠 Volver al menú principal'," => "  '2. SEGUROS ADICIONALES' => 'Ver Cartilla Informativa 📎
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
🏠 Volver al menú principal',",

"  '🔙 Volver a Seguros' => 'i. ENTIDAD PRESTADORA DE SALUD – EPS
ii. SEGUROS ADICIONALES
🔙 Volver a Bienestar de personal
🏠 Volver al menú principal'," => "  '🔙 Volver a Seguros' => '1. ENTIDAD PRESTADORA DE SALUD – EPS
2. SEGUROS ADICIONALES
🔙 Volver a Bienestar de personal
🏠 Volver al menú principal',",

"  '🔙 Volver a Bienestar de personal' => 'a. TRÁMITES ANTE ESSALUD Y EL ÁREA DE POTENCIAL HUMANO
b. SEGUROS
🏠 Volver al menú principal'," => "  '🔙 Volver a Bienestar de personal' => '1. TRÁMITES ANTE ESSALUD Y EL ÁREA DE POTENCIAL HUMANO
2. SEGUROS
🏠 Volver al menú principal',",

// 4. CREDENCIAL Y FOTOCHECK
"  '4. CREDENCIAL Y FOTOCHECK' => 'a. Credencial – Carreras Especiales
b. Fotocheck
🏠 Volver al menú principal'," => "  '4. CREDENCIAL Y FOTOCHECK' => '1. Credencial – Carreras Especiales
2. Fotocheck
🏠 Volver al menú principal',",

"  'a. Credencial – Carreras Especiales' => 'i. Nuevo / Robo - Pérdida / Caducidad - Deterioro
Debes presentar el formato de solicitud de credencial adjuntando:
• 2 fotografías tamaño carnet (fondo blanco, blusa o camisa blanca y terno oscuro)
• Copia simple de DNI
• Copia simple de Acta de Juramentación
• Copia simple Resolución de designación
• Copia de denuncia policial (si es robo/pérdida)
• Copia de credencial caducada (si aplica)
📍 Lugar de presentación:
Mesa de Partes de Gerencia y Presidencia (Ventanilla N° 8) - Sede la Paz 320.
🔙 Volver a Credencial
🏠 Volver al menú principal'," => "  '1. Credencial – Carreras Especiales' => 'Para solicitar tu credencial, puedes hacerlo en los siguientes casos:
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
🏠 Volver al menú principal',",

"  'b. Fotocheck' => '1. Emisión: Se realiza de oficio por el Área de Potencial Humano.
2. Devolución: Al finalizar el vínculo laboral, junto con la entrega de cargo (Mesa de Partes).
3. Robo - Pérdida: Presentar escrito adjuntando Copia de Denuncia Policial (se puede generar virtualmente en la PNP).
🔙 Volver a Credencial
🏠 Volver al menú principal'," => "  '2. Fotocheck' => '1. Emisión
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
🏠 Volver al menú principal',",

"  '🔙 Volver a Credencial' => 'a. Credencial – Carreras Especiales
b. Fotocheck
🏠 Volver al menú principal'," => "  '🔙 Volver a Credencial' => '1. Credencial – Carreras Especiales
2. Fotocheck
🏠 Volver al menú principal',",

// 5. CONSTANCIAS
"  '5. EMISIÓN DE CONSTANCIAS Y CERTIFICADOS DE TRABAJO' => 'Selecciona el trámite que deseas realizar:
a. 📄 Solicitar Constancia de Trabajo
b. 📜 Solicitar Certificado de Trabajo
🏠 Volver al menú principal'," => "  '5. EMISIÓN DE CONSTANCIAS Y CERTIFICADOS DE TRABAJO' => 'Selecciona el trámite que deseas realizar:
1. 📄Solicitar Constancia de Trabajo
2. 📜Solicitar Certificado de Trabajo
🏠 Volver al menú principal',",

"  'a. 📄 Solicitar Constancia de Trabajo' => '¿Cómo deseas solicitarla?
i. 💻 A través del sistema institucional
ii. 📝 Mediante solicitud escrita
🔙 Volver a Constancias
🏠 Volver al menú principal'," => "  '1. 📄Solicitar Constancia de Trabajo' => '¿Cómo deseas solicitarla?
1. 💻A través del sistema institucional
2. 📝Mediante solicitud escrita
3. 🎥Ver video tutorial
4. Descargar modelo de solicitud
🔙 Volver a Constancias
🏠 Volver al menú principal',",

"  'i. 💻 A través del sistema institucional' => 'Puedes realizar tu solicitud ingresando a: 👉 https://Sistemas2.mpfn.gob.pe
Pasos:
* Ingresa con tu usuario institucional.
* Seleccione el Módulo de Emisión de Constancias
* Elige el tipo de constancia (Simple, Histórica, Personalizada).
* Envía tu solicitud.
* Recibirás la confirmación en tu correo registrado
📌 Importante:
Si tienes problemas de acceso, comunícate con la Mesa de Ayuda del Área de Tecnologías de la Información: 📞 937 461 856
[🎥 Ver video tutorial](https://www.youtube.com/watch?v=tutorial_constancia)
🔙 Volver a Constancias
🏠 Volver al menú principal'," => "  '1. 💻A través del sistema institucional' => 'Puedes realizar tu solicitud ingresando a:
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
🏠 Volver al menú principal',",

"  'ii. 📝 Mediante solicitud escrita' => 'Si no puedes acceder al sistema, puedes presentar una solicitud simple presentado por CEA –Oficina General de Potencial Humano
📧 Correo electrónico: mesap.gerencia.aqp@mpfn.gob.pe
[📎 Descargar modelo de solicitud (WORD)](https://docs.google.com/document/d/1TajA-HSIIZiuv3C6ikZqk0gfAA_H4gWs/edit?usp=drive_link&ouid=111203226793113830230&rtpof=true&sd=true)
🔙 Volver a Constancias
🏠 Volver al menú principal'," => "  '2. 📝Mediante solicitud escrita' => 'Si no puedes acceder al sistema, puedes presentar una solicitud simple 
presentado por CEA –Oficina General de Potencial Humano
📧 Correo electrónico: mesap.gerencia.aqp@mpfn.gob.pe
[📎 Descargar modelo de solicitud (WORD)](https://docs.google.com/document/d/1TajA-HSIIZiuv3C6ikZqk0gfAA_H4gWs/edit?usp=drive_link&ouid=111203226793113830230&rtpof=true&sd=true)
🔙 Volver a Constancias
🏠 Volver al menú principal',",

"  'b. 📜 Solicitar Certificado de Trabajo' => 'El certificado de trabajo se emite al término del vínculo laboral.
Requisitos:
📝 Solicitud simple
¿Cómo presentarlo?
Puedes hacerlo mediante:
📍 Mesa de Partes de Gerencia y Presidencia (Ventanilla N° 8) - Sede la Paz 320.
📧 Correo electrónico: mesap.gerencia.aqp@mpfn.gob.pe
[📎 Descargar modelo de solicitud (WORD)](https://docs.google.com/document/d/1tOVt_4nDTDkXifd-mLaAMDcjUJjeUVyk/edit?usp=drive_link&ouid=111203226793113830230&rtpof=true&sd=true)
🔙 Volver a Constancias
🏠 Volver al menú principal'," => "  '2. 📜Solicitar Certificado de Trabajo' => 'El certificado de trabajo se emite al término del vínculo laboral.
Requisitos:
📝 Solicitud simple
¿Cómo presentarlo?
Puedes hacerlo mediante:
📍 Mesa de Partes de Gerencia y Presidencia (Ventanilla N° 8) - Sede la Paz 320.
📧 Correo electrónico: mesap.gerencia.aqp@mpfn.gob.pe
[📎 Descargar modelo de solicitud (WORD)](https://docs.google.com/document/d/1tOVt_4nDTDkXifd-mLaAMDcjUJjeUVyk/edit?usp=drive_link&ouid=111203226793113830230&rtpof=true&sd=true)
🔙 Volver a Constancias
🏠 Volver al menú principal',",

"  '🔙 Volver a Constancias' => 'Selecciona el trámite que deseas realizar:
a. 📄 Solicitar Constancia de Trabajo
b. 📜 Solicitar Certificado de Trabajo
🏠 Volver al menú principal'," => "  '🔙 Volver a Constancias' => 'Selecciona el trámite que deseas realizar:
1. 📄Solicitar Constancia de Trabajo
2. 📜Solicitar Certificado de Trabajo
🏠 Volver al menú principal',",

// 6. VACACIONES
"  '6. VACACIONES' => 'a. Programación de vacaciones anual
b. Reprogramación de vacaciones
c. Adelanto de vacaciones
🏠 Volver al menú principal'," => "  '6. VACACIONES' => '1. Programación de vacaciones anual
2. Reprogramación de vacaciones
3. Adelanto de vacaciones
🏠 Volver al menú principal',",

"  'a. Programación de vacaciones anual' => 'Se realiza una vez al año, generalmente a fin de año.
La programación se realiza a través de Sistemas2.mpfn.gob.pe
🎥 Videotutorial:
• Para jefes: [Ver videotutorial jefe](https://drive.google.com/file/d/1ZpH9e7SKLFjZGnPTrTjoHnEuaTuCyV3j/view?usp=sharing)
• Para trabajadores: [Ver videotutorial trabajador](https://drive.google.com/file/d/1jJ1pv6rH8WFCG05fkawqScLvjcNhku-l/view?usp=sharing)
🔙 Volver a Vacaciones
🏠 Volver al menú principal'," => "  '1. Programación de vacaciones anual' => 'Se realiza una vez al año, generalmente a fin de año.
La programación se realiza a través de Sistemas2.mpfn.gob.pe
🎥 Videotutorial:
* Para jefes: [Ver videotutorial jefe](https://drive.google.com/drive/folders/1abhH2FikOVARPnx2w0n3leTnITKqX8de)
* Para trabajadores: [Ver videotutorial trabajador](https://drive.google.com/drive/folders/1abhH2FikOVARPnx2w0n3leTnITKqX8de)
🔙 Volver a Vacaciones
🏠 Volver al menú principal',",

"  'b. Reprogramación de vacaciones' => 'i. Solicitud de reprogramación
ii. Recomendaciones
iii. Modelo de Solicitud
🔙 Volver a Vacaciones
🏠 Volver al menú principal'," => "  '2. Reprogramación de vacaciones' => '1. Solicitud de reprogramación
2. Recomendaciones
3. Modelo de Solicitud
🔙 Volver a Vacaciones
🏠 Volver al menú principal',",

"  'i. Solicitud de reprogramación' => '• Presentar el escrito hasta el quinto día hábil anterior al inicio de tus vacaciones.
• Debe contar con V°B° del Jefe Inmediato y firma del solicitante.
• Presentarlo mediante la Carpeta Electrónica Administrativa (CEA) – Área de Potencial Humano Arequipa-Patiño Juarez Karla (sólo al personal indicado).
• IMPORTANTE: En el asunto colocar: Reprogramación de Vacaciones -(Apellidos y Nombres)
🔙 Volver a Reprogramación
🏠 Volver al menú principal'," => "  '1. Solicitud de reprogramación' => '* Presentar el escrito hasta el quinto día hábil anterior al inicio de tus vacaciones.
* Debe contar con V°B° del Jefe Inmediato y firma del solicitante.
* Presentarlo mediante la Carpeta Electrónica Administrativa (CEA) – Área de Potencial Humano Arequipa-Patiño Juarez Karla (sólo al personal indicado).
* IMPORTANTE: En el asunto colocar: Reprogramación de Vacaciones -(Apellidos y Nombres)
🔙 Volver a Reprogramación
🏠 Volver al menú principal',",

"  'ii. Recomendaciones' => '• La suma de todos los periodos fraccionados no puede superar 30 días calendarios.
• No se pueden tomar más de 4 días hábiles por semana de los 7 días hábiles fraccionables.
• Si el periodo inicia o termina un viernes, los sábados y domingos siguientes también se computan.
• El acuerdo de fraccionamiento debe ser previo al disfrute de las vacaciones y debe incluir las fechas originales y nuevas.
🔙 Volver a Reprogramación
🏠 Volver al menú principal'," => "  '2. Recomendaciones' => '* La suma de todos los periodos fraccionados no puede superar 30 días calendarios.
* No se pueden tomar más de 4 días hábiles por semana de los 7 días hábiles fraccionables.
* Si el periodo inicia o termina un viernes, los sábados y domingos siguientes también se computan.
* El acuerdo de fraccionamiento debe ser previo al disfrute de las vacaciones y debe incluir las fechas originales y nuevas.
🔙 Volver a Reprogramación
🏠 Volver al menú principal',",

"  'iii. Modelo de Solicitud' => '📎 [Descargar modelo de solicitud (WORD)](https://docs.google.com/document/d/1TajA-HSIIZiuv3C6ikZqk0gfAA_H4gWs/edit?usp=drive_link&ouid=111203226793113830230&rtpof=true&sd=true)
🔙 Volver a Reprogramación
🏠 Volver al menú principal'," => "  '3. Modelo de Solicitud' => '📎 [Descargar modelo de solicitud (WORD)](https://docs.google.com/document/d/1TajA-HSIIZiuv3C6ikZqk0gfAA_H4gWs/edit?usp=drive_link&ouid=111203226793113830230&rtpof=true&sd=true)
🔙 Volver a Reprogramación
🏠 Volver al menú principal',",

"  'c. Adelanto de vacaciones' => 'Para verificar si puedes acceder al adelanto de vacaciones, comunícate al:
📞 949 305 573
🔙 Volver a Vacaciones
🏠 Volver al menú principal'," => "  '3. Adelanto de vacaciones' => 'Para verificar si puedes acceder al adelanto de vacaciones, comunícate al:
📞 949 305 573
🔙 Volver a Vacaciones
🏠 Volver al menú principal',",

"  '🔙 Volver a Reprogramación' => 'i. Solicitud de reprogramación
ii. Recomendaciones
iii. Modelo de Solicitud
🔙 Volver a Vacaciones
🏠 Volver al menú principal'," => "  '🔙 Volver a Reprogramación' => '1. Solicitud de reprogramación
2. Recomendaciones
3. Modelo de Solicitud
🔙 Volver a Vacaciones
🏠 Volver al menú principal',",

"  '🔙 Volver a Vacaciones' => 'a. Programación de vacaciones anual
b. Reprogramación de vacaciones
c. Adelanto de vacaciones
🏠 Volver al menú principal'," => "  '🔙 Volver a Vacaciones' => '1. Programación de vacaciones anual
2. Reprogramación de vacaciones
3. Adelanto de vacaciones
🏠 Volver al menú principal',"

];

foreach ($replacements as $search => $replace) {
    if (strpos($content, $search) !== false) {
        $content = str_replace($search, $replace, $content);
    } else {
        echo "Could not find block:\n$search\n\n";
    }
}

// Ensure the new ones missing from Constancias are added at the end or replaced if needed.
$append_constancias = "  '3. 🎥Ver video tutorial' => 'Puedes revisar el tutorial para realizar tu solicitud en el sistema institucional:
📺 Ver video
🔙 Volver a Constancias
🏠 Volver al menú principal',
  '4. Descargar modelo de solicitud' => '📎 [Descargar modelo de solicitud (WORD)](https://docs.google.com/document/d/1TajA-HSIIZiuv3C6ikZqk0gfAA_H4gWs/edit?usp=drive_link&ouid=111203226793113830230&rtpof=true&sd=true)
🔙 Volver a Constancias
🏠 Volver al menú principal',
";
if (strpos($content, "'3. 🎥Ver video tutorial'") === false) {
    // just append right before the last ');'
    $content = preg_replace("/\);\s*$/s", ",\n" . $append_constancias . ");\n", $content);
}

file_put_contents($file, $content);
echo "Verbatim update 2 applied.";
