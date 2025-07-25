-- =====================================================
-- PRÁCTICA 37: SISTEMA DE E-LEARNING
-- Script de creación de tablas para MariaDB
-- Fecha: 2025-07-25
-- =====================================================

-- Usar la base de datos existente
USE ahj_ende_pinedah;

-- =====================================================
-- TABLA: curso
-- Almacena los cursos del sistema de e-learning
-- =====================================================
DROP TABLE IF EXISTS `curso`;
CREATE TABLE `curso` (
  `id_curso` int(11) NOT NULL AUTO_INCREMENT,
  `nom_curso` varchar(200) NOT NULL COMMENT 'Nombre del curso',
  `des_curso` text DEFAULT NULL COMMENT 'Descripción del curso',
  `id_eje_creador` int(10) unsigned NOT NULL COMMENT 'Ejecutivo que creó el curso',
  `fec_creacion_curso` datetime NOT NULL DEFAULT current_timestamp() COMMENT 'Fecha de creación',
  `fec_actualizacion_curso` datetime DEFAULT NULL ON UPDATE current_timestamp() COMMENT 'Fecha de última actualización',
  `eli_curso` tinyint(1) NOT NULL DEFAULT 1 COMMENT 'Borrado lógico (1=activo, 0=eliminado)',
  PRIMARY KEY (`id_curso`),
  KEY `idx_curso_creador` (`id_eje_creador`),
  KEY `idx_curso_eliminado` (`eli_curso`),
  CONSTRAINT `fk_curso_ejecutivo` FOREIGN KEY (`id_eje_creador`) REFERENCES `ejecutivo` (`id_eje`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Cursos del sistema de e-learning';

-- =====================================================
-- TABLA: clase
-- Almacena las clases de cada curso (estructura intermedia)
-- =====================================================
DROP TABLE IF EXISTS `clase`;
CREATE TABLE `clase` (
  `id_clase` int(11) NOT NULL AUTO_INCREMENT,
  `id_curso` int(11) NOT NULL COMMENT 'Curso al que pertenece la clase',
  `tit_clase` varchar(200) NOT NULL COMMENT 'Título de la clase',
  `des_clase` text DEFAULT NULL COMMENT 'Descripción de la clase',
  `ord_clase` int(11) NOT NULL DEFAULT 1 COMMENT 'Orden de la clase dentro del curso',
  `id_eje_creador` int(10) unsigned NOT NULL COMMENT 'Ejecutivo que creó la clase',
  `fec_creacion_clase` datetime NOT NULL DEFAULT current_timestamp() COMMENT 'Fecha de creación',
  `fec_actualizacion_clase` datetime DEFAULT NULL ON UPDATE current_timestamp() COMMENT 'Fecha de última actualización',
  `eli_clase` tinyint(1) NOT NULL DEFAULT 1 COMMENT 'Borrado lógico (1=activo, 0=eliminado)',
  PRIMARY KEY (`id_clase`),
  KEY `idx_clase_curso` (`id_curso`),
  KEY `idx_clase_creador` (`id_eje_creador`),
  KEY `idx_clase_orden` (`ord_clase`),
  KEY `idx_clase_eliminado` (`eli_clase`),
  CONSTRAINT `fk_clase_curso` FOREIGN KEY (`id_curso`) REFERENCES `curso` (`id_curso`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_clase_ejecutivo` FOREIGN KEY (`id_eje_creador`) REFERENCES `ejecutivo` (`id_eje`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Clases de los cursos';

-- =====================================================
-- TABLA: contenido
-- Almacena el contenido multimedia de las clases
-- =====================================================
DROP TABLE IF EXISTS `contenido`;
CREATE TABLE `contenido` (
  `id_contenido` int(11) NOT NULL AUTO_INCREMENT,
  `id_clase` int(11) NOT NULL COMMENT 'Clase a la que pertenece el contenido',
  `tit_contenido` varchar(200) NOT NULL COMMENT 'Título del contenido',
  `tip_contenido` enum('video_archivo','video_youtube','audio','pdf','imagen') NOT NULL COMMENT 'Tipo de contenido',
  `arc_contenido` varchar(255) DEFAULT NULL COMMENT 'Nombre del archivo (para tipos archivo)',
  `url_contenido` text DEFAULT NULL COMMENT 'URL del contenido (para Externos)',
  `des_contenido` text DEFAULT NULL COMMENT 'Descripción del contenido',
  `ord_contenido` int(11) NOT NULL DEFAULT 1 COMMENT 'Orden del contenido dentro de la clase',
  `id_eje_creador` int(10) unsigned NOT NULL COMMENT 'Ejecutivo que creó el contenido',
  `fec_creacion_contenido` datetime NOT NULL DEFAULT current_timestamp() COMMENT 'Fecha de creación',
  `fec_actualizacion_contenido` datetime DEFAULT NULL ON UPDATE current_timestamp() COMMENT 'Fecha de última actualización',
  `eli_contenido` tinyint(1) NOT NULL DEFAULT 1 COMMENT 'Borrado lógico (1=activo, 0=eliminado)',
  PRIMARY KEY (`id_contenido`),
  KEY `idx_contenido_clase` (`id_clase`),
  KEY `idx_contenido_tipo` (`tip_contenido`),
  KEY `idx_contenido_creador` (`id_eje_creador`),
  KEY `idx_contenido_orden` (`ord_contenido`),
  KEY `idx_contenido_eliminado` (`eli_contenido`),
  CONSTRAINT `fk_contenido_clase` FOREIGN KEY (`id_clase`) REFERENCES `clase` (`id_clase`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_contenido_ejecutivo` FOREIGN KEY (`id_eje_creador`) REFERENCES `ejecutivo` (`id_eje`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Contenido multimedia de las clases';

-- =====================================================
-- TABLA: comentario_elearning
-- Almacena comentarios recursivos tipo Facebook
-- =====================================================
DROP TABLE IF EXISTS `comentario_elearning`;
CREATE TABLE `comentario_elearning` (
  `id_comentario` int(11) NOT NULL AUTO_INCREMENT,
  `id_contenido` int(11) NOT NULL COMMENT 'Contenido al que pertenece el comentario',
  `tex_comentario` text NOT NULL COMMENT 'Texto del comentario',
  `id_eje_comentario` int(10) unsigned NOT NULL COMMENT 'Ejecutivo que hizo el comentario',
  `id_comentario_padre` int(11) DEFAULT NULL COMMENT 'Comentario padre (para respuestas recursivas)',
  `fec_comentario` datetime NOT NULL DEFAULT current_timestamp() COMMENT 'Fecha del comentario',
  `fec_actualizacion_comentario` datetime DEFAULT NULL ON UPDATE current_timestamp() COMMENT 'Fecha de última actualización',
  `eli_comentario` tinyint(1) NOT NULL DEFAULT 1 COMMENT 'Borrado lógico (1=activo, 0=eliminado)',
  PRIMARY KEY (`id_comentario`),
  KEY `idx_comentario_contenido` (`id_contenido`),
  KEY `idx_comentario_ejecutivo` (`id_eje_comentario`),
  KEY `idx_comentario_padre` (`id_comentario_padre`),
  KEY `idx_comentario_fecha` (`fec_comentario`),
  KEY `idx_comentario_eliminado` (`eli_comentario`),
  CONSTRAINT `fk_comentario_contenido` FOREIGN KEY (`id_contenido`) REFERENCES `contenido` (`id_contenido`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_comentario_ejecutivo` FOREIGN KEY (`id_eje_comentario`) REFERENCES `ejecutivo` (`id_eje`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_comentario_padre` FOREIGN KEY (`id_comentario_padre`) REFERENCES `comentario_elearning` (`id_comentario`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Comentarios recursivos del sistema e-learning';

-- =====================================================
-- TABLA: progreso_ejecutivo
-- Opcional: Rastrea el progreso de cada ejecutivo en los cursos
-- =====================================================
DROP TABLE IF EXISTS `progreso_ejecutivo`;
CREATE TABLE `progreso_ejecutivo` (
  `id_progreso` int(11) NOT NULL AUTO_INCREMENT,
  `id_ejecutivo` int(10) unsigned NOT NULL COMMENT 'Ejecutivo estudiante',
  `id_curso` int(11) NOT NULL COMMENT 'Curso en progreso',
  `id_clase_actual` int(11) DEFAULT NULL COMMENT 'Última clase vista',
  `porcentaje_completado` decimal(5,2) NOT NULL DEFAULT 0.00 COMMENT 'Porcentaje de progreso (0.00-100.00)',
  `fec_inicio` datetime NOT NULL DEFAULT current_timestamp() COMMENT 'Fecha de inicio del curso',
  `fec_ultima_actividad` datetime DEFAULT NULL ON UPDATE current_timestamp() COMMENT 'Última actividad en el curso',
  `fec_completado` datetime DEFAULT NULL COMMENT 'Fecha de finalización del curso',
  `eli_progreso` tinyint(1) NOT NULL DEFAULT 1 COMMENT 'Borrado lógico (1=activo, 0=eliminado)',
  PRIMARY KEY (`id_progreso`),
  UNIQUE KEY `idx_progreso_unique` (`id_ejecutivo`, `id_curso`),
  KEY `idx_progreso_ejecutivo` (`id_ejecutivo`),
  KEY `idx_progreso_curso` (`id_curso`),
  KEY `idx_progreso_clase_actual` (`id_clase_actual`),
  KEY `idx_progreso_completado` (`porcentaje_completado`),
  KEY `idx_progreso_eliminado` (`eli_progreso`),
  CONSTRAINT `fk_progreso_ejecutivo` FOREIGN KEY (`id_ejecutivo`) REFERENCES `ejecutivo` (`id_eje`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_progreso_curso` FOREIGN KEY (`id_curso`) REFERENCES `curso` (`id_curso`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_progreso_clase` FOREIGN KEY (`id_clase_actual`) REFERENCES `clase` (`id_clase`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Progreso de ejecutivos en los cursos';

-- =====================================================
-- DATOS DE PRUEBA
-- =====================================================

-- Insertar cursos de ejemplo
INSERT INTO `curso` (`nom_curso`, `des_curso`, `id_eje_creador`) VALUES
('Introducción al Sistema', 'Curso básico para nuevos ejecutivos del sistema SICAM', 1),
('Gestión de Citas', 'Manejo avanzado del sistema de citas y seguimiento', 2),
('Reportes y Análisis', 'Generación de reportes y análisis de datos del sistema', 4);

-- Insertar clases de ejemplo
INSERT INTO `clase` (`id_curso`, `tit_clase`, `des_clase`, `ord_clase`, `id_eje_creador`) VALUES
-- Curso 1: Introducción al Sistema
(1, 'Bienvenida', 'Introducción general al sistema y sus funcionalidades', 1, 1),
(1, 'Navegación Básica', 'Aprende a navegar por las diferentes secciones', 2, 1),
(1, 'Funciones Avanzadas', 'Características avanzadas del sistema', 3, 1),

-- Curso 2: Gestión de Citas
(2, 'Crear Citas', 'Cómo crear y programar nuevas citas', 1, 2),
(2, 'Modificar Citas', 'Edición y actualización de citas existentes', 2, 2),
(2, 'Reportes de Citas', 'Generación de reportes sobre citas', 3, 2),
(2, 'Filtros Avanzados', 'Uso de filtros para búsquedas específicas', 4, 2),
(2, 'Gestión de Estados', 'Manejo de estados y seguimiento de citas', 5, 2),

-- Curso 3: Reportes y Análisis
(3, 'Reportes Básicos', 'Introducción a la generación de reportes', 1, 4),
(3, 'Análisis de Datos', 'Interpretación y análisis de la información', 2, 4),
(3, 'Dashboards', 'Creación y personalización de dashboards', 3, 4),
(3, 'Exportación', 'Exportar datos a diferentes formatos', 4, 4);

-- Insertar contenido de ejemplo
INSERT INTO `contenido` (`id_clase`, `tit_contenido`, `tip_contenido`, `arc_contenido`, `url_contenido`, `des_contenido`, `ord_contenido`, `id_eje_creador`) VALUES
-- Clase 1: Bienvenida
(1, 'Video Introductorio', 'video_youtube', NULL, 'https://www.youtube.com/embed/xAWDqdpOlu8', 'Introducción general al sistema de gestión', 1, 1),
(1, 'Manual de Usuario', 'pdf', 'manual_usuario.pdf', NULL, 'Documentación completa del sistema', 2, 1),

-- Clase 2: Navegación Básica
(2, 'Guía de Navegación', 'pdf', 'guia_navegacion.pdf', NULL, 'Manual paso a paso para navegar en el sistema', 1, 1),
(2, 'Tutorial de Menús', 'video_youtube', NULL, 'https://www.youtube.com/embed/sample1', 'Explicación de los menús principales', 2, 1),

-- Clase 3: Funciones Avanzadas
(3, 'Configuraciones Avanzadas', 'pdf', 'config_avanzadas.pdf', NULL, 'Guía de configuraciones especializadas', 1, 1),

-- Clase 4: Crear Citas
(4, 'Video Tutorial - Crear Citas', 'video_youtube', NULL, 'https://www.youtube.com/embed/sample2', 'Proceso completo de creación de citas', 1, 2),
(4, 'Formulario de Citas', 'imagen', 'formulario_citas.png', NULL, 'Vista del formulario de creación', 2, 2);

-- Insertar comentarios de ejemplo
INSERT INTO `comentario_elearning` (`id_contenido`, `tex_comentario`, `id_eje_comentario`, `id_comentario_padre`) VALUES
-- Comentarios principales
(1, 'Excelente explicación en el video. Me ayudó mucho a entender el proceso.', 2, NULL),
(2, 'El manual está muy completo, gracias por la documentación.', 4, NULL),
(1, 'Muy útil para los nuevos ejecutivos.', 6, NULL),

-- Respuestas a comentarios (estructura recursiva)
(1, 'Me alegra que te haya sido útil. Si tienes más dudas, no dudes en preguntar.', 1, 1),
(1, 'Totalmente de acuerdo, es una excelente introducción.', 9, 3),
(2, 'Si necesitas ayuda con alguna sección específica, avísame.', 1, 2);

-- =====================================================
-- TRIGGERS PARA MANTENIMIENTO AUTOMÁTICO
-- =====================================================

-- Trigger para actualizar progreso automáticamente
DELIMITER ;;
CREATE TRIGGER tr_actualizar_progreso_clase
AFTER INSERT ON comentario_elearning
FOR EACH ROW
BEGIN
    DECLARE v_id_curso INT;
    DECLARE v_total_clases INT;
    DECLARE v_clases_vistas INT;
    DECLARE v_nuevo_porcentaje DECIMAL(5,2);
    
    -- Obtener el curso de la clase del contenido comentado
    SELECT c.id_curso INTO v_id_curso
    FROM contenido con 
    JOIN clase cl ON con.id_clase = cl.id_clase 
    WHERE con.id_contenido = NEW.id_contenido;
    
    -- Contar total de clases del curso
    SELECT COUNT(*) INTO v_total_clases
    FROM clase 
    WHERE id_curso = v_id_curso AND eli_clase = 1;
    
    -- Simular que comentar = ver contenido (contabilizar progreso)
    -- Esto se puede mejorar con una tabla específica de visualizaciones
    
    -- Insertar o actualizar progreso
    INSERT INTO progreso_ejecutivo (id_ejecutivo, id_curso, fec_ultima_actividad)
    VALUES (NEW.id_eje_comentario, v_id_curso, NOW())
    ON DUPLICATE KEY UPDATE 
        fec_ultima_actividad = NOW();
END;;
DELIMITER ;

-- =====================================================
-- VISTAS ÚTILES PARA CONSULTAS
-- =====================================================

-- Vista para obtener cursos con información del creador
CREATE OR REPLACE VIEW vista_cursos_completa AS
SELECT 
    c.id_curso,
    c.nom_curso,
    c.des_curso,
    c.fec_creacion_curso,
    c.fec_actualizacion_curso,
    e.nom_eje as creador_curso,
    e.tipo as tipo_creador,
    (SELECT COUNT(*) FROM clase cl WHERE cl.id_curso = c.id_curso AND cl.eli_clase = 1) as total_clases,
    (SELECT COUNT(*) FROM contenido con 
     JOIN clase cl ON con.id_clase = cl.id_clase 
     WHERE cl.id_curso = c.id_curso AND con.eli_contenido = 1 AND cl.eli_clase = 1) as total_contenidos
FROM curso c
JOIN ejecutivo e ON c.id_eje_creador = e.id_eje
WHERE c.eli_curso = 1
ORDER BY c.fec_creacion_curso DESC;

-- Vista para comentarios con estructura jerárquica
CREATE OR REPLACE VIEW vista_comentarios_jerarquicos AS
SELECT 
    ce.id_comentario,
    ce.id_contenido,
    ce.tex_comentario,
    ce.fec_comentario,
    ce.id_comentario_padre,
    e.nom_eje as autor_comentario,
    e.tipo as tipo_autor,
    con.tit_contenido,
    cl.tit_clase,
    cur.nom_curso,
    CASE 
        WHEN ce.id_comentario_padre IS NULL THEN 0 
        ELSE 1 
    END as es_respuesta
FROM comentario_elearning ce
JOIN ejecutivo e ON ce.id_eje_comentario = e.id_eje
JOIN contenido con ON ce.id_contenido = con.id_contenido
JOIN clase cl ON con.id_clase = cl.id_clase
JOIN curso cur ON cl.id_curso = cur.id_curso
WHERE ce.eli_comentario = 1
ORDER BY 
    ce.id_contenido,
    COALESCE(ce.id_comentario_padre, ce.id_comentario),
    ce.fec_comentario;

-- =====================================================
-- PROCEDIMIENTOS ALMACENADOS ÚTILES
-- =====================================================

-- Procedimiento para obtener el progreso de un ejecutivo
DELIMITER ;;
CREATE PROCEDURE sp_obtener_progreso_ejecutivo(IN p_id_ejecutivo INT)
BEGIN
    SELECT 
        pe.id_progreso,
        c.nom_curso,
        pe.porcentaje_completado,
        pe.fec_inicio,
        pe.fec_ultima_actividad,
        pe.fec_completado,
        cl.tit_clase as clase_actual
    FROM progreso_ejecutivo pe
    JOIN curso c ON pe.id_curso = c.id_curso
    LEFT JOIN clase cl ON pe.id_clase_actual = cl.id_clase
    WHERE pe.id_ejecutivo = p_id_ejecutivo 
    AND pe.eli_progreso = 1
    ORDER BY pe.fec_ultima_actividad DESC;
END;;
DELIMITER ;

-- =====================================================
-- ÍNDICES ADICIONALES PARA OPTIMIZACIÓN
-- =====================================================

-- Índices compuestos para consultas frecuentes
CREATE INDEX idx_contenido_clase_tipo ON contenido(id_clase, tip_contenido);
CREATE INDEX idx_comentario_contenido_fecha ON comentario_elearning(id_contenido, fec_comentario);
CREATE INDEX idx_clase_curso_orden ON clase(id_curso, ord_clase);

-- =====================================================
-- PERMISOS Y COMENTARIOS FINALES
-- =====================================================

-- Agregar comentarios a nivel de base de datos
ALTER TABLE curso COMMENT = 'P37: Cursos del sistema de e-learning';
ALTER TABLE clase COMMENT = 'P37: Clases organizadas por curso';
ALTER TABLE contenido COMMENT = 'P37: Contenido multimedia (videos, PDFs, etc.)';
ALTER TABLE comentario_elearning COMMENT = 'P37: Sistema de comentarios recursivos tipo Facebook';
ALTER TABLE progreso_ejecutivo COMMENT = 'P37: Seguimiento del progreso de ejecutivos en cursos';

-- =====================================================
-- SCRIPT COMPLETADO EXITOSAMENTE
-- =====================================================
-- Este script crea toda la estructura necesaria para el
-- sistema de e-learning de la Práctica 37, incluyendo:
-- 
-- ✅ Tablas principales (curso, clase, contenido)
-- ✅ Sistema de comentarios recursivos
-- ✅ Seguimiento de progreso
-- ✅ Relaciones con tabla ejecutivo existente
-- ✅ Datos de prueba
-- ✅ Triggers automáticos
-- ✅ Vistas para consultas
-- ✅ Procedimientos almacenados
-- ✅ Índices optimizados
-- 
-- Ejecutar este script en MariaDB para crear la
-- estructura completa del sistema de e-learning.
-- =====================================================
