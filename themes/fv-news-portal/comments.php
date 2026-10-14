<?php
/**
 * Comments template: 댓글 목록 + 작성 폼.
 *
 * @package fv-news-portal
 */

if ( post_password_required() ) {
	return;
}
?>
<section id="comments" class="comments">
	<header class="section-head">
		<h2 class="section-title">댓글 <?php echo esc_html( number_format_i18n( get_comments_number() ) ); ?></h2>
	</header>

	<?php if ( have_comments() ) : ?>
		<ol class="comment-list">
			<?php
			wp_list_comments(
				array(
					'style'       => 'ol',
					'short_ping'  => true,
					'avatar_size' => 40,
				)
			);
			?>
		</ol>
		<?php the_comments_pagination(); ?>
	<?php endif; ?>

	<?php if ( comments_open() ) : ?>
		<?php
		comment_form(
			array(
				'title_reply'  => '댓글 쓰기',
				'label_submit' => '등록',
			)
		);
		?>
	<?php else : ?>
		<p class="comments-closed">댓글이 닫힌 기사입니다.</p>
	<?php endif; ?>
</section>
