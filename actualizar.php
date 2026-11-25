<?php
/**
 * UPDATE (lógica) · Valida y ejecuta el UPDATE.
 */
require_once __DIR__ . '/includes/funciones.php';

$id = leer_id($_POST);
$volverA = $id ? 'editar.php?id=' . $id : 'index.php';

exigir_post_valido($volverA);

// 1. El contacto debe existir
try {
    $actual = $id ? obtener_contacto($id) : null;
} catch (PDOException $ex) {
    error_log('[agenda] ' . $ex->getMessage());
    $actual = null;
}
if ($actual === null) {
    flash('error', 'El contacto que intentas editar no existe.');
    redirigir('index.php');
}

// 2. Validación en servidor (la foto es opcional al editar)
$datos   = limpiar_entrada($_POST);
$errores = validar_contacto($datos);

$archivo = $_FILES['foto'] ?? null;
if ($errorFoto = validar_foto($archivo, false)) {
    $errores['foto'] = $errorFoto;
}

if ($errores) {
    guardar_formulario($datos, $errores);
    flash('error', 'Revisa los campos marcados.');
    redirigir($volverA);
}

// 3. Foto nueva (si se envió)
$fotoNueva = null;
if (se_envio_foto($archivo)) {
    $fotoNueva = mover_foto($archivo);
    if ($fotoNueva === null) {
        guardar_formulario($datos, ['foto' => 'No se pudo guardar la foto en el servidor.']);
        redirigir($volverA);
    }
}

// 4. UPDATE con sentencia preparada
try {
    $stmt = conectar()->prepare(
        'UPDATE contactos
            SET nombre = :nombre, apellido = :apellido, telefono = :telefono, foto = :foto,
                email = :email, direccion = :direccion, notas = :notas
          WHERE id = :id'
    );
    $stmt->execute([
        ':nombre'    => $datos['nombre'],
        ':apellido'  => $datos['apellido'],
        ':telefono'  => $datos['telefono'],
        ':foto'      => $fotoNueva ?? $actual['foto'],
        ':email'     => $datos['email']     !== '' ? $datos['email']     : null,
        ':direccion' => $datos['direccion'] !== '' ? $datos['direccion'] : null,
        ':notas'     => $datos['notas']     !== '' ? $datos['notas']     : null,
        ':id'        => $id,
    ]);

    if ($fotoNueva !== null) {
        borrar_foto($actual['foto']);       // la anterior ya no se usa
    }

    flash('exito', 'Contacto actualizado correctamente.');
    redirigir('ver.php?id=' . $id);
} catch (PDOException $ex) {
    borrar_foto($fotoNueva);
    error_log('[agenda] UPDATE: ' . $ex->getMessage());
    guardar_formulario($datos, []);
    flash('error', 'No se pudieron guardar los cambios. Intenta de nuevo.');
    redirigir($volverA);
}
