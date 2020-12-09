# wps-router
Create custom permalinks, routes, and templates to be used in WordPress plugins, themes, and child themes..

## How to Use in Project
You will need to add the wps-router.php file to your theme, plugin, or child theme. Then require the file from your functions.php file or autoload it.

## How to set a route and template
Create a new instance of the router class with an array of the router arguments.

  $router = new WPStudioCode\WPS_Router\Router(
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
   
