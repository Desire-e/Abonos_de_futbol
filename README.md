# Web de Compra y Gestión de Abonos de Fútbol
 
Aplicación web desarrollada con **PHP** y **Laravel 12** para la gestión y compra de abonos de fútbol.
Proyecto práctico del módulo *Desarrollo Web en Entorno Servidor*, que demuestra el manejo de un backend completo: autenticación, generación de PDFs, validaciones personalizadas e interacción dinámica con AJAX y jQuery.

---
## Funcionalidades
 
- **Compra de abono de fútbol**: formularios, token CSRF, gestión de cookies, vistas Blade.
- **Ticket de compra**: visualización y descarga forzada en PDF mediante la librería DomPDF. Los archivos se almacenan en disco local.
- **Autenticación**: login de administrador mediante usuario y contraseña, o mediante cuenta de Google con OAuth 2.0.
- **Panel de administrador**: gestión de tipos de abonos (crear, eliminar, visualizar) y visualización de abonos comprados.
- **Gestión de imágenes**: almacenamiento y servicio de imágenes guardadas en la base de datos.
- **Validación**: componentes y reglas de validación personalizadas.
- **AJAX + DataTables**: interacción dinámica en secciones del panel de administración, como la creación y el listado de tipos de abonos.

---
## Demo
 
Próximamente. Mientras tanto, puedes probar la instalación en entorno local siguiendo los pasos a continuación.

---
## Tecnologías utilizadas

- PHP 8.2
- Laravel 12
- MySQL
- Blade
- JavaScript
- jQuery
- AJAX
- DataTables
- DomPDF
- Google OAuth 2.0

---
## Instalación en entorno local
 
### Requisitos
- PHP 8.2.12 o superior
- Composer
- MySQL
- Laravel 12
- Apache o servidor compatible para correr en local
- Cuenta de Google Cloud Console (solo si se quiere probar el login con Google)

### Instalación 
1. Clonar o descargar el repositorio.
```bash
   git clone https://github.com/Desire-e/Abonos_de_futbol.git
```
 
2. Instalar las dependencias de Composer (paquetes definidos en `composer.json`, crea la carpeta `vendor`).
```bash
   cd Abonos_de_futbol/TO7/compra_abonos_to7
   composer install
```
 
3. Copiar `.env.example` a `.env` (ajusta el idioma si es necesario).
```bash
   cp .env.example .env
```
 
4. Generar la `APP_KEY`.
```bash
   php artisan key:generate
```
 
5. Crear la base de datos e importar `uda.sql` (contiene la estructura y los datos principales).
6. Configurar las credenciales de la base de datos en el archivo `.env` (`DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`).
7. Ejecutar las migraciones para crear las tablas adicionales.
```bash
   php artisan migrate
```
 
8. Crear el enlace simbólico para el almacenamiento interno.
```bash
   php artisan storage:link
```
 
9. Configurar las credenciales de Google OAuth 2.0 (opcional, solo para probar el login con Google) — ver la sección siguiente.

### Configurar credenciales de Google OAuth 2.0 
Por motivos de seguridad, las credenciales de Google no se incluyen en el repositorio. Para generar las tuyas:
 
1. Crea un proyecto en [Google Cloud Console](https://console.cloud.google.com/).
2. Configura la pantalla de consentimiento OAuth.
3. Crea un ID de cliente OAuth 2.0 y añade la siguiente URL de redirección autorizada:
```
   http://localhost/TO7/compra_abonos_to7/public/google/callback
```
   Si necesitas una `GOOGLE_REDIRECT_URL` distinta, regístrala también en Google Cloud Console y actualiza tu `.env`:
```env
   GOOGLE_REDIRECT_URL=...
```
4. Copia el Client ID y el Client Secret en tu `.env`:
```env
   GOOGLE_CLIENT_ID=...
   GOOGLE_CLIENT_SECRET=...
```

---
## Cómo usarla

Despliega el proyecto en un servidor compatible con PHP (Apache, Nginx, Laragon, XAMPP, etc.) y accede a la URL correspondiente según tu configuración.
 
Si usas XAMPP, copia la carpeta `TO7` dentro de `xampp/htdocs` y accede a:
```
http://localhost/TO7/compra_abonos_to7/public/
```
 
> Si mueves el proyecto después de ejecutar `php artisan storage:link`, vuelve a crear el enlace simbólico.
 
Para acceder al panel de administración mediante usuario y contraseña, usa las credenciales de prueba incluidas en `uda.sql`:
 
- **Usuario:** `uda`
- **Contraseña:** `1234`
 
También puedes iniciar sesión con una cuenta de Google una vez configurado OAuth 2.0.

---
## Autor

**Desire-e** — [GitHub](https://github.com/Desire-e)
