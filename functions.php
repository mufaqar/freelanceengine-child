<?php


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

