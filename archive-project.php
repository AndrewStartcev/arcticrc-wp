<?php
defined( 'ABSPATH' ) || exit;

get_header();
?>
<main class="page page--projects" id="main">
	<div class="page__container">
		<div class="page-heading"><h1 class="page-heading__title"><?php post_type_archive_title(); ?></h1></div>
		<div class="collection">
			<?php while ( have_posts() ) : the_post(); ?>
				<article class="project-card">
					<?php if ( has_post_thumbnail() ) : ?><a href="<?php the_permalink(); ?>"><?php the_post_thumbnail( 'large' ); ?></a><?php endif; ?>
					<h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
					<?php if ( has_excerpt() ) : ?><p><?php echo esc_html( get_the_excerpt() ); ?></p><?php endif; ?>
				</article>
			<?php endwhile; ?>
		</div>
	</div>
</main>
<?php get_footer(); ?>
