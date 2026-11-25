<?php
/**
 * Arranque común y funciones de apoyo.
 * Todas las páginas empiezan con:  require_once __DIR__ . '/includes/funciones.php';
 */
declare(strict_types=1);

ini_set('display_errors', '0');   // nunca mostrar errores PHP/SQL al usuario
error_reporting(E_ALL);           // pero sí registrarlos en el log del servidor

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config/db.php';

const DIR_FOTOS      = __DIR__ . '/../archivos/';
const URL_FOTOS      = 'archivos/';
const FOTO_MAX_BYTES = 2 * 1024 * 1024; // 2 MB
const TIPOS_FOTO     = [
    'image/jpeg' => 'jpg',
    'image/png'  => 'png',
    'image/webp' => 'webp',
    'image/gif'  => 'gif',
];

/* ---------------------------------------------------------------
 *  Salida segura
 * ------------------------------------------------------------- */

/** Escapa cualquier dato antes de imprimirlo en HTML (evita XSS). */
function e(?string $valor): string
{
    return htmlspecialchars((string) $valor, ENT_QUOTES, 'UTF-8');
}

function redirigir(string $url): void
{
    header('Location: ' . $url);
    exit;
}

/* ---------------------------------------------------------------
 *  Mensajes flash y datos del formulario entre redirecciones
 * ------------------------------------------------------------- */

function flash(string $tipo, string $mensaje): void
{
    $_SESSION['flash'] = ['tipo' => $tipo, 'mensaje' => $mensaje];
}

function tomar_flash(): ?array
{
    $f = $_SESSION['flash'] ?? null;
    unset($_SESSION['flash']);
    return $f;
}

function guardar_formulario(array $datos, array $errores): void
{
    $_SESSION['formulario'] = ['datos' => $datos, 'errores' => $errores];
}

function tomar_formulario(): array
{
    $f = $_SESSION['formulario'] ?? ['datos' => [], 'errores' => []];
    unset($_SESSION['formulario']);
    return $f;
}

/* ---------------------------------------------------------------
 *  POST + token CSRF
 * ------------------------------------------------------------- */

function csrf_token(): string
{
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf'];
}

function csrf_campo(): string
{
    return '<input type="hidden" name="csrf" value="' . e(csrf_token()) . '">';
}

/** Las páginas de lógica solo aceptan POST con un token válido. */
function exigir_post_valido(string $volverA = 'index.php'): void
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        redirigir('index.php');
    }

    // Si el archivo supera post_max_size, PHP vacía $_POST y $_FILES.
    if (empty($_POST) && (int) ($_SERVER['CONTENT_LENGTH'] ?? 0) > 0) {
        flash('error', 'El archivo es demasiado grande. La foto debe pesar máximo 2 MB.');
        redirigir($volverA);
    }

    if (!hash_equals(csrf_token(), (string) ($_POST['csrf'] ?? ''))) {
        flash('error', 'La sesión expiró. Vuelve a intentarlo.');
        redirigir($volverA);
    }
}

