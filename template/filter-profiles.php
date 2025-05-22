<div class="fre-profile-filter-box">
      <script type="data/json" id="search_data">
            <?php
                $search_data = $_POST;
                echo json_encode($search_data);
            ?>
      </script>
      <div class="profile-filter-header visible-sm visible-xs">
          <a class="profile-filter-title" href=""><?php _e('Advance search', ET_DOMAIN);?></a>
      </div>
      <div class="fre-profile-list-filter">
            <div class="row">
                  <div class="col-md-12">
                      <div class="fre-input-field">
                          <input class="keyword* search* search_input" id="s**" type="text" name="ais" placeholder="<?php _e("Briefly describe your project and the expertise you need. For example, 'Looking for a molecular biologist with expertise in PCR and recombinant DNA for a nanomedicine project.", ET_DOMAIN);?>">
                          <input type="button" class="fre-normal-btn primary-bg-color find_profile" value="Go">
                          <div class="ai_progress">AI is analyzing your needs to find the best consultants. This may take up to 1 minute. Please don't refresh the page. <div class="dot-flashing"></div></div>
                      </div>
                      <div class="cros_err_response"></div>
                  </div>
             </div>
          <form class="form_filter_inputs">      
              <div class="row">
                  <div class="col-md-4">
                      <div class="fre-input-field">
                          <label for="keywords" class="fre-field-title"><?php _e('Keyword', ET_DOMAIN);?></label>
                          <input class="keyword search" id="s" type="text" name="s" placeholder="<?php _e('Search freelancers by keyword', ET_DOMAIN);?>">
                      </div>
                  </div>
                  <div class="col-md-4">
                      <div class="fre-input-field dropdown">
                          <label for="skills" class="fre-field-title"><?php _e('Skills', ET_DOMAIN);?></label>
                          <input id="skills" class="dropdown-toggle fre-skill-field" type="text" placeholder="<?php _e ('Search freelancers by skills', ET_DOMAIN ); ?>" data-toggle="dropdown" readonly>
                          <?php $terms = get_terms('skill', array('hide_empty' => 0)); ?>
                          <?php if(!empty($terms)) : ?>
                            <div class="dropdown-menu dropdown-menu-skill">
                              <?php if(count($terms) > 7) : ?>
                                  <div class="search-skill-dropdown">
                                    <input class="fre-search-skill-dropdown" type="text">
                                  </div>
                                <?php endif ?>
                              <ul class="fre-skill-dropdown" data-name="skill">

                                <?php
                                    foreach ($terms as $key => $value) {
                                        echo '<li><a class="fre-skill-item" name="'.$value->slug.'" href="">'.$value->name.'</a></li>';
                                    }
                                ?>
                              </ul>
                            </div>
                          <?php endif; ?>
                      </div>
                  </div>
                  <div class="col-md-4">
                      <div class="fre-input-field">
                          <label for="total_earning" class="fre-field-title"><?php _e('Earning', ET_DOMAIN);?> (<?php fre_currency_sign() ?>)</label>
                          <select name="earning" id="total_earning" class="fre-chosen-single">
                              <option value=""><?php _e('Any amount', ET_DOMAIN);?></option>
                              <option value="100">0 - 100</option>
                              <option value="100-1000">100 - 1000</option>
                              <option value="1000-10000">1000 - 10000</option>
                              <option value="10000"><?php _e('Greater than 10000',ET_DOMAIN) ?> </option>
                          </select>
                      </div>
                  </div>
                  <div class="clearfix"></div>
                  <div class="col-md-4">
                      <div class="fre-input-field project-number-worked">
                          <label for="project-number-worked" class="fre-field-title"><?php _e('Projects Worked', ET_DOMAIN);?></label>
                          <select name="total_projects_worked" id="project-number-worked" class="fre-chosen-single">
                              <option value=""><?php _e('Any projects worked', ET_DOMAIN);?></option>
                              <option value="10">0 - 10</option>
                              <option value="20">11 - 20</option>
                              <option value="30">21 - 30</option>
                              <option value="40"><?php _e('Greater than 30', ET_DOMAIN) ?></option>
                          </select>
                      </div>
                  </div>
                  <div class="col-md-4">
                      <div class="fre-input-field">
                          <label for="location" class="fre-field-title"><?php _e('Location', ET_DOMAIN);?></label>
                          <?php
                              ae_tax_dropdown( 'country' ,array(
                                      'attr'            => 'data-chosen-width="100%" data-chosen-disable-search="" data-placeholder="'.__("Select country", ET_DOMAIN).'"',
                                      'class'           => 'fre-chosen-single',
                                      'hide_empty'      => false,
                                      'hierarchical'    => true ,
                                      'value'           => 'slug',
                                      'id'              => 'country',
                                      'show_option_all' => __("Select country", ET_DOMAIN)
                                  )
                              );
                          ?>
                      </div>
                  </div>
                  <div class="col-md-4">
                      <?php $max_slider = ae_get_option('fre_slide_max_budget_freelancer', 2000); ?>
                      <div class="fre-input-field fre-budget-field">
                          <label for="budget" class="fre-field-title"><?php _e('Hourly Rate', ET_DOMAIN);?> (<?php fre_currency_sign() ?>)</label>
                          <input id="budget" class="filter-budget-min" type="number" name="min_budget" value="0" min="0">
                          <span>-</span>
                          <input class="filter-budget-max" type="number" name="max_budget" value="<?php echo $max_slider; ?>" min="0">
                          <input id="hour_rate" type="hidden" name="hour_rate" value="0,<?php echo $max_slider; ?>"/>
                          <input type="hidden" name="user_available" id="user_available" value= "yes" />
                      </div>
                  </div>
              </div>
              <a class="profile-filter-clear clear-filter secondary-color" href=""><?php _e('Clear all filters', ET_DOMAIN);?></a>
          </form>
      </div>
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

        $('.find_profile').on('click', function() {
            $('.clear-filter').click();
            var input_data = $('.search_input').val();
            if (input_data) {
					$('.find_profile').addClass('loading');  
				} else {
					$('.cros_err_response').text("Please input some query to get process the results.");
					$('.cros_err_response').fadeIn();
					return true;
				}
            $('.cros_err_response').fadeOut();
            $('.ai_progress').fadeIn();

            var api_url = "<?php echo OPENAI_URL; ?>/freelancers/?query=" + encodeURIComponent(input_data);

            var settings = {
                "url": api_url,
                "method": "GET",
                "timeout": 0
            };

            $.ajax(settings)
                .done(function(response) {
                    // Handle success
                    $('.find_profile').removeClass('loading');
                    $('.ai_progress').fadeOut();
                    console.log('Success:', response);

                    var keyword = response.reply.keyword;
                    if (keyword) {
                        //console.log("keyword:: ", response.reply.keyword)
                        $('.keyword.search').val(keyword);
                    } else {
                        $('.keyword.search').val('');
                    }

                    var skills = response.reply.skills;
                    if (skills) {
                        //console.log("skills:: ", response.reply.skills)
                        $('#skills').val(skills);
                    } else {
                        $('#skills').val('');
                    }

                    var earnings = response.reply.earnings;
                    if (earnings) {
                        //console.log("total_earning:: ", response.reply.earnings)
                        $('#total_earning option').filter(function() {
                            return $(this).text() === earnings;
                        }).prop('selected', true);
                        $('#total_earning').trigger("chosen:updated");
                    } else {
                        $('#total_earning option').filter(function() {
                            return $(this).text() === 'Any amount';
                        }).prop('selected', true);
                        $('#total_earning').trigger("chosen:updated");
                    }

                    var projects_worked = response.reply.projects_worked;
                    if (projects_worked) {
                        //console.log("projects_worked:: ", response.reply.projects_worked)
                        $('#project-number-worked option').filter(function() {
                            return $(this).text() === projects_worked;
                        }).prop('selected', true);
                        $('#project-number-worked').trigger("chosen:updated");
                    } else {
                        $('#project-number-worked option').filter(function() {
                            return $(this).text() === 'Any projects worked';
                        }).prop('selected', true);
                        $('#project-number-worked').trigger("chosen:updated");
                    }

                    var location = response.reply.location;
                    if (location) {
                        //console.log("location:: ", response.reply.location)
                        $('#country option').filter(function() {
                            return $(this).text() === location;
                        }).prop('selected', true);
                        $('#country').trigger("chosen:updated");
                    } else {
                        $('#country option').filter(function() {
                            return $(this).text() === 'Select country';
                        }).prop('selected', true);
                        $('#country').trigger("chosen:updated");
                    }

                    var hourly_rate = response.reply.hourly_rate;
                    if (hourly_rate) {
                        //console.log("hourly_rate:: ", response.reply.hourly_rate)
                        var rate_as_integer = parseInt(hourly_rate.replace(/[^0-9]/g, ''), 10);
                        $('.filter-budget-max').val(rate_as_integer);
                    } else {
                        $('.filter-budget-max').val('2000');
                    }

                    // handle events
                    if (keyword) {
                        var e = jQuery.Event('keyup', { keyCode: 32 }); // 32 is the key code for the spacebar
                        $('.keyword').trigger(e);
                    }

                    if (skills) {
                        $('form ul.fre-skill-dropdown li .fre-skill-item').removeClass('active');
                        skills.forEach(function(skill) {
                            var skliiname = skill.toLowerCase().replace(/\s+/g, '-');
                            $('.fre-skill-dropdown li a[name="' + skliiname + '"]').click();
                            //console.log("Clicked skill:", skliiname);
                        });
                    }

                    if (earnings) {
                        $('#total_earning').change();
                    }

                    if (projects_worked) {
                        $('#project-number-worked').change();
                    }

                    if (location) {
                        $('#country').change();
                    }

                    if (hourly_rate) {
                        $('.fre-budget-field input[name="max_budget"]').change();
                    }

                })
                .fail(function(jqXHR, textStatus, errorThrown) {
                    // Handle error
                    console.error('Error:', textStatus, errorThrown);
                    $('.cros_err_response').text('An error occurred. Please try again. Error: ' + errorThrown);
                    $('.cros_err_response').fadeIn();
                    $('.ai_progress').fadeOut();
                    $('.find_profile').removeClass('loading');
                });

        });

        $('.send_data').on('click', function() {
            // Prepare the input_data as an object
            var input_data = {
                query: {
                    post_type: 'fre_profile',
                    post_status: 'publish',
                    orderby: 'date',
                    place_category: '',
                    location: '',
                    showposts: '',
                    order: 'DESC',
                    paginate: 'page',
                    s: 'deepa'
                },
                page: 1,
                paged: 1,
                paginate: 'page',
                action: 'ae-fetch-profiles' // The action registered in PHP
            };

            // var api_url = ABSPATH . 'wp-admin/admin-ajax.php';
            var api_url = admin_url( 'admin-ajax.php' );

            $.ajax({
                url: api_url, // WordPress Ajax URL
                type: 'POST',
                data: input_data, // Send the input_data object
                success: function(response) {
                    if (response.success) {
                        console.log('Data retrieved successfully:', response);
                        // Handle success - you can display the data on your page here
                    } else {
                        console.error('Error retrieving data:', response);
                        // Handle error
                    }
                },
                error: function(error) {
                    console.error('Error in Ajax request:', error);
                    // Handle error
                }
            });
        });

    });
</script>

