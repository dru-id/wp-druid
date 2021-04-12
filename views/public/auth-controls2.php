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
    ?>
    <?php if ($data['is_user_logged']) : ?>
        <a href="<?php echo $dna_;?>" class="btn btn-primary"><?php echo __('QUIERO MI DESCUENTO'); ?></a>
    <?php else: 
        ?>
        <a href="<?php echo $data['login_url']; ?>"  class="btn btn-primary"><?php echo __('QUIERO MI DESCUENTO'); ?></a>
    <?php endif; ?>
<?php endif;?>   
</div>