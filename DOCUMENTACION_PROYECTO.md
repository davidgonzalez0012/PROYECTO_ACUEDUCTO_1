# Proyecto: Sistema de Gestión de Tickets - Acueducto y Alcantarillado de Popayán

## Descripción

Este proyecto es un sistema web para la gestión de tickets de soporte técnico, desarrollado en PHP usando el framework CodeIgniter. 
 Permite a los usuarios crear, visualizar y gestionar tickets, y a los administradores y personal de soporte dar seguimiento y resolver incidencias.

---

## Estructura del Proyecto

- **app/Controllers/**: Controladores principales (`Home.php`, `Ticket.php`, `Categoria.php`, etc.)
- **app/Models/**: Modelos de acceso a datos (`TicketModel.php`, etc.)
- **app/Views/**: Vistas para cada rol y funcionalidad (administrador, soporte, formularios, dashboard, etc.)
- **public/**: Archivos públicos (CSS, JS, imágenes)
- **writable/**: Archivos generados por el sistema (logs, caché, etc.)

---

## Instalación

1. **Clona el repositorio**
   ```bash
   git clone <URL-del-repositorio>
   cd PROYECTO_ACUEDUCTO_1
   ```

2. **Configura la base de datos**
   - Edita `app/Config/Database.php` con tus credenciales de Oracle.

3. **Instala dependencias**
   ```bash
   composer install
   ```

4. **Configura el entorno**
   - Copia `.env.example` a `.env` y ajusta las variables necesarias.

5. **Inicia el servidor**
   ```bash
   php spark serve
   ```
   Accede en [http://localhost:8080](http://localhost:8080)

---

## Funcionalidades principales

- **Login de usuarios** (administrador, soporte, solicitante)
- **Dashboard** con estadísticas y tickets recientes
- **Gestión de tickets**: crear, listar, filtrar por categoría, prioridad y estado
- **Panel de administración**: ver tickets por categoría, prioridad, estado, usuario
- **Panel de soporte**: ver y resolver tickets asignados
- **Reportes**: tickets resueltos, tickets de alta prioridad, etc.

---

## Estructura de la Base de Datos

- **TICKETS**
  - ID
  - TITULO
  - DESCRIPCION
  - CATEGORIA_ID
  - PRIORIDAD
  - ESTADO
  - USUARIO_ID
  - FECHA_CREACION
  - FECHA_ACTUALIZACION

- **USUARIOS**
  - ID
  - NOMBRE
  - ROL
  - 

- **DEPENDENCIAS**
  - ID
  - NOMBRE
  - 

---

## Controladores principales

- **Home.php**: Dashboard, login, estadísticas generales.
- **Ticket.php**: CRUD de tickets, filtros por prioridad y estado.
- **Categoria.php**: Listado de tickets por categoría.

---

## Vistas principales

- **administrador/inicio_administrador.php**: Dashboard del administrador.
- **administrador/tickets.php**: Listado de tickets filtrados.
- **soporte/lista_tickets_por_categoria_soporte.php**: Tickets para soporte.
- **administrador/crear_ticket_admin.php**: Formulario de creación de tickets.

---

## Personalización

- **Estilos**: `public/css/style_pagina_web.css`
- **Imágenes**: `public/images/`
- **Sidebar y logo**: Personalizable en las vistas del layout.

---

## Notas técnicas

- El sistema utiliza Oracle como base de datos.
- El sistema es sensible a mayúsculas/minúsculas en los valores de los campos (por ejemplo, `PRIORIDAD = 'ALTA'`).
- Para agregar nuevas categorías, actualiza la tabla `CATEGORIAS` y los mapas en los controladores.

---

## Contacto

Para soporte o dudas, contacta a:  
**Equipo de Desarrollo Acueducto Popayán**  
Correo: soporte@acueductopopayan.com

---


