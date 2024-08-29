<?php

if (!isset($data['is_user_logged'])) { $data['is_user_logged'] = false; }
if (!isset($data['edit_account_url'])) { $data['edit_account_url'] = '#'; }
if (!isset($data['text'])) { $data['text'] = 'My account'; }

echo '<div class="druid-auth-controls-edit-account">';
if ($data['is_user_logged']) {
    echo '<a href="'.$data['edit_account_url'].'" class="druid-auth-control-link druid-edit-account">'.__($data['text']).'</a>';
}
echo '</div>';

