<?php

if (!isset($data['is_user_logged'])) { $data['is_user_logged'] = false; }
if (!isset($data['edit_account_url'])) { $data['edit_account_url'] = '#'; }
if (!isset($data['logout_url'])) { $data['logout_url'] = '/druid-actions/logout'; }
if (!isset($data['login_url'])) { $data['login_url'] = '#'; }
if (!isset($data['register_url'])) { $data['register_url'] = '#'; }

$username = array();
if (!empty($data['name'])) { $username[] = $data['name']; }
if (!empty($data['surname'])) { $username[] = $data['surname']; }
$usernames = trim(implode(' ', $username));


echo '<div class="druid-auth-controls">';
if ($data['is_user_logged']) {
    if ($usernames)
        echo '<div class="druid-auth-username">' . esc_html($usernames) . '</div>';

    echo '<a href="' . esc_url($data['edit_account_url']) . '" class="druid-auth-control-link druid-edit-account">' . esc_html__('Mi Cuenta', WPDR_LANG_NS) . '</a>';
    echo '<a href="' . esc_url($data['logout_url']) . '" class="druid-auth-control-link druid-logout">' . esc_html__('Desconectar', WPDR_LANG_NS) . '</a>';
} else {

    if ($data['show_login'])
        echo '<a href="' . esc_url($data['login_url']) . '" class="druid-auth-control-link druid-login">' . esc_html__('Login', WPDR_LANG_NS) . '</a>';

    if ($data['show_register'])
        echo '<a href="' . esc_url($data['register_url']) . '" class="druid-auth-control-link druid-register">' . esc_html__('Registro', WPDR_LANG_NS) . '</a>';

}
echo '</div>';
