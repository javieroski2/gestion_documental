<?php $titulo = 'Todos los Documentos'; ?>
<?php require_once '../app/views/layouts/header.php'; ?>
<?php require_once '../app/views/layouts/navbar.php'; ?>
<?php require_once '../app/views/layouts/sidebar.php'; ?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1><i class="fas fa-file-alt"></i> Todos los Documentos</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?php echo URL_BASE; ?>dashboard">Inicio</a></li>
                        <li class="breadcrumb-item active">Documentos</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Repositorio de Documentos</h3>
                </div>
                <div class="card-body">
                    <table id="tablaDocumentos" class="table table-bordered table-striped table-hover table-sm">
                        <thead>
                            <tr>
                                <th width="50">ID</th>
                                <th>Título</th>
                                <th>Categoría</th>
                                <th>Usuario</th>
                                <th width="80">Tamaño</th>
                                <th width="100">Estado</th>
                                <th width="110">Fecha</th>
                                <th width="120">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($documentos)): ?>
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">
                                    <i class="fas fa-inbox fa-2x mb-2"></i><br>
                                    No hay documentos en el sistema
                                </td>
                            </tr>
                            <?php else: ?>
                            <?php foreach ($documentos as $doc): ?>
                            <tr>
                                <td><?php echo $doc['id']; ?></td>
                                <td>
                                    <strong><?php echo htmlspecialchars($doc['titulo']); ?></strong>
                                    <?php if ($doc['descripcion']): ?>
                                    <br><small class="text-muted"><?php echo htmlspecialchars(substr($doc['descripcion'], 0, 50)); ?><?php echo strlen($doc['descripcion']) > 50 ? '...' : ''; ?></small>
                                    <?php endif; ?>
                                    <?php if ($doc['observaciones']): ?>
                                    <br><span class="badge badge-info" data-toggle="tooltip" title="<?php echo htmlspecialchars($doc['observaciones']); ?>">
                                        <i class="fas fa-comment"></i> Observaciones
                                    </span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo htmlspecialchars($doc['categoria_nombre']); ?></td>
                                <td><small><?php echo htmlspecialchars($doc['usuario_nombre']); ?></small></td>
                                <td><small><?php echo number_format($doc['tamano'] / 1024, 1); ?> KB</small></td>
                                <td class="text-center">
                                    <?php 
                                    $badges = [
                                        'pendiente' => 'badge-warning',
                                        'aprobado' => 'badge-success',
                                        'rechazado' => 'badge-danger'
                                    ];
                                    $badge = $badges[$doc['estado_validacion']] ?? 'badge-secondary';
                                    ?>
                                    <span class="badge <?php echo $badge; ?>">
                                        <?php echo ucfirst($doc['estado_validacion']); ?>
                                    </span>
                                </td>
                                <td><small><?php echo date('d/m/Y', strtotime($doc['fecha_creacion'])); ?></small></td>
                                <td class="text-center">
                                    <?php 
                                    // Determinar si puede descargar
                                    $puedeDescargar = false;
                                    
                                    // Super admin y admin pueden descargar todo
                                    if ($_SESSION['rol_id'] == ROL_SUPER_ADMIN || $_SESSION['rol_id'] == ROL_ADMIN) {
                                        $puedeDescargar = true;
                                    }
                                    // Validador puede descargar documentos pendientes
                                    elseif ($_SESSION['rol_id'] == ROL_VALIDADOR && $doc['estado_validacion'] == 'pendiente') {
                                        $puedeDescargar = true;
                                    }
                                    // Usuario puede descargar sus propios documentos
                                    elseif ($doc['usuario_id'] == $_SESSION['user_id']) {
                                        $puedeDescargar = true;
                                    }
                                    // Cualquiera puede descargar documentos aprobados
                                    elseif ($doc['estado_validacion'] == 'aprobado') {
                                        $puedeDescargar = true;
                                    }
                                    ?>
                                    
                                    <?php if ($puedeDescargar): ?>
                                    <?php
                                    // Obtener datos de validación si está aprobado
                                    $validadorNombre = '';
                                    if ($doc['estado_validacion'] == 'aprobado' && !empty($doc['validador_id'])) {
                                        $userModel = $this->model('User');
                                        $validadorData = $userModel->getById($doc['validador_id']);
                                        if ($validadorData) {
                                            $validadorNombre = $validadorData['nombre'] . ' ' . ($validadorData['apellidos'] ?? '');
                                        }
                                    }
                                    $fechaVal = !empty($doc['fecha_validacion']) ? date('d/m/Y H:i', strtotime($doc['fecha_validacion'])) : '';
                                    ?>
                                    <div class="btn-group btn-group-sm">
                                        <button onclick="verDocumento(<?php echo $doc['id']; ?>, '<?php echo htmlspecialchars($doc['titulo']); ?>', '<?php echo $doc['extension']; ?>', '<?php echo $doc['archivo']; ?>', '<?php echo $doc['estado_validacion']; ?>', '<?php echo htmlspecialchars($validadorNombre); ?>', '<?php echo $fechaVal; ?>')" 
                                                class="btn btn-primary" title="Ver documento">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <a href="<?php echo URL_BASE; ?>documento/descargar/<?php echo $doc['id']; ?>" 
                                           class="btn btn-info" title="Descargar">
                                            <i class="fas fa-download"></i>
                                        </a>
                                        <?php if ($doc['estado_validacion'] == 'aprobado'): ?>
                                        <a href="<?php echo URL_BASE; ?>documento/certificado/<?php echo $doc['id']; ?>" 
                                           target="_blank"
                                           class="btn btn-success" 
                                           title="Ver Certificado de Validación">
                                            <i class="fas fa-certificate"></i>
                                        </a>
                                        <?php if ($doc['extension'] == 'pdf'): ?>
                                        <a href="<?php echo URL_BASE; ?>documento/descargarFirmado/<?php echo $doc['id']; ?>" 
                                           class="btn btn-warning" 
                                           title="Descargar PDF con Timbre">
                                            <i class="fas fa-file-signature"></i>
                                        </a>
                                        <?php endif; ?>
                                        <?php endif; ?>
                                        <?php if ($doc['usuario_id'] == $_SESSION['user_id'] && $doc['estado_validacion'] == 'pendiente'): ?>
                                        <button onclick="eliminarDocumento(<?php echo $doc['id']; ?>)" 
                                                class="btn btn-danger" title="Eliminar">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                        <?php endif; ?>
                                    </div>
                                    <?php else: ?>
                                    <span class="text-muted" title="Solo disponible cuando esté aprobado">
                                        <i class="fas fa-lock"></i>
                                    </span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                <div class="card-footer">
                    <small class="text-muted">
                        <i class="fas fa-info-circle"></i> 
                        Mostrando <?php echo count($documentos); ?> documentos
                    </small>
                </div>
            </div>

        </div>
    </section>
