<?php




// new code added by w3villa 

function get_final_search_query($request)
{
    if (is_search() && !is_admin()) {
        // Output the final search query in the error log or display it on the page
        //error_log($request); // This will log the query in the PHP error log
        echo $request; // This will display the query on the page (for debugging purposes)
    }
	echo $request;
    return $request;
}
//add_filter('posts_request', 'get_final_search_query');


// more where clause for meta search in keyword
function search_by_meta_key_value_where( $where, $wp_query ) {
    global $wpdb;

    // Check if the meta_key and meta_value are set in the query.
    if ( isset( $_REQUEST['query']['s'] ) && $_REQUEST['query']['s'] != '' && $wp_query->query_vars['post_type'] == PROFILE ) {
        // Prepare the WHERE clause to search by meta key and value.
        $meta_key = 'et_professional_title';
        $meta_value = $_REQUEST['query']['s'];

        $where .= $wpdb->prepare(
            " OR $wpdb->posts.ID IN (
                SELECT post_id FROM $wpdb->postmeta
                WHERE meta_key = %s
                AND meta_value LIKE %s
            )",
            $meta_key,
            '%' . $wpdb->esc_like( $meta_value ) . '%'
        );
    }

    return $where;
}
//add_filter( 'posts_where', 'search_by_meta_key_value_where', 10, 2 );


// search with meta, title, content, excerpt, and taxonomy or case
function search_by_meta_title_content_excerpt_taxonomy_where( $where, $wp_query ) {
    global $wpdb;

    // Check if the search query (`s`) is set and if the post type is 'PROFILE'.
    if ( isset( $_REQUEST['query']['s'] ) && $_REQUEST['query']['s'] != '' && isset( $wp_query->query_vars['post_type'] ) && $wp_query->query_vars['post_type'] == PROFILE ) {
        // Get the search keyword.
        $search_keyword = $_REQUEST['query']['s'];

        // Prepare the meta key and value search clause.
        $meta_key = 'et_professional_title';
        $meta_search = $wpdb->prepare(
            " $wpdb->posts.ID IN (
                SELECT post_id FROM $wpdb->postmeta
                WHERE meta_key = %s
                AND meta_value LIKE %s
            )",
            $meta_key,
            '%' . $wpdb->esc_like( $search_keyword ) . '%'
        );

        // Add the title search condition.
        $title_search = $wpdb->prepare(
            " $wpdb->posts.post_title LIKE %s",
            '%' . $wpdb->esc_like( $search_keyword ) . '%'
        );

        // Add the content search condition.
        $content_search = $wpdb->prepare(
            " $wpdb->posts.post_content LIKE %s",
            '%' . $wpdb->esc_like( $search_keyword ) . '%'
        );

        // Add the excerpt search condition.
        $excerpt_search = $wpdb->prepare(
            " $wpdb->posts.post_excerpt LIKE %s",
            '%' . $wpdb->esc_like( $search_keyword ) . '%'
        );

        // Add taxonomy search condition for 'skill'.
        $taxonomy_search = $wpdb->prepare(
            " $wpdb->posts.ID IN (
                SELECT tr.object_id 
                FROM $wpdb->term_relationships AS tr
                INNER JOIN $wpdb->term_taxonomy AS tt ON tr.term_taxonomy_id = tt.term_taxonomy_id
                INNER JOIN $wpdb->terms AS t ON tt.term_id = t.term_id
                WHERE tt.taxonomy = %s
                AND t.name LIKE %s
            )",
            'skill', // Replace 'skill' with your taxonomy slug if different
            '%' . $wpdb->esc_like( $search_keyword ) . '%'
        );

        // Combine all conditions (meta, title, content, excerpt, and taxonomy) with an OR clause.
        $where .= " OR ( $meta_search OR $title_search OR $content_search OR $excerpt_search OR $taxonomy_search )";


       // added additional or tax and keyword
		if ( isset( $_REQUEST['query']['skill'] ) && ! empty( $_REQUEST['query']['skill'] ) && 1==1 ) {
            global $wpdb;

            // Get the 'skill' terms from the query variables
            $skill_terms = (array) $_REQUEST['query']['skill'];

            // Sanitize the terms for safe usage
            $skill_terms = array_map( 'sanitize_text_field', $skill_terms );

            // Generate placeholders for the term slugs
            $placeholders = implode( ', ', array_fill( 0, count( $skill_terms ), '%s' ) );

            // Append the custom WHERE clause for the 'skill' taxonomy
            $where .= $wpdb->prepare(
                " OR EXISTS (
                    SELECT 1 FROM {$wpdb->term_relationships} AS tr
                    INNER JOIN {$wpdb->term_taxonomy} AS tt ON tr.term_taxonomy_id = tt.term_taxonomy_id
                    INNER JOIN {$wpdb->terms} AS t ON tt.term_id = t.term_id
                    WHERE tr.object_id = {$wpdb->posts}.ID
                    AND tt.taxonomy = 'skill'
                    AND t.slug IN ($placeholders)
                )",
                $skill_terms
            );
        }
    }

    return $where;
}
add_filter( 'posts_where', 'search_by_meta_title_content_excerpt_taxonomy_where', 10, 2 );


