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

    // Photo note toggles
    const creditBlocks = document.querySelectorAll('.photo-credit-block');

    creditBlocks.forEach(block => {
        const toggle = block.querySelector('.hero-photo-note-toggle');
        const note = block.querySelector('.hero-photo-note');
        if (!toggle || !note) {
            return;
        }

        const closeButton = note.querySelector('.hero-photo-note-close');

        const onDocumentClick = event => {
            if (!block.contains(event.target)) {
                setExpanded(false);
            }
        };

        const onDocumentKeydown = event => {
            if (event.key === 'Escape') {
                setExpanded(false);
                toggle.focus();
            }
        };

        const setExpanded = expanded => {
            toggle.setAttribute('aria-expanded', expanded ? 'true' : 'false');
            note.hidden = !expanded;
            block.classList.toggle('is-open', expanded);

            if (expanded) {
                document.addEventListener('click', onDocumentClick, true);
                document.addEventListener('keydown', onDocumentKeydown);
            } else {
                document.removeEventListener('click', onDocumentClick, true);
                document.removeEventListener('keydown', onDocumentKeydown);
            }
        };

        setExpanded(false);

        toggle.addEventListener('click', () => {
            const expanded = toggle.getAttribute('aria-expanded') === 'true';
            setExpanded(!expanded);
        });

        if (closeButton) {
            closeButton.addEventListener('click', () => {
                setExpanded(false);
                toggle.focus();
            });
        }
    });
});