</div>

<!-- Modal para Ver Documento -->
<div class="modal fade" id="modalVerDocumento" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <h4 class="modal-title">
                    <i class="fas fa-file-alt"></i> 
                    <span id="tituloDocumento"></span>
                </h4>
                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body" id="contenidoDocumento" style="min-height: 500px;">
                <div class="text-center py-5">
                    <i class="fas fa-spinner fa-spin fa-3x text-primary"></i>
                    <p class="mt-3">Cargando documento...</p>
                </div>
            </div>
            <div class="modal-footer">
                <a id="btnDescargarModal" href="#" class="btn btn-success" download>
                    <i class="fas fa-download"></i> Descargar
                </a>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <i class="fas fa-times"></i> Cerrar
                </button>
            </div>
        </div>
    </div>
</div>

<script>
// Activar tooltips para ver observaciones
$(function () {
    $('[data-toggle="tooltip"]').tooltip();
});

// Ver documento en modal
function verDocumento(id, titulo, extension, archivo, estado = null, validador = null, fechaValidacion = null) {
    $('#tituloDocumento').text(titulo);
    $('#modalVerDocumento').modal('show');
    
    // RUTA CORREGIDA - sin "public" porque ya estamos en public/
    const rutaArchivo = '<?php echo URL_BASE; ?>uploads/documentos/' + archivo;
    $('#btnDescargarModal').attr('href', '<?php echo URL_BASE; ?>documento/descargar/' + id);
    
    let contenido = '';
    
    // Determinar cómo mostrar según la extensión
    if (extension === 'pdf') {
        // Mostrar PDF en iframe
        contenido = `
            <div style="position: relative;">
                <iframe src="${rutaArchivo}" 
                        style="width: 100%; height: 600px; border: none;"
                        type="application/pdf">
                    <p>Tu navegador no puede mostrar PDFs. 
                       <a href="${rutaArchivo}" target="_blank">Haz click aquí para abrirlo</a>
                    </p>
                </iframe>
                ${estado === 'aprobado' ? generarTimbreVisual(validador, fechaValidacion) : ''}
            </div>
        `;
    } else if (['jpg', 'jpeg', 'png', 'gif', 'webp'].includes(extension.toLowerCase())) {
        // Mostrar imagen
        contenido = `
            <div style="position: relative;" class="text-center">
                <img src="${rutaArchivo}" 
                     alt="${titulo}" 
                     style="max-width: 100%; height: auto; max-height: 600px;">
                ${estado === 'aprobado' ? generarTimbreVisual(validador, fechaValidacion, 'imagen') : ''}
            </div>
        `;
    } else {
        // Para otros archivos (Word, Excel, etc.)
        const iconos = {
            'doc': 'fa-file-word text-primary',
            'docx': 'fa-file-word text-primary',
            'xls': 'fa-file-excel text-success',
            'xlsx': 'fa-file-excel text-success',
            'ppt': 'fa-file-powerpoint text-danger',
            'pptx': 'fa-file-powerpoint text-danger',
            'txt': 'fa-file-alt text-secondary',
            'zip': 'fa-file-archive text-warning',
            'rar': 'fa-file-archive text-warning'
        };
        
        const icono = iconos[extension.toLowerCase()] || 'fa-file text-muted';
        
        contenido = `
            <div class="text-center py-5">
                ${estado === 'aprobado' ? generarTimbreVisual(validador, fechaValidacion, 'archivo') : ''}
                <i class="fas ${icono} fa-5x mb-4"></i>
                <h4>${titulo}</h4>
                <p class="text-muted">Archivo .${extension.toUpperCase()}</p>
                <p>Este tipo de archivo no se puede previsualizar en el navegador.</p>
                <a href="<?php echo URL_BASE; ?>documento/descargar/${id}" class="btn btn-success btn-lg">
                    <i class="fas fa-download"></i> Descargar Archivo
                </a>
                <br><br>
                <small class="text-muted">
                    Descarga el archivo para abrirlo en la aplicación correspondiente
                </small>
            </div>
        `;
    }
    
    $('#contenidoDocumento').html(contenido);
}

