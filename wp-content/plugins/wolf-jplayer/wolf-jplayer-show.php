<?php
class Wolf_Jplayer_Show{
            
            function __construct() {
            		add_action( 'wp_print_styles', array( &$this, 'print_style' ) );
            		add_action( 'wp_enqueue_scripts', array( &$this, 'enqueue_script' ) );
            }

            // --------------------------------------------------------------------------

            function print_style() {

		wp_register_style( 'jplayer-playlist', WOLF_JPLAYER_PLUGIN_URL.'/css/jplayer.css', array(), '2.0.2', 'all' );

            }

            // --------------------------------------------------------------------------

            function enqueue_script() {
          	  	if (  !wp_script_is( 'jquery' ) )
    			wp_enqueue_script( 'jquery' );
            		
            		wp_register_script( 'jplayer', WOLF_JPLAYER_PLUGIN_URL.'/js/jquery.jplayer.min.js', 'jquery', '2.4.0', false );
            		wp_register_script( 'jplayer-playlist', WOLF_JPLAYER_PLUGIN_URL.'/js/jplayer.playlist.min.js', 'jquery', '2.3.0', false );
            		wp_register_script( 'wolf-jplayer-custom', WOLF_JPLAYER_PLUGIN_URL.'/js/jquery.jplayer.custom.js', 'jquery', '2.0.2', false );
            		wp_localize_script( 'wolf-jplayer-custom', 'WolfJplayer', array( 
            			'iTunesText' => __( 'Buy on iTunes', 'wolf' ), 
            			'amazonText' => __( 'Buy on amazon', 'wolf' ),
            			'buyNowText' => __( 'Buy now', 'wolf' ),
            			'downloadText' => __( 'Right click and save link to download the mp3', 'wolf' ),
            			) 
            		);
            }


	// --------------------------------------------------------------------------

