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
                        padding: '20px',
                        border: '2px dashed #006847',
                        borderRadius: '8px',
                        textAlign: 'center',
                        backgroundColor: '#f0f9f4'
                    }
                },
                el('h3', 
                    { style: { margin: '0 0 10px 0', color: '#006847' } },
                    '🏞️ Door County Land Trust Hero Section'
                ),
                el('p', 
                    { style: { margin: '0 0 15px 0', color: '#666' } },
                    'Configure your hero section settings in the "Hero Block Settings" meta box below the editor.'
                ),
                el('div',
                    { style: { fontSize: '14px', color: '#888' } },
                    'Block ID: ' + attributes.blockId
                )
            );
        },

        save: function() {
            // Return null because we're using PHP render callback
            return null;
        }
    });
})();