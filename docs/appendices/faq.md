# Frequently Asked Questions

## I have no language options in the General settings. ##

You might read first [WordPress in your language](http://codex.wordpress.org/WordPress_in_Your_Language).

## But I'd like the interface to stay in English. ##

You can choose the language of every website and the dashboard in the settings page of the plugin.

## Do I really need a multisite? ##

It's up to you - of course. But yes, if you want to use the Multisite Language Switcher.

## How can I automatically redirect users based on the browser language? ##

The Multisite Language Switcher does not redirect the users automatically. I'm not sure if the plugin should do that. You might check out this [jQuery plugin](
https://github.com/danieledesantis/jquery-language-detection) or [this approach with a theme](https://github.com/oncleben31/Multisite-Language-Switcher-Theme) 
if you need such functionality.

## How can I add the Multisite Language Switcher to the nav-menu of my blog? ##

Please check this [plugin](https://wordpress.org/plugins/mslsmenu/) out.

## I don't want to upload the same media files for every site. What can I do? ##

You could try the plugin [Network Shared Media](http://wordpress.org/plugins/network-shared-media/). It adds a new tab to the "Add Media" window, allowing you to access the media files in the other sites in your multisite.

## Is there a function I can call to get the language of the page the user is currently viewing? ##

Yes, you can get the language like that

	$blog     = MslsBlogCollection::instance()->get_current_blog();
	$language = $blog->get_language();

## How can I move from WPML to MSLS? ##

There is a [plugin](http://wordpress.org/plugins/wpml2wpmsls/) which comes handy in here.

## Has MSLS something like _icl_object_id_ in WPML?

**Short answer:** No.

**Long answer:** I'm not sure if the Multisite Language Switcher has really to deal with that. I've seen some customized WordPress-themes which had some old-fashioned static functionality to create content in the sidebar. In this case you could need something like that (or you could modify something in your theme). 

Here is an example how you can request the category_id in the current blog when you know just the ID of the category of the standard blog:

    function my_get_category_id( $id ) {
        if ( class_exists( 'MslsBlogCollection' ) ) {
            $blogs = MslsBlogCollection::instance();
            if ( $blogs->get_current_blog_id() != BLOG_ID_CURRENT_SITE ) {
                $language = $blogs->get_current_blog()->get_language();
                switch_to_blog( BLOG_ID_CURRENT_SITE );
                $mydata = new MslsCategoryOptions( $id );
                $id = $mydata->__get( $language );
                restore_current_blog();
            }
        }
        return $id;
    }
