<!-- sidebar.php -->
<!-- Modal Perfil -->
<div class="modal fade" id="perfilModal" tabindex="-1" aria-labelledby="perfilModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="perfilModalLabel">Perfil de Usuario</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p><strong>Nombre:</strong> <?php echo htmlspecialchars($nombreUsuario); ?></p>
                <p><strong>Correo Electrónico:</strong> <?php echo htmlspecialchars($correoUsuario); ?></p>
                <p><strong>Tipo de Usuario:</strong> <?php echo htmlspecialchars($tipoUsuario); ?></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<div class="topbar">
    <button class="toggle-sidebar-btn"><i class="bi bi-list"></i></button>
    <img src="" alt="">
    <div class="user-menu">
        <button class="btn btn-user" data-toggle="modal" data-target="#perfilModal">
            <?php echo strtoupper(substr($nombreUsuario, 0, 1)); ?>
        </button>
        <a href="../controllers/cerrarSesion.php" class="btn btn-logout"><i class="bi bi-box-arrow-right"></i></a>
    </div>
</div>

<div class="sidebar" id="sidebar">
    <div class="sidebar-logo">
        <img src="../img/logo3.png" alt="SGID">
        <h4><?php echo htmlspecialchars($nombreUsuario); ?></h4>
        <p>Web SGID</p>
    </div>
    <ul class="sidebar-menu">
        <li class="menu-item">
            <a href="#" class="nav-link toggle-menu"><i class="bi bi-archive"></i><span>Inicio</span><i class="arrow bi bi-chevron-down"></i></a>
            <ul class="submenu">
                <li><a href="viewPageHome.php" class="nav-link"><span>Calendario</span></a></li>
                <li><a href="viewPage2ListaDisp.php" class="nav-link"><span>Agregar</span></a></li>
                <li><a href="viewPage3ListaRep.php" class="nav-link"><span>Reparaciones</span></a></li>
                <li><a href="viewPage2.1ListaDispNR.php" class="nav-link"><span>No reparados</span></a></li>
                <li><a href="viewPage7Seguimiento.php" class="nav-link"><span>Seguimiento</span></a></li>
               <!-- <li><a href="viewPage4Grafics.php" class="nav-link"><span>Graficos</span></a></li>-->
            </ul>
        </li>
        <?php if ($tipoUsuario === 'administrador') { ?>
        <li class="menu-item">
            <a href="#" class="nav-link toggle-menu"><i class="bi bi-briefcase"></i><span>Contenido</span><i class="arrow bi bi-chevron-down"></i></a>
            <ul class="submenu">
                <li><a href="viewPage5historial.php" class="nav-link"><span>Historial</span></a></li>
                <li><a href="viewPage8usuarios.php" class="nav-link"><span>Usuarios</span></a></li>
                <li><a href="viewPage9Establecimientos.php" class="nav-link"><span>Establecimientos</span></a></li>
                <li><a href="viewPage10marcas.php" class="nav-link"><span>Marcas</span></a></li>
            </ul>
        </li>
        <?php } ?>
    </ul>
    <div class="sidebar-profile">
        <div class="user-initials"><?php echo strtoupper(substr($nombreUsuario, 0, 1)); ?></div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
<script src="../js/sidebar.js"></script>
