<?php
/**
 * READ individual · Detalle de un contacto.
 */
require_once __DIR__ . '/includes/funciones.php';

$c = contacto_desde_get();                 // SELECT * FROM contactos WHERE id = :id

$titulo = $c['nombre'] . ' ' . $c['apellido'];
require __DIR__ . '/includes/header.php';
?>

<p class="miga"><a href="index.php">&larr; Volver a contactos</a></p>

<article class="tarjeta detalle">
    <img class="avatar avatar-xl" src="<?= e(URL_FOTOS . $c['foto']) ?>"
         alt="Foto de <?= e($c['nombre'] . ' ' . $c['apellido']) ?>">

    <div class="detalle-cuerpo">
        <h1><?= e($c['nombre'] . ' ' . $c['apellido']) ?></h1>

        <dl class="datos">
            <dt>Teléfono</dt>
            <dd><a href="tel:<?= e($c['telefono']) ?>"><?= e($c['telefono']) ?></a></dd>

            <dt>Correo</dt>
            <dd><?= $c['email'] ? '<a href="mailto:' . e($c['email']) . '">' . e($c['email']) . '</a>' : '<span class="sutil">Sin registrar</span>' ?></dd>

            <dt>Dirección</dt>
            <dd><?= $c['direccion'] ? e($c['direccion']) : '<span class="sutil">Sin registrar</span>' ?></dd>

            <dt>Notas</dt>
            <dd><?= $c['notas'] ? nl2br(e($c['notas'])) : '<span class="sutil">Sin notas</span>' ?></dd>

            <dt>Registrado</dt>
            <dd class="sutil"><?= e(date('d/m/Y H:i', strtotime($c['creado_en']))) ?></dd>
        </dl>

        <div class="acciones acciones-izq">
            <a class="boton boton-primario" href="editar.php?id=<?= (int) $c['id'] ?>">Editar</a>
            <a class="boton boton-peligro" href="eliminar.php?id=<?= (int) $c['id'] ?>">Eliminar</a>
        </div>
    </div>
</article>

<?php require __DIR__ . '/includes/footer.php'; ?>
