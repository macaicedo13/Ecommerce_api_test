# Guía de Pruebas (Testing)

Este proyecto incluye tanto pruebas automatizadas como procedimientos de verificación manual para asegurar la calidad de los flujos de negocio.

## ✅ Pruebas Automatizadas (Backend)

El backend utiliza **PHPUnit** para validar la lógica de las entidades y reglas de negocio críticas.

### Ejecución de Pruebas
Si estás usando Docker, ejecuta el siguiente comando desde la raíz:

```bash
docker exec -it phptest_backend php bin/phpunit --no-configuration --bootstrap vendor/autoload.php tests/Entity --testdox
```

### 📝 Reporte de Resultados Detallado
Para generar un archivo **`test_results.txt`** organizado por bloques (Pedidos y Productos) y con descripciones legibles de cada prueba:

```bash
# Dentro del contenedor o en la carpeta backend
composer test
```
Este comando utiliza el modo `--testdox` para que el reporte no solo muestre puntos, sino una lista clara de qué funcionalidad se validó en cada bloque.

### Cobertura de Pruebas
Actualmente, las pruebas se enfocan en:
1. **Entidad Pedido (`Order`)**:
   - Creación correcta de pedidos.
   - Cálculo exacto de subtotales, impuestos (15%) y totales.
   - Validación de estados (qué pedidos pueden ser modificados o pagados).
2. **Entidad Producto (`Product`)**:
   - Validación de precios positivos.
   - Control de inventario (el stock no puede ser negativo).

---

## 🧪 Verificación Manual (Frontend)

Para validar la experiencia de usuario, sigue estos procedimientos:

### 1. Flujo de Compra (Cliente)
- **Login**: Entra con cualquier ID y el rol `customer`.
- **Carrito**: Añade productos, cambia cantidades y verifica que el total se actualice en la barra de navegación y en la vista del carrito.
- **Pago**: Completa el pedido y verifica que el estado cambie a "Completado" en el historial.

### 2. Panel de Control (Admin)
- **Login**: Inicia sesión con el rol `admin`.
- **Vista Global**: Verifica que puedas ver todos los pedidos realizados, no solo los tuyos.
- **Creación**: Crea un producto nuevo y verifica que aparezca inmediatamente en el catálogo principal.

---

## 🛠️ Herramientas de Diagnóstico
- **Symfony Profiler**: Accede a `http://localhost:8000/_profiler` para depurar consultas SQL y rendimiento.
- **Consola del Navegador**: Verifica errores de red o de React si la interfaz no responde.
- **Logs de Docker**:
  ```bash
  docker-compose logs -f backend
  ```
