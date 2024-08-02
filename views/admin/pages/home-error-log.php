<div class="wrap">
    <?php include WPDR_PLUGIN_DIR . 'views/admin/partials/header.php'; ?>
    <h1 class="wp-heading-inline"><?php _e('Error Log', WPDR_LANG_NS); ?></h1>
    <hr class="wp-header-end">

    <table class="widefat">
        <thead>
            <tr>
                <th>Date</th>
                <th>Section</th>
                <th>Error code</th>
                <th>Message</th>
            </tr>
        </thead>
        <tbody>
    <?php

        if (empty($data)) {
            ?>
            <tr>
                <td class="message" colspan="4">No errors.</td>
            </tr>
        <?php
        }

        foreach ($data as $item) {
	?>
        <tr>
            <td><?php echo date('m/d/Y H:i:s', strtotime($item->date)); ?></td>
            <td><?php echo $item->section; ?></td>
            <td><?php echo (empty($item->code) ? '-' : $item->code); ?></td>
            <td><?php echo strip_tags($item->message); ?></td>
        </tr>
    <?php } ?>
        </tbody>
    </table>

</div>
