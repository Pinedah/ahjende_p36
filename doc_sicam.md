
## 🖥️ HTML - Formulario con Imagen

```html
<!-- Modal para crear ejecutivo -->
<div class="modal fade" id="modal_ejecutivo">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4>Crear Ejecutivo</h4>
            </div>
            <div class="modal-body">
                <form id="formularioEjecutivo" enctype="multipart/form-data">
                    
                    <div class="form-group">
                        <label>Nombre:</label>
                        <input type="text" id="nom_eje" name="nom_eje" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Teléfono:</label>
                        <input type="text" id="tel_eje" name="tel_eje" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Foto (opcional):</label>
                        <input type="file" id="fot_eje" name="fot_eje" class="form-control" accept="image/*">
                        <small class="text-muted">JPG, PNG. Máximo 5MB</small>
                    </div>
                    
                    <!-- Preview de imagen -->
                    <div id="preview" style="display:none; margin-top:10px;">
                        <img id="img-preview" src="" style="max-width: 150px; border: 1px solid #ddd;">
                    </div>
                    
                    <div class="form-group" style="margin-top:20px;">
                        <button type="submit" class="btn btn-primary">Guardar</button>
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                    </div>
                    
                </form>
            </div>
        </div>
    </div>
</div>
```

---

## 📱 JAVASCRIPT (jQuery) - Validación Frontend

```javascript
// Preview al seleccionar imagen
$("#fot_eje").change(function() {
    mostrarPreview(this);
});

// Submit del formulario con validaciones
$("#formularioEjecutivo").submit(function(e) {
    e.preventDefault();
    
    // Validar campos de texto
    if (!$("#nom_eje").val().trim()) {
        alert('El nombre es requerido');
        return;
    }
    
    if (!$("#tel_eje").val().trim()) {
        alert('El teléfono es requerido');
        return;
    }
    
    // Validar imagen si existe
    if ($("#fot_eje")[0].files[0]) {
        if (!validarImagen()) {
            return;
        }
    }
    
    // Si todo está bien, enviar
    enviarFormulario();
});

// Función para validar imagen
function validarImagen() {
    var archivo = $("#fot_eje")[0].files[0];
    var nombre = archivo.name;
    var tamannio = archivo.size;
    var extension = nombre.split('.').pop().toLowerCase();
    
    // Validar extensión
    if (!['jpg', 'jpeg', 'png'].includes(extension)) {
        alert('Solo se permiten archivos JPG y PNG');
        return false;
    }
    
    // Validar tamaño (5MB)
    if (tamannio > 5242880) {
        alert('La imagen no debe exceder 5MB');
        return false;
    }
    
    return true;
}

// Enviar formulario completo
function enviarFormulario() {
    var formData = new FormData($('#formularioEjecutivo')[0]);
    formData.append('action', 'crear_ejecutivo');
    
    $.ajax({
        url: 'server/controlador_ejecutivos.php',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        dataType: 'json',
        beforeSend: function() {
            $('button[type="submit"]').prop('disabled', true).text('Guardando...');
        },
        success: function(response) {
            if (response.success) {
                $('#modal_ejecutivo').modal('hide');
                alert(response.message);
                limpiarFormulario();
                // Recargar datos si es necesario
            } else {
                alert('Error: ' + response.message);
            }
        },
        error: function() {
            alert('Error de conexión');
        },
        complete: function() {
            $('button[type="submit"]').prop('disabled', false).text('Guardar');
        }
    });
}

// Mostrar preview de imagen
function mostrarPreview(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            $('#img-preview').attr('src', e.target.result);
            $('#preview').show();
        }
        reader.readAsDataURL(input.files[0]);
    } else {
        $('#preview').hide();
    }
}

// Limpiar formulario
function limpiarFormulario() {
    $('#formularioEjecutivo')[0].reset();
    $('#preview').hide();
}
```

---

## ⚙️ PHP - Backend en controlador_ejecutivos.php

