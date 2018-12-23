# Integration in your website

## Using the widget

As described in the section [Editing association](/user-docs/editing-association-contents.html) of posts, besides indicating for each post or page the available translations, the widget can be integrated in any dynamic sidebar.

![Widgets admin](/widgets-admin.png)

The output depends on your settings in the plugin configuration and on the CSS of your theme but here an example:

![Widget output](/widget-output.png)

## Using the shortcodes

The **Multisite Language Switcher** comes with two shortcodes:

    [sc_msls]

You can insert it into your posts or pages when you want to show a link to the alternative translation of the current content.

    [sc_msls_widget]

This is a shortcode that renders the widget output because there are themes that you widgets also in that way. 

## Using the block

The last shortcode is also available as a block in the new Gutenberg editor. This is a first step because the UI is right now far from perfect.

![Block editor](/block-editor.png)

## Using the API 

It is also possible to directly use the plugin features in your theme, you can use for example the following code in the *header.php* file of your WordPress theme.

    <?php if ( function_exists( 'the_msls' ) ) the_msls(); ?>