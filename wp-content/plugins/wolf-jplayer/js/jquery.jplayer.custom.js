;( function( $ ) {
	"use strict";

	$( '.wolf-jplayer-playlist' ).addClass( 'js' );

	$( '.wolf-jplayer-playlist' ).find( 'span.close-wolf-jp-share' ).click ( function() {
		$( this ).parent().parent().parent().fadeOut();
	} );

	$( '.wolf-jp-share-icon' ).click( function() {
		var container = $( this ).parent().parent().parent();
		container.find( '.wolf-jp-overlay' ).fadeIn();
	} );
		

	$( '.wolf-share-jp-popup' ).click( function() {
		var url = jQuery(this).attr( 'href' );
		var popup = window.open(url,'null', 'height=350,width=570, top=150, left=150');
		if ( window.focus ) {
			popup.focus();
		}
		return false; 
	} );

	function responsiveWolfjPlayer() {
		var Wolfplayer = $( '.wolf-jplayer-playlist' );

		if ( Wolfplayer.length ) {

			Wolfplayer.each( function() {
				var width = $( this).width();
				
				if ( 425 > width ) {

					$( this ).find( '.wolf-volume' ).hide();

				}else{
					
					$( this ).find( '.wolf-volume' ).show();
				}
			} );

		}
	}
		

	$( window ).resize( function() {
		responsiveWolfjPlayer();
	} ).resize();

} )( jQuery );

jQuery( window ).load( function() {

	"use strict";

	var $ = jQuery;

	$( '.wolf-jplayer-playlist' ).find( 'img' ).removeAttr( 'style' );
	$( '.wolf-jplayer-playlist .jp-jplayer').removeAttr( 'style' );

             var Wolfplayer = $( '.wolf-jplayer-playlist' );

	if ( Wolfplayer.length ) {

		Wolfplayer.each( function() {
						
			$( this ).fadeIn();

		} );

		$( window ).trigger( 'resize' );

	}

} );