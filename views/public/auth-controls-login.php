<?php

if (!isset($data['is_user_logged'])) { $data['is_user_logged'] = false; }
if (!isset($data['login_url'])) { $data['login_url'] = '#'; }
if (!isset($data['text'])) { $data['text'] = 'Login'; }

echo '<div class="druid-auth-controls-login">';
if (!$data['is_user_logged']) {
    echo '<a href="' . $data['login_url'] . '" class="druid-auth-control-link druid-login">' . __($data['text']) . '</a>';
}
echo '</div>';

