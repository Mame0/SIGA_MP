-- Script de configuración para el Chatbot Inteligente
-- Tesis: Implementación de un chatbot inteligente para optimizar la atención de consultas

-- Tabla para almacenar el historial de conversaciones
CREATE TABLE IF NOT EXISTS mp_chatbot_historial (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sesion_id VARCHAR(100) NOT NULL COMMENT 'ID único de sesión del usuario',
    usuario_mensaje TEXT NOT NULL COMMENT 'Mensaje enviado por el usuario',
    bot_respuesta TEXT NOT NULL COMMENT 'Respuesta generada por el bot',
    proveedor_ia VARCHAR(20) DEFAULT 'gemini' COMMENT 'gemini o openai',
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    ip_usuario VARCHAR(45) NULL COMMENT 'IP del usuario (opcional)',
    INDEX idx_sesion (sesion_id),
    INDEX idx_fecha (fecha_creacion)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla para configuración del chatbot (opcional pero útil)
CREATE TABLE IF NOT EXISTS mp_chatbot_config (
    id INT AUTO_INCREMENT PRIMARY KEY,
    clave VARCHAR(100) UNIQUE NOT NULL,
    valor TEXT,
    descripcion VARCHAR(255),
    fecha_modificacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insertar configuraciones iniciales
INSERT INTO mp_chatbot_config (clave, valor, descripcion) VALUES
('proveedor_activo', 'gemini', 'Proveedor de IA activo: gemini u openai'),
('nombre_bot', 'Asistente Virtual', 'Nombre del chatbot'),
('mensaje_bienvenida', '¡Hola! Soy tu asistente virtual. ¿En qué puedo ayudarte hoy?', 'Mensaje inicial del bot'),
('prompt_sistema', 'Eres un asistente virtual amable y profesional de una entidad pública en Arequipa, Perú. Responde de manera clara, concisa y útil a las consultas de los ciudadanos.', 'Instrucciones para la IA')
ON DUPLICATE KEY UPDATE valor=valor;

-- Tabla para estadísticas (útil para la tesis)
CREATE TABLE IF NOT EXISTS mp_chatbot_estadisticas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    fecha DATE NOT NULL,
    total_conversaciones INT DEFAULT 0,
    total_mensajes INT DEFAULT 0,
    tiempo_respuesta_promedio DECIMAL(10,2) DEFAULT 0 COMMENT 'En segundos',
    UNIQUE KEY unique_fecha (fecha)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
