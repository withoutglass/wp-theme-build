<?php
/**
 * MSN 공급용 RSS2 피드 템플릿. URL: /feed/ms
 * 본문 링크 제거 + 실시간 인기기사 블록(UTM) 삽입.
 *
 * @package sample-01
 */

header( 'Content-Type: ' . feed_content_type( 'rss2' ) . '; charset=' . get_option( 'blog_charset' ), true );
$more = 1;

echo '<?xml version="1.0" encoding="' . esc_attr( get_option( 'blog_charset' ) ) . '"?' . '>';

do_action( 'rss_tag_pre', 'rss2' );
?>
<rss version="2.0"
	xmlns:content="http://purl.org/rss/1.0/modules/content/"
	xmlns:wfw="http://wellformedweb.org/CommentAPI/"
	xmlns:dc="http://purl.org/dc/elements/1.1/"
	xmlns:atom="http://www.w3.org/2005/Atom"
	xmlns:sy="http://purl.org/rss/1.0/modules/syndication/"
	xmlns:slash="http://purl.org/rss/1.0/modules/slash/"
	xmlns:dcterms="http://purl.org/dc/terms"
	<?php do_action( 'rss2_ns' ); ?>>

	<channel>
		<title><?php wp_title_rss(); ?></title>
		<atom:link href="<?php self_link(); ?>" rel="self" type="application/rss+xml" />
		<link><?php bloginfo_rss( 'url' ); ?></link>
		<description><?php bloginfo_rss( 'description' ); ?></description>
		<lastBuildDate><?php echo get_feed_build_date( 'r' ); ?></lastBuildDate>
		<language><?php bloginfo_rss( 'language' ); ?></language>
		<sy:updatePeriod><?php echo apply_filters( 'rss_update_period', 'hourly' ); ?></sy:updatePeriod>
		<sy:updateFrequency><?php echo apply_filters( 'rss_update_frequency', '1' ); ?></sy:updateFrequency>
		<?php
		do_action( 'rss2_head' );

		while ( have_posts() ) :
			the_post();
			?>
			<item>
				<title><?php the_title_rss(); ?></title>
				<link><?php the_permalink_rss(); ?></link>
				<?php if ( get_comments_number() || comments_open() ) : ?>
					<comments><?php comments_link_feed(); ?></comments>
				<?php endif; ?>

				<dc:creator>
					<![CDATA[<?php the_author(); ?>]]>
				</dc:creator>
				<pubDate><?php echo mysql2date( 'D, d M Y H:i:s +0000', get_post_time( 'Y-m-d H:i:s', true ), false ); ?></pubDate>
				<?php
				// 초기수정시간과 현재수정시간이 다르고, 수정이 3시간(10800초) 이내면 <dcterms:modified> 표기.
				$initial_modified_time = get_post_meta( get_the_ID(), '_initial_modified_time', true );
				$current_modified_time = get_post_modified_time( 'Y-m-d H:i:s', true );
				$time_difference       = time() - strtotime( $current_modified_time );
				if ( $initial_modified_time && $initial_modified_time !== $current_modified_time && $time_difference <= 10800 ) {
					echo '<dcterms:modified>' . mysql2date( 'D, d M Y H:i:s +0000', $current_modified_time, false ) . '</dcterms:modified>';
				}
				?>
				<?php the_category_rss( 'rss2' ); ?>
				<guid isPermaLink="false"><?php the_guid(); ?></guid>

				<?php if ( get_option( 'rss_use_excerpt' ) ) : ?>
					<description>
						<![CDATA[<?php the_excerpt_rss(); ?>]]>
					</description>
				<?php else : ?>
					<description>
						<![CDATA[<?php the_excerpt_rss(); ?>]]>
					</description>
					<?php $content = get_the_content_feed( 'rss2' ); ?>
					<?php if ( strlen( $content ) > 0 ) : ?>
						<content:encoded>
							<![CDATA[<?php echo preg_replace( '/<a\s+[^>]*>(.*?)<\/a\s*>/', '$1', $content ); ?>
					<h3>실시간 인기기사</h3>
					<ul>
					<?php
						$popular = new WP_Query(
							array(
								'posts_per_page' => 3,
								'post__not_in'   => array( get_the_ID() ),
							)
						);
						while ( $popular->have_posts() ) :
							$popular->the_post();
							echo '<li><a href="' . get_permalink() . '?utm_source=msn&utm_medium=feed&utm_campaign=popular">' . get_the_title() . '</a></li>';
						endwhile;
						wp_reset_postdata();
					?>
					</ul>
				]]>
						</content:encoded>
					<?php else : ?>
						<content:encoded>
							<![CDATA[<?php the_excerpt_rss(); ?>]]>
						</content:encoded>
					<?php endif; ?>
				<?php endif; ?>

				<?php if ( get_comments_number() || comments_open() ) : ?>
					<wfw:commentRss><?php echo esc_url( get_post_comments_feed_link( null, 'rss2' ) ); ?></wfw:commentRss>
					<slash:comments><?php echo get_comments_number(); ?></slash:comments>
				<?php endif; ?>

				<?php rss_enclosure(); ?>

				<?php do_action( 'rss2_item' ); ?>
			</item>
		<?php endwhile; ?>
	</channel>
</rss>
