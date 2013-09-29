jQuery( function( $ ) {

/*-----------------------------------------------------------------------------------*/
/*	Uploader
/*-----------------------------------------------------------------------------------*/
	$( '.wolf_jplayer_upload_button' ).click( function(e) {
		var $el = $( this ).parent();
		e.preventDefault();
		var uploader = wp.media( {
			title : 'Choose a song',
			//button : {
			//	text : ''
			//},
			multiple : false
		} )
		.on( 'select', function() {
			var selection = uploader.state().get( 'selection' );
			var attachment = selection.first().toJSON();
			//console.log(attachment);
			$( 'input', $el ).val( attachment.url );
			$( 'img', $el ).attr('src', attachment.url ).show();
		} )
		.open();
	} );

/*-----------------------------------------------------------------------------------*/
/*	Reset Image preview in theme options
/*-----------------------------------------------------------------------------------*/

	$( '.wolf_jplayer_reset' ).click( function() {
		
		$( this ).parent().find( 'input' ).val( '' );
		$( this ).parent().find( '.wolf_jplayer_img_preview' ).hide();
		return false;

	});
});