<?php

use Spec\ImageMock;

describe('tests', function() {

  describe('helpers', function() {

    it('should parse image src', function () {

      // when
      $info = parseImageSrc('/foo/bar.jpeg?hello=1&world');

      // then
      expect($info['basename'])->toBe('bar.jpeg');
      expect($info['params'])->toBe([ 'hello' => '1', 'world' => '' ]);

      // when
      $info = parseImageSrc('/foo/bar.jpeg');

      // then
      expect($info['basename'])->toBe('bar.jpeg');
      expect($info['params'])->toBe([]);
    });


    it('should apply image filters', function() {

      // given
      $image = new ImageMock('/foo/bar.jpeg');
      $params = [ 'gaussianBlur' => '2', 'foo' => 'bar' ];

      // then
      expect($image)->toReceive('gaussianBlur')->with('2')->times(1);
      expect($image)->toReceive('foo')->with('bar')->times(1);

      // when
      applyImageFilters($image, $params);
    });


    it('should get original image', function() {

      // given
      $images = [ 'foo.jpg' => 'instance1', 'bar.jpg' => 'instance2' ];

      // when
      $data = getOriginalImage('bar.jpg?foo=bar', $images);

      // then
      expect($data[0])->toBe('instance2');
      expect($data[1])->toBe([ 'foo' => 'bar' ]);


      // when
      $data = getOriginalImage('/foo/bar.jpg', []);

      // then
      expect($data)->toBe(null);
    }); 


    it('should get widths > 0', function() {

      // given
      $widths = [
        [ 'width' => -100 ],
        [ 'width' => 0 ],
        [ 'width' => 200 ],
        [ 'foo' => '11' ],
        [ 'width' => '13' ],
        [ 'width' => 300. ],
        [ 'width' => 'foo' ],
        [ 'width' => 400 ],
      ];

      // when
      $widths = getConfigWidths($widths);

      // then
      expect($widths)->toBe([ 200, 400 ]);

      // when
      $widths = getConfigWidths(null);

      // then
      expect($widths)->toBe([]);
    });

  });

  describe('manipulateElement', function() {

    beforeEach(function() {
      $this->config = [ 
        'widths' => [
          [ 'width' => 480 ], 
          [ 'width' => 960 ] 
        ],
        'fallback' => 1,
        'fallback_settings' => [
          [ 'key' => 'gaussianBlur', 'value' => '2' ]
        ]
      ];
    });


    it('should set `srcset`', function() {

      // given
      $image = new ImageMock('/foo/bar.jpeg');
      $Inline = [
        'element' => [
          'attributes' => [
            'srcset' => 'bar1.jpeg 400w bar2.jpeg 800w'
          ]
        ]
      ];

      // when
      $attributes = manipulateElement($Inline, [$image, []], $this->config)['element']['attributes']; 
      
      // then
      expect($attributes['data-srcset'])->toBe('bar1.jpeg 400w bar2.jpeg 800w');
      expect($attributes['class'])->toBe('lazyload');
      expect($attributes['data-sizes'])->toBe('auto');
      expect($attributes['src'])->toBe('/foo/bar.jpeg?mocked');
    });


    it('should apply image manipulation arguments to fallback', function() {
      
      // given
      $image = new ImageMock('/foo/bar.jpeg');
      $Inline = [
        'element' => [
          'attributes' => [
            'srcset' => 'bar1.jpeg 400w bar2.jpeg 800w'
          ]
        ]
      ];

      // then
      expect($image)->toReceive('gaussianBlur')->with('2')->times(1);

      // when
      manipulateElement($Inline, [$image, []], $this->config)['element']['attributes']; 
    });


    it('should remove `src` when fallback disabled', function() {

      // given
      $image = new ImageMock('/foo/bar.jpeg');
      $config = array_merge($this->config, [ 'fallback' => 0 ]);
      $Inline = [
        'element' => [
          'attributes' => [
            'srcset' => 'bar1.jpeg 400w bar2.jpeg 800w'
          ]
        ]
      ];

      // when
      $attributes = manipulateElement($Inline, [$image, []], $config)['element']['attributes']; 

      // then
      expect(isset($attributes['src']))->toBeFalsy();
    });


    it('should set `data-src` when image not in page media', function() {

      // given
      $image = new ImageMock('/foo/bar.jpeg');
      $config = array_merge($this->config, [ 'fallback' => 0 ]);
      $Inline = [
        'element' => [
          'attributes' => [
            'src' => 'https://example.com/example.jpg'
          ]
        ]
      ];

      // when
      $attributes = manipulateElement($Inline, null, $config)['element']['attributes']; 
      
      // then
      expect($attributes['data-src'])->toBe('https://example.com/example.jpg');
      expect($attributes['class'])->toBe('lazyload');
      expect($attributes['data-sizes'])->toBe('auto');
      expect(isset($attributes['src']))->toBeFalsy();
    });


    it('should set `src` when image not in page media but fallback enabled', function() {

      // given
      $image = new ImageMock('/foo/bar.jpeg');
      $Inline = [
        'element' => [
          'attributes' => [
            'src' => 'https://example.com/example.jpg'
          ]
        ]
      ];

      // when
      $attributes = manipulateElement($Inline, null, $this->config)['element']['attributes']; 
      
      // then
      expect($attributes['data-src'])->toBe('https://example.com/example.jpg');
      expect($attributes['src'])->toBe('https://example.com/example.jpg');
      expect($attributes['class'])->toBe('lazyload');
      expect($attributes['data-sizes'])->toBe('auto');
    });

  });
});


