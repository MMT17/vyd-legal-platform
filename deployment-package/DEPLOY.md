# Despliegue manual cPanel - VYD Abogados

Este paquete fue preparado para subirlo por File Manager en un hosting cPanel sin acceso SSH.

## Pasos

1. Subir `deployment-package-without-vendor.zip` a la carpeta del Laravel actual en cPanel.
2. Extraer el ZIP reemplazando archivos del proyecto.
3. No reemplazar el archivo `.env` del servidor.
4. No reemplazar ni borrar `vendor`.
5. Si aparece error 500, borrar manualmente `bootstrap/cache/*.php`.
6. Revisar el sitio en `https://preview.vydabogados.cl`.

## Base de datos

Importar `database/production_cms_update.sql` desde phpMyAdmin si el servidor no ejecuta migraciones.

## Comandos locales recomendados antes de empaquetar

```bash
php artisan optimize:clear
npm run build
powershell -ExecutionPolicy Bypass -File .\deploy-package.ps1
```
