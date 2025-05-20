<?php
/* Template Name: Dashboard Page */

if (!is_user_logged_in()) {
    wp_redirect(wp_login_url());
    exit;
}

global $wp_query, $ae_post_factory, $post, $current_user, $user_ID;
//convert current user
$ae_users  = AE_Users::get_instance();
$user_data = $ae_users->convert( $current_user->data );
$user_role = ae_user_role( $current_user->ID );
//convert current profile
$post_object = $ae_post_factory->get( PROFILE );
$author_id = get_query_var( 'author' );
$author_name = get_the_author_meta( 'display_name', $author_id );
$profile_id = get_user_meta( $user_ID, 'user_profile_id', true );
$profile = array();
if ( $profile_id ) {
	$profile_post = get_post( $profile_id );
	if ( $profile_post && ! is_wp_error( $profile_post ) ) {
		$profile = $post_object->convert( $profile_post );
	}
}

get_header();

// Initialize variables
$total_projects = 0;
$current_active_projects = 0;
$total_hired_consultant = 0;
$total_spent = 0;
$total_bids = 0;
$total_accepted_bids = 0;
$total_earned = 0;
$profile_completion_rate = 0;

if ( ae_user_role( $author_id ) == FREELANCER ) {
    // Fetch data for freelancers
    $total_bids = get_total_bids($user_ID);
    $total_accepted_bids = get_post_meta( $profile_id, 'total_projects_worked', true );
    // $total_earned = $profile->earned;
    $total_earned = fre_count_total_user_earned( $user_ID );
    $profile_completion_rate = calculate_profile_completion_rate( $profile );
} elseif ( ae_user_role( $author_id ) != FREELANCER ) {
    // Fetch data for employers
    $total_projects = fre_count_user_posts_by_type( $user_ID, 'project', '"publish","complete","close","disputing","disputed", "archive" ', true );
    $current_active_projects = fre_count_user_posts_by_type( $user_ID, 'project', '"publish","disputing","disputed" ', true );
    $total_hired_consultant = fre_count_hire_freelancer( $user_ID );
    $total_spent = fre_count_total_user_spent( $user_ID );
}

?>

<!-- Dashboard - By: eK -->
<div class="fre-page-wrapper list-profile-wrapper">
    <div class="fre-page-title">
        <div class="container">
            <h1><strong><?php echo $current_user->display_name; ?>'s</strong> Dashboard</h1>
            <div class="dashboard-data">
                <!-- Statistics -->
                <?php if ( ae_user_role( $author_id ) == FREELANCER ): ?>
                    <div class="dashboard-container">
                        <t2>Consultant Statistics</t2>
                        <div class="dashboard-data">
                            <div class="dashboard-item">
                                <strong>Profile Completion Rate:</strong>
                                <!-- <span><?php echo $profile_completion_rate; ?> %</span> -->
                                <div class="rate-container">
                                    <div class="progress-bar">
                                        <div class="progress" style="width: <?php echo $profile_completion_rate; ?>%;"><?php echo $profile_completion_rate; ?> %</div>
                                    </div>
                                </div>
                            </div>
                            <div class="dashboard-item">
                                <strong>Total Bids:</strong>
                                <span><?php echo $total_bids; ?></span>
                            </div>
                            <div class="dashboard-item">
                                <strong>Total Accepted Bids:</strong>
                                <span><?php echo $total_accepted_bids; ?></span>
                            </div>
                            <div class="dashboard-item">
                                <strong>Total Earned:</strong>
                                <span><?php echo $total_earned; ?> $</span>
                            </div>
                        </div>
                    </div>
                <?php elseif ( ae_user_role( $author_id ) != FREELANCER ): ?>
                    <div class="dashboard-container">
                        <t2>Client Statistics</t2>
                        <div class="dashboard-data">
                            <div class="dashboard-item">
                                <strong>Total Projects:</strong>
                                <span><?php echo $total_projects; ?></span>
                            </div>
                            <div class="dashboard-item">
                                <strong>Current Active Projects:</strong>
                                <span><?php echo $current_active_projects; ?></span>
                            </div>
                            <div class="dashboard-item">
                                <strong>Hired Consultants:</strong>
                                <span><?php echo $total_hired_consultant; ?></span>
                            </div>
                            <div class="dashboard-item">
                                <strong>Total Spent:</strong>
                                <span><?php echo $total_spent; ?> $</span>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
                <!-- Quick Links -->
                <div class="dashboard-container">
                    <t2>Quick Links</t2>
                    <div class="dashboard-data">
                        <ul style="padding-left: 20px !important">
                           <li><a href="<?php echo home_url( '/profile' ); ?>">My Profile</a></li>
                            <li><a href="<?php echo home_url( '/private-message' ); ?>">Private Messages</a></li>
                            <li><a href="<?php echo home_url( '/my-credit' ); ?>">My Credit</a></li>
                            <?php if ( ae_user_role( $author_id ) == FREELANCER ): ?>
                                <li><a href="<?php echo home_url( '/my-project' ); ?>">My Worked Projects</a></li>
                                <li><a href="<?php echo home_url( '/projects' ); ?>">Find Projects</a></li>
                            <?php elseif ( ae_user_role( $author_id ) != FREELANCER ): ?>
                                <li><a href="<?php echo home_url( '/my-project' ); ?>">My Posted Projects</a></li>
                                <li><a href="<?php echo home_url( '/submit-project' ); ?>">Post a Project</a></li>
                                <li><a href="<?php echo home_url( '/profiles' ); ?>">Find Consultant</a></li>
                            <?php endif; ?>
                      
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Footer -->
<?php
get_footer();

