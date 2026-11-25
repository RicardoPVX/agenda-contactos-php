<?php /** @var string $titulo  lo define cada página antes de incluir este archivo */ ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($titulo ?? 'Inicio') ?> · Agenda de Contactos</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
<?php require __DIR__ . '/menu.php'; ?>
<main class="contenedor">
<?php if ($aviso = tomar_flash()): ?>
    <div class="alerta alerta-<?= e($aviso['tipo']) ?>" role="status"><?= e($aviso['mensaje']) ?></div>
<?php endif; ?>
