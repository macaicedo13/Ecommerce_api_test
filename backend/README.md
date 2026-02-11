# Backend - API de Gestión de Pedidos

Esta es la API robusta encargada de la lógica de negocio, persistencia de datos y gestión de pedidos, desarrollada con **Symfony 7**.

## 🛠️ Tecnologías
- **PHP 8.2**
- **Symfony 7.x**
- **MySQL 8.0**
- **Doctrine ORM**
- **NelmioApiDocBundle** (Swagger)
- **NelmioCorsBundle** (CORS management)

## 📂 Estructura Principal
- `src/Controller/`: Endpoints de la API (Productos, Pedidos, Autenticación).
- `src/Service/`: Lógica de negocio reutilizable (Stock, Checkout, Gestión de Pedidos).
- `src/Entity/`: Definición del modelo de datos.
- `src/DataFixtures/`: Datos de prueba para el catálogo.

## 📊 Arquitectura de Datos
Para un detalle profundo de las tablas, relaciones y reglas de negocio, consulta:
👉 **[Documentación del Modelo de Datos](./docs/MODELO_DE_DATOS.md)**

## 🚀 Endpoints Clave
- `GET /api/products`: Catálogo de productos.
- `POST /api/products`: Creación de productos (Solo Admin).
- `GET /api/orders`: Historial de pedidos (Filtrado por rol).
- `POST /api/orders`: Creación de nuevos pedidos.
- `POST /api/orders/{id}/checkout`: Procesamiento de pago simulado.

## 📖 Documentación Interactiva
Puedes explorar y probar todos los endpoints desde la interfaz de Swagger:
[http://localhost:8000/api/doc](http://localhost:8000/api/doc)

### 🧪 Pruebas y Lógica
Para aprender a ejecutar los tests unitarios y validar la lógica de negocio:
👉 **[Guía de Pruebas (Testing)](../PRUEBAS.md)**

## 💻 Desarrollo Local (Sin Docker)
Si deseas correrlo localmente sin Docker:
1. Instalar dependencias: `composer install`
2. Configurar el archivo `.env.local` con tu base de datos.
3. Crear DB y Migraciones:
   ```bash
   php bin/console doctrine:database:create
   php bin/console doctrine:migrations:migrate
   ```
4. Iniciar servidor: `symfony serve`
