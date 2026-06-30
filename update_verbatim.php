<?php
$file = 'c:\\xampp\\htdocs\\siga\\chatbot_potencial_humano\\api.php';
$content = file_get_contents($file);

// We will locate the start of $opcionesEstaticas and the end of it.
// Wait, doing this by regex is hard. Let's just do str_replace on specific keys.
// To be safe, let's just replace the exact keys one by one.

$replacements = [

"  '1. CONTROL DE ASISTENCIA' => 'a. Boleta de Permiso
b. Compensación por trabajo en sobretiempo
c. Autorización de ingreso en días de descanso
d. Récord de Asistencia
e. Fecha de cierre de asistencia
f. Comunicados
g. Contacto en casos de problemas con el reloj
h. Derivación de Documentos
🏠 Volver al menú principal'," => "  '1. CONTROL DE ASISTENCIA' => 'a. Boleta de Permiso
b. Compensación por trabajo en sobretiempo por toda la jornada laboral
c. Autorización de ingreso en días de descanso obligatorio, feriados y días no laborables.
d. Récord de Asistencia
e. Fecha de cierre de asistencia
f. Comunicados
g. Contacto en casos de problemas con el reloj biométrico
h. Derivación de Documentos mediante Carpeta Electrónica Administrativa CEA
🏠 Volver al menú principal',",

"  'a. Boleta de Permiso' => 'i. Permiso Particular
ii. Comisión de Servicios
iii. Compensación
iv. Atención de Salud
v. Registro fuera del horario establecido
vi. Omisión de Registro de Marcación
🔙 Volver a Asistencia
🏠 Volver al menú principal'," => "  'a. Boleta de Permiso' => 'Todas las boletas de permiso que se ejecuten a primera hora de la jornada laboral, es decir que inicien a partir de las 08:00 horas deberán de ser presentadas en vigilancia de cada sede o carpeta electrónica administrativa - CEA para servidores que laboran en Provincias, el día anterior al permiso debidamente firmado por su jefe inmediato, deben de marcar (x) acorde al motivo del permiso:
i. Permiso Particular
ii. Comisión de Servicios
iii. Compensación
iv. Atención de Salud
v. Registro fuera del horario establecido
vi. Omisión de Registro de Marcación
🔙 Volver a Asistencia
🏠 Volver al menú principal',",

"  'i. Permiso Particular' => 'Si inicia después de la hora de ingreso, la boleta debe presentarse en el día.
Debe efectuar registro de marcación antes y después de la ejecución del permiso.
Son establecidas por horas. No puede superar 8h/día ni en el mes.
[Descargar formato de boleta de permiso](https://drive.google.com/file/d/1VaQ6mmBpQOn3RtMWFWm_MunNmfjIyDmI/view?usp=drive_link)
🔙 Volver a Boleta de Permiso
🏠 Volver al menú principal'," => "  'i. Permiso Particular' => '* Si inicia después de la hora de ingreso, la boleta debe presentarse en el día.
* Debe efectuar registro de marcación antes y después de la ejecución del permiso.
* Son establecidas por horas.
* No puede superar las 8 horas en el día, ni en el mes.
[📎 Descargar formato de boleta de permiso](https://drive.google.com/file/d/1VaQ6mmBpQOn3RtMWFWm_MunNmfjIyDmI/view?usp=drive_link)
🔙 Volver a Boleta de Permiso
🏠 Volver al menú principal',",

"  'ii. Comisión de Servicios' => 'Si inicia después de la hora de ingreso, presentarse en el día.
Marcación antes y después. 
Presentar al retorno el documento sellado, con FIRMA, FECHA y HORA de atención.
[Ver modelo](https://drive.google.com/file/d/1OfhvRb-0MQKACH6vuhCnJ1wlnKfS7eyt/view?usp=drive_link)
[Descargar formato de boleta de permiso](https://drive.google.com/file/d/1VaQ6mmBpQOn3RtMWFWm_MunNmfjIyDmI/view?usp=drive_link)
🔙 Volver a Boleta de Permiso
🏠 Volver al menú principal'," => "  'ii. Comisión de Servicios' => '* Si inicia después de la hora de ingreso, la boleta debe presentarse en el día.
* Debe efectuar registro de marcación antes y después de la ejecución del permiso.
* El trabajador deberá presentar al retorno del permiso, el documento debidamente sellado, consignando la FIRMA, FECHA y HORA de la atención por la persona de la entidad a donde se desplazó
[📎 Ver modelo](https://drive.google.com/file/d/1OfhvRb-0MQKACH6vuhCnJ1wlnKfS7eyt/view?usp=drive_link)
[📎 Descargar formato de boleta de permiso](https://drive.google.com/file/d/1VaQ6mmBpQOn3RtMWFWm_MunNmfjIyDmI/view?usp=drive_link)
🔙 Volver a Boleta de Permiso
🏠 Volver al menú principal',",

"  'iii. Compensación' => 'Establecidas por horas. Adjuntar formato correspondiente.
No superar 16 horas al mes. Marcación antes y después.
Máximo 2 oportunidades no consecutivas al mes.
[Ver modelo](https://drive.google.com/file/d/16Ax25zHx2TqNM44sLYChpQRqHICJFOC8/view?usp=drive_link)
[Descargar formato de boleta de permiso](https://drive.google.com/file/d/1VaQ6mmBpQOn3RtMWFWm_MunNmfjIyDmI/view?usp=drive_link)
[Descargar autorización en sobretiempo](https://drive.google.com/file/d/1NuYeLG0JEmCIX-ATn--bHrrm5Ajz0s-w/view?usp=drive_link)
🔙 Volver a Boleta de Permiso
🏠 Volver al menú principal'," => "  'iii. Compensación' => '* Para la presentación con boleta de permiso son establecidas por horas y se debe adjuntar el formato correspondiente del sobretiempo. El mismo que debió ser presentado con anterioridad al permiso solicitado acorde al procedimiento de “trabajos en sobretiempo”.
* No debe de superar las 16 horas al mes.
* Debe efectuar registro de marcación antes y después de la ejecución del permiso.
* Acorde al convenio colectivo, puede presentarse el mismo día hasta en dos oportunidades no consecutivas en el mes en caso el permiso sea al inicio de la jornada.
[📎 Ver modelo](https://drive.google.com/file/d/16Ax25zHx2TqNM44sLYChpQRqHICJFOC8/view?usp=drive_link)
[📎 Descargar formato de boleta de permiso](https://drive.google.com/file/d/1VaQ6mmBpQOn3RtMWFWm_MunNmfjIyDmI/view?usp=drive_link)
[📎 Descargar autorización en sobretiempo](https://drive.google.com/file/d/1NuYeLG0JEmCIX-ATn--bHrrm5Ajz0s-w/view?usp=drive_link)
🔙 Volver a Boleta de Permiso
🏠 Volver al menú principal',",

"  'iv. Atención de Salud' => '**Cita médica del servidor:**
Hasta 4 horas (7h exámenes). Requiere boleta y constancia de atención.
**Atención de emergencia de familiar directo:**
Solo en EMERGENCIA, hasta 4h (7h exámenes). Requiere boleta y constancia del ÁREA DE EMERGENCIA.
[Descargar formato de boleta de permiso](https://drive.google.com/file/d/1VaQ6mmBpQOn3RtMWFWm_MunNmfjIyDmI/view?usp=drive_link)
🔙 Volver a Boleta de Permiso
🏠 Volver al menú principal'," => "  'iv. Atención de Salud' => '* Permiso por cita médica del servidor: 
1. Se otorga hasta un máximo de 04 horas y en caso de exámenes especiales hasta por 07 horas. 
2. Requiere la presentación de la boleta de permiso debidamente autorizada y la constancia de atención médica. 
3. En caso de no adjuntar la constancia de atención médica, será considerado como permiso particular.
* Permiso por atención médica de emergencia del familiar directo: 
1. El permiso se concede únicamente en casos de EMERGENCIA, se otorga hasta cuatro (4) horas al día, y en caso de exámenes especiales hasta siete (7) horas dentro de la jornada laboral. 
2. Requiere la presentación de la boleta de permiso debidamente autorizada y la constancia de atención médica del ÁREA DE EMERGENCIA, como sustento de haber sido atendida en dicha área, caso contrario será considerado como permiso particular
[📎 Descargar formato de boleta de permiso](https://drive.google.com/file/d/1VaQ6mmBpQOn3RtMWFWm_MunNmfjIyDmI/view?usp=drive_link)
🔙 Volver a Boleta de Permiso
🏠 Volver al menú principal',",

"  'v. Registro fuera del horario establecido' => 'Se presenta en el día o hasta 24h posteriores cuando se marca ingreso entre 8:16 y 8:59 Hrs (lun-vie).
Hasta 5 boletas en el mes. Consignar \"x\" si es 1ra, 2da, 3ra. En 4ta o 5ta consignar en el detalle.
[Descargar formato de boleta de permiso](https://drive.google.com/file/d/1VaQ6mmBpQOn3RtMWFWm_MunNmfjIyDmI/view?usp=drive_link)
🔙 Volver a Boleta de Permiso
🏠 Volver al menú principal'," => "  'v. Registro fuera del horario establecido' => '* La boleta de permiso se presenta en el mismo día o hasta 24 horas posteriores al día de la incidencia cuando el servidor tiene registro de marcación de ingreso entre las 8:16 y 8:59 Horas de lunes a viernes (jornada laboral).
* Se pueden presentar hasta cinco (05) boletas en el mes.
* Debe consignar “x” si es primera, segunda o tercera boleta; y, de ser cuarta o quinta boleta, debe consignar en el detalle.
[📎 Descargar formato de boleta de permiso](https://drive.google.com/file/d/1VaQ6mmBpQOn3RtMWFWm_MunNmfjIyDmI/view?usp=drive_link)
* 📎Comunicado de registro fuera de hora convenio colectivo
🔙 Volver a Boleta de Permiso
🏠 Volver al menú principal',",

"  'vi. Omisión de Registro de Marcación' => 'Presentar hasta 48 horas siguientes de ocurrido el hecho.
Justifica la marcación de ingreso/salida de lun-vie por única vez en el mes.
[Descargar formato de boleta de permiso](https://drive.google.com/file/d/1VaQ6mmBpQOn3RtMWFWm_MunNmfjIyDmI/view?usp=drive_link)
[Modelo de boleta por omisión](https://drive.google.com/file/d/1OfhvRb-0MQKACH6vuhCnJ1wlnKfS7eyt/view?usp=drive_link)
🔙 Volver a Boleta de Permiso
🏠 Volver al menú principal'," => "  'vi. Omisión de Registro de Marcación' => '* Acorde al convenio colectivo debe ser presentado hasta 48 horas siguientes de ocurrido el hecho.
* Justifica la marcación de asistencia de ingreso o salida de lunes a viernes (jornada laboral) y de manera excepcional por única vez en el mes.
[📎 Descargar formato de boleta de permiso](https://drive.google.com/file/d/1VaQ6mmBpQOn3RtMWFWm_MunNmfjIyDmI/view?usp=drive_link)
[📎 Modelo de boleta por omisión](https://drive.google.com/file/d/1OfhvRb-0MQKACH6vuhCnJ1wlnKfS7eyt/view?usp=drive_link)
🔙 Volver a Boleta de Permiso
🏠 Volver al menú principal',",

"  'b. Compensación por trabajo en sobretiempo' => 'Laborar mínimo 1 hora y máximo 5 horas por día.
Efectivo al día siguiente o hasta 30 días hábiles, previa autorización.
Presentar 1 día anterior hábil. Máx 16h/mes. V°B° del jefe.
Presentar por CEA a Arequipa (Bustinza Sierra Fanny) o Provincias (Flores Mamani Eliant).
[Modelo Solicitud Compensación 728/CAS](https://drive.google.com/file/d/16Ax25zHx2TqNM44sLYChpQRqHICJFOC8/view?usp=drive_link)
[Modelo llenado formato sobretiempo](https://drive.google.com/file/d/1Lvh43r3nDECcelt5lPtRPpnkVPOQ7mkO/view?usp=drive_link)
[Autorización en Sobretiempo](https://drive.google.com/file/d/1NuYeLG0JEmCIX-ATn--bHrrm5Ajz0s-w/view?usp=drive_link)
🔙 Volver a Asistencia
🏠 Volver al menú principal'," => "  'b. Compensación por trabajo en sobretiempo por toda la jornada laboral' => '* Para la contabilización del sobretiempo el trabajador deberá laborar como mínimo (01) hora y máxima (05) horas por día.
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
🏠 Volver al menú principal',",

"  'c. Autorización de ingreso en días de descanso' => 'Días de descanso obligatorio, feriados y no laborables.
Solicitado por jefe inmediato, acreditando necesidad. Presentar 1 día hábil antes.
[Modelo de solicitud de autorización de ingreso](https://docs.google.com/document/d/1T4KgMSd9cHtQc-czXMf2oJSkIc82wwsI/edit?usp=drive_link&ouid=111203226793113830230&rtpof=true&sd=true)
🔙 Volver a Asistencia
🏠 Volver al menú principal'," => "  'c. Autorización de ingreso en días de descanso obligatorio, feriados y días no laborables.' => 'Debe ser solicitado por el jefe inmediato, acreditando la necesidad de servicio ante la Gerencia Administrativa, el mismo debe estar suscrito por los servidores, se presenta hasta un (01) día hábil antes de la realización del trabajo, para el acceso a las instalaciones.
[📎 Modelo de solicitud de autorización de ingreso](https://docs.google.com/document/d/1T4KgMSd9cHtQc-czXMf2oJSkIc82wwsI/edit?usp=drive_link&ouid=111203226793113830230&rtpof=true&sd=true)
🔙 Volver a Asistencia
🏠 Volver al menú principal',",

"  'd. Récord de Asistencia' => 'Para visualizar tu récord:
👉 Ingresa al sistema institucional: https://Sistemas2.mpfn.gob.pe
1. Selecciona Sistema de Récord de Asistencia – ASIST
2. Selecciona Periodo Asistencial
[Ver video tutorial](https://drive.google.com/drive/folders/1abhH2FikOVARPnx2w0n3leTnITKqX8de)
⚠️ Soporte técnico: Mesa de Ayuda TI - 937 461 856
🔙 Volver a Asistencia
🏠 Volver al menú principal'," => "  'd. Récord de Asistencia' => 'Para visualizar y revisar tu récord de asistencia, sigue estos pasos:
👉 Ingresa al sistema institucional: https://Sistemas2.mpfn.gob.pe
1. Selecciona Sistema de Récord de Asistencia – ASIST
2. Selecciona Periodo Asistencial
[🎥 Ver video tutorial](https://drive.google.com/drive/folders/1abhH2FikOVARPnx2w0n3leTnITKqX8de)
⚠️ Soporte técnico
Si no visualizas el módulo ASIST dentro de Sistemas2, comunícate con:
📞 Mesa de Ayuda – Área de Tecnologías de la Información
937 461 856
🔙 Volver a Asistencia
🏠 Volver al menú principal',",

"  'e. Fecha de cierre de asistencia' => 'Planilla MAYO 2026: 26 mar al 25 abr
Planilla JUNIO 2026: 26 abr al 25 may
Planilla JULIO 2026: 26 may al 25 jun
Planilla AGOSTO 2026: 26 jun al 25 jul
Planilla SETIEMBRE 2026: 26 jul al 25 ago
Planilla OCTUBRE 2026: 26 ago al 25 set
Planilla NOVIEMBRE 2026: 26 set al 25 oct
Planilla DICIEMBRE 2026: 26 oct al 25 nov
🔙 Volver a Asistencia
🏠 Volver al menú principal'," => "  'e. Fecha de cierre de asistencia' => '* Cierre para planilla de MAYO 2026 
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
🏠 Volver al menú principal',",

"  'f. Comunicados' => 'A continuación verás todos los comunicados vigentes:
[Flyer Convenio Colectivo 2025-2026 Asistencia](https://drive.google.com/drive/folders/1VsQ2MrAg8OX2LlJYMA-6P1eQ9i3vLZXZ)
[Facilidades sobre el registro de asistencia 20 FEB](https://drive.google.com/drive/folders/1VsQ2MrAg8OX2LlJYMA-6P1eQ9i3vLZXZ)
[Recuperación horas dejadas de laborar Tolerancia 23 FEB](https://drive.google.com/drive/folders/1VsQ2MrAg8OX2LlJYMA-6P1eQ9i3vLZXZ)
🔙 Volver a Asistencia
🏠 Volver al menú principal'," => "  'f. Comunicados' => 'A continuación verás todos los comunicados vigentes:
[📎 Flyer Convenio Colectivo 2025-2026 Asistencia](https://drive.google.com/drive/folders/1VsQ2MrAg8OX2LlJYMA-6P1eQ9i3vLZXZ)
[📎 Facilidades sobre el registro de asistencia 20 FEB](https://drive.google.com/drive/folders/1VsQ2MrAg8OX2LlJYMA-6P1eQ9i3vLZXZ)
[📎 Recuperación horas dejadas de laborar Tolerancia 23 FEB](https://drive.google.com/drive/folders/1VsQ2MrAg8OX2LlJYMA-6P1eQ9i3vLZXZ)
[📎 Facilidades sobre el registro de asistencia 11 MAR](https://drive.google.com/drive/folders/1VsQ2MrAg8OX2LlJYMA-6P1eQ9i3vLZXZ)
[📎 Facilidades sobre el registro de asistencia 12 MAR](https://drive.google.com/drive/folders/1VsQ2MrAg8OX2LlJYMA-6P1eQ9i3vLZXZ)
🔙 Volver a Asistencia
🏠 Volver al menú principal',",

"  'g. Contacto en casos de problemas con el reloj' => 'En caso de que se registren casos de cortes de luz y tenga problemas con la marcación, deberá comunicarse a:
📞 959371597
🔙 Volver a Asistencia
🏠 Volver al menú principal'," => "  'g. Contacto en casos de problemas con el reloj biométrico' => 'En caso de que se registren casos de cortes de luz y tenga problemas con la marcación, deberá comunicarse a: 📞 959371597
🔙 Volver a Asistencia
🏠 Volver al menú principal',",

"  'h. Derivación de Documentos' => 'Derivación mediante Carpeta Electrónica Administrativa CEA.
En el Asunto colocar: MOTIVO - APELLIDOS Y NOMBRES
Derivar al siguiente personal:
* Bustinza Sierra, Fanny (Arequipa)
* Flores Mamani, Eliant Yanira (Provincias)
🔙 Volver a Asistencia
🏠 Volver al menú principal'," => "  'h. Derivación de Documentos mediante Carpeta Electrónica Administrativa CEA' => 'En el Asunto colocar: MOTIVO - APELLIDOS Y NOMBRES
Derivar al siguiente personal:
* Bustinza Sierra, Fanny (para el personal que labora en Arequipa)
* Flores Mamani, Eliant Yanira (para el personal que labora en provincias)
🔙 Volver a Asistencia
🏠 Volver al menú principal',",

"  '🔙 Volver a Asistencia' => 'a. Boleta de Permiso
b. Compensación por trabajo en sobretiempo
c. Autorización de ingreso en días de descanso
d. Récord de Asistencia
e. Fecha de cierre de asistencia
f. Comunicados
g. Contacto en casos de problemas con el reloj
h. Derivación de Documentos
🏠 Volver al menú principal'," => "  '🔙 Volver a Asistencia' => 'a. Boleta de Permiso
b. Compensación por trabajo en sobretiempo por toda la jornada laboral
c. Autorización de ingreso en días de descanso obligatorio, feriados y días no laborables.
d. Récord de Asistencia
e. Fecha de cierre de asistencia
f. Comunicados
g. Contacto en casos de problemas con el reloj biométrico
h. Derivación de Documentos mediante Carpeta Electrónica Administrativa CEA
🏠 Volver al menú principal',",


// Option 5
"  'a. 📄 Solicitar Constancia de Trabajo' => 'i. 💻 A través del sistema institucional
Puedes realizar tu solicitud ingresando a: https://Sistemas2.mpfn.gob.pe
(Elige el Módulo de Emisión de Constancias -> Simple, Histórica o Personalizada).
ii. 📝 Mediante solicitud escrita
Si no puedes acceder al sistema, presenta una solicitud simple a CEA – Oficina General de Potencial Humano o al correo: mesap.gerencia.aqp@mpfn.gob.pe
[Descargar modelo de solicitud (WORD)](https://docs.google.com/document/d/1TajA-HSIIZiuv3C6ikZqk0gfAA_H4gWs/edit?usp=drive_link&ouid=111203226793113830230&rtpof=true&sd=true)
🔙 Volver a Constancias
🏠 Volver al menú principal'," => "  'a. 📄 Solicitar Constancia de Trabajo' => '¿Cómo deseas solicitarla?
i. 💻 A través del sistema institucional
ii. 📝 Mediante solicitud escrita
🔙 Volver a Constancias
🏠 Volver al menú principal',
  'i. 💻 A través del sistema institucional' => 'Puedes realizar tu solicitud ingresando a: 👉 https://Sistemas2.mpfn.gob.pe
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
🏠 Volver al menú principal',
  'ii. 📝 Mediante solicitud escrita' => 'Si no puedes acceder al sistema, puedes presentar una solicitud simple presentado por CEA –Oficina General de Potencial Humano
📧 Correo electrónico: mesap.gerencia.aqp@mpfn.gob.pe
[📎 Descargar modelo de solicitud (WORD)](https://docs.google.com/document/d/1TajA-HSIIZiuv3C6ikZqk0gfAA_H4gWs/edit?usp=drive_link&ouid=111203226793113830230&rtpof=true&sd=true)
🔙 Volver a Constancias
🏠 Volver al menú principal',",

// Fix Certificado de trabajo
"  'b. 📜 Solicitar Certificado de Trabajo' => 'El certificado de trabajo se emite al término del vínculo laboral.
• Requisitos: Solicitud simple
• Presentación: Mesa de Partes de Gerencia y Presidencia (Ventanilla N° 8) - Sede la Paz 320 o al correo mesap.gerencia.aqp@mpfn.gob.pe
[Descargar modelo de solicitud (WORD)](https://docs.google.com/document/d/1tOVt_4nDTDkXifd-mLaAMDcjUJjeUVyk/edit?usp=drive_link&ouid=111203226793113830230&rtpof=true&sd=true)
🔙 Volver a Constancias
🏠 Volver al menú principal'," => "  'b. 📜 Solicitar Certificado de Trabajo' => 'El certificado de trabajo se emite al término del vínculo laboral.
Requisitos:
📝 Solicitud simple
¿Cómo presentarlo?
Puedes hacerlo mediante:
📍 Mesa de Partes de Gerencia y Presidencia (Ventanilla N° 8) - Sede la Paz 320.
📧 Correo electrónico: mesap.gerencia.aqp@mpfn.gob.pe
[📎 Descargar modelo de solicitud (WORD)](https://docs.google.com/document/d/1tOVt_4nDTDkXifd-mLaAMDcjUJjeUVyk/edit?usp=drive_link&ouid=111203226793113830230&rtpof=true&sd=true)
🔙 Volver a Constancias
🏠 Volver al menú principal',",

// Option 7
"  '7. DECLARACIÓN JURADA DE INGRESOS Y DE BIENES Y RENTAS' => 'Se encuentran obligados a presentar DECLARACIÓN JURADA DE INGRESOS, BIENES Y RENTAS, quienes ocupen los cargos según Art. 2 Ley 27482.
a. ✅ Sí, tengo firma digital (Bienes)
b. ❌ No, tengo firma digital (Bienes)
c. Video Tutorial (Bienes)
🏠 Volver al menú principal'," => "  '7. DECLARACIÓN JURADA DE INGRESOS Y DE BIENES Y RENTAS' => 'Se encuentran obligados a presentar DECLARACIÓN JURADA DE INGRESOS, BIENES Y RENTAS, quienes ocupen los cargos o desarrollen las funciones establecidas en el Art. 2 de la Ley  27482.
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
🏠 Volver al menú principal',",

// Option 8
"  '8. DECLARACIÓN JURADA DE INTERESES' => 'Se encuentran obligados a presentar DECLARACIÓN JURADA DE INTERESES, quienes ocupen los cargos según Art. 3 Ley 31227.
a. ✅ Sí, tengo firma digital
b. ❌ No, tengo firma digital
🏠 Volver al menú principal'," => "  '8. DECLARACIÓN JURADA DE INTERESES' => 'Se encuentran obligados a presentar DECLARACIÓN JURADA DE INTERESES, quienes ocupen los cargos o desarrollen las funciones establecidas en el Art. 3 de la Ley 31227.
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
🏠 Volver al menú principal',",

// Option 9
"  '9. BOLETAS DE PAGO' => 'Para visualizar y descargar tu boleta de pago, sigue estos pasos:
👉 Ingresa al sistema institucional: https://Sistemas2.mpfn.gob.pe
a. 📂 Dentro del sistema
b. 🎥 ¿Necesitas ayuda?
c. ⚠️ Soporte técnico
🏠 Volver al menú principal'," => "  '9. BOLETAS DE PAGO' => 'Para visualizar y descargar tu boleta de pago, sigue estos pasos:
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
🏠 Volver al menú principal',"

];

foreach ($replacements as $search => $replace) {
    if (strpos($content, $search) !== false) {
        $content = str_replace($search, $replace, $content);
    } else {
        echo "Could not find block:\n$search\n\n";
    }
}

file_put_contents($file, $content);
echo "Verbatim update applied.";
