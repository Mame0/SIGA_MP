-- ============================================
-- MIGRACIÓN: Renombrar Tablas con Prefijo MP
-- ============================================
-- Este script renombra todas las tablas del chatbot para usar el prefijo mp_
-- IMPORTANTE: Hacer backup de la base de datos antes de ejecutar
-- ============================================

-- Verificar que las tablas existen antes de renombrar
SELECT 'Verificando tablas existentes...' as mensaje;

-- Renombrar las tablas (preserva todos los datos, índices y claves)
RENAME TABLE 
    chatbot_config TO mp_chatbot_config,
    chatbot_directorio TO mp_chatbot_directorio,
    chatbot_estadisticas TO mp_chatbot_estadisticas,
    chatbot_historial TO mp_chatbot_historial;

-- Verificar que las tablas se renombraron correctamente
SELECT 'Tablas renombradas exitosamente' as mensaje;

-- Mostrar las nuevas tablas
SHOW TABLES LIKE 'mp_chatbot_%';

-- Verificar cantidad de registros en cada tabla
SELECT 'mp_chatbot_config' as tabla, COUNT(*) as registros FROM mp_chatbot_config
UNION ALL
SELECT 'mp_chatbot_directorio' as tabla, COUNT(*) as registros FROM mp_chatbot_directorio
UNION ALL
SELECT 'mp_chatbot_estadisticas' as tabla, COUNT(*) as registros FROM mp_chatbot_estadisticas
UNION ALL
SELECT 'mp_chatbot_historial' as tabla, COUNT(*) as registros FROM mp_chatbot_historial;

-- ============================================
-- FIN DE LA MIGRACIÓN
-- ============================================
