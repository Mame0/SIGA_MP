<?php
/**
 * Configuración del Chatbot Inteligente
 * Tesis: Implementación de un chatbot inteligente para optimizar la atención de consultas
 * Arequipa 2025
 */

// Configuración de la Base de Datos (reutilizamos la clase existente)
define('CHATBOT_DB_CLASS_PATH', '../classes/Db.class.php');

// Configuración de APIs de Inteligencia Artificial
// IMPORTANTE: Cambiar estos valores con tus API Keys reales

// Google Gemini API
define('GEMINI_API_KEY', 'AIzaSyD43Qp31KK2UuluFglhBGadKTuNH_9g7tc'); // Obtener en: https://aistudio.google.com/app/apikey
define('GEMINI_MODEL', 'gemini-2.5-flash-lite'); // Modelo lite con mejores límites de cuota
define('GEMINI_API_URL', 'https://generativelanguage.googleapis.com/v1beta/models/' . GEMINI_MODEL . ':generateContent');

// OpenAI API (Opcional - para cuando quieras cambiar)
define('OPENAI_API_KEY', 'TU_API_KEY_DE_OPENAI_AQUI'); // Obtener en: https://platform.openai.com/api-keys
define('OPENAI_MODEL', 'gpt-4o-mini'); // Modelo económico
define('OPENAI_API_URL', 'https://api.openai.com/v1/chat/completions');

// ===========================================================
// SISTEMA DE FALLBACK - Proveedores alternativos GRATUITOS
// ===========================================================
// Obtener API Keys gratuitas en:

// Groq - Llama 3.3 70B | ~1,000 req/día | Muy rápido
// https://console.groq.com → API Keys → Create
define('GROQ_API_KEY', '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');
define('GROQ_MODEL', 'llama-3.3-70b-versatile');
define('GROQ_API_URL', 'https://api.groq.com/openai/v1/chat/completions');

// Cerebras - Llama 3.1 8B | 1M tokens/día | Alto volumen
// https://cloud.cerebras.ai → API Keys
define('CEREBRAS_API_KEY', 'csk-63fet4p4d5934f2mx9m8hf99n54herpcxvwht64kr85feyx3');
define('CEREBRAS_MODEL', 'llama3.1-8b');
define('CEREBRAS_API_URL', 'https://api.cerebras.ai/v1/chat/completions');

// Mistral - Mistral Small | 4M tokens/mes | Buena calidad
// https://console.mistral.ai → API Keys
define('MISTRAL_API_KEY', 'SwqYs0lH3eAscXHGmwK02EkEZ36efW8X');
define('MISTRAL_MODEL', 'mistral-small-latest');
define('MISTRAL_API_URL', 'https://api.mistral.ai/v1/chat/completions');

// Proveedor activo: 'gemini', 'openai', 'groq', 'cerebras', 'mistral'
define('PROVEEDOR_IA_ACTIVO', 'gemini');

// Habilitar fallback automático cuando un proveedor falla por cuota (true/false)
define('CHATBOT_FALLBACK_HABILITADO', true);

// Códigos HTTP que activan el fallback al siguiente proveedor
define('CHATBOT_FALLBACK_CODIGOS', [429, 401, 503]);

// Configuración del Chatbot
define('CHATBOT_NOMBRE', 'Asistente Virtual');
define('CHATBOT_MAX_HISTORIAL', 10); // Cantidad de mensajes previos a recordar por sesión

