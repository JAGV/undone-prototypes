<?php 
include_once( 'inc/wp.php' );
$playlist_id = null;

if ( isset( $_GET['playlist_id'] ) ){

	$playlist_id = intval( $_GET['playlist_id'] );
}

if ( ! function_exists( 'wolf_jplayer_playlist_wp_title' ) ):
    function wolf_jplayer_playlist_wp_title( $title ) {
    	global $wpdb, $playlist_id;
	$wolf_jplayer_playlists_table = $wpdb->prefix.'wolf_jplayer_playlists';
	$playlist = $wpdb->get_row( "SELECT * FROM $wolf_jplayer_playlists_table WHERE id = '$playlist_id'" );
             
	if ($playlist)
             	return $playlist->name.' | '.get_bloginfo( 'name' );
             else
             	return get_bloginfo( 'name');
       
    }

add_filter( 'wp_title', 'wolf_jplayer_playlist_wp_title' );
endif;

get_header();
	?><div id="wolf-jplayer-single-page"><?php
	if ( function_exists( 'wolf_jplayer_playlist_show' ) && $playlist_id )
		echo wolf_jplayer_playlist_show( $playlist_id, false );
	?></div><!-- #main-content .wrap --><?php
get_footer(); 
?>