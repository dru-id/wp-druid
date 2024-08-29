<?php

if (!isset($data['is_user_logged'])) { $data['is_user_logged'] = false; }
if (!isset($data['logout_url'])) { $data['logout_url'] = '/druid-actions/logout'; }
if (!isset($data['text'])) { $data['text'] = 'Logout'; }

echo '<div class="druid-auth-controls-logout">';
if ($data['is_user_logged']) {
    echo '<a href="'.$data['logout_url'].'" class="druid-auth-control-link druid-logout">'.__($data['text']).'</a>';
}
echo '</div>';

