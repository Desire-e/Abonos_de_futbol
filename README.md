# Web de Compra y Gestión de Abonos de Fútbol 
Proyecto práctico del módulo Desarrollo Web en Entorno Servidor.
Se trata de una aplicación web desarrollada con PHP y Laravel 12 para la gestión y compra de abonos de fútbol. 
Además, incorpora AJAX y DataTables con jQuery para mejorar la interacción en determinadas secciones del panel de administración, como la creación y el listado de tipos de abonos.

## Funcionalidades
- Compra de abono de fútbol: Formularios, csrf token, gestión de cookies, vistas blade.
- Visualización del ticket y descarga forzada de tickets de compra en formato PDF mediante la librería DomPDF. . Los archivos se almacenan en disco local. 
- Login con cuenta de administrador mediante usuarios y contraseña, o mediante su cuenta de Google haciendo uso de OAuth 2.0.
- Panel de administrador: gestión de tipos de abonos (crear, eliminar, visualizar), visualizar abonos comprados.
- Almacenamiento y servicio de imágenes guardadas en la base de datos.
- Componentes y reglas de validación personalizadas.

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

## Demo
Próximamente. Mientras tanto, puede probar la **Instalación para pruebas en entorno local.**

## Instalación para pruebas en entorno local

### Requisitos
- PHP 8.2.12 o superior
- Composer
- MySQL
- Laravel 12
- Apache o servidor compatible para correr en local
- Google Cloud Console

### Instalación
1.	Clonar o descargar repositorio.
```
git clone https://github.com/Desire-e/Abonos_de_futbol.git
```
2.	Instalar dependencias de Composer (paquetes definidos en composer.json y crear vendor)
```
cd Abonos_de_futbol/TO7/compra_abonos_to7
composer install
```
3.	Copiar `.env.example` (cambiar idiomas si es necesario)
```
cp .env.example .env
```
4.	Generar APP_KEY
```
php artisan key:generate
```
5.	Crear la base de datos e importando `uda.sql` (estructura y datos principales).
6.	Configurar las credenciales de base de datos en el archivo `.env` (DB_HOST, DB_PORT, DB_PASSWORD).
7.	Ejecutar migraciones para crear tablas adicionales en la base de datos.
```
php artisan migrate
```
8.	Ejecutar enlace simbólico para almacenamiento interno.
```
php artisan storage:link
```
9. Crear sus propias credenciales de Google OAuth 2.0 e introducirlas en su .env, para el login de cuentas administrativas mediante cuenta de Google 

### Sobre crear credenciales de Google OAuth 2.0
Por motivos de seguridad, las credenciales de Google no se incluyen en el repositorio, por lo que deberá crear las suyas siguiendo estos pasos:
1. Cree un proyecto en Google Cloud.
2. Configure la pantalla de consentimiento OAuth.
3. Cree un ID de cliente OAuth 2.0. Añada la siguiente URL de redirección autorizada:
```
http://localhost/TO7/compra_abonos_to7/public/google/callback
```
Si necesita una GOOGLE_REDIRECT_URL distinta, regístrela en su Authorized redirect URI en Google Cloud Console y modifique el archivo .env:
```
GOOGLE_REDIRECT_URL=...
```
4. Copie el Client ID y el Client Secret en .env.
```
GOOGLE_CLIENT_ID=...
GOOGLE_CLIENT_SECRET=...
```

### Cómo usarla
Despliegue el proyecto en un servidor web compatible con PHP (Apache, Nginx, Laragon, XAMPP, etc.) y acceda a la URL correspondiente según su configuración.

Si utiliza XAMPP, copie la carpeta `TO7` dentro de `xampp/htdocs` y acceda a: 
http://localhost/TO7/compra_abonos_to7/public/
**Si ha movido el proyecto después de ejecutar `php artisan storage:link`, vuelva a crear el enlace simbólico.**

Para acceder al panel de administración mediante usuario y contraseña, utilice las credenciales incluidas en `uda.sql`:
- Usuario: `uda`
- Contraseña: `1234`

También puede iniciar sesión mediante una cuenta de Google una vez configurado OAuth 2.0.
