<?php
/**
 * The template for displaying the footer
 *
 * Contains the closing of the #content div and all content after.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package WP_Bootstrap_Starter
 */

?>
<?php if(!is_page_template( 'blank-page.php' ) && !is_page_template( 'blank-page-with-container.php' )): ?>
			
			
			<!-- </div> .row -->
		</div><!-- .container -->
	</div><!-- #content -->
  <?php //get_template_part( 'footer-widget' ); ?>

  <footer id="footer" class="container-fluid">
    <div class="container footer">
      <?php show_post('footer');  // Shows the content of the "About" page. ?>
    </div>
  </footer>

  <footer id="colophon" class="site-footer <?php echo wp_bootstrap_starter_bg_class(); ?>" role="contentinfo">
		<div class="container">
        <div class="site-info">
            &copy; <?php echo date('Y'); ?> WOG CORPORATION
        </div><!-- close .site-info -->
		</div>
	</footer><!-- #colophon -->

  <!-- button to top -->
  <a id="toTop" style="opacity: 1;"></a>

<?php endif; ?>
</div><!-- #page -->

<?php wp_footer(); ?>

<script src="<?php echo get_stylesheet_directory_uri(); ?>/js/owl/owl.carousel.min.js"></script>

</body>
</html>