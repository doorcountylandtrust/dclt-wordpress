<?php
/**
 * The header for Door County Land Trust theme
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="profile" href="https://gmpg.org/xfn/11">
    
    <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<div id="page" class="site">
    <a class="skip-link screen-reader-text" href="#main"><?php esc_html_e('Skip to content', 'dclt'); ?></a>

    <header id="masthead" class="site-header" role="banner">
        <div class="header-container">
            
            <!-- Main Header Bar -->
            <div class="header-main bg-white shadow-sm border-b border-gray-100">
                <div class="max-w-7xl mx-auto px-4">
                    <div class="flex items-center justify-between h-20">
                        
                        <!-- Logo/Branding -->
                        <div class="header-branding flex items-center">
                            <a href="<?php echo esc_url(home_url('/')); ?>" class="site-logo flex items-center" rel="home">
                                <?php if (has_custom_logo()) : ?>
                                    <?php the_custom_logo(); ?>
                                <?php else : ?>
                                    <!-- Placeholder Logo -->
                                    <div class="logo-placeholder w-12 h-12 bg-green-800 rounded-full flex items-center justify-center mr-3">
                                        <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M12 2L13.09 7.26L18 6L16.74 11.09L22 12L16.74 12.91L18 18L13.09 16.74L12 22L10.91 16.74L6 18L7.26 12.91L2 12L7.26 11.09L6 6L10.91 7.26L12 2Z"/>
                                        </svg>
                                    </div>
                                    <div class="site-title-group">
                                        <h1 class="site-title text-xl font-bold text-gray-900 leading-tight">
                                            <?php bloginfo('name'); ?>
                                        </h1>
                                        <?php 
                                        $description = get_bloginfo('description', 'display');
                                        if ($description || is_customize_preview()) : ?>
                                            <p class="site-description text-sm text-gray-600 leading-tight">
                                                <?php echo $description; ?>
                                            </p>
                                        <?php endif; ?>
                                    </div>
                                <?php endif; ?>
                            </a>
                        </div>
                        
                       <!-- Desktop Navigation -->
                        <nav class="header-navigation hidden lg:flex items-center space-x-8" role="navigation" aria-label="Primary Navigation">
                            <?php
                            if (has_nav_menu('primary')) {
                                wp_nav_menu(array(
                                    'theme_location' => 'primary',
                                    'menu_class'     => 'primary-menu flex items-center space-x-8',
                                    'container'      => false,
                                    'fallback_cb'    => false,
                                ));
                            } else {
                                // Fallback menu
                                echo '<ul class="primary-menu flex items-center space-x-8">';
                                echo '<li><a href="' . home_url('/about') . '">About</a></li>';
                                echo '<li><a href="' . home_url('/protect-your-land') . '">Protect Your Land</a></li>';
                                echo '<li><a href="' . home_url('/get-involved') . '">Get Involved</a></li>';
                                echo '</ul>';
                            }
                            ?>
                        </nav>
                        
                        <!-- Primary CTA & Mobile Toggle -->
                        <div class="header-actions flex items-center space-x-4">
                            <!-- Primary CTA Button -->
                            <?php
                            wp_nav_menu(array(
                                'theme_location' => 'header-cta',
                                'menu_class'     => 'header-cta-menu',
                                'container'      => false,
                                'fallback_cb'    => 'dclt_header_cta_fallback',
                            ));
                            ?>
                            
                            <!-- Mobile Menu Toggle -->
                            <button type="button" 
                                    class="mobile-menu-toggle lg:hidden inline-flex items-center justify-center p-2 rounded-md text-gray-600 hover:text-gray-900 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-green-800"
                                    aria-controls="mobile-menu" 
                                    aria-expanded="false"
                                    aria-label="Toggle navigation menu">
                                <span class="sr-only">Open main menu</span>
                                <!-- Menu Icon -->
                                <svg class="menu-icon block h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                                </svg>
                                <!-- Close Icon (hidden by default) -->
                                <svg class="close-icon hidden h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                        
                    </div>
                </div>
            </div>
            
            <!-- Mobile Menu -->
            <div class="mobile-menu hidden lg:hidden" id="mobile-menu">
                <div class="bg-white border-t border-gray-200 shadow-lg">
                    <div class="max-w-7xl mx-auto px-4 py-4">
                        <nav class="mobile-navigation" role="navigation" aria-label="Mobile Navigation">
                            <?php
                            if (has_nav_menu('primary')) {
                                wp_nav_menu(array(
                                    'theme_location' => 'primary',
                                    'menu_class'     => 'mobile-primary-menu space-y-2',
                                    'container'      => false,
                                    'fallback_cb'    => false,
                                ));
                            } else {
                                // Fallback mobile menu
                                echo '<div class="mobile-primary-menu space-y-2">';
                                echo '<div class="mobile-menu-item"><a href="' . home_url('/about') . '">About</a></div>';
                                echo '<div class="mobile-menu-item"><a href="' . home_url('/protect-your-land') . '">Protect Your Land</a></div>';
                                echo '<div class="mobile-menu-item"><a href="' . home_url('/get-involved') . '">Get Involved</a></div>';
                                echo '</div>';
                            }
                            ?>
                            
                            <!-- Mobile CTA Button -->
                            <div class="pt-4 border-t border-gray-200 mt-4">
                                <?php
                                wp_nav_menu(array(
                                    'theme_location' => 'header-cta',
                                    'menu_class'     => 'mobile-cta-menu',
                                    'container'      => false,
                                    'fallback_cb'    => 'dclt_mobile_cta_fallback',
                                ));
                                ?>
                            </div>
                        </nav>
                    </div>
                </div>
            </div>
            
        </div>
    </header>

<style>
/* Enhanced Header Styles */

