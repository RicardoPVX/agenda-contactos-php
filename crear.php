<?php
/**
 * CREATE (vista) · Formulario para un contacto nuevo.
 */
require_once __DIR__ . '/includes/funciones.php';

// Si guardar.php encontró errores, regresa los datos y los mensajes.
$formulario = tomar_formulario();
$datos      = $formulario['datos'];
$errores    = $formulario['errores'];
$accion     = 'guardar.php';

$titulo = 'Nuevo contacto';
require __DIR__ . '/includes/header.php';
?>

<div class="encabezado">
    <div>
        <h1>Nuevo contacto</h1>
        <p class="sutil">Los campos con <span class="req">*</span> son obligatorios.</p>
    </div>
</div>

<?php require __DIR__ . '/includes/formulario.php'; ?>

<?php require __DIR__ . '/includes/footer.php'; ?>
