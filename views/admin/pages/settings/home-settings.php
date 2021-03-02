<div class="wrap settings">

	<?php include WPDR_PLUGIN_DIR . 'views/admin/partials/header.php'; ?>

	<div class="container-fluid">

		<div class="row">
			<h1><?php _e('DruID Settings', WPDR_LANG_NS); ?></h1>

            <?php if ((count(get_settings_errors()) == 0) && isset($_GET['settings-updated'])) { ?>
				<div id="message" class="updated">
					<p><strong><?php _e('Settings saved.', WPDR_LANG_NS); ?></strong></p>
				</div>
			<?php } ?>
			<?php settings_errors(); ?>

			<ul class="nav nav-tabs" role="tablist">
				<li role="presentation" class="active"><a href="#config" aria-controls="config" role="tab" data-toggle="tab"><?php _e('Basic', WPDR_LANG_NS); ?></a></li>
				<li role="presentation"><a href="#clients" aria-controls="clients" role="tab" data-toggle="tab"><?php _e('Clients', WPDR_LANG_NS); ?></a></li>
			</ul>
		</div>

		<form action="" method="post">
			<div class="tab-content">
				<div role="tabpanel" class="tab-pane row active" id="config">
					<div class="row">
                        <div class="col-lg-12">
                            Basic
                        </div>
					</div>
				</div>
				<div role="tabpanel" class="tab-pane row" id="clients">
                    <div class="row">
                        <div class="col-lg-12">
                            <ul>
                                <li><strong>Client ID:</strong> <?php echo $client_data['client_id']; ?></li>
                                <li><strong>Redirections:</strong>
                                    <ul>
                                        <li><strong>Post login:</strong> <?php echo $client_data['redirects']['post-login']; ?></li>
                                        <li><strong>Post register:</strong> <?php echo $client_data['redirects']['register']; ?></li>
                                        <li><strong>Post confirm user:</strong> <?php echo $client_data['redirects']['confirm-user']; ?></li>
                                        <li><strong>Post edit account:</strong> <?php echo $client_data['redirects']['post-edit-account']; ?></li>
                                    </ul>
                                </li>
                                <li><strong>Apis:</strong>
                                    <?php if (count($client_data['apis']) > 0) : ?>
                                    <ul>
                                        <?php foreach($client_data['apis'] as $api) : ?>
                                            <li><?php echo $api; ?></li>
                                        <?php endforeach; ?>
                                    </ul>
                                    <?php endif; ?>
                                </li>
                            </ul>
                        </div>
                    </div>
				</div>
			</div>

			<div class="row">
                <div class="col-lg-12">
                    <div class="buttons">
                        <input type="submit" name="submit" id="submit" class="button primary" value="Save Changes"/>
                    </div>
                </div>
			</div>
		</form>
	</div>
</div>

<script type="text/javascript">
    jQuery(document).ready(function(){
        var $nav_tabs = jQuery('[role="tab"]');
        if ($nav_tabs.length) {
            $nav_tabs.on('click', function(e){
                e.preventDefault();
                jQuery(this).tab('show');
            });
        }
    });
</script>

