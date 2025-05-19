<div class="modal fade" id="modal_send_nda">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">
                    <i class="fa fa-times"></i>
                </button>
                <h4 class="modal-title"><?php _e( "Send NDA", ET_DOMAIN ) ?></h4>
            </div>
            <div class="modal-body">
                <form role="form" id="form-send-nda" class="form-send-nda acf-form fre-modal-form" action="" method="post" enctype="multipart/form-data">
                    <div class="fre-content-confirm">
                        <h2><?php _e( 'There is one more step', ET_DOMAIN ); ?></h2>
                        <p><?php _e( '- Once you confirm the NDA, your bid will be qualified to be reviewed and have chance to be awarded.', ET_DOMAIN ); ?></p>
                        
                        <!-- NDA - By: eK -->
                        <p>- You can download
                        <?php
                            $ek_nda_template = get_field('project-nda', $post->ID);
                            if ( $ek_nda_template ) {
                                echo '<a href="' . $ek_nda_template['url'] .'">Project NDA Template</a>';
                            }
                            else {
                                echo '<a href="../../default-nda-template.pdf">Default NDA Template</a>';
                            }
                        ?>
                        , and fill in the information and sign it, Finally upload it using the following form.</p>
                    </div>
                    
                    <!-- NDA - By: eK -->
                    <?php
                        $ek_bidding_id  = 0;
                        $child_posts = get_children(
                            array(
                                'post_parent' => $post->ID,
                                'post_type'   => BID,
                                'post_status' => 'publish',
                                'author'      => $user_ID
                            )
                        );
                        if ( ! empty( $child_posts ) ) {
                            foreach ( $child_posts as $key => $value ) {
                                $ek_bidding_id = $value->ID;
                            }
                        }

                        acf_form_head();
                        get_header();

                        acf_form(array(
                            'id' => 'form-send-nda',
                            // 'post_id' => the_ID(),
                            // 'post_id' => 'user_' . $user_ID,
                            'post_id' => $ek_bidding_id,
                            // 'field_groups' => array('group_657e1adf72d1a'), // Replace with your field group ID
                            'fields' => array('bid-nda'), // Replace with your field ID
                            'form' => false,
                            // 'return' => add_query_arg('updated', 'true', get_permalink()), // Redirect after submission
                            'updated_message' => __(''), // NDA Updated Successfully
                            'submit_value' => 'Confirm NDA'
                        ));

                        get_footer();
                    ?>
                    
                    <div class="fre-form-btn">
                        <input type="submit" class="fre-normal-btn btn-submit btn-send-nda" value="Confirm NDA">
                        <!-- <button type="submit" class="fre-normal-btn btn-submit btn-send-nda"><?php _e('Confirm', ET_DOMAIN) ?></button> -->
                        <span class="fre-form-close" data-dismiss="modal"><?php _e( 'Cancel', ET_DOMAIN ); ?></span>
                    </div>
                </form>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
