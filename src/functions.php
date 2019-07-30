<?php

function manipulateElement($Inline, $imageData, $config) {
    
    if(!isset($Inline)) return null;

    $attributes = &$Inline['element']['attributes'];
    
    if (isset($attributes['srcset'])) $attributes['data-srcset'] = $attributes['srcset'];
    if (isset($attributes['src'])) $attributes['data-src'] = $attributes['src'];
    
    $attributes['class'] = isset($attributes['class']) ? $attributes['class'] . ' lazyload' : 'lazyload';
    $attributes['data-sizes'] = 'auto';

    unset($attributes['srcset']);
    unset($attributes['sizes']);
    
    // remove src fallback when fallback disabled
    if (!$config['fallback']) unset($attributes['src']);

    // set fallback when config fallback==true
    if ($imageData && $config['fallback']) {
      
      [$image, $params] = $imageData;

      $fallbackSettings = is_array($config['fallback_settings']) ? $config['fallback_settings'] : []; 

      foreach($fallbackSettings as $filter) {
        applyImageFilter($image, $filter['key'], $filter['value']);
      }

      $attributes['src'] = $image->url();
    }

    return $Inline;
}

// helper

function getOriginalImage($src, $images) {

  @['basename' => $basename, 'params' => $params] = parseImageSrc($src);
  
  if (!array_key_exists($basename, $images)) {
    return null;
  }

  return [ $images[$basename], $params];
}

function parseImageSrc($src) {
  
  // destruct array
  @['path' => $path, 'query' => $query] = parse_url($src);

  // parse query params
  parse_str($query, $params);

  return [
    'basename' => basename($path), 
    'params' => is_array($params) ? $params : [], 
  ];
}

function applyImageFilters($img, $params) {
  foreach ($params as $type => $value) {
    applyImageFilter($img, $type, $value);
  }
}

function applyImageFilter($img, $type, $value) {
  try {
    $img->__call($type, [ $value ]);
  } 
  // silently fail on invalid number of arguments or a filter doesnt exist
  catch (\InvalidArgumentException $e) {}
  catch (\BadFunctionCallException $e) {}
}

function getConfigWidths($widths) {

  if(!is_array($widths)) return [];
  
  $widths = array_column($widths, 'width');

  // widths cannot be < 0
  $widths = array_filter($widths, function($w) {
    return is_int($w) && $w > 0;
  });

  // reset indexes
  return array_values($widths);
} 