// Generar timbre visual para overlay
function generarTimbreVisual(validador, fechaValidacion, tipo = 'pdf') {
    const posicion = tipo === 'imagen' ? 'top: 10px; right: 10px;' : 'top: 20px; right: 20px;';
    
    return `
        <div style="
            position: absolute;
            ${posicion}
            z-index: 9999;
            background: linear-gradient(135deg, #28a745, #20c997);
            color: white;
            padding: 20px;
            border-radius: 15px;
            box-shadow: 0 8px 25px rgba(40, 167, 69, 0.5);
            border: 4px solid white;
            min-width: 250px;
            text-align: center;
            animation: fadeIn 0.5s ease-in;
        ">
            <div style="font-size: 24px; font-weight: bold; margin-bottom: 10px;">
                ✓ DOCUMENTO APROBADO
            </div>
            <div style="border-top: 2px solid rgba(255,255,255,0.3); padding-top: 10px; margin-top: 10px;">
                <div style="font-size: 14px; margin: 5px 0;">
                    <strong>Validado por:</strong><br>
                    ${validador || 'Validador'}
                </div>
                <div style="font-size: 12px; opacity: 0.9; margin-top: 8px;">
                    ${fechaValidacion || 'Fecha no disponible'}
                </div>
            </div>
            <div style="
                margin-top: 12px;
                padding: 8px;
                background: rgba(255,255,255,0.2);
                border-radius: 5px;
                font-size: 11px;
            ">
                <i class="fas fa-shield-alt"></i> Certificado Digital Verificado
            </div>
        </div>
        <style>
            @keyframes fadeIn {
                from { opacity: 0; transform: scale(0.9); }
                to { opacity: 1; transform: scale(1); }
            }
        </style>
    `;
}

// Eliminar documento
function eliminarDocumento(id) {
    if (confirm('¿Está seguro de eliminar este documento? Esta acción no se puede deshacer.')) {
        fetch('<?php echo URL_BASE; ?>documento/eliminar/' + id, {
            method: 'POST',
            headers: {'Content-Type': 'application/json'}
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Documento eliminado correctamente');
                location.reload();
            } else {
                alert(data.message || 'Error al eliminar documento');
            }
        })
        .catch(error => {
            alert('Error de conexión');
            console.error(error);
        });
    }
}
</script>

<!-- DataTables -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap4.min.css">
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap4.min.js"></script>
<script>
$(document).ready(function() {
    $('#tablaDocumentos').DataTable({
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.13.4/i18n/es-ES.json"
        },
        "order": [[0, "desc"]],
        "pageLength": 25,
        "responsive": true,
        "columnDefs": [
            { "orderable": false, "targets": -1 } // Deshabilitar orden en columna Acciones
        ]
    });
});
</script>

<?php require_once '../app/views/layouts/footer.php'; ?>
