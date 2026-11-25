<?php
/**
 * Formulario compartido por crear.php y editar.php.
 *
 * @var string     $accion     guardar.php | actualizar.php
 * @var array      $datos      valores a mostrar en los campos
 * @var array      $errores    errores de validación del servidor
 * @var array|null $contacto   contacto actual (solo en edición)
 */
$esEdicion = isset($contacto);
$err = fn(string $campo) => isset($errores[$campo])
    ? '<p class="campo-error">' . e($errores[$campo]) . '</p>' : '';
$cls = fn(string $campo) => isset($errores[$campo]) ? ' con-error' : '';
?>
<form class="tarjeta formulario" action="<?= e($accion) ?>" method="post" enctype="multipart/form-data">
    <?= csrf_campo() ?>
    <?php if ($esEdicion): ?>
        <input type="hidden" name="id" value="<?= (int) $contacto['id'] ?>">
    <?php endif; ?>

    <div class="rejilla-2">
        <div class="campo<?= $cls('nombre') ?>">
            <label for="nombre">Nombre <span class="req">*</span></label>
            <input type="text" id="nombre" name="nombre" required maxlength="60"
                   value="<?= e($datos['nombre'] ?? '') ?>" autocomplete="given-name">
            <?= $err('nombre') ?>
        </div>

        <div class="campo<?= $cls('apellido') ?>">
            <label for="apellido">Apellido <span class="req">*</span></label>
            <input type="text" id="apellido" name="apellido" required maxlength="60"
                   value="<?= e($datos['apellido'] ?? '') ?>" autocomplete="family-name">
            <?= $err('apellido') ?>
        </div>

        <div class="campo<?= $cls('telefono') ?>">
            <label for="telefono">Teléfono <span class="req">*</span></label>
            <input type="tel" id="telefono" name="telefono" required maxlength="20"
                   pattern="\+?[0-9\s\-()]{7,20}" title="De 7 a 20 dígitos; se permiten espacios, guiones y paréntesis"
                   value="<?= e($datos['telefono'] ?? '') ?>" placeholder="871 123 4567" autocomplete="tel">
            <?= $err('telefono') ?>
        </div>

        <div class="campo<?= $cls('email') ?>">
            <label for="email">Correo electrónico</label>
            <input type="email" id="email" name="email" maxlength="100"
                   value="<?= e($datos['email'] ?? '') ?>" placeholder="nombre@dominio.com" autocomplete="email">
            <?= $err('email') ?>
        </div>
    </div>

    <div class="campo<?= $cls('direccion') ?>">
        <label for="direccion">Dirección</label>
        <input type="text" id="direccion" name="direccion" maxlength="150"
               value="<?= e($datos['direccion'] ?? '') ?>" autocomplete="street-address">
        <?= $err('direccion') ?>
    </div>

    <div class="campo<?= $cls('notas') ?>">
        <label for="notas">Notas</label>
        <textarea id="notas" name="notas" rows="3" maxlength="500"><?= e($datos['notas'] ?? '') ?></textarea>
        <?= $err('notas') ?>
    </div>

    <div class="campo<?= $cls('foto') ?>">
        <label for="foto">Foto <?= $esEdicion ? '' : '<span class="req">*</span>' ?></label>
        <div class="foto-campo">
            <?php if ($esEdicion): ?>
                <img class="avatar avatar-md" src="<?= e(URL_FOTOS . $contacto['foto']) ?>" alt="Foto actual">
            <?php endif; ?>
            <div>
                <input type="file" id="foto" name="foto" accept="image/jpeg,image/png,image/webp,image/gif"
                       <?= $esEdicion ? '' : 'required' ?>>
                <p class="ayuda">
                    JPG, PNG, WEBP o GIF · máximo 2 MB<?= $esEdicion ? ' · déjalo vacío para conservar la foto actual' : '' ?>
                </p>
            </div>
        </div>
        <?= $err('foto') ?>
    </div>

    <div class="acciones">
        <a class="boton" href="<?= $esEdicion ? 'ver.php?id=' . (int) $contacto['id'] : 'index.php' ?>">Cancelar</a>
        <button type="submit" class="boton boton-primario">
            <?= $esEdicion ? 'Guardar cambios' : 'Guardar contacto' ?>
        </button>
    </div>
</form>
