# 🛒 Sistema de Gestión E-commerce y Análisis Estadístico

Este proyecto es una aplicación web full-stack desarrollada para gestionar ventas de un E-commerce y analizar indicadores estadísticos clave en tiempo real.

El sistema permite registrar clientes y pedidos, manteniendo un historial detallado, y visualiza métricas como el promedio de ventas diarias, la desviación estándar y la correlación entre precio y cantidad vendida mediante un Dashboard interactivo.

---

## 🚀 Características Principales

### 🖥️ Frontend (Interfaz de Usuario)
* **Gestión de Ventas:** Formulario dinámico para registrar ventas con cálculo automático de totales y protección de precios.
* **Gestión de Clientes:** Registro rápido de nuevos clientes.
* **Historial de Pedidos:** Tabla visual para consultar ventas pasadas y ver el detalle de cada ticket.
* **Dashboard Estadístico:** 4 gráficos interactivos (Chart.js):
    * 📊 Ventas por Producto (Barras).
    * 📈 Evolución de Ventas por Fecha (Líneas).
    * 🟡 Correlación Precio vs. Cantidad (Dispersión/Scatter).
    * 🥧 Distribución por Métodos de Pago (Torta/Pie).

### ⚙️ Backend (API RESTful)
* **Arquitectura MVC:** Router centralizado en PHP puro sin frameworks.
* **Base de Datos Segura:** Uso de PDO, Consultas Preparadas (evita inyección SQL) y Transacciones (Commit/Rollback) para la integridad de los pedidos.
* **Cálculos Estadísticos:** Implementación manual de fórmulas matemáticas (como la Correlación de Pearson) directamente en SQL para compatibilidad con todas las versiones de MySQL.

---

## 🛠️ Tecnologías Utilizadas

* **Lenguaje Backend:** PHP 8+
* **Base de Datos:** MySQL 5.7 / 8.0
* **Frontend:** HTML5, CSS3, JavaScript (ES6+)
* **Framework CSS:** Bootstrap 5.3
* **Librería de Gráficos:** Chart.js 4.4
* **Servidor Web:** Apache (vía XAMPP/WAMP)

---

## 📂 Estructura del Proyecto

```text
/Ecommerce-Estadisticas/
│
├── api/                        
│   ├── controllers/          
│   ├── index.php               
│   └── .htaccess               
│
├── config/
│   └── conexion.php           
│
├── database/
│   └── create-sql.sql           
│
├── views/                      
│   ├── index.html              
│   ├── app.js                 
│   ├── dashboard.js            
│   ├── formulario.js           
│   └── pedidos.js              
│
└── README.md
````
## 🔧 Guía de Instalación y Configuración
Sigue estos pasos para ejecutar el proyecto en tu entorno local (ej. XAMPP).

### Clonar o Descargar
Descarga el proyecto y colócalo en tu carpeta de servidor web.
* **Ruta ejemplo:** C:/xampp/htdocs/Ecommerce-Estadisticas.
  
### Base de Datos
* **Abre phpMyAdmin:** C:/xampp/htdocs/Ecommerce-Estadisticas.
* **Crea una nueva base de datos llamada:** C:/xampp/htdocs/Ecommerce-Estadisticas.
* **Ve a la pestaña Importar.** 
* **Selecciona el archivo:** db/database.sql incluido en este proyecto
* **Ejecuta:** la importación para crear las tablas y datos de prueba.
  
### Configurar Backend
Abre el archivo config/conexion.php y asegúrate de que las credenciales sean correctas para tu entorno local:
```text
$host = 'localhost';
$db_name = 'ecommerce_estadisticas';
$username = 'root';  // Tu usuario de MySQL
$password = '';      // Tu contraseña de MySQL (en XAMPP suele ser vacía)
````
### Configurar Frontend
Abre el archivo views/app.js y verifica que la constante API_URL apunte a tu carpeta correcta:
```text
// Si tu carpeta se llama 'Ecommerce-Estadisticas'
const API_URL = 'http://localhost/Ecommerce-Estadisticas/api';
````

## 📖 Cómo Utilizar el Sistema

* **Iniciar Servidor:** Asegúrate de que Apache y MySQL estén corriendo en XAMPP.
* **Abrir Navegador:** Ve a http://localhost/Ecommerce-Estadisticas/views/index.html.

### Flujo de Trabajo Recomendado
* **Registrar Cliente:**
Ve a la pestaña "Nuevo Cliente" y crea uno si no existe.

* **Registrar Venta:**
Ve a "Nueva Venta".
Selecciona un cliente y un producto.
Nota: El precio se carga automáticamente (es de solo lectura).
Ingresa la cantidad y el método de pago.
Haz clic en "Confirmar Venta".

* **Ver Historial:**
Ve a la pestaña "Historial Pedidos" para ver la venta recién creada.
Haz clic en "Ver Detalle" para desplegar los ítems del pedido.

* **Analizar Datos:**
Ve a la pestaña "Dashboard".
Los gráficos y los indicadores (Promedio, Desvío, Correlación) se actualizarán automáticamente con la nueva venta.

##📡 Documentación de la API
```text
Método,Endpoint,Descripción
GET,/clientes,Obtiene todos los clientes.
POST,/clientes,Crea un nuevo cliente.
GET,/productos,Obtiene el catálogo de productos.
GET,/pedidos,Obtiene el historial de ventas.
POST,/pedidos,Registra una nueva venta (con transacción).
GET,/pedidos/{id},Obtiene el detalle de items de un pedido específico.
GET,/estadisticas/promedio-ventas,Calcula el promedio de ventas diarias.
GET,/estadisticas/desvio-estandar,Calcula la desviación estándar de los montos.
GET,/estadisticas/correlacion-precio,Calcula la correlación de Pearson (Precio/Cantidad).
````
