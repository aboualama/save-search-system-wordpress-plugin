<?php 
    /*
    Plugin Name: Custom Search Plugins
    Plugin URI: http://www.aboualama.com
    Description: Plugin For Search System
    Author: Mohamed Aboualama
    Version: 1.0
    Author URI: http://www.aboualama.com
    */
 

register_activation_hook( __FILE__ , "SearchSystem"); 
function SearchSystem() {
	global $wpdb; 
	$charset_collate = $wpdb->get_charset_collate();
	$table_name = $wpdb->prefix . "save_search_table";
	$sql = "CREATE TABLE `$table_name` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
	`userId` varchar(220) NULL,
	`deviceId` text,
	`searchWord` text,  
	`active` boolean,  
	PRIMARY KEY(id)
	) ENGINE=MyISAM DEFAULT CHARSET=utf8;
	";
	if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
		require_once(ABSPATH . "wp-admin/includes/upgrade.php");
		dbDelta($sql);
	}
}   


function Search_System($request) { 
	global $wpdb;
	$table = $wpdb->prefix.'save_search_table';  
	$id = $request['id'];  
	$data = [ 
			'userId'   => $request['user_id'],
			'deviceId' => $request['device_id'],
			'searchWord' => $request['keyword'],
			'active'   => $request['status'], 
			];   
	if(!empty($id)){ 
		$wpdb->update($table , $data , ['id' => $id]);  
	}else{ 
		$wpdb->insert($table , $data);  
	}
	return rest_ensure_response( $data );  
} 
add_action('rest_api_init', function() {
	register_rest_route('wp/v3/', 'save_search', [
		'methods' =>  'POST', 
		'callback' => 'Search_System',
	]); 
});
  

  
function getAllKeyword($request) {
	global $wpdb;     
	$table_name = $wpdb->prefix . "save_search_table";
 
	$device_id =  $request['deviceId']; 
	$keywords = [];  
	$results = $wpdb->get_results ("SELECT * FROM $table_name WHere (deviceId = '". $device_id ."')");  
	foreach($results as $result)
	{ 
		$keywords[] = $result;
	} 
	return $keywords;
} 
add_action('rest_api_init', function() {
	register_rest_route('wp/v3/', 'get_keywords', [
		'methods' =>  'POST', 
		'callback' => 'getAllKeyword',
	]); 
});

 
function removeKeyword($request) { 
	global $wpdb;     
	$table_name = $wpdb->prefix . "save_search_table";  
	$Id = $request['id'];  
    $wpdb->get_results ("DELETE FROM $table_name WHere (id = '". $Id ."')"); 
 
} 
add_action('rest_api_init', function() {
	register_rest_route('wp/v3/', 'remove_keywords', [
		'methods' =>  'POST', 
		'callback' => 'removeKeyword',
	]); 
});

 
 
function publish_product($new_status, $old_status, $post) {
	global $wpdb;     
	$table_name = $wpdb->prefix . "save_search_table";   

	if($old_status != 'publish' && $new_status == 'publish' && !empty($post->ID) && in_array( $post->post_type , ['product'] ) ) 
		{ 
			$devices = [];   
			$results = $wpdb->get_results($wpdb->prepare("SELECT * FROM $table_name WHERE searchWord LIKE '%%$post->title%%'"));
			
			foreach($results as $result)
				{ 
					$devices[] = $result->deviceId;
				}  
		} 
 }
 add_action('transition_post_status', 'publish_product', 10, 3);









