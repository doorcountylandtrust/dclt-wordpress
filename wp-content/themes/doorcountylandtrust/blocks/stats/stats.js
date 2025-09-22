/**
 * Door County Land Trust - Stats Block Animations
 * File: blocks/stats/stats.js
 * Handles scroll-triggered animations for conservation impact stats
 */

(function() {
    'use strict';

    // Check for reduced motion preference
    const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    
    if (prefersReducedMotion) {
        // Skip animations but ensure content is visible
        document.addEventListener('DOMContentLoaded', function() {
            const statsBlocks = document.querySelectorAll('[data-stats-animate]');
            statsBlocks.forEach(block => {
                block.setAttribute('data-animated', 'true');
            });
        });
        return;
    }

    function triggerStatsAnimation(statsBlock) {
        if (statsBlock.hasAttribute('data-animated')) {
            return; // Already animated
        }

        const cards = statsBlock.querySelectorAll('.stat-card');
        
        // Set animated attribute to trigger CSS animations
        statsBlock.setAttribute('data-animated', 'true');
        
        // Trigger individual card animations with staggered delays
        cards.forEach((card, index) => {
            const delay = parseInt(card.dataset.delay) || (index * 200);
            
            setTimeout(() => {
                card.style.transition = 'opacity 0.6s ease-out, transform 0.6s ease-out';
                card.style.opacity = '1';
                card.style.transform = 'translateY(0)';
            }, delay);
        });
    }

    function initializeStatsAnimations() {
        const statsBlocks = document.querySelectorAll('[data-stats-animate]');
        
        if (statsBlocks.length === 0) return;

        // Create intersection observer for scroll-triggered animations
        const observerOptions = {
            threshold: 0.3,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    triggerStatsAnimation(entry.target);
                    // Unobserve after animation to prevent re-triggering
                    observer.unobserve(entry.target);
                }
            });
        }, observerOptions);

        // Observe all stats blocks
        statsBlocks.forEach(block => {
            observer.observe(block);
        });

        // Fallback: trigger animation if user doesn't scroll (e.g., block is already in view)
        setTimeout(() => {
            statsBlocks.forEach(block => {
                const rect = block.getBoundingClientRect();
                const isVisible = rect.top < window.innerHeight && rect.bottom > 0;
                
                if (isVisible && !block.hasAttribute('data-animated')) {
                    triggerStatsAnimation(block);
                }
            });
        }, 1000);
    }

    // Manual trigger function (useful for testing)
    window.dcltTriggerStatsAnimation = function() {
        const statsBlocks = document.querySelectorAll('[data-stats-animate]');
        statsBlocks.forEach(block => {
            // Reset animation state
            block.removeAttribute('data-animated');
            const cards = block.querySelectorAll('.stat-card');
            cards.forEach(card => {
                card.style.opacity = '0';
                card.style.transform = 'translateY(30px)';
            });
            
            // Trigger animation after brief delay
            setTimeout(() => triggerStatsAnimation(block), 100);
        });
    };

    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initializeStatsAnimations);
    } else {
        initializeStatsAnimations();
    }

    // Re-initialize on dynamic content changes (if using AJAX)
    document.addEventListener('dclt:stats-refresh', initializeStatsAnimations);

})();

/**
 * Optional: Add this to your browser console to test animations:
 * dcltTriggerStatsAnimation()
 */