<?php

if (!isset($data['is_user_logged'])) { $data['is_user_logged'] = false; }
if (!isset($data['edit_account_url'])) { $data['edit_account_url'] = '#'; }
if (!isset($data['logout_url'])) { $data['logout_url'] = '/druid-actions/logout'; }
if (!isset($data['login_url'])) { $data['login_url'] = '#'; }
if (!isset($data['register_url'])) { $data['register_url'] = '#'; }
if (!isset($data['urlCompleteAccount'])) { $data['urlCompleteAccount'] = ''; }

$username = array();
if (isset($name) && $name) { $username[] = $name; }
if (isset($surname) && $surname) { $username[] = $surname; }
$username = trim(implode(' ', array($name, $surname)));
$protocol = isset($_SERVER['HTTPS'])===true ? 'https://' : 'http://';
$state="&state=".$protocol.$_SERVER['SERVER_NAME']."/wog-promo-gracias/";
?><div class="druid-auth-controls">
    <?php if ($data['data-value']!='qr'):?>
        <?php if ($data['is_user_logged']) : ?>
        <?php if ($data['urlCompleteAccount']=='') : ?>
        <a href="/wog-promo-gracias/" class="btn btn-primary"><?php echo __('Participa'); ?></a>
    <?php else:?>
        <a href="<?php echo $data['urlCompleteAccount']; ?>" class="btn btn-primary"><?php echo __('Participa'); ?></a>
    <?php endif;?>
    <?php else:
        ?>
        <a href="<?php echo $data['register_url'].$state; ?>" class="btn btn-primary"><?php echo __('Participa'); ?></a>
    <?php endif; ?>
    <?php else:
    $oid_=$data['oid'];
    $dna_ = 'https://www.dna.demo.dru-id.com/s/wog/'.$oid_.'?redirect_uri='.urlencode('https://www.rewards.demo.dru-id.com/marketing/d70d55de459780a47f616125dce0fd7b53fb1ffa836278cbc7e19d9f4206523c?uid='.$oid_);
    $button_id = 'custom-button-' . uniqid();
    ?>
    <?php if ($data['is_user_logged']) : ?>
        <button id="<?php echo $button_id; ?>" class="btn btn-primary"><?php echo __('QUIERO MI DESCUENTO'); ?></button>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                let customButton = document.getElementById('<?php echo $button_id; ?>');
                if (customButton) {
                    customButton.addEventListener('click', function() {
                        let action = 'send_promotion';
                        let url = '<?php echo $dna_;?>';
                        let text = '<?php echo __('QUIERO MI DESCUENTO'); ?>';
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
                            + encodeURIComponent(url) + '&text='
                            + encodeURIComponent(text));
                    });
                }
            });
        </script>
    <?php else:
    ?>
        <a href="<?php echo $data['login_url']; ?>"  class="btn btn-primary"><?php echo __('QUIERO MI DESCUENTO'); ?></a>
    <?php endif; ?>
    <?php endif;?>
</div>