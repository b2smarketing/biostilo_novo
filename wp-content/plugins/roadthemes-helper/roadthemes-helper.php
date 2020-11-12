<?php
/**
 * Plugin Name: RoadThemes Helper
 * Plugin URI: http://roadthemes.com/
 * Description: The helper plugin for RoadThemes themes.
 * Version: 1.0.0
 * Author: RoadThemes
 * Author URI: http://roadthemes.com/
 * Text Domain: flaton
 * License: GPL/GNU.
 /*  Copyright 2015  RoadThemes  (email : roadthemez@gmail.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

//Add less compiler
function compileLessFile($input, $output, $params) {
   require_once( plugin_dir_path( __FILE__ ).'less/lessc.inc.php' );
   
	$less = new lessc;
	$less->setVariables($params);
	
    // input and output location
    $inputFile = get_template_directory().'/less/'.$input;
    $outputFile = get_template_directory().'/css/'.$output;

    try {
		$less->compileFile($inputFile, $outputFile);
	} catch (Exception $ex) {
		echo "lessphp fatal error: ".$ex->getMessage();
	}
}
function compileChildLessFile($input, $output, $params) {
	require_once( plugin_dir_path( __FILE__ ).'less/lessc.inc.php' );
	$less = new lessc;
	$less->setVariables($params);
	
    // input and output location
    $inputFile = get_stylesheet_directory().'/less/'.$input;
    $outputFile = get_stylesheet_directory().'/css/'.$output;

    try {
		$less->compileFile($inputFile, $outputFile);
	} catch (Exception $ex) {
		echo "lessphp fatal error: ".$ex->getMessage();
	}
}

//Shortcodes
add_shortcode( 'ourbrands', 'oxelar_brands_shortcode' );
add_shortcode( 'popular_categories', 'oxelar_popular_categories_shortcode' );
add_shortcode( 'categoriescarousel', 'oxelar_categoriescarousel_shortcode' );
add_shortcode( 'latestposts', 'oxelar_latestposts_shortcode' );
add_shortcode( 'oxelar_map', 'oxelar_contact_map' );
add_shortcode( 'roadlogo', 'oxelar_logo_shortcode' );
add_shortcode( 'roadmainmenu', 'oxelar_mainmenu_shortcode' );
add_shortcode( 'roadcategoriesmenu', 'oxelar_roadcategoriesmenu_shortcode' );
add_shortcode( 'roadlangswitch', 'oxelar_roadlangswitch_shortcode' );
add_shortcode( 'roadsocialicons', 'oxelar_roadsocialicons_shortcode' );
add_shortcode( 'roadminicart', 'oxelar_roadminicart_shortcode' );
add_shortcode( 'roadproductssearch', 'oxelar_roadproductssearch_shortcode' );
add_shortcode( 'roadcopyright', 'oxelar_roadcopyright_shortcode' );
add_shortcode( 'ourbrands', 'oxelar_brands_shortcode' );
add_shortcode( 'popular_categories', 'oxelar_popular_categories_shortcode' );
add_shortcode( 'categoriescarousel', 'oxelar_categoriescarousel_shortcode' );
add_shortcode( 'latestposts', 'oxelar_latestposts_shortcode' );
add_shortcode( 'oxelar_map', 'oxelar_contact_map' );