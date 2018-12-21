var el = wp.element.createElement,
  registerBlockType = wp.blocks.registerBlockType,
  ServerSideRender = wp.components.ServerSideRender,
  TextControl = wp.components.TextControl,
  InspectorControls = wp.editor.InspectorControls;

registerBlockType( 'lloc/msls-widget-block', {
  title: 'Multisite Language Switcher',
  icon: 'translation',
  category: 'widgets',

  edit: function( props ) {
    return [
      el( ServerSideRender, {
        block: 'lloc/msls-widget-block',
        attributes: props.attributes,
      } ),
      el( InspectorControls, {},
        el( TextControl, {
          label: 'Title',
          value: props.attributes.title,
          onChange: ( value ) => { props.setAttributes( { title: value } ); },
        } )
      ),
    ];
  },
  save: function() {
    return null;
  },
} );