// added or condition for taxonomy search
function modify_skill_taxonomy_filter( $query ) {
    //print_r($_REQUEST['query']);
   if ($_REQUEST['query']['s'] &&  $_REQUEST['query']['post_type']=='fre_profile' && sizeof($_REQUEST['query']['skill']) > 0) {
      $query->set( 'tax_query', 'OR' );
   }
}
add_action( 'pre_get_posts', 'modify_skill_taxonomy_filter' );






// Hook the custom orderby function into 'posts_orderby' filter
add_filter('posts_orderby', 'custom_search_orderby', 10, 2);
function custom_search_orderby($orderby, $query)
{
    global $wpdb;
    //echo $orderby;
    // Check if this is the main search query and 'orderby' is set to 'relevance'
    if ($query->get('orderby') === 'relevance' || $_REQUEST['query']['s']) {

        // Define your custom order criteria here

        $q = $query->query_vars;

        $orderby = "(CASE 
                  WHEN {$wpdb->posts}.post_title LIKE '%{$query->get('s')}%' THEN 1
                  WHEN {$wpdb->posts}.post_content LIKE '%{$query->get('s')}%' THEN 1
                  WHEN {$wpdb->posts}.post_excerpt LIKE '%{$query->get('s')}%' THEN 3
                  ";

        // odrer for title with and
        $orderand = '';
        $orderby .= " WHEN ";
        foreach ((array)$q['search_terms'] as $term) :
            $term = esc_sql(like_escape($term));
            $orderby .= " {$orderand} {$wpdb->posts}.post_title LIKE '%{$term}%'";
            $orderand = ' AND ';
        endforeach;
        $orderby .= " THEN 4 ";

        // odrer for post_content with and 
        $orderand = '';
        $orderby .= " WHEN ";
        foreach ((array)$q['search_terms'] as $term) :
            $term = esc_sql(like_escape($term));
            $orderby .= " {$orderand} {$wpdb->posts}.post_content LIKE '%{$term}%'";
            $orderand = ' AND ';
        endforeach;
        $orderby .= " THEN 5 ";

       

        // last default else   
        $orderby .= " ELSE 6 END) ";



        // You can add more custom order criteria as needed, separated by commas
        // For example, to add sorting by date in ascending order after relevance:
        $orderby .= ", {$wpdb->posts}.post_date DESC ";

        // Return the custom orderby criteria
        //echo '======'.$orderby;
        return $orderby;
    }

    // For other queries, return the default orderby value
    //echo '//'.$orderby.'//';
    return $orderby;
}


