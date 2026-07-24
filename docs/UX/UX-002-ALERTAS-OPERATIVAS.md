# UX-002 - Alertas Operativas

# 1. Objetivo del módulo

Alertas Operativas debe funcionar como una bandeja de asuntos que requieren atención, no como un reporte técnico de condiciones detectadas.

El objetivo principal es que un abogado o usuario operativo pueda entrar al inicio del día, comprender rápidamente qué asuntos requieren acción o revisión, priorizar su trabajo y abrir el registro correspondiente sin recorrer listados extensos ni interpretar datos técnicos.

La pantalla debe responder una pregunta principal:

¿Qué asuntos requieren atención y qué debo revisar primero?

La experiencia debe ayudar a reducir carga administrativa, evitar duplicidad visual y transformar señales dispersas del sistema en una lista operativa clara, accionable y confiable.

# 2. Problema actual

La implementación actual calcula alertas dinámicamente y funciona como fuente de información, pero su presentación se comporta como un reporte. Esto genera varios problemas para uso diario:

- un mismo registro puede aparecer varias veces si cumple varias condiciones;
- las alertas se separan por tipo, no por asunto;
- los contadores superiores no explican qué atender primero;
- las tablas inferiores muestran muchas filas simultáneamente;
- no existe paginación real;
- no existe búsqueda;
- no existe una jerarquía operativa clara;
- el usuario debe interpretar manualmente la gravedad y la acción esperada;
- las acciones son genéricas y no siempre están disponibles por permisos.

El problema no es que falten datos. El problema es que los datos no están organizados como una bandeja de trabajo.

# 3. Usuarios autorizados

La pantalla requiere el permiso:

`reportes.ver`

Los usuarios previstos para esta versión son:

- Admin;
- Abogado Admin;
- Abogado Editor;
- Cliente Chilquinta Admin.

Todos los usuarios autorizados deben ver todos los asuntos disponibles para el módulo. En esta versión no se implementa una vista de "Mis alertas".

Las acciones sobre registros deben respetar permisos específicos:

- `convenios.ver`;
- `querellas.ver`;
- `procesos.editar`;
- `contactos.editar`.

# 4. Principios específicos

La experiencia debe cumplir estos principios:

- Una fila representa un asunto o registro, no una alerta aislada.
- Un asunto puede contener varias alertas asociadas.
- La prioridad debe ser simple, comprensible y derivada de reglas explícitas.
- La acción principal debe ser clara y no competir con acciones secundarias.
- La pantalla debe permitir filtrar, buscar y paginar.
- La información técnica no debe exponerse.
- La interfaz debe ayudar a decidir, no solo informar.
- La experiencia debe ser consistente para Convenios, Querellas, Procesos y Contactos.
- Las alertas son dinámicas y desaparecen cuando la condición deja de cumplirse.

# 5. Modelo conceptual

## Asunto

Un asunto es la unidad principal de la bandeja. Corresponde a un registro del sistema que requiere atención.

Puede ser:

- un Convenio;
- una Querella;
- un Proceso;
- un Contacto.

Cada asunto debe aparecer una sola vez en la bandeja, aunque tenga múltiples alertas asociadas.

## Alerta asociada

Una alerta asociada es una condición detectada sobre un asunto.

Ejemplo:

Un Convenio puede tener simultáneamente:

- falta de abogado;
- falta de documentos;
- falta de actividad.

En la bandeja debe mostrarse como un único asunto con tres badges o señales asociadas.

## Prioridad

La prioridad operativa resume el nivel de atención que requiere el asunto.

En V1 existen solo dos niveles:

- Requiere acción;
- Requiere revisión.

No se deben usar categorías como "Crítica" o "Urgente" hasta que existan reglas de producto que las respalden.

## Acción sugerida

Cada asunto debe mostrar una acción recomendada basada en la alerta más relevante.

Ejemplos:

- Asignar responsable;
- Agendar fecha;
- Revisar contacto;
- Agregar documentos;
- Revisar actividad.

La acción sugerida orienta al usuario, aunque la acción disponible en V1 sea abrir el registro original.

# 6. Tipos de alerta existentes

La V1 debe considerar los tipos de alerta ya existentes:

| Tipo de alerta | Módulo | Descripción funcional |
| --- | --- | --- |
| Convenio sin actividad | Convenios | Convenio sin movimientos recientes según auditoría. |
| Querella sin actividad | Querellas | Querella sin movimientos recientes según auditoría. |
| Convenio sin documentos | Convenios | Convenio sin documentos relacionados. |
| Querella sin documentos | Querellas | Querella sin documentos relacionados. |
| Convenio sin abogado | Convenios | Convenio sin abogado responsable ni nombre de abogado. |
| Querella sin abogado | Querellas | Querella sin abogado responsable. |
| Proceso sin fecha | Procesos | Proceso pendiente de calendarización. |
| Contacto importante pendiente | Contactos | Contacto marcado como importante, futuro o sin fecha. |

