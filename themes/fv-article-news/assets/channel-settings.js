/**
 * 채널 설정: 프로필 이미지·파비콘·푸터 로고 선택 (미디어 라이브러리).
 */
( function ( $ ) {
	function setImage( box, id, url ) {
		box.find( 'input[type="hidden"]' ).val( id );
		box.find( '.fv-image-preview' ).attr( 'src', url ).prop( 'hidden', ! id );
		box.find( '.fv-image-remove' ).prop( 'hidden', ! id );
	}

	$( document ).on( 'click', '.fv-image-select', function ( e ) {
		e.preventDefault();
		var box = $( this ).closest( '.fv-image-field' );
		var frame = wp.media( {
			title: '이미지 선택',
			button: { text: '이 이미지 사용' },
			library: { type: 'image' },
			multiple: false
		} );
		frame.on( 'select', function () {
			var image = frame.state().get( 'selection' ).first().toJSON();
			var url = image.sizes && image.sizes.medium ? image.sizes.medium.url : image.url;
			setImage( box, image.id, url );
		} );
		frame.open();
	} );

	$( document ).on( 'click', '.fv-image-remove', function ( e ) {
		e.preventDefault();
		setImage( $( this ).closest( '.fv-image-field' ), '', '' );
	} );
}( jQuery ) );
