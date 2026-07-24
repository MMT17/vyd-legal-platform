# 1. Objetivo del módulo

El módulo de importaciones permite crear o actualizar registros masivamente mediante archivos CSV. Su objetivo es reducir carga manual, disminuir errores de digitación y mantener trazabilidad sobre operaciones que afectan muchos registros.

En VYD Legal Platform, la importación no debe entenderse como una simple carga de archivo. Es un flujo de revisión y decisión. El usuario debe saber qué archivo está usando, qué datos contiene, qué registros se crearán, cuáles se actualizarán, qué filas presentan problemas y cuándo la información será modificada.

El módulo debe servir inicialmente para Convenios y Querellas, respetando las diferencias de cada dominio sin crear experiencias desconectadas. El patrón de uso debe ser común: preparar archivo, cargar, validar, revisar, confirmar, ejecutar y consultar historial.

# 2. Usuarios autorizados

El acceso al módulo depende de permisos definidos por el producto y la administración del sistema.

Los usuarios sin autorización:

- no deben ver las acciones de importación;
- no deben acceder directamente a la página;
- no deben poder ejecutar el proceso.

La experiencia no debe revelar detalles internos a usuarios sin permiso. Si una persona intenta acceder a una importación sin autorización, debe recibir un mensaje sobrio y comprensible, sin información técnica.

No se definen en este documento nombres técnicos de permisos. La implementación deberá conectarse a las reglas vigentes del proyecto y validar autorización tanto en la interfaz como en la ejecución.

# 3. Principios específicos del importador

El importador debe guiar al usuario paso a paso. Cargar datos masivos puede tener consecuencias importantes; por eso la experiencia debe reducir incertidumbre y no forzar decisiones apresuradas.

No se deben mezclar carga, validación, confirmación y resultado en una sola vista saturada. Cada etapa debe tener una intención clara.

El módulo actual debe mantenerse siempre visible: Convenios o Querellas. El usuario debe saber en todo momento qué tipo de información está importando.

El nombre del archivo seleccionado debe mostrarse de forma persistente durante el flujo. Si el usuario reemplaza el archivo, ese cambio debe ser evidente.

La vista previa no debe ejecutar cambios. Validar y revisar no debe crear, actualizar ni eliminar registros.

La diferencia entre creación y actualización debe explicarse claramente. Crear significa que el registro no existe según las reglas vigentes del dominio. Actualizar significa que el sistema encontró un registro existente y propone modificarlo.

El usuario debe poder corregir y volver a cargar. Un archivo con errores no debe sentirse como un callejón sin salida.

Todo proceso debe dejar historial y trazabilidad. La importación debe servir también para reconstruir qué ocurrió después.

# 4. Flujo principal

## 1. Selección del tipo de importación

Objetivo: definir si se trabajará con Convenios o Querellas.

Información visible: nombre del módulo, descripción breve y alcance de la importación.

Acción principal: elegir o confirmar el módulo.

Acción secundaria: revisar historial del módulo, si está disponible.

Condiciones para avanzar: usuario autorizado y módulo definido.

Posibles errores: acceso no autorizado o módulo no disponible.

Resultado esperado: el usuario entra a un flujo contextualizado.

## 2. Preparación y descarga de plantilla

Objetivo: ayudar al usuario a preparar un CSV compatible.

Información visible: botón de descarga, columnas esperadas, separador coma y encoding recomendado UTF-8.

Acción principal: descargar plantilla.

Acción secundaria: continuar si el usuario ya tiene archivo preparado.

Condiciones para avanzar: ninguna, salvo autorización.

Posibles errores: plantilla no disponible.

Resultado esperado: el usuario cuenta con una referencia confiable.

## 3. Selección del archivo

Objetivo: elegir el CSV que se revisará.

Información visible: área de selección o arrastre, formatos permitidos y módulo actual.

Acción principal: seleccionar archivo.

Acción secundaria: cancelar o volver.

Condiciones para avanzar: archivo seleccionado con extensión permitida.

Posibles errores: extensión inválida, archivo ilegible o archivo vacío.

Resultado esperado: el sistema muestra el archivo seleccionado y habilita revisión.

## 4. Lectura y validación

Objetivo: leer encabezados y filas sin modificar registros.

Información visible: estado de validación en curso y nombre del archivo.

