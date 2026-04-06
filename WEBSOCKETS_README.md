# Guía de Implementación y Paso a Producción: WebSockets con Laravel Reverb

Esta guía detalla todo lo que se ha configurado para integrar WebSockets en tu entorno local (desarrollo) para la sincronización colaborativa del Diagrama de Actividades en tiempo real. Adicionalmente, incluye la guía detallada de comandos y flujos necesarios para poner este sistema en **producción**.

## 1. ¿Qué se implementó (El Flujo Actual)?
Actualmente el flujo de eventos se comporta de la siguiente manera:
1. **Frontend (GoJS + Vue):** Cada que realizar un arrastre, al crear un carril o una nueva actividad, el listener `ModelChangedListener` de GoJS envía mediante **Axios** un POST con el nuevo JSON a `/politica_negocio/{id}/broadcast`.
2. **Backend (Laravel):** El controlador toma el JSON y **despacha** el evento `DiagramUpdated`. En esto utiliza el driver de Broadcaster (Reverb).
3. **Websocket (Reverb):** Reverb recibe la señal interna de Laravel y emite a todos los clientes que están sentados en el `PresenceChannel` con nombre `diagrama.{id}`.
4. **Clientes (Echo):** El resto de usuarios con la misma sesión (en edit.vue) reciben el evento gracias a `Laravel Echo`. Detiene temporalmente la emisión circular y reemplaza instantáneamente el diagrama con el nuevo JSON (`go.Model.fromJson(e.json)`).

---

## 2. Puesta en Producción

En un servidor de producción (por ejemplo Laravel Forge, DigitalOcean, AWS EC2, VPS clásico con Ubuntu Nginx), Reverb no puede ejecutarse solo corriendo `php artisan reverb:start` y dejándolo allí, ya que si falla nadie lo levantará y no dispone de SSL propio nativamente que deba combinarse con tu dominio.

A continuación, la configuración:

### Paso A: Preparar Variables de Entorno (.env)
Asegúrate de que en el archivo `.env` de tu servidor en **producción**, cuentes con credenciales idénticas a Reverb. Se auto generaron al instalar `php artisan install:broadcasting`.

```ini
BROADCAST_CONNECTION=reverb

REVERB_APP_ID=680049
REVERB_APP_KEY=7fmxedrt0dwgy21ocuzx
REVERB_APP_SECRET=OcultoEnProduccion

# Dominio y HTTPs de producción
REVERB_HOST="tu-dominio.com"
REVERB_PORT=443
REVERB_SCHEME=https

VITE_REVERB_APP_KEY="${REVERB_APP_KEY}"
VITE_REVERB_HOST="${REVERB_HOST}"
VITE_REVERB_PORT="${REVERB_PORT}"
VITE_REVERB_SCHEME="${REVERB_SCHEME}"
```
*Note: Configurar el puerto a 443 en Vite es vital para que las solicitudes en navegadores con HTTPS no sean bloqueadas (Mixed Content Error).*

### Paso B: Configurar Reverb usando Supervisor (Mantenerlo vivo 24/7)
Tu servidor Ubuntu necesita un sistema que mantenga levantado el socket de Reverb. Laravel recomienda **Supervisor**.

1. Instalar Supervisor en tu VPS de producción:
```bash
sudo apt-get install supervisor
```

2. Crear un archivo de worker para Reverb:
```bash
sudo nano /etc/supervisor/conf.d/reverb.conf
```
Dentro de este contenedor, colocar lo siguiente (modifica rutas dependientes de tu proyecto):
```ini
[program:reverb]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/tu_proyecto/artisan reverb:start
autostart=true
autorestart=true
user=www-data
redirect_stderr=true
stdout_logfile=/var/www/tu_proyecto/storage/logs/reverb.log
stopwaitsecs=3600
```

3. Recargar la configuración e iniciar Reverb:
```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start reverb:*
```

### Paso C: Proxy Inverso con NGINX (Activando HTTPS)
Como en producción la gente usará tu sitio por `https://`, los WebSockets también deben circular por la capa segura (Protocolo `wss://`). Usaremos Nginx como intermediario. 

Deberás añadir la siguiente configuración en tu bloque `server` de Nginx:

```nginx
server {
    listen 443 ssl;
    server_name tu-dominio.com;

    # ... Tus otros settings de PHP y Laravel ...

    # Proxy para el websocket (Reverb funciona nativamente en 8080 localmente)
    location /app {
        proxy_pass http://127.0.0.1:8080;
        proxy_http_version 1.1;
        proxy_set_header Upgrade $http_upgrade;
        proxy_set_header Connection "Upgrade";
        proxy_set_header Host $host;
    }
}
```

Al utilizar `/app`, cualquier subpetición WSS que trate de enviar Echo de Vue al servidor se enrutará a `ws://127.0.0.1:8080`, por lo que NGINX lo convertirá en protocolo seguro y será imperceptible para los usuarios. Reinicia Nginx tras alterar el bloque de servidor:
```bash
sudo systemctl restart nginx
```

### Paso D: Purgar Cachés y Compilar JS
No olvides compilar los assets de Vite para producción. Esto minificará y asegurará que la llave de tu `.env` de producción se transpiló al Front-End.

```bash
composer install --optimize-autoloader --no-dev
php artisan config:cache
php artisan event:cache
php artisan route:cache

# Compilar Assets (Vite)
npm ci
npm run build
```

---

Con esto, el sistema se mantendrá operando y conectando todos los diagramas modificados por los distintos trabajadores o administradores que accedan de manera conjunta al plano en tiempo real. 
