<?php
/* Create simplified custom routes, permalinks, and template connections easily.
 * Requires you to have the "Pretty Permalinks" turned on in WordPress
 * Please resave (or flush) your permalinks after adding new routes
*/

namespace WPStudioCode\WPS_Router;
use WP_Error as WP_Error;

define( 'ABSPATH' ) or die();

class WPSRouter {
  
  /* Basefoleder where templates are stored
   * @access private */
  private $folder;
  
  /* Routes
   * @access private */
  private $routes;
  
  /* Permalink structure
   * @access public */
  public $structure;
  
  /* Query-variables for template matching
   * @access private */
  private $queryVar;
  
  /* Construct routes and custom query variables
   * @param array $routes - Routes array
   * @param string $folder - Folder containing templates
   * @param string $queryVar - Query variable used to identify template
  */
  public function __construct( Array $routes = [], $folder = 'templates', $queryVar = 'template' ) {
    
    /* Initial variables */
    $this->folder = apply_filters('wp_config_template_folder', $folder);
    $this->routes = $routes;
    $this->structure = get_option('permalink_structure');
    $this->queryVar = apply_filters('wp_config_query_vars', $queryVar);
    
    /* Custom Query Vars */
    $queryVar = $this->queryVar;
    add_filter( 'query_vars', function( $vars ) use( $queryVar ){
      if( is_array($queryVar) ){
        $vars = array_merge( $vars, $queryVar );
      } else {
        array_push( $vars, $query_var );
      }
      return $vars;
    }, 10, 1 );
    
    /* Add rewrites */
    if( $this->structure ){
      $this->rewrite();
    }
    
    /* Find Template */
    $this->locate();
  }
  
  /* Add required rewrites for routes */
  private function rewrite(){
    $routes = $this->routes;
    $structure = $this->structure;
    $queryVar = $this->queryVar;
    
    /* Add rewrite rules and watch for prefix */
    add_action( 'init', function() use( $routes, $queryVar, $structure ){
      $prefix = '';
      if( preg_match('/(?U)(.*)(\/%.*%\/)/', $structure, $matches) ){
        if( !empty($matches[1]) )
          $prefix = str_replace('/', '', $matches[1]) . '/';
      }
      
      /* Register custom routes */
      foreach( $routes as $name => $properties) {
        if( isset($properties['route']) )
          add_rewrite_rule( $prefix . $properties['route'] . '?$', 'index.php?' . $queryVar . '=' . $name, 'top' );
      }
    });
  }
  
  /* Find Template */
  private function locate(){
    $folder = $this->folder;
    $queryVar = $this->queryVar;
    
    add_filter( 'template_include', function( $template ) use( $folder, $queryVar ){
      $name = get_query_var( $queryVar );
      if( ! $name ){
        return $template;
      }
      
      /* absolute path for folder can also be used */
      if( strpos( $folder, ABSPATH ) !== false ){
        $template = file_exists( $folder . '/' . $name . '.php' ) ? $folder . '/' . $name . '.php' : false;
      } else {
        $template = locate_template( $folder . '/' . $name . '.php' );
      }
      
      /* Set query vars for custom page */
      global $wp_query;
      $wp_query->is_404 = false;
      $wp_query->is_custom = true;
      
      if( ! $template ){
        $error = new WP_Error(
          'missing_template',
          sprintf(__('The file for the template %s does not exist', 'wps-router'), '<b>' . $name . '</b>')
        );
        echo $error->get_error_message();
      }
      return apply_filters('wp_router_template', $template);
    } );
    
    /* page title for custom templates */
    $routes = $this->routes;
    add_filter( 'document_title_parts', function( $title ) use ( $routes, $queryVar ) {
      $name = get_query_var( $queryVar );
      if( $name && isset($routes[$name]['title']) ){
        $title['title'] = $routes[$name]['title'];
      }
      return $title;
    });
    
    /* body class to custom template */
    add_filter( 'body_class', function( $classes ) use($queryVar) {
      $name = get_query_var( $queryVar);
      if($name)
        $classes[] = 'template-' . $name;
      return $classes;
    });
  }
}
