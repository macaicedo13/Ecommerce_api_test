# Sistema de Gestión de Pedidos y Pagos (Fullstack)

Este proyecto es una solución integral para la gestión de un catálogo de productos, carrito de compras y procesamiento de pedidos, con una arquitectura moderna separada en **Backend (Symfony)** y **Frontend (React)**.

## 🚀 Tecnologías Utilizadas

### Backend
- **Symfony 7**: Framework principal de PHP.
- **PHP 8.2**: Última versión estable.
- **MySQL 8.0**: Base de datos relacional.
- **NelmioApiDocBundle**: Documentación automatizada de la API (Swagger/OpenAPI).
- **Doctrine ORM**: Gestión de entidades y base de datos.

### Frontend
- **React 18**: Biblioteca de UI.
- **Vite**: Herramienta de construcción ultra pápida.
- **TypeScript**: Tipado estático para mejor mantenimiento.
- **Bootstrap 5 (React-Bootstrap)**: Sistema de diseño responsive y profesional.
- **Lucide React**: Iconografía moderna.
- **Axios**: Cliente HTTP para comunicación con el API.

---

## 🏗️ Arquitectura y Pruebas
Para un detalle profundo del diseño y validación del proyecto:
- 👉 **[Arquitectura de Sistema](./ARCHITECTURE.md)**
- 👉 **[Guía de Pruebas (Testing)](./PRUEBAS.md)**

---

## 📖 Guía de Uso y Roles

El sistema utiliza una **autenticación simulada** basada en roles. No necesitas crear una cuenta real, solo elegir cómo quieres entrar:

### 👤 Como Cliente (Customer)
1. Ve a la pantalla de Login.
2. Ingresa un ID (ej: `customer123` o `cliente1`).
3. Selecciona el rol **Customer**.
4. **Acciones**: Podrás ver productos, añadirlos al carrito y realizar pedidos. En "Mis Pedidos" verás solo tu historial.

### 🔐 Como Administrador (Admin)
1. Ve a la pantalla de Login.
2. Ingresa cualquier nombre.
3. Selecciona el rol **Admin**.
4. **Acciones**: Verás el enlace **Panel Admin**. Desde allí puedes ver **todos los pedidos** de todos los clientes y **crear nuevos productos** para el catálogo. No podrás realizar compras.

### 👤 Área del Cliente
- **Catálogo Dinámico**: Búsqueda en tiempo real de productos.
- **Carrito de Compras**: Gestión de ítems con persistencia local.
- **Flujo de Pedido**: Creación de pedidos y proceso de pago simulado.
- **Mis Pedidos**: Historial de compras personal con estados (Pendiente, Completado).

### 🔐 Área de Administrador
- **Panel Global**: Vista de todos los pedidos realizados en la plataforma.
- **Gestión de Inventario**: Formulario para crear nuevos productos (Nombre, Precio, Stock).
- **Control de Roles**: Interfaz adaptativa que oculta opciones de compra para el administrador.

---

## 🛠️ Instalación y Configuración

El proyecto está completamente dockerizado para facilitar su despliegue en cualquier entorno.

### Requisitos Previos
- Docker y Docker Compose instalados.

### Pasos para Instalar

1. **Clonar el repositorio**:
   ```bash
   git clone https://github.com/macaicedo13/Ecommerce_api_test.git
   cd Ecommerce_api_test
   ```

2. **Levantar los contenedores**:
   Desde la raíz del proyecto, ejecuta:
   ```bash
   docker-compose up -d --build
   ```

3. **Inicializar la base de datos (Primera vez)**:
   Ejecuta las migraciones de Symfony para crear las tablas y cargar datos de prueba:
   ```bash
   docker exec -it phptest_backend composer install
   docker exec -it phptest_backend php bin/console doctrine:migrations:migrate --no-interaction
   docker exec -it phptest_backend php bin/console doctrine:fixtures:load --no-interaction
   ```

---

## 🔗 Enlaces de Acceso

Una vez que los contenedores estén corriendo, podrás acceder a:

- **Frontend**: [http://localhost:3000](http://localhost:3000)
- **Backend (API)**: [http://localhost:8000](http://localhost:8000)
- **Documentación Swagger**: [http://localhost:8000/api/doc](http://localhost:8000/api/doc)
- **Postman Collection**: Localizada en `backend/docs/postman_collection.json`

---

## 📂 Estructura del Proyecto

```text
.
├── backend/            # Aplicación Symfony (API REST)
├── frontend/           # Aplicación React + Vite
├── docker-compose.yml  # Orquestación de contenedores
└── .gitignore          # Reglas de exclusión para Git
```

---

## 📝 Notas de Desarrollo
- La autenticación es **simulada** para facilitar las pruebas de rol sin necesidad de registro complejo.
- Se siguen principios de **Clean Code** y separación de responsabilidades.
- El diseño es completamente **full-width** y adaptativo para diferentes tamaños de pantalla.
