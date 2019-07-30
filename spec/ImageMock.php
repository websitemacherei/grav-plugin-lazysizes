<?php

namespace Spec;

class ImageMock {
 
  public function __construct($url, $dim = [ 1000, 1000 ]) {
    $this->url = $url . '?mocked';
    [$this->width, $this->height] = $dim;
  }
 
  public function __call($method, $args) {
    return $this;
  }
 
  public function url() {
    return $this->url;
  }
}