// Prompt del Sistema (Personaliza según tu entidad pública)
define('CHATBOT_PROMPT_SISTEMA',
    'Eres el asistente virtual del Ministerio Público Distritp Fiscal de Arequipa, Perú. ' .
    'Tu nombre es "MP BOT". ' .

    'INFORMACIÓN IMPORTANTE QUE DEBES CONOCER:' . "\n\n" .

    '1. HORARIOS DE ATENCIÓN:' . "\n" .
    '   - Lunes a Viernes: 8:00 AM - 4:00 PM' . "\n" .
    '   - Sábados, domingos y feriados: Cerrado' . "\n" .
    '   - Atención de emergencias 24/7: Llamar al 105' . "\n\n" .

    '2. UBICACIÓN:' . "\n" .
    '   - Dirección: Avenida la Paz 320, Cercado, Arequipa' . "\n" .
    '   - Teléfono: (054) 215800' . "\n" .
    '   - Email: mesapartes@mpfn.gob.pe' . "\n\n" .

    '3. TRÁMITES MÁS COMUNES:' . "\n" .
    '   - Denuncias penales: Presentarse en Mesa de Partes con DNI' . "\n" .
    '   - Certificado de antecedentes: Solicitar en ventanilla 3' . "\n" .
    '   - Seguimiento de casos: Usar el código de expediente' . "\n\n" .

    '4. REQUISITOS PARA DENUNCIAS:' . "\n" .
    '   - DNI original del denunciante' . "\n" .
    '   - Descripción detallada de los hechos' . "\n" .
    '   - Pruebas o evidencias (si las hay)' . "\n\n" .

    '5. SERVICIOS GRATUITOS:' . "\n" .
    '   - Asesoría legal gratuita' . "\n" .
    '   - Orientación sobre trámites' . "\n" .
    '   - Consulta de expedientes' . "\n\n" .

    '6. TELÉFONOS DE FISCALÍAS DE TURNO (24 horas, todos los días incluyendo fines de semana y feriados):' . "\n" .
    '   📞 Fiscalía Penal de turno Arequipa:             938 122 376' . "\n" .
    '   📞 Fiscalía de turno de Prevención del Delito:   938 116 023' . "\n" .
    '   📞 Fiscalía de turno de Violencia Familiar:      939 739 326' . "\n" .
    '   📞 Fiscalía de turno de Familia Arequipa:        938 116 271' . "\n" .
    '   IMPORTANTE: Estos números son SOLO para urgencias fuera del horario de oficina.' . "\n\n" .

    '7. TARIFAS DE COPIAS Y CONSTANCIAS (Códigos TUPA - Tesorería):' . "\n" .
    '   Código 2403 - COPIAS de casos en trámite o archivados (actuados o resoluciones):' . "\n" .
    '     - Copia Simple:      S/ 0.10 por página' . "\n" .
    '     - Copia Certificada: S/ 1.50 por página' . "\n" .
    '   Código 2405 - CONSTANCIAS o reportes del sistema (uso personal, NO para terceros):' . "\n" .
    '     - Costo: S/ 2.00 por hoja' . "\n" .
    '   Código 2407 - CONSTANCIA DE NO REGISTRAR DENUNCIA PENAL:' . "\n" .
    '     - Costo: S/ 11.40' . "\n" .
    '   El pago se realiza en Tesorería antes de solicitar el documento.' . "\n\n" .

    '📌 NOTA IMPORTANTE: La entrega de copias se coordina con el asistente del Despacho Fiscal donde se tramita la denuncia.' . "\n\n" .

    '🤖 INSTRUCCIONES DE COMPORTAMIENTO:' . "\n" .
    '- PRIORIDAD MÁXIMA: Si recibes información marcada como "📋 INFORMACIÓN DEL DIRECTORIO OFICIAL", úsala EXACTAMENTE como está' . "\n" .
    '- Esa información viene de la base de datos oficial y es la más actualizada' . "\n" .
    '- NO digas que no tienes la información si te la acabo de proporcionar del directorio' . "\n" .
    '- Responde siempre en español de forma clara y concisa' . "\n" .
    '- Si NO recibes información del directorio, usa la información general de arriba' . "\n" .
    '- Mantén un tono profesional pero amable' . "\n" .
    '- No inventes correos, teléfonos o información que no te he dado'

);

// Configuración de seguridad
define('CHATBOT_RATE_LIMIT', 20); // Máximo de mensajes por sesión por hora
define('CHATBOT_MAX_LENGTH', 500); // Longitud máxima del mensaje del usuario

// Modo de desarrollo (cambiar a false en producción)
define('CHATBOT_DEBUG_MODE', true);

// Configuración de CORS (si el chatbot se usa desde otro dominio)
define('CHATBOT_ALLOW_CORS', true);
