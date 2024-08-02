<div class="wrap">
    <?php include WPDR_PLUGIN_DIR . 'views/admin/partials/header.php'; ?>
    <h1 class="wp-heading-inline"><?php _e('DruID Settings', WPDR_LANG_NS); ?></h1>
    <hr class="wp-header-end">

    <h2 class="nav-tab-wrapper">
        <a class="nav-tab <?php echo ($tab == 'config') ? 'nav-tab-active' : ''?>" href="<?php echo add_query_arg('tab', 'config', $current_admin_page) ?>">Config</a>
        <a class="nav-tab <?php echo ($tab == 'logs') ? 'nav-tab-active' : ''?>" href="<?php echo add_query_arg('tab', 'logs', $current_admin_page) ?>">Druid Logs</a>
    </h2>

    <?php settings_errors(); ?>

    <?php if ($tab == 'config') { ?>
        <div class="form-wrap">
            <form action="<?php echo admin_url('admin-post.php'); ?>" id="druid-config" method="post" class="validate">
                <input type="hidden" name="action" value="edit_druid_settings" />
                <input type="hidden" name="druid_meta_nonce" value="<?php echo wp_create_nonce('druid_edit_config'); ?>" />

                <div class="form-field form-required regular-text">
                    <label for="domain">Domain</label>
                    <input name="domain" id="domain" type="text" value="<?php echo $actual_config->getDomain(); ?>" size="40" aria-required="true" class="form-required">
                </div>
                <div class="form-field form-required regular-text">
                    <label for="client_id">Client ID</label>
                    <input name="client_id" id="client_id" type="text" value="<?php echo $actual_config->getClientId(); ?>" size="40" aria-required="true" class="form-required">
                </div>
                <div class="form-field form-required regular-text">
                    <label for="client_secret">Client Secret</label>
                    <input name="client_secret" id="client_secret" type="text" value="<?php echo $actual_config->getClientSecret(); ?>" size="40" aria-required="true">
                </div>
                <div class="form-field form-required regular-text">
                    <label for="environment">Environment</label>
                    <select name="environment" id="log_level">
                        <option value="dev" <?php echo ($actual_config->getEnvironment()=='dev') ? 'selected' : ''; ?>>Dev</option>
                        <option value="test" <?php echo ($actual_config->getEnvironment()=='test') ? 'selected' : ''; ?>>Test</option>
                        <option value="prod" <?php echo ($actual_config->getEnvironment()=='prod') ? 'selected' : ''; ?>>Prod</option>
                    </select>
                </div>

                <div class="form-field form-required">
                    <label for="entry_points">Entry Point</label>
                    <input name="entry_points" id="entry_points" type="text" value="<?php echo $actual_config->getEntryPoint(); ?>" size="40" aria-required="true" class="form-required">
                </div>
                <div class="form-field form-required">
                    <label for="callback">Callback</label>
                    <input name="callback" id="callback" type="text" value="<?php echo $actual_config->getCallback(); ?>" size="60" aria-required="true" maxlength="200" class="form-required">
                </div>
                <div class="form-field form-required">
                    <label for="log_path">Log Path</label>
                    <input name="log_path" id="log_path" type="text" value="<?php echo $actual_config->getLogPath(); ?>" size="60" aria-required="true" maxlength="200" class="form-required">
                    <p>Relative Path Folder to the Document Root</p>
                </div>
                <div class="form-field form-required">
                    <label for="cache_path">Cache Path</label>
                    <input name="cache_path" id="cache_path" type="text" value="<?php echo $actual_config->getCachePath(); ?>" size="60" aria-required="true" maxlength="200" class="form-required">
                    <p>Relative Path Folder to the Document Root</p>
                </div>
                <div class="form-field form-required regular-text">
                    <label for="log_level">Log Level</label>
                    <select name="log_level" id="log_level">
                        <option value="DEBUG" <?php echo ($actual_config->getLogLevel()=='DEBUG') ? 'selected' : ''; ?>>Debug</option>
                        <option value="ERROR" <?php echo ($actual_config->getLogLevel()=='ERROR') ? 'selected' : ''; ?>>Error</option>
                        <option value="OFF" <?php echo ($actual_config->getLogLevel()=='OFF') ? 'selected' : ''; ?>>Off</option>
                    </select>
                </div>
                <?php submit_button('Save Changes') ?>
            </form>
        </div>
    <?php } else {

        $logs_last = empty($actual_config->getClientId()) ? '' : $_SERVER['DOCUMENT_ROOT'].'/'.$actual_config->getLogPath().'/'.$actual_config->getClientId().'/gid-last-request.log';
        $logs_all = empty($actual_config->getClientId()) ? '' : $_SERVER['DOCUMENT_ROOT'].'/'.$actual_config->getLogPath().'/'.$actual_config->getClientId().'/gid-all-requests.log';
        ?>
        <div class="wp-clearfix">
            <table class="form-table">
                <tr>
                    <th scope="row"><label>Last Request</label></th>
                    <td><textarea name="last-request" cols="100" rows="20"><?php if (!empty($logs_last)) echo file_get_contents($logs_last); ?></textarea></td>
                </tr>
                <tr>
                    <th scope="row">All Requests</th>
                    <td><textarea name="last-request" cols="100" rows="30"><?php if (!empty($logs_all)) echo file_get_contents($logs_all); ?></textarea></td>
                </tr>
            </table>
        </div>
    <?php } ?>
</div>

<script type="text/javascript">
    jQuery(document).ready(function(a) {
        jQuery("#druid-config").submit(function() {
            console.log("validate");
            return wpAjax.validateForm("#druid-config")
        })
    });
</script>
