# Plugin DruID para WordPress

Este plugin integra DruID Identity en WordPress para autenticación, registro, edición de cuenta y logout mediante OAuth2.

## Instalación

1. Empaquete o descargue el plugin como ZIP.
2. En WordPress vaya a `Plugins -> Añadir nuevo -> Subir plugin`.
3. Instale y active el plugin.
4. Tras activarlo, acceda al menú `DruID` del admin para completar la configuración.

### Comportamiento de instalación

- En la activación el plugin prepara o actualiza su persistencia interna.
- En la desactivación no se borra la configuración ni los logs.
- En la desinstalación se elimina la información interna generada por el plugin.

## Configuración del plugin

La configuración se gestiona desde `DruID -> Config`.

![Configuración del Plugin](assets/img/fields_section.png)

### Campos disponibles

1. `Domain`
   Dominio de la plataforma DruID. Se utiliza en la configuración del SDK.

2. `Client ID`
   Identificador de cliente de la app en DruID.

3. `Client Secret`
   Secreto asociado al `Client ID`.

4. `Environment`
   Entorno de DruID. Los valores soportados por la interfaz actual son:
   - `dev`
   - `test`
   - `prod`

5. `Entry Point`
   Punto de entrada por defecto. Puede sobreescribirse en los shortcodes.

6. `Log Path`
   Ruta relativa al `Document Root` donde el SDK escribe los logs de fichero.

7. `Cache Path`
   Ruta relativa al `Document Root` donde el SDK almacena la caché.

8. `Log Level`
   Nivel de log del SDK y del registro de errores del plugin. Los valores soportados por la interfaz actual son:
   - `DEBUG`
   - `ERROR`
   - `OFF`

### Rutas relevantes

- Callback OAuth del plugin: `home_url('/actions/callback')`

La URL de callback a registrar en DruID debe apuntar a la URL completa generada por `home_url('/actions/callback')`.

## Pantallas de administración

### `DruID -> Config`

Pantalla principal de configuración del plugin.

### `DruID -> Druid Logs`

Pestaña dentro de la pantalla principal que muestra la traza técnica disponible del SDK cuando el logging está configurado.

![Logs del Plugin](assets/img/logs_section.png)

### `DruID -> Error Log`

Submenú independiente que muestra los errores funcionales registrados por el plugin.

Esta pantalla muestra:

- fecha (`logged_at`)
- sección
- código de error
- mensaje

## Uso del plugin

El plugin expone shortcodes para mostrar enlaces o bloques de autenticación.

## Integraciones

El plugin expone algunas APIs y hooks públicos que pueden reutilizarse desde temas o plugins complementarios para integraciones de frontend y flujos de autenticación.

### URLs de autenticación

Se pueden obtener URLs públicas sin renderizar shortcodes:

- `\WP_Druid\Services\Shortcodes::get_login_url($attributes = array())`
- `\WP_Druid\Services\Shortcodes::get_register_url($attributes = array())`
- `\WP_Druid\Services\Shortcodes::get_edit_account_url($attributes = array())`
- `\WP_Druid\Services\Shortcodes::get_logout_url($attributes = array())`

Los atributos soportados siguen la misma semántica general que los shortcodes, por ejemplo `entrypoint`, `social`, `url-to-redirect` o `state`, según aplique.

Ejemplo:

```php
$login_url = \WP_Druid\Services\Shortcodes::get_login_url(array(
    'entrypoint' => 'XXXXXXXXXXXXX-default',
    'url-to-redirect' => home_url('/mi-cuenta/'),
));
```

### Estado de vinculación del usuario

Para comprobar si el usuario actual está vinculado con DruID y obtener su identificador público:

- `\WP_Druid\Services\Users::has_druid_link($wp_user_id = null)`
- `\WP_Druid\Services\Users::get_druid_id_by_wp_user_id($wp_user_id)`
- `\WP_Druid\Services\Users::get_current_user_druid_id()`

Ejemplo:

```php
if (is_user_logged_in() && \WP_Druid\Services\Users::has_druid_link()) {
    $druid_id = \WP_Druid\Services\Users::get_current_user_druid_id();
}
```

### Usuario logado en DruID

Si necesita consultar el usuario actual directamente desde la sesion DruID activa, el plugin expone una API publica basada en el SDK integrado:

- `\WP_Druid\Services\Users::get_current_user_druid_user_data()`
- `\WP_Druid\Services\Users::get_current_user_druid_profile()`
- `\WP_Druid\Services\Users::get_druid_profile_from_user_data($druid_user_data)`

Notas:

- `get_current_user_druid_user_data()` devuelve el objeto bruto obtenido desde `Genetsis\UserApi::getUserLogged()`.
- `get_current_user_druid_profile()` devuelve un perfil normalizado para integraciones ligeras con:
  - `druid_id`
  - `email`
  - `email_confirmed`
  - `first_name`
  - `last_name`
  - `display_name`
- Estas APIs leen el usuario actual desde DruID, no desde la replica persistida en WordPress.

Ejemplos:

```php
$druid_user = \WP_Druid\Services\Users::get_current_user_druid_user_data();

if ($druid_user instanceof \stdClass) {
    $email = $druid_user->user->user_ids->email->value ?? null;
}
```

```php
$profile = \WP_Druid\Services\Users::get_current_user_druid_profile();

if (is_array($profile) && !empty($profile['email'])) {
    $email = $profile['email'];
}
```

Tambien puede normalizar el payload recibido en los hooks publicos del plugin:

