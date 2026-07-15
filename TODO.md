# TODO - Traducción completa (ES/EN)

- [ ] Escanear pantallas/módulos para ubicar textos hardcodeados en ES.
- [ ] Actualizar `i18n.php` agregando claves para TODAS las cadenas encontradas.
- [ ] Reemplazar textos hardcodeados por `<?= htmlspecialchars(t('...')) ?>` en todas las páginas.
- [ ] Reemplazar textos hardcodeados dentro de `<script>` usando inyección desde PHP (t() -> JS).
- [ ] Probar:
  - [ ] Cambiar a `?lang=en` y validar navbar + home + login.
  - [ ] Probar flujo de `Clientes` (agregar/listar/eliminar/editar).
  - [ ] Probar flujo de `Productos` (agregar/listar/editar/eliminar).
  - [ ] Probar flujo de `Ventas` (nueva venta/registrar/listar) y mensajes.
- [ ] Ajustar cualquier texto que quede sin traducir (si no existe clave en diccionario).

