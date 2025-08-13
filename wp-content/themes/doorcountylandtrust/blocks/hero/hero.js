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
    
    // Parallax effect for background images (optional)
    const heroImagesBlocks = document.querySelectorAll('.dclt-hero-block[data-background-type="image"]');
    
    function handleScroll() {
        heroImagesBlocks.forEach(block => {
            const rect = block.getBoundingClientRect();
            const speed = 0.5;
            const yPos = -(rect.top * speed);
            const img = block.querySelector('img');
            
            if (img && rect.bottom >= 0 && rect.top <= window.innerHeight) {
                img.style.transform = `translateY(${yPos}px)`;
            }
        });
    }
    
    // Throttled scroll handler
    let ticking = false;
    function requestTick() {
        if (!ticking) {
            requestAnimationFrame(handleScroll);
            ticking = true;
            setTimeout(() => ticking = false, 16);
        }
    }
    
    window.addEventListener('scroll', requestTick);
});