// Functions to fetch data
function calculate_profile_completion_rate( $profile ) {
    $comp_rate = 0;

    //get profile skills
    $current_skills = get_the_terms( $profile, 'skill' );
    //define variables:
    $skills         = isset( $profile->tax_input['skill'] ) ? $profile->tax_input['skill'] : array();
    $job_title      = isset( $profile->et_professional_title ) ? $profile->et_professional_title : '';
    $display_name   = $user_data->display_name;
    $experience     = isset( $profile->et_experience ) ? $profile->et_experience : '';
    $hour_rate      = isset( $profile->hour_rate ) ? $profile->hour_rate : '';
    $about          = isset( $profile->post_content ) ? $profile->post_content : '';
    $country        = isset( $profile->tax_input['country'][0] ) ? $profile->tax_input['country'][0]->name : '';
    $category       = isset( $profile->tax_input['project_category'][0] ) ? $profile->tax_input['project_category'][0]->slug : '';

    if ( $job_title != '' ) {
        $comp_rate += 10;
    }
    if ( count($skills) > 0 ) {
        $comp_rate += 20;
    }
    if ( $experience != '' ) {
        $comp_rate += 20;
    }
    if ( $hour_rate != '' ) {
        $comp_rate += 10;
    }
    if ( $about != '' ) {
        $comp_rate += 10;
    }
    if ( $country != '' ) {
        $comp_rate += 10;
    }
    if ( $category != '' ) {
        $comp_rate += 20;
    }
    
    return $comp_rate;
}

function get_total_bids( $consultant_id, $post_type = 'bid' ) {
	global $wpdb;
	$count_bid = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM  $wpdb->posts
        WHERE post_type =%s
            and (post_status = 'publish'
            or post_status = 'complete'
            or post_status = 'accept'
            or post_status = 'unaccept')
            and post_author = %d", $post_type, $consultant_id ) );

	return (int) $count_bid;
}
?>


<!-- Dashboard Style -->
<style>
    body {
        font-family: 'Open Sans', sans-serif;
        background-color: #f7f9fc;
        color: #333;
        margin: 0;
        padding: 0;
    }

    .dashboard-container {
        max-width: 800px;
        /* width: 85%; */
        padding: 20px;
        background-color: #ffffff;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    }

    /* Header Style */
    t2 {
        color: #34495e;
        font-size: 24px;
        /* font-weight: 600; */
        border-bottom: 2px solid #3498db;
        padding-bottom: 10px;
        margin-bottom: 40px; !important
    }

    li {
        list-style: none;
        padding-bottom: 10px;
    }

    /* Paragraph Styles */
    .dashboard-data {
        display: flex;
        flex-wrap: wrap;
        gap: 20px;
        margin-bottom: 20px;
        margin-top: 30px;
        /* margin-left: 30px; */
    }

    .dashboard-item {
        flex: 1 1 calc(50% - 20px);
        background-color: #3498db;
        color: #ffffff;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 1px 5px rgba(0, 0, 0, 0.1);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .dashboard-item:hover {
        transform: translateY(-5px);
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
    }

    .dashboard-item strong {
        font-size: 16px;
        display: block;
        margin-bottom: 8px;
    }

    .dashboard-item span {
        font-size: 24px;
        font-weight: 700;
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .dashboard-item {
            flex: 1 1 100%;
        }
    }

    @media (max-width: 1024px) {
        .dashboard-item {
            flex: 1 1 calc(50% - 20px);
        }
    }

    .rate-container {
    font-family: Arial, sans-serif;
    font-size: 18px;
    color: #fff;
    /* width: 100px; */
    margin: 20px 0;
    }

    .progress-bar {
        background-color: #e0e0e0;
        border-radius: 5px;
        overflow: hidden;
        height: 20px;
        width: 100%;
        position: relative;
        font-size: 18px !important;
    }

    .progress {
        background-color: #4caf50;
        height: 100%;
        border-radius: 5px;
        text-align: center;
        line-height: 20px;
        white-space: nowrap;
        overflow: hidden;
        padding: 0 2px;
        font-weight: bold;
    }
</style>