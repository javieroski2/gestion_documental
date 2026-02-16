<?php $titulo = 'Editar Categoría'; ?>
<?php require_once '../app/views/layouts/header.php'; ?>
<?php require_once '../app/views/layouts/navbar.php'; ?>
<?php require_once '../app/views/layouts/sidebar.php'; ?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1><i class="fas fa-edit"></i> Editar Categoría</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?php echo URL_BASE; ?>dashboard">Inicio</a></li>
                        <li class="breadcrumb-item"><a href="<?php echo URL_BASE; ?>categoria">Categorías</a></li>
                        <li class="breadcrumb-item active">Editar</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-8 offset-md-2">
                    <div class="card card-warning">
                        <div class="card-header">
                            <h3 class="card-title">Editar Información de la Categoría</h3>
                        </div>
                        <form action="<?php echo URL_BASE; ?>categoria/editar/<?php echo $categoria['id']; ?>" method="POST">
                            <div class="card-body">
                                
                                <div class="form-group">
                                    <label for="nombre">Nombre de la Categoría <span class="text-danger">*</span></label>
                                    <input type="text" name="nombre" id="nombre" 
                                           class="form-control <?php echo isset($errors['nombre']) ? 'is-invalid' : ''; ?>" 
                                           value="<?php echo htmlspecialchars($categoria['nombre']); ?>" 
                                           required>
                                    <?php if (isset($errors['nombre'])): ?>
                                    <div class="invalid-feedback"><?php echo $errors['nombre']; ?></div>
                                    <?php endif; ?>
                                </div>

                                <div class="form-group">
                                    <label for="descripcion">Descripción</label>
                                    <textarea name="descripcion" id="descripcion" class="form-control" rows="4"><?php echo htmlspecialchars($categoria['descripcion'] ?? ''); ?></textarea>
                                    <small class="form-text text-muted">Describe el tipo de documentos que incluye esta categoría</small>
                                </div>

                                <div class="form-group">
                                    <label for="estado">Estado</label>
                                    <select name="estado" id="estado" class="form-control">
                                        <option value="1" <?php echo ($categoria['estado'] == 1) ? 'selected' : ''; ?>>Activa</option>
                                        <option value="0" <?php echo ($categoria['estado'] == 0) ? 'selected' : ''; ?>>Inactiva</option>
                                    </select>
                                    <small class="form-text text-muted">Las categorías inactivas no aparecerán para seleccionar al subir documentos</small>
                                </div>

                            </div>
                            <div class="card-footer">
                                <button type="submit" class="btn btn-warning">
                                    <i class="fas fa-save"></i> Actualizar Categoría
                                </button>
                                <a href="<?php echo URL_BASE; ?>categoria" class="btn btn-secondary">
                                    <i class="fas fa-times"></i> Cancelar
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<?php require_once '../app/views/layouts/footer.php'; ?>
