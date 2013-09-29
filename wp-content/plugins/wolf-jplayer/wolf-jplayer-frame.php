<?php 
include_once( 'inc/wp.php' );

$id = null;

if( isset( $_GET['playlist_id'] ) ) {

	$id = intval( $_GET['playlist_id'] );
}

global $wpdb, $options;
$wolf_jplayer_playlists_table = $wpdb->prefix.'wolf_jplayer_playlists';
$playlist = $wpdb->get_row("SELECT * FROM $wolf_jplayer_playlists_table WHERE id = '$id'");

$iframe = false;
$in_popup = true;


if ( $playlist)
	$page_title = $playlist->name.' | '.get_bloginfo( 'name' );
else
	$page_title = get_bloginfo( 'name' );

if ( isset( $_GET['iframe'] ) && $_GET['iframe'] == true ) {
	$iframe = true;
	$in_popup = false;
}

$color = '#353535';
$font_color = '#ffffff';
$settings = get_option( 'wolf_jplayer_settings' );
if( $settings ) {
	$color = $settings['bg_color'];
	$font_color = $settings['font_color'];
}
	
?>
<!DOCTYPE html> 
<html <?php language_attributes(); ?>>
<head>
	<title><?php echo $page_title; ?></title>
	<link rel="stylesheet" href="<?php echo WOLF_JPLAYER_PLUGIN_URL . '/css/reset.css'; ?>">
	<link rel="stylesheet" href="<?php echo WOLF_JPLAYER_PLUGIN_URL . '/css/jplayer.css'; ?>">
	
	<style type="text/css">
		<?php if ( $iframe ): ?>
			body {background:none}
		<?php endif; ?>
		<?php if ( $in_popup ): ?>
		html { background: <?php echo $color ? $color : '#353535'; ?>;}
		body{
			background: <?php echo $color ? $color : '#353535'; ?>;
			height:auto!important;
		      	overflow-x:hidden!important;overflow-y:hidden!important; 
		}
		.jp-repeat, .jp-repeat-off{
			right:34px!important;
		}

		.jp-shuffle, .jp-shuffle-off{
			right:10px!important;

		}
		a.wolf-jp-popup { display:none!important }
		<?php endif; ?>

		.wolf-jplayer-playlist, .wolf-jplayer-playlist a{
			color: <?php echo $font_color ? $font_color : '#ffffff'; ?>;
		}

		.wolf-jplayer-playlist div.jp-type-playlist{
			background-color: <?php echo $color ? $color : '#353535'; ?>;
		}

		.wolf-jplayer-playlist .jp-play-bar, .wolf-jplayer-playlist .jp-volume-bar-value{
			background-color: <?php echo $font_color ? $font_color : '#ffffff'; ?>;
		}

	</style>
	<script type='text/javascript' src="<?php echo includes_url('js/jquery/jquery.js'); ?>"></script>
	<script type='text/javascript' src="<?php echo WOLF_JPLAYER_PLUGIN_URL.'/js/jquery.jplayer.min.js'; ?>"></script>
	<script type='text/javascript' src="<?php echo WOLF_JPLAYER_PLUGIN_URL.'/js/jplayer.playlist.min.js'; ?>"></script>
	<script type='text/javascript' src="<?php echo WOLF_JPLAYER_PLUGIN_URL.'/js/jquery.jplayer.custom.js'; ?>"></script>
	<script type="text/javascript">
		/* <![CDATA[ */
		var WolfJplayer = { "iTunesText":"Buy on iTunes", "amazonText":"Buy on amazon", "buyNowText":"Buy now", "downloadText":"Right click and save link to download the mp3" };
		/* ]]> */
            		jQuery( function( $ ) {

            			$( '.wolf-jplayer-playlist' ).find( 'span.close-wolf-jp-share' ).click( function() {
            				$( this ).parent().parent().fadeOut();
            			} );

            			$( '.wolf-jp-share-icon').click( function() {
            				var container = $( this ).parent().parent().parent();
            				container.find('.wolf-jp-overlay').fadeIn();
            			} );

            			jQuery('.wolf-share-jp-popup').click(function() {
		 		var url = jQuery( this ).attr('href');
				var popup = window.open( url, 'null', 'height=350,width=570, top=150, left=150');
				if ( window.focus ) {
					popup.focus();
				}
				return false; 
			} );
            		} );
            	</script>
	<!-- HTML5 and media queries Fix for IE --> 
	<!--[if IE]>
		<script src="<?php echo WOLF_JPLAYER_PLUGIN_URL; ?>/js/html5.js"></script>
	<![endif]-->
	<!-- End Fix for IE --> 
</head>
<body>
<div id="main">
	<?php 
		if ( function_exists( 'wolf_jplayer_playlist_show' ) )
			echo wolf_jplayer_playlist_show( $id, $in_popup );
	?>
</div>
</body>
</html>