Acción principal: ninguna durante el procesamiento.

Acción secundaria: cancelar solo si la implementación lo permite.

Condiciones para avanzar: lectura completada.

Posibles errores: encabezados incorrectos, columnas faltantes, caracteres no reconocidos o archivo vacío.

Resultado esperado: resumen de validación y vista previa.

## 5. Vista previa

Objetivo: mostrar una muestra comprensible de datos y resultado esperado por fila.

Información visible: filas de muestra, número de fila original, estado esperado y datos jurídicos principales.

Acción principal: continuar a revisión o confirmación.

Acción secundaria: reemplazar archivo.

Condiciones para avanzar: al menos una fila válida o regla explícita para continuar con importación parcial.

Posibles errores: todas las filas inválidas o estructura incompatible.

Resultado esperado: el usuario entiende qué ocurrirá antes de modificar datos.

## 6. Revisión de incidencias

Objetivo: permitir entender problemas y corregir el archivo.

Información visible: fila, campo, valor recibido, motivo y forma de corregir.

Acción principal: descargar reporte o reemplazar archivo.

Acción secundaria: filtrar filas con problemas.

Condiciones para avanzar: depende de si existen filas válidas procesables.

Posibles errores: volumen alto de incidencias.

Resultado esperado: el usuario sabe cómo corregir.

## 7. Confirmación

Objetivo: pedir autorización explícita antes de modificar información.

Información visible: módulo, archivo, creados, actualizados, omitidos, errores y advertencia de impacto.

Acción principal: Importar registros.

Acción secundaria: Volver a revisar.

Condiciones para avanzar: usuario autorizado y confirmación explícita.

Posibles errores: pérdida de sesión, permisos insuficientes o datos vencidos.

Resultado esperado: el usuario decide ejecutar con información suficiente.

## 8. Ejecución

Objetivo: aplicar cambios confirmados.

Información visible: proceso en curso, archivo y módulo.

Acción principal: bloqueada mientras se ejecuta.

Acción secundaria: ninguna que genere doble ejecución.

Condiciones para avanzar: proceso finalizado.

Posibles errores: falla parcial, falla total o interrupción.

Resultado esperado: registros creados o actualizados y resultado persistente.

## 9. Resultado

Objetivo: comunicar qué ocurrió.

Información visible: estado general, creados, actualizados, omitidos, fallidos, fecha, hora y usuario.

Acción principal: ver registros importados o revisar detalle.

Acción secundaria: realizar otra importación.

Condiciones para avanzar: resultado registrado.

Posibles errores: resultado parcial o reporte no disponible.

Resultado esperado: el usuario sabe qué pasó y qué puede hacer.

## 10. Historial

Objetivo: recuperar contexto y auditar importaciones previas.

Información visible: módulo, archivo, usuario, fecha, estado y resumen.

Acción principal: abrir detalle.

Acción secundaria: descargar reporte si existe.

Condiciones para avanzar: historial disponible.

Posibles errores: historial vacío.

Resultado esperado: la operación queda trazable.

# 5. Pantalla inicial

La pantalla inicial debe contener título de importación e indicación clara del módulo: Convenios o Querellas. Debe incluir una explicación breve: el usuario cargará un CSV para revisar registros antes de crearlos o actualizarlos.

Debe existir una acción para descargar plantilla. La plantilla debe estar cerca del área de carga, no escondida en ayuda secundaria.

La pantalla debe incluir un área para seleccionar o arrastrar CSV. Debe mostrar formatos permitidos. Los límites de tamaño solo deben mostrarse si están confirmados por la implementación.

El botón principal debe estar deshabilitado hasta seleccionar un archivo válido. También debe existir un enlace para revisar historial.

En esta pantalla no deben mostrarse estadísticas, tablas de errores ni información técnica. Todavía no hay datos que resumir.

# 6. Archivo seleccionado

Después de seleccionar un archivo, la vista debe mostrar:

- nombre del archivo;
- tamaño en formato legible;
- opción de reemplazar;
- opción de quitar;
- tipo de importación;
- botón “Revisar archivo”.

Si el archivo tiene extensión inválida, el problema debe mostrarse antes de procesarlo. El mensaje debe ser claro: “El archivo seleccionado no es CSV. Selecciona un archivo con extensión .csv.”

