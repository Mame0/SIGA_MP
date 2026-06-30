<?php
$file = 'c:\\xampp\\htdocs\\siga\\chatbot_potencial_humano\\api.php';
$content = file_get_contents($file);

$search = "  '2. LICENCIAS' => '1. Licencias con goce de haber
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
3. Volver al menú principal',";

$replace = "  '2. LICENCIAS' => 'a. Licencias con goce de haber
b. Licencias sin goce de haber
c. Volver al menú principal',
  'c. Volver al menú principal' => \$menuPrincipal,

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
• Formato Interno de descanso médico 📎
• Copia fedateada del Certificado de Incapacidad Temporal para el Trabajo - CITT
b. Ministerio de Salud del Perú - MINSA, Clínicas Privadas, Entidades Prestadoras de Salud - EPS, Sanidad de las Fuerzas Armadas, Sanidad de la Policía Nacional del Perú o Consultorios Médicos Particulares
• Formato Interno de descanso médico 📎
• Copia fedateada del descanso médico o Certificado Médico en especie valorada del Colegio Médico del Perú
• Copia fedateada del comprobante de pago de la atención médica, de corresponder
• Copia fedateada de la receta médica
🔙 Volver a Licencias con goce
🏠 Volver al menú principal',

  'ii. Licencia por maternidad' => '• Requisitos de acuerdo al establecimiento de salud que emite el certificado médico:
a. Seguro Social de Salud - EsSalud
• Formato Interno de descanso médico 📎
• Copia fedateada del Certificado de Incapacidad Temporal para el Trabajo - CITT por Maternidad
b. Ministerio de Salud del Perú - MINSA, Clínicas Privadas, Entidades Prestadoras de Salud - EPS, Sanidad de las Fuerzas Armadas, Sanidad de la Policía Nacional del Perú o Consultorios Médicos Particulares
• Formato Interno de descanso médico 📎
• Copia fedateada del descanso médico o Certificado Médico en especie valorada
• Consideraciones a tener en cuenta respecto a las solicitudes de licencia por enfermedad y maternidad:
1. El expediente de licencia por enfermedad debe ser presentado dentro de las cuarenta y ocho (48) horas de su emisión, pudiendo delegar a un familiar o a un tercero cercano para el trámite correspondiente.
2. La presentación es en forma física a través de la mesa de partes de Gerencia y Presidencia (Ventanilla N° 08)
🔙 Volver a Licencias con goce
🏠 Volver al menú principal',

  'iii. Licencia por paternidad' => '• Ver Cartilla Informativa 📎
• Requisitos:
a. La solicitud será presentada mediante la Carpeta Electrónica Administrativa (CEA) – Área de Potencial Humano Arequipa-Pacheco Apaza, Gonzalo Alexander (sólo al personal indicado).
b. Copia fedateada del acta de nacido vivo o partida de nacimiento del menor de corresponder.
c. Copia del informe u orden de alta de la madre o del recién nacido de corresponder.
🔙 Volver a Licencias con goce
🏠 Volver al menú principal',

  'iv. Licencia por fallecimiento de familiar directo' => '• Ver Cartilla Informativa 📎
• Requisitos:
• La solicitud será presentada mediante la Carpeta Electrónica Administrativa (CEA) – Área de Potencial Humano Arequipa-Pacheco Apaza, Gonzalo Alexander (sólo al personal indicado).
a. Adjuntar el acta o certificado de Defunción
b. Documento que acredite la vinculación de parentesco
🔙 Volver a Licencias con goce
🏠 Volver al menú principal',

  'v. Licencia por enfermedad grave de familiar directo' => '• Ver Cartilla Informativa 📎
• Requisitos:
• Escrito presentado mediante la Carpeta Electrónica Administrativa (CEA) – Área de Potencial Humano Arequipa-Pacheco Apaza, Gonzalo Alexander (sólo al personal indicado).
a. Documento que acredite la vinculación de parentesco
b. 📎 Formato de Certificado Médico Ley N° 30012
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

  'i. Licencia sin goce de haber' => '• Ver Cartilla Informativa 📎
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
c. Volver al menú principal',";

if (strpos($content, "'2. LICENCIAS'") === false) {
    echo "Could not find LICENCIAS section!\n";
} else {
    $content = str_replace($search, $replace, $content);
    file_put_contents($file, $content);
    echo "Replaced Licencias successfully!";
}
