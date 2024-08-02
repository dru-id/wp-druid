<div class="wrap">
	<?php include WPDR_PLUGIN_DIR . 'views/admin/partials/header.php'; ?>
    <h1 class="wp-heading-inline"><?php _e('Promotions', WPDR_LANG_NS); ?></h1>
    <hr class="wp-header-end">

    <?php settings_errors(); ?>

    <form method="post" action="/wp/wp-admin/admin.php?page=druid-promotions">
        <input type="hidden" name="page" value="my_list_test" />
        <?php
//        $promotions_list->prepare_items();
//        $promotions_list->search_box('Search', 's');
//
//        $promotions_list->display();
        ?>

        <?php
        $options = array(
        'post_id' => 'new_post', // post to save to
        'field_groups' => array(9),
        'form' => false,
        'form_attributes' => array(
            'id' => 'design-fotos',
            'action' => '/wp/wp-admin/admin.php?page=druid-promotions',
            'method' => 'post'
        ),
        //'return' => add_query_arg( 'updated', 'true', (get_admin_url() . 'admin.php?page=s-advanced-search') ), // return url
        'submit_value' => 'Save',
        'updated_message' => 'Saved',
        );

        //acf_form($options);

        $field_groups[] = acf_get_field_group( 9 );

        //load fields based on field groups
        if( !empty($field_groups) ) {

            foreach( $field_groups as $field_group ) {

                $field_group_fields = acf_get_fields( $field_group );

                if( !empty($field_group_fields) ) {

                    foreach( array_keys($field_group_fields) as $i ) {
                        $field = acf_extract_var($field_group_fields, $i);;

                        if ($field['name'] == 'entry_point')
                            $field['value'] = 'asdfd';

                        $fields[] = $field;
                    }

                }

            }

        }
        ?>
        <div class="acf-fields acf-form-fields -<?php echo $args['label_placement']; ?>">
			<?php


			// html before fields
			//echo $args['html_before_fields'];


			// render
			acf_render_fields( $fields, $post_id, $args['field_el'], $args['instruction_placement'] );


			// html after fields
			//echo $args['html_after_fields'];


			?>
        </div>

        <?php
       // acf_get_form(9);
        ?>

        <?php submit_button('Save Changes')?>
    </form>

</div>