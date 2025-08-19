<?php get_header(); ?>

<main>
    <?php
    while (have_posts()) : the_post();
        ?>
        <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
            
            <!-- This is where your blocks will render -->
            <div class="entry-content">
                <?php the_content(); ?>
            </div>
            
        </article>
        <?php
    endwhile;
    ?>
</main>

<?php get_footer(); ?>