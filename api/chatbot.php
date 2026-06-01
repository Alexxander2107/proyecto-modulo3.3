<?php
require_once __DIR__ . '/../auth.php';
// No obligatorio: el chatbot puede ser usado solo por usuarios autenticados
requireAuth();

header('Content-Type: application/json; charset=utf-8');

$input = json_decode(file_get_contents('php://input'), true);
$mensaje = trim((string)($input['mensaje'] ?? ''));

function normalize(string $s): string {
    $s = mb_strtolower($s, 'UTF-8');
    $s = str_replace(['á','é','í','ó','ú','ü','ñ'], ['a','e','i','o','u','u','n'], $s);
    return $s;
}

$texto = normalize($mensaje);

function answer(string $text, array $links = []): array {
    $resp = ['texto' => $text];
    if (!empty($links)) $resp['links'] = $links;
    return $resp;
}

$faq = [];

$faq[] = [
    'match' => ['ayuda','como usar','como uso','guia','manual','instrucciones'],
    'resp' => answer(
        'Este sistema te ayuda a gestionar: Clientes, Productos, Ventas y Reportes. Usa el menú superior para navegar.\n\nFlujo rápido:\n1) Ve a Productos para tener tu inventario listo.\n2) Ve a Clientes para agregar a tus clientes.\n3) Registra una Venta desde Ventas → “Registrar Nueva Venta”.\n4) Revisa métricas en Reportes.',
        [
            ['label' => 'Dashboard', 'url' => '../dashboard.php'],
            ['label' => 'Clientes', 'url' => '../clientes/listar.php'],
            ['label' => 'Productos', 'url' => '../productos/listar.php'],
            ['label' => 'Nueva Venta', 'url' => '../ventas/nueva_venta.php'],
            ['label' => 'Reportes', 'url' => '../reportes/reportes.php'],
        ]
    )
];

$faq[] = [
    'match' => ['dashboard','panel','principal'],
    'resp' => answer(
        'El Dashboard muestra un resumen:\n- Total de clientes\n- Total de productos\n- Total de ventas\n\nDesde ahí puedes ir directo a las secciones importantes (por ejemplo: registrar una venta o administrar usuarios según tu rol).',
        [
            ['label' => 'Ir a Dashboard', 'url' => '../dashboard.php']
        ]
    )
];

$faq[] = [
    'match' => ['clientes','cliente','ver mis clientes','informacion de clientes'],
    'resp' => answer(
        'En el módulo Clientes puedes: listar clientes, agregar nuevos, editar o eliminar.\n\nRuta: Clientes → “Listar”.',
        [
            ['label' => 'Listar Clientes', 'url' => '../clientes/listar.php'],
            ['label' => 'Agregar Cliente', 'url' => '../clientes/agregar.php'],
        ]
    )
];

$faq[] = [
    'match' => ['productos','producto','stock','inventario'],
    'resp' => answer(
        'En el módulo Productos controlas el inventario (stock), precios y categorías.\n\nPuedes listar, agregar, editar o eliminar productos.\n\nCuando registras una venta, el sistema descuenta el stock automáticamente.',
        [
            ['label' => 'Listar Productos', 'url' => '../productos/listar.php'],
            ['label' => 'Agregar Producto', 'url' => '../productos/agregar.php'],
        ]
    )
];

$faq[] = [
    'match' => ['ventas','venta','registrar venta','nueva venta','como registrar'],
    'resp' => answer(
        'Para registrar una venta:\n1) Ve a Ventas → “Nueva Venta”.\n2) Selecciona (o agrega manualmente) el cliente.\n3) Selecciona (o agrega manualmente) el producto.\n4) Indica la cantidad y presiona “Registrar Venta”.\n\nRuta: Ventas → Nueva Venta.',
        [
            ['label' => 'Registrar Nueva Venta', 'url' => '../ventas/nueva_venta.php'],
            ['label' => 'Ver Ventas', 'url' => '../ventas/listar.php'],
        ]
    )
];

$faq[] = [
    'match' => ['reportes','reporte','grafica','graficas','metricas'],
    'resp' => answer(
        'En Reportes se muestran gráficas con métricas del sistema, como:\n- Productos por categoría\n- Ventas por cliente\n- Stock por producto\n- Total por venta',
        [
            ['label' => 'Ver Reportes', 'url' => '../reportes/reportes.php'],
        ]
    )
];

$faq[] = [
    'match' => ['usuarios','empleados','administrar','gerente','contador','rol'],
    'resp' => answer(
        'Los usuarios/empleados se administran según tu rol.\n\nSi tienes permisos, puedes administrar usuarios desde: Usuarios → Listar.',
        [
            ['label' => 'Administrar Usuarios', 'url' => '../empleados/listar.php']
        ]
    )
];

$faq[] = [
    'match' => ['cerrar sesion','logout','salir','cierre'],
    'resp' => answer(
        'Para salir del sistema usa “Cerrar sesión” en el menú superior.',
        [
            ['label' => 'Cerrar sesión', 'url' => '../logout.php']
        ]
    )
];

// Matching simple por palabras
foreach ($faq as $item) {
    foreach ($item['match'] as $k) {
        if ($k !== '' && str_contains($texto, $k)) {
            echo json_encode($item['resp']);
            exit;
        }
    }
}

// Respuesta por defecto
$default = answer(
    'No encontré una respuesta exacta para tu consulta.\n\nPreguntas típicas: \n- “¿Cómo uso el sistema?”\n- “¿Dónde veo mis clientes?”\n- “¿Para qué sirve Productos?”\n- “¿Cómo registro una venta?”\n- “¿Qué muestran los Reportes?”\n- “¿Cómo cierro sesión?”',
    [
        ['label' => 'Dashboard', 'url' => '../dashboard.php'],
        ['label' => 'Clientes', 'url' => '../clientes/listar.php'],
        ['label' => 'Productos', 'url' => '../productos/listar.php'],
        ['label' => 'Nueva Venta', 'url' => '../ventas/nueva_venta.php'],
        ['label' => 'Reportes', 'url' => '../reportes/reportes.php'],
    ]
);

echo json_encode($default);

