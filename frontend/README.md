# Frontend - Tienda Online

Interfaz de usuario moderna y rápida para el sistema de gestión de pedidos, desarrollada con **React** y **Vite**.

## 🛠️ Tecnologías
- **React 18** (Functional Components & Hooks)
- **TypeScript**
- **Vite** (Build Tool)
- **Bootstrap 5 (React-Bootstrap)** (Layout & UI)
- **Axios** (API Client)
- **React Router 18** (Routing)
- **Lucide React** (Iconografía)

## 📁 Estructura del Proyecto
- `src/components/`: Componentes globales como la barra de navegación.
- `src/context/`: Gestión de estado global (Autenticación y Carrito de compras).
- `src/views/`: Vistas principales (Login, Catálogo, Detalle de Pedido, Panel Admin).
- `src/api/`: Configuración del cliente Axios e interceptores para headers personalizados.

## 🔑 Gestión de Roles
La aplicación implementa una interfaz adaptativa:
- **Admin**: Accede al Panel de Administración para crear productos y ver pedidos globales.
- **Customer**: Puede comprar productos, gestionar su carrito y ver sus propios pedidos.

## 🚀 Desarrollo Local (Sin Docker)
1. Instalar dependencias:
   ```bash
   npm install
   ```
2. Iniciar servidor de desarrollo:
   ```bash
   npm run dev
   ```
   *Acceso: http://localhost:5173 (o puerto asignado)*

## 📦 Construcción para Producción
```bash
npm run build
```
Esto generará la carpeta `dist/` optimizada para despliegue.