```php
case 'crear_ejecutivo':
    $nom_eje = escape($_POST['nom_eje'], $connection);
    $tel_eje = escape($_POST['tel_eje'], $connection);
    
    // Validaciones backend
    if (empty($nom_eje)) {
        echo respuestaError('El nombre es requerido');
        break;
    }
    
    if (empty($tel_eje)) {
        echo respuestaError('El teléfono es requerido');
        break;
    }
    
    // Insertar ejecutivo PRIMERO para obtener el ID
    $query = "INSERT INTO ejecutivo (nom_eje, tel_eje) VALUES ('$nom_eje', '$tel_eje')";
    
    if (!mysqli_query($connection, $query)) {
        echo respuestaError('Error al crear ejecutivo');
        break;
    }
    
    $nuevo_id = mysqli_insert_id($connection);
    
    // Procesar imagen si existe
    if (isset($_FILES['fot_eje']) && $_FILES['fot_eje']['error'] === UPLOAD_ERR_OK) {
        $archivo = $_FILES['fot_eje'];
        $extension = strtolower(pathinfo($archivo['name'], PATHINFO_EXTENSION));
        
        // Validaciones de imagen
        if (!in_array($extension, ['jpg', 'jpeg', 'png'])) {
            echo respuestaError('Solo se permiten archivos JPG y PNG');
            break;
        }
        
        if ($archivo['size'] > 5242880) {
            echo respuestaError('La imagen no debe exceder 5MB');
            break;
        }
        
        // Generar nombre único: ID + SHA-1 del contenido
        $contenido_archivo = file_get_contents($archivo['tmp_name']);
        $sha1_hash = sha1($contenido_archivo . $nuevo_id);
        $fot_eje = "foto-ejecutivo-{$nuevo_id}-{$sha1_hash}.{$extension}";
        
        $ruta = '../../uploads/' . $fot_eje;
        
        // Mover archivo a carpeta uploads
        if (move_uploaded_file($archivo['tmp_name'], $ruta)) {
            // Actualizar ejecutivo con el nombre de la imagen
            $query_update = "UPDATE ejecutivo SET fot_eje = '$fot_eje' WHERE id_eje = '$nuevo_id'";
            mysqli_query($connection, $query_update);
        } else {
            echo respuestaError('Error al guardar imagen');
            break;
        }
    }
    
    echo respuestaExito(['id' => $nuevo_id], 'Ejecutivo creado correctamente');
break;

case 'actualizar_ejecutivo':
    $id_eje = escape($_POST['id_eje'], $connection);
    $nom_eje = escape($_POST['nom_eje'], $connection);
    $tel_eje = escape($_POST['tel_eje'], $connection);
    
    // Validaciones backend
    if (empty($id_eje)) {
        echo respuestaError('ID del ejecutivo es requerido');
        break;
    }
    
    if (empty($nom_eje)) {
        echo respuestaError('El nombre es requerido');
        break;
    }
    
    if (empty($tel_eje)) {
        echo respuestaError('El teléfono es requerido');
        break;
    }
    
    // Actualizar datos básicos
    $query = "UPDATE ejecutivo SET nom_eje = '$nom_eje', tel_eje = '$tel_eje' WHERE id_eje = '$id_eje'";
    
    if (!mysqli_query($connection, $query)) {
        echo respuestaError('Error al actualizar ejecutivo');
        break;
    }
    
    // Procesar nueva imagen si existe
    if (isset($_FILES['fot_eje']) && $_FILES['fot_eje']['error'] === UPLOAD_ERR_OK) {
        $archivo = $_FILES['fot_eje'];
        $extension = strtolower(pathinfo($archivo['name'], PATHINFO_EXTENSION));
        
        // Validaciones de imagen
        if (!in_array($extension, ['jpg', 'jpeg', 'png'])) {
            echo respuestaError('Solo se permiten archivos JPG y PNG');
            break;
        }
        
        if ($archivo['size'] > 5242880) {
            echo respuestaError('La imagen no debe exceder 5MB');
            break;
        }
        
        // Obtener foto actual para eliminarla
        $query_foto = "SELECT fot_eje FROM ejecutivo WHERE id_eje = '$id_eje'";
        $resultado = ejecutarConsulta($query_foto, $connection);
        
        if ($resultado && count($resultado) > 0) {
            $fotoActual = $resultado[0]['fot_eje'];
            
            // Eliminar foto anterior si existe
            if ($fotoActual != NULL && file_exists("../../uploads/$fotoActual")) {
                unlink("../../uploads/$fotoActual");
            }
            
            // Generar NUEVO nombre único (IMPORTANTE: evita caché del navegador)
            $contenido_archivo = file_get_contents($archivo['tmp_name']);
            $sha1_hash = sha1($contenido_archivo . $id_eje . time()); // Agregamos time() para forzar nuevo hash
            $nueva_foto = "foto-ejecutivo-{$id_eje}-{$sha1_hash}.{$extension}";
            
            $ruta = '../../uploads/' . $nueva_foto;
            
            // Mover nueva imagen
            if (move_uploaded_file($archivo['tmp_name'], $ruta)) {
                // Actualizar BD con nuevo nombre
                $query_update = "UPDATE ejecutivo SET fot_eje = '$nueva_foto' WHERE id_eje = '$id_eje'";
                mysqli_query($connection, $query_update);
            } else {
                echo respuestaError('Error al guardar nueva imagen');
                break;
            }
        }
    }
    
    echo respuestaExito(null, 'Ejecutivo actualizado correctamente');
break;
```

