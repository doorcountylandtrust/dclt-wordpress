(function() {
    'use strict';

    const { registerBlockType } = wp.blocks;
    const { createElement: el } = wp.element;

    registerBlockType('dclt/feature-grid', {
        title: 'DCLT Feature Grid',
        icon: 'grid-view',
        category: 'layout',
        description: 'Display features, services, or benefits in a responsive grid layout',
        keywords: ['features', 'grid', 'services', 'dclt'],

        attributes: {
            blockId: {
                type: 'string',
                default: ''
            }
        },

        edit: function(props) {
            return el('div', {
                className: 'dclt-block-placeholder',
                style: {
                    background: '#f0fdf4',
                    border: '2px dashed #065f46',
                    borderRadius: '12px',
                    padding: '40px 20px',
                    textAlign: 'center',
                    minHeight: '200px',
                    display: 'flex',
                    flexDirection: 'column',
                    justifyContent: 'center',
                    alignItems: 'center'
                }
            }, [
                el('div', {
                    style: {
                        fontSize: '48px',
                        marginBottom: '16px',
                        color: '#065f46'
                    }
                }, '⚡'),
                el('h3', {
                    style: {
                        color: '#065f46',
                        marginBottom: '12px',
                        fontSize: '24px',
                        fontWeight: 'bold'
                    }
                }, 'DCLT Feature Grid'),
                el('p', {
                    style: {
                        color: '#047857',
                        fontSize: '16px',
                        maxWidth: '400px',
                        lineHeight: '1.5',
                        margin: '0 auto 16px'
                    }
                }, 'Showcase your conservation work, services, or features in a beautiful responsive grid. Configure this block using the meta box below the editor.'),
                el('div', {
                    style: {
                        background: '#065f46',
                        color: 'white',
                        padding: '8px 16px',
                        borderRadius: '6px',
                        fontSize: '14px',
                        fontWeight: '500'
                    }
                }, '📝 Configure in "Feature Grid Block Settings" below')
            ]);
        },

        save: function() {
            // Server-side rendering - return null
            return null;
        }
    });

})();