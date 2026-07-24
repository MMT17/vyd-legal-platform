# VYD UX Guidelines

## Navegacion
La navegacion debe ser predecible y orientada a tareas. Mantener recursos Filament nativos cuando sea posible. Las acciones destructivas o irreversibles requieren confirmacion.

## Jerarquia Visual
Cada pantalla debe responder tres preguntas: que estoy viendo, que requiere atencion y cual es la accion siguiente. Usar titulos de seccion, metricas y alertas para organizar procesos.

## Formularios
Agrupar campos por significado juridico u operativo. Evitar bloques largos sin respiracion. Usar labels claros, ayuda breve y validaciones cerca del campo.

## Validaciones
Las validaciones deben explicar que corregir. No mostrar mensajes tecnicos, indices internos ni stack traces.

## Alertas
Cada alerta contiene icono, titulo corto y explicacion de una linea. Evitar parrafos extensos, informacion duplicada y colores simultaneos sin jerarquia.

## Confirmaciones
Una confirmacion debe indicar impacto concreto: registros a crear, registros a actualizar, filas invalidas y si se generara reporte.

## Procesos Largos
Dividir en pasos visibles. Mostrar estado final persistente, no solo una notificacion temporal.

## Tablas
Usar encabezados semanticos, scroll horizontal en movil y columnas prioritarias. No intentar mostrar todos los campos a la vez si perjudica lectura.

## Estados Vacios
Un estado vacio debe explicar que falta y ofrecer una accion clara cuando exista.

## Feedback De Acciones
Despues de acciones importantes mostrar resumen con resultados. En importaciones: procesados, creados, actualizados, omitidos y errores.

## Prevencion De Errores
Antes de importar, validar columnas, archivo, tamano, separador y filas invalidas. Si faltan columnas requeridas, impedir continuar.

## Lenguaje Para Abogados
Usar lenguaje claro y profesional: "3 filas tienen errores" y "Descargue el reporte para revisar los campos invalidos". Evitar "ValidationException" o nombres de clases.

## Informacion Tecnica
No mostrar JSON tecnico, rutas internas, SQL ni trazas al usuario. Esa informacion pertenece al log tecnico o al historial interno.
