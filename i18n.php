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

    // Global / UI (placeholders, buttons, labels)
    'global.back' => ['es' => 'Volver', 'en' => 'Back'],
    'global.new' => ['es' => 'Nuevo', 'en' => 'New'],
    'global.save' => ['es' => 'Guardar', 'en' => 'Save'],
    'global.update' => ['es' => 'Actualizar', 'en' => 'Update'],
    'global.edit' => ['es' => 'Editar', 'en' => 'Edit'],
    'global.delete' => ['es' => 'Eliminar', 'en' => 'Delete'],
    'global.filter' => ['es' => 'Filtrar', 'en' => 'Filter'],
    'global.all_categories' => ['es' => 'Todas las categorías', 'en' => 'All categories'],

    // Login JS alerts
    'login.alert.missing_credentials' => ['es' => 'Ingresa correo y contraseña para continuar.', 'en' => 'Enter email and password to continue.'],
    'login.alert.password_length' => ['es' => 'La contraseña debe tener al menos 6 caracteres.', 'en' => 'Password must be at least 6 characters long.'],

    // Dashboard / recent sales table headings
    'table.id_sale' => ['es' => 'ID Venta', 'en' => 'Sale ID'],
    'table.client' => ['es' => 'Cliente', 'en' => 'Client'],
    'table.total' => ['es' => 'Total', 'en' => 'Total'],
    'table.date' => ['es' => 'Fecha', 'en' => 'Date'],

    // Clients
    'clients.title' => ['es' => 'Clientes', 'en' => 'Clients'],
    'clients.list_title' => ['es' => 'Listado de Clientes', 'en' => 'Clients list'],
    'clients.list_subtitle' => ['es' => 'Gestión de clientes registrados en el sistema.', 'en' => 'Manage registered clients in the system.'],
    'clients.new_title' => ['es' => 'Agregar Cliente', 'en' => 'Add client'],
    'clients.new_subtitle' => ['es' => 'Registra un nuevo cliente en el sistema.', 'en' => 'Register a new client in the system.'],
    'clients.edit_title' => ['es' => 'Editar Cliente', 'en' => 'Edit client'],
    'clients.edit_subtitle' => ['es' => 'Actualiza los datos del cliente seleccionado.', 'en' => 'Update the selected client details.'],
    'clients.save_button' => ['es' => 'Guardar Cliente', 'en' => 'Save client'],
    'clients.update_button' => ['es' => 'Actualizar', 'en' => 'Update'],
    'clients.action.edit_title' => ['es' => 'Editar cliente', 'en' => 'Edit client'],
    'clients.action.delete_title' => ['es' => 'Eliminar cliente', 'en' => 'Delete client'],
    'clients.alert.cannot_delete_has_sales' => ['es' => 'No se puede eliminar el cliente porque tiene ventas registradas.', 'en' => 'Cannot delete the client because it has sales registered.'],
    'clients.alert.deleted' => ['es' => 'Cliente eliminado correctamente.', 'en' => 'Client deleted successfully.'],
    'clients.alert.confirm_delete' => ['es' => '¿Confirmar eliminación del cliente?', 'en' => 'Confirm client deletion?'],

    'client.field.name' => ['es' => 'Nombre', 'en' => 'Name'],
    'client.field.last_name' => ['es' => 'Apellido', 'en' => 'Last name'],
    'client.field.email' => ['es' => 'Correo', 'en' => 'Email'],
    'client.field.phone' => ['es' => 'Teléfono', 'en' => 'Phone'],
    'client.field.address' => ['es' => 'Dirección', 'en' => 'Address'],

    // Products
    'products.title' => ['es' => 'Productos', 'en' => 'Products'],
    'products.list_title' => ['es' => 'Listado de Productos', 'en' => 'Product list'],
    'products.list_subtitle' => ['es' => 'Información de stock y precios disponibles.', 'en' => 'Stock and available pricing information.'],
    'products.add_button' => ['es' => 'Agregar Producto', 'en' => 'Add product'],
    'products.back' => ['es' => 'Volver', 'en' => 'Back'],
    'products.search_placeholder' => ['es' => 'Buscar producto...', 'en' => 'Search product...'],
    'products.table.product' => ['es' => 'Producto', 'en' => 'Product'],
    'products.table.price' => ['es' => 'Precio', 'en' => 'Price'],
    'products.table.stock' => ['es' => 'Stock', 'en' => 'Stock'],
    'products.table.category' => ['es' => 'Categoría', 'en' => 'Category'],
    'products.table.actions' => ['es' => 'Acciones', 'en' => 'Actions'],

    'products.exists' => ['es' => 'El producto "@name@" ya está registrado.', 'en' => 'The product "@name@" is already registered.'],
    'products.created' => ['es' => 'Producto registrado correctamente.', 'en' => 'Product registered successfully.'],

    'product.new_title' => ['es' => 'Agregar Producto', 'en' => 'Add product'],
    'product.new_subtitle' => ['es' => 'Registra un nuevo producto en el inventario.', 'en' => 'Register a new product in the inventory.'],
    'product.name_placeholder' => ['es' => 'Nombre del producto', 'en' => 'Product name'],
    'product.price_placeholder' => ['es' => 'Precio', 'en' => 'Price'],
    'product.stock_placeholder' => ['es' => 'Stock', 'en' => 'Stock'],
    'product.save_button' => ['es' => 'Guardar Producto', 'en' => 'Save product'],

    'product.edit_title' => ['es' => 'Editar Producto', 'en' => 'Edit product'],
    'product.edit_subtitle' => ['es' => 'Actualiza los datos del producto seleccionado.', 'en' => 'Update the selected product data.'],
    'product.update_button' => ['es' => 'Actualizar Producto', 'en' => 'Update product'],

    // Sales
    'sales.new_title' => ['es' => 'Registrar Nueva Venta', 'en' => 'Register new sale'],
    'sales.new_subtitle' => ['es' => 'Selecciona cliente y producto para crear la venta.', 'en' => 'Select a client and product to create the sale.'],

    'sales.error.invalid' => ['es' => 'Datos inválidos.', 'en' => 'Invalid data.'],
    'sales.error.invalid_stock' => ['es' => 'Stock insuficiente.', 'en' => 'Insufficient stock.'],
    'sales.error.client' => ['es' => 'Error al registrar cliente.', 'en' => 'Error registering client.'],
    'sales.success.registered' => ['es' => 'Venta registrada correctamente.', 'en' => 'Sale registered successfully.'],

    'sales.checkbox.manual_client' => ['es' => 'Agregar cliente manualmente', 'en' => 'Add client manually'],
    'sales.checkbox.manual_product' => ['es' => 'Agregar producto manualmente', 'en' => 'Add product manually'],

    'sales.label.client' => ['es' => 'Cliente', 'en' => 'Client'],
    'sales.select.placeholder.client' => ['es' => '-- Seleccione cliente --', 'en' => '-- Select client --'],

    'sales.label.product' => ['es' => 'Producto', 'en' => 'Product'],
    'sales.select.placeholder.product' => ['es' => '-- Seleccione producto --', 'en' => '-- Select product --'],

    'sales.placeholder.client.name' => ['es' => 'Nombre cliente', 'en' => 'Client name'],
    'sales.placeholder.client.last_name' => ['es' => 'Apellido cliente', 'en' => 'Client last name'],
    'sales.placeholder.client.email' => ['es' => 'Correo cliente', 'en' => 'Client email'],

    'sales.placeholder.product.name' => ['es' => 'Nombre del producto', 'en' => 'Product name'],
    'sales.placeholder.product.price' => ['es' => 'Precio', 'en' => 'Price'],
    'sales.placeholder.product.stock' => ['es' => 'Stock inicial', 'en' => 'Initial stock'],

    'sales.product.meta.stock_available' => ['es' => 'Stock disponible:', 'en' => 'Available stock:'],
    'sales.product.meta.unit_price' => ['es' => 'Precio unitario:', 'en' => 'Unit price:'],
    'sales.product.meta.estimated_total' => ['es' => 'Total estimado:', 'en' => 'Estimated total:'],

    'sales.field.quantity' => ['es' => 'Cantidad', 'en' => 'Quantity'],
    'sales.submit' => ['es' => 'Registrar Venta', 'en' => 'Register sale'],

    // Sales list
    'sales.list.title' => ['es' => 'Listado de Ventas', 'en' => 'Sales list'],
    'sales.list.subtitle' => ['es' => 'Historial y detalle de ventas registradas.', 'en' => 'History and details of registered sales.'],
    'sales.detail' => ['es' => 'Detalle', 'en' => 'Detail'],
    'sales.detail.product' => ['es' => 'Producto', 'en' => 'Product'],
    'sales.detail.quantity' => ['es' => 'Cantidad', 'en' => 'Quantity'],
    'sales.detail.price' => ['es' => 'Precio', 'en' => 'Price'],
    'sales.detail.subtotal' => ['es' => 'Subtotal', 'en' => 'Subtotal'],
    'sales.detail.total' => ['es' => 'Total', 'en' => 'Total'],

    // JS confirm dialog
    'global.confirm.delete' => ['es' => '¿Deseas eliminar este registro?', 'en' => 'Do you want to delete this record?'],
];

function t(string $key): string {
    global $dict, $lang;
    if (isset($dict[$key]) && isset($dict[$key][$lang])) {
        return (string)$dict[$key][$lang];
    }
    // Fallback: ES si existe, si no, la key.
    return (string)($dict[$key]['es'] ?? $key);
}


