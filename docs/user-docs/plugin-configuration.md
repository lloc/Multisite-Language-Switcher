# Plugin configuration

## Choose the blog language

In order to choose the blog language, go to the administration menu, to *Settings/General* and choose the language in the select-menu.

![Choose the blog language](/choose-language.png)

::: warning Make sure
The default downloaded version of WordPress includes only one language. Make sure that you already have the WordPress files related to the languages that you want to use on the other blogs! For this to work, you have to install less .mo files concerning the languages that you want.
:::

See the following articles:

-  [​Installing WordPress in Your Language](http://codex.wordpress.org/Installing_WordPress_in_Your_Language)​
-  and [WordPress in your language](http://codex.wordpress.org/WordPress_in_Your_Language)​

## Activate the plugin

After you [downloaded the Multisite Language Switcher](https://wordpress.org/plugins/multisite-language-switcher/) plugin and extracted the .zip file, copy the entire folder onto the following sub-folder of your WordPress installation:

    wp-content/plugins/

So far the plugin installation follows the usual procedure. In a practical way, the plugin activation can then be made

-  either by the **network administrator** on all the blogs,
-  or by the **blog administrator** for each particular blog.

## Settings in the administration menu

The plugin settings are rather simple (in the **Administration Menu -> Settings -> Multisite Language Switcher**). You need to activate the plugin once in each blog : click « Save » at the bottom of the settings even if you haven't changed anything yet. It's the only way for the plugin to offer the necessary flexibility, regarding each language for example.

![Plugin settings](/settings.png)

You'll see here, similar to the mentioned options page before,  a select-menu that shows you the language set for the current website.

## Main settings

### Display

You can choose 4 kinds of displaying:

-  flag and description
-  flag only
-  description only
-  description and flag

### Sort output by description

The output can be sorted by the values that are set in the **Description** field. The sorting will be by language code otherwise.

### Display link to the current language

It is possible to include a link to the current content in the output of the plugin.

### Show only link with a translation

This option will show only existing translations. *Otherwise there will be a link to the homepage.*

### Description

This value will be used in the title-attribute of the images and in the text-links. 

![Main settings](/main-settings.png)

### Text/HTML fields

With the next 4 fields, it is possible to add some text or code HTML in order to personalise the HTML list to appear on the front end when your use the plugin.

### Add hint for available translations

Indicate to the visitor the available languages available to read the post. This is an textual output under the post. 

### Hint priority 

You can decide which priority the plugin should apply for the translation hints. The output uses the WordPress event for **the_content** and there are many plugins that change the output. You could assign a higher number for being the last function that alters the content.

## Advanced settings

![Advanced settings](/advanced-settings.png)

### Activate experimental autocomplete inputs

This will show an autocomplete input in the sidebar of the editor instead of a dropdown for available contents in the connected sites.

*I decided to let this experimental since there are issues in the taxonomies.*

### Custom URL for flag images

There are flag-images included but you can set also a **custom directory** if you have your own set.

### Reference user

**This is an important but somewhat overseen field.**

The easiest (but not the only one) way to connect websites is the user. There is no problem if you have just one user that accesses all the websites. But this option is useful if you want to connect a subset or if you have more than 1 admin.

Just set who is the reference for the connections and you are done.

### Exclude this blog from output

This option plays together with the "Reference user". You can just exclude a blog and the plugin will not do anything in that website.

### Activate the content import functionality

Activate this new functionality if you need to copy the original content with meta and media to a new translation.

## Rewrite settings

This part is mostly readonly and shows if there are any slugs translated. The pluin can save the translated slug and use it to create the correct connection.  
