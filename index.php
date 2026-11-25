<?php
/**
 * READ · Listado y búsqueda de contactos.
 */
require_once __DIR__ . '/includes/funciones.php';

$busqueda  = trim((string) ($_GET['q'] ?? ''));
$contactos = [];
$errorBD   = false;

try {
    if ($busqueda !== '') {
        // Los comodines % y _ que escriba el usuario se tratan como texto normal.
        $patron = '%' . addcslashes($busqueda, '%_\\') . '%';
        $stmt = conectar()->prepare(
            "SELECT id, nombre, apellido, telefono, email, foto
               FROM contactos
              WHERE nombre   LIKE :q1
                 OR apellido LIKE :q2
                 OR telefono LIKE :q3
                 OR email    LIKE :q4
                 OR CONCAT(nombre, ' ', apellido) LIKE :q5
              ORDER BY nombre, apellido"
        );
        $stmt->execute([':q1' => $patron, ':q2' => $patron, ':q3' => $patron, ':q4' => $patron, ':q5' => $patron]);
    } else {
        $stmt = conectar()->prepare(
            'SELECT id, nombre, apellido, telefono, email, foto
               FROM contactos
              ORDER BY nombre, apellido'
        );
        $stmt->execute();
    }
    $contactos = $stmt->fetchAll();
} catch (PDOException $ex) {
    error_log('[agenda] ' . $ex->getMessage());
    $errorBD = true;
}

$titulo = 'Contactos';
require __DIR__ . '/includes/header.php';
?>

<div class="encabezado">
    <div>
        <h1>Contactos</h1>
        <p class="sutil">
            <?php if ($busqueda !== ''): ?>
                <?= count($contactos) ?> resultado(s) para «<?= e($busqueda) ?>»
            <?php else: ?>
                <?= count($contactos) ?> contacto(s) en la agenda
            <?php endif; ?>
        </p>
    </div>
    <form class="buscador" action="index.php" method="get" role="search">
        <input type="search" name="q" value="<?= e($busqueda) ?>"
               placeholder="Buscar por nombre, apellido, teléfono o correo" aria-label="Buscar contactos" maxlength="100">
        <button type="submit" class="boton boton-primario">Buscar</button>
        <?php if ($busqueda !== ''): ?>
            <a class="boton" href="index.php">Limpiar</a>
        <?php endif; ?>
    </form>
</div>

<?php if ($errorBD): ?>
    <div class="alerta alerta-error">No se pudieron cargar los contactos. Intenta de nuevo más tarde.</div>
<?php elseif (!$contactos): ?>
    <div class="tarjeta vacio">
        <?php if ($busqueda !== ''): ?>
            <p>No hay contactos que coincidan con «<?= e($busqueda) ?>».</p>
            <a class="boton" href="index.php">Ver todos</a>
        <?php else: ?>
            <p>Tu agenda está vacía.</p>
            <a class="boton boton-primario" href="crear.php">Agregar el primer contacto</a>
        <?php endif; ?>
    </div>
<?php else: ?>
    <div class="tarjeta tabla-envoltura">
        <table class="tabla">
            <thead>
            <tr>
                <th>Contacto</th>
                <th>Teléfono</th>
                <th class="solo-ancho">Correo</th>
                <th class="col-acciones">Acciones</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($contactos as $c): ?>
                <tr>
                    <td>
                        <a class="persona" href="ver.php?id=<?= (int) $c['id'] ?>">
                            <img class="avatar" src="<?= e(URL_FOTOS . $c['foto']) ?>" alt="" loading="lazy">
                            <span><?= e($c['nombre'] . ' ' . $c['apellido']) ?></span>
                        </a>
                    </td>
                    <td class="col-tel"><a href="tel:<?= e($c['telefono']) ?>"><?= e($c['telefono']) ?></a></td>
                    <td class="solo-ancho"><?= $c['email'] ? e($c['email']) : '<span class="sutil">—</span>' ?></td>
                    <td class="col-acciones">
                        <a class="boton boton-chico" href="ver.php?id=<?= (int) $c['id'] ?>">Ver</a>
                        <a class="boton boton-chico" href="editar.php?id=<?= (int) $c['id'] ?>">Editar</a>
                        <a class="boton boton-chico boton-peligro" href="eliminar.php?id=<?= (int) $c['id'] ?>">Eliminar</a>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>

<?php require __DIR__ . '/includes/footer.php'; ?>
