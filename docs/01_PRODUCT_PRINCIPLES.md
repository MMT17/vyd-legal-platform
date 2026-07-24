# 1. Objetivo del documento

Este documento convierte la visión de VYD Legal Platform en reglas prácticas para diseñar, implementar y revisar cualquier módulo del producto.

Su propósito es evitar decisiones improvisadas. La plataforma debe crecer con criterios comunes, no como una suma de soluciones aisladas. Cada pantalla, flujo, formulario, tabla, acción y mensaje debe respetar una misma forma de entender el trabajo jurídico: clara, segura, consistente y orientada a decisiones reales.

Estos principios no reemplazan el criterio de producto, diseño o desarrollo. Lo ordenan. Sirven como referencia para evaluar si una funcionalidad está lista, si una experiencia es comprensible y si una solución respeta la promesa central de VYD: ayudar a los equipos jurídicos a trabajar mejor sin obligarlos a aprender un sistema complejo.

Toda nueva funcionalidad debe revisarse contra este documento antes de considerarse terminada.

# 2. Jerarquía de decisiones

Cuando exista tensión entre distintas alternativas, VYD debe tomar decisiones según esta prioridad:

1. Seguridad e integridad de la información.
2. Comprensión del usuario.
3. Prevención de errores.
4. Consistencia con el resto del producto.
5. Rapidez para completar la tarea.
6. Facilidad técnica de implementación.

La seguridad e integridad de la información siempre están primero. Ninguna mejora de velocidad, comodidad o simplicidad visual puede justificar pérdida de datos, duplicaciones silenciosas, exposición indebida o acciones ambiguas sobre información sensible.

La comprensión del usuario tiene prioridad sobre la conveniencia interna. Una solución técnicamente simple no debe imponerse si genera una experiencia confusa, insegura o difícil de explicar. Si el usuario no entiende qué está viendo, qué ocurrirá o qué resultado produjo una acción, la solución no está resuelta.

La prevención de errores debe considerarse desde el diseño inicial. No basta con validar al final. El producto debe guiar, limitar, advertir y confirmar cuando corresponda.

La consistencia protege el aprendizaje. Si una acción se comporta de una forma en Convenios, no debería comportarse de otra forma en Querellas sin una razón evidente.

La rapidez importa, pero no debe sacrificar control. Una tarea rápida que deja dudas no es una buena experiencia.

La facilidad técnica de implementación es relevante, pero ocupa el último lugar. La tecnología debe servir a la experiencia; no definirla.

# 3. Principios de interfaz

Una pantalla debe responder una pregunta principal. Antes de diseñar una vista, debe quedar claro qué pregunta ayuda a resolver: qué registros necesitan atención, qué documento corresponde revisar, qué cambió, qué falta o qué acción debe ejecutarse.

Una vista debe tener una acción principal reconocible. El usuario debe poder identificar rápidamente qué puede hacer. Las acciones secundarias pueden existir, pero no deben competir visualmente con la acción principal.

Las acciones secundarias deben tener menor peso visual. Exportar, cancelar, ver historial o descargar una plantilla no deben desplazar la tarea principal si el flujo está orientado a importar, guardar o confirmar.

La información necesaria para decidir debe mostrarse primero. Los datos complementarios deben quedar después, agrupados o disponibles bajo demanda. Mostrar todo al mismo tiempo no equivale a informar mejor.

No se debe repetir la misma información en diferentes bloques de una vista. La repetición genera ruido, dudas sobre cuál versión es la correcta y sensación de desorden.

El contexto del registro o proceso actual debe mantenerse visible. Si el usuario está importando Convenios, revisando una Querella o editando un documento, esa referencia debe ser clara durante todo el flujo.

Las tablas deben priorizar columnas útiles. Una tabla no debe ser un volcado completo de datos. Debe permitir escanear, comparar y tomar decisiones. Las columnas secundarias pueden ocultarse, agruparse o quedar disponibles en detalle.

Los estados deben expresarse con texto. El color puede apoyar, pero nunca debe ser el único indicador. Un usuario debe entender el estado aunque no distinga el color.

Las pantallas vacías deben explicar qué falta y qué hacer. Un estado vacío no debe sentirse como error. Debe decir por qué no hay información y cuál es el próximo paso posible.