Reemplazar archivo debe limpiar validaciones anteriores. Quitar archivo debe volver al estado inicial.

# 7. Validación y vista previa

La validación no debe modificar registros. Esta regla debe ser visible en la experiencia: revisar un archivo no equivale a importarlo.

La vista debe mostrar primero una muestra de los datos y luego el resumen de validación. El usuario necesita reconocer el contenido antes de interpretar indicadores.

La vista previa debe mostrar una cantidad limitada de filas. Si existen más filas, debe indicarse claramente que se trata de una muestra.

El resumen debe priorizar como máximo tres indicadores:

- filas listas para importar;
- filas que requieren corrección;
- registros existentes que serán actualizados.

La cantidad total puede mostrarse como contexto secundario.

Los estados deben diferenciar:

- creación;
- actualización;
- omisión;
- error bloqueante;
- advertencia.

La creación y actualización deben explicarse con lenguaje del dominio, no como operaciones técnicas.

# 8. Tabla de vista previa

La tabla de vista previa debe mostrar columnas jurídicas reconocibles. No debe mostrar todas las columnas si no son necesarias para decidir.

Debe permitir identificar el número de fila original del CSV. Esto es esencial para corregir el archivo externo.

Cada fila debe indicar el resultado esperado: se creará, se actualizará, se omitirá o requiere corrección. El estado debe usar texto e iconos. El color no puede ser el único indicador.

La tabla debe permitir acceder al detalle del error cuando exista. Ese detalle puede estar expandido en la fila o disponible mediante una acción secundaria.

Los encabezados deben ser comprensibles. No se deben mostrar estructuras JSON, nombres internos, claves técnicas o mensajes del sistema.

La tabla debe estar pensada para revisión, no para edición masiva. La corrección principal ocurre reemplazando el archivo.

# 9. Tratamiento de errores

Cada error debe identificar, cuando corresponda:

- fila;
- campo;
- valor recibido;
- motivo;
- forma de corregirlo.

Ejemplo correcto:

“Fila 12 · Estado: ‘Finalizado’ no es un valor reconocido. Usa ‘Firmado’, ‘Frustrado’ o ‘En negociación’.”

No se deben mostrar excepciones, SQL, nombres de tablas, rutas o stack traces.

Cuando existan muchos errores, la experiencia debe:

- mostrar resumen;
- permitir filtrar filas con problemas;
- permitir descargar reporte;
- permitir volver y reemplazar el archivo.

Los errores deben diferenciarse de advertencias. Una advertencia puede permitir continuar. Un error bloqueante impide procesar esa fila o el archivo completo, según su naturaleza.

# 10. Reglas de creación y actualización

El sistema debe utilizar la clave única definida por cada módulo según reglas vigentes del dominio. Este documento no fija claves técnicas si no están confirmadas.

Un registro existente debe presentarse como actualización. Un registro inexistente debe presentarse como creación. Nunca debe producirse una duplicación silenciosa.

La vista previa debe explicar qué campos cambiarán cuando sea razonable. Si mostrar todos los cambios genera ruido, debe priorizar los cambios más relevantes y permitir acceso al detalle.

Una fila inválida no debe impedir necesariamente procesar filas válidas, salvo que exista un error estructural del archivo. Por ejemplo, un encabezado esencial faltante puede impedir validar todo el archivo. En cambio, una fecha inválida en una fila puede afectar solo esa fila.

Las filas duplicadas dentro del mismo CSV deben identificarse antes de importar. El usuario debe saber cuál es el problema y cómo corregirlo.

# 11. Confirmación

Antes de ejecutar la importación, la pantalla debe mostrar:

- módulo;
- archivo;
- filas que se crearán;
- filas que se actualizarán;
- filas omitidas;
- filas con error;
- advertencia de que la acción modificará información.

La acción principal debe decir:

“Importar registros”

La acción secundaria debe decir:

“Volver a revisar”

No se debe utilizar únicamente “Confirmar” o “Aceptar”.

Si existen errores, la pantalla debe explicar si las filas válidas podrán procesarse. El usuario no debe tener que inferirlo.

# 12. Ejecución y progreso

Durante la ejecución, la interfaz debe impedir una segunda ejecución. El botón principal debe quedar deshabilitado o cambiar a un estado de procesamiento.

