<?php
/**
 * Template Name: Homepage
 * Description: Door County Land Trust homepage with custom block layout
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

get_header(); ?>

<main id="main" class="site-main" role="main">
    
    <?php while (have_posts()) : the_post(); ?>
        
        <article id="post-<?php the_ID(); ?>" <?php post_class('homepage-content'); ?>>
            
            <!-- Homepage Block Content -->
            <div class="homepage-blocks">
                <?php the_content(); ?>
            </div>
            
            <!-- Fallback content if no blocks are added -->
            <?php if (empty(trim(get_the_content()))): ?>
                
                <!-- Default Hero Section -->
                <section class="dclt-hero-block relative overflow-hidden bg-gray-900 text-white">
                    <div class="absolute inset-0">
                        <div class="w-full h-full bg-gradient-to-br from-green-800 to-green-900"></div>
                        <div class="absolute inset-0 bg-black opacity-40"></div>
                    </div>
                    
                    <div class="max-w-7xl mx-auto px-4 relative z-10 py-20 md:py-32">
                        <div class="hero-content max-w-2xl">
                            <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold mb-6 leading-tight">
                                Protect the Land You Love
                            </h1>
                            <p class="text-xl md:text-2xl mb-8 text-white/90 leading-relaxed">
                                From majestic bluffs to pristine beaches, Door County's natural beauty is worth preserving.
                            </p>
                            <div class="flex flex-col sm:flex-row gap-4">
                                <a href="/protect-your-land" 
                                   class="bg-white text-green-800 hover:bg-green-50 px-8 py-4 rounded-lg font-bold text-lg transition-colors duration-200 inline-block text-center">
                                    Protect Your Land
                                </a>
                                <a href="/about" 
                                   class="bg-transparent text-white border-2 border-white hover:bg-white hover:text-green-800 px-8 py-4 rounded-lg font-semibold text-lg transition-all duration-200 inline-block text-center">
                                    Learn More
                                </a>
                            </div>
                        </div>
                    </div>
                </section>
                
                <!-- Default Stats Section -->
                <section class="py-16 bg-white">
                    <div class="max-w-6xl mx-auto px-4">
                        <div class="grid md:grid-cols-3 gap-8 text-center">
                            <div class="stats-item">
                                <div class="text-4xl md:text-5xl font-bold text-green-800 mb-2">6,345</div>
                                <div class="text-lg text-gray-600 uppercase tracking-wide">Acres Preserved</div>
                            </div>
                            <div class="stats-item">
                                <div class="text-4xl md:text-5xl font-bold text-green-800 mb-2">178</div>
                                <div class="text-lg text-gray-600 uppercase tracking-wide">Landowners Partnered</div>
                            </div>
                            <div class="stats-item">
                                <div class="text-4xl md:text-5xl font-bold text-green-800 mb-2">36</div>
                                <div class="text-lg text-gray-600 uppercase tracking-wide">Preserves Established</div>
                            </div>
                        </div>
                    </div>
                </section>
                
                <!-- Default CTA Section -->
                <section class="py-16 bg-gradient-to-br from-green-800 to-green-900 text-white">
                    <div class="max-w-6xl mx-auto px-4">
                        <div class="grid md:grid-cols-2 gap-12 items-center">
                            <div>
                                <h2 class="text-3xl md:text-4xl font-bold mb-6">
                                    Ready to Protect Your Land?
                                </h2>
                                <p class="text-lg mb-8 text-white/90">
                                    From majestic bluffs to pristine beaches, Door County's natural beauty is worth preserving.
                                </p>
                            </div>
                            <div class="text-center">
                                <a href="/protect-your-land" 
                                   class="bg-white text-green-800 hover:bg-green-50 px-8 py-4 rounded-lg font-bold text-lg transition-colors duration-200 inline-block">
                                    Protect Your Land
                                </a>
                            </div>
                        </div>
                    </div>
                </section>
                
            <?php endif; ?>
            
        </article>
        
    <?php endwhile; ?>
    
</main>

<?php get_footer(); ?>

<style>
/* Homepage-specific styles */
.homepage-content {
    /* Remove default post margins */
    margin: 0;
}

.homepage-blocks {
    /* Ensure blocks have no gaps */
    line-height: 0;
}

.homepage-blocks > * {
    /* Reset line height for block content */
    line-height: normal;
}

/* Stats animation on scroll (basic version) */
.stats-item {
    opacity: 0;
    transform: translateY(30px);
    transition: all 0.6s ease-out;
}

.stats-item.animated {
    opacity: 1;
    transform: translateY(0);
}

/* Progressive enhancement for JavaScript */
.no-js .stats-item {
    opacity: 1;
    transform: none;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Basic stats animation on scroll
    const statsItems = document.querySelectorAll('.stats-item');
    
    const observerOptions = {
        threshold: 0.3,
        rootMargin: '0px 0px -50px 0px'
    };
    
    const observer = new IntersectionObserver(function(entries) {
        entries.forEach(function(entry) {
            if (entry.isIntersecting) {
                entry.target.classList.add('animated');
            }
        });
    }, observerOptions);
    
    statsItems.forEach(function(item) {
        observer.observe(item);
    });
});
</script>