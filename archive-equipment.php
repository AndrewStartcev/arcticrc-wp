<?php
defined( 'ABSPATH' ) || exit;

get_header();
?>
<main class="page page--equipment" id="main">
	<div class="page__container">
		<div class="page-heading"><h1 class="page-heading__title"><?php post_type_archive_title(); ?></h1></div>
		<div class="collection collection--equipment">
			<?php while ( have_posts() ) : the_post(); ?>
				<article class="equipment-card">
					<?php if ( has_post_thumbnail() ) : the_post_thumbnail( 'large', array( 'class' => 'equipment-card__image' ) ); endif; ?>
					<h2 class="equipment-card__title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
					<?php if ( has_excerpt() ) : ?><p><?php echo esc_html( get_the_excerpt() ); ?></p><?php endif; ?>
				</article>
			<?php endwhile; ?>
		</div>
	</div>
</main>
<?php get_footer(); ?>