            function popup() {
            		
		$popup = 'jQuery(".wolf-jp-popup").click(function() {
				Player = $(this).parent().prev();
				Player.jPlayer("stop");
		 		var url = jQuery(this).attr("href");
		 		var popupHeight = jQuery(this).parents(".wolf-jplayer-playlist").height();
				var popup = window.open(url,"null", "height=" + popupHeight + ",width=570, top=150, left=150");
				if (window.focus) {
					popup.focus();
				}
				return false; 
		});';

		return $popup;
            }

            // --------------------------------------------------------------------------

            function head_script( $id, $playlist_id, $songs, $in_popup, $autoplay = null ) {
		$output = '';
	    	$playlist = '';
	    	$artist = '';
	    	$free = 'on';
	    	$poster = '';
	    	$external = 0;

	    	if ( $songs) {
	    		$ogg = '';
	    		$poster = '';

	    		foreach ( $songs as $song ) {

	    			$free = $song->free;

	    			$playlist .= '{ mp3:"'. $song->mp3 .'", title : "' . $song->name . '"';

	    			if ( $song->artist )
                                			$playlist .= ',artist : "' . $song->artist . '" ';

				
                                		if ( $free !='on' ) {
					if ( $song->itunes )
	                                			$playlist .= ',itunes : "' . $song->itunes . '" ';

	                                		if ( $song->amazon )
	                                			$playlist .= ',amazon : "' . $song->amazon . '" ';

	                                		if ( $song->buy )
	                                			$playlist .= ',buy : "' . $song->buy . '" ';
                               
	                                	}

	                                	else {
	                                		$playlist .= ',download : "' . $song->mp3 . '" ';
	                                	}

                                		if ( $song->poster)
                                			$playlist .= ',poster : "' . $song->poster . '" ';
                                		else
                                			$playlist .= ',poster : "' . WOLF_JPLAYER_PLUGIN_URL . '/img/default_poster.png'.'" ';
	    			
	    			
	    			$playlist .= ' },';

	    		}

	    	$playlist = substr( $playlist, 0, -1 );

	    	$output .= '<script type="text/javascript">
		//<![CDATA[';

		$output .= "\n";
		$output .= 'jQuery(document).ready(function($) {
				new jPlayerPlaylist({
					jPlayer: "#jquery_jplayer_' . $id . '",
					cssSelectorAncestor: "#jp_container_' . $id . '" }, 
					['.$playlist.'], {
					swfPath: "'.WOLF_JPLAYER_PLUGIN_URL.'/js",
					wmode: "window", ';
				
				$output .= 'supplied: "mp3"';		

				$output .= ', solution:"flash, html"';

				if (  $autoplay && $autoplay == 'on' ) {
					$output .= ', 
					playlistOptions: { autoPlay : true }';
				}

		$output .= '});'; // end playlist
		$output .= "\n";

                       	/* Popup
		----------------------------------------*/
		if ( !$in_popup )
			$output .= $this->popup();

		$output .= '});'; // end document ready playlist

		$output .= "\n";
		$output .= '//]]>
		</script>';

	    	}

	    	echo $output;
            }
     
            // --------------------------------------------------------------------------

   	/* Output jPlayer */
    	function jplayer_playlist_show( $playlist_id, $in_popup ) {
        		global $wpdb, $options;

        		wp_enqueue_style( 'jplayer-playlist' );
        		wp_enqueue_script( 'jplayer' );
            		wp_enqueue_script( 'jplayer-playlist' );
            		wp_enqueue_script( 'wolf-jplayer-custom' );

		$wolf_jplayer_playlists_table = $wpdb->prefix.'wolf_jplayer_playlists';
		$wolf_jplayer_table = $wpdb->prefix.'wolf_jplayer';
	    	$playlist = $wpdb->get_row( "SELECT * FROM $wolf_jplayer_playlists_table WHERE id = '$playlist_id'" );
	    	$songs = $wpdb->get_results( "SELECT * FROM $wolf_jplayer_table WHERE playlist_id = '$playlist_id' ORDER BY position" );

	    	$autoplay = null;

	    	if ( $playlist)
			$share_title = $playlist->name.' | '.get_bloginfo( 'name' );
		else
			$share_title = get_bloginfo( 'name' );

	    	$id = $playlist_id.rand( 1,999 );

	if ( $playlist && $songs):
	
	$autoplay = $playlist->autoplay;

	$player_height = 170 + 35*count( $songs );
	$logo = null;
	$html = $this->head_script( $id, $playlist_id, $songs, $in_popup, $autoplay );
		
		if ( $playlist->logo) {
			$logo = "background-image : url( '" . $playlist->logo . "' );";
		}
            
            
    	$html .='
    	<!-- jPlayer -->
	<div class="wolf-jplayer-playlist" style="display:none">
	<div class="wolf-jp-overlay">
		<div class="wolf-jp-share-container">
			<div class="wolf-jp-share">
			<div>
				<p><strong>Share</strong></p>
			</div>
			<div class="wolf-share-input">
				<label>url : </label>
				<div>
					<input onclick="this.focus();this.select()" type="text" value="'. WOLF_JPLAYER_PLUGIN_URL . '/wolf-jplayer-single.php?playlist_id=' . $playlist_id . '">
				</div>
			</div>
			<div class="wolf-share-input">
				<label>embed : </label>
				<div>
				<input onclick="this.focus();this.select()" type="text" value="&lt;iframe width=&quot;100%&quot; height=&quot;' . $player_height . '&quot; scrolling=&quot;no&quot; frameborder=&quot;no&quot; src=&quot;'. WOLF_JPLAYER_PLUGIN_URL . '/wolf-jplayer-frame.php?playlist_id=' . $playlist_id . '&amp;iframe=true&amp;wmode=transparent&quot;&gt;&lt;/iframe&gt;">
				</div>
			</div>
			<div class="clear"></div>
			<div class="wolf-jp-share-socials">
				<a class="wolf-share-jp-popup" href="http://www.facebook.com/sharer.php?u='. WOLF_JPLAYER_PLUGIN_URL . '/wolf-jplayer-single.php?playlist_id=' . $playlist_id .'&t='.urlencode( $share_title ).'" title="'.__( 'Share on facebook', 'wolf' ).'" target="_blank">
				<span id="wolf-jplayer-facebook-button"></span>
				</a>
				<a class="wolf-share-jp-popup" href="http://twitter.com/home?status='. urlencode( $share_title.' - ' ) . WOLF_JPLAYER_PLUGIN_URL . '/wolf-jplayer-single.php?playlist_id=' . $playlist_id .'" title="'.__( 'Share on twitter', 'wolf' ).'" target="_blank">
				<span id="wolf-jplayer-twitter-button"></span>
				</a>
			</div>
			<span class="close-wolf-jp-share" title="'. __( 'close', 'wolf' ) .'">&times;</span>
		</div>
		</div>
	</div>
	<div id="jplayer_container_' . $id . '" class="jplayer_container">
	<div id="jquery_jplayer_' . $id . '" class="jp-jplayer"></div>
		<div id="jp_container_' . $id . '" class="jp-audio">
		<div class="jp-logo" style="' . $logo . '"></div><span title="'. __( 'share', 'wolf' ) .'" class="wolf-jp-share-icon"></span>';
	
	if ( !$in_popup )
		$html .= '<a href="'.WOLF_JPLAYER_PLUGIN_URL . '/wolf-jplayer-frame.php?playlist_id=' . $playlist_id . '" class="wolf-jp-popup" title="popup window"></a>';
             
             $html .= '<div class="jp-type-playlist">
			<div class="jp-gui jp-interface">
				<ul class="jp-controls">
					<li><a href="javascript:;" class="jp-previous" tabindex="1"></a></li>
					<li><a href="javascript:;" class="jp-play" tabindex="1"></a></li>
					<li><a href="javascript:;" class="jp-pause" tabindex="1"></a></li>
					<li><a href="javascript:;" class="jp-next" tabindex="1"></a></li>
					<li><a href="javascript:;" class="jp-stop" tabindex="1"></a></li>
					<li class="wolf-volume">
						<a href="javascript:;" class="jp-mute" tabindex="1" title="mute"></a>
						<a href="javascript:;" class="jp-unmute" tabindex="1" title="unmute"></a>
					</li>
					<li><a href="javascript:;" class="jp-volume-max wolf-volume" tabindex="1" title="max volume"></a></li>
				</ul>
				<div class="jp-progress">
					<div class="jp-seek-bar">
						<div class="jp-play-bar"></div>
					</div>
				</div>
				<div class="jp-volume-bar wolf-volume">
					<div class="jp-volume-bar-value"></div>
				</div>
				<div class="jp-current-time"></div>
				<div class="jp-duration"></div>
				<ul class="jp-toggles">
					<li><a href="javascript:;" class="jp-shuffle" tabindex="1" title="shuffle"></a></li>
					<li><a href="javascript:;" class="jp-shuffle-off" tabindex="1" title="shuffle off"></a></li>
					<li><a href="javascript:;" class="jp-repeat" tabindex="1" title="repeat"></a></li>
					<li><a href="javascript:;" class="jp-repeat-off" tabindex="1" title="repeat off"></a></li>
				</ul>
			</div>

			<div class="jp-playlist">
				<ul>
					<li></li>
				</ul>
			</div>

			<div class="jp-no-solution">
				<span>Update Required</span>
				To play the media you will need to either update your browser to a recent version or update your <a href="http://get.adobe.com/flashplayer/" target="_blank">Flash plugin</a>.
			</div>

                            </div>

		</div>
	</div>
	</div>
	<div class="clear"></div>
	<!-- End jPlayer -->';

	else:
		if ( is_user_logged_in() )
			$html = '<p style="text-shadow:none!important"><em>'.__( 'This playlist does not exist or is empty. Please double check the playlist ID and be sure you have uploaded songs.', 'wolf' ).'</em></p>';
		else
			$html = '<p style="text-shadow:none!important"><em>'.__( 'This playlist does not exist or is empty.', 'wolf' ).'</em></p>';
	endif;

                    return $html;
	}


} //end class

global $wolf_jplayer_playlist;
$wolf_jplayer_playlist = new Wolf_Jplayer_Show;


/*-----------------------------------------------------------------------------------*/
/*  jPlayer Show Function
/*-----------------------------------------------------------------------------------*/
function wolf_jplayer_playlist_show( $id = 1, $in_popup = false )
{
    	global $wolf_jplayer_playlist;
    	return $wolf_jplayer_playlist->jplayer_playlist_show( $id, $in_popup );
}


/*-----------------------------------------------------------------------------------*/
/*  jPlayer Shortcode
/*-----------------------------------------------------------------------------------*/

add_shortcode( 'wolf_jplayer_playlist', 'wolf_jplayer_playlist_shortcode' );
function wolf_jplayer_playlist_shortcode( $atts )
{
	if ( function_exists( 'wolf_jplayer_playlist_show' ) ) {  
		
		extract( shortcode_atts( array(
		      "id" => '1',
		), $atts) );
		
		return wolf_jplayer_playlist_show( $id );	
	}
}

/*-----------------------------------------------------------------------------------*/
/*  jPlayer Widget
/*-----------------------------------------------------------------------------------*/
add_action( 'widgets_init', 'wolf_jplayer_init' );

function wolf_jplayer_init() {

	register_widget( 'wolf_jplayer_widget' );
	
}


/*-----------------------------------------------------------------------------------*/
/*  Widget Class
/*-----------------------------------------------------------------------------------*/
class wolf_jplayer_widget extends WP_Widget {

	/*-----------------------------------------------------------------------------------*/
	/*  Widget Setup
	/*-----------------------------------------------------------------------------------*/
	function wolf_jplayer_widget() {

		// Widget settings
		$ops = array( 'classname' => 'wolf_jplayer_widget', 'description' => __( 'Display a playlist', 'wolf' ) );

		// Create the widget
		$this->WP_Widget( 'wolf_jplayer_widget', 'Wolf jPlayer', $ops);
		
	}

	/*-----------------------------------------------------------------------------------*/
	/*  Display Widget
	/*-----------------------------------------------------------------------------------*/
	function widget( $args, $instance ) {
		
		extract( $args );
		
		$title = apply_filters( 'widget_title', $instance['title'] );
		$desc = '<p>'.$instance['desc'].'</p>';
		echo $before_widget;
		
		if ( !empty( $title ) ) echo $before_title . $title . $after_title;
		
		if ( $instance['desc'] ) echo $desc;
		
		echo wolf_jplayer_playlist_show( $instance['playlist_id'] );
		echo $after_widget;
	
	}

	/*-----------------------------------------------------------------------------------*/
	/*  Update Widget
	/*-----------------------------------------------------------------------------------*/
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		$instance['title'] = $new_instance['title'];
		$instance['desc'] = $new_instance['desc'];
		$instance['playlist_id'] = $new_instance['playlist_id'];

		return $instance;
	}

	/*-----------------------------------------------------------------------------------*/
	/*	Displays the widget settings controls on the widget panel
	/*-----------------------------------------------------------------------------------*/
	function form( $instance ) {

		global $wpdb;
		$wolf_jplayer_playlists_table = $wpdb->prefix . 'wolf_jplayer_playlists';
		$playlists = $wpdb->get_results( "SELECT * FROM $wolf_jplayer_playlists_table" );
		$default_playlist_id = 0;
		
		if ( $playlists )
			$default_playlist_id = $playlists[0]->id;

		// Set up some default widget settings
		$defaults = array(
			'title' => '', 
			'playlist_id' => $default_playlist_id, 
			'desc' => '' );
		$instance = wp_parse_args( (array) $instance, $defaults );

		?>
		<?php if ( $playlists ): ?>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title', 'wolf' ); ?>:</label>
			<input class="widefat" type="text" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>">
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'desc' ); ?>"><?php _e( 'Optional Text', 'wolf' ); ?>:</label>
			<textarea class="widefat"  id="<?php echo $this->get_field_id( 'desc' ); ?>" name="<?php echo $this->get_field_name( 'desc' ); ?>" ><?php echo $instance['desc']; ?></textarea>
		</p>
		
		<p>
			<label for="<?php echo $this->get_field_id( 'playlist_id' ); ?>"><?php _e( 'Playlist', 'wolf' ); ?>:</label>
			<select name="<?php echo $this->get_field_name( 'playlist_id' ); ?>" id="<?php echo $this->get_field_id( 'playlist_id' ); ?>">
				<?php foreach ( $playlists as $p ) : ?>
					<option value="<?php echo $p->id; ?>" <?php if ( $instance['playlist_id'] == $p->id ) echo 'selected="selected"'; ?>><?php echo $p->name; ?></option>
				<?php endforeach; ?>
			</select>
			
		</p>
		<?php else: ?>
			<p><?php _e( 'No playlist yet.', 'wolf' ); ?></p>
		<?php endif; ?>
		<?php
	}

}
?>