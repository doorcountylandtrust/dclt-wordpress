/**
 * Hero Block Editor JavaScript
 * File: blocks/hero/hero-editor.js
 */

(function() {
    const { registerBlockType } = wp.blocks;
    const { createElement: el } = wp.element;
    const { __ } = wp.i18n;

    registerBlockType('dclt/hero', {
        title: __('DCLT Hero Section'),
        description: __('Curved hero section with background and CTAs'),
        icon: 'cover-image',
        category: 'layout',
        keywords: ['hero', 'banner', 'cta', 'curved'],
        
        attributes: {
            blockId: {
                type: 'string',
                default: ''
            }
        },

        edit: function(props) {
            const { attributes, setAttributes } = props;

            // Generate unique block ID if not set
            if (!attributes.blockId) {
                setAttributes({ blockId: 'hero_' + Date.now() });
            }

            return el('div',
                {
                    className: 'dclt-hero-block-editor',
                    style: {
                        position: 'relative',
                        minHeight: '400px',
                        display: 'flex',
                        alignItems: 'center',
                        justifyContent: 'center',
                        padding: '40px 20px',
                        border: '2px dashed #1F4421',
                        borderRadius: '8px',
                        background: 'linear-gradient(135deg, rgba(31, 68, 33, 0.1) 0%, rgba(74, 124, 149, 0.1) 100%)',
                        overflow: 'hidden'
                    }
                },
                el('div',
                    {
                        style: {
                            textAlign: 'center',
                            maxWidth: '600px',
                            zIndex: '10'
                        }
                    },
                    el('div',
                        {
                            style: {
                                fontSize: '48px',
                                marginBottom: '16px'
                            }
                        },
                        '🏞️'
                    ),
                    el('h3',
                        {
                            style: {
                                margin: '0 0 12px 0',
                                color: '#1F4421',
                                fontSize: '24px',
                                fontWeight: '700'
                            }
                        },
                        'DCLT Hero Section'
                    ),
                    el('p',
                        {
                            style: {
                                margin: '0 0 20px 0',
                                color: '#5C5F58',
                                fontSize: '16px',
                                lineHeight: '1.6'
                            }
                        },
                        'Configure your hero section with headline, intro, CTAs, and media slideshow in the "Hero Block Settings" meta box below the editor.'
                    ),
                    el('div',
                        {
                            style: {
                                display: 'inline-block',
                                padding: '8px 16px',
                                background: 'rgba(31, 68, 33, 0.1)',
                                borderRadius: '4px',
                                fontSize: '13px',
                                color: '#1F4421',
                                fontFamily: 'monospace'
                            }
                        },
                        'Block ID: ' + attributes.blockId
                    )
                )
            );
        },

        save: function() {
            // Return null because we're using PHP render callback
            return null;
        }
    });
})();