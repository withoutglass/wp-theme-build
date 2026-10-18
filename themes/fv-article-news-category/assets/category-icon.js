/**
 * 글 > 카테고리: 채널 아이콘 선택 (미디어 라이브러리).
 *
 * @package fv-article-news-category
 */
( function ( $ ) {
	function setIcon( box, id, url ) {
		box.find( 'input[name="channel_icon"]' ).val( id );
		box.find( '.fv-channel-icon-preview' ).attr( 'src', url ).prop( 'hidden', ! id );
		box.find( '.fv-channel-icon-remove' ).prop( 'hidden', ! id );
	}

	$( document ).on( 'click', '.fv-channel-icon-select', function ( e ) {
		e.preventDefault();
		var box = $( this ).closest( '.fv-channel-icon' );
		var frame = wp.media( {
			title: '채널 아이콘 선택',
			button: { text: '이 이미지 사용' },
			library: { type: 'image' },
			multiple: false
		} );
		frame.on( 'select', function () {
			var image = frame.state().get( 'selection' ).first().toJSON();
			var url = image.sizes && image.sizes.thumbnail ? image.sizes.thumbnail.url : image.url;
			setIcon( box, image.id, url );
		} );
		frame.open();
	} );

	$( document ).on( 'click', '.fv-channel-icon-remove', function ( e ) {
		e.preventDefault();
		setIcon( $( this ).closest( '.fv-channel-icon' ), '', '' );
	} );

	// 카테고리 추가는 AJAX라 폼이 새로 고쳐지지 않고, WP는 숨은 필드를 비우지 않는다.
	// 그대로 두면 다음에 추가하는 카테고리에 같은 아이콘이 들어가므로 추가 성공 시 비운다.
	$( document ).ajaxSuccess( function ( e, xhr, settings ) {
		if ( settings.data && settings.data.indexOf( 'action=add-tag' ) !== -1 && xhr.responseText.indexOf( 'wp_error' ) === -1 ) {
			$( '#addtag .fv-channel-icon' ).each( function () {
				setIcon( $( this ), '', '' );
			} );
		}
	} );
}( jQuery ) );
