<?php

namespace MD\Dispatch;

/*

  Copyright 2016 Matt Dwyer
  This software is licensed under the terms of the MIT license.


  The dispatch() function takes an an array of route => callback
  pairs where 'route' is something like this:
    /about
    /user/:name
    /
    *
    /search/*
  and 'callback' is something that passes is_callable() and has
  this signature: function($url, $method, $route, $data) { }

  When a route contains ':whatever' segments the value provided
  in that position will be put into $data['whatever'] ie:
    /user/:name          ->  $data['name']
    /post/:id            ->  $data['id']              
    /search/:type/:term  ->  $data['type'] and $data['term']

*/


function parseURL($url) {
  return explode('/', trim($url, '/'));
}

function dispatch($url, $method, $routes) {
  $urlSegments = parseURL($url);
  $numUrlSegments = count($urlSegments);

  foreach($routes as $route => $data) {
    if(!in_array($method, $data['method']) && $data['method'][0] !== '*')
      continue;

    $routeSegments = parseURL($route);
    $numRouteSegments = count($routeSegments);

    $segmentData = array(); // Where * and :xxx data gets put
    foreach($urlSegments as $index => $value) {
      if($index >= $numRouteSegments - 1 && $routeSegments[$numRouteSegments - 1] === '*') {
        $segmentData[] = $value; // Route ends with * so push the rest of the URL
      }
      else {
        $segment = $routeSegments[$index];
        if($segment !== '' && $segment[0] === ':') {
          $segmentData[substr($routeSegments[$index], 1)] = $value; // Variable segment getting filled
        }
        else if($value !== $segment) {
          continue 2; // Route stops matching URL, skip to next route
        }
        else if($numRouteSegments > $numUrlSegments && $routeSegments[$index + 1] !== '*') {
          continue 2; // URL ends early and the last segment isn't an * (meaning $data can't be empty)
        }
      }
    }

    if(!is_callable($data['handler']))
      throw new \RuntimeException('Function for '.$route.' is not callable');

    $data['route'] = $route;
    $data['handler']($url, $method, $data, $segmentData);

    return;
  }

  // If execution gets here then ...
  throw new \RuntimeException('No matching '.$method.' route for '.$url);
}
