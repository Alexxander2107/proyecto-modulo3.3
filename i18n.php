<?php
// Simple i18n helper (es/en)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$availableLangs = ['es', 'en'];

if (isset($_GET['lang']) && in_array($_GET['lang'], $availableLangs, true)) {
    $_SESSION['lang'] = $_GET['lang'];
}

$lang = $_SESSION['lang'] ?? 'es';
if (!in_array($lang, $availableLangs, true)) {
    $lang = 'es';
}

$dict = [
    'app.title' => ['es' => 'MarketPlace Express', 'en' => 'MarketPlace Express'],

    'nav.dashboard' => ['es' => 'Dashboard', 'en' => 'Dashboard'],
    'nav.clients' => ['es' => 'Clientes', 'en' => 'Clients'],
    'nav.products' => ['es' => 'Productos', 'en' => 'Products'],
    'nav.sales' => ['es' => 'Ventas', 'en' => 'Sales'],
    'nav.users' => ['es' => 'Usuarios', 'en' => 'Users'],
    'nav.logout' => ['es' => 'Cerrar sesión', 'en' => 'Log out'],

    'auth.login' => ['es' => 'Iniciar sesión', 'en' => 'Log in'],
    'auth.register' => ['es' => 'Registrarse', 'en' => 'Sign up'],
    'auth.email' => ['es' => 'Correo', 'en' => 'Email'],
    'auth.password' => ['es' => 'Contraseña', 'en' => 'Password'],
    'auth.show_password' => ['es' => 'Mostrar', 'en' => 'Show'],
    'auth.submit_login' => ['es' => 'Ingresar', 'en' => 'Continue'],
    'auth.no_account' => ['es' => '¿No tienes cuenta?', 'en' => "Don't have an account?"],

    'home.tagline' => ['es' => 'Sistema de Gestión Empresarial', 'en' => 'Business Management System'],
    'home.description' => ['es' => 'Control de clientes, productos, ventas y reportes.', 'en' => 'Manage clients, products, sales, and reports.'],

    'home.cards.clients.title' => ['es' => 'Clientes', 'en' => 'Clients'],
    'home.cards.clients.link' => ['es' => 'Administrar clientes registrados.', 'en' => 'Manage registered clients.'],

    'home.cards.products.title' => ['es' => 'Productos', 'en' => 'Products'],
    'home.cards.products.link' => ['es' => 'Control de stock y categorías.', 'en' => 'Control stock and categories.'],

    'home.cards.sales.title' => ['es' => 'Ventas', 'en' => 'Sales'],
    'home.cards.sales.link' => ['es' => 'Registro y control de ventas.', 'en' => 'Record and manage sales.'],

    'dashboard.title' => ['es' => 'Dashboard Principal', 'en' => 'Main Dashboard'],
    'dashboard.welcome' => ['es' => 'Bienvenido', 'en' => 'Welcome'],
    'dashboard.manage' => ['es' => 'Gestiona tus módulos desde aquí.', 'en' => 'manage your modules from here.'],
    'dashboard.error' => ['es' => 'Ups, algo salió mal', 'en' => 'Oops, something went wrong'],
    'dashboard.register_sale' => ['es' => 'Registrar Venta', 'en' => 'Register Sale'],
    'dashboard.manage_users' => ['es' => 'Administrar Usuarios', 'en' => 'Manage Users'],
    'dashboard.create_backup' => ['es' => 'Crear respaldo', 'en' => 'Create Backup'],
    'dashboard.clients_registered' => ['es' => 'Clientes registrados', 'en' => 'Registered Clients'],
    'dashboard.view_clients' => ['es' => 'Ver clientes', 'en' => 'View Clients'],
    'dashboard.products_available' => ['es' => 'Productos disponibles', 'en' => 'Products Available'],
    'dashboard.view_products' => ['es' => 'Ver productos', 'en' => 'View Products'],
    'dashboard.sales_registered' => ['es' => 'Ventas registradas', 'en' => 'Registered Sales'],
    'dashboard.view_sales' => ['es' => 'Ver ventas', 'en' => 'View Sales'],
    'dashboard.last_sales' => ['es' => 'Últimas ventas', 'en' => 'Latest Sales'],
    'dashboard.last_sales_sub' => ['es' => 'Historial de las cinco ventas más recientes.', 'en' => 'History of the five most recent sales.'],
    'dashboard.no_sales' => ['es' => 'No hay ventas registradas aún.', 'en' => 'No sales recorded yet.'],

    'auth.create_account' => ['es' => 'Crear Cuenta', 'en' => 'Create Account'],
    'auth.name' => ['es' => 'Nombre', 'en' => 'Name'],
    'auth.password_rules' => ['es' => 'Mínimo 8 caracteres, al menos una mayúscula y el símbolo @', 'en' => 'Minimum 8 characters, at least one uppercase letter and the "@" symbol'],
    'auth.confirm_password' => ['es' => 'Confirmar contraseña', 'en' => 'Confirm password'],
    'auth.register_submit' => ['es' => 'Registrar', 'en' => 'Register'],
    'auth.have_account' => ['es' => '¿Ya tienes cuenta?', 'en' => "Don't have an account?"],
];

function t(string $key): string {
    global $dict, $lang;
    if (isset($dict[$key]) && isset($dict[$key][$lang])) {
        return (string)$dict[$key][$lang];
    }
    // Fallback: ES si existe, si no, la key.
    return (string)($dict[$key]['es'] ?? $key);
}


