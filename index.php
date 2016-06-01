<?php

/*

  Run 'php -S localhost:8080' in this directory

*/


include 'dispatch.php';


$routes = array(
  '/about' =>
    function($url, $route, $data) {
      print 'Matt is a really cool guy';
    },

  '/user/:name' =>
    function($url, $route, $data) {
      print 'Hi there '.ucfirst($data['name']);
    },

  '/stuff/*' => 'var_dump',

  '/' =>
    function($url, $route, $data) {
      print 'Index page';
    },

  '*' =>
    function($url, $route, $data) {
      print 'Error 404: '.$url.' is an undefined route';
    },
);


$url = '/';
if(isset($_SERVER['PATH_INFO']))
  $url = $_SERVER['PATH_INFO'];


ob_start();
\MD\Dispatch\dispatch($url, $routes);
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
