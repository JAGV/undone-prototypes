<?php
global $wpdb, $options; 

$wolf_jplayer_table = $wpdb->prefix.'wolf_jplayer';
$wolf_jplayer_settings = get_option( 'wolf_jplayer_settings' );

include_once( WOLF_JPLAYER_PLUGIN_DIR . '/inc/functions.php' );
include_once( WOLF_JPLAYER_PLUGIN_DIR . '/inc/playlist-functions.php' );
include_once( WOLF_JPLAYER_PLUGIN_DIR . '/inc/song-functions.php' );

?>
<div class="wrap">
	<div id="icon-options-general" class="icon32"></div>
	<h2>jPlayer</h2>
<?php 
if ( !isset( $_GET['playlist_id'] ) ){
	/**
	* Playlists
	*/
	include_once( WOLF_JPLAYER_PLUGIN_DIR . '/wolf-jplayer-playlists.php' ); 

}elseif( isset( $_GET['playlist_id'] ) ){

	/**
	* Songs
	*/
	include_once( WOLF_JPLAYER_PLUGIN_DIR . '/wolf-jplayer-songs.php' ); 

}
?>
</div><!--  end .wrap -->
