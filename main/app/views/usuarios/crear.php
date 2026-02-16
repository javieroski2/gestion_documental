<?php $titulo = 'Crear Usuario'; ?>
<?php require_once '../app/views/layouts/header.php'; ?>
<?php require_once '../app/views/layouts/navbar.php'; ?>
<?php require_once '../app/views/layouts/sidebar.php'; ?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1><i class="fas fa-user-plus"></i> Crear Usuario</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?php echo URL_BASE; ?>dashboard">Inicio</a></li>
                        <li class="breadcrumb-item"><a href="<?php echo URL_BASE; ?>usuario">Usuarios</a></li>
                        <li class="breadcrumb-item active">Crear</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-8 offset-md-2">
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title">Formulario de Nuevo Usuario</h3>
                        </div>
                        <form action="<?php echo URL_BASE; ?>usuario/crear" method="POST" enctype="multipart/form-data">
                            <div class="card-body">
                                
                                <div class="form-group">
                                    <label for="nombre">Nombre <span class="text-danger">*</span></label>
                                    <input type="text" name="nombre" id="nombre" class="form-control <?php echo isset($errors['nombre']) ? 'is-invalid' : ''; ?>" 
                                           value="<?php echo $data['nombre'] ?? ''; ?>" 
                                           placeholder="Ej: Juan" required>
                                    <?php if (isset($errors['nombre'])): ?>
                                    <div class="invalid-feedback"><?php echo $errors['nombre']; ?></div>
                                    <?php endif; ?>
                                </div>

                                <div class="form-group">
                                    <label for="apellidos">Apellidos <span class="text-danger">*</span></label>
                                    <input type="text" name="apellidos" id="apellidos" class="form-control <?php echo isset($errors['apellidos']) ? 'is-invalid' : ''; ?>" 
                                           value="<?php echo $data['apellidos'] ?? ''; ?>" 
                                           placeholder="Ej: Pérez González" required>
                                    <?php if (isset($errors['apellidos'])): ?>
                                    <div class="invalid-feedback"><?php echo $errors['apellidos']; ?></div>
                                    <?php endif; ?>
                                </div>

                                <div class="form-group">
                                    <label for="cargo">Cargo/Puesto</label>
                                    <input type="text" name="cargo" id="cargo" class="form-control" 
                                           value="<?php echo $data['cargo'] ?? ''; ?>" 
                                           placeholder="Ej: Gerente de Operaciones">
                                    <small class="form-text text-muted">Puesto o cargo en la organización</small>
                                </div>

                                <div class="form-group">
                                    <label for="email">Email <span class="text-danger">*</span></label>
                                    <input type="email" name="email" id="email" class="form-control <?php echo isset($errors['email']) ? 'is-invalid' : ''; ?>" 
                                           value="<?php echo $data['email'] ?? ''; ?>" required>
                                    <?php if (isset($errors['email'])): ?>
                                    <div class="invalid-feedback"><?php echo $errors['email']; ?></div>
                                    <?php endif; ?>
                                </div>

                                <div class="form-group">
                                    <label for="foto">Foto de Perfil</label>
                                    <div class="custom-file">
                                        <input type="file" name="foto" id="foto" class="custom-file-input" accept="image/*">
                                        <label class="custom-file-label" for="foto">Seleccionar imagen...</label>
                                    </div>
                                    <small class="form-text text-muted">Formatos: JPG, PNG. Tamaño máximo: 2 MB</small>
                                </div>

                                <div class="form-group">
                                    <label for="password">Contraseña <span class="text-danger">*</span></label>
                                    <input type="password" name="password" id="password" class="form-control <?php echo isset($errors['password']) ? 'is-invalid' : ''; ?>" 
                                           required minlength="6">
                                    <small class="form-text text-muted">Mínimo 6 caracteres</small>
                                    <?php if (isset($errors['password'])): ?>
                                    <div class="invalid-feedback"><?php echo $errors['password']; ?></div>
                                    <?php endif; ?>
                                </div>

                                <div class="form-group">
                                    <label for="rol_id">Rol <span class="text-danger">*</span></label>
                                    <select name="rol_id" id="rol_id" class="form-control <?php echo isset($errors['rol_id']) ? 'is-invalid' : ''; ?>" required>
                                        <option value="">Seleccione un rol</option>
                                        <?php foreach ($roles as $rol): ?>
                                        <option value="<?php echo $rol['id']; ?>" <?php echo (isset($data['rol_id']) && $data['rol_id'] == $rol['id']) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($rol['nombre']); ?>
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <?php if (isset($errors['rol_id'])): ?>
                                    <div class="invalid-feedback"><?php echo $errors['rol_id']; ?></div>
                                    <?php endif; ?>
                                </div>

                                <div class="form-group">
                                    <label for="telefono">Teléfono</label>
                                    <input type="text" name="telefono" id="telefono" class="form-control" 
                                           value="<?php echo $data['telefono'] ?? ''; ?>">
                                </div>

                                <div class="form-group">
                                    <label for="estado">Estado</label>
                                    <select name="estado" id="estado" class="form-control">
                                        <option value="1" <?php echo (isset($data['estado']) && $data['estado'] == 1) ? 'selected' : ''; ?>>Activo</option>
                                        <option value="0" <?php echo (isset($data['estado']) && $data['estado'] == 0) ? 'selected' : ''; ?>>Inactivo</option>
                                    </select>
                                </div>

                            </div>
                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Guardar Usuario
                                </button>
                                <a href="<?php echo URL_BASE; ?>usuario" class="btn btn-secondary">
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

<script>
// Mostrar nombre del archivo seleccionado
$('#foto').on('change', function() {
    var fileName = $(this).val().split('\\').pop();
    $(this).next('.custom-file-label').html(fileName);
});
</script>

<?php require_once '../app/views/layouts/footer.php'; ?>
