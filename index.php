<?php

/**
 * Plugin Name: specific employee select - Amelia
 * Plugin URI: https://fortecsolution.com/
 * Description: specific employee select - Amelia
 * Author: Fortec Solutions
 * Version: 1.0.0
 * Author URI: https://fortecsolution.com/
*/

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}


function filter_employees() {
	global $wpdb;
$prefix = $wpdb->prefix;

$appointment_table = $prefix."amelia_appointments";
$customer_booking_table = $prefix."amelia_customer_bookings"; // appointmentId customerId
$amelia_users_table = $prefix."amelia_users";
$users_table = $prefix."users";

if(is_user_logged_in()) {
	
	$user_id = get_current_user_id();
	
$query_1 = "Select * from ".$users_table." where id = ".$user_id;
$result_1 = $wpdb->get_results($query_1);

$current_user_email =  $result_1[0]->user_email;

$query_2 = "Select ".$appointment_table.".providerId from ".$appointment_table." 
			left join ".$customer_booking_table." on ".$customer_booking_table.".appointmentId = ".$appointment_table.".id
			left join ".$amelia_users_table." on ".$customer_booking_table.".customerId = ".$amelia_users_table.".id
			where ".$amelia_users_table.".externalId = '".$user_id."' AND ".$appointment_table.".status = 'approved'";

$result_2 = $wpdb->get_results($query_2);

if(count($result_2) == 0) {
$query_2 = "Select ".$appointment_table.".providerId from ".$appointment_table." 
			left join ".$customer_booking_table." on ".$customer_booking_table.".appointmentId = ".$appointment_table.".id
			left join ".$amelia_users_table." on ".$customer_booking_table.".customerId = ".$amelia_users_table.".id
			where ".$amelia_users_table.".email LIKE '".$current_user_email."' AND ".$appointment_table.".status = 'approved'";
$result_2 = $wpdb->get_results($query_2);
}


$provider = $result_2[0]->providerId;

if(count($result_2)) {
$query_3 = "Select * from ".$amelia_users_table."
			where id = '".$provider."'";
$result_3 = $wpdb->get_results($query_3);

	if(count($result_3)) {
	$provider_name = $result_3[0]->firstName." ".$result_3[0]->lastName;

		if(!empty($provider_name)) {
	?>
	
	<script>
	var flag_append = 0;
	jQuery(document).ready(function(){		
			//setTimeout(function() { 
			var everythingLoaded = setInterval(function() {
			if(jQuery('.am-select-any-employee-option')[0] && flag_append == 0) {
				console.log("here");
				
				var i = 0;
				
				 jQuery('.am-select-any-employee-option').parent().addClass('custom_list');
				jQuery('.custom_list li').each(function() {
					var old_emp = jQuery(this).html();
					var new_emp = '<span><?php echo $provider_name; ?></span>';
					
					console.log(old_emp);
					
					if(i == 0) {
					jQuery(this).html('<span>Any employee</span>');
					} 
					else if(old_emp == new_emp) {
					jQuery(this).html(new_emp);
					} else {
					jQuery(this).remove();
					}
					
					i++;
				}); 
				
				flag_append = 1;
				clearInterval(everythingLoaded);
				
				/* var content = '<li class="el-select-dropdown__item am-select-any-employee-option selected"><span>Any employee</span></li><li class="el-select-dropdown__item"><span>Test1 User</span></li>';
				jQuery('.custom_list').parent().append(content); */
				
			} else {
				console.log("else");
			}
			
			//}, 3000);
			}, 10);
				
		});	

	</script>
	<?php 
		} //endif
	} //endif
} //endif
}
}

add_action( 'wp_footer', 'filter_employees' );

?>