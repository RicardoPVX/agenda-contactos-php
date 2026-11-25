<?php
$paginaActual = basename($_SERVER['SCRIPT_NAME']);
$enListado    = in_array($paginaActual, ['index.php', 'ver.php', 'editar.php', 'eliminar.php'], true);
?>
<header class="barra">
    <div class="barra-interior">
        <a class="marca" href="index.php">
            <span class="marca-icono" aria-hidden="true">&#9742;</span>
            Agenda de Contactos
        </a>
        <nav class="menu" aria-label="Menú principal">
            <a href="index.php" class="<?= $enListado ? 'activo' : '' ?>">Contactos</a>
            <a href="crear.php" class="boton boton-primario <?= $paginaActual === 'crear.php' ? 'activo' : '' ?>">+ Nuevo contacto</a>
        </nav>
    </div>
</header>