/* Reset and Base Styles */
* {
    box-sizing: border-box;
}

/* Header Structure */
.site-header {
    position: sticky;
    top: 0;
    z-index: 100;
    background: white;
    transition: all 0.3s ease-in-out;
}

.header-main {
    transition: box-shadow 0.2s ease-in-out;
    border-bottom: 1px solid #e5e7eb;
}

.site-header.scrolled .header-main {
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(10px);
}

/* Container and Layout */
.max-w-7xl {
    max-width: 80rem;
    margin-left: auto;
    margin-right: auto;
}

.px-4 {
    padding-left: 1rem;
    padding-right: 1rem;
}

.h-20 {
    height: 5rem;
}

.flex {
    display: flex;
}

.items-center {
    align-items: center;
}

.justify-between {
    justify-content: space-between;
}

.space-x-4 > * + * {
    margin-left: 1rem;
}

.space-x-8 > * + * {
    margin-left: 2rem;
}

/* Logo and Branding */
.header-branding {
    display: flex;
    align-items: center;
}

.site-logo {
    display: flex;
    align-items: center;
    text-decoration: none;
    transition: opacity 0.2s ease-in-out;
}

.site-logo:hover {
    opacity: 0.8;
}

.logo-placeholder {
    width: 3rem;
    height: 3rem;
    background: #1f2937;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 0.75rem;
    background: linear-gradient(135deg, #065f46 0%, #047857 100%);
    box-shadow: 0 2px 4px rgba(6, 95, 70, 0.2);
}

.site-title {
    font-size: 1.25rem;
    font-weight: 700;
    color: #1f2937;
    line-height: 1.2;
    margin: 0;
}

.site-description {
    font-size: 0.875rem;
    color: #6b7280;
    line-height: 1.2;
    margin: 0;
}

.site-logo img {
    height: 48px;
    width: auto;
}

/* Desktop Navigation */
.header-navigation {
    display: none;
}

.primary-menu {
    display: flex;
    list-style: none;
    margin: 0;
    padding: 0;
    align-items: center;
    gap: 2rem;
}

.primary-menu li {
    list-style: none;
    margin: 0;
    position: relative;
}

.primary-menu a {
    color: #374151;
    font-weight: 500;
    text-decoration: none;
    padding: 0.75rem 0;
    border-bottom: 2px solid transparent;
    transition: all 0.2s ease-in-out;
    position: relative;
    font-size: 1rem;
}

.primary-menu a:hover,
.primary-menu a:focus {
    color: #065f46;
    border-bottom-color: #065f46;
}

.primary-menu .current-menu-item a,
.primary-menu .current_page_item a {
    color: #065f46;
    border-bottom-color: #065f46;
    font-weight: 600;
}

/* Header Actions */
.header-actions {
    display: flex;
    align-items: center;
    gap: 1rem;
}

/* Header CTA Menu Styling */
.header-cta-menu {
    margin: 0;
    padding: 0;
    list-style: none;
}

.header-cta-menu li {
    margin: 0;
    padding: 0;
    list-style: none;
}

.header-cta-menu a {
    background: linear-gradient(135deg, #065f46 0%, #047857 100%);
    color: white !important;
    padding: 0.625rem 1.5rem;
    border-radius: 0.5rem;
    font-weight: 600;
    font-size: 0.875rem;
    text-decoration: none;
    transition: all 0.2s ease-in-out;
    box-shadow: 0 2px 4px rgba(6, 95, 70, 0.2);
    border-bottom: none !important;
    display: inline-block;
}

.header-cta-menu a:hover {
    background: linear-gradient(135deg, #047857 0%, #065f46 100%);
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(6, 95, 70, 0.3);
    color: white !important;
}

.header-cta-menu a:active {
    transform: translateY(0);
}

.header-cta-menu a::after {
    display: none;
}

/* Mobile CTA Menu */
.mobile-cta-menu {
    margin: 0;
    padding: 0;
    list-style: none;
}

.mobile-cta-menu a {
    background: linear-gradient(135deg, #065f46 0%, #047857 100%);
    color: white;
    padding: 0.75rem 1.5rem;
    border-radius: 0.5rem;
    font-weight: 600;
    text-align: center;
    display: block;
    text-decoration: none;
    transition: all 0.2s ease-in-out;
    box-shadow: 0 2px 4px rgba(6, 95, 70, 0.2);
}

.mobile-cta-menu a:hover {
    background: linear-gradient(135deg, #047857 0%, #065f46 100%);
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(6, 95, 70, 0.3);
    color: white;
}

/* Mobile Menu Toggle */
.mobile-menu-toggle {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 0.5rem;
    border-radius: 0.375rem;
    color: #6b7280;
    background: transparent;
    border: none;
    cursor: pointer;
    transition: all 0.2s ease-in-out;
}

.mobile-menu-toggle:hover {
    color: #1f2937;
    background: #f3f4f6;
}

.mobile-menu-toggle:focus {
    outline: 2px solid #065f46;
    outline-offset: 2px;
}

.mobile-menu-toggle svg {
    width: 1.5rem;
    height: 1.5rem;
}

/* Mobile Menu */
.mobile-menu {
    display: none;
    transition: all 0.3s ease-in-out;
    max-height: 0;
    overflow: hidden;
}

.mobile-menu.open {
    display: block;
    max-height: 500px;
}

.mobile-menu .bg-white {
    background: white;
    border-top: 1px solid #e5e7eb;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
}

.mobile-navigation {
    padding: 1rem 0;
}

.mobile-primary-menu {
    margin: 0;
    padding: 0;
}

.mobile-menu-item {
    margin-bottom: 0.5rem;
}

.mobile-primary-menu a,
.mobile-menu-item a {
    display: block;
    color: #374151;
    font-weight: 500;
    text-decoration: none;
    padding: 0.75rem 0;
    border-bottom: 1px solid #f3f4f6;
    transition: color 0.2s ease-in-out;
    font-size: 1rem;
}

.mobile-primary-menu a:hover,
.mobile-primary-menu a:focus,
.mobile-menu-item a:hover,
.mobile-menu-item a:focus {
    color: #065f46;
}

.mobile-primary-menu .current-menu-item a,
.mobile-primary-menu .current_page_item a {
    color: #065f46;
    font-weight: 600;
}

/* Utility Classes */
.hidden {
    display: none;
}

.block {
    display: block;
}

.sr-only {
    position: absolute;
    width: 1px;
    height: 1px;
    padding: 0;
    margin: -1px;
    overflow: hidden;
    clip: rect(0, 0, 0, 0);
    white-space: nowrap;
    border: 0;
}

/* Accessibility */
.skip-link {
    position: absolute;
    left: -9999px;
    z-index: 999999;
    padding: 8px 16px;
    background: #000;
    color: #fff;
    text-decoration: none;
    border-radius: 3px;
}

.skip-link:focus {
    left: 6px;
    top: 7px;
    z-index: 999999;
}

/* Responsive Design */
@media (min-width: 640px) {
    .sm\:block {
        display: block;
    }
}

@media (min-width: 1024px) {
    .lg\:flex {
        display: flex;
    }
    
    .lg\:hidden {
        display: none;
    }
    
    .header-navigation {
        display: flex;
        align-items: center;
        gap: 2rem;
    }
    
    .mobile-menu-toggle {
        display: none;
    }
    
    .site-header.scrolled {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
    }
}

/* Mobile menu fixes */
@media (max-width: 1023px) {
    .mobile-menu-toggle {
        display: inline-flex !important;
        z-index: 101;
    }
    
    .header-cta-menu {
        display: none;
    }
    
    .header-actions {
        gap: 0.5rem;
    }
}

/* Ensure mobile menu appears above everything */
.mobile-menu {
    z-index: 102;
}

.mobile-menu.open {
    z-index: 102;
}

/* Enhanced Visual Polish */
.header-main {
    background: linear-gradient(180deg, #ffffff 0%, #fafafa 100%);
}

/* Micro-interactions */
.primary-menu a::after {
    content: '';
    position: absolute;
    bottom: -2px;
    left: 50%;
    width: 0;
    height: 2px;
    background: #065f46;
    transition: all 0.3s ease-in-out;
    transform: translateX(-50%);
}

.primary-menu a:hover::after {
    width: 100%;
}

.primary-menu .current-menu-item a::after,
.primary-menu .current_page_item a::after {
    width: 100%;
}

/* Focus states for better accessibility */
.primary-menu a:focus,
.mobile-primary-menu a:focus,
.mobile-menu-item a:focus {
    outline: 2px solid #065f46;
    outline-offset: 2px;
    border-radius: 2px;
}

/* High contrast mode support */
@media (prefers-contrast: high) {
    .site-header {
        border-bottom: 2px solid #000;
    }
    
    .primary-menu a,
    .mobile-primary-menu a {
        border: 1px solid transparent;
    }
    
    .primary-menu a:focus,
    .mobile-primary-menu a:focus {
        border-color: #000;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Mobile menu toggle
    const mobileMenuToggle = document.querySelector('.mobile-menu-toggle');
    const mobileMenu = document.querySelector('.mobile-menu');
    const menuIcon = document.querySelector('.menu-icon');
    const closeIcon = document.querySelector('.close-icon');
    
    if (mobileMenuToggle && mobileMenu) {
        mobileMenuToggle.addEventListener('click', function() {
            const isExpanded = mobileMenuToggle.getAttribute('aria-expanded') === 'true';
            
            // Toggle aria-expanded
            mobileMenuToggle.setAttribute('aria-expanded', !isExpanded);
            
            // Toggle menu visibility
            mobileMenu.classList.toggle('hidden');
            mobileMenu.classList.toggle('open');
            
            // Toggle icons
            menuIcon.classList.toggle('hidden');
            closeIcon.classList.toggle('hidden');
            menuIcon.classList.toggle('block');
            closeIcon.classList.toggle('block');
        });
    }
    
    // Header scroll effect
    let lastScrollTop = 0;
    const header = document.querySelector('.site-header');
    
    window.addEventListener('scroll', function() {
        const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
        
        if (scrollTop > 100) {
            header.classList.add('scrolled');
        } else {
            header.classList.remove('scrolled');
        }
        
        lastScrollTop = scrollTop;
    });
    
    // Close mobile menu when clicking outside
    document.addEventListener('click', function(event) {
        if (!event.target.closest('.header-actions') && !event.target.closest('.mobile-menu')) {
            mobileMenu?.classList.add('hidden');
            mobileMenu?.classList.remove('open');
            mobileMenuToggle?.setAttribute('aria-expanded', 'false');
            menuIcon?.classList.remove('hidden');
            closeIcon?.classList.add('hidden');
            menuIcon?.classList.add('block');
            closeIcon?.classList.remove('block');
        }
    });
});
</script>