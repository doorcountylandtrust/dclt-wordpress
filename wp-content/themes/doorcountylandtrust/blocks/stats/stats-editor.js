( function( blocks, i18n, element ) {
  const el = element.createElement;
  blocks.registerBlockType('dclt/stats', {
    title: 'DCLT Stats',
    icon: 'chart-bar',
    category: 'widgets',
    edit: () => el('div', { className:'components-placeholder is-large' }, 'Door County Land Trust — Stats Block (configure in “Stats Block Settings” meta box below).'),
    save: () => null // server render
  });
})( window.wp.blocks, window.wp.i18n, window.wp.element );