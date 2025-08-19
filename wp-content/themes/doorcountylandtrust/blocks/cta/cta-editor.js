(function() {
    const { registerBlockType } = wp.blocks;
    const { createElement: el } = wp.element;
    const { __ } = wp.i18n;

    registerBlockType('dclt/cta', {
        title: __('DCLT Call to Action'),
        description: __('Conversion-focused CTA section with multiple layouts'),
        icon: 'megaphone',
        category: 'design',
        keywords: ['cta', 'button', 'action', 'conversion', 'landowner'],
        
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
                setAttributes({ blockId: 'cta_' + Date.now() });
            }

            return el('div', 
                {
                    className: 'dclt-cta-block-editor',
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
                    '📢 Door County Land Trust CTA Section'
                ),
                el('p', 
                    { style: { margin: '0 0 15px 0', color: '#666' } },
                    'Configure your call-to-action settings in the "CTA Block Settings" meta box below the editor.'
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
