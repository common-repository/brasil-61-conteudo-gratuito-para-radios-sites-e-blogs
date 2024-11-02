jQuery( document ).ready(function() {

	var url_site = jQuery('#baseurl').val();

	jQuery( ".b61_audio_player" ).on( "play", function(e) {

		var noticia_id = jQuery(this).data('noticia');
		var nonce = jQuery('#audio_nonce').val();

		jQuery.ajax({
			url: url_site+"/wp-admin/admin-ajax.php",
			type:'POST',
			data:{
				'action':'br61_audio_view',
				'nonce':nonce,
				'noticia_id':noticia_id,
			},
			dataType: 'JSON',
			success:function(response) {
				console.log(response);
			}
		});


	});

});