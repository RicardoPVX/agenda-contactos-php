<?php
/**
 * CREATE (lógica) · Valida y ejecuta el INSERT.
 */
require_once __DIR__ . '/includes/funciones.php';

exigir_post_valido('crear.php');

// 1. Validación en servidor
$datos   = limpiar_entrada($_POST);
$errores = validar_contacto($datos);

$archivo = $_FILES['foto'] ?? null;
if ($errorFoto = validar_foto($archivo, true)) {
    $errores['foto'] = $errorFoto;
}

if ($errores) {
    guardar_formulario($datos, $errores);
    flash('error', 'Revisa los campos marcados.');
    redirigir('crear.php');
}

// 2. Guardar la foto en /archivos
$foto = mover_foto($archivo);
if ($foto === null) {
    guardar_formulario($datos, ['foto' => 'No se pudo guardar la foto en el servidor.']);
    redirigir('crear.php');
}

// 3. INSERT con sentencia preparada
try {
    $pdo  = conectar();
    $stmt = $pdo->prepare(
        'INSERT INTO contactos (nombre, apellido, telefono, foto, email, direccion, notas)
         VALUES (:nombre, :apellido, :telefono, :foto, :email, :direccion, :notas)'
    );
    $stmt->execute([
        ':nombre'    => $datos['nombre'],
        ':apellido'  => $datos['apellido'],
        ':telefono'  => $datos['telefono'],
        ':foto'      => $foto,
        ':email'     => $datos['email']     !== '' ? $datos['email']     : null,
        ':direccion' => $datos['direccion'] !== '' ? $datos['direccion'] : null,
        ':notas'     => $datos['notas']     !== '' ? $datos['notas']     : null,
    ]);

    flash('exito', 'Contacto creado correctamente.');
    redirigir('ver.php?id=' . (int) $pdo->lastInsertId());
} catch (PDOException $ex) {
    borrar_foto($foto);
    error_log('[agenda] INSERT: ' . $ex->getMessage());   // detalle solo en el log
    guardar_formulario($datos, []);
    flash('error', 'No se pudo guardar el contacto. Intenta de nuevo.');
    redirigir('crear.php');
}
