<?php
session_start();
require 'login/database.php';

// Verifica sesión activa y rol
if (!isset($_SESSION['rol']) || !isset($_SESSION['username'])) {
    die("❌ No has iniciado sesión o no se ha asignado un rol.");
}

$rol      = $_SESSION['rol'];      // 'trabajador' o 'usuario'
$ftp_user = $_SESSION['username'];

// Solo los trabajadores tienen permiso de gestión
$canManage = in_array($rol, ['trabajador','admin']);

// Obtener contraseña FTP real desde la BD
$stmt = $conexion->prepare("SELECT ftp_password FROM users WHERE nombre = ?");
$stmt->bind_param("s", $ftp_user);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 0) {
    die("❌ Usuario no encontrado en la base de datos.");
}
$user     = $result->fetch_assoc();
$ftp_pass = $user['ftp_password'];
$stmt->close();

// Conexión FTP
$ftp_server = "vsftpd";
$conn_id    = ftp_connect($ftp_server, 21, 10);
if (!$conn_id) die("❌ No se pudo conectar al servidor FTP.");
if (!ftp_login($conn_id, $ftp_user, $ftp_pass)) {
    ftp_close($conn_id);
    die("❌ Error al iniciar sesión FTP. Verifica las credenciales.");
}

// Ruta actual (por defecto la raíz compartida)
$ruta = isset($_GET['ruta']) ? $_GET['ruta'] : "/";

// Función auxiliar para redirigir con mensaje
function redirect_with_msg($msg, $color, $ruta) {
    $_SESSION['mensaje'] = [$msg, $color];
    header('Location: ?ruta=' . urlencode($ruta));
    exit;
}

// --- BLOQUES DE GESTIÓN (solo si $canManage) ---

if ($canManage) {
    // Crear carpeta
    if (isset($_POST['crear_carpeta']) && !empty($_POST['nombre_carpeta'])) {
        $newdir = rtrim($ruta, '/') . '/' . basename($_POST['nombre_carpeta']);
        if (ftp_mkdir($conn_id, $newdir)) {
            redirect_with_msg('✅ Carpeta creada correctamente.', 'green', $ruta);
        } else {
            redirect_with_msg('❌ No se pudo crear la carpeta.', 'red', $ruta);
        }
    }

    // Subir archivo
    if (isset($_FILES['archivo']) && $_FILES['archivo']['error'] === UPLOAD_ERR_OK) {
        $tmp  = $_FILES['archivo']['tmp_name'];
        $name = basename($_FILES['archivo']['name']);
        $dest = rtrim($ruta, '/') . '/' . $name;
        if (ftp_put($conn_id, $dest, $tmp, FTP_BINARY)) {
            redirect_with_msg('✅ Archivo subido correctamente.', 'green', $ruta);
        } else {
            redirect_with_msg('❌ Error al subir el archivo.', 'red', $ruta);
        }
    }

    // Eliminar archivo
    if (isset($_GET['eliminar']) && !empty($_GET['eliminar'])) {
        $file = $_GET['eliminar'];
        if (ftp_delete($conn_id, $file)) {
            redirect_with_msg('✅ Archivo eliminado.', 'green', $ruta);
        } else {
            redirect_with_msg('❌ No se pudo eliminar el archivo.', 'red', $ruta);
        }
    }

    // Mover archivo
if (isset($_POST['mover_archivo']) && !empty($_POST['archivo_origen']) && isset($_POST['nuevo_directorio'])) {
    $orig = $_POST['archivo_origen'];
    $destdir = rtrim($_POST['nuevo_directorio'], '/');

    // Validar ruta destino
    if (!preg_match('/^\/?[a-zA-Z0-9_\-\/]*$/', $destdir)) {
        redirect_with_msg('❌ Ruta destino inválida.', 'red', $ruta);
    }

    // No verificar con ftp_chdir si es raíz
  if ($destdir !== '/') {
    $dirs = ftp_nlist($conn_id, $destdir);
    if ($dirs === false) {
        redirect_with_msg('❌ Carpeta destino no existe.', 'red', $ruta);
    }
}


    // Regresar al directorio actual antes de mover
    ftp_chdir($conn_id, $ruta);

    $bn  = basename($orig);
    $new = ($destdir === '') ? "/$bn" : "$destdir/$bn";
    if (@ftp_rename($conn_id, $orig, $new)) {
        redirect_with_msg('✅ Archivo movido correctamente.', 'green', $ruta);
    } else {
        redirect_with_msg('❌ Error al mover el archivo.', 'red', $ruta);
    }
}


    // Eliminar carpeta (recursivo)
    function delete_dir($conn, $dir) {
        $items = ftp_nlist($conn, $dir);
        if ($items !== false) {
            foreach ($items as $item) {
                if ($item === '.' || $item === '..') continue;
                $path = $dir . '/' . basename($item);
                if (@ftp_chdir($conn, $path)) {
                    delete_dir($conn, $path);
                    ftp_rmdir($conn, $path);
                } else {
                    ftp_delete($conn, $path);
                }
            }
        }
    }
    if (isset($_GET['eliminar_carpeta']) && !empty($_GET['eliminar_carpeta'])) {
        $d = $_GET['eliminar_carpeta'];
        delete_dir($conn_id, $d);
        ftp_rmdir($conn_id, $d);
        redirect_with_msg('✅ Carpeta eliminada correctamente.', 'green', $ruta);
    }
}

