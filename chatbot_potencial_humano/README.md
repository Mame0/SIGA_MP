# Chatbot Inteligente - Guía de Instalación

## 📋 Requisitos Previos
- Servidor con PHP 7.4+ y MySQL
- Extensión PHP cURL habilitada
- Acceso a phpMyAdmin o consola MySQL
- API Key de Google Gemini (gratis) o OpenAI (pago)

## 🚀 Instalación Paso a Paso

### 1. Configurar la Base de Datos

1. Abre phpMyAdmin
2. Selecciona tu base de datos actual (la misma que usa SIGA)
3. Ve a la pestaña "SQL"
4. Abre el archivo `setup.sql` y copia todo su contenido
5. Pégalo en el área de SQL y haz clic en "Continuar"
6. Verifica que se crearon las tablas: `chatbot_historial`, `chatbot_config`, `chatbot_estadisticas`

### 2. Obtener API Key de Google Gemini (GRATIS)

1. Ve a: https://aistudio.google.com/app/apikey
2. Inicia sesión con tu cuenta de Google
3. Haz clic en "Create API Key"
4. Copia la clave generada (algo como: `AIzaSyXXXXXXXXXXXXXXXXXXXXXXXXXXXXX`)

### 3. Configurar el Chatbot

1. Abre el archivo `config.php`
2. Busca la línea:
   ```php
   define('GEMINI_API_KEY', 'TU_API_KEY_DE_GEMINI_AQUI');
   ```
3. Reemplaza `TU_API_KEY_DE_GEMINI_AQUI` con tu API Key real
4. Guarda el archivo

### 4. Verificar Permisos

Asegúrate de que la carpeta `chatbot` tenga permisos de lectura para el servidor web.

### 5. Probar el Chatbot

1. Abre tu navegador
2. Ve a: `http://localhost/siga/chatbot/` (o tu URL correspondiente)
3. Deberías ver la interfaz del chat
4. Escribe un mensaje de prueba como "Hola"
5. Si todo está bien, el bot responderá

## 🔧 Solución de Problemas

### Error: "Datos inválidos" o "Error del servidor"

1. Abre `config.php` y cambia:
   ```php
   define('CHATBOT_DEBUG_MODE', true);
   ```
2. Recarga la página y revisa los mensajes de error detallados
3. Verifica que la API Key esté correcta

### El bot no responde

1. Verifica que cURL esté habilitado en PHP:
   - Crea un archivo `test.php` con: `<?php phpinfo(); ?>`
   - Busca "cURL" en la página
   - Si no aparece, contacta a tu proveedor de hosting

2. Verifica la conexión a la base de datos:
   - Asegúrate de que el archivo `.credentials/db.php.ini` existe
   - Verifica que las credenciales sean correctas

### Error de conexión a la base de datos

1. Verifica que las tablas se crearon correctamente en phpMyAdmin
2. Asegúrate de que la clase `Db.class.php` esté en `../classes/Db.class.php`

## 🔄 Cambiar de Gemini a OpenAI (Opcional)

Si más adelante quieres usar ChatGPT en lugar de Gemini:

1. Obtén una API Key de OpenAI en: https://platform.openai.com/api-keys
2. Abre `config.php`
3. Agrega tu API Key:
   ```php
   define('OPENAI_API_KEY', 'sk-XXXXXXXXXXXXXXXXX');
   ```
4. Cambia el proveedor activo:
   ```php
   define('PROVEEDOR_IA_ACTIVO', 'openai');
   ```
5. Guarda y recarga

## 📊 Para tu Tesis

### Obtener Estadísticas

Puedes consultar las conversaciones guardadas con:

```sql
-- Total de conversaciones
SELECT COUNT(DISTINCT sesion_id) as total_sesiones FROM chatbot_historial;

-- Mensajes por día
SELECT DATE(fecha_creacion) as fecha, COUNT(*) as mensajes 
FROM chatbot_historial 
GROUP BY DATE(fecha_creacion);

-- Preguntas más frecuentes (análisis manual)
SELECT usuario_mensaje, COUNT(*) as veces 
FROM chatbot_historial 
GROUP BY usuario_mensaje 
ORDER BY veces DESC 
LIMIT 20;
```

### Personalizar el Bot

Edita el archivo `config.php` y modifica:

```php
define('CHATBOT_PROMPT_SISTEMA', 
    'Aquí escribe las instrucciones específicas para tu entidad...'
);
```

## ✅ Checklist Final

- [ ] Base de datos configurada (tablas creadas)
- [ ] API Key de Gemini obtenida y configurada
- [ ] Chatbot responde correctamente
- [ ] Conversaciones se guardan en la BD
- [ ] Interfaz se ve correctamente en móvil y escritorio

## 📞 Soporte

Si tienes problemas, revisa:
1. El archivo `error_log` en la carpeta `chatbot`
2. La consola del navegador (F12 → Console)
3. Los logs de PHP de tu servidor
