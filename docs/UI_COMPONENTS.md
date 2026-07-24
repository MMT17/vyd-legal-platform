# VYD UI Components

## `x-vyd.metric-card`
Proposito: mostrar una metrica ejecutiva.
Props: `label`, `value`, `description`, `icon`, `tone`, `percentage`.
Variantes: `neutral`, `info`, `success`, `warning`, `danger`, `gold`.
Blade:
```blade
<x-vyd.metric-card label="Creados" value="3" tone="success" icon="heroicon-o-check-circle" />
```
Filament: usar dentro de `Placeholder::make()->content(new HtmlString(view(...)->render()))`.
Accesibilidad: no depender solo del color; incluir label visible.

## `x-vyd.alert`
Proposito: alertas operativas cortas.
Props: `title`, `description`, `tone`, `icon`, `compact`, `actionLabel`, `actionUrl`.
Blade:
```blade
<x-vyd.alert title="3 filas tienen errores" tone="danger" description="Descargue el reporte para revisar campos invalidos." />
```
Accesibilidad: usa `role=status`, icono y texto.

## `x-vyd.stepper`
Proposito: mostrar progreso de procesos.
Props: `steps`, `current`, `completed`.
Blade:
```blade
<x-vyd.stepper :steps="['Seleccionar', 'Validar', 'Confirmar', 'Resultado']" :current="2" :completed="[1]" />
```
Accesibilidad: usa lista ordenada y `aria-current`.

## `x-vyd.preview-table`
Proposito: preview tabular responsive.
Props: `columns`, `rows`, `statusColumn`, `maxHeight`, `emptyMessage`.
Blade:
```blade
<x-vyd.preview-table :columns="$columns" :rows="$rows" status-column="status" />
```
Accesibilidad: tabla real con `th scope=col`.

## `x-vyd.status-badge`
Proposito: estado compacto.
Variantes: `created`, `updated`, `skipped`, `error`, `pending`, `processing`, `completed`.
Blade:
```blade
<x-vyd.status-badge variant="updated" />
```

## `x-vyd.empty-state`
Proposito: estado vacio con accion opcional.
Props: `title`, `description`, `icon`, `actionLabel`, `actionUrl`.

## `x-vyd.section-header`
Proposito: encabezado de seccion con descripcion y slot de acciones.
Props: `title`, `description`, `icon`.

## `x-vyd.result-summary`
Proposito: resumen final de procesos.
Props: `processed`, `created`, `updated`, `skipped`, `errors`.

## `x-vyd.info-pair`
Proposito: mostrar pares de dato/valor.
Props: `label`, `value`.

## `x-vyd.data-status-row`
Proposito: fila compacta de estado de datos.
Props: `status`, `title`, `description`.

## Reglas Generales
Todos los componentes deben funcionar en dark/light mode, usar foco visible, no introducir JavaScript innecesario y no romper Alpine, Livewire ni Filament.