```php
add_action('druid_post_login', function ($context) {
    if (!is_array($context) || empty($context['druid_user']) || !($context['druid_user'] instanceof \stdClass)) {
        return;
    }

    $profile = \WP_Druid\Services\Users::get_druid_profile_from_user_data($context['druid_user']);
}, 10, 1);
```

### Hooks públicos

El plugin expone hooks para reaccionar a eventos del ciclo de autenticación:

- `WPDR_ACTION_POST_LOGIN` / `druid_post_login`
- `WPDR_ACTION_POST_REGISTER` / `druid_post_register`
- `WPDR_ACTION_POST_EDIT_ACCOUNT` / `druid_post_edit_account`

Ejemplo:

```php
add_action('druid_post_login', function ($context) {
    if (!is_array($context) || empty($context['wp_user_id'])) {
        return;
    }

    // Integracion personalizada tras un login valido en WordPress.
}, 10, 1);
```

### `[druid_auth_controls]`

Renderiza un bloque combinado de login/registro o de cuenta/logout según el estado del usuario en DruID.

Parámetros:

- `entrypoint`: entry point a utilizar.
- `social`: proveedor social a utilizar en login, si aplica.
- `url-to-redirect`: URL a la que volver tras el flujo.
- `state`: estado adicional a preservar.
- `show_login`: `true` o `false`. Por defecto `true`.
- `show_register`: `true` o `false`. Por defecto `true`.
- `get_only_url`: devuelve solo la URL de `login` o `register`.

Si no se informa `url-to-redirect`, el shortcode utiliza por defecto la URL actual de la pagina donde se renderiza. Si por algun motivo esa informacion no llega al callback, el plugin intenta volver a la ultima URL valida del frontend guardada en sesion y, si no existe, a `home_url()`.

Clases CSS:

- `druid-auth-controls`
- `druid-auth-username`
- `druid-auth-control-link`
- `druid-edit-account`
- `druid-logout`
- `druid-login`
- `druid-register`

Ejemplo:

```shortcode
[druid_auth_controls entrypoint="XXXXXXXXXXXXX-default"]
```

### `[druid_auth_controls_login]`

Renderiza un enlace de login.

Parámetros:

- `entrypoint`: entry point a utilizar.
- `social`: proveedor social a utilizar, si aplica.
- `text`: texto del enlace. Por defecto `Login`.
- `url-to-redirect`: URL a la que volver tras el login.
- `state`: estado adicional a preservar.

Si no se informa `url-to-redirect`, el shortcode utiliza por defecto la URL actual de la pagina donde se renderiza. Si por algun motivo esa informacion no llega al callback, el plugin intenta volver a la ultima URL valida del frontend guardada en sesion y, si no existe, a `home_url()`.

Clases CSS:

- `druid-auth-controls-login`
- `druid-auth-control-link`
- `druid-login`

Ejemplo:

```shortcode
[druid_auth_controls_login text="Iniciar sesión"]
```

### `[druid_auth_controls_register]`

Renderiza un enlace de registro.

Parámetros:

- `entrypoint`: entry point a utilizar.
- `text`: texto del enlace. Por defecto `Register`.
- `url-to-redirect`: URL a la que volver tras el registro.
- `state`: estado adicional a preservar.

Si no se informa `url-to-redirect`, el shortcode utiliza por defecto la URL actual de la pagina donde se renderiza. Si por algun motivo esa informacion no llega al callback, el plugin intenta volver a la ultima URL valida del frontend guardada en sesion y, si no existe, a `home_url()`.

Clases CSS:

- `druid-auth-controls-register`
- `druid-auth-control-link`
- `druid-register`

Ejemplo:

```shortcode
[druid_auth_controls_register text="Registrarse"]
```

### `[druid_auth_controls_edit_account]`

Renderiza un enlace a la edición de cuenta en DruID.

Parámetros:

- `entrypoint`: entry point a utilizar.
- `text`: texto del enlace. Por defecto `My account`.
- `url-to-redirect`: URL a la que volver tras editar la cuenta.
- `state`: estado adicional a preservar.

Si no se informa `url-to-redirect`, el shortcode utiliza por defecto la URL actual de la pagina donde se renderiza. Si por algun motivo esa informacion no llega al callback, el plugin intenta volver a la ultima URL valida del frontend guardada en sesion y, si no existe, a `home_url()`.

Clases CSS:

- `druid-auth-controls-edit-account`
- `druid-auth-control-link`
- `druid-edit-account`

Ejemplo:

```shortcode
[druid_auth_controls_edit_account text="Mi cuenta"]
```

### `[druid_auth_controls_logout]`

Renderiza un enlace de logout del plugin.

Parámetros:

- `text`: texto del enlace. Por defecto `Logout`.
- `url-to-redirect`: URL a la que volver tras el logout.
- `state`: estado adicional a preservar.

Si no se informa `url-to-redirect`, el shortcode utiliza por defecto la URL actual de la pagina donde se renderiza. Si por algun motivo esa informacion no llega al callback, el plugin intenta volver a la ultima URL valida del frontend guardada en sesion y, si no existe, a `home_url()`.

Clases CSS:

- `druid-auth-controls-logout`
- `druid-auth-control-link`
- `druid-logout`

Ejemplo:

```shortcode
[druid_auth_controls_logout text="Cerrar sesión"]
```

## Notas de funcionamiento

- El plugin utiliza la sesión de DruID para construir los enlaces y sincronizar el estado del usuario.
- Los shortcodes no generan enlaces si la configuración básica del plugin está incompleta.
- Los errores funcionales del plugin quedan disponibles desde la pantalla `DruID -> Error Log`.
