<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Práctica 37 - Sistema de E-Learning</title>
    
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    
    <!-- Font Awesome para iconos -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <!-- PDF.js -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
    
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <style>
        /* Estilos principales siguiendo el patrón de index.php */
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .main-container {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        
        .header-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            border-radius: 8px 8px 0 0;
        }
        
        .header-section h3 {
            margin: 0;
            font-weight: 300;
        }
        
        .header-section small {
            opacity: 0.9;
        }
        
        /* Navegación de cursos */
        .courses-nav {
            background: white;
            border-bottom: 1px solid #e9ecef;
            padding: 15px 20px;
        }
        
        .course-item {
            cursor: pointer;
            padding: 10px 15px;
            margin-right: 10px;
            border-radius: 6px;
            border: 1px solid #dee2e6;
            background: white;
            transition: all 0.3s ease;
        }
        
        .course-item:hover {
            background: #f8f9fa;
            border-color: #007bff;
        }
        
        .course-item.active {
            background: #007bff;
            color: white;
            border-color: #007bff;
        }
        
        /* Stepper para clases */
        .stepper-container {
            padding: 20px;
            background: white;
        }
        
        .stepper {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 30px;
            overflow-x: auto;
            padding: 10px 0;
        }
        
        .step {
            display: flex;
            flex-direction: column;
            align-items: center;
            min-width: 120px;
            cursor: pointer;
            position: relative;
        }
        
        .step-circle {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #e9ecef;
            border: 2px solid #dee2e6;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 8px;
            transition: all 0.3s ease;
            font-weight: bold;
        }
        
        .step.active .step-circle {
            background: #007bff;
            border-color: #007bff;
            color: white;
        }
        
        .step.completed .step-circle {
            background: #28a745;
            border-color: #28a745;
            color: white;
        }
        
        .step-title {
            font-size: 12px;
            text-align: center;
            max-width: 100px;
            word-wrap: break-word;
        }
        
        .step-connector {
            flex: 1;
            height: 2px;
            background: #dee2e6;
            margin: 0 10px;
            position: relative;
            top: -15px;
        }
        
        .step.completed + .step-connector {
            background: #28a745;
        }
        
        /* Contenido de clase */
        .class-content {
            background: white;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        
        .class-header {
            padding: 20px;
            border-bottom: 1px solid #e9ecef;
        }
        
        .class-title {
            font-size: 24px;
            margin-bottom: 15px;
            color: #333;
        }
        
        .class-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            color: #6c757d;
            font-size: 14px;
        }
        
        .meta-item {
            display: flex;
            align-items: center;
            gap: 5px;
        }
        
        /* Área de contenido multimedia */
        .content-area {
            padding: 20px;
        }
        
        .content-item {
            border: 1px solid #e9ecef;
            border-radius: 8px;
            margin-bottom: 20px;
            overflow: hidden;
        }
        
        .content-header {
            background: #f8f9fa;
            padding: 15px;
            border-bottom: 1px solid #e9ecef;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .content-type-badge {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: bold;
        }
        
        .content-type-video {
            background: #e3f2fd;
            color: #1976d2;
        }
        
        .content-type-audio {
            background: #f3e5f5;
            color: #7b1fa2;
        }
        
        .content-type-pdf {
            background: #ffebee;
            color: #c62828;
        }
        
        .content-type-image {
            background: #e8f5e8;
            color: #2e7d32;
        }
        
        .content-body {
            padding: 20px;
        }
        
        /* Media players */
        .video-player {
            width: 100%;
            max-width: 800px;
            margin: 0 auto;
        }
        
        .audio-player {
            width: 100%;
            margin: 10px 0;
        }
        
        .pdf-viewer {
            width: 100%;
            height: 600px;
            border: none;
        }
        
        .image-viewer {
            max-width: 100%;
            height: auto;
            border-radius: 4px;
        }
        
        /* Descripción y descarga */
        .content-description {
            margin: 15px 0;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 6px;
            border-left: 4px solid #007bff;
        }
        
        .download-section {
            text-align: center;
            margin-top: 15px;
        }
        
        /* Sección de comentarios */
        .comments-section {
            background: white;
            border-radius: 8px;
            padding: 20px;
            margin-top: 20px;
        }
        
        .comments-header {
            border-bottom: 1px solid #e9ecef;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        
        .comment-form {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 6px;
            margin-bottom: 20px;
        }
        
        .comment-item {
            border: 1px solid #e9ecef;
            border-radius: 6px;
            padding: 15px;
            margin-bottom: 15px;
            position: relative;
        }
        
        .comment-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }
        
        .comment-author {
            font-weight: bold;
            color: #007bff;
        }
        
        .comment-date {
            color: #6c757d;
            font-size: 12px;
        }
        
        .comment-content {
            margin-bottom: 10px;
        }
        
        .comment-actions {
            display: flex;
            gap: 10px;
        }
        
        .reply-section {
            margin-left: 30px;
            margin-top: 15px;
            padding-left: 15px;
            border-left: 2px solid #e9ecef;
        }
        
        /* Botones de administración */
        .admin-panel {
            background: white;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
        }
        
        .admin-panel h5 {
            margin-bottom: 15px;
            color: #333;
        }
        
        .btn-admin {
            margin-right: 10px;
            margin-bottom: 10px;
        }
        
        /* Formularios modales */
        .modal-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        
        .modal-header .close {
            color: white;
            opacity: 0.8;
        }
        
        .modal-header .close:hover {
            opacity: 1;
        }
        
        .form-control:focus {
            border-color: #007bff;
            box-shadow: 0 0 0 0.2rem rgba(0,123,255,.25);
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .stepper {
                flex-direction: column;
                align-items: stretch;
            }
            
            .step {
                margin-bottom: 10px;
            }
            
            .step-connector {
                display: none;
            }
            
            .class-meta {
                flex-direction: column;
                gap: 10px;
            }
        }
        
        /* Indicadores de estado */
        .loading-indicator {
            text-align: center;
            padding: 40px;
            color: #6c757d;
        }
        
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #6c757d;
        }
        
        .empty-state i {
            font-size: 48px;
            margin-bottom: 20px;
            opacity: 0.5;
        }
    </style>