El contexto debe conservarse: módulo, archivo y resumen confirmado.

La vista debe mostrar que el proceso está en curso. No debe mostrar una barra de porcentaje falsa. El progreso real solo debe mostrarse cuando el sistema pueda calcularlo.

La advertencia de no cerrar la página solo debe mostrarse si es realmente necesaria. Si el proceso puede continuar en segundo plano, el mensaje debe reflejarlo. Esta capacidad requiere validación técnica durante la implementación.

# 13. Resultado final

Al finalizar, la pantalla debe mostrar:

- estado general;
- registros creados;
- registros actualizados;
- registros omitidos;
- registros fallidos;
- fecha y hora;
- usuario que ejecutó la importación;
- acceso al detalle;
- descarga de errores cuando corresponda.

Acciones posteriores:

- ver registros importados;
- realizar otra importación;
- revisar historial.

No debe mostrarse información técnica. Si la importación falló parcialmente, debe decirlo con claridad y orientar el siguiente paso.

# 14. Historial de importaciones

Cada registro del historial debe mostrar:

- módulo;
- archivo;
- usuario;
- fecha;
- estado;
- total;
- creados;
- actualizados;
- omitidos;
- fallidos.

Debe permitir abrir un detalle. El historial debe servir para auditoría y recuperación de contexto, no solo como registro técnico.

El detalle debe permitir entender qué archivo se procesó, qué resultado tuvo y qué errores o advertencias se registraron. Si existe reporte descargable, debe estar disponible desde esta vista.

# 15. Estados obligatorios de la interfaz

| Estado | Mensaje principal | Acciones disponibles |
| --- | --- | --- |
| Estado inicial | Selecciona un archivo CSV para revisar antes de importar. | Descargar plantilla, seleccionar archivo, ver historial. |
| Archivo seleccionado | Archivo listo para revisión. | Revisar archivo, reemplazar, quitar. |
| Validando | Estamos revisando estructura, encabezados y filas. | Sin acción principal; cancelar solo si está soportado. |
| Validación exitosa | El archivo está listo para importar. | Continuar a confirmación, reemplazar archivo. |
| Validación con advertencias | Hay observaciones, pero existen filas que pueden procesarse. | Revisar incidencias, continuar si corresponde, reemplazar archivo. |
| Validación con errores | Algunas filas requieren corrección. | Filtrar errores, descargar reporte, reemplazar archivo. |
| Confirmación | Esta acción modificará información del módulo. | Importar registros, volver a revisar. |
| Importando | La importación está en curso. | Ninguna acción que duplique ejecución. |
| Importación exitosa | Todos los registros válidos fueron procesados. | Ver registros, importar otro archivo, ver historial. |
| Importación parcial | Algunos registros fueron procesados y otros fallaron u omitidos. | Descargar errores, ver detalle, importar otro archivo. |
| Importación fallida | No fue posible completar la importación. | Ver detalle, reemplazar archivo, intentar nuevamente si corresponde. |
| Historial vacío | Todavía no hay importaciones registradas. | Iniciar importación, descargar plantilla. |

# 16. Casos límite

Archivo vacío: debe mostrar que no hay filas para procesar y permitir reemplazar archivo.

Encabezados incorrectos: debe listar columnas faltantes o no reconocidas. Si impide validar, se considera error estructural.

Columnas faltantes: debe explicar cuáles son obligatorias para el módulo.

Extensión inválida: debe detectarse antes de procesar.

Caracteres especiales: deben soportarse si el archivo está en UTF-8. Otros encodings requieren validación técnica.

Filas duplicadas dentro del mismo CSV: deben detectarse y presentarse como incidencias.

Registros existentes: deben mostrarse como actualización, no duplicarse.

Archivo con todas las filas inválidas: no debe permitir importar registros.

Archivo con mezcla de filas válidas e inválidas: debe explicar si puede procesar filas válidas. La regla concreta requiere validación durante implementación.

Pérdida de sesión: debe impedir ejecución y solicitar reingreso sin mostrar errores técnicos.

Recarga accidental: debe evitar dobles ejecuciones. La recuperación exacta del estado requiere validación técnica.

Doble clic: debe bloquearse la acción mientras se procesa.

Archivo pequeño: debe recorrer el mismo flujo, sin saltarse confirmación.

