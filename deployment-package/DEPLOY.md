# Despliegue cPanel - Plataforma Legal VYD

Este paquete fue preparado para subirlo por File Manager en un hosting compartido cPanel sin acceso SSH.

## Pasos

1. Descomprime el ZIP localmente o sÃºbelo y extrÃ¡elo desde File Manager.
2. Sube/reemplaza los archivos del proyecto en el servidor.
3. No reemplazar el archivo `.env` del servidor.
4. No reemplazar `storage/logs`.
5. Si existen archivos PHP dentro de `bootstrap/cache`, elimÃ­nalos antes de probar el sitio.
6. Ingresa a:
   https://preview.vydabogados.cl/

## Si existe acceso a terminal

Ejecutar:

```bash
php artisan optimize:clear
```

## Variantes

- `deployment-package-without-vendor.zip`: no incluye `vendor`.
- `deployment-package-with-vendor.zip`: incluye `vendor` para servidores sin dependencias instaladas.
