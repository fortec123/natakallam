<?php

/**
 * Plugin Name: Amalia Student Pre-approval Extension
 * Plugin URI: https://natakallam.com/
 * Description: Once approved allow the selection of only specific employees
 * Author: NaTakallam
 * Version: 1.0.0
 * Author URI: https://natakallam.com/
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

$result_count = count($result_2)-1;

$provider = $result_2[$result_count]->providerId;

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
			
			var everythingLoaded = setInterval(function() {
			if(jQuery('.am-select-any-employee-option')[0] && flag_append == 0) {
				
				
				var i = 0;
				
				 jQuery('.am-select-any-employee-option').parent().addClass('custom_list');
				jQuery('.custom_list li').each(function() {
					var old_emp = jQuery(this).html();
					var new_emp = '<span><?php echo $provider_name; ?></span>';
					
					//console.log(old_emp);
					
					if(i == 0) {
					jQuery(this).html('<span>Any employee</span>');
					} 
					else if(old_emp == new_emp) {
					jQuery(this).html(new_emp);
					} else {
					jQuery(this).hide();
					}
					
					i++;
				}); 
				
				jQuery('.am-select-employee-option').append('<a onclick="return false;" class="view_button_emp" type="button" style="font-size: 12px;cursor: pointer;float: right;">View All</a>');
				
				
				flag_append = 1;
				clearInterval(everythingLoaded);
				
				
			} 
			
			}, 10);
				
		});	
		
		jQuery(document).on('click','.view_button_emp',function() {
				jQuery('.custom_list li').each(function() {
					
					jQuery(this).show();
					
				}); 
		});
		

	</script>
	<?php 
		} //endif
	} //endif
} //endif
}
}

add_action( 'wp_footer', 'filter_employees' );



function check_bookings() {	

	global $wpdb;
$prefix = $wpdb->prefix;

$appointment_table = $prefix."amelia_appointments";
$customer_booking_table = $prefix."amelia_customer_bookings"; // appointmentId customerId
$amelia_payments = $prefix."amelia_payments";
$amelia_users_table = $prefix."amelia_users";
$users_table = $prefix."users";

if(is_user_logged_in()) {
	
	$user_id = get_current_user_id();
	
$query_1 = "Select * from ".$users_table." where id = ".$user_id;
$result_1 = $wpdb->get_results($query_1);

$current_user_email =  $result_1[0]->user_email;

$query_approve = "Select ".$appointment_table.".serviceId, ".$appointment_table.".providerId, ".$appointment_table.".id from ".$appointment_table." 
			left join ".$customer_booking_table." on ".$customer_booking_table.".appointmentId = ".$appointment_table.".id
			left join ".$amelia_users_table." on ".$customer_booking_table.".customerId = ".$amelia_users_table.".id
			left join ".$amelia_payments." on ".$customer_booking_table.".appointmentId = ".$amelia_payments.".customerBookingId
			where ".$amelia_users_table.".externalId = '".$user_id."' AND ".$appointment_table.".status = 'approved' AND ".$amelia_payments.".status = 'paid'";

$result_approve = $wpdb->get_results($query_approve);

$query_pending = "Select ".$appointment_table.".serviceId, ".$appointment_table.".providerId, ".$appointment_table.".id from ".$appointment_table." 
			left join ".$customer_booking_table." on ".$customer_booking_table.".appointmentId = ".$appointment_table.".id
			left join ".$amelia_users_table." on ".$customer_booking_table.".customerId = ".$amelia_users_table.".id
			left join ".$amelia_payments." on ".$customer_booking_table.".appointmentId = ".$amelia_payments.".customerBookingId
			where ".$amelia_users_table.".externalId = '".$user_id."' AND ".$appointment_table.".status = 'pending' AND ".$amelia_payments.".status = 'paid'";

$result_pending = $wpdb->get_results($query_pending);



if(count($result_approve) == 0) {
	

$query_approve = "Select ".$appointment_table.".serviceId, ".$appointment_table.".providerId, ".$appointment_table.".id from ".$appointment_table." 
			left join ".$customer_booking_table." on ".$customer_booking_table.".appointmentId = ".$appointment_table.".id
			left join ".$amelia_users_table." on ".$customer_booking_table.".customerId = ".$amelia_users_table.".id
			left join ".$amelia_payments." on ".$customer_booking_table.".appointmentId = ".$amelia_payments.".customerBookingId
			where ".$amelia_users_table.".email LIKE '".$current_user_email."' AND ".$appointment_table.".status = 'approved' AND ".$amelia_payments.".status = 'paid'";
			
$result_approve = $wpdb->get_results($query_approve);


$query_pending = "Select ".$appointment_table.".serviceId, ".$appointment_table.".providerId, ".$appointment_table.".id from ".$appointment_table." 
			left join ".$customer_booking_table." on ".$customer_booking_table.".appointmentId = ".$appointment_table.".id
			left join ".$amelia_users_table." on ".$customer_booking_table.".customerId = ".$amelia_users_table.".id
			left join ".$amelia_payments." on ".$customer_booking_table.".appointmentId = ".$amelia_payments.".customerBookingId
			where ".$amelia_users_table.".email LIKE '".$current_user_email."' AND ".$appointment_table.".status = 'pending' AND ".$amelia_payments.".status = 'paid'";
			
			//(".$appointment_table.".status = 'approved' OR ".$appointment_table.".status = 'pending')";
$result_pending = $wpdb->get_results($query_pending);

}

$result_count = count($result_pending)-1;
$provider = $result_pending[$result_count]->providerId;
$serviceId = $result_pending[$result_count]->serviceId;
$appointment_id = $result_pending[$result_count]->id;

foreach($result_approve as $result_appro ){
	
	if($serviceId == $result_appro->serviceId && $provider == $result_appro->providerId ) {
		$previous_CP[] = $result_appro->providerId;
		$previous_service_id[] = $result_appro->serviceId;
	}
	
}


	if(!empty($previous_CP) && !empty($previous_service_id)) {
	 if(in_array($provider,$previous_CP) && in_array($serviceId,$previous_service_id)){
		
		$query_update = "Update ".$appointment_table." SET status='approved' where id=".$appointment_id."";
		$result_update = $wpdb->query($query_update);
		
		
		?>
		
		<script>
		jQuery(document).ready(function () {
			jQuery.ajax({
				type: 'post',
				data: {status:"approved"},
				url: "<?php echo get_site_url(); ?>/wp-admin/admin-ajax.php?action=wpamelia_api&call=/appointments/status/<?php echo $appointment_id; ?>", 

				success: function(result){
				//console.log(result);	
				}
			});

		});
		</script>
		
		<?php
		
	 }
	}

}
}


add_action( 'wp_footer', 'check_bookings' );

function test(){
?>

<script>



</script>

<?php
}

add_action( 'wp_footer', 'test' );

?>