Archivo grande: debe mostrar procesamiento en curso. Límites de tamaño, tiempos y ejecución en segundo plano requieren validación técnica.

# 17. Criterios de aceptación

La experiencia está aprobada cuando:

- el usuario comprende qué archivo debe cargar;
- puede descargar una plantilla;
- sabe qué se creará y qué se actualizará;
- puede reconocer y corregir errores;
- ninguna información técnica queda expuesta;
- no se ejecutan cambios antes de confirmar;
- la acción no puede ejecutarse dos veces;
- el resultado queda registrado;
- los permisos se respetan;
- existe un camino claro para volver a intentar.

Además, el flujo debe poder completarse para una importación frecuente sin capacitación externa. La documentación puede apoyar, pero la interfaz debe sostener la tarea por sí misma.

# 18. Fuera de alcance

Quedan fuera de esta primera versión, salvo que ya existan:

- importación Excel;
- mapeo manual de columnas;
- edición masiva dentro de la vista previa;
- reintento automático en segundo plano;
- programación de importaciones;
- integración directa con sistemas externos;
- reversión completa de una importación.

Estas capacidades podrán evaluarse en versiones posteriores. No deben incorporarse parcialmente si comprometen claridad, trazabilidad o seguridad del flujo principal.

# 19. Estado de implementación

El módulo de importaciones de Convenios y Querellas se considera completado para esta versión.

La implementación fue revisada manualmente en ambos módulos y aprobada en los siguientes aspectos:

- selección manual de archivo CSV;
- carga mediante Drag & Drop;
- reemplazo y eliminación de archivo seleccionado;
- descarga de plantilla oficial;
- lectura y validación previa sin modificar registros;
- vista previa de datos;
- métricas principales de revisión;
- tabla de previsualización con columnas del dominio;
- navegación exclusiva por etapas;
- revisión de incidencias;
- confirmación previa a la ejecución;
- importación de registros;
- resultado final;
- acciones posteriores;
- consistencia entre Convenios y Querellas.

No deben realizarse más cambios visuales o funcionales sobre este flujo salvo que exista una nueva decisión de producto o una regresión verificada.

## Componentes reutilizables

La implementación utiliza componentes reutilizables para mantener consistencia visual y funcional entre Convenios y Querellas:

| Componente | Uso |
| --- | --- |
| `x-vyd.stepper` | Indicador de progreso del flujo por etapas. |
| `x-vyd.file-upload-card` | Selección inicial y Drag & Drop de CSV. |
| `x-vyd.metric-card` | Métricas de revisión y resultado. |
| `x-vyd.preview-table` | Vista previa tabular con estados por fila. |
| `x-vyd.status-badge` | Estados visibles como creación, actualización, omisión o corrección requerida. |
| `x-vyd.empty-state` | Estado sin incidencias o sin datos. |
| `x-vyd.alert` | Advertencias, errores y mensajes de contexto. |
| `x-vyd.info-pair` | Resúmenes etiqueta/valor en confirmación y resultado. |

Estos componentes son compartidos por ambos módulos. Cualquier ajuste futuro debe realizarse de forma reutilizable y verificando que no rompa la consistencia entre Convenios y Querellas.

## Decisiones principales confirmadas

Una etapa visible a la vez: el flujo no acumula secciones. Al avanzar, el contenido central se reemplaza y el usuario conserva contexto mediante el stepper.

Importación parcial de filas válidas: cuando el archivo contiene una mezcla de filas válidas e inválidas, las filas válidas pueden procesarse y las incidencias quedan disponibles para revisión.

Soporte CSV con coma y punto y coma: la plantilla oficial utiliza coma, pero el sistema mantiene tolerancia para archivos reales separados por coma o punto y coma.

Parser tolerante de fechas: la interfaz recomienda `yyyy-mm-dd`, `dd-mm-yyyy` y `dd/mm/yyyy`; el motor puede aceptar otros formatos soportados si puede normalizarlos de forma segura.

Historial visible según permiso de importación: un usuario autorizado para importar en Convenios puede revisar historial y detalle de Convenios; un usuario autorizado para importar en Querellas puede revisar historial y detalle de Querellas.

No se muestra información técnica al usuario final: errores SQL, JSON, clases, rutas internas, excepciones y trazas quedan fuera de la interfaz.
