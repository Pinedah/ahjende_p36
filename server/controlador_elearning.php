<?php
// Habilitar reporte de errores para debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Log de debugging
file_put_contents('debug_elearning.log', '[' . date('Y-m-d H:i:s') . '] REQUEST recibido: ' . print_r($_POST, true) . "\n", FILE_APPEND);

include '../inc/conexion.php';
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	
	// Validar que action esté presente
	if (!isset($_POST['action'])) {
		echo respuestaError('Acción no especificada');
		exit;
	}
	
	$action = escape($_POST['action'], $connection);
	
	switch($action) {

		case 'test_conexion':
			echo respuestaExito(['timestamp' => date('Y-m-d H:i:s')], 'Controlador de e-learning funcionando correctamente');
		break;

		case 'obtener_cursos':
			$query = "SELECT c.id_curso, c.nom_curso, c.des_curso, c.fec_creacion_curso,
					         e.nom_eje as creador_curso,
					         (SELECT COUNT(*) FROM clase cl WHERE cl.id_curso = c.id_curso AND cl.eli_clase = 1) as total_clases
					  FROM curso c
					  JOIN ejecutivo e ON c.id_eje_creador = e.id_eje
					  WHERE c.eli_curso = 1
					  ORDER BY c.fec_creacion_curso DESC";
			
			file_put_contents('debug_elearning.log', '[' . date('Y-m-d H:i:s') . '] Query cursos: ' . $query . "\n", FILE_APPEND);
			
			$datos = ejecutarConsulta($query, $connection);

			if($datos !== false) {
				file_put_contents('debug_elearning.log', '[' . date('Y-m-d H:i:s') . '] Cursos encontrados: ' . count($datos) . "\n", FILE_APPEND);
				echo respuestaExito($datos, 'Cursos obtenidos correctamente');
			} else {
				$error = mysqli_error($connection);
				file_put_contents('debug_elearning.log', '[' . date('Y-m-d H:i:s') . '] Error MySQL cursos: ' . $error . "\n", FILE_APPEND);
				echo respuestaError('Error al consultar cursos: ' . $error);
			}
		break;

		case 'obtener_clases_curso':
			$id_curso = isset($_POST['id_curso']) ? intval($_POST['id_curso']) : 0;
			
			if (!$id_curso) {
				echo respuestaError('ID de curso requerido');
				break;
			}
			
			$query = "SELECT cl.id_clase, cl.tit_clase, cl.des_clase, cl.ord_clase,
					         e.nom_eje as creador_clase, cl.fec_creacion_clase
					  FROM clase cl
					  JOIN ejecutivo e ON cl.id_eje_creador = e.id_eje
					  WHERE cl.id_curso = $id_curso AND cl.eli_clase = 1
					  ORDER BY cl.ord_clase ASC";
			
			file_put_contents('debug_elearning.log', '[' . date('Y-m-d H:i:s') . '] Query clases curso ' . $id_curso . ': ' . $query . "\n", FILE_APPEND);
			
			$datos = ejecutarConsulta($query, $connection);

			if($datos !== false) {
				file_put_contents('debug_elearning.log', '[' . date('Y-m-d H:i:s') . '] Clases encontradas: ' . count($datos) . "\n", FILE_APPEND);
				echo respuestaExito($datos, 'Clases obtenidas correctamente');
			} else {
				$error = mysqli_error($connection);
				file_put_contents('debug_elearning.log', '[' . date('Y-m-d H:i:s') . '] Error MySQL clases: ' . $error . "\n", FILE_APPEND);
				echo respuestaError('Error al consultar clases: ' . $error);
			}
		break;

		case 'obtener_contenido_clase':
			$id_clase = isset($_POST['id_clase']) ? intval($_POST['id_clase']) : 0;
			
			if (!$id_clase) {
				echo respuestaError('ID de clase requerido');
				break;
			}
			
			// Obtener información de la clase y curso
			$query_clase = "SELECT cl.tit_clase, cl.des_clase, cl.fec_creacion_clase,
							       e_clase.nom_eje as creador_clase,
							       c.nom_curso, e_curso.nom_eje as creador_curso, c.fec_creacion_curso
							FROM clase cl
							JOIN curso c ON cl.id_curso = c.id_curso
							JOIN ejecutivo e_clase ON cl.id_eje_creador = e_clase.id_eje
							JOIN ejecutivo e_curso ON c.id_eje_creador = e_curso.id_eje
							WHERE cl.id_clase = $id_clase AND cl.eli_clase = 1";
			
			$info_clase = ejecutarConsulta($query_clase, $connection);
			
			if (!$info_clase || count($info_clase) === 0) {
				echo respuestaError('Clase no encontrada');
				break;
			}
			
			// Obtener contenido de la clase
			$query_contenido = "SELECT con.id_contenido, con.tit_contenido, con.tip_contenido,
								       con.arc_contenido, con.url_contenido, con.des_contenido,
								       con.ord_contenido, con.fec_creacion_contenido,
								       e.nom_eje as creador_contenido
								FROM contenido con
								JOIN ejecutivo e ON con.id_eje_creador = e.id_eje
								WHERE con.id_clase = $id_clase AND con.eli_contenido = 1
								ORDER BY con.ord_contenido ASC";
			
			file_put_contents('debug_elearning.log', '[' . date('Y-m-d H:i:s') . '] Query contenido clase ' . $id_clase . ': ' . $query_contenido . "\n", FILE_APPEND);
			
			$contenidos = ejecutarConsulta($query_contenido, $connection);

			if($contenidos !== false) {
				$resultado = [
					'clase' => $info_clase[0],
					'contenidos' => $contenidos
				];
				
				file_put_contents('debug_elearning.log', '[' . date('Y-m-d H:i:s') . '] Contenidos encontrados: ' . count($contenidos) . "\n", FILE_APPEND);
				echo respuestaExito($resultado, 'Contenido obtenido correctamente');
			} else {
				$error = mysqli_error($connection);
				file_put_contents('debug_elearning.log', '[' . date('Y-m-d H:i:s') . '] Error MySQL contenido: ' . $error . "\n", FILE_APPEND);
				echo respuestaError('Error al consultar contenido: ' . $error);
			}
		break;

		case 'obtener_comentarios_contenido':
			$id_contenido = isset($_POST['id_contenido']) ? intval($_POST['id_contenido']) : 0;
			
			if (!$id_contenido) {
				echo respuestaError('ID de contenido requerido');
				break;
			}
			
			$query = "SELECT ce.id_comentario, ce.tex_comentario, ce.fec_comentario,
					         ce.id_comentario_padre, e.nom_eje as autor_comentario,
					         e.id_eje as id_autor
					  FROM comentario_elearning ce
					  JOIN ejecutivo e ON ce.id_eje_comentario = e.id_eje
					  WHERE ce.id_contenido = $id_contenido AND ce.eli_comentario = 1
					  ORDER BY COALESCE(ce.id_comentario_padre, ce.id_comentario), ce.fec_comentario ASC";
			
			file_put_contents('debug_elearning.log', '[' . date('Y-m-d H:i:s') . '] Query comentarios contenido ' . $id_contenido . ': ' . $query . "\n", FILE_APPEND);
			
			$datos = ejecutarConsulta($query, $connection);

			if($datos !== false) {
				// Organizar comentarios en estructura jerárquica
				$comentarios_organizados = organizarComentariosJerarquicos($datos);
				
				file_put_contents('debug_elearning.log', '[' . date('Y-m-d H:i:s') . '] Comentarios encontrados: ' . count($datos) . "\n", FILE_APPEND);
				echo respuestaExito($comentarios_organizados, 'Comentarios obtenidos correctamente');
			} else {
				$error = mysqli_error($connection);
				file_put_contents('debug_elearning.log', '[' . date('Y-m-d H:i:s') . '] Error MySQL comentarios: ' . $error . "\n", FILE_APPEND);
				echo respuestaError('Error al consultar comentarios: ' . $error);
			}
		break;

		case 'agregar_comentario':
			$id_contenido = isset($_POST['id_contenido']) ? intval($_POST['id_contenido']) : 0;
			$tex_comentario = isset($_POST['comentario']) ? escape($_POST['comentario'], $connection) : '';
			$id_eje_comentario = isset($_POST['id_ejecutivo']) ? intval($_POST['id_ejecutivo']) : 1; // Default user
			$id_comentario_padre = isset($_POST['id_comentario_padre']) && $_POST['id_comentario_padre'] !== '' ? intval($_POST['id_comentario_padre']) : null;
			
			if (!$id_contenido || !$tex_comentario) {
				echo respuestaError('Datos requeridos: contenido y comentario');
				break;
			}
			
			$valores_padre = $id_comentario_padre ? "'$id_comentario_padre'" : 'NULL';
			
			$query = "INSERT INTO comentario_elearning (id_contenido, tex_comentario, id_eje_comentario, id_comentario_padre)
					  VALUES ($id_contenido, '$tex_comentario', $id_eje_comentario, $valores_padre)";
			
			file_put_contents('debug_elearning.log', '[' . date('Y-m-d H:i:s') . '] Query agregar comentario: ' . $query . "\n", FILE_APPEND);
			
			if (mysqli_query($connection, $query)) {
				$nuevo_id = mysqli_insert_id($connection);
				
				// Obtener el comentario recién creado con información del autor
				$query_nuevo = "SELECT ce.id_comentario, ce.tex_comentario, ce.fec_comentario,
								       ce.id_comentario_padre, e.nom_eje as autor_comentario,
								       e.id_eje as id_autor
								FROM comentario_elearning ce
								JOIN ejecutivo e ON ce.id_eje_comentario = e.id_eje
								WHERE ce.id_comentario = $nuevo_id";
				
				$comentario_nuevo = ejecutarConsulta($query_nuevo, $connection);
				
				if ($comentario_nuevo && count($comentario_nuevo) > 0) {
					file_put_contents('debug_elearning.log', '[' . date('Y-m-d H:i:s') . '] Comentario agregado exitosamente ID: ' . $nuevo_id . "\n", FILE_APPEND);
					echo respuestaExito($comentario_nuevo[0], 'Comentario agregado correctamente');
				} else {
					echo respuestaExito(['id' => $nuevo_id], 'Comentario agregado correctamente');
				}
			} else {
				$error = mysqli_error($connection);
				file_put_contents('debug_elearning.log', '[' . date('Y-m-d H:i:s') . '] Error MySQL agregar comentario: ' . $error . "\n", FILE_APPEND);
				echo respuestaError('Error al agregar comentario: ' . $error);
			}
		break;

		case 'crear_curso':
			$nom_curso = isset($_POST['nombre']) ? escape($_POST['nombre'], $connection) : '';
			$des_curso = isset($_POST['descripcion']) ? escape($_POST['descripcion'], $connection) : '';
			$id_eje_creador = isset($_POST['id_ejecutivo']) ? intval($_POST['id_ejecutivo']) : 1;
			
			if (!$nom_curso) {
				echo respuestaError('Nombre del curso requerido');
				break;
			}
			
			$query = "INSERT INTO curso (nom_curso, des_curso, id_eje_creador)
					  VALUES ('$nom_curso', '$des_curso', $id_eje_creador)";
			
			file_put_contents('debug_elearning.log', '[' . date('Y-m-d H:i:s') . '] Query crear curso: ' . $query . "\n", FILE_APPEND);
			
			if (mysqli_query($connection, $query)) {
				$nuevo_id = mysqli_insert_id($connection);
				file_put_contents('debug_elearning.log', '[' . date('Y-m-d H:i:s') . '] Curso creado exitosamente ID: ' . $nuevo_id . "\n", FILE_APPEND);
				echo respuestaExito(['id' => $nuevo_id], 'Curso creado correctamente');
			} else {
				$error = mysqli_error($connection);
				file_put_contents('debug_elearning.log', '[' . date('Y-m-d H:i:s') . '] Error MySQL crear curso: ' . $error . "\n", FILE_APPEND);
				echo respuestaError('Error al crear curso: ' . $error);
			}
		break;

		case 'crear_clase':
			$id_curso = isset($_POST['id_curso']) ? intval($_POST['id_curso']) : 0;
			$tit_clase = isset($_POST['titulo']) ? escape($_POST['titulo'], $connection) : '';
			$des_clase = isset($_POST['descripcion']) ? escape($_POST['descripcion'], $connection) : '';
			$ord_clase = isset($_POST['orden']) ? intval($_POST['orden']) : 1;
			$id_eje_creador = isset($_POST['id_ejecutivo']) ? intval($_POST['id_ejecutivo']) : 1;
			
			if (!$id_curso || !$tit_clase) {
				echo respuestaError('Curso y título de clase requeridos');
				break;
			}
			
			$query = "INSERT INTO clase (id_curso, tit_clase, des_clase, ord_clase, id_eje_creador)
					  VALUES ($id_curso, '$tit_clase', '$des_clase', $ord_clase, $id_eje_creador)";
			
			file_put_contents('debug_elearning.log', '[' . date('Y-m-d H:i:s') . '] Query crear clase: ' . $query . "\n", FILE_APPEND);
			
			if (mysqli_query($connection, $query)) {
				$nuevo_id = mysqli_insert_id($connection);
				file_put_contents('debug_elearning.log', '[' . date('Y-m-d H:i:s') . '] Clase creada exitosamente ID: ' . $nuevo_id . "\n", FILE_APPEND);
				echo respuestaExito(['id' => $nuevo_id], 'Clase creada correctamente');
			} else {
				$error = mysqli_error($connection);
				file_put_contents('debug_elearning.log', '[' . date('Y-m-d H:i:s') . '] Error MySQL crear clase: ' . $error . "\n", FILE_APPEND);
				echo respuestaError('Error al crear clase: ' . $error);
			}
		break;

		case 'subir_contenido':
			$id_clase = isset($_POST['id_clase']) ? intval($_POST['id_clase']) : 0;
			$tit_contenido = isset($_POST['titulo']) ? escape($_POST['titulo'], $connection) : '';
			$tip_contenido = isset($_POST['tipo']) ? escape($_POST['tipo'], $connection) : '';
			$des_contenido = isset($_POST['descripcion']) ? escape($_POST['descripcion'], $connection) : '';
			$ord_contenido = isset($_POST['orden']) ? intval($_POST['orden']) : 1;
			$id_eje_creador = isset($_POST['id_ejecutivo']) ? intval($_POST['id_ejecutivo']) : 1;
			
			$arc_contenido = null;
			$url_contenido = null;
			
			if (!$id_clase || !$tit_contenido || !$tip_contenido) {
				echo respuestaError('Clase, título y tipo de contenido requeridos');
				break;
			}
			
			// Manejar archivo o URL según el tipo
			if ($tip_contenido === 'video_youtube') {
				$url_contenido = isset($_POST['url']) ? escape($_POST['url'], $connection) : '';
				if (!$url_contenido) {
					echo respuestaError('URL requerida para videos de YouTube');
					break;
				}
			} else {
				// Manejar subida de archivo
				if (isset($_FILES['archivo']) && $_FILES['archivo']['error'] === UPLOAD_ERR_OK) {
					$archivo = $_FILES['archivo'];
					$extension = strtolower(pathinfo($archivo['name'], PATHINFO_EXTENSION));
					
					// Validar tipos de archivo
					$extensiones_permitidas = [
						'video_archivo' => ['mp4'],
						'audio' => ['mp3'],
						'pdf' => ['pdf'],
						'imagen' => ['jpg', 'jpeg', 'png']
					];
					
					if (!isset($extensiones_permitidas[$tip_contenido]) || 
						!in_array($extension, $extensiones_permitidas[$tip_contenido])) {
						echo respuestaError('Tipo de archivo no válido para este tipo de contenido');
						break;
					}
					
					// Generar nombre único para el archivo
					$timestamp = time();
					$hash = md5($archivo['name'] . $timestamp);
					$arc_contenido = "elearning_{$tip_contenido}_{$timestamp}_{$hash}.{$extension}";
					
					$ruta_destino = '../uploads/' . $arc_contenido;
					
					if (!move_uploaded_file($archivo['tmp_name'], $ruta_destino)) {
						echo respuestaError('Error al subir el archivo');
						break;
					}
				} else {
					echo respuestaError('Archivo requerido para este tipo de contenido');
					break;
				}
			}
			
			// Preparar valores para la query
			$arc_valor = $arc_contenido ? "'$arc_contenido'" : 'NULL';
			$url_valor = $url_contenido ? "'$url_contenido'" : 'NULL';
			
			$query = "INSERT INTO contenido (id_clase, tit_contenido, tip_contenido, arc_contenido, url_contenido, des_contenido, ord_contenido, id_eje_creador)
					  VALUES ($id_clase, '$tit_contenido', '$tip_contenido', $arc_valor, $url_valor, '$des_contenido', $ord_contenido, $id_eje_creador)";
			
			file_put_contents('debug_elearning.log', '[' . date('Y-m-d H:i:s') . '] Query crear contenido: ' . $query . "\n", FILE_APPEND);
			
			if (mysqli_query($connection, $query)) {
				$nuevo_id = mysqli_insert_id($connection);
				file_put_contents('debug_elearning.log', '[' . date('Y-m-d H:i:s') . '] Contenido creado exitosamente ID: ' . $nuevo_id . "\n", FILE_APPEND);
				echo respuestaExito(['id' => $nuevo_id, 'archivo' => $arc_contenido], 'Contenido subido correctamente');
			} else {
				$error = mysqli_error($connection);
				file_put_contents('debug_elearning.log', '[' . date('Y-m-d H:i:s') . '] Error MySQL crear contenido: ' . $error . "\n", FILE_APPEND);
				echo respuestaError('Error al subir contenido: ' . $error);
			}
		break;

		default:
			echo respuestaError('Acción no válida');
		break;
	}

	mysqli_close($connection);
	exit;
}

// =====================================
// FUNCIONES AUXILIARES
// =====================================

function organizarComentariosJerarquicos($comentarios) {
	// Crear un array indexado por id para acceso rápido
	$comentarios_indexados = [];
	foreach ($comentarios as $comentario) {
		$comentario['respuestas'] = [];
		$comentarios_indexados[$comentario['id_comentario']] = $comentario;
	}
	
	// Organizar jerárquicamente
	$comentarios_raiz = [];
	
	foreach ($comentarios_indexados as $id => $comentario) {
		if ($comentario['id_comentario_padre'] === null) {
			// Es un comentario raíz
			$comentarios_raiz[] = &$comentarios_indexados[$id];
		} else {
			// Es una respuesta, agregarla al comentario padre
			$id_padre = $comentario['id_comentario_padre'];
			if (isset($comentarios_indexados[$id_padre])) {
				$comentarios_indexados[$id_padre]['respuestas'][] = &$comentarios_indexados[$id];
			}
		}
	}
	
	return $comentarios_raiz;
}
?>
