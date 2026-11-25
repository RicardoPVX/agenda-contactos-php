# Agenda de Contactos · PHP + MySQL (CRUD)

**Materia:** Programación Web
**Unidad:** Unidad 4 – Programación del lado del servidor con PHP
**Actividad:** Mini Proyecto 2 – Desarrollo Web con PHP y Base de Datos (CRUD)

## Integrantes del equipo

- Ricardo Francisco Pinto Velasco
- Sebastián Ali Fernández Culebro
- Gabriel Alejandro Cruz Guillen

## Enlaces

- 🌐 **Sistema publicado:** _pendiente_
- 🔗 **Repositorio:** https://github.com/RicardoPVX/agenda-contactos-php

## Descripción

Aplicación web dinámica para administrar una agenda de contactos. Todas las
operaciones se ejecutan en el servidor con PHP, que se conecta a MySQL mediante
PDO y sentencias preparadas. Cada contacto guarda nombre, apellido, teléfono,
foto (subida al servidor en `archivos/`), correo, dirección y notas.

## Tecnologías utilizadas

| Tecnología | Uso |
|---|---|
| PHP 8 | Lógica del servidor, validación y manejo de archivos |
| MySQL / MariaDB | Almacenamiento de los contactos |
| PDO | Conexión a la base de datos y sentencias preparadas |
| HTML5 | Formularios con validación en cliente (`required`, `type`, `pattern`, `maxlength`) |
| CSS3 | Hoja de estilos externa (`css/styles.css`), diseño responsivo |

## Funcionalidades implementadas (CRUD completo)

| Operación | Archivo | Descripción |
|---|---|---|
| **READ** | `index.php` | Listado de contactos y búsqueda por nombre, apellido, teléfono o correo |
| **CREATE** | `crear.php` → `guardar.php` | Formulario y `INSERT` con validación en servidor y subida de foto |
| **READ** | `ver.php` | Detalle de un contacto |
| **UPDATE** | `editar.php` → `actualizar.php` | Formulario con datos actuales y `UPDATE`; la foto se puede reemplazar |
| **DELETE** | `eliminar.php` | Pantalla de confirmación y `DELETE` por POST; borra también la foto |

### Seguridad

- **Sentencias preparadas** (`prepare()` + `execute()`) en todas las consultas → sin inyección SQL.
- **`htmlspecialchars()`** (función `e()`) en toda la salida → sin XSS.
- **Validación en servidor** de todos los campos (`includes/funciones.php` → `validar_contacto()` y `validar_foto()`), además de la validación HTML5.
- Las operaciones que modifican datos solo aceptan **POST** y llevan **token CSRF**.
- **Eliminación con confirmación previa.**
- **Los errores SQL nunca se muestran**: se registran con `error_log()` y el usuario ve un mensaje genérico (`display_errors` desactivado).
- La foto se valida por su contenido real (`getimagesize`), se limita a 2 MB y se guarda con nombre aleatorio; `archivos/.htaccess` impide ejecutar scripts en esa carpeta.
- Las credenciales viven en `config/db.local.php`, que no se sube a GitHub.

## Estructura

```
/
├── index.php          Listado y búsqueda (READ)
├── crear.php          Formulario de creación (CREATE – vista)
├── guardar.php        Procesa el INSERT (CREATE – lógica)
├── editar.php         Formulario de edición (UPDATE – vista)
├── actualizar.php     Procesa el UPDATE (UPDATE – lógica)
├── ver.php            Detalle de contacto (READ individual)
├── eliminar.php       Confirmación y DELETE
├── database.sql       Script para crear la tabla
├── config/
│   ├── db.php                 Conexión PDO
│   └── db.local.example.php   Plantilla de credenciales
├── includes/
│   ├── funciones.php  Arranque, validación, CSRF, fotos, htmlspecialchars
│   ├── header.php
│   ├── menu.php
│   ├── formulario.php Formulario compartido crear/editar
│   └── footer.php
├── css/
│   └── styles.css
└── archivos/          Fotos subidas
```

## Instalación

### En local (XAMPP / Laragon)

1. Copia la carpeta en `htdocs/` (o `www/`).
2. En phpMyAdmin crea la base `agenda` y ejecuta `database.sql`.
3. Copia `config/db.local.example.php` como `config/db.local.php` y ajusta usuario y contraseña.
4. Abre `http://localhost/agenda-contactos/`.

### En InfinityFree (u otro hosting con PHP y MySQL)

1. Crea la cuenta y el sitio; en **MySQL Databases** crea una base (ej. `if0_XXXX_agenda`).
2. Abre **phpMyAdmin** desde el panel, selecciona la base y ejecuta `database.sql`.
3. Crea `config/db.local.php` con los datos del panel (host `sqlXXX.infinityfree.com`, base, usuario `if0_XXXX` y la contraseña de la cuenta).
4. Sube **todo el contenido** de la carpeta a `htdocs/` con el File Manager o FileZilla (incluyendo `db.local.php` y los `.htaccess`).
5. Entra a la URL del sitio y crea un contacto de prueba.
