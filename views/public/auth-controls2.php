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
    <?php if ($data['is_user_logged']) : ?>
    	<?php if ($data['urlCompleteAccount']=='') : ?>
        	<a href="/wog-promo-gracias/" class="btn btn-primary"><?php echo __('Participa'); ?></a>
		<?php else:?>
			<a href="<?php echo $data['urlCompleteAccount']; ?>" class="btn btn-primary"><?php echo __('Participa'); ?></a>
    	<?php endif;?>
    <?php else : 
    	?>
        <a href="<?php echo $data['register_url'].$state; ?>" class="btn btn-primary"><?php echo __('Participa'); ?></a>
        
    <?php endif; ?>
</div>