# 7. Regla de agrupación por registro

La regla central del rediseño es:

Una fila = un asunto o registro.

La agrupación conceptual debe utilizar:

`tipo de registro + ID del registro`

Ejemplo:

Si el Convenio `#123` cumple tres condiciones:

- sin abogado;
- sin documentos;
- sin actividad;

debe mostrarse una sola fila:

```text
Convenio 123
[Sin abogado] [Sin documentos] [Sin actividad]
```

No debe mostrarse como tres filas independientes.

Esta regla reduce duplicidad, mejora lectura y alinea la pantalla con la forma en que los abogados piensan el trabajo: por asunto, no por condición técnica.

# 8. Regla de prioridad de la versión 1

La V1 utiliza dos niveles de prioridad operativa.

## Requiere acción

Se aplica cuando el asunto tiene al menos una de estas condiciones:

- registro sin abogado;
- proceso sin fecha;
- contacto importante pendiente.

Estos casos requieren intervención operativa más directa porque bloquean responsabilidad, calendarización o seguimiento.

## Requiere revisión

Se aplica cuando el asunto tiene al menos una de estas condiciones y ninguna alerta de nivel superior:

- registro sin documentos;
- registro sin actividad.

Estos casos requieren revisión, pero no necesariamente una intervención inmediata antes de entender contexto.

## Múltiples alertas

Si un asunto tiene varias alertas, adopta el nivel más alto presente.

Ejemplo:

Un Convenio con `Sin abogado` y `Sin documentos` debe clasificarse como `Requiere acción`.

## Orden dentro del mismo nivel

Dentro del mismo nivel, ordenar por:

1. cantidad de alertas asociadas;
2. antigüedad;
3. fecha relevante;
4. identificador estable.

Estas reglas pueden evolucionar posteriormente si el producto define prioridades más sofisticadas.

# 9. Estructura completa de la pantalla

La pantalla debe organizarse en cinco bloques:

1. Encabezado.
2. Resumen superior.
3. Filtros y búsqueda.
4. Bandeja principal paginada.
5. Detalle mediante modal o slide-over.

Wireframe general:

```text
Alertas Operativas
Asuntos que requieren atención operativa.

[Actualizar alertas]

Resumen
[Asuntos sin responsable] [Asuntos sin documentos] [Asuntos sin actividad] [Procesos y contactos pendientes]

Filtros rápidos
[Todos] [Convenios] [Querellas] [Procesos] [Contactos] [Sin abogado] [Sin documentos] [Sin actividad] [Sin fecha] [Contactos pendientes]

Búsqueda
[Buscar por caso, NIS, nombre, RUC, RIT, comuna o responsable]

Filtros avanzados
[Responsable] [Antigüedad] [Comuna] [Estado] [Tipo de alerta] [Fecha relevante]

Bandeja
--------------------------------------------------------------------------------
Prioridad          Asunto             Alertas              Responsable     Acción
Requiere acción    Convenio 30001     Sin abogado, docs    Sin asignar     Abrir
Requiere revisión  Querella 20001     Sin actividad        Abogado X       Abrir
--------------------------------------------------------------------------------

Paginación
25 por página
```

# 10. Resumen superior

El resumen superior debe mostrar máximo cuatro métricas.

Propuesta:

1. Asuntos sin responsable.
2. Asuntos sin documentos.
3. Asuntos sin actividad.
4. Procesos y contactos pendientes.

Las métricas deben contar asuntos únicos cuando corresponda. Un mismo Convenio no debe sumarse varias veces en una métrica general por tener múltiples alertas.

Cuando sea viable, cada tarjeta debe actuar como filtro rápido.

Ejemplo:

Al seleccionar `Asuntos sin documentos`, la bandeja debe mostrar asuntos que tengan esa alerta asociada.

# 11. Filtros rápidos

Los filtros rápidos deben estar siempre visibles.

Filtros requeridos:

- Todos;
- Convenios;
- Querellas;
- Procesos;
- Contactos;
- Sin abogado;
- Sin documentos;
- Sin actividad;
- Sin fecha;
- Contactos pendientes.

No incluir:

- Mis alertas;
- Resueltas;
- Silenciadas;
- Descartadas.

Cada filtro debe cambiar la bandeja sin alterar las reglas de prioridad.

# 12. Filtros avanzados

Los filtros avanzados deben estar colapsados inicialmente.

Filtros sugeridos:

- responsable;
- antigüedad;
- comuna;
- estado;
- tipo de alerta;
- fecha relevante.

