<?php

/*

  Run 'php -S localhost:8080' in this directory

*/


include 'dispatch.php';


$routes = array(
  '/about' => array(
    'method' => array('GET'),
    'handler' => function($url, $route, $data) {
      print 'Matt is a really cool guy';
    },
  ),

  '/user/:name' => array(
    'method' => array('*'),
    'handler' => function($url, $route, $data) {
      print 'Hi there '.ucfirst($data['name']);
    },
  ),

  '/insecure/:password' => array( // If using GET then '*' route will run instead
    'method' => array('POST'),
    'handler' => 'var_dump',
  ),

  '/stuff/*' => array(
    'method' => array('*'),
    'handler' => 'var_dump',
  ),

  '/' => array(
    'method' => array('GET'),
    'handler' => function($url, $route, $data) {
      print 'Index page';
    },
  ),

  '*' => array(
    'method' => array('GET'),
    'handler' => function($url, $route, $data) {
      print 'Error 404: '.$url.' is an undefined route';
    },
  ),
);


$url = '/';
if(isset($_SERVER['PATH_INFO']))
  $url = $_SERVER['PATH_INFO'];

$method = $_SERVER['REQUEST_METHOD'];


ob_start();
\MD\Dispatch\dispatch($url, $method, $routes);
$content = ob_get_clean();

?>
<ul>
  <li><a href="/about">About</a></li>
  <li><a href="/user/matt">Profile</a></li>
  <li><a href="/stuff/oh/my/god">Stuff</a></li>
  <li><a href="/">Index</a></li>
  <li><a href="/wow/awesome">404</a></li>
</ul>
<?php

print $content;
