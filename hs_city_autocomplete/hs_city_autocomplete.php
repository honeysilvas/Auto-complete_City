<?php
/*
	Plugin Name: City Autocomplete
	Description: Autocompletes user input based on most populous city.
	Version:  0.0.1
	Author:  Honey Silvas
*/

defined( "ABSPATH" ) or die( "Not allowed!" );		

function hs_city_autocomplete_enqueue_script() {
	wp_enqueue_style( "city_jqueryui_style",  plugins_url( "/hs_city_autocomplete/source/asset/vendor/jquery-ui/jquery-ui-themes-1.11.4/themes/smoothness/jquery-ui.min.css" ), false ); 
	wp_enqueue_script( "city_jqueryui_js", plugins_url( "/hs_city_autocomplete/source/asset/vendor/jquery-ui/jquery-ui-1.11.4/jquery-ui.min.js" ), array( "jquery" ) );
}

function hs_city_autocomplete_main() {
	if ( strpos( $_SERVER[ "REQUEST_URI" ], "location-search" ) != 0 ){
		require_once( "/source/index.php" );
	}
}

function hs_city_autocomplete_rewrite(){
	add_rewrite_rule( "^cities-json", "index.php", "top" );
	add_rewrite_rule( "^location-search", "index.php", "top" );
}

function hs_city_json_feed() {
	if ( strpos( $_SERVER[ "REQUEST_URI" ], "cities-json" ) != 0 ){
		global $wpdb; 
				
		$sql = "select location, slug, population
			from population";
		
		if ( get_query_var( "term" ) ) {
			$sql .= " where location like '" .  sanitize_text_field( get_query_var( "term" ) )  . "%'";	
		}
		
		$sql .= " order by population desc
			limit 10;";

		$result = $wpdb->get_results( $sql, OBJECT );
		
		foreach ( $result as $value ) {
			$row[] = array( 
				"label" => $value->location,
				"slug" => $value->slug,
				"population" => $value->population,
			);
		}
		
		if ( !empty( $row ) ){
			$data = json_encode( $row, true );
		}

		echo $data;		
		exit;
		wp_die(); 
	}
}

function hs_city_autocomplete_activate(){
	global $wpdb;

	$charset_collate = $wpdb->get_charset_collate();
	$table_name = $wpdb->prefix . "population";
	
	$package_table = 'population';
        $sql = "CREATE TABLE " . $package_table . " (
            id INT NOT NULL AUTO_INCREMENT, 
            location TEXT NOT NULL, 
            slug TEXT NOT NULL, 
            population INT NOT NULL, 
            PRIMARY KEY  (id)
        ) ". $charset_collate .";";
			
	$wpdb->query($sql);	
}

add_action( "wp", "hs_city_json_feed" );
add_action( "init", "hs_city_autocomplete_rewrite" );
add_action( "wp_enqueue_scripts", "hs_city_autocomplete_enqueue_script" );
add_action( "the_content", "hs_city_autocomplete_main" );
register_activation_hook( __FILE__, "hs_city_autocomplete_activate" );