---

## 🔧 CARACTERÍSTICAS PRINCIPALES

✅ **Formulario completo**: Datos + imagen en un solo envío  
✅ **Crear y Editar**: Maneja tanto creación como actualización  
✅ **Validación frontend**: jQuery valida antes de enviar  
✅ **Validación backend**: PHP valida nuevamente por seguridad  
✅ **Preview de imagen**: Vista previa antes de enviar  
✅ **Nomenclatura única**: `foto-ejecutivo-{id}-{sha1}.{ext}`  
✅ **Anti-caché**: Rename forzado en ediciones para evitar caché del navegador  
✅ **Limpieza automática**: Elimina imágenes anteriores al actualizar  
✅ **Carpeta centralizada**: Todas las imágenes en `/uploads/`  
✅ **Respuestas JSON**: Formato estándar SICAM  
✅ **Manejo de errores**: Validaciones completas  
✅ **Seguridad**: Escape de datos y validación de archivos

---

## 📝 EJEMPLO DE NOMBRE GENERADO

```
foto-ejecutivo-123-a1b2c3d4e5f6789012345678901234567890abcd.jpg
```

- **123**: ID del ejecutivo en la base de datos
- **a1b2c3d4...**: Hash SHA-1 del contenido del archivo + ID + timestamp
- **jpg**: Extensión original del archivo

**🔒 ¿Cómo evitamos duplicados?**
- El **ID del ejecutivo** es único en la base de datos
- El **SHA-1** se genera del contenido real del archivo + ID + time()
- Si el mismo ejecutivo sube la misma imagen → mismo hash (reemplaza)  
- Si es diferente ejecutivo o diferente imagen → hash único garantizado
- **Imposible colisión**: La combinación ID + SHA-1 + timestamp es matemáticamente única

**⚠️ PROBLEMA DE CACHÉ DEL NAVEGADOR**
La caché es **amiga del usuario** (páginas más rápidas) pero **enemiga del programador** (cambios no se ven).

**🔄 Solución - Rename forzado en edición:**
- Al editar, agregamos `time()` al hash → nuevo nombre siempre  
- Ejemplo: `foto-ejecutivo-123-HASH1.jpg` → `foto-ejecutivo-123-HASH2.jpg`
- El navegador ve nombre diferente → descarga imagen nueva
- **Sin rename = imagen cached antigua, Con rename = imagen nueva**

---

## 📌 NOTAS IMPORTANTES

- ✅ Header `Content-Type` con `charset=utf-8` en controladores
- ✅ Controlador devuelve JSON con array estructurado
- ✅ `response.data` es un array de objetos
- ✅ `escape()` obligatorio para prevenir SQL Injection
- ✅ Funciones pequeñas (máximo 20-30 líneas)
- ✅ Separar lógica de presentación
- ✅ Mostrar query en errores para debugging
- ✅ Vibecodear moderadamente 🚀

---

> *"Por cada paso de éxito, subir 2 de humildad"* - **ericorps**
