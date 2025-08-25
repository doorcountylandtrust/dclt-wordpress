/**
 * Shared Block JavaScript
 * File: blocks/shared/blocks.js
 */

(function($) {
    'use strict';

    /**
     * Initialize all block functionality
     */
    function initBlocks() {
        initFormTriggers();
        initAccessibility();
        initPerformanceOptimizations();
    }

    /**
     * Form trigger functionality for CTA blocks
     */
    function initFormTriggers() {
        $('.dclt-form-trigger').on('click', function(e) {
            e.preventDefault();
            
            const $button = $(this);
            const formId = $button.data('form-id');
            const formType = $button.data('form-type');
            
            // Show loading state
            const originalText = $button.text();
            $button.text('Loading...').prop('disabled', true);
            
            // Here you would integrate with your Salesforce form system
            // This is a placeholder for your actual form handling
            openSalesforceForm(formId, formType, function() {
                // Reset button state
                $button.text(originalText).prop('disabled', false);
            });
        });
    }

    /**
     * Placeholder for Salesforce form integration
     */
    function openSalesforceForm(formId, formType, callback) {
        // TODO: Implement your Salesforce form integration here
        // This could be a modal, redirect, or embedded form
        
        console.log('Opening Salesforce form:', { formId, formType });
        
        // For now, just show a simple alert (replace with your actual form)
        alert(`Opening ${formType} contact form (ID: ${formId})`);
        
        if (callback) callback();
    }

    /**
     * Accessibility enhancements
     */
    

    /**
     * Performance optimizations
     */
    // Keep only ONE initAccessibility() with everything inside:
function initAccessibility() {
  $('.dclt-button, .dclt-form-trigger').on('keydown', function(e) {
    if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); $(this).click(); }
  });
  $('.counter-number').attr('aria-live', 'polite');
  $('.feature-item').each(function(index) {
    $(this).attr('aria-labelledby', `feature-${index}-title`);
    $(this).find('h3').attr('id', `feature-${index}-title`);
  });
  // hero ARIA IDs
  $('.dclt-hero-block').each(function(index) {
    $(this).attr('aria-labelledby', `hero-${index}-title`);
    $(this).find('h1').attr('id', `hero-${index}-title`);
  });
}

    // Add ARIA labels where needed
    $('.counter-number').attr('aria-live', 'polite');
    
    // Improve screen reader experience for feature grids
    $('.feature-item').each(function(index) {
        $(this).attr('aria-labelledby', `feature-${index}-title`);
        $(this).find('h3').attr('id', `feature-${index}-title`);
    });
    
    // ADD THESE 3 LINES FOR HERO BLOCKS:
    $('.dclt-hero-block').each(function(index) {
        $(this).attr('aria-labelledby', `hero-${index}-title`);
        $(this).find('h1').attr('id', `hero-${index}-title`);
    });
}
    /**
     * Utility function for tracking analytics events
     */
    window.dcltTrackEvent = function(category, action, label, value) {
        // Google Analytics tracking (if implemented)
        if (typeof gtag !== 'undefined') {
            gtag('event', action, {
                event_category: category,
                event_label: label,
                value: value
            });
        }
        
        // Plausible Analytics tracking (your preferred choice)
        if (typeof plausible !== 'undefined') {
            plausible(action, {
                props: {
                    category: category,
                    label: label,
                    value: value
                }
            });
        }
        
        console.log('Event tracked:', { category, action, label, value });
    };

    /**
     * Form submission tracking
     */
    $(document).on('submit', 'form', function() {
        const formType = $(this).data('form-type') || 'general';
        dcltTrackEvent('Form', 'Submit', formType);
    });

    /**
     * CTA click tracking
     */
    $(document).on('click', '.dclt-button, .dclt-form-trigger', function() {
        const buttonText = $(this).text().trim();
        const buttonType = $(this).hasClass('dclt-form-trigger') ? 'Form Trigger' : 'Link';
        dcltTrackEvent('CTA', 'Click', `${buttonType}: ${buttonText}`);
    });

    // Initialize when document is ready
    $(document).ready(initBlocks);

})(jQuery);

/**
 * Directory Structure Setup Guide
 * 
 * Create this folder structure in your theme:
 * 
 * doorcountylandtrust/
 * ├── blocks/
 * │   ├── shared/
 * │   │   ├── blocks.css
 * │   │   └── blocks.js (this file)
 * │   ├── hero/
 * │   │   ├── hero.php
 * │   │   ├── hero.css
 * │   │   └── hero.js
 * │   ├── cta/
 * │   │   └── cta.php
 * │   ├── feature-grid/
 * │   │   └── feature-grid.php
 * │   ├── stats/
 * │   │   ├── stats.php
 * │   │   └── stats.js
 * │   └── faq/ (coming next)
 * │       └── faq.php
 * ├── inc/
 * │   └── blocks.php (your registration functions)
 * └── functions.php
 */

/**
 * Next Steps Checklist:
 * 
 * 1. ✅ Create the directory structure above
 * 2. ✅ Add the block registration code to functions.php or inc/blocks.php
 * 3. ✅ Create the ACF field groups (can be done via WordPress admin)
 * 4. ✅ Test Hero block on a test page
 * 5. ✅ Test CTA block with different layouts
 * 6. ✅ Test Feature Grid with your "Ways to Help" content
 * 7. ✅ Test Stats block with your impact numbers
 * 8. 🔲 Build FAQ block for landowner myth-busting
 * 9. 🔲 Build Process Steps block for landowner journey
 * 10. 🔲 Integrate with Salesforce forms
 * 11. 🔲 Set up Preserve Explorer styling integration
 * 12. 🔲 Build homepage using these blocks
 * 13. 🔲 Build landowner hub pages
 * 14. 🔲 Accessibility audit and testing
 * 15. 🔲 Performance optimization
 */

/**
 * Salesforce Integration Planning
 * 
 * You'll need to replace the openSalesforceForm function above with your actual integration.
 * Common approaches:
 * 
 * 1. Modal Forms: Load Salesforce form in a modal overlay
 * 2. Inline Forms: Embed Salesforce forms directly in the page
 * 3. API Integration: Submit to Salesforce via REST API
 * 4. Web-to-Lead: Use Salesforce Web-to-Lead forms
 * 
 * The form triggers are set up to pass:
 * - Form ID (for different form types)
 * - Form Type (landowner, donor, volunteer, general)
 * - Source tracking (which page/CTA triggered the form)
 */

/**
 * Performance Monitoring
 */
function initPerformanceMonitoring() {
    // Core Web Vitals tracking for Plausible
    if (typeof plausible !== 'undefined') {
        // Largest Contentful Paint
        new PerformanceObserver((entryList) => {
            for (const entry of entryList.getEntries()) {
                plausible('LCP', { props: { value: Math.round(entry.startTime) } });
            }
        }).observe({ entryTypes: ['largest-contentful-paint'] });

        // First Input Delay
        new PerformanceObserver((entryList) => {
            for (const entry of entryList.getEntries()) {
                plausible('FID', { props: { value: Math.round(entry.processingStart - entry.startTime) } });
            }
        }).observe({ entryTypes: ['first-input'] });
    }
}

// Initialize performance monitoring
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initPerformanceMonitoring);
} else {
    initPerformanceMonitoring();
}