</head>
<body>
    <div class="container-fluid mt-4">
        <!-- Header Principal -->
        <div class="main-container">
            <div class="header-section">
                <h3><i class="fas fa-graduation-cap mr-2"></i>Sistema de E-Learning</h3>
            </div>
            
            <!-- Navegación de cursos -->
            <div class="courses-nav">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="mb-0">Cursos Disponibles</h6>
                </div>
                <div id="courses-list" class="d-flex flex-wrap">
                    <!-- Los cursos se cargarán aquí dinámicamente -->
                </div>
            </div>
        </div>
        
        <!-- Contenido principal del curso -->
        <div class="main-container" id="course-content" style="display: none;">
            <!-- Contenido de la clase actual -->
            <div id="class-content-area">
                <!-- El contenido se cargará aquí -->
            </div>
        </div>
        
        <!-- Sección de comentarios -->
        <div class="comments-section main-container" id="comments-section" style="display: none;">
            <div class="comments-header">
                <h5><i class="fas fa-comments mr-2"></i>Comentarios y Discusión</h5>
            </div>
            
            <!-- Formulario para nuevo comentario -->
            <div class="comment-form">
                <div class="form-group">
                    <textarea class="form-control" id="nuevo-comentario" rows="3" placeholder="Escribe tu comentario o pregunta..."></textarea>
                </div>
                <button class="btn btn-primary" onclick="agregarComentario()">
                    <i class="fas fa-paper-plane mr-1"></i>Publicar Comentario
                </button>
            </div>
            
            <!-- Lista de comentarios -->
            <div id="comments-list">
                <!-- Los comentarios se cargarán aquí -->
            </div>
        </div>
    </div>

    <!-- Modal para crear/editar curso -->
    <div class="modal fade" id="modalCurso" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-graduation-cap mr-2"></i>
                        <span id="titulo-modal-curso">Crear Nuevo Curso</span>
                    </h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="form-curso">
                        <input type="hidden" id="curso-id" name="id">
                        <div class="form-group">
                            <label for="curso-nombre">Nombre del Curso</label>
                            <input type="text" class="form-control" id="curso-nombre" name="nombre" required>
                        </div>
                        <div class="form-group">
                            <label for="curso-descripcion">Descripción</label>
                            <textarea class="form-control" id="curso-descripcion" name="descripcion" rows="4"></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" onclick="guardarCurso()">Guardar Curso</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para crear/editar clase -->
    <div class="modal fade" id="modalClase" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-chalkboard mr-2"></i>
                        <span id="titulo-modal-clase">Crear Nueva Clase</span>
                    </h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="form-clase">
                        <input type="hidden" id="clase-id" name="id">
                        <div class="form-group">
                            <label for="clase-curso">Curso</label>
                            <select class="form-control" id="clase-curso" name="id_curso" required>
                                <option value="">Seleccionar curso...</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="clase-titulo">Título de la Clase</label>
                            <input type="text" class="form-control" id="clase-titulo" name="titulo" required>
                        </div>
                        <div class="form-group">
                            <label for="clase-descripcion">Descripción</label>
                            <textarea class="form-control" id="clase-descripcion" name="descripcion" rows="3"></textarea>
                        </div>
                        <div class="form-group">
                            <label for="clase-orden">Orden en el curso</label>
                            <input type="number" class="form-control" id="clase-orden" name="orden" min="1" required>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" onclick="guardarClase()">Guardar Clase</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para subir contenido -->
    <div class="modal fade" id="modalContenido" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-upload mr-2"></i>
                        <span id="titulo-modal-contenido">Subir Contenido</span>
                    </h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="form-contenido" enctype="multipart/form-data">
                        <input type="hidden" id="contenido-id" name="id">
                        <div class="form-group">
                            <label for="contenido-clase">Clase</label>
                            <select class="form-control" id="contenido-clase" name="id_clase" required>
                                <option value="">Seleccionar clase...</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="contenido-titulo">Título del Contenido</label>
                            <input type="text" class="form-control" id="contenido-titulo" name="titulo" required>
                        </div>
                        <div class="form-group">
                            <label for="contenido-tipo">Tipo de Contenido</label>
                            <select class="form-control" id="contenido-tipo" name="tipo" required onchange="mostrarCamposSegunTipo()">
                                <option value="">Seleccionar tipo...</option>
                                <option value="video_archivo">Video (MP4)</option>
                                <option value="video_youtube">Video (YouTube)</option>
                                <option value="audio">Audio (MP3)</option>
                                <option value="pdf">Documento PDF</option>
                                <option value="imagen">Imagen (JPG, PNG)</option>
                            </select>
                        </div>
                        <div class="form-group" id="campo-archivo" style="display: none;">
                            <label for="contenido-archivo">Archivo</label>
                            <input type="file" class="form-control-file" id="contenido-archivo" name="archivo" accept="">
                        </div>
                        <div class="form-group" id="campo-url" style="display: none;">
                            <label for="contenido-url">URL de YouTube</label>
                            <input type="url" class="form-control" id="contenido-url" name="url" placeholder="https://www.youtube.com/watch?v=...">
                        </div>
                        <div class="form-group">
                            <label for="contenido-descripcion">Descripción</label>
                            <textarea class="form-control" id="contenido-descripcion" name="descripcion" rows="3"></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" onclick="guardarContenido()">Subir Contenido</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Variables globales
        let cursoActual = null;
        let claseActual = null;
        let ejecutivoActual = 1; // Simular ejecutivo logueado

        // Cargar cursos al iniciar
        $(document).ready(function() {
            cargarCursos();
        });

        // Funciones principales
        function cargarCursos() {
            // Simular carga de cursos
            const cursosEjemplo = [
                {id: 1, nombre: "Introducción al Sistema", descripcion: "Curso básico para nuevos ejecutivos", total_clases: 3},
                {id: 2, nombre: "Gestión de Citas", descripcion: "Manejo avanzado del sistema de citas", total_clases: 5},
                {id: 3, nombre: "Reportes y Análisis", descripcion: "Generación de reportes y análisis de datos", total_clases: 4}
            ];
            mostrarCursos(cursosEjemplo);
        }

        function mostrarCursos(cursos) {
            const container = $('#courses-list');
            container.empty();
            
            if (cursos.length === 0) {
                container.html(`
                    <div class="empty-state w-100">
                        <i class="fas fa-graduation-cap"></i>
                        <h5>No hay cursos disponibles</h5>
                        <p>Crea el primer curso para comenzar</p>
                    </div>
                `);
                return;
            }
            
            cursos.forEach(curso => {
                const cursoItem = $(`
                    <div class="course-item" data-id="${curso.id}" onclick="seleccionarCurso(${curso.id})">
                        <h6 class="mb-2">${curso.nombre}</h6>
                        <small class="text-muted">${curso.total_clases} clases</small>
                        <p class="mb-0 mt-2" style="font-size: 13px;">${curso.descripcion}</p>
                    </div>
                `);
                container.append(cursoItem);
            });
        }

        function seleccionarCurso(idCurso) {
            cursoActual = idCurso;
            $('.course-item').removeClass('active');
            $(`.course-item[data-id="${idCurso}"]`).addClass('active');
            
            cargarClasesCurso(idCurso);
            $('#course-content').show();
        }

        function cargarClasesCurso(idCurso) {
            // Simular carga de clases
            const clasesEjemplo = [
                {id: 1, titulo: "Bienvenida", orden: 1, completada: true},
                {id: 2, titulo: "Navegación Básica", orden: 2, completada: false},
                {id: 3, titulo: "Funciones Avanzadas", orden: 3, completada: false}
            ];
            
            mostrarStepper(clasesEjemplo);
            if (clasesEjemplo.length > 0) {
                seleccionarClase(clasesEjemplo[0].id);
            }
        }

        function mostrarStepper(clases) {
            const stepper = $('#classes-stepper');
            stepper.empty();
            
            clases.forEach((clase, index) => {
                const isActive = index === 0; // Primera clase activa por defecto
                const stepHtml = `
                    <div class="step ${clase.completada ? 'completed' : ''} ${isActive ? 'active' : ''}" 
                         data-id="${clase.id}" onclick="seleccionarClase(${clase.id})">
                        <div class="step-circle">
                            ${clase.completada ? '<i class="fas fa-check"></i>' : clase.orden}
                        </div>
                        <div class="step-title">${clase.titulo}</div>
                    </div>
                `;
                stepper.append(stepHtml);
                
                if (index < clases.length - 1) {
                    stepper.append('<div class="step-connector"></div>');
                }
            });
        }

        function seleccionarClase(idClase) {
            claseActual = idClase;
            $('.step').removeClass('active');
            $(`.step[data-id="${idClase}"]`).addClass('active');
            
            cargarContenidoClase(idClase);
            $('#comments-section').show();
        }

        function cargarContenidoClase(idClase) {
            // Simular contenido de clase
            const contenidoEjemplo = {
                titulo: "Información del Curso",
                ejecutivo_curso: "Juan Pérez",
                ejecutivo_clase: "María González",
                fecha_creacion: "2025-01-15",
                contenidos: [
                    {
                        id: 1,
                        titulo: "Video Introductorio",
                        tipo: "video_youtube",
                        url: "https://www.youtube.com/watch?v=xAWDqdpOlu8&ab_channel=BillieEilish",
                        descripcion: "Introducción general al sistema de gestión",
                        ejecutivo: "Carlos Ruiz",
                        fecha: "2025-01-15"
                    },
                    {
                        id: 2,
                        titulo: "Manual de Usuario",
                        tipo: "pdf",
                        archivo: "manual_usuario.pdf",
                        descripcion: "Documentación completa del sistema",
                        ejecutivo: "Ana Martín",
                        fecha: "2025-01-16"
                    }
                ]
            };
            
            mostrarContenidoClase(contenidoEjemplo);
        }

        function mostrarContenidoClase(contenido) {
            const contentArea = $('#class-content-area');
            let html = `
                <div class="class-content">
                    <div class="class-header">
                        <h4 class="class-title">${contenido.titulo}</h4>
                        <div class="class-meta">
                            <div class="meta-item">
                                <i class="fas fa-user"></i>
                                <span>Creador del curso: ${contenido.ejecutivo_curso}</span>
                            </div>
                            <div class="meta-item">
                                <i class="fas fa-chalkboard-teacher"></i>
                                <span>Creador de la clase: ${contenido.ejecutivo_clase}</span>
                            </div>
                            <div class="meta-item">
                                <i class="fas fa-calendar"></i>
                                <span>Fecha de creación: ${contenido.fecha_creacion}</span>
                            </div>
                        </div>
                    </div>
                    <div class="content-area">
            `;
            
            contenido.contenidos.forEach(item => {
                html += generarContenidoItem(item);
            });
            
            html += '</div></div>';
            contentArea.html(html);
        }

        function generarContenidoItem(item) {
            const typeClasses = {
                'video_youtube': 'content-type-video',
                'video_archivo': 'content-type-video',
                'audio': 'content-type-audio',
                'pdf': 'content-type-pdf',
                'imagen': 'content-type-image'
            };
            
            const typeIcons = {
                'video_youtube': 'fas fa-video',
                'video_archivo': 'fas fa-video',
                'audio': 'fas fa-volume-up',
                'pdf': 'fas fa-file-pdf',
                'imagen': 'fas fa-image'
            };
            
            let mediaContent = '';
            
            switch(item.tipo) {
                case 'video_youtube':
                    mediaContent = `
                        <div class="video-player">
                            <iframe src="${item.url}" frameborder="0" allowfullscreen 
                                    style="width: 100%; height: 450px;"></iframe>
                        </div>
                    `;
                    break;
                    
                case 'video_archivo':
                    mediaContent = `
                        <div class="video-player">
                            <video controls style="width: 100%; max-height: 450px;">
                                <source src="uploads/${item.archivo}" type="video/mp4">
                                Tu navegador no soporta el elemento video.
                            </video>
                        </div>
                    `;
                    break;
                    
                case 'audio':
                    mediaContent = `
                        <audio controls class="audio-player">
                            <source src="uploads/${item.archivo}" type="audio/mpeg">
                            Tu navegador no soporta el elemento audio.
                        </audio>
                    `;
                    break;
                    
                case 'pdf':
                    mediaContent = `
                        <div class="pdf-container">
                            <iframe src="uploads/${item.archivo}" class="pdf-viewer" 
                                    type="application/pdf">
                                <p>Tu navegador no puede mostrar PDFs. 
                                   <a href="uploads/${item.archivo}" target="_blank">Haz clic aquí para descargar</a>
                                </p>
                            </iframe>
                        </div>
                    `;
                    break;
                    
                case 'imagen':
                    mediaContent = `
                        <img src="uploads/${item.archivo}" alt="${item.titulo}" class="image-viewer">
                    `;
                    break;
            }
            
            return `
                <div class="content-item">
                    <div class="content-header">
                        <div>
                            <h6 class="mb-1">${item.titulo}</h6>
                            <small class="text-muted">Por ${item.ejecutivo} - ${item.fecha}</small>
                        </div>
                        <span class="content-type-badge ${typeClasses[item.tipo]}">
                            <i class="${typeIcons[item.tipo]} mr-1"></i>
                            ${item.tipo.replace('_', ' ').toUpperCase()}
                        </span>
                    </div>
                    <div class="content-body">
                        ${mediaContent}
                        ${item.descripcion ? `
                            <div class="content-description">
                                <strong>Descripción:</strong> ${item.descripcion}
                            </div>
                        ` : ''}
                        <div class="download-section">
                            <button class="btn btn-outline-primary btn-sm" onclick="descargarContenido('${item.archivo || item.url}')">
                                <i class="fas fa-download mr-1"></i>Descargar
                            </button>
                        </div>
                    </div>
                </div>
            `;
        }

        // Funciones de modales
        function mostrarModalCurso() {
            $('#modalCurso').modal('show');
            $('#form-curso')[0].reset();
            $('#curso-id').val('');
            $('#titulo-modal-curso').text('Crear Nuevo Curso');
        }

        function mostrarModalClase() {
            cargarCursosEnSelect();
            $('#modalClase').modal('show');
            $('#form-clase')[0].reset();
            $('#clase-id').val('');
            $('#titulo-modal-clase').text('Crear Nueva Clase');
        }

        function mostrarModalContenido() {
            cargarClasesEnSelect();
            $('#modalContenido').modal('show');
            $('#form-contenido')[0].reset();
            $('#contenido-id').val('');
            $('#titulo-modal-contenido').text('Subir Contenido');
        }

        function cargarCursosEnSelect() {
            // Simular carga en select
            const select = $('#clase-curso');
            select.html('<option value="">Seleccionar curso...</option>');
            select.append('<option value="1">Introducción al Sistema</option>');
            select.append('<option value="2">Gestión de Citas</option>');
        }

        function cargarClasesEnSelect() {
            // Simular carga en select
            const select = $('#contenido-clase');
            select.html('<option value="">Seleccionar clase...</option>');
            select.append('<option value="1">Bienvenida</option>');
            select.append('<option value="2">Navegación Básica</option>');
        }

        function mostrarCamposSegunTipo() {
            const tipo = $('#contenido-tipo').val();
            const campoArchivo = $('#campo-archivo');
            const campoUrl = $('#campo-url');
            const inputArchivo = $('#contenido-archivo');
            
            campoArchivo.hide();
            campoUrl.hide();
            
            if (tipo === 'video_youtube') {
                campoUrl.show();
            } else if (tipo !== '') {
                campoArchivo.show();
                
                // Configurar accept según tipo
                switch(tipo) {
                    case 'video_archivo':
                        inputArchivo.attr('accept', '.mp4');
                        break;
                    case 'audio':
                        inputArchivo.attr('accept', '.mp3');
                        break;
                    case 'pdf':
                        inputArchivo.attr('accept', '.pdf');
                        break;
                    case 'imagen':
                        inputArchivo.attr('accept', '.jpg,.jpeg,.png');
                        break;
                }
            }
        }

        // Funciones de guardado
        function guardarCurso() {
            const formData = new FormData($('#form-curso')[0]);
            // Aquí iría la llamada AJAX al servidor
            console.log('Guardando curso:', Object.fromEntries(formData));
            $('#modalCurso').modal('hide');
            cargarCursos();
        }

        function guardarClase() {
            const formData = new FormData($('#form-clase')[0]);
            // Aquí iría la llamada AJAX al servidor
            console.log('Guardando clase:', Object.fromEntries(formData));
            $('#modalClase').modal('hide');
            if (cursoActual) {
                cargarClasesCurso(cursoActual);
            }
        }

        function guardarContenido() {
            const formData = new FormData($('#form-contenido')[0]);
            // Aquí iría la llamada AJAX al servidor para subir archivo
            console.log('Guardando contenido:', Object.fromEntries(formData));
            $('#modalContenido').modal('hide');
            if (claseActual) {
                cargarContenidoClase(claseActual);
            }
        }

        // Funciones de comentarios
        function agregarComentario() {
            const contenido = $('#nuevo-comentario').val().trim();
            if (!contenido) return;
            
            // llamada a AJAX
            console.log('Agregando comentario:', contenido);
            $('#nuevo-comentario').val('');
            cargarComentarios();
        }

        function cargarComentarios() {
            // Simular comentarios
            const comentarios = [
                {
                    id: 1,
                    autor: "María González",
                    fecha: "2025-01-20 10:30",
                    contenido: "Excelente explicación en el video. Me ayudó mucho a entender el proceso.",
                    respuestas: [
                        {
                            id: 2,
                            autor: "Juan Pérez",
                            fecha: "2025-01-20 11:15",
                            contenido: "Me alegra que te haya sido útil. Si tienes más dudas, no dudes en preguntar."
                        }
                    ]
                }
            ];
            
            mostrarComentarios(comentarios);
        }

        function mostrarComentarios(comentarios) {
            const container = $('#comments-list');
            container.empty();
            
            comentarios.forEach(comentario => {
                let html = `
                    <div class="comment-item">
                        <div class="comment-header">
                            <span class="comment-author">${comentario.autor}</span>
                            <span class="comment-date">${comentario.fecha}</span>
                        </div>
                        <div class="comment-content">${comentario.contenido}</div>
                        <div class="comment-actions">
                            <button class="btn btn-sm btn-outline-primary" onclick="responderComentario(${comentario.id})">
                                <i class="fas fa-reply mr-1"></i>Responder
                            </button>
                        </div>
                `;
                
                if (comentario.respuestas && comentario.respuestas.length > 0) {
                    html += '<div class="reply-section">';
                    comentario.respuestas.forEach(respuesta => {
                        html += `
                            <div class="comment-item">
                                <div class="comment-header">
                                    <span class="comment-author">${respuesta.autor}</span>
                                    <span class="comment-date">${respuesta.fecha}</span>
                                </div>
                                <div class="comment-content">${respuesta.contenido}</div>
                            </div>
                        `;
                    });
                    html += '</div>';
                }
                
                html += '</div>';
                container.append(html);
            });
        }

        function responderComentario(idComentario) {
            // Implementar funcionalidad de respuesta
            console.log('Respondiendo al comentario:', idComentario);
        }

        function descargarContenido(archivo) {
            // Implementar descarga
            console.log('Descargando:', archivo);
            window.open('uploads/' + archivo, '_blank');
        }

        // Cargar comentarios al iniciar
        setTimeout(() => {
            cargarComentarios();
        }, 1000);
    </script>
</body>
</html>