// --- DESCARGA (permitida para todos) ---
if (isset($_GET['descargar']) && !empty($_GET['descargar'])) {
    $f   = $_GET['descargar'];
    $tmp = tempnam(sys_get_temp_dir(), 'ftp');
    if (ftp_get($conn_id, $tmp, $f, FTP_BINARY)) {
        ftp_close($conn_id);
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . basename($f) . '"');
        readfile($tmp);
        unlink($tmp);
        exit;
    } else {
        redirect_with_msg('❌ No se pudo descargar el archivo.', 'red', $ruta);
    }
}

// Listar archivos/carpetas
$list = ftp_nlist($conn_id, $ruta);
$info = [];
foreach ($list as $entry) {
    $isDir = false;
    $cwd   = ftp_pwd($conn_id);
    if (@ftp_chdir($conn_id, $entry)) {
        $isDir = true;
        ftp_chdir($conn_id, $cwd);
    }
    $info[] = ['ruta' => $entry, 'nombre' => basename($entry), 'esCarpeta' => $isDir];
}
ftp_close($conn_id);

// Calcular ruta anterior
function ruta_anterior($r) {
    $t = rtrim($r, '/');
    if ($t === '' || $t === '/') return '/';
    $p = explode('/', $t);
    array_pop($p);
    return implode('/', $p) ?: '/';
}
$prev = ruta_anterior($ruta);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Archivos FTP</title>
    <style>
      <?php include 'css/estilos.css'; ?>
    </style>
</head>
<body>
<?php include 'header.php'; ?>
<div class="ftp-container">
    <h1>📂 Gestión de Archivos</h1>
    <h2>Directorio actual: <?= htmlspecialchars($ruta) ?></h2>

    <?php if (isset($_SESSION['mensaje'])): ?>
        <p style="color: <?= $_SESSION['mensaje'][1] ?>; text-align:center;">
            <?= htmlspecialchars($_SESSION['mensaje'][0]) ?>
        </p>
        <?php unset($_SESSION['mensaje']); ?>
    <?php endif; ?>

    <?php if ($ruta !== '/'): // Volver solo en subcarpetas ?>
        <p><a href="?ruta=<?= urlencode($prev) ?>">⬅️ Volver al directorio anterior</a></p>
    <?php endif; ?>

    <?php if ($canManage): // Sólo trabajadores ven estos controles ?>
    <form method="POST" style="display:inline-block; margin-right:20px;">
        <input type="text" name="nombre_carpeta" placeholder="Nueva carpeta" required>
        <button type="submit" name="crear_carpeta">➕ Crear</button>
    </form>
    <form method="POST" enctype="multipart/form-data" style="display:inline-block;">
        <input type="file" name="archivo" required>
        <button type="submit">⬆️ Subir</button>
    </form>
    <?php endif; ?>

    <ul>
    <?php foreach ($info as $item): ?>
        <li>
            <?php if ($item['esCarpeta']): ?>
                📁 <a href="?ruta=<?= urlencode($item['ruta']) ?>">
                    <?= htmlspecialchars($item['nombre']) ?>
                </a>
                <?php if ($canManage): ?>
                    <!-- borrar carpeta -->
                    '<a href="?ruta=<?= urlencode($ruta) ?>&eliminar_carpeta=<?= urlencode($item['ruta']) ?>"
                        onclick="return confirm('¿Eliminar carpeta <?= htmlspecialchars($item['nombre']) ?>?')">
                        🗑️
                    </a>'
                <?php endif; ?>
            <?php else: ?>
                📄 <?= htmlspecialchars($item['nombre']) ?>
                <!-- descargar siempre -->
                <a href="?ruta=<?= urlencode($ruta) ?>&descargar=<?= urlencode($item['ruta']) ?>"> ⬇️ </a>
                <?php if ($canManage): ?>
                    <!-- borrar archivo -->
                    <a href="?ruta=<?= urlencode($ruta) ?>&eliminar=<?= urlencode($item['ruta']) ?>"
                        onclick="return confirm('¿Eliminar <?= htmlspecialchars($item['nombre']) ?>?')"> 🗑️ </a>
                    <!-- mover archivo -->
                    <form method="POST" style="display:inline; margin-left:10px;">
                        <input type="hidden" name="archivo_origen" value="<?= htmlspecialchars($item['ruta']) ?>">
                        <input type="text" name="nuevo_directorio" placeholder="Destino" required style="width:120px;">
                        <button type="submit" name="mover_archivo">📂</button>
                    </form>
                <?php endif; ?>
            <?php endif; ?>
        </li>
    <?php endforeach; ?>
    </ul>
</div>
</body>
</html>
