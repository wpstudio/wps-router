# WPS-Router
Create custom permalinks, routes, and templates to be used in WordPress plugins, themes, and child themes..

## How to Use in Project
You will need to add the wps-router.php file to your theme, plugin, or child theme. Then require the file from your functions.php file or autoload it.

## How to set a route and template
Create a new instance of the router class with an array of the router arguments.
```
  $router = new WPStudioCode\WPS_Router\WPSRouter(
    [
      'muffin' => [
        'route' => muffin/,
        'title' => __('Beautiful Muffin Title')],
      'evenmore' => [
        'route' => amazroute/,
        'title' => __('Even More Awesomeness!!!')]
     ],
     'templates', //Folder the templates are stored in
     'template' //Query variable by which the template is identified (ex. get_query_var('template')) Defaults to template
   );
```
In the example above... The URL https://www.domain.com/muffin/ will use the page title "Beautiful Muffin Title" and will fetch the template muffin.php from the /templates/ folder (in either the theme or the plugin depending on where the included wps-router.php file is called from).

The additional route above takes the URL https://www.domain.com/amazroute/ and uses the page title "Even More Awesomeness!!!" and finds the template evenmore.php from the /templates/folder.