/** Lee un id entero positivo; devuelve null si no es válido. */
function leer_id(array $origen): ?int
{
    $id = filter_var($origen['id'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
    return $id === false ? null : $id;
}

/* ---------------------------------------------------------------
 *  Validación en servidor
 * ------------------------------------------------------------- */

function limpiar_entrada(array $origen): array
{
    $datos = [];
    foreach (['nombre', 'apellido', 'telefono', 'email', 'direccion', 'notas'] as $campo) {
        $datos[$campo] = trim((string) ($origen[$campo] ?? ''));
    }
    return $datos;
}

function validar_contacto(array $d): array
{
    $errores = [];

    // Texto con codificación inválida: se rechaza antes de llegar a MySQL.
    foreach ($d as $campo => $valor) {
        if (!mb_check_encoding($valor, 'UTF-8')) {
            $errores[$campo] = 'El texto contiene caracteres no válidos.';
        }
    }
    if ($errores) {
        return $errores;
    }

    if ($d['nombre'] === '') {
        $errores['nombre'] = 'El nombre es obligatorio.';
    } elseif (mb_strlen($d['nombre']) > 60) {
        $errores['nombre'] = 'El nombre admite máximo 60 caracteres.';
    }

    if ($d['apellido'] === '') {
        $errores['apellido'] = 'El apellido es obligatorio.';
    } elseif (mb_strlen($d['apellido']) > 60) {
        $errores['apellido'] = 'El apellido admite máximo 60 caracteres.';
    }

    if ($d['telefono'] === '') {
        $errores['telefono'] = 'El teléfono es obligatorio.';
    } elseif (!preg_match('/^\+?[0-9\s\-()]{7,20}$/', $d['telefono'])) {
        $errores['telefono'] = 'Teléfono no válido: usa de 7 a 20 dígitos, espacios, guiones o paréntesis.';
    }

    if ($d['email'] !== '') {
        if (!filter_var($d['email'], FILTER_VALIDATE_EMAIL)) {
            $errores['email'] = 'El correo no tiene un formato válido (ej. nombre@dominio.com).';
        } elseif (mb_strlen($d['email']) > 100) {
            $errores['email'] = 'El correo admite máximo 100 caracteres.';
        }
    }

    if (mb_strlen($d['direccion']) > 150) {
        $errores['direccion'] = 'La dirección admite máximo 150 caracteres.';
    }

    if (mb_strlen($d['notas']) > 500) {
        $errores['notas'] = 'Las notas admiten máximo 500 caracteres.';
    }

    return $errores;
}

/* ---------------------------------------------------------------
 *  Foto del contacto
 * ------------------------------------------------------------- */

function se_envio_foto(?array $archivo): bool
{
    return $archivo !== null && ($archivo['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE;
}

/** Devuelve el mensaje de error, o null si la foto es válida. */
function validar_foto(?array $archivo, bool $obligatoria): ?string
{
    if (!se_envio_foto($archivo)) {
        return $obligatoria ? 'La foto es obligatoria.' : null;
    }
    if ($archivo['error'] === UPLOAD_ERR_INI_SIZE || $archivo['error'] === UPLOAD_ERR_FORM_SIZE
        || $archivo['size'] > FOTO_MAX_BYTES) {
        return 'La foto debe pesar máximo 2 MB.';
    }
    if ($archivo['error'] !== UPLOAD_ERR_OK) {
        return 'No se pudo subir la foto. Inténtalo de nuevo.';
    }
    // Se revisa el contenido real del archivo, no solo la extensión.
    $info = @getimagesize($archivo['tmp_name']);
    if ($info === false || !isset(TIPOS_FOTO[$info['mime']])) {
        return 'La foto debe ser una imagen JPG, PNG, WEBP o GIF.';
    }
    return null;
}

/** Mueve la foto a /archivos con un nombre aleatorio. Devuelve el nombre o null. */
function mover_foto(array $archivo): ?string
{
    $info   = getimagesize($archivo['tmp_name']);
    $nombre = bin2hex(random_bytes(12)) . '.' . TIPOS_FOTO[$info['mime']];
    return move_uploaded_file($archivo['tmp_name'], DIR_FOTOS . $nombre) ? $nombre : null;
}

function borrar_foto(?string $nombre): void
{
    if ($nombre && preg_match('/^[a-f0-9]{24}\.(jpg|png|webp|gif)$/', $nombre) && is_file(DIR_FOTOS . $nombre)) {
        unlink(DIR_FOTOS . $nombre);
    }
}

/* ---------------------------------------------------------------
 *  Consultas comunes
 * ------------------------------------------------------------- */

/** SELECT de un contacto por id con sentencia preparada. */
function obtener_contacto(int $id): ?array
{
    $stmt = conectar()->prepare('SELECT * FROM contactos WHERE id = :id');
    $stmt->execute([':id' => $id]);
    $contacto = $stmt->fetch();
    return $contacto ?: null;
}

/** Busca el contacto del id recibido por GET o manda al listado. */
function contacto_desde_get(): array
{
    $id = leer_id($_GET);
    $contacto = null;
    if ($id !== null) {
        try {
            $contacto = obtener_contacto($id);
        } catch (PDOException $ex) {
            error_log('[agenda] ' . $ex->getMessage());
        }
    }
    if ($contacto === null) {
        flash('error', 'El contacto solicitado no existe.');
        redirigir('index.php');
    }
    return $contacto;
}

function iniciales(array $c): string
{
    return mb_strtoupper(mb_substr($c['nombre'], 0, 1) . mb_substr($c['apellido'], 0, 1));
}
