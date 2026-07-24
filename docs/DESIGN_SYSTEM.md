# VYD Legal Platform Design System

## Proposito
El Design System de VYD Legal Platform define una interfaz juridica corporativa, clara y confiable para la intranet administrativa. Su objetivo es acelerar nuevas pantallas sin perder consistencia visual, accesibilidad ni compatibilidad con Filament 5.

## Personalidad
- Juridica, ejecutiva y sobria.
- Moderna sin verse tecnologica en exceso.
- Confiable, precisa y orientada a trabajo profesional.
- Sin apariencia gamer, fintech o de plantilla generica.

## Tokens De Color
Principales:
- `--vyd-navy-950`: `#071525`
- `--vyd-navy-900`: `#0F2744`
- `--vyd-navy-800`: `#16365D`
- `--vyd-gold-600`: `#B8862F`
- `--vyd-gold-500`: `#C89B3C`
- `--vyd-gold-400`: `#D7A947`

Semanticos:
- `--vyd-success`: `#16A34A`
- `--vyd-warning`: `#D97706`
- `--vyd-danger`: `#DC2626`
- `--vyd-info`: `#2563EB`

El dorado se reserva para accion primaria, paso activo, foco controlado e indicadores de actualizacion. No debe dominar la pantalla.

## Light Mode
- Fondo: `--vyd-bg-light` / `#F6F7F9`
- Superficie: `--vyd-surface-light` / `#FFFFFF`
- Borde: `--vyd-border-light` / `#DDE2E8`
- Texto: `--vyd-text-light` / `#18202A`
- Texto secundario: `--vyd-text-muted-light` / `#667085`

## Dark Mode
- Fondo: `--vyd-bg` / `#0B0D10`
- Superficie: `--vyd-surface` / `#14171C`
- Superficie elevada: `--vyd-surface-raised` / `#1B1F26`
- Borde: `--vyd-border` / `#303640`
- Texto: `--vyd-text` / `#F5F7FA`
- Texto secundario: `--vyd-text-muted` / `#A7AFBC`

## Tipografia
La administracion usa Instrument Sans, ya integrada por Vite. Si en el futuro se integra Plus Jakarta Sans o Inter en el panel admin, puede reemplazarla como fuente sans principal.

Jerarquia:
- Titulo de pagina: 28-32 px, semibold/bold.
- Titulo de seccion: 18-22 px, semibold.
- Texto normal: 14-16 px.
- Etiqueta: 12-14 px, medium.
- Metrica: 28-36 px, bold.
- Ayuda: 12-14 px.

## Espaciado
Usar pasos consistentes: 4, 8, 12, 16, 24 y 32 px. En formularios complejos preferir grupos con 16-24 px entre secciones.

## Radios
- Pequeno: 8 px.
- Medio: 12 px.
- Grande: 16 px.

## Sombras
Sombras sutiles, sin glow ni neon. Deben sugerir elevacion, no decoracion.

## Estados
Cada estado debe combinar color, icono y texto:
- Exito: check-circle, verde.
- Advertencia: exclamation-triangle, ambar.
- Error: x-circle, rojo.
- Informacion: information-circle, azul.
- Actualizacion: dorado/ambar.

## Iconografia
Usar solo Heroicons disponibles en Filament. No mezclar librerias. Referencias: dashboard `squares-2x2`, convenios `document-text`, querellas `scale`, importaciones `arrow-up-tray`, historial `clock`.

## Responsive
Desktop puede usar dos columnas. Tablet apila secciones y usa metricas en dos columnas. Movil prioriza legibilidad, scroll horizontal en tablas y botones a ancho completo cuando corresponda.

## Accesibilidad
Mantener contraste legible, foco visible, labels claros, tablas semanticas y estados no dependientes solo del color. Respetar `prefers-reduced-motion`.

## Uso Correcto
- Usar metric cards para resumen de importacion.
- Usar alertas cortas con titulo, icono y una linea explicativa.
- Mostrar previews en tablas, no en JSON.

## Uso Incorrecto
- Mostrar rutas internas, errores SQL o excepciones tecnicas al usuario.
- Saturar una pantalla con dorado.
- Usar textos menores a 12 px.
- Mostrar datos operativos como objetos JSON.
