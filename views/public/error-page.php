<?php ob_start(); ?>
<?php if (isset($message)) { echo esc_html($message); } ?>
<br/>
<br/>
<a href="<?php echo esc_url(home_url()); ?>"><?php echo esc_html__( 'Back to home page', WPDR_LANG_NS); ?></a>
<?php wp_die(ob_get_clean());
