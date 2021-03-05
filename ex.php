 

function getByKeyword($request) {
	global $wpdb;     
	$table_name = $wpdb->prefix . "save_search_table";

	$keyword = $request['keyword']; 
	$devices = [];   
	$results = $wpdb->get_results("SELECT * FROM $table_name WHere (searchWord = '". $keyword ."')");  
	foreach($results as $result)
	{ 
		$devices[] = $result->userId;
	} 
	return $devices;
} 
add_action('rest_api_init', function() {
	register_rest_route('wp/v3/', 'getkeyword', [
		'methods' =>  'GET', 
		'callback' => 'getByKeyword',
	]); 
});









function test($request) {
	  
      echo 'test';
  }
  
  add_action('rest_api_init', function() {
      register_rest_route('wp/v3', '/test/', [
          'methods' =>  'GET', 
          'callback' => 'test',
      ]); 
  });
   