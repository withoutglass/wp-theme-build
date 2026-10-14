/**
 * 기사 스트림: 기사 끝에 가까워지면 이전 발행글을 REST로 받아 아래에 이어 붙인다.
 * - 자동으로 fvStream.autoMax편까지 불러오고, 그다음은 "다음 기사 보기"를 누를 때마다 한 편씩.
 * - 화면 가운데에 걸친 기사로 주소창 URL·탭 제목을 바꾼다 (replaceState — 뒤로 가기는 사이트를 떠남).
 *   GA4 향상된 측정(브라우저 기록 이벤트 기반 페이지 변경)이 이 주소 변경을 페이지뷰로 집계한다.
 * - 이어 붙인 기사에 실제 애드센스 <ins>가 있으면 push로 채우고, fvstream:loaded 이벤트를 보낸다.
 * JS·IntersectionObserver가 없으면 아무것도 하지 않고 "다음 기사 보기"는 일반 링크로 남는다.
 */
( function () {
	'use strict';

	var cfg = window.fvStream;
	var stream = document.querySelector( '.stream' );
	var control = document.querySelector( '.stream-control' );
	var more = control && control.querySelector( '.stream-more' );
	if ( ! cfg || ! stream || ! more || ! ( 'IntersectionObserver' in window ) || ! window.fetch ) {
		return;
	}
	var status = control.querySelector( '.stream-status' );
	var autoMax = parseInt( cfg.autoMax, 10 ) || 5; // wp_localize_script는 숫자도 문자열로 넘긴다

	var loaded = 0;
	var loading = false;
	var auto = true;
	var seen = {};
	var current = stream.querySelector( '.stream-item' );
	seen[ current.dataset.id ] = true;

	// 자동 모드에서는 링크를 숨기고, 컨트롤이 화면 아래 1200px 안으로 들어오면 미리 불러온다.
	// IntersectionObserver 대신 스크롤 위치로 판단한다 — End 키·빠른 플링으로 컨트롤을 한 번에 지나쳐
	// 화면 위로 올라가 버려도(교차 상태 변화 없음) 놓치지 않도록.
	more.hidden = true;
	var ticking = false;
	window.addEventListener(
		'scroll',
		function () {
			if ( ! ticking ) {
				ticking = true;
				window.requestAnimationFrame( function () {
					ticking = false;
					checkAuto();
				} );
			}
		},
		{ passive: true }
	);

	// 화면 세로 가운데 선에 걸친 기사를 "지금 읽는 기사"로 본다.
	var currentObserver = new IntersectionObserver(
		function ( entries ) {
			entries.forEach( function ( entry ) {
				if ( entry.isIntersecting ) {
					activate( entry.target );
				}
			} );
		},
		{ rootMargin: '-50% 0px -50% 0px' }
	);
	currentObserver.observe( current );

	more.addEventListener( 'click', function ( event ) {
		event.preventDefault();
		load();
	} );

	checkAuto();

	function lastItem() {
		var items = stream.querySelectorAll( '.stream-item' );
		return items[ items.length - 1 ];
	}

	function checkAuto() {
		if ( auto && ! loading && control.getBoundingClientRect().top < window.innerHeight + 1200 ) {
			load();
		}
	}

	function stopAuto() {
		auto = false;
	}

	function finish() {
		stopAuto();
		more.hidden = true;
		status.textContent = '';
	}

	function load() {
		if ( loading ) {
			return;
		}
		loading = true;
		status.textContent = '다음 기사를 불러오는 중…';

		var url = cfg.endpoint + ( cfg.endpoint.indexOf( '?' ) > -1 ? '&' : '?' ) + 'after=' + lastItem().dataset.id;
		fetch( url, { credentials: 'same-origin' } )
			.then( function ( response ) {
				if ( ! response.ok ) {
					throw new Error( 'HTTP ' + response.status );
				}
				return response.json();
			} )
			.then( function ( data ) {
				loading = false;
				status.textContent = '';
				if ( data.done || seen[ data.id ] ) {
					finish();
					return;
				}

				var tpl = document.createElement( 'template' );
				tpl.innerHTML = data.html.trim();
				var item = tpl.content.querySelector( '.stream-item' );
				stream.appendChild( tpl.content );
				seen[ data.id ] = true;
				loaded++;

				currentObserver.observe( item );
				fillAds( item );
				document.dispatchEvent( new CustomEvent( 'fvstream:loaded', { detail: { element: item, id: data.id, url: data.url } } ) );

				if ( ! data.next_url ) {
					finish();
					return;
				}
				more.href = data.next_url;

				if ( loaded >= autoMax ) {
					stopAuto();
					more.hidden = false;
				} else {
					// 이어 붙인 기사가 짧아 컨트롤이 아직 범위 안이면 스크롤 없이도 이어서 부른다.
					checkAuto();
				}
			} )
			.catch( function () {
				loading = false;
				stopAuto();
				status.textContent = '기사를 불러오지 못했습니다. 아래 버튼으로 다시 시도해 주세요.';
				more.hidden = false;
			} );
	}

	function activate( item ) {
		if ( item === current ) {
			return;
		}
		current = item;
		if ( window.location.href !== item.dataset.url ) {
			window.history.replaceState( window.history.state, '', item.dataset.url );
		}
		document.title = item.dataset.title;
	}

	function fillAds( item ) {
		var slots = item.querySelectorAll( 'ins.adsbygoogle:not([data-adsbygoogle-status])' );
		for ( var i = 0; i < slots.length; i++ ) {
			( window.adsbygoogle = window.adsbygoogle || [] ).push( {} );
		}
	}
} )();
