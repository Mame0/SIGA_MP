<?php
$file = 'c:\\xampp\\htdocs\\siga\\chatbot_potencial_humano\\api.php';
$content = file_get_contents($file);

$search = "  '1. CONTROL DE ASISTENCIA' => '1. Boleta de Permiso
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

**Permiso Particular**
* Si inicia después de la hora de ingreso, la boleta debe presentarse en el día.
* Debe efectuar registro de marcación antes y después de la ejecución del permiso.
* Son establecidas por horas.
* No puede superar las 8 horas en el día, ni en el mes.
[📎 Descargar formato de boleta de permiso](https://drive.google.com/file/d/1VaQ6mmBpQOn3RtMWFWm_MunNmfjIyDmI/view?usp=drive_link)

**Comisión de Servicios**
* Si inicia después de la hora de ingreso, la boleta debe presentarse en el día.
* Debe efectuar registro de marcación antes y después de la ejecución del permiso.
* El trabajador deberá presentar al retorno del permiso, el documento debidamente sellado, consignando la FIRMA, FECHA y HORA de la atención por la persona de la entidad a donde se desplazó
[📎 Ver modelo](https://drive.google.com/file/d/1OfhvRb-0MQKACH6vuhCnJ1wlnKfS7eyt/view?usp=drive_link) 
[📎 Descargar formato de boleta de permiso](https://drive.google.com/file/d/1VaQ6mmBpQOn3RtMWFWm_MunNmfjIyDmI/view?usp=drive_link)

**Compensación**
* Para la presentación con boleta de permiso son establecidas por horas y se debe adjuntar el formato correspondiente del sobretiempo. El mismo que debió ser presentado con anterioridad al permiso solicitado acorde al procedimiento de “trabajos en sobretiempo”.
* No debe de superar las 16 horas al mes.
* Debe efectuar registro de marcación antes y después de la ejecución del permiso.
* Acorde al convenio colectivo, puede presentarse el mismo día hasta en dos oportunidades no consecutivas en el mes en caso el permiso sea al inicio de la jornada.
[📎 Ver modelo](https://drive.google.com/file/d/16Ax25zHx2TqNM44sLYChpQRqHICJFOC8/view?usp=drive_link)
[📎 Descargar formato de boleta de permiso](https://drive.google.com/file/d/1VaQ6mmBpQOn3RtMWFWm_MunNmfjIyDmI/view?usp=drive_link)
[📎 Descargar autorización en sobretiempo](https://drive.google.com/file/d/1NuYeLG0JEmCIX-ATn--bHrrm5Ajz0s-w/view?usp=drive_link)

**Atención de Salud**
* Permiso por cita médica del servidor: 
* Se otorga hasta un máximo de 04 horas y en caso de exámenes especiales hasta por 07 horas. 
* Requiere la presentación de la boleta de permiso debidamente autorizada y la constancia de atención médica. 
* En caso de no adjuntar la constancia de atención médica, será considerado como permiso particular.
* Permiso por atención médica de emergencia del familiar directo: 
* El permiso se concede únicamente en casos de EMERGENCIA, se otorga hasta cuatro (4) horas al día, y en caso de exámenes especiales hasta siete (7) horas dentro de la jornada laboral. 
* Requiere la presentación de la boleta de permiso debidamente autorizada y la constancia de atención médica del ÁREA DE EMERGENCIA, como sustento de haber sido atendida en dicha área, caso contrario será considerado como permiso particular
[📎 Descargar formato de boleta de permiso](https://drive.google.com/file/d/1VaQ6mmBpQOn3RtMWFWm_MunNmfjIyDmI/view?usp=drive_link)

**Registro fuera del horario establecido**
* La boleta de permiso se presenta en el mismo día o hasta 24 horas posteriores al día de la incidencia cuando el servidor tiene registro de marcación de ingreso entre las 8:16 y 8:59 Horas de lunes a viernes (jornada laboral).
* Se pueden presentar hasta cinco (05) boletas en el mes.
* Debe consignar “x” si es primera, segunda o tercera boleta; y, de ser cuarta o quinta boleta, debe consignar en el detalle.
[📎 Descargar formato de boleta de permiso](https://drive.google.com/file/d/1VaQ6mmBpQOn3RtMWFWm_MunNmfjIyDmI/view?usp=drive_link)
[📎 Comunicado de registro fuera de hora convenio colectivo](https://drive.google.com/drive/folders/1VsQ2MrAg8OX2LlJYMA-6P1eQ9i3vLZXZ)

**Omisión de Registro de Marcación**
* Acorde al convenio colectivo debe ser presentado hasta 48 horas siguientes de ocurrido el hecho.
* Justifica la marcación de asistencia de ingreso o salida de lunes a viernes (jornada laboral) y de manera excepcional por única vez en el mes.
[📎 Descargar formato de boleta de permiso](https://drive.google.com/file/d/1VaQ6mmBpQOn3RtMWFWm_MunNmfjIyDmI/view?usp=drive_link)
[📎 Modelo de boleta por omisión](https://drive.google.com/file/d/1OfhvRb-0MQKACH6vuhCnJ1wlnKfS7eyt/view?usp=drive_link)
🔙 Volver a Asistencia
🏠 Volver al menú principal',

  '2. Compensación por trabajo en sobretiempo por toda la jornada laboral' => '* Para la contabilización del sobretiempo el trabajador deberá laborar como mínimo (01) hora y máxima (05) horas por día.
* La compensación podrá hacerse efectivo a partir del día siguiente de haberse realizado dicho trabajo hasta treinta (30) días hábiles posteriores, previa autorización del jefe inmediato
* Debe de presentarse hasta un (01) día anterior hábil como plazo máximo.
* No debe de superarse las 16 horas al mes
* La solicitud debe contar con el visto bueno de su jefe inmediato
* Presentarlo mediante la Carpeta Electrónica Administrativa (CEA) – Área de Potencial Humano Arequipa - Bustinza Sierra Fanny en caso de personal que labora en Arequipa.
* Flores Mamani Eliant personal que labora en provincias
* Los escritos sólo deberán de ser enviados al personal indicado.
[📎 Modelo Solicitud Compensación 728](https://drive.google.com/file/d/16Ax25zHx2TqNM44sLYChpQRqHICJFOC8/view?usp=drive_link)
[📎 Modelo Solicitud Compensación CAS](https://drive.google.com/file/d/16Ax25zHx2TqNM44sLYChpQRqHICJFOC8/view?usp=drive_link)
[📎 Modelo de llenado de formato de sobretiempo](https://drive.google.com/file/d/1Lvh43r3nDECcelt5lPtRPpnkVPOQ7mkO/view?usp=drive_link)
[📎 Autorización en Sobretiempo](https://drive.google.com/file/d/1NuYeLG0JEmCIX-ATn--bHrrm5Ajz0s-w/view?usp=drive_link)
🔙 Volver a Asistencia
🏠 Volver al menú principal',

  '3. Autorización de ingreso en días de descanso obligatorio, feriados y días no laborables.' => '* Debe ser solicitado por el jefe inmediato, acreditando la necesidad de servicio ante la Gerencia Administrativa, el mismo debe estar suscrito por los servidores, se presenta hasta un (01) día hábil antes de la realización del trabajo, para el acceso a las instalaciones.
[📎 Modelo de solicitud de autorización de ingreso](https://docs.google.com/document/d/1T4KgMSd9cHtQc-czXMf2oJSkIc82wwsI/edit?usp=drive_link&ouid=111203226793113830230&rtpof=true&sd=true)
🔙 Volver a Asistencia
🏠 Volver al menú principal',

  '4. Récord de Asistencia' => 'Para visualizar y revisar tu récord de asistencia, sigue estos pasos:
👉 Ingresa al sistema institucional: https://Sistemas2.mpfn.gob.pe
* Selecciona Sistema de Récord de Asistencia – ASIST
* Selecciona Periodo Asistencial
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
🏠 Volver al menú principal',";

$replace = "  '1. CONTROL DE ASISTENCIA' => 'a. Boleta de Permiso
b. Compensación por trabajo en sobretiempo por toda la jornada laboral
c. Autorización de ingreso en días de descanso obligatorio, feriados y días no laborables.
d. Récord de Asistencia
e. Fecha de cierre de asistencia
f. Comunicados
g. Contacto en casos de problemas con el reloj biométrico
h. Derivación de Documentos mediante Carpeta Electrónica Administrativa CEA
i. Volver al menú principal.',
  'i. Volver al menú principal.' => \$menuPrincipal,

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
• El permiso se concede únicamente en casos de EMERGENCIA, se otorga hasta cuatro (4) horas al día, y en caso de exámenes especiales hasta siete (7) horas dentro de la jornada laboral. 
a. Requiere la presentación de la boleta de permiso debidamente autorizada y la constancia de atención médica del ÁREA DE EMERGENCIA, como sustento de haber sido atendida en dicha área, caso contrario será considerado como permiso particular
[📎 Descargar formato de boleta de permiso](https://drive.google.com/file/d/1VaQ6mmBpQOn3RtMWFWm_MunNmfjIyDmI/view?usp=drive_link)
🔙 Volver a Boleta de Permiso
🏠 Volver al menú principal',

  'v. Registro fuera del horario establecido' => '• La boleta de permiso se presenta en el mismo día o hasta 24 horas posteriores al día de la incidencia cuando el servidor tiene registro de marcación de ingreso entre las 8:16 y 8:59 Horas de lunes a viernes (jornada laboral).
• Se pueden presentar hasta cinco (05) boletas en el mes.
• Debe consignar “x” si es primera, segunda o tercera boleta; y, de ser cuarta o quinta boleta, debe consignar en el detalle.
[📎 Descargar formato de boleta de permiso](https://drive.google.com/file/d/1VaQ6mmBpQOn3RtMWFWm_MunNmfjIyDmI/view?usp=drive_link)
[📎 Comunicado de registro fuera de hora convenio colectivo](https://drive.google.com/drive/folders/1VsQ2MrAg8OX2LlJYMA-6P1eQ9i3vLZXZ)
🔙 Volver a Boleta de Permiso
🏠 Volver al menú principal',

  'vi. Omisión de Registro de Marcación' => '• Acorde al convenio colectivo debe ser presentado hasta 48 horas siguientes de ocurrido el hecho.
• Justifica la marcación de asistencia de ingreso o salida de lunes a viernes (jornada laboral) y de manera excepcional por única vez en el mes.
[📎 Descargar formato de boleta de permiso](https://drive.google.com/file/d/1VaQ6mmBpQOn3RtMWFWm_MunNmfjIyDmI/view?usp=drive_link)
[📎 Modelo de boleta por omisión](https://drive.google.com/file/d/1OfhvRb-0MQKACH6vuhCnJ1wlnKfS7eyt/view?usp=drive_link)
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
[📎 Modelo Solicitud Compensación 728](https://drive.google.com/file/d/16Ax25zHx2TqNM44sLYChpQRqHICJFOC8/view?usp=drive_link)
[📎 Modelo Solicitud Compensación CAS](https://drive.google.com/file/d/16Ax25zHx2TqNM44sLYChpQRqHICJFOC8/view?usp=drive_link)
[📎 Modelo de llenado de formato de sobretiempo](https://drive.google.com/file/d/1Lvh43r3nDECcelt5lPtRPpnkVPOQ7mkO/view?usp=drive_link)
[📎 Autorización en Sobretiempo](https://drive.google.com/file/d/1NuYeLG0JEmCIX-ATn--bHrrm5Ajz0s-w/view?usp=drive_link)
🔙 Volver a Asistencia
🏠 Volver al menú principal',

  'c. Autorización de ingreso en días de descanso obligatorio, feriados y días no laborables.' => 'Debe ser solicitado por el jefe inmediato, acreditando la necesidad de servicio ante la Gerencia Administrativa, el mismo debe estar suscrito por los servidores, se presenta hasta un (01) día hábil antes de la realización del trabajo, para el acceso a las instalaciones.
[📎 Modelo de solicitud de autorización de ingreso](https://docs.google.com/document/d/1T4KgMSd9cHtQc-czXMf2oJSkIc82wwsI/edit?usp=drive_link&ouid=111203226793113830230&rtpof=true&sd=true)
🔙 Volver a Asistencia
🏠 Volver al menú principal',

  'd. Récord de Asistencia' => 'Para visualizar y revisar tu récord de asistencia, sigue estos pasos:
👉 Ingresa al sistema institucional: https://Sistemas2.mpfn.gob.pe
i. Selecciona Sistema de Récord de Asistencia – ASIST
ii. Selecciona Periodo Asistencial
[iii. 🎥 Ver video tutorial](https://drive.google.com/drive/folders/1abhH2FikOVARPnx2w0n3leTnITKqX8de)
iv. ⚠️ Soporte técnico
Si no visualizas el módulo ASIST dentro de Sistemas2, comunícate con:
📞 Mesa de Ayuda – Área de Tecnologías de la Información
937 461 856
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
[i. 📎 Flyer Convenio Colectivo 2025-2026 Asistencia](https://drive.google.com/drive/folders/1VsQ2MrAg8OX2LlJYMA-6P1eQ9i3vLZXZ)
[ii. 📎 Facilidades sobre el registro de asistencia 20 FEB](https://drive.google.com/drive/folders/1VsQ2MrAg8OX2LlJYMA-6P1eQ9i3vLZXZ)
[iii. 📎 Recuperación horas dejadas de laborar Tolerancia 23 FEB](https://drive.google.com/drive/folders/1VsQ2MrAg8OX2LlJYMA-6P1eQ9i3vLZXZ)
[iv. 📎 Facilidades sobre el registro de asistencia 11 MAR](https://drive.google.com/drive/folders/1VsQ2MrAg8OX2LlJYMA-6P1eQ9i3vLZXZ)
[v. 📎 Facilidades sobre el registro de asistencia 12 MAR](https://drive.google.com/drive/folders/1VsQ2MrAg8OX2LlJYMA-6P1eQ9i3vLZXZ)
🔙 Volver a Asistencia
🏠 Volver al menú principal',

  'g. Contacto en casos de problemas con el reloj biométrico' => 'En caso de que se registren casos de cortes de luz y tenga problemas con la marcación, deberá comunicarse a: 📞 959371597
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
🏠 Volver al menú principal',";

if (strpos($content, "'1. CONTROL DE ASISTENCIA'") === false) {
    echo "Could not find ASISTENCIA section!\n";
} else {
    $content = str_replace($search, $replace, $content);
    file_put_contents($file, $content);
    echo "Replaced Asistencia successfully!";
}