No todos los filtros aplican a todos los módulos. La interfaz debe evitar prometer campos inexistentes para un tipo de asunto.

Ejemplo:

`RUC` y `RIT` aplican a Querellas, pero no a Convenios.

# 13. Búsqueda

La búsqueda debe permitir encontrar asuntos según disponibilidad de datos por módulo.

Campos buscables sugeridos:

- caso;
- NIS;
- nombre;
- RUC;
- RIT;
- comuna;
- responsable.

La búsqueda debe operar sobre asuntos agrupados, no sobre filas de alertas individuales.

El resultado debe seguir mostrando una fila por asunto.

# 14. Bandeja principal

La bandeja principal es el centro de la pantalla.

Debe mostrar asuntos únicos, priorizados y paginados.

No debe comportarse como un reporte completo ni cargar todas las secciones en el render inicial.

La bandeja debe permitir:

- escanear rápidamente;
- entender prioridad;
- reconocer alertas asociadas;
- abrir el registro;
- acceder al detalle de alertas;
- paginar sin perder filtros.

# 15. Contenido de cada fila

Cada fila debe mostrar:

- tipo de registro;
- identificador legible;
- información secundaria útil;
- prioridad operativa;
- badges de alertas asociadas;
- responsable, si existe;
- antigüedad o fecha relevante;
- acción recomendada;
- botón para abrir el registro.

No mostrar:

- IDs internos;
- JSON;
- SQL;
- nombres técnicos;
- descripciones repetidas;
- filas duplicadas por cada alerta;
- trazas o datos de implementación.

Ejemplo:

```text
Requiere acción
Convenio 30001
Cliente Uno · Valparaíso
[Sin abogado] [Sin documentos] [Sin actividad]
Responsable: Sin asignar
Fecha relevante: 12-06-2026
Acción sugerida: Asignar responsable
[Abrir convenio] [Ver detalle]
```

# 16. Badges de alerta

Los badges deben identificar las alertas asociadas al asunto.

Badges requeridos:

- Sin abogado;
- Sin documentos;
- Sin actividad;
- Sin fecha;
- Contacto pendiente.

Los badges deben incluir texto. El color puede apoyar, pero no debe ser el único indicador.

Los badges deben ser comprensibles sin conocer la regla técnica exacta.

# 17. Acción principal

La acción principal de V1 es abrir el registro original.

Texto recomendado según módulo:

- Abrir convenio;
- Abrir querella;
- Abrir proceso;
- Abrir contacto.

No utilizar únicamente:

- Ver;
- Abrir;
- Ir.

La acción debe ser específica para reducir ambigüedad.

Si no existe permiso para actuar, no debe mostrarse un botón inútil.

# 18. Modal o slide-over de detalle

Al seleccionar un asunto, debe abrirse un modal o slide-over con:

- resumen del registro;
- todas las alertas asociadas;
- explicación comprensible de cada alerta;
- fecha relevante;
- acción sugerida;
- botón para abrir el registro original.

No incluir edición inline en esta versión.

Ejemplo:

```text
Convenio 30001
Cliente Uno · Valparaíso

Prioridad: Requiere acción

Alertas asociadas

1. Sin abogado
El convenio no tiene abogado responsable registrado.
Acción sugerida: Asignar responsable.

2. Sin documentos
El convenio no tiene respaldo documental asociado.
Acción sugerida: Revisar y agregar documentos desde el registro.

3. Sin actividad
No registra movimiento reciente.
Acción sugerida: Revisar estado del caso.

[Abrir convenio]
```

# 19. Paginación

La bandeja debe utilizar paginación real.

Valor inicial recomendado:

- 25 asuntos por página.

Opciones deseables:

- 25;
- 50;
- 100.

Estas opciones deben incluirse solo si la implementación puede soportarlas sin afectar rendimiento.

No se deben cargar todas las secciones y filas en el render inicial.

# 20. Ordenamiento

Orden inicial recomendado:

1. prioridad operativa;
2. cantidad de alertas asociadas;
3. antigüedad;
4. fecha relevante;
5. identificador estable.

La prioridad operativa debe ordenar `Requiere acción` antes que `Requiere revisión`.

El usuario puede necesitar ordenamientos adicionales posteriormente, pero V1 debe partir con un orden útil por defecto.

# 21. Estados vacíos

Debe existir estado vacío para:

- no hay alertas activas;
- búsqueda sin resultados;
- filtros sin resultados;
- usuario sin permisos de acción sobre los registros filtrados.

Ejemplo:

```text
No hay asuntos que requieran atención con estos filtros.
Prueba cambiar el filtro o limpiar la búsqueda.
```

El estado vacío no debe sentirse como error.

