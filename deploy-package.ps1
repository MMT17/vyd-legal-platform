$ErrorActionPreference = 'Stop'

$Root = (Resolve-Path -LiteralPath (Split-Path -Parent $MyInvocation.MyCommand.Path)).Path
$PackageName = 'deployment-package'
$ZipName = 'deployment-package-without-vendor.zip'
$PackagePath = Join-Path $Root $PackageName
$ZipPath = Join-Path $Root $ZipName

function Assert-WorkspacePath {
    param(
        [Parameter(Mandatory = $true)]
        [string] $Path
    )

    $FullPath = [System.IO.Path]::GetFullPath($Path)

    if (-not $FullPath.StartsWith($Root, [System.StringComparison]::OrdinalIgnoreCase)) {
        throw "Ruta fuera del proyecto: $FullPath"
    }

    return $FullPath
}

function Copy-ProjectItem {
    param(
        [Parameter(Mandatory = $true)]
        [string] $RelativePath
    )

    $Source = Join-Path $Root $RelativePath

    if (-not (Test-Path -LiteralPath $Source)) {
        return
    }

    $Destination = Join-Path $PackagePath $RelativePath
    $DestinationParent = Split-Path -Parent $Destination

    if ($DestinationParent -and -not (Test-Path -LiteralPath $DestinationParent)) {
        New-Item -ItemType Directory -Path $DestinationParent | Out-Null
    }

    Copy-Item -LiteralPath $Source -Destination $Destination -Recurse -Force
}

function New-PackageDirectory {
    param(
        [Parameter(Mandatory = $true)]
        [string] $RelativePath
    )

    $DirectoryPath = Join-Path $PackagePath $RelativePath

    if (-not (Test-Path -LiteralPath $DirectoryPath)) {
        New-Item -ItemType Directory -Path $DirectoryPath | Out-Null
    }
}

$SafePackagePath = Assert-WorkspacePath -Path $PackagePath
$SafeZipPath = Assert-WorkspacePath -Path $ZipPath

if (Test-Path -LiteralPath $SafePackagePath) {
    Remove-Item -LiteralPath $SafePackagePath -Recurse -Force
}

if (Test-Path -LiteralPath $SafeZipPath) {
    Remove-Item -LiteralPath $SafeZipPath -Force
}

New-Item -ItemType Directory -Path $SafePackagePath | Out-Null

$ItemsToCopy = @(
    'app',
    'bootstrap',
    'config',
    'database',
    'public',
    'resources',
    'routes',
    'storage\app',
    'storage\framework',
    'composer.json',
    'composer.lock',
    'artisan',
    'package.json'
)

if (Test-Path -LiteralPath (Join-Path $Root 'package-lock.json')) {
    $ItemsToCopy += 'package-lock.json'
}

if (Test-Path -LiteralPath (Join-Path $Root 'vite.config.js')) {
    $ItemsToCopy += 'vite.config.js'
}

foreach ($Item in $ItemsToCopy) {
    Copy-ProjectItem -RelativePath $Item
}

New-PackageDirectory -RelativePath 'storage\app'
New-PackageDirectory -RelativePath 'storage\framework\cache\data'
New-PackageDirectory -RelativePath 'storage\framework\sessions'
New-PackageDirectory -RelativePath 'storage\framework\views'
New-PackageDirectory -RelativePath 'storage\framework\testing'

$CleanupPatterns = @(
    '.env',
    '.git',
    '.github',
    'vendor',
    'node_modules',
    'storage\logs',
    'tests',
    'README.md',
    'phpunit.xml'
)

foreach ($Pattern in $CleanupPatterns) {
    $Target = Join-Path $PackagePath $Pattern

    if (Test-Path -LiteralPath $Target) {
        $SafeTarget = Assert-WorkspacePath -Path $Target
        Remove-Item -LiteralPath $SafeTarget -Recurse -Force
    }
}

$BootstrapCachePath = Join-Path $PackagePath 'bootstrap\cache'

if (Test-Path -LiteralPath $BootstrapCachePath) {
    Get-ChildItem -LiteralPath $BootstrapCachePath -Filter '*.php' -File | Remove-Item -Force
}

Get-ChildItem -LiteralPath $PackagePath -Recurse -Force -File |
    Where-Object {
        $_.Extension -in @('.zip', '.tmp', '.temp') -or
        $_.Name -in @('npm-debug.log', 'yarn-error.log', '.DS_Store', 'Thumbs.db')
    } |
    Remove-Item -Force

$DeployInstructions = @'
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
'@

Set-Content -Path (Join-Path $PackagePath 'DEPLOY.md') -Value $DeployInstructions -Encoding UTF8

Compress-Archive -Path (Join-Path $PackagePath '*') -DestinationPath $SafeZipPath -CompressionLevel Optimal

$Zip = Get-Item -LiteralPath $SafeZipPath
$ZipSizeMb = [Math]::Round($Zip.Length / 1MB, 2)

Write-Host "Paquete generado: $ZipName"
Write-Host "Ruta: $SafeZipPath"
Write-Host "Tamano: $ZipSizeMb MB"