Las tareas frecuentes deben requerir la menor cantidad razonable de pasos. Los flujos excepcionales no deben complicar el flujo habitual. Lo que ocurre una vez al mes no debe entorpecer lo que ocurre todos los días.

# 4. Principios de formularios

Un formulario debe solicitar solamente la información necesaria. Cada campo debe tener una razón. Los campos opcionales deben existir porque aportan valor, no porque podrían usarse algún día.

Los campos obligatorios y opcionales deben diferenciarse con claridad. El usuario no debe descubrir al guardar que faltaba información esencial.

Los nombres de campos deben ser comprensibles para usuarios jurídicos y administrativos. El lenguaje interno del sistema no debe imponerse sobre el lenguaje del trabajo real.

Cuando un dato pueda ser ambiguo, debe incluir ayuda breve. Esa ayuda debe explicar qué se espera, no enseñar conceptos técnicos.

Después de un error, los valores ingresados deben mantenerse. El usuario no debe repetir trabajo por una validación fallida.

La validación debe ocurrir lo antes posible sin interrumpir innecesariamente. Si el error puede detectarse al seleccionar un archivo, no debe esperar hasta el final del flujo. Si puede detectarse al completar un campo, no debe esperar hasta guardar.

Los errores deben mostrarse cerca del campo correspondiente. Un resumen general puede ayudar, pero no reemplaza el mensaje contextual.

No se deben usar mensajes genéricos. “Dato inválido” no es suficiente. El mensaje debe indicar qué ocurrió y cómo corregirlo.

El sistema no debe exigir al usuario memorizar identificadores. Si una acción depende de un registro, caso, cliente o documento, esa información debe mantenerse visible o seleccionable.

Las acciones con consecuencias importantes deben confirmarse. La confirmación debe explicar qué ocurrirá, no limitarse a pedir autorización.

# 5. Principios de acciones y confirmaciones

Toda acción debe entregar feedback visible. Después de guardar, importar, descargar, eliminar o actualizar, el usuario debe saber si la acción ocurrió y cuál fue el resultado.

Toda acción destructiva o difícil de revertir requiere confirmación. La confirmación debe explicar qué se modificará, qué se eliminará o qué impacto tendrá la acción.

No se deben confirmar acciones rutinarias y reversibles sin motivo. Pedir confirmación para todo debilita las confirmaciones realmente importantes y hace más lento el trabajo.

Los botones ambiguos deben evitarse. “Aceptar”, “OK” o “Continuar” solo son adecuados cuando el contexto es completamente evidente. En general, deben preferirse verbos específicos: Importar, Actualizar, Eliminar, Descargar, Guardar, Reemplazar.

Las acciones deben deshabilitarse cuando todavía no puedan ejecutarse. Si falta un archivo, un campo requerido o una selección necesaria, la acción principal no debe estar disponible.

El producto debe evitar dobles ejecuciones. Cuando una operación está en curso, la acción debe bloquearse o cambiar de estado para impedir duplicaciones.

Las operaciones no inmediatas deben mostrar progreso o estado de avance. Si el progreso real no puede calcularse, no debe mostrarse una barra falsa. Es mejor decir que el proceso está en curso.

Al terminar, el sistema debe indicar resultado y siguiente paso posible. Una buena acción no termina en “listo”; termina explicando qué cambió y qué puede hacer ahora el usuario.

# 6. Principios de errores

VYD nunca debe mostrar JSON al usuario final. Nunca debe mostrar SQL. Nunca debe mostrar excepciones, clases, rutas internas o trazas. Esa información pertenece al diagnóstico técnico, no a la experiencia de usuario.

Un error debe explicar qué ocurrió, dónde ocurrió y cómo corregirlo. Debe mantener visible el contexto del proceso, registro o archivo involucrado.

Las advertencias deben diferenciarse de errores bloqueantes. Una advertencia informa un riesgo o condición revisable. Un error bloqueante impide continuar hasta corregir el problema.

El lenguaje nunca debe culpar al usuario. El sistema debe ayudar a resolver, no señalar culpables.

Cuando existan muchos errores, debe permitirse descargar un reporte. Revisar decenas o cientos de problemas directamente en pantalla puede ser ineficiente.

Ejemplos:

