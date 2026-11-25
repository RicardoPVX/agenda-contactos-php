<?php
/**
 * UPDATE (vista) · Formulario con los datos actuales del contacto.
 */
require_once __DIR__ . '/includes/funciones.php';

$contacto = contacto_desde_get();          // SELECT ... WHERE id = :id

// Si actualizar.php encontró errores, se muestran los datos que escribió el usuario.
$formulario = tomar_formulario();
$datos      = $formulario['datos'] ?: $contacto;
$errores    = $formulario['errores'];
$accion     = 'actualizar.php';

$titulo = 'Editar contacto';
require __DIR__ . '/includes/header.php';
?>

<div class="encabezado">
    <div>
        <h1>Editar contacto</h1>
        <p class="sutil"><?= e($contacto['nombre'] . ' ' . $contacto['apellido']) ?></p>
    </div>
</div>

<?php require __DIR__ . '/includes/formulario.php'; ?>

<?php require __DIR__ . '/includes/footer.php'; ?>