// Function to create the shortcode for cros listing and search
function cros_shortcode($atts = []) {

    // Output the custom HTML
	ob_start(); ?>
    <div class="container">
        <div class="row">
            <div class="col-md-1"></div>
            <div class="col-md-10">
			   <div class="fre-input-field">
					<input class="keyword* search* search_input" id="s**" type="text" name="ais" placeholder="<?php _e("Briefly describe the service you need. For example, 'Need a CRO for Phase I clinical trials in oncology, focusing on bioequivalence studies and patient recruitment", ET_DOMAIN);?>">
					<input type="button" class="fre-normal-btn primary-bg-color find_profile" value="Go">
					<div class="ai_progress">AI is processing your request. This may take up to 1 minute. Please don't refresh the page. <div class="dot-flashing"></div></div>
                </div>
				<div class="cros_err_response"></div>
            </div>
        </div>
    </div>
  

	<div class="container* cros_data">
        <div class="row*">
            <div class="col-md-12 cros_response_list"></div>
        </div>
		<div class="row* cros_response_grid"></div>
    </div>

	<script>
	jQuery(document).ready(function($) {

		// Trigger the find_profile button on Enter key press in the input field
		$('.search_input').on('keypress', function(e) {
			if (e.which === 13) { // 13 is the Enter key code
				e.preventDefault(); // Prevent form submission or default behavior
				$('.find_profile').click(); // Trigger click event on the button
			}
		});

		//  $('.search_input').val('List  all Contract Research Organizations in US and UK');
		//  setTimeout(function() {
		// 		$('.find_profile').click();
		// 	}, 2000); // 2000 milliseconds = 2 seconds

		$('.find_profile').on('click', function() {   
			var input_data = $('.search_input').val();
				if (input_data) {
					$('.find_profile').addClass('loading');  
				} else {
					$('.cros_err_response').text("Please input some query to get process the results.");
					$('.cros_err_response').fadeIn();
					return true;
				}
			$('.cros_data').fadeOut();
			$('.cros_err_response').fadeOut();
			$('.ai_progress').fadeIn();
			

			var api_url = "<?php echo OPENAI_URL; ?>/cros/?query=" + encodeURIComponent(input_data);

			var settings = {
				"url": api_url,
				"method": "GET",
				"timeout": 0
			};

			$.ajax(settings)
				.done(function(response) {
					// Remove loading class on success
					$('.find_profile').removeClass('loading');
					$('.ai_progress').fadeOut();
					console.log('Success:', response);
					console.log("reply:::", response.reply.length);

					if (response.reply && response.reply.length > 0) {
						var listhtml = '';
						var gridhtml = '';
						listhtml += '<div class="col-md-12 cro-results-counts">[ '+response.reply.length+' Results Found ]</div></div>';

						// Loop through each item in the reply array (for list view)
						response.reply.forEach(function(item) {
							listhtml += '<div class="cro-item">';
							listhtml += '<h3>' + item.name + '</h3>';
							listhtml += '<p><strong>Location:</strong> ' + item.location.state + ', ' + item.location.country + '</p>';
							listhtml += '<p><strong>Areas of Expertise:</strong> ' + item.specialization + '</p>';
							listhtml += '<p><strong>Expertise Classification:</strong> ' + item.specialties.join(', ') + '</p>';
							listhtml += '<p><strong>Services Offered:</strong> ' + item.services.join(', ') + '</p>';
							listhtml += '<p><strong>Website:</strong> <a href="' + item.website + '" target="_blank">' + item.website + '</a></p>';
							listhtml += '<p><strong>Brief Description:</strong> ' + item.description + '</p>';
							listhtml += '</div><hr>'; // Adding a horizontal line to separate each item
						});

						// Loop through each item in the reply array (for grid view)
						response.reply.forEach(function(item, index) {
							gridhtml += '<div class="col-md-6"><div class="cro-item">';
							gridhtml += '<h3>' + item.name + '</h3>';
							gridhtml += '<p><strong>Location:</strong> ' + item.location.state + ', ' + item.location.country + '</p>';
							gridhtml += '<p><strong>Areas of Expertise:</strong> ' + item.specialization + '</p>';
							gridhtml += '<p><strong>Expertise Classification:</strong> ' + item.specialties.join(', ') + '</p>';
							gridhtml += '<p><strong>Services Offered:</strong> ' + item.services.join(', ') + '</p>';
							gridhtml += '<p><strong>Website:</strong> <a href="' + item.website + '" target="_blank">' + item.website + '</a></p>';
							gridhtml += '<p><strong>Brief Description:</strong> ' + item.description + '</p>';
							gridhtml += '</div></div>';

							// Add <hr> after every second item (index starts at 0, so index + 1 is item number)
							if ((index + 1) % 2 === 0) {
								gridhtml += '<div class="col-md-12"><hr></div>';
							}
						});

						// Insert the generated HTML into the .cros_response divs
						//$('.cros_response_list').html(listhtml);
						$('.cros_response_grid').html(gridhtml);
						$('.cros_data').fadeIn();

					} else {
						// Fallback if no data is found
						$('.cros_err_response').text('No relevant data found.');
						$('.cros_err_response').fadeIn();
					}
				})
				.fail(function(jqXHR, textStatus, errorThrown) {
					// Handle error
					console.error('Error:', textStatus, errorThrown);
					$('.find_profile').removeClass('loading');
					
					// Update the UI to show an error message
					$('.cros_err_response').text('An error occurred. Please try again with some other string including country. Error: ' + errorThrown);
					$('.cros_err_response').fadeIn();
					$('.ai_progress').fadeOut();
				});
		});

	});
</script>

 <?php
    return ob_get_clean();
}

// Register the shortcode
add_shortcode('cors_list', 'cros_shortcode');


