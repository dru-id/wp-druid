<?php

if (!isset($data['is_user_logged'])) { $data['is_user_logged'] = false; }
if (!isset($data['login_url'])) { $data['login_url'] = '#'; }
if (!isset($data['text']) || $data['text'] === null) { $data['text'] = __('Login', WPDR_LANG_NS); }

echo '<div class="druid-auth-controls-login">';
if (!$data['is_user_logged']) {
    echo '<a href="' . esc_url($data['login_url']) . '" class="druid-auth-control-link druid-login">' . esc_html($data['text']) . '</a>';
}
echo '</div>';
