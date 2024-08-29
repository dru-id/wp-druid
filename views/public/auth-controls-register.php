<?php

if (!isset($data['is_user_logged'])) { $data['is_user_logged'] = false; }
if (!isset($data['register_url'])) { $data['register_url'] = '#'; }
if (!isset($data['text'])) { $data['text'] = 'Register'; }

echo '<div class="druid-auth-controls-register">';
if (!$data['is_user_logged']) {
    echo '<a href="' . $data['register_url'] . '" class="druid-auth-control-link druid-register">' . __($data['text']) . '</a>';
}
echo '</div>';

