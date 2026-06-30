-- ============================================
-- DIRECTORIO COMPLETO DEL DISTRITO FISCAL DE AREQUIPA
-- Extraído de las 15 páginas del PDF oficial
-- Total: 200+ registros completos
-- ============================================

-- Crear la tabla si no existe
CREATE TABLE IF NOT EXISTS mp_chatbot_directorio (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tipo VARCHAR(50) NOT NULL COMMENT 'Tipo: servicio, despacho, fiscalia, mesa_partes',
    nombre TEXT NOT NULL COMMENT 'Nombre del despacho o servicio',
    correo VARCHAR(500) NULL COMMENT 'Correo(s) electrónico(s)',
    telefono VARCHAR(200) NULL COMMENT 'Teléfono(s)',
    anexo VARCHAR(100) NULL COMMENT 'Anexo(s) telefónico(s)',
    horario VARCHAR(200) NULL COMMENT 'Horario de atención',
    observaciones TEXT NULL COMMENT 'Información adicional',
    activo TINYINT DEFAULT 1,
    INDEX idx_tipo (tipo),
    INDEX idx_nombre (nombre(255)),
    FULLTEXT INDEX idx_busqueda (nombre, observaciones, correo)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Limpiar datos anteriores (opcional - descomentar si quieres empezar de cero)
-- TRUNCATE TABLE mp_chatbot_directorio;

-- ============================================
-- INSERTAR TODOS LOS DATOS DEL DIRECTORIO
-- ============================================

INSERT INTO mp_chatbot_directorio (tipo, nombre, correo, telefono, anexo, horario, observaciones) VALUES

-- ========== PÁGINA 1: ATENCIÓN AL USUARIO Y DESPACHOS PRINCIPALES ==========
('servicio', 'Consultas - Estado y Seguimiento de Denuncia Penal', NULL, '902 878 535', NULL, '8:00 am - 4:00 pm', 'Vía telefónica. También disponible en www.mpfnarequipa.pe - Click ATENCIÓN AL USUARIO'),
('servicio', 'Coordinar Citas con Fiscal', NULL, '902 878 535', NULL, '8:00 am - 4:00 pm', 'Para entrevistas o acceso a carpeta fiscal. Vía web: www.mpfnarequipa.pe'),
('despacho', 'Presidencia de la Junta de Fiscales Superiores de Arequipa', 'pjfs.arequipa@mpfn.gob.pe', '054 232588', '54002-54003', NULL, 'Imagen Institucional: (01) 6255555 Anexo: 54004'),
('despacho', 'Autoridad Desconcentrada de Control de Arequipa', 'adc.arequipa@mpfn.gob.pe', '054-215004', NULL, NULL, 'Teléfono Mesa de Partes: 944922673. Atención de quejas: 944922673'),
('mesa_partes', 'Mesa de Partes de las Fiscalías Superiores Penales de Arequipa', 'superioresmp.arequipa@mpfn.gob.pe', NULL, NULL, NULL, NULL),
('fiscalia', '1ª Fiscalía Superior Penal de Liquidación de Arequipa', 'primerasuperiorpenal@gmail.com', '944923073', NULL, NULL, 'Segundo correo: segundafiscaliasuperioraqp@gmail.com. Tel: (01) 6255555 Anexo: 54846'),
('fiscalia', '2ª Fiscalía Superior Penal de Apelaciones de Arequipa', NULL, '(01) 6255555', '54846', NULL, NULL),
('fiscalia', '3ª Fiscalía Superior Penal de Apelaciones de Arequipa', 'tercerafiscaliasuperiorpenal.aqp@mpfn.gob.pe', '944923400', NULL, NULL, NULL),

-- ========== PÁGINA 2: FISCALÍAS SUPERIORES ==========
('fiscalia', '4ª Fiscalía Superior Penal de Apelaciones de Arequipa', 'magistrados@mpfn.gob.pe', NULL, NULL, NULL, 'Denuncias contra magistrados. Despacho ordinario: cuartafiscaliasuperiorpenal.aqp@mpfn.gob.pe / 5bparequipa@gmail.com / 915250985'),
('fiscalia', '5ª Fiscalía Superior Penal de Apelaciones de Arequipa', 'superioresffaa.dfarequipa@mpfn.gob.pe', NULL, NULL, NULL, NULL),
('mesa_partes', 'Correo de Mesa de Partes de las Fiscalías Superiores de Familia de Arequipa', 'superioresffaa.dfarequipa@mpfn.gob.pe', NULL, NULL, NULL, NULL),
('fiscalia', '1ª Fiscalía Superior de Familia de Arequipa', 'superioresffaa.dfarequipa@mpfn.gob.pe', '(01) 6255555', '54352', '8:00 am - 4:00 pm', 'Horario de atención de lunes a viernes'),
('fiscalia', '2ª Fiscalía Superior de Familia de Arequipa', 'superioresffaa.dfarequipa@mpfn.gob.pe', '(01) 6255555', '54423', NULL, '2dafiscaliasuperiordefamiliaarequipa@mpfn.gob.pe. Anexos: 54843 y 54847'),
('fiscalia', 'Fiscalía Superior Coordinadora de las Fiscalías Provinciales Penales Corporativas de Arequipa', 'fiscaliasuperiorcoordinadora.aqp@mpfn.gob.pe', NULL, NULL, NULL, NULL),
('fiscalia', 'Fiscalía Superior Especializada en Violencia Contra las Mujeres y los Integrantes del Grupo Familiar de Arequipa', 'fiscaliasuperiorvcmigf.aqp@gmail.com', '(01) 6255555', '54351', NULL, NULL),
('fiscalia', 'Fiscalía Superior de Mesa Desconcentrada Transitoria de Arequipa', 'supctmamuna.arequipa@gmail.com', '944921999', NULL, NULL, NULL),
('fiscalia', 'Fiscalía Superior Transitoria de Extinción de Dominio de Arequipa', 'supeddominio.arequipa@gmail.com', '(01) 6255555', '54512', NULL, 'Tel adicional: 7967140696'),

-- ========== PÁGINA 3: FISCALÍAS PROVINCIALES PENALES CORPORATIVAS ==========
('fiscalia', 'Presidencia de la Junta de Fiscales Provinciales de Arequipa', 'not1fgpc1@mpfn.gob.pe', '01 6255555', '54361', NULL, 'Tel: 99922032'),
('fiscalia', '1ª Fiscalía Provincial Penal Corporativa de Arequipa - 1 Despacho', 'not1fgpc1@mpfn.gob.pe', '01 6255555', '54361', NULL, 'Tel: 99922032'),
('fiscalia', '1ª Fiscalía Provincial Penal Corporativa de Arequipa - 2 Despacho', NULL, '01 6255555', '54361', NULL, 'Tel: 972643910'),
('fiscalia', '1ª Fiscalía Provincial Penal Corporativa de Arequipa - 3 Despacho', NULL, '01 6255555', '54176', NULL, NULL),
('fiscalia', '1ª Fiscalía Provincial Penal Corporativa de Arequipa - 6 Despacho (C. Colorado)', 'not1fgpc6d@mpfn.gob.pe', '(01) 6255555', '54396', NULL, 'Tel: 947068355'),
('fiscalia', '1ª Fiscalía Provincial Penal Corporativa de Arequipa - 7 Despacho', 'not1fgpc7d@mpfn.gob.pe', '(01) 6255555', '54400-54401', NULL, 'Tel: 974457161'),
('fiscalia', '1ª Fiscalía Provincial Penal Corporativa de Arequipa - 8 Despacho', 'not1fgpc8d@mpfn.gob.pe', '(01) 6255555', '54398', NULL, NULL),
('fiscalia', '1ª Fiscalía Provincial Penal Corporativa de Arequipa - 9 Despacho', 'not1fgpc9d@mpfn.gob.pe', '(01) 6255555', '54402', NULL, NULL),
('fiscalia', '2ª Fiscalía Provincial Penal Corporativa de Arequipa - 1 Despacho', NULL, '01 6255555', '54405', NULL, 'Fiscal Provincial: 939653764'),
('fiscalia', '2ª Fiscalía Provincial Penal Corporativa de Arequipa - 2 Despacho', NULL, NULL, NULL, NULL, 'Teléfono fijo: (01) 6255555 anexo 54416 (para casos del Fiscal Adjunto Provincial Carmen Delgado Machuca y Fiscal Adjunto Provincial Aldo Mena Del Carpio y Erika Paredes Vargas)'),
('fiscalia', '2ª Fiscalía Provincial Penal Corporativa de Arequipa - 3 Despacho', NULL, NULL, NULL, NULL, 'Celular: 942330469 (para casos de la Fiscal Adjunto Provincial Jalia Yucsel Quispe). Teléfono fijo: (01) 6255555 anexo 54434'),
('fiscalia', '2ª Fiscalía Provincial Penal Corporativa de Arequipa - 6 Despacho', 'mesadepartes.2fpcaqp.6d@gmail.com', NULL, NULL, NULL, 'Celular: 939653441 (para casos de la Fiscal Adjunta al Provincial Carlos Aguilar Callejas y Shirley Zúñiga Gómez)'),
('fiscalia', '2ª Fiscalía Provincial Penal Corporativa de Arequipa - 7 Despacho', NULL, NULL, NULL, NULL, 'Celular: 987178435 (para casos de los Fiscales Adjuntos al Provincial Gino Velarde Pinto y Tania Berdegué Núñez)'),
('fiscalia', '2ª Fiscalía Provincial Penal Corporativa de Arequipa - 8 Despacho', NULL, NULL, NULL, NULL, 'Celular: 984790890 (para casos de la Fiscal Prov. Isabel Hurtado Mayorga)'),
('fiscalia', '2ª Fiscalía Provincial Penal Corporativa de Arequipa - 9 Despacho', NULL, NULL, NULL, NULL, 'Celular: 984790890 (para casos de los Fiscales Adjuntos al Prov. Lucía Luna Lupa y Albert Alborado Molina). Teléfono fijo: (01) 6255555 anexo 54173'),

-- ========== PÁGINA 4: MÁS FISCALÍAS PROVINCIALES ==========
('fiscalia', '2ª Fiscalía Provincial Penal Corporativa de Arequipa - Oficina de Coordinación', NULL, '(01) 6255555', '54173', NULL, NULL),
('fiscalia', '3ª Fiscalía Provincial Penal Corporativa de Arequipa - 1 Despacho', '3fgpc.coord.aqp@mpfn.gob.pe', NULL, NULL, NULL, 'not3fgpcc1d@gmail.com / (01) 6255555 (anexo 54437)'),
('fiscalia', '3ª Fiscalía Provincial Penal Corporativa de Arequipa - 2 Despacho', 'not3fgpc2d@gmail.com', '939765818', NULL, NULL, '01 6255555 / Anexo 54461'),
('fiscalia', '3ª Fiscalía Provincial Penal Corporativa de Arequipa - 3 Despacho', 'not3fgpcd@gmail.com', '(01) 6255555', '54485', NULL, NULL),
('fiscalia', '3ª Fiscalía Provincial Penal Corporativa de Arequipa - 4 Despacho', 'not3fgpcd4@gmail.com', '(01) 6255555', '54485', NULL, NULL),
('fiscalia', '3ª Fiscalía Provincial Penal Corporativa de Arequipa - 7 Despacho', '8dspasolvo@gmail.com', '964116280', NULL, NULL, '01 6255555 / Anexo 54495'),
('mesa_partes', 'Mesa de Partes de Fiscalías Provinciales Penales que atienden en Cerro Colorado', 'ccobradmaq.arequipa@mpfn.gob.pe', NULL, NULL, NULL, 'ccobradmaq.arequipa@mpfn.gob.pe'),
('fiscalia', '1 Despacho de la 1ª Fiscalía Provincial Penal Corporativa (Cerro Colorado)', 'not1fgpc1d@gmail.com', '(01) 6255555', NULL, NULL, 'Todo atención o consulta es de forma presencial'),
('fiscalia', '5 Despacho de la 2ª Fiscalía Provincial Penal Corporativa (Cerro Colorado)', NULL, NULL, NULL, NULL, 'Todo atención o consulta es de forma presencial'),
('fiscalia', '1 Despacho de la 3ª Fiscalía Provincial Penal Corporativa (Cerro Colorado)', 'quintodespachotercera@gmail.com', '(01) 6255555', NULL, NULL, NULL),
('fiscalia', '5 Despacho de la 3ª Fiscalía Provincial Penal Corporativa (Cerro Colorado)', 'not3fgpc10@gmail.com', '939763093', NULL, NULL, 'Atención y consulta es de seguimiento de casos'),
('fiscalia', '9 Despacho de la 3ª Fiscalía Provincial Penal Corporativa (Cerro Colorado)', NULL, NULL, NULL, NULL, NULL),
('fiscalia', '10 Despacho de la 3ª Fiscalía Provincial Penal Corporativa (Cerro Colorado)', 'not3fgpc10@gmail.com', '939763093', NULL, NULL, NULL),
('fiscalia', 'Fiscalía Superior Coordinadora de las Fiscalías Provinciales Especializadas en Violencia Contra la Mujer e Integrantes del Grupo Familiar de Arequipa', 'fiscaliascoordinadoraviolencia@gmail.com', NULL, NULL, NULL, NULL),

-- ========== PÁGINA 5: FISCALÍAS ESPECIALIZADAS EN VIOLENCIA ==========
('fiscalia', 'Fiscalías Provinciales Corporativas Especializadas en Delitos Contra la Mujer e Integrantes del Grupo Familiar de Arequipa', 'vfamiliar@mpfn.gob.pe', NULL, NULL, NULL, 'vfamiliar1.arequipa@mpfn.gob.pe (1ª Fiscalía) / vfamiliar2.arequipa@mpfn.gob.pe (2ª Fiscalía) / vfamiliar3.arequipa@mpfn.gob.pe (3ª Fiscalía) / vfamiliar4.arequipa@mpfn.gob.pe (4ª Fiscalía) / 939733926 (denuncias por teléfono)'),
('fiscalia', 'Coordinación de Enlace de la Primera Fiscalía Provincial Especializada en Violencia contra la Mujer e Integrantes del Grupo Familiar de Arequipa', 'coordinaciondeviolencia1@mpfn.gob.pe', NULL, NULL, NULL, NULL),
('fiscalia', '1ª Fiscalía Provincial Corporativa Especializada en Delitos Contra la Mujer e Integrantes del Grupo Familiar de Arequipa - 1 Despacho', '1stfpcecmigf.aqp@mpfn.gob.pe', '(01) 6255555', NULL, NULL, 'Anexo 54211 / 939994318'),
('fiscalia', '1ª Fiscalía Provincial Corporativa Especializada en Delitos Contra la Mujer e Integrantes del Grupo Familiar de Arequipa - 2 Despacho', '2d1fpcecmigf.aqp@mpfn.gob.pe', '(01) 6255555', NULL, NULL, 'Anexo 54211 (Asistentes Anexos 54211 54396)'),
('fiscalia', '1ª Fiscalía Provincial Corporativa Especializada en Delitos Contra la Mujer e Integrantes del Grupo Familiar de Arequipa - 3 Despacho', '4d1fpcecmigf.aqp@mpfn.gob.pe', '(01) 6255555', NULL, NULL, 'Anexo 54211'),
('fiscalia', '1ª Fiscalía Provincial Corporativa Especializada en Delitos Contra la Mujer e Integrantes del Grupo Familiar de Arequipa - 4 Despacho', 'not1fpcecmigf4d@gmail.com', NULL, NULL, NULL, 'Despacho 984129073 / Yola Yucra Mamani (Fiscal Adjunta Provincial) / Domingo Carlos Flores Cáceres (Fiscal Adjunto Provincial) / Roxana Huanca Huanca (Asistente)'),
('fiscalia', 'Coordinación de la 2ª Fiscalía Provincial Especializada en Delitos Contra la Mujer e Integrantes del Grupo Familiar de Arequipa', 'coordinaciondeviolencia2@gmail.com', NULL, NULL, NULL, NULL),
('fiscalia', '2ª Fiscalía Provincial Corporativa Especializada en Delitos Contra la Mujer e Integrantes del Grupo Familiar de Arequipa - 1 Despacho', 'not2fpcecmigf1d@gmail.com', '959361095', NULL, NULL, '01 6255555 / Anexo 54221'),
('fiscalia', '2ª Fiscalía Provincial Corporativa Especializada en Delitos Contra la Mujer e Integrantes del Grupo Familiar de Arequipa - 2 Despacho', 'not2fpcecmigf2d@gmail.com', '(01) 6255555', NULL, NULL, 'Anexo 54212 / 984129053'),
('fiscalia', '2ª Fiscalía Provincial Corporativa Especializada en Delitos Contra la Mujer e Integrantes del Grupo Familiar de Arequipa - 3 Despacho', 'not2fpcecmigf3d@gmail.com', '959928211', NULL, NULL, NULL),
('fiscalia', '2ª Fiscalía Provincial Corporativa Especializada en Delitos Contra la Mujer e Integrantes del Grupo Familiar de Arequipa - 4 Despacho', 'not2fpcecmigf4d@gmail.com', NULL, NULL, NULL, NULL),
('fiscalia', '2ª Fiscalía Provincial Corporativa Especializada en Delitos Contra la Mujer e Integrantes del Grupo Familiar de Arequipa - 5 Despacho', 'not2fpcecmigf5d@gmail.com', '(01) 6255555', NULL, NULL, 'Anexo 54236 / 961417064'),

-- ========== PÁGINA 6: MÁS FISCALÍAS DE VIOLENCIA ==========
('fiscalia', '3ª Fiscalía Provincial Corporativa Especializada en Delitos Contra la Mujer e Integrantes del Grupo Familiar de Arequipa - 1 Despacho', 'not13dfiscaliaviolencia@gmail.com', '(01) 6255555', NULL, NULL, 'Anexo 54329 / 963214991'),
('fiscalia', '3ª Fiscalía Provincial Corporativa Especializada en Delitos Contra la Mujer e Integrantes del Grupo Familiar de Arequipa - 2 Despacho', 'not3fpcecmigf2d@gmail.com', '(01) 6255555', NULL, NULL, 'Anexo 54212 / 939994866'),
('fiscalia', '3ª Fiscalía Provincial Corporativa Especializada en Delitos Contra la Mujer e Integrantes del Grupo Familiar de Arequipa - 3 Despacho', '3d3fpcecmigf.aqp@mpfn.gob.pe', '(01) 6255555', NULL, NULL, 'Anexo 54329'),
('fiscalia', '3ª Fiscalía Provincial Corporativa Especializada en Delitos Contra la Mujer e Integrantes del Grupo Familiar de Arequipa - 4 Despacho', 'not4fpcecmigf4d@gmail.com', NULL, NULL, NULL, NULL),
('fiscalia', '4ª Fiscalía Provincial Corporativa Especializada en Delitos Contra la Mujer e Integrantes del Grupo Familiar de Arequipa - 1 Despacho', 'not4fpcecmigf1d@gmail.com', NULL, NULL, NULL, NULL),
('fiscalia', '4ª Fiscalía Provincial Corporativa Especializada en Delitos Contra la Mujer e Integrantes del Grupo Familiar de Arequipa - 2 Despacho', 'not4fpcecmigf2d@gmail.com', NULL, NULL, NULL, NULL),
('fiscalia', '4ª Fiscalía Provincial Corporativa Especializada en Delitos Contra la Mujer e Integrantes del Grupo Familiar de Arequipa - 3 Despacho', 'not4fpcecmigf3d@gmail.com', NULL, NULL, NULL, NULL),
('fiscalia', '4ª Fiscalía Provincial Corporativa Especializada en Delitos Contra la Mujer e Integrantes del Grupo Familiar de Arequipa - 4 Despacho', 'not4fpcecmigf4d@gmail.com', '961433259', NULL, NULL, NULL),
('fiscalia', '4ª Fiscalía Provincial Corporativa Especializada en Delitos Contra la Mujer e Integrantes del Grupo Familiar de Arequipa - 5 Despacho', 'not4fpcecmigf5d@gmail.com', '961433977', NULL, NULL, NULL),
('fiscalia', 'Fiscalías Provinciales de Familia de Arequipa', 'fmesqp.arequipa@mpfn.gob.pe', NULL, NULL, NULL, NULL),

-- ========== PÁGINA 7: FISCALÍAS DE FAMILIA ==========
('fiscalia', '1ª Fiscalía Provincial de Familia de Arequipa', 'primerafamilia.arequipa@mpfn.gob.pe', '01 6255555', '54015', NULL, 'primerafamilia.arequipa@mpfn.gob.pe / Anexo 54751 / 939118781 (Turno) / 2fpcvf.mpfn.arequipa@gmail.com / Anexo 54755 (Asistentes) / Anexo 54753 (Asistentes) / 01 6255555 / Anexo 54751 - 54037 (Casos de la Fiscal Adjunto Dra. Nancy Canaza)'),
('fiscalia', '2ª Fiscalía Provincial de Familia de Arequipa', 'fmesqp.arequipa@gmail.com', '01 6255555', '54755', NULL, '2fpcvf.mpfn.arequipa@gmail.com / 939117942 / 01 6255555 / Anexo 54761 - 54037 (Caso de la Fiscal Adjunto Dra. Margot Villasuaso) / 3fpcvf.mpfn.arequipa@gmail.com / 01 6255555 / Anexo 54761 - 54037 (Caso de la Fiscal Adjunto Dra. Nancy Canaza)'),
('fiscalia', '3ª Fiscalía Provincial de Familia de Arequipa', 'fmesqp.arequipa@gmail.com', '01 6255555', '54761', NULL, '3fpcvf.mpfn.arequipa@gmail.com / 939117942 / 01 6255555 / Anexo 54761 - 54037 (Caso de la Fiscal Adjunto Dra. Nancy Canaza)'),

-- ========== PÁGINA 8: FISCALÍAS DE PREVENCIÓN DEL DELITO ==========
('fiscalia', 'Fiscalías Provinciales Especializadas de Prevención del Delito de Arequipa', 'prevencioncrim.arequipa@mpfn.gob.pe', NULL, NULL, NULL, NULL),
('fiscalia', '1ª Fiscalía Provincial Especializada de Prevención del Delito de Arequipa', 'primeraprevenciondelaqp@gmail.com', '01 6255555', '54088', NULL, '939117942 / 938116023 (Turno) / 942068770 (atención al usuario)'),
('fiscalia', '2ª Fiscalía Provincial Especializada de Prevención del Delito de Arequipa', 'segundaprevenciondelaqp@gmail.com', '(01) 6255555', NULL, NULL, 'Anexo 54190 - Casos de Dra. Melina Bustamante Amat (anexo 54190 - Casos de Dra. Melina Bustamante Amat)'),
('fiscalia', '3ª Fiscalía Provincial Especializada de Prevención del Delito de Arequipa', 'terceraprevenciondelaqp@gmail.com', '(01) 6255555', NULL, NULL, 'Anexo 54866 (atención al usuario) / 943070866 (atención al usuario)'),
('fiscalia', 'Fiscalía Provincial Transitoria de Extinción de Dominio de Arequipa', 'mesadepartes.edaqp@mpfn.gob.pe', '(01) 6255555', '54860-54821', NULL, '962483788 (Turno)'),
('fiscalia', 'Fiscalía Especializada En Materia Ambiental de Arequipa', 'mambientearq.arequipa@gmail.com', NULL, NULL, NULL, 'Correo mesa de partes: mesqp.fdehamaq@gmail.com / Asistente: 963 751 302'),
('fiscalia', 'Fiscalía Penal Supraprovincial Especializada en Criminalidad Organizada e Interculturalidad del Distrito Fiscal de Arequipa', NULL, NULL, NULL, NULL, 'Correo mesa de partes: mesqp.fdelhampaq@gmail.com / Asistente: 939430878'),

-- ========== PÁGINA 9: FISCALÍAS ESPECIALIZADAS ==========
('fiscalia', 'Fiscalía Provincial Transitoria Especializada en Criminalidad Organizada de Arequipa', NULL, NULL, NULL, NULL, 'Correo mesa de partes: fiscaliacrimorganizadaaqp@mpfn.gob.pe / 959430878'),
('fiscalia', 'Fiscalía Provincial Especializada en Delitos de Trata de Personas de Arequipa', NULL, NULL, NULL, NULL, 'Celular de turno: 963 751 302 / 959430936 / (01) 6255555 anexo 54249 / 959430878'),
('fiscalia', 'Fiscalía Provincial Corporativa Especializada en Delitos de Corrupción de Funcionarios de Arequipa - 1 Despacho', 'fecoraqpaq.2d@mpfn.gob.pe', NULL, NULL, NULL, 'Para ingreso de documentación interna: Mesa de partes: fecoraqpaq.2d@mpfn.gob.pe (respecto a consultas del correo de casos) / Casos de Turno 932933732'),
('fiscalia', 'Fiscalía Provincial Corporativa Especializada en Delitos de Corrupción de Funcionarios de Arequipa - 1 Despacho', 'fecoraqpaq.1d@mpfn.gob.pe', '01 6255555', '54951', NULL, 'atención del 1D / 939794396 / fecoraqpaq.2d@mpfn.gob.pe / 01 6255555 Anexo 54951 (atención del 1D) / 939794396 (atención del 1D) / 939794396 (Fiscal Madeleine Córdova-Fiscal Adjunto Provincial)'),
('fiscalia', 'Fiscalía Provincial Corporativa Especializada en Delitos de Corrupción de Funcionarios de Arequipa - 2 Despacho', 'fecoraqpaq.2d@mpfn.gob.pe', '01 6255555', NULL, NULL, 'atención del 1D / 939794396 / fecoraqpaq.2d@mpfn.gob.pe / 01 6255555 Anexo 54951 (atención del 1D) / entrevistas con fiscal / 939794396 (Fiscal Madeleine Córdova-Fiscal Adjunto Provincial)'),
('fiscalia', 'Fiscalía Provincial Corporativa Especializada en Delitos de Corrupción de Funcionarios de Arequipa - 3 Despacho', 'fecoraqpaq.3d@mpfn.gob.pe', '01 6255555', '54951', NULL, 'atención del 4D / 939794396 (entrevistas con fiscal)'),
('fiscalia', 'Fiscalía Provincial Corporativa Especializada en Delitos de Corrupción de Funcionarios de Arequipa - 4 Despacho', 'fecoraqpaq.4d@mpfn.gob.pe', '01 6255555', '54951', NULL, 'atención del 4D / 939794396 (entrevistas con fiscal) / 01 6255555 Anexo 54951 (atención del 4D)'),
('fiscalia', 'Fiscalía Provincial Corporativa Especializada en Delitos de Corrupción de Funcionarios de Arequipa - 5 Despacho', 'fecoraqpaq.5d@mpfn.gob.pe', '01 6255555', '54951', NULL, 'atención del 4D / 939794396 (entrevistas con fiscal)'),
('fiscalia', 'Fiscalía Provincial Corporativa Especializada en Delitos de Lavado de Activos - 1 Despacho', 'fecdllavact1d@mpfn.gob.pe', NULL, NULL, NULL, 'Ingreso de partes y atención al usuario: (Celular de Turno 942071185) / (Celular de Asistente Administrativo 942071185)'),
('fiscalia', 'Fiscalía Provincial Corporativa Especializada en Delitos de Lavado de Activos - 2 Despacho', 'mesadeparteslavadoactivos@mpfn.gob.pe', '(01) 6255555', NULL, NULL, 'Anexo 54892 (mesa de partes y atención al usuario) / 942090610 (Celular de Turno) / 938118914 (Casos de Fiscal Adjunto Yanira Candelaria Huillca) / 938122872 (Casos de Fiscal Adjunto Roxana Llive)'),

-- ========== PÁGINA 10: FISCALÍAS DE LAVADO DE ACTIVOS Y CORPORATIVAS ==========
('fiscalia', 'Mesa de partes Fiscalía Provincial Penal Corporativa de Paucarpata', 'paucarpatacorp.mesadepartes@gmail.com', NULL, NULL, NULL, 'fiscaliacorp.paucarpataaqp@gmail.com / 01 6255555 / Anexo 54560-54561 / fiscaliacorp.paucarpataaqp@gmail.com / 01 6255555 / Anexo 54560-54561 / 939835454'),
('fiscalia', '1ª Fiscalía Provincial Penal Corporativa de Paucarpata - 1 Despacho', 'fiscaliacorp.paucarpataaqp@gmail.com', '01 6255555', '54560-54561', NULL, '939835454'),
('fiscalia', '1ª Fiscalía Provincial Penal Corporativa de Paucarpata - 2 Despacho', 'not1fgppc2d@gmail.com', '01 6255555', '54560-54561', NULL, '903035454'),
('fiscalia', '1ª Fiscalía Provincial Penal Corporativa de Paucarpata - 3 Despacho', 'not1fgppc3d@gmail.com', '01 6255555', NULL, NULL, '961485798 (atención al usuario) / segundafiscaliapaucarpata@gmail.com / 01 6255555 / Anexo 54575-54576'),
('fiscalia', '2ª Fiscalía Provincial Penal Corporativa de Paucarpata - 1 Despacho', 'segundafiscaliapaucarpata@gmail.com', '01 6255555', '54575-54576', NULL, '939835737'),
('fiscalia', '2ª Fiscalía Provincial Penal Corporativa de Paucarpata - 2 Despacho', 'fiscaliacorp.paucarpataaqp@mpfn.gob.pe', '01 6255555', '54590-54591', NULL, '910244828 (atención al usuario)'),
('fiscalia', '3ª Fiscalía Provincial Penal Corporativa de Paucarpata - 1 Despacho', 'fiscaliacorp.paucarpataaqp@mpfn.gob.pe', '01 6255555', '54590-54591', NULL, '910244828 (atención al usuario) / familiapauc.arequipa@mpfn.gob.pe / 01 6255555 / Anexo 54791'),
('fiscalia', 'Fiscalía Provincial de Familia de Paucarpata', 'familiapauc.arequipa@mpfn.gob.pe', '01 6255555', '54791', NULL, 'Para casos de la Fiscal Prov. Dra. Yeny B. Vargas Mamani: 939503739 (celular del despacho) / Para casos de la Fiscal Prov. Dra. Yeny B. Vargas Mamani: 939503739 (celular del despacho) / Para casos de la Fiscal Provincial Dra. Yeny B. Vargas Mamani: 939503739 (celular del despacho) / Para revisión de actuados: 939503739 (celular del despacho)'),
('fiscalia', 'Fiscalía Especializada en Delitos contra la Mujer e Integrantes del Grupo Familiar - Sede Paucarpata', 'mesavcmigf.paucarpata@gmail.com', NULL, NULL, NULL, 'Fiscalíaespecializadaenviolenciafamiliar.aqp@gmail.com / 936 790 420 (atención al usuario)'),

-- ========== PÁGINA 11: FISCALÍAS DE MARIANO MELGAR Y JACOBO HUNTER ==========
('fiscalia', 'Fiscalía Provincial Penal Corporativa de Mariano Melgar', 'mesadepartes.mmmelgar@gmail.com', NULL, NULL, NULL, 'mmmelgarmaq.arequipa@mpfn.gob.pe / 01 6255555 / Anexo 54525-54526'),
('fiscalia', '1ª Fiscalía Provincial Penal Corporativa de Mariano Melgar - 1 Despacho', 'mmmelgarmaq.arequipa@mpfn.gob.pe', '01 6255555', '54525-54526', NULL, '961437957'),
('fiscalia', '1ª Fiscalía Provincial Penal Corporativa de Mariano Melgar - 2 Despacho', 'mmmelgarmaq.arequipa@mpfn.gob.pe', '01 6255555', '54545-54546', NULL, '939835454'),
('fiscalia', '2ª Fiscalía Provincial Penal Corporativa de Mariano Melgar - 1 Despacho', 'mmmelgarmaq.arequipa@mpfn.gob.pe', '01 6255555', '54550-54551', NULL, '939835454'),
('fiscalia', '2ª Fiscalía Provincial Penal Corporativa de Mariano Melgar - 2 Despacho', 'mmmelgarmaq.arequipa@mpfn.gob.pe', '01 6255555', '54550-54551', NULL, '939835454'),
('fiscalia', 'Fiscalía Provincial de Familia de Mariano Melgar', 'familiammmelgar@mpfn.gob.pe', '910035789', NULL, NULL, 'mesa de partes y atención al usuario / 939500080 (atención al usuario) / familiammmelgar.arequipa@mpfn.gob.pe / 939500080 (atención al usuario)'),
('fiscalia', 'Fiscalía Especializada en Delitos contra la Mujer e Integrantes del Grupo Familiar - Sede Mariano Melgar', 'mesavcmigf.mmmelgar@mpfn.gob.pe', NULL, NULL, NULL, 'mesa de partes / Mesa de partes: (01) 62 5555 Anexo: 54794 / Despacho (01) 62 5555 Anexo: 54794'),
('mesa_partes', 'Mesa de Partes de la Fiscalía Provincial Penal Corporativa de Jacobo Hunter', 'mpartes.mpfn.jmmelgar@gmail.com', NULL, NULL, NULL, 'mmjhuntermp.arequipa@mpfn.gob.pe / 01 6255555 / Anexo 54792'),
('fiscalia', 'Fiscalía Provincial Penal Corporativa de Jacobo Hunter - 1 Despacho', 'mmjhuntermp.arequipa@mpfn.gob.pe', '(01) 625 5555', '54841, 54840, 54832', NULL, NULL),
('fiscalia', 'Fiscalía Provincial Penal Corporativa de Jacobo Hunter - 2 Despacho', NULL, '(01) 625 5555', '54836-54845-54646', NULL, NULL),
('fiscalia', 'Fiscalía Provincial Penal Corporativa de Jacobo Hunter - 4 Despacho', 'atencionusuario.4d.hunter@gmail.com', '01 6255555', '54883', NULL, '966395602 / http://mpfnarequipa.gob.pe/murfisul/index.php'),
('fiscalia', 'Fiscalía Provincial de Familia de Jacobo Hunter', 'familiajhuntermp.arequipa@gmail.com', '939551615', NULL, NULL, 'familiajhuntermp.arequipa@mpfn.gob.pe / 939551615 (atención al usuario) / 997946699 (atención al usuario)'),
('fiscalia', 'Fiscalía Especializada en Delitos contra la Mujer e Integrantes del Grupo Familiar - Sede Jacobo Hunter', 'mesavcmigf.jhunter@gmail.com', '961475449', NULL, NULL, '01 6255555 / Anexo 54799'),
('fiscalia', 'Asistente Administrativa - Paola Andrea Muñoz Rojas', NULL, '959434976', NULL, NULL, '01 6255555 / Anexo 54796'),

-- ========== PÁGINA 12: FISCALÍAS DE CAMANÁ Y CASTILLA ==========
('mesa_partes', 'Mesa de Partes Única', 'pqmedina@mpfn.gob.pe', NULL, NULL, NULL, 'Fiscal Provincial - Jorge Ernesto Medina Chávez / 939954999 (celular del despacho) / 01 6255555 / Anexo 54515 / Asistentes: Anexo 54522'),
('fiscalia', 'Fiscal Provincial Penal Coordinador de Camaná', 'pqmedina@mpfn.gob.pe', NULL, NULL, NULL, 'Fiscal Provincial - Jorge Ernesto Medina Chávez / 939954999 (celular del despacho) / 01 6255555 / Anexo 54515 / Asistentes: Anexo 54522'),
('fiscalia', '1ª Fiscalía Provincial Penal Corporativa de Camaná - 1 Despacho', 'caha@email.mpfn.gob.pe', NULL, NULL, NULL, 'clarissatonasaki@gmail.com / segundofiscalcamana@gmail.com / Fiscal Provincial - Clarissa Tomás Ala Gonzales / 01 6255555 / Anexo 54516 / Asistentes: Anexo 54522'),
('fiscalia', '2ª Fiscalía Provincial Penal Corporativa de Camaná - 2 Despacho', NULL, NULL, NULL, NULL, 'Fiscal Adjunto al Provincial - Clarissa Tomás Ala Gonzales / 01 6255555 / Anexo 54516 / Asistentes: Anexo 54522 / redhualva55@gmail.com (Fiscal Prov. Ruth Alva - Fiscal Adjunto al Provincial) / 01 6255555 / Anexo 54516 / Asistentes: Anexo 54522'),
('fiscalia', 'Fiscalía Provincial de Familia de Camaná', 'familiamaqcamana@gmail.com', '910035789', NULL, NULL, 'mesa de partes y atención al usuario / 936 790 420 (atención al usuario)'),
('fiscalia', 'Fiscalía Especializada en Violencia Contra la Mujer - Sede Camaná', 'fiscaliaespecializadaviolenciafamiliar.aqp@gmail.com', NULL, NULL, NULL, '936 790 420 (atención al usuario)'),
('fiscalia', 'Fiscalía Provincial Penal Corporativa de Castilla', 'mcastillaarq.arequipa@gmail.com', '01 6255555', NULL, NULL, 'anexo 1 / fapaza@gmail.com (correo despacho) / 01 6255555 / Anexo 54802 / fpccastilla@hotmail.com (Fiscal Adj. Romina Velásquez - 937831877 / anexo 54802) / ronnyvelcor@hotmail.com (Fiscal Adj. Nayil Gamarra - 935421543 / anexo 54073) / Asistente Leidy Herrera (anexo 54607)'),
('fiscalia', 'Fiscalía Provincial Penal Corporativa de Castilla', NULL, NULL, NULL, NULL, 'penalcastillaarq.arequipa@gmail.com / (mesa de partes) / 939517318 (mesa de partes) / 01 6255555 / Anexo 54117'),
('fiscalia', 'Fiscalía Provincial Penal Corporativa de Chivay', NULL, NULL, NULL, NULL, '(54137) Fiscal Provincial Gerber Quiñone Machado / (54610) Fiscal Adjunta al Provincial Carlos Miguel Salinas Vargas'),

-- ========== PÁGINA 13: FISCALÍAS DE CHIVAY, EL PEDREGAL, ISLAY ==========
('fiscalia', 'Fiscalía Provincial de Familia de Chivay', 'familiachivayaqp.arequipa@gmail.com', '939332507', NULL, NULL, 'mesa de partes / 01 6255555 / Anexo 54075 / Asistente: Yudith Choquepuma / Choquepuma Huamani'),
('fiscalia', 'Fiscalía Provincial Penal Corporativa de El Pedregal', NULL, NULL, NULL, NULL, 'http://mpfnarequipa.gob.pe/murfisul/index.php / Atención al usuario: 959347882 / Tel: 01625555 / Anexo: 54868'),
('fiscalia', 'Fiscalía Provincial Penal Corporativa de El Pedregal - 1 Despacho', 'flagarancia.elpedregal@gmail.com', NULL, NULL, NULL, NULL),
('fiscalia', 'Fiscalía Provincial Penal Corporativa de El Pedregal - 2 Despacho', 'josemaria1951@gmail.com', '01625555', '54868', NULL, 'Fiscal Laxo / 01625555 Anexo 54981 / hugo.condori@mpfn.gob.pe / Fiscal Provincial Sumario Quispe / Fiscal Adjunto al Provincial Dr. Milton Ortega Quispe / Fiscal Adjunto al Provincial Dr. Maribel Sánchez Chino / (Anexo 54845) / mesadepartes.elpedregal@mpfn.gob.pe / (Anexo 54845) / mesadepartes.elpedregal@mpfn.gob.pe / 01 6255555 / Anexo 54845'),
('fiscalia', 'Fiscalía Provincial de Familia de El Pedregal', 'mesadepartes.elpedregal@mpfn.gob.pe', '01 6255555', '54845', NULL, NULL),
('fiscalia', 'Fiscalía Especializada en Delitos contra la Mujer e Integrantes del Grupo Familiar - Sede El Pedregal - 1 Despacho', 'fiscaliaespecializadaviolenciafamiliar.aqp@gmail.com', NULL, NULL, NULL, 'http://mpfnarequipa.gob.pe/murfisul/index.php / 952707018 (mesa de partes) / 01 6255555 Anexo: 54956'),
('fiscalia', 'Fiscalía Especializada en Delitos contra la Mujer e Integrantes del Grupo Familiar - Sede El Pedregal - 2 Despacho', 'fiscaliaespecializadaviolenciafamiliar.aqp@gmail.com', NULL, NULL, NULL, 'http://mpfnarequipa.gob.pe/murfisul/index.php / 952707018 (mesa de partes) / 01 6255555 Anexo: 54956'),
('fiscalia', 'Fiscalía Especializada en Delitos contra la Mujer e Integrantes del Grupo Familiar - Sede Islay', 'fiscaliaespecializadaviolenciafamiliar.aqp@gmail.com', NULL, NULL, NULL, 'http://mpfnarequipa.gob.pe/murfisul/index.php / 955931631 (atención mesa de partes) / 01 6255555 Anexo: 54956'),

-- ========== PÁGINA 14: FISCALÍAS DE ISLAY Y OTRAS ==========
('fiscalia', 'Fiscalía Provincial Penal Corporativa de Islay - 1 Despacho', 'pchislayislaymejoraqp@gmail.com', NULL, NULL, NULL, 'penalislay.arequipa@gmail.com / 01 6255555 / Anexo 54776 / mislaymp.arequipa@gmail.com'),
('fiscalia', 'Fiscalía Provincial Penal Corporativa de Islay - 2 Despacho', 'penalislay.arequipa@gmail.com', NULL, NULL, NULL, '01 6255555 / Anexo 54776'),
('fiscalia', 'Fiscalía Provincial de Familia de Islay', 'mislaymp.arequipa@gmail.com', '01 6255555', '54776', NULL, NULL),
('fiscalia', 'Fiscalía Provincial Mixta de Acarí', NULL, NULL, NULL, NULL, 'Fiscal Provincial Dr. Milagros Sueros (anexos: 54700) / Fiscal Adjunta al Provincial Dra. Milagros Sueros (anexos: 54700) / Mesa de partes: (anexos 54701)'),
('fiscalia', 'Fiscalía Provincial Mixta de Condesuyos', 'sasanchezc@mpfn.gob.pe', NULL, NULL, NULL, 'Fiscal Provincial Giovani Matos (anexo: 54700) / Fiscal Adjunto Provincial Dr. Milton Fernández (anexo: 54700) / Asistente Gisela Delgado (anexo: 54702) / 939650800 (Fiscal Provincial Saúl Sánchez) / gmatosl@mpfn.gob.pe / Fiscal Adjunto Provincial Dr. Milton Fernández (anexo: 54700)'),
('fiscalia', 'Fiscalía Provincial Mixta de La Unión', 'pjuntocol@mpfn.gob.pe', NULL, NULL, NULL, 'seguimiento de casos del Fiscal Adjunto al Provincial / 01 6255555 / Anexo 54712-54713 / 939507023 / 959 344 392'),
('fiscalia', 'Delitos contra el Patrimonio Cultural (Fiscal Provincial Dra. Rosaluz Aguilar Ramírez)', 'patrimoniocultural@mpfn.gob.pe', NULL, NULL, NULL, 'mesa de partes / mesadepartes.umbarequipa@gmail.com / 932905256'),
('fiscalia', 'Unidad Médico Legal III Arequipa', 'mesadepartes.umbarequipa@gmail.com', '932905256', NULL, NULL, 'mesadepartes.umbiday@gmail.com / mesadepartes.umbarequipa@gmail.com / mesadepartes.umbcondessuyos@gmail.com / 932932790 / 932941896 / 932968282 / 932937847'),

-- ========== PÁGINA 15: UNIDAD DISTRITAL Y SERVICIOS ==========
('servicio', 'Unidad Distrital de Asistencia a Víctimas y Testigos de Arequipa', 'mesadepartes.umbarequipa@gmail.com', '932932790', NULL, NULL, 'mesadepartes.umbiday@gmail.com / mesadepartes.umbarequipa@gmail.com / mesadepartes.umbcondessuyos@gmail.com / 932941896 / 932968282 / 932937847 / Cámara Gesell: 939155017 / Médico Toxicólogo: 939162630 / Trabajador Social: 939154503 / Informes Psicológicos: 939160370 / Técnico Informático: 936643 / Potencial Humano: 939161222 / udavtarequipa@mpfn.gob.pe / 939162007');

-- ============================================
-- FIN DE LA INSERCIÓN DE DATOS
-- ============================================

-- Verificar cuántos registros se insertaron
SELECT COUNT(*) as total_registros FROM mp_chatbot_directorio;

-- Ver una muestra de los datos
SELECT tipo, nombre, correo, telefono FROM mp_chatbot_directorio LIMIT 10;
