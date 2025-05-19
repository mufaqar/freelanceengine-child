<div class="modal fade" id="modal_nda_template">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">
                    <i class="fa fa-times"></i>
                </button>
                <h4 class="modal-title"><?php _e( "NDA Template", ET_DOMAIN ) ?></h4>
            </div>
            <div class="modal-body">
                <form role="form" id="form-nda-template" class="form-nda-template acf-form fre-modal-form" action="" method="post" enctype="multipart/form-data">
                    <div class="fre-content-confirm">
                        <!-- NDA - By: eK -->
                        <h2><?php _e( 'Use this form to add your custom NDA Template', ET_DOMAIN ); ?></h2>
                        <p>- You can let this empty, and use <a href="../../default-nda-template.pdf">Default NDA Template</a>.</p>
                        <p>- Or upload your custom NDA Template.</p>
                    </div>
                    
                    <!-- NDA - By: eK -->
                    <?php
                        acf_form_head();
                        get_header();

                        acf_form(array(
                            'id' => 'form-nda-template',
                            // 'post_id' => the_ID(),
                            // 'post_id' => 'user_' . $user_ID,
                            'post_id' => $post->ID,
                            // 'field_groups' => array('group_657e1adf72d1a'), // Replace with your field group ID
                            'fields' => array('project-nda'), // Replace with your field ID
                            'form' => false,
                            // 'return' => add_query_arg('updated', 'true', get_permalink()), // Redirect after submission
                            'updated_message' => __(''), // NDA Updated Successfully
                            'submit_value' => 'Submit'
                        ));

                        get_footer();
                    ?>
                    
                    <div class="fre-form-btn">
                        <input type="submit" class="fre-normal-btn btn-submit btn-nda-template" value="Submit">
                        <!-- <button type="submit" class="fre-normal-btn btn-submit btn-nda-template""><?php _e('Confirm', ET_DOMAIN) ?></button> -->
                        <span class="fre-form-close" data-dismiss="modal"><?php _e( 'Cancel', ET_DOMAIN ); ?></span>
                    </div>
                </form>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
