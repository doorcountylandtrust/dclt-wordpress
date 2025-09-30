/**
 * Hero Block JavaScript
 * File: blocks/hero/hero.js
 */

document.addEventListener('DOMContentLoaded', function() {
    // Video background handling
    const heroBlocks = document.querySelectorAll('.dclt-hero-block[data-background-type="video"]');
    
    heroBlocks.forEach(block => {
        const video = block.querySelector('video');
        if (video) {
            // Ensure video plays on mobile
            video.setAttribute('playsinline', true);
            video.setAttribute('webkit-playsinline', true);
            
            // Handle video loading
            video.addEventListener('loadeddata', function() {
                this.play().catch(e => {
                    console.log('Video autoplay failed:', e);
                });
            });
        }
    });
    
    // Apply load-based animation class for hero images
    const heroImagesBlocks = document.querySelectorAll('.dclt-hero-block[data-background-type="image"]');
    
    heroImagesBlocks.forEach(block => {
        const img = block.querySelector('img');
        if (img) {
            img.classList.add('animate');
        }
    });
});
