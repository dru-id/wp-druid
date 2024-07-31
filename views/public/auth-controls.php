<?php

if (!isset($data['is_user_logged'])) { $data['is_user_logged'] = false; }
if (!isset($data['edit_account_url'])) { $data['edit_account_url'] = '#'; }
if (!isset($data['logout_url'])) { $data['logout_url'] = '/druid-actions/logout'; }
if (!isset($data['login_url'])) { $data['login_url'] = '#'; }
if (!isset($data['register_url'])) { $data['register_url'] = '#'; }

$username = array();
if (isset($name) && $name) { $username[] = $name; }
if (isset($surname) && $surname) { $username[] = $surname; }
$username = trim(implode(' ', $username));
?><div class="druid-auth-controls">
    <?php if ($data['is_user_logged']) : ?>
        <?php if ($username) : ?>
            <div style="color: black;"><?php echo $username; ?></div>
        <?php endif; ?>
        <a href="<?php echo $data['edit_account_url']; ?>" class="druid-auth-control-link druid-login"><?php echo __('Mis datos'); ?></a>
        <a href="<?php echo $data['logout_url']; ?>" class="druid-auth-control-link druid-logout"><?php echo __('Desconectar'); ?></a>
    <?php else : ?>
        <a href="<?php echo $data['login_url']; ?>" class="druid-auth-control-link druid-login"><?php echo __('Login'); ?></a>
        <a href="<?php echo $data['register_url']; ?>" class="druid-auth-control-link druid-register"><?php echo __('Register'); ?></a>
    <?php endif; ?>
</div>