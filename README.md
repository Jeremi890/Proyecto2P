# ⚡ NexusStock MVC - Sistema Integral de Gestión de Inventario y Logística
**Proyecto de Segundo Parcial: Desarrollo de Aplicación Web con PHP y MySQL**

![PHP Version](https://img.shields.io/badge/PHP-8.0%2B-777BB4?style=for-the-badge&logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-PDO_Singleton-4479A1?style=for-the-badge&logo=mysql&logoColor=white)
![Architecture](https://img.shields.io/badge/Arquitectura-MVC_Nativo-6366F1?style=for-the-badge)
![UI Premium](https://img.shields.io/badge/Dise%C3%B1o-Glassmorphism_Dark-10B981?style=for-the-badge)

---

## 📋 1. Descripción General y Proceso Real Resuelto

**NexusStock MVC** es una plataforma web dinámica diseñada para solucionar un **proceso real administrativo y comercial**: el control de existencias, clasificación de mercadería, directorio comercial y registro auditable de transacciones en almacén (compras a proveedores y retiros/ventas).

Para cumplir y superar el requisito exigido de *"dos entidades relacionadas por integrante de grupo"*, la aplicación implementa **4 entidades relacionales completas**, cubriendo tanto proyectos individuales como en equipo:
1. **Categorías:** Clasificación del catálogo (`Relación 1:N` con Productos).
2. **Productos:** Entidad central del inventario con control financiero, alertas automáticas de stock mínimo y códigos SKU.
3. **Proveedores:** Directorio comercial y de abastecimiento (`Relación 1:N` con Movimientos).
4. **Movimientos (Transacciones):** Historial operacional de **Entradas** y **Salidas** que actualiza el stock en tiempo real mediante **Transacciones SQL ACID** (`Relación N:1` con Productos y Proveedores).

---

## 🏛️ 2. Arquitectura MVC y Estructura del Proyecto

El sistema está construido siguiendo estrictamente el patrón **Modelo-Vista-Controlador (MVC)** nativo en PHP sin el peso de frameworks externos:

```text
/public/                     ➔ Enrutador frontal (Front Controller index.php) y activos estáticos (CSS/JS)
  ├── index.php              ➔ Intercepta las peticiones, analiza la URL y ejecuta el Controlador adecuado
  ├── css/styles.css         ➔ Diseño State of the Art (Modo Oscuro, Glassmorphism y animaciones)
  └── js/                    ➔ Validaciones en tiempo real (validations.js) y modales de confirmación (app.js)
/app/
  ├── config/config.php      ➔ Parámetros globales, conexión MySQL y sistema de mensajes Flash (Toasts)
  ├── models/                ➔ Capa de Datos: Conexión PDO Singleton (Database.php) y consultas SQL seguras
  ├── controllers/           ➔ Capa de Negocio: Recepción de datos, validaciones backend y coordinación M-V
  └── views/                 ➔ Capa de Presentación: Plantillas HTML5 organizadas por layout y módulos
/database/
  └── database.sql           ➔ Script de base de datos relacional con claves foráneas y datos semilla (Seed Data)
```

---

## ⚙️ 3. Instrucciones de Instalación y Ejecución Local

Puedes ejecutar este proyecto de forma muy sencilla utilizando XAMPP, Laragon o el servidor web integrado nativo de PHP:

### Opción A: Servidor Integrado de PHP (Sin configurar Apache) - ¡Recomendado!
1. Asegúrate de tener **MySQL activo** (desde XAMPP, Laragon o tu terminal).
2. Entra a tu cliente de MySQL (ej. phpMyAdmin en `http://localhost/phpmyadmin`) e importa el archivo `database/database.sql`. Esto creará la base de datos `nexusstock_db` y cargará datos de prueba listos para usar.
3. Abre tu terminal en la carpeta raíz del proyecto (`C:\Users\Abel_\Desktop\Proyecto2P`) y ejecuta:
   ```bash
   php -S localhost:8000 -t public
   ```
4. Abre tu navegador web e ingresa a: **`http://localhost:8000`**

### Opción B: Usando XAMPP o Laragon tradicional
1. Copia la carpeta `Proyecto2P` dentro de tu directorio `htdocs` (en XAMPP) o `www` (en Laragon).
2. Importa el archivo `database/database.sql` en phpMyAdmin.
3. Si tus credenciales de MySQL son diferentes a root sin contraseña, edita el archivo `app/config/config.php`:
   ```php
   define('DB_USER', 'tu_usuario');
   define('DB_PASS', 'tu_contraseña');
   ```
4. Ingresa en tu navegador a: **`http://localhost/Proyecto2P/public/`**

---

## 🌐 4. Guía de Despliegue en la Nube (Render / InfinityFree / Railway)

Para cumplir con el requisito de despliegue accesible desde internet, te recomendamos las siguientes plataformas gratuitas:

### Método 1: InfinityFree / 000webhost (Hosting PHP tradicional gratis)
1. Crea una cuenta gratuita en **InfinityFree** y crea una nueva cuenta de hosting.
2. En el **Panel de Control (cPanel)** ve a *MySQL Databases*, crea una base de datos e importa el archivo `database.sql`.
3. Sube todos los archivos del proyecto usando el **Administrador de Archivos (File Manager)** o FileZilla.
4. Actualiza `app/config/config.php` con el Host MySQL, Usuario, Contraseña y Nombre de BD que te asignó InfinityFree.
5. ¡Listo! Tu sitio estará en vivo en tu subdominio gratuito.

### Método 2: Render.com o Railway (Usando Docker)
1. Sube este repositorio a tu cuenta de **GitHub**.
2. Crea un proyecto en **Render.com** seleccionando *Web Service* desde GitHub.
3. Puedes usar un entorno Docker o un servicio de base de datos MySQL gestionada (como Clever Cloud gratis o Railway) conectando la variable `DB_HOST` hacia tu base de datos remota.

---

## 🎓 5. GUÍA DE SUSTENTACIÓN ORAL (4.0 PUNTOS DE LA RÚBRICA)

Esta sección es un **Guion Preparado** para cuando tengas que presentar el proyecto ante el profesor y responder sus preguntas técnicas con total seguridad:

### ❓ Pregunta 1: *"¿Qué problema o proceso resuelve la aplicación?"*
> **Tu respuesta:** "El proyecto resuelve un problema crítico en negocios comerciales y almacenes: el descontrol del stock físico y la falta de alertas tempranas ante quiebres de inventario. A través de un sistema integral de **4 entidades interrelacionadas** (`Categorías`, `Productos`, `Proveedores` y `Movimientos`), permitimos clasificar artículos, valorizar el almacén en tiempo real, auditar quién suministró la mercadería y controlar automáticamente las existencias cada vez que ocurre una entrada o salida."

### ❓ Pregunta 2: *"¿Explica cómo aplicaste el Patrón MVC en el código?"*
> **Tu respuesta:** "Desarrollamos una arquitectura modular estricta:
> - **El Enrutador (`public/index.php`):** Actúa como *Front Controller*. Captura cualquier URL del usuario (ej. `producto/create`), instancia automáticamente la clase `ProductoController` y llama al método `create()`.
> - **El Controlador (`app/controllers/`):** Es el cerebro. Recibe las peticiones, valida los datos del formulario (ej. que los precios no sean negativos o que el RUC sea válido) y le pide información al Modelo.
> - **El Modelo (`app/models/`):** Habla directamente con MySQL utilizando nuestra clase `Database` (Singleton con PDO). Ejecuta sentencias preparadas para devolver objetos a los controladores.
> - **La Vista (`app/views/`):** Renderiza la interfaz HTML5/CSS y muestra los datos recibidos del controlador."

### ❓ Pregunta 3: *"¿Cómo se conecta a la base de datos y cómo garantizan la seguridad?"*
> **Tu respuesta:** "Para la conexión creamos la clase `Database` ubicada en `app/models/Database.php` bajo el **Patrón Singleton**. Esto asegura que toda la aplicación comparta una única instancia de conexión PDO, ahorrando memoria del servidor. Además, todas las consultas CRUD utilizan **Sentencias Preparadas (Prepared Statements)** de PDO, lo cual hace al sistema 100% inmune a ataques de **Inyección SQL** al separar las instrucciones SQL de los parámetros ingresados por el usuario. En la interfaz, sanitizamos toda salida con `htmlspecialchars` para evitar inyecciones de script (**Cross-Site Scripting XSS**)."

### ❓ Pregunta 4: *"¿Cómo manejan las validaciones del Frontend y Backend?"*
> **Tu respuesta:** "Implementamos una **seguridad de doble capa**:
> 1. **Capa 1 (Frontend - `validations.js`):** Valida en el navegador del cliente en tiempo real usando expresiones regulares (Regex). Verifica que los correos tengan '@', que el RUC tenga exactamente 11 dígitos y que los números sean positivos, mostrando bordes rojos y mensajes de error sin necesidad de recargar la página.
> 2. **Capa 2 (Backend - Controladores PHP):** Aunque el usuario intente burlar el JavaScript, el backend en PHP vuelve a verificar la integridad de los campos y hace consultas SQL para evitar duplicados en códigos SKU y números de RUC antes de ejecutar un `INSERT` o `UPDATE`."

### ❓ Pregunta 5: *"¿Cómo funciona el control automático de stock en las transacciones?"*
> **Tu respuesta (¡El punto más impresionante para ganar el 10/10!):** "Cuando se registra una operación en el módulo de **Movimientos**, no modificamos el stock manualmente. El método `registrar()` en `Movimiento.php` abre una **Transacción SQL (Begin Transaction / Commit / Rollback)**:
> 1. Primero inserta el registro del movimiento con su costo total.
> 2. Inmediatamente ejecuta un `UPDATE` en la tabla `productos` sumando (si es Entrada) o restando (si es Salida) la cantidad solicitada.
> 3. Si por alguna razón falla la actualización del stock, se ejecuta un *Rollback* y se deshace el movimiento, garantizando la **Integridad ACID** del inventario."

---

## 🚀 Guion Sugerido para la Demostración Práctica en Vivo (3 minutos)
Para lucirte en tu presentación frente al docente, sigue exactamente este orden:

1. **Mostrar el Dashboard:** Señala las 4 tarjetas de resumen financiero y destaca que las métricas se calculan dinámicamente con agregaciones SQL (`SUM`, `COUNT`).
2. **Crear una Categoría / Proveedor (CRUDrápido):** Ve a Categorías, intenta crear una sin nombre para mostrar la alerta visual del Frontend (borde rojo). Luego ingresa un nombre válido y guárdala para mostrar la notificación flotante (Toast verde).
3. **Módulo de Productos:** Muestra cómo el catálogo une la información con un `INNER JOIN` para mostrar el nombre de la categoría y la valorización de venta.
4. **Prueba de Fuego (Control de Stock y Alertas):**
   - Elige el producto *"Monitor Curvo Samsung Odyssey"* (que tiene un stock inicial en la demo de **1 unidad** y stock mínimo de **2 unidades**).
   - Ve al módulo de **Movimientos** y registra una **SALIDA** de **1 unidad**.
   - Regresa al **Dashboard**. Muestra cómo el stock bajó a **0**, se encendió la **Alerta Roja Crítica** de stock en la tabla y se actualizó el valor financiero del almacén. ¡El profesor quedará muy impresionado!

---
*Desarrollado para alcanzar la máxima excelencia académica - 2026.*
