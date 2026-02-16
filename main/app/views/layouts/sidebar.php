<!-- Main Sidebar -->
<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="<?php echo URL_BASE; ?>dashboard" class="brand-link">
        <i class="fas fa-file-alt brand-image ml-2"></i>
        <span class="brand-text font-weight-light">Gestión Documental</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
        <!-- User Panel -->
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <div class="image">
                <?php if (!empty($_SESSION['foto'])): ?>
                    <img src="<?php echo URL_BASE; ?>public/uploads/usuarios/<?php echo $_SESSION['foto']; ?>" 
                         class="img-circle elevation-2" alt="<?php echo $_SESSION['nombre']; ?>" 
                         style="width: 40px; height: 40px; object-fit: cover;">
                <?php else: ?>
                    <i class="fas fa-user-circle fa-2x text-white"></i>
                <?php endif; ?>
            </div>
            <div class="info">
                <a href="#" class="d-block">
                    <?php echo $_SESSION['nombre'] ?? 'Usuario'; ?>
                    <?php if (!empty($_SESSION['apellidos'])): ?>
                        <?php echo $_SESSION['apellidos']; ?>
                    <?php endif; ?>
                </a>
                <small class="text-muted"><?php echo $_SESSION['rol_nombre'] ?? ''; ?></small>
            </div>
        </div>

        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                
                <!-- Dashboard -->
                <li class="nav-item">
                    <a href="<?php echo URL_BASE; ?>dashboard" class="nav-link">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>Dashboard</p>
                    </a>
                </li>

                <?php $rol_id = $_SESSION['rol_id'] ?? 0; ?>

                <!-- Gestión de Usuarios (Super Admin y Admin) -->
                <?php if ($rol_id == ROL_SUPER_ADMIN || $rol_id == ROL_ADMIN): ?>
                <li class="nav-item">
                    <a href="<?php echo URL_BASE; ?>usuario" class="nav-link">
                        <i class="nav-icon fas fa-users"></i>
                        <p>
                            Usuarios
                            <?php if ($rol_id == ROL_SUPER_ADMIN): ?>
                            <span class="right badge badge-danger">Admin</span>
                            <?php endif; ?>
                        </p>
                    </a>
                </li>

                <!-- Categorías -->
                <li class="nav-item">
                    <a href="<?php echo URL_BASE; ?>categoria" class="nav-link">
                        <i class="nav-icon fas fa-tags"></i>
                        <p>Categorías</p>
                    </a>
                </li>
                <?php endif; ?>

                <!-- Documentos -->
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-folder-open"></i>
                        <p>
                            Documentos
                            <i class="fas fa-angle-left right"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <?php if ($rol_id == ROL_GESTOR || $rol_id == ROL_SUPER_ADMIN || $rol_id == ROL_ADMIN): ?>
                        <li class="nav-item">
                            <a href="<?php echo URL_BASE; ?>documento/subir" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Subir Documento</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?php echo URL_BASE; ?>documento/mis-documentos" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Mis Documentos</p>
                            </a>
                        </li>
                        <?php endif; ?>
                        
                        <?php if ($rol_id == ROL_VALIDADOR || $rol_id == ROL_SUPER_ADMIN || $rol_id == ROL_ADMIN): ?>
                        <li class="nav-item">
                            <a href="<?php echo URL_BASE; ?>documento/pendientes" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>
                                    Pendientes de Validar
                                    <span class="right badge badge-warning">New</span>
                                </p>
                            </a>
                        </li>
                        <?php endif; ?>
                        
                        <li class="nav-item">
                            <a href="<?php echo URL_BASE; ?>documento" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Todos los Documentos</p>
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- Reportes -->
                <li class="nav-item">
                    <a href="<?php echo URL_BASE; ?>reporte" class="nav-link">
                        <i class="nav-icon fas fa-chart-bar"></i>
                        <p>Reportes</p>
                    </a>
                </li>

                <?php if ($rol_id == ROL_SUPER_ADMIN): ?>
                <!-- Configuración (Solo Super Admin) -->
                <li class="nav-header">CONFIGURACIÓN</li>
                <li class="nav-item">
                    <a href="<?php echo URL_BASE; ?>configuracion" class="nav-link">
                        <i class="nav-icon fas fa-cogs"></i>
                        <p>Configuración</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?php echo URL_BASE; ?>auditoria" class="nav-link">
                        <i class="nav-icon fas fa-history"></i>
                        <p>Auditoría</p>
                    </a>
                </li>
                <?php endif; ?>

                <!-- Cerrar Sesión -->
                <li class="nav-header">CUENTA</li>
                <li class="nav-item">
                    <a href="<?php echo URL_BASE; ?>auth/logout" class="nav-link text-danger">
                        <i class="nav-icon fas fa-sign-out-alt"></i>
                        <p>Cerrar Sesión</p>
                    </a>
                </li>

            </ul>
        </nav>
    </div>
</aside>
