/**
 * Hero Block JavaScript
 * File: blocks/hero/hero.js
 */

document.addEventListener('DOMContentLoaded', function() {
    const heroBlocks = document.querySelectorAll('.dclt-hero-block');
    const prefersReduce = window.matchMedia('(prefers-reduced-motion: reduce)');

    const addMqListener = (mq, handler) => {
        if (!mq) { return () => {}; }
        if (typeof mq.addEventListener === 'function') {
            mq.addEventListener('change', handler);
            return () => mq.removeEventListener('change', handler);
        }
        if (typeof mq.addListener === 'function') {
            mq.addListener(handler);
            return () => mq.removeListener(handler);
        }
        return () => {};
    };

    const scheduleImageAnimation = root => {
        if (!root) { return; }
        let attempts = 0;
        const maxAttempts = 15;

        const tryAnimate = () => {
            if (!root.isConnected) { return; }
            const img = root.querySelector('img.hero-media-fade');
            if (!img) {
                if (attempts < maxAttempts) {
                    attempts += 1;
                    requestAnimationFrame(tryAnimate);
                }
                return;
            }

            if (!img.classList.contains('hero-media-fade')) {
                img.classList.add('hero-media-fade');
            }

            if (prefersReduce.matches) {
                img.classList.remove('animate');
                return;
            }

            requestAnimationFrame(() => {
                img.classList.add('animate');
            });
        };

        requestAnimationFrame(tryAnimate);
    };

    const bindCreditBlock = block => {
        if (!block) { return null; }
        const creditTextEl = block.querySelector('.hero-credit');
        const toggle = block.querySelector('.hero-photo-note-toggle');
        const notePanel = block.querySelector('.hero-photo-note');
        const noteTextEl = notePanel ? notePanel.querySelector('.hero-photo-note-text') : null;
        const noteLinkWrap = notePanel ? notePanel.querySelector('.hero-photo-note-link') : null;
        const noteLink = noteLinkWrap ? noteLinkWrap.querySelector('a') : null;

        let removeDocClick = null;
        let removeDocKey = null;

        const removeDocumentListeners = () => {
            if (removeDocClick) {
                document.removeEventListener('click', removeDocClick, true);
                removeDocClick = null;
            }
            if (removeDocKey) {
                document.removeEventListener('keydown', removeDocKey);
                removeDocKey = null;
            }
        };

        const setExpanded = expanded => {
            if (!toggle || !notePanel) { return; }
            toggle.setAttribute('aria-expanded', expanded ? 'true' : 'false');
            notePanel.hidden = !expanded;
            block.classList.toggle('is-open', expanded);

            removeDocumentListeners();
            if (expanded) {
                removeDocClick = event => {
                    if (!block.contains(event.target)) {
                        setExpanded(false);
                    }
                };
                removeDocKey = event => {
                    if (event.key === 'Escape') {
                        setExpanded(false);
                        toggle.focus();
                    }
                };
                document.addEventListener('click', removeDocClick, true);
                document.addEventListener('keydown', removeDocKey);
            }
        };

        if (toggle && notePanel) {
            setExpanded(false);
            toggle.addEventListener('click', () => {
                const expanded = toggle.getAttribute('aria-expanded') === 'true';
                setExpanded(!expanded);
            });
        }

        if (notePanel) {
            const closeButton = notePanel.querySelector('.hero-photo-note-close');
            if (closeButton) {
                closeButton.addEventListener('click', () => {
                    setExpanded(false);
                    if (toggle) { toggle.focus(); }
                });
            }
        }

        const update = slide => {
            const creditText = slide ? (slide.getAttribute('data-slide-credit') || '') : '';
            const noteRaw = slide ? (slide.getAttribute('data-slide-note') || '') : '';
            const website = slide ? (slide.getAttribute('data-slide-website') || '') : '';
            let noteText = '';
            if (noteRaw) {
                try {
                    noteText = JSON.parse(noteRaw);
                } catch (err) {
                    noteText = noteRaw;
                }
            }
            const hasNote = !!noteText;

            if (creditTextEl) {
                creditTextEl.textContent = creditText;
                creditTextEl.hidden = creditText === '';
            }
            if (toggle) {
                toggle.hidden = !hasNote;
                toggle.setAttribute('aria-hidden', hasNote ? 'false' : 'true');
                toggle.setAttribute('aria-expanded', 'false');
            }
            if (notePanel) {
                notePanel.hidden = !hasNote;
            }
            if (noteTextEl) {
                noteTextEl.innerHTML = hasNote ? noteText.replace(/\n/g, '<br>') : '';
            }
            if (noteLinkWrap) {
                if (website) {
                    noteLinkWrap.hidden = false;
                    if (noteLink) {
                        noteLink.href = website;
                    }
                } else {
                    noteLinkWrap.hidden = true;
                }
            }
            setExpanded(false);
        };

        const cleanup = () => {
            removeDocumentListeners();
        };

        return { update, setExpanded, cleanup, toggle, notePanel };
    };

    const bindVideoSlide = (slide, prefersReduceWatcher) => {
        const video = slide.querySelector('video');
        if (!video || video.dataset.heroBound === '1') { return; }
        video.dataset.heroBound = '1';
        video.setAttribute('playsinline', 'true');
        video.setAttribute('webkit-playsinline', 'true');
        video.muted = true;
        video.loop = true;
        video.preload = 'metadata';

        const fallbackUrl = slide.getAttribute('data-slide-fallback') || '';

        const replaceWithFallback = () => {
            if (!video.parentElement) { return; }
            if (fallbackUrl) {
                const fallbackImg = document.createElement('img');
                fallbackImg.src = fallbackUrl;
                fallbackImg.alt = '';
                fallbackImg.setAttribute('aria-hidden', 'true');
                fallbackImg.className = 'hero-media-fallback hero-media-fade';
                fallbackImg.loading = 'lazy';
                video.replaceWith(fallbackImg);
                slide.setAttribute('data-slide-type', 'image');
                scheduleImageAnimation(slide);
            } else {
                video.removeAttribute('autoplay');
                video.pause();
            }
        };

        if (prefersReduceWatcher.matches) {
            replaceWithFallback();
            return;
        }

        const onLoadedData = () => {
            if (slide.classList.contains('is-active')) {
                video.play().catch(() => {});
            }
        };
        video.addEventListener('loadeddata', onLoadedData);

        const onVideoError = () => {
            replaceWithFallback();
            video.removeEventListener('error', onVideoError);
            video.removeEventListener('stalled', onVideoError);
            video.removeEventListener('loadeddata', onLoadedData);
            if (typeof removeMotionListener === 'function') {
                removeMotionListener();
            }
        };
        video.addEventListener('error', onVideoError);
        video.addEventListener('stalled', onVideoError);

        const handleMotion = event => {
            if (event.matches) {
                replaceWithFallback();
                if (typeof removeMotionListener === 'function') {
                    removeMotionListener();
                }
            }
        };
        const removeMotionListener = addMqListener(prefersReduceWatcher, handleMotion);

        slide._videoCleanup = () => {
            video.removeEventListener('loadeddata', onLoadedData);
            video.removeEventListener('error', onVideoError);
            video.removeEventListener('stalled', onVideoError);
            if (typeof removeMotionListener === 'function') {
                removeMotionListener();
            }
        };
    };

    heroBlocks.forEach(block => {
        const slides = Array.from(block.querySelectorAll('.hero-slide'));
        const creditBlock = block.querySelector('.photo-credit-block');
        const creditApi = bindCreditBlock(creditBlock);

        if (!slides.length) {
            if (creditApi) {
                creditApi.update(null);
            }
            return;
        }

        const intervalAttr = parseInt(block.getAttribute('data-slideshow-interval') || '7000', 10);
        const autoplayAttr = block.getAttribute('data-slideshow-enabled') === '1';
        let currentIndex = slides.findIndex(slide => slide.classList.contains('is-active'));
        if (currentIndex < 0) { currentIndex = 0; }

        slides.forEach(slide => {
            if (slide.getAttribute('data-slide-type') === 'video') {
                bindVideoSlide(slide, prefersReduce);
            }
        });

        const setActive = index => {
            if (!slides.length) return;
            const normalizedIndex = ((index % slides.length) + slides.length) % slides.length;

            slides.forEach((slide, slideIndex) => {
                const isActive = slideIndex === normalizedIndex;
                const wasActive = slide.classList.contains('is-active');

                if (isActive && !wasActive) {
                    // Becoming active
                    slide.classList.add('is-active');
                    slide.classList.remove('was-active');
                } else if (!isActive && wasActive) {
                    // Becoming inactive
                    slide.classList.remove('is-active');
                    slide.classList.add('was-active');
                    setTimeout(() => slide.classList.remove('was-active'), 1000);
                }

                // Video handling
                const video = slide.querySelector('video');
                if (video) {
                    if (isActive) {
                        video.play().catch(() => {});
                    } else {
                        video.pause();
                        try { video.currentTime = 0; } catch (e) {}
                    }
                }
            });

            block.setAttribute('data-background-type', slides[normalizedIndex].getAttribute('data-slide-type') || 'image');
            scheduleImageAnimation(slides[normalizedIndex]);
            if (creditApi) creditApi.update(slides[normalizedIndex]);
            currentIndex = normalizedIndex;
        };

        setActive(currentIndex);

        const canAutoPlay = () => autoplayAttr && slides.length > 1 && !prefersReduce.matches;
        let timer = null;

        const start = () => {
            if (timer || !canAutoPlay()) { return; }
            timer = setInterval(() => {
                setActive(currentIndex + 1);
            }, intervalAttr);
        };

        const stop = () => {
            if (!timer) { return; }
            clearInterval(timer);
            timer = null;
        };

        if (canAutoPlay()) {
            start();
        }

        const handleMotionChange = event => {
            if (event.matches) {
                stop();
                setActive(0);
            } else if (canAutoPlay()) {
                start();
            }
        };
        const removeMotionListener = addMqListener(prefersReduce, handleMotionChange);

        const visibilityHandler = () => {
            if (document.hidden) {
                stop();
            } else if (canAutoPlay()) {
                start();
            }
        };
        document.addEventListener('visibilitychange', visibilityHandler);

        const pageHideHandler = () => {
            stop();
            document.removeEventListener('visibilitychange', visibilityHandler);
            removeMotionListener();
            slides.forEach(slide => {
                if (slide._videoCleanup) {
                    slide._videoCleanup();
                }
            });
            if (creditApi && typeof creditApi.cleanup === 'function') {
                creditApi.cleanup();
            }
        };
        window.addEventListener('pagehide', pageHideHandler, { once: true });
    });
});