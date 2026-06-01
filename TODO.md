# TODO - Actualización automática de gráficas de Reportes

- [x] Identificar dónde están las gráficas: `reportes/reportes.php` (Chart.js) y datos: `reportes/data.php`.
- [x] Verificar qué eventos/refresco ya existen en `reportes/reportes.php`.
- [x] Hacer que al completar **CRUD de clientes/productos** se dispare un refresco en `reportes.php` usando `reportes_refresh=1`.
- [x] Hacer que al completar **registrar venta** se dispare un refresco en `reportes.php` usando `reportes_refresh=1`.
- [x] Asegurar que `reportes/reportes.php` lea el parámetro `reportes_refresh` y recargue los datos.
- [ ] (Opcional) Validar con pruebas manuales: abrir reportes y luego ejecutar alta/edición/eliminación y confirmar actualización.

