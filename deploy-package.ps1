param(
    [ValidateSet('without-vendor', 'with-vendor')]
    [string] $Mode = 'without-vendor'
)

$ErrorActionPreference = 'Stop'

$Root = Split-Path -Parent $MyInvocation.MyCommand.Path
$PackageName = 'deployment-package'
$PackagePath = Join-Path $Root $PackageName
$ZipName = if ($Mode -eq 'with-vendor') { 'deployment-package-with-vendor.zip' } else { 'deployment-package-without-vendor.zip' }
$ZipPath = Join-Path $Root $ZipName

function Copy-ProjectItem {
    param(
        [Parameter(Mandatory = $true)]
        [string] $RelativePath
    )

    $Source = Join-Path $Root $RelativePath
    $Destination = Join-Path $PackagePath $RelativePath

    if (-not (Test-Path -LiteralPath $Source)) {
        return
    }

    $Parent = Split-Path -Parent $Destination

    if ($Parent -and -not (Test-Path -LiteralPath $Parent)) {
        New-Item -ItemType Directory -Path $Parent | Out-Null
    }

    Copy-Item -LiteralPath $Source -Destination $Destination -Recurse -Force
}

function New-PackageDirectory {
    param(
        [Parameter(Mandatory = $true)]
        [string] $RelativePath
    )

    $Path = Join-Path $PackagePath $RelativePath

    if (-not (Test-Path -LiteralPath $Path)) {
        New-Item -ItemType Directory -Path $Path | Out-Null
    }
}

if (Test-Path -LiteralPath $PackagePath) {
    Remove-Item -LiteralPath $PackagePath -Recurse -Force
}

if (Test-Path -LiteralPath $ZipPath) {
    Remove-Item -LiteralPath $ZipPath -Force
}

New-Item -ItemType Directory -Path $PackagePath | Out-Null

$ItemsToCopy = @(
    'app',
    'bootstrap',
    'config',
    'database',
    'lang',
    'public',
    'resources',
    'routes',
    'composer.json',
    'composer.lock',
    'artisan'
)

if ($Mode -eq 'with-vendor') {
    $ItemsToCopy += 'vendor'
}

foreach ($Item in $ItemsToCopy) {
    Copy-ProjectItem -RelativePath $Item
}

Copy-ProjectItem -RelativePath 'storage\app'

New-PackageDirectory -RelativePath 'storage\framework\cache\data'
New-PackageDirectory -RelativePath 'storage\framework\sessions'
New-PackageDirectory -RelativePath 'storage\framework\testing'
New-PackageDirectory -RelativePath 'storage\framework\views'

$BootstrapCachePath = Join-Path $PackagePath 'bootstrap\cache'

if (Test-Path -LiteralPath $BootstrapCachePath) {
    Get-ChildItem -LiteralPath $BootstrapCachePath -Filter '*.php' -File | Remove-Item -Force
}

$TemporaryPaths = @(
    'storage\app\livewire-tmp',
    'storage\app\private\livewire-tmp',
    'storage\app\public\livewire-tmp'
)

foreach ($TemporaryPath in $TemporaryPaths) {
    $FullTemporaryPath = Join-Path $PackagePath $TemporaryPath

    if (Test-Path -LiteralPath $FullTemporaryPath) {
        Remove-Item -LiteralPath $FullTemporaryPath -Recurse -Force
    }
}

Get-ChildItem -LiteralPath $PackagePath -Recurse -Force -File |
    Where-Object {
        $_.Name -in @('npm-debug.log', 'yarn-error.log') -or
        $_.Extension -in @('.tmp', '.temp')
    } |
    Remove-Item -Force

$DeployInstructions = @'
# Despliegue cPanel - Plataforma Legal VYD

Este paquete fue preparado para subirlo por File Manager en un hosting compartido cPanel sin acceso SSH.

## Pasos

1. Descomprime el ZIP localmente o súbelo y extráelo desde File Manager.
2. Sube/reemplaza los archivos del proyecto en el servidor.
3. No reemplazar el archivo `.env` del servidor.
4. No reemplazar `storage/logs`.
5. Si existen archivos PHP dentro de `bootstrap/cache`, elimínalos antes de probar el sitio.
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
'@

Set-Content -Path (Join-Path $PackagePath 'DEPLOY.md') -Value $DeployInstructions -Encoding UTF8

Compress-Archive -Path (Join-Path $PackagePath '*') -DestinationPath $ZipPath -CompressionLevel Optimal

$Zip = Get-Item -LiteralPath $ZipPath
$ZipSizeMb = [Math]::Round($Zip.Length / 1MB, 2)

Write-Host "Paquete generado: $ZipName"
Write-Host "Ubicacion: $ZipPath"
Write-Host "Tamano: $ZipSizeMb MB"
Write-Host "Modo: $Mode"
