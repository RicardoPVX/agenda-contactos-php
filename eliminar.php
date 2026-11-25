<?php
/**
 * DELETE · Eliminación con confirmación previa.
 *   GET  eliminar.php?id=N  → pantalla de confirmación
 *   POST eliminar.php       → ejecuta el DELETE
 */
require_once __DIR__ . '/includes/funciones.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    exigir_post_valido();

    $id = leer_id($_POST);
    try {
        $contacto = $id ? obtener_contacto($id) : null;
        if ($contacto === null) {
            flash('error', 'El contacto ya no existe.');
            redirigir('index.php');
        }

        $stmt = conectar()->prepare('DELETE FROM contactos WHERE id = :id');
        $stmt->execute([':id' => $id]);

        borrar_foto($contacto['foto']);
        flash('exito', 'Se eliminó a ' . $contacto['nombre'] . ' ' . $contacto['apellido'] . ' de la agenda.');
    } catch (PDOException $ex) {
        error_log('[agenda] DELETE: ' . $ex->getMessage());
        flash('error', 'No se pudo eliminar el contacto. Intenta de nuevo.');
    }
    redirigir('index.php');
}

// GET: pedir confirmación
$c = contacto_desde_get();

$titulo = 'Eliminar contacto';
require __DIR__ . '/includes/header.php';
?>

<div class="tarjeta confirmacion">
    <img class="avatar avatar-lg" src="<?= e(URL_FOTOS . $c['foto']) ?>" alt="">
    <h1>¿Eliminar este contacto?</h1>
    <p>
        Vas a eliminar a <strong><?= e($c['nombre'] . ' ' . $c['apellido']) ?></strong>
        (<?= e($c['telefono']) ?>). Esta acción no se puede deshacer.
    </p>

    <form action="eliminar.php" method="post" class="acciones acciones-centro">
        <?= csrf_campo() ?>
        <input type="hidden" name="id" value="<?= (int) $c['id'] ?>">
        <a class="boton" href="ver.php?id=<?= (int) $c['id'] ?>">Cancelar</a>
        <button type="submit" class="boton boton-peligro-solido">Sí, eliminar</button>
    </form>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
