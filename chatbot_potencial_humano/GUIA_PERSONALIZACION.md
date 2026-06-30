# Guía de Personalización del Chatbot

## 🎯 Cómo Funciona el Chatbot

Este chatbot usa **Inteligencia Artificial Generativa** (Google Gemini), no es un bot de reglas fijas.

### Ventajas:
- ✅ No necesitas programar cada pregunta/respuesta
- ✅ Entiende lenguaje natural (diferentes formas de preguntar)
- ✅ Genera respuestas dinámicas
- ✅ Tiene memoria de conversación

---

## 📝 Método 1: Entrenar con el Prompt del Sistema (FÁCIL)

### Paso 1: Abre `config.php`
Busca la línea 32 donde dice `CHATBOT_PROMPT_SISTEMA`

### Paso 2: Reemplaza con tu información
Copia el ejemplo de [`ejemplo_personalizacion.php`](file:///c:/xampp/htdocs/siga/chatbot/ejemplo_personalizacion.php) y adáptalo con:

- **Horarios de atención** de tu entidad
- **Ubicación y contacto**
- **Trámites más comunes** y sus requisitos
- **Servicios que ofrecen**
- **Preguntas frecuentes** con sus respuestas

### Ejemplo de Preguntas que Responderá:

**Usuario:** "¿Cuál es el horario de atención?"
**Bot:** "Nuestro horario de atención es de lunes a viernes de 8:00 AM a 5:00 PM..."

**Usuario:** "¿Cómo hago una denuncia?"
**Bot:** "Para presentar una denuncia necesitas: DNI original, descripción detallada..."

**Usuario:** "¿Dónde están ubicados?"
**Bot:** "Estamos ubicados en Calle La Merced 307, Cercado, Arequipa..."

---

## 🗄️ Método 2: Conectar con tu Base de Datos (AVANZADO)

Si quieres que el bot consulte información dinámica de tu BD (ej. estado de trámites):

### Edita `ChatbotAI.php`, línea 25:

```php
public function obtenerRespuesta($mensajeUsuario, $historial = []) {
    // NUEVO: Buscar en la BD antes de enviar a la IA
    $infoBD = $this->buscarEnBaseDatos($mensajeUsuario);
    
    if ($this->proveedor === 'gemini') {
        return $this->consultarGemini($mensajeUsuario, $historial, $infoBD);
    }
    // ...
}

// NUEVA FUNCIÓN
private function buscarEnBaseDatos($mensaje) {
    // Ejemplo: Si pregunta por un trámite
    if (preg_match('/expediente|trámite|caso/i', $mensaje)) {
        // Buscar en tu BD
        $resultado = $this->db->query("SELECT estado FROM tramites WHERE codigo = ?");
        return "Estado del trámite: " . $resultado[0]['estado'];
    }
    return '';
}
```

---

## 📊 Analizar Preguntas Frecuentes (Para tu Tesis)

### Consulta SQL para ver qué preguntan más:

```sql
-- Ver las 20 preguntas más comunes
SELECT 
    usuario_mensaje, 
    COUNT(*) as veces_preguntado
FROM chatbot_historial 
GROUP BY usuario_mensaje 
ORDER BY veces_preguntado DESC 
LIMIT 20;
```

### Con esta información puedes:
1. Identificar qué preguntan más los usuarios
2. Mejorar el prompt del sistema con esas respuestas
3. Crear gráficos para tu tesis

---

## 🎨 Personalizar la Apariencia

### Cambiar el nombre del bot:
**Archivo:** `index.php`, línea 20
```html
<h1>Asistente MPFN</h1>
```

### Cambiar colores:
**Archivo:** `style.css`, línea 7-9
```css
--primary-color: #2563eb;  /* Azul principal */
--user-message-bg: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
```

### Cambiar mensaje de bienvenida:
**Archivo:** `index.php`, línea 29
```html
<p>¡Hola! Soy el asistente del MPFN Arequipa. ¿En qué puedo ayudarte?</p>
```

---

## ✅ Checklist de Personalización

- [ ] Editar `CHATBOT_PROMPT_SISTEMA` con información de tu entidad
- [ ] Cambiar nombre del bot en `index.php`
- [ ] Personalizar mensaje de bienvenida
- [ ] Ajustar colores en `style.css` (opcional)
- [ ] Probar con preguntas reales
- [ ] Revisar respuestas y ajustar el prompt si es necesario

---

## 💡 Consejos

1. **Sé específico en el prompt**: Mientras más detalles des, mejores respuestas dará
2. **Prueba y ajusta**: Haz preguntas y ve si responde bien, luego mejora el prompt
3. **No sobrecargues**: Si el prompt es muy largo (>2000 palabras), divídelo
4. **Usa ejemplos**: En el prompt puedes poner "Ejemplo de respuesta: ..."

---

## 🆘 Soporte

Si el bot responde mal a algo:
1. Revisa el prompt del sistema
2. Agrega más contexto sobre ese tema
3. Prueba de nuevo

**Recuerda:** El bot aprende de lo que le dices en el prompt, no de las conversaciones anteriores.