| Mensaje incorrecto | Mensaje correcto |
| --- | --- |
| Error SQL: duplicate key | Ya existe un registro con este identificador. Revisa si corresponde actualizarlo en lugar de crear uno nuevo. |
| Exception: invalid date format | La fecha ingresada no tiene un formato reconocido. Usa yyyy-mm-dd, dd-mm-yyyy o dd/mm/yyyy. |
| Validation failed | Hay campos que requieren corrección antes de continuar. Revisa los mensajes marcados en el formulario. |
| JSON parse error | El archivo no pudo leerse correctamente. Verifica que sea CSV, use separador coma y esté guardado en UTF-8. |
| Unauthorized | No tienes autorización para realizar esta acción. Si necesitas acceso, solicita revisión a un administrador. |

# 7. Principios de información y métricas

Una métrica debe servir para tomar una decisión. Si un número no ayuda a priorizar, revisar, corregir o entender un resultado, probablemente no debe estar en primer plano.

No se deben mostrar métricas decorativas. Más indicadores no significan mejor información. En un resumen, deben priorizarse como máximo tres indicadores principales.

La información total, válida, inválida, creada y actualizada debe diferenciarse con claridad. Estos conceptos no son intercambiables. Un usuario debe entender cuántos registros se procesaron, cuántos quedaron listos y cuántos requieren atención.

El resumen debe permitir acceder al detalle. Un número agregado puede orientar, pero el usuario debe poder revisar qué registros componen ese resultado cuando sea necesario.

No se deben ocultar resultados parciales. Si una operación tuvo éxito parcial, el sistema debe decirlo explícitamente. Presentarla como éxito total o error total produce desconfianza.

Fechas, cantidades y estados deben usar formatos consistentes en toda la plataforma. La consistencia reduce interpretación y evita errores.

# 8. Principios de permisos y seguridad

Cada usuario debe ver solo la información correspondiente a su rol. El principio de mínimo acceso necesario debe aplicarse en vistas, acciones, datos y descargas.

Las acciones no autorizadas deben ocultarse o no estar disponibles. Sin embargo, la seguridad no puede depender únicamente de ocultar botones. La autorización debe respetarse también al intentar acceder directamente o ejecutar una acción.

Las acciones sensibles deben confirmarse. Esto incluye operaciones que modifican muchos registros, eliminan información, exponen datos o cambian estados relevantes.

Las acciones relevantes deben registrarse. La trazabilidad no es un lujo; es parte de la confianza operativa.

Los mensajes no deben exponer datos internos. Un usuario no autorizado no necesita saber si un registro existe, qué tabla falló o qué regla interna bloqueó la acción. Debe recibir una explicación adecuada a su rol.

La seguridad debe sentirse clara, no hostil. Cuando una acción no está permitida, el producto debe explicarlo con sobriedad.

# 9. Criterios de aceptación de UX

Una funcionalidad no se considera terminada hasta que:

- El usuario comprende su propósito.
- La acción principal es evidente.
- Los estados inicial, carga, éxito, vacío y error están diseñados.
- Los errores son accionables.
- Los permisos están contemplados.
- Las acciones sensibles están protegidas.
- La experiencia es consistente con otros módulos.
- No se expone información técnica.
- Puede utilizarse sin capacitación externa para una tarea frecuente.

Estos criterios deben revisarse antes de aprobar una funcionalidad. Si alguno falla, la funcionalidad puede estar técnicamente operativa, pero no está terminada como producto.

# 10. Lista de verificación

Antes de aprobar una funcionalidad, revisar:

- ¿La pantalla responde una pregunta principal?
- ¿La acción principal se reconoce sin explicación externa?
- ¿La información esencial aparece antes que la complementaria?
- ¿Los campos solicitados son realmente necesarios?
- ¿Los mensajes de error indican qué ocurrió, dónde y cómo corregirlo?
- ¿Existe estado vacío, estado de carga, éxito y error?
- ¿Las acciones sensibles explican consecuencias antes de ejecutarse?
- ¿Se evita mostrar información técnica al usuario final?
- ¿Los permisos están aplicados en vista y ejecución?
- ¿La experiencia es consistente con módulos similares?
- ¿El resultado de cada acción queda claro?
- ¿La funcionalidad reduce trabajo o incertidumbre en lugar de aumentarlos?
