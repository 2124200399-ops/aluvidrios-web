# 🪟 Aluvidrios - Plataforma Web Comercial & Cotizador

Plataforma web full-stack para empresa de cancelería de aluminio y vidrio templado, que incluye catálogo de productos interactivo, sistema de cotización en línea, agendado de citas de instalación, autenticación mediante **Google OAuth** y panel de administración para seguimiento de pedidos.

## 🚀 Tecnologías

- **Frontend:** HTML5, CSS3 moderno, JavaScript (ES6+), **Vite**
- **Backend:** **PHP** (API REST modularizada en /api), Composer
- **Autenticación:** Google OAuth 2.0 (login_google.php) & Autenticación tradicional
- **Notificaciones:** Envío de correos automáticos (enviar_correo.php)
- **Base de Datos:** **MySQL** (Diagrama Entidad-Relación y script DDL incluidos)

## 📌 Módulos Principales

- **Catálogo Dinámico:** Muestra de canceles para baño, barandales, fachadas y fabricación a medida con filtrado de servicios.
- **Sistema de Citas:** Agendado automatizado de visitas técnicas y mediciones a domicilio (gendar.html y citas.php).
- **Portal de Clientes (PERFIL CLIENTE):** Historial de cotizaciones, solicitudes y estado de instalación.
- **Panel Administrativo (PERFIL ADMIN):** Control integral de productos en catálogo, confirmación de pedidos y agenda de citas.
- **Base de Datos Relacional:** Incluye script de creación y población (luvidrios.sql) junto al diagrama conceptual y lógico (Diagrama ER.png).

## ⚙️ Estructura del Repositorio

`	ext
├── aluvidrios.sql           # Script de base de datos relacional
├── Diagrama ER.png          # Modelo Entidad-Relación
├── FASE DE BASE DE DATOS.pdf# Documentación de diseño y modelado
└── Inicio/                  # Código fuente de la aplicación
    ├── api/                 # Endpoints PHP (citas, pedidos, google auth, mailer)
    ├── PERFIL ADMIN/        # Vistas y lógica del administrador
    ├── PERFIL CLIENTE/      # Portal para clientes registrados
    ├── src/                 # Lógica cliente y componentes
    ├── vite.config.js       # Configuración del bundler Vite
    └── package.json
`

## 🛠️ Puesta en Marcha

1. **Clonar repositorio:**
   `ash
   git clone https://github.com/2124200399-ops/aluvidrios-web.git
   cd aluvidrios-web/Inicio
   `

2. **Instalar dependencias:**
   `ash
   npm install
   composer install
   `

3. **Importar la base de datos:**
   Importar el archivo luvidrios.sql en tu servidor MySQL (ej. phpMyAdmin o MySQL CLI).

4. **Compilar y servir:**
   `ash
   npm run dev
   `

---
Proyecto desarrollado como solución integral de digitalización y comercio electrónico.