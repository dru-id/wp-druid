<?php ob_start(); ?>
<?php if (isset($message)) { echo $message; } ?>
<br/>
<br/>
<a href="<?php echo home_url(); ?>"><?php echo __( 'Back to home page', WPDR_LANG_NS); ?></a>
<?php wp_die(ob_get_clean());