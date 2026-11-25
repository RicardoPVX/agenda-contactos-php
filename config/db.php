<?php
/**
 * Conexión a la base de datos con PDO.
 *
 * Los datos reales de acceso NO se suben a GitHub: se leen de
 * config/db.local.php (copiar db.local.example.php y llenarlo).
 */

function conectar(): PDO
{
    static $pdo = null;           // una sola conexión por petición
    if ($pdo !== null) {
        return $pdo;
    }

    $cfg = [
        'host' => 'localhost',
        'port' => 3306,
        'name' => 'agenda',
        'user' => 'root',
        'pass' => '',
    ];
    if (is_file(__DIR__ . '/db.local.php')) {
        $cfg = array_merge($cfg, require __DIR__ . '/db.local.php');
    }

    $dsn = "mysql:host={$cfg['host']};port={$cfg['port']};dbname={$cfg['name']};charset=utf8mb4";

    try {
        $pdo = new PDO($dsn, $cfg['user'], $cfg['pass'], [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // errores como excepciones
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // arreglos asociativos
            PDO::ATTR_EMULATE_PREPARES   => false,                  // preparadas reales en MySQL
        ]);
    } catch (PDOException $e) {
        // El detalle técnico va al log del servidor, nunca al usuario.
        error_log('[agenda] Error de conexión: ' . $e->getMessage());
        http_response_code(500);
        exit('<!DOCTYPE html><html lang="es"><meta charset="UTF-8"><title>Error</title>'
           . '<link rel="stylesheet" href="css/styles.css"><main class="contenedor">'
           . '<div class="alerta alerta-error">No fue posible conectar con la base de datos. '
           . 'Intenta de nuevo más tarde.</div></main></html>');
    }

    return $pdo;
}
