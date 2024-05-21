<?php
// custom-link.php

// Verifica si se proporcionó una URL y un texto para el botón
$url = isset($href) ? esc_url($href) : '#';
$text = isset($text) ? esc_html($text) : 'Button';
$class = isset($class) ? esc_attr($class) : '';

// Agrega un identificador único al botón
$button_id = 'custom-button-' . uniqid();
?>

<button id="<?php echo $button_id; ?>" class="<?php echo $class; ?>"><?php echo $text; ?></button>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        let customButton = document.getElementById('<?php echo $button_id; ?>');
        if (customButton) {
            customButton.addEventListener('click', function() {
                let action = 'send_click';
                let url = '<?php echo esc_url($url); ?>';
                let current_url = window.location.href;// Utiliza la URL recibida del shortcode
                let text = '<?php echo esc_js($text); ?>'; // Utiliza el texto recibido del shortcode
                let id = '<?php echo $button_id; ?>';

                // Realiza una solicitud AJAX al servidor para ejecutar la acción
                var xhr = new XMLHttpRequest();
                xhr.open('POST', '<?php echo admin_url('admin-ajax.php'); ?>');
                xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                xhr.onload = function() {
                    if (xhr.status === 200) {
                        // Si la solicitud AJAX es exitosa, redirige al usuario a la URL del botón
                        window.location.href = url;
                    } else {
                        // Maneja cualquier error que ocurra durante la solicitud AJAX
                        console.error('Error en la solicitud AJAX:', xhr.statusText);
                    }
                };
                xhr.onerror = function() {
                    // Maneja cualquier error de red que ocurra durante la solicitud AJAX
                    console.error('Error de red al realizar la solicitud AJAX.');
                };
                xhr.send('action=' + action + '&id='
                    + encodeURIComponent(id) + '&url='
                    + encodeURIComponent(url) + '&current_url='
                    + encodeURIComponent(current_url) + '&text='
                    + encodeURIComponent(text));
            });
        }
    });
</script>