# 22. Permisos

La pantalla requiere:

`reportes.ver`

Las acciones deben respetar permisos específicos:

| Módulo | Permiso para acción |
| --- | --- |
| Convenios | `convenios.ver` |
| Querellas | `querellas.ver` |
| Procesos | `procesos.editar` |
| Contactos | `contactos.editar` |

La pantalla puede mostrar el asunto si el usuario tiene acceso general, pero no debe permitir acciones no autorizadas.

# 23. Comportamiento cuando no existe permiso de acción

Si el usuario puede ver la alerta pero no abrir o editar el registro:

- mostrar la información del asunto;
- ocultar el botón de acción principal;
- mostrar una indicación discreta:

```text
Sin permiso para abrir este registro.
```

No mostrar botones deshabilitados sin explicación.

No mostrar rutas, nombres de políticas ni permisos técnicos.

# 24. Cache y actualización de datos

Las alertas se calculan dinámicamente desde:

`App\Support\OperationalAlerts`

No existe una tabla persistente de alertas.

Los datos actuales pueden tardar hasta 60 segundos en reflejar una corrección debido al cache existente.

La interfaz no debe afirmar que una alerta desaparecerá instantáneamente.

Puede existir una acción:

```text
Actualizar alertas
```

Esta acción debe quedar sujeta a decisión técnica. Si se implementa, debe recalcular la bandeja de forma segura sin exponer información técnica.

# 25. Responsive

En escritorio:

- resumen superior en tarjetas horizontales;
- filtros rápidos visibles;
- bandeja con columnas principales;
- detalle en slide-over si es posible.

En tablet:

- resumen en dos columnas;
- filtros rápidos con scroll horizontal o wrap;
- bandeja con menos columnas visibles;
- detalle en modal.

En móvil:

- resumen apilado;
- filtros rápidos compactos;
- cada asunto como tarjeta vertical;
- badges visibles;
- acción principal al final de la tarjeta;
- detalle en modal de pantalla completa.

La experiencia móvil debe conservar la regla central:

Una tarjeta = un asunto.

# 26. Accesibilidad

La pantalla debe cumplir:

- acciones con texto claro;
- badges con texto;
- prioridad expresada con texto, no solo color;
- foco visible;
- filtros accesibles por teclado;
- modal o slide-over con foco controlado;
- tabla o lista con encabezados comprensibles;
- estados vacíos legibles;
- contraste suficiente en modo claro y oscuro.

No se debe depender solo del color para distinguir prioridad o tipo de alerta.

# 27. Casos límite

## Un asunto con múltiples alertas

Debe aparecer una sola vez, con todas sus alertas asociadas.

## Un asunto sin acción autorizada

Debe mostrarse sin botón inútil y con explicación discreta.

## Filtros sin resultados

Debe mostrar estado vacío accionable.

## Búsqueda parcial

Debe buscar por campos disponibles y mantener agrupación por asunto.

## Cache vigente

Si una condición fue corregida recientemente, puede seguir visible por hasta 60 segundos.

## Gran volumen de datos

Debe paginar y evitar cargar todas las filas en el render inicial.

## Datos incompletos

Si no existe nombre, comuna, responsable o fecha, mostrar texto comprensible como:

```text
Sin responsable
Sin fecha relevante
```

No mostrar valores técnicos vacíos.

# 28. Criterios de aceptación

La experiencia se aprueba cuando:

- un registro no aparece duplicado por múltiples alertas;
- el usuario comprende qué asuntos requieren acción;
- el usuario puede distinguir `Requiere acción` y `Requiere revisión`;
- el usuario puede filtrar;
- el usuario puede buscar;
- el usuario puede navegar sin recorrer cientos de filas;
- existe paginación real;
- cada fila tiene una acción clara cuando el usuario tiene permiso;
- las alertas asociadas se entienden;
- el detalle muestra todas las alertas del asunto;
- los permisos se respetan;
- las condiciones corregidas desaparecen al recalcular;
- no se expone información técnica;
- Convenios, Querellas, Procesos y Contactos comparten el mismo patrón;
- la pantalla funciona en escritorio, tablet y móvil.

# 29. Fuera de alcance

Queda fuera de V1:

- Mis alertas;
- alertas resueltas;
- historial de alertas;
- silenciar;
- descartar;
- posponer;
- asignación inline;
- carga de documentos inline;
- edición directa desde la bandeja;
- tabla persistente de alertas;
- reglas avanzadas de prioridad;
- notificaciones automáticas;
- automatización de tareas;
- exportación de alertas.

Estas capacidades pueden evaluarse en versiones posteriores, pero no deben incorporarse parcialmente si aumentan complejidad o rompen la claridad de la bandeja.
