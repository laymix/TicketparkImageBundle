# TicketparkImageBundle

This Symfony2 bundle ads functionalities to manipulate images with ease.

## Functionalities
* Resizer (Service and TwigExtension)
    * Resize files, based on pixel size or combination of mm and dpi (for print products)

## Installation

Add TicketparkImageBundle in your composer.json:

```js
{
    "require": {
        "ticketpark/image-bundle": "dev-master"
    }
}
```

Now tell composer to download the bundle by running the command:

``` bash
$ php composer.phar update ticketpark/image-bundle
```

Enable the bundle in the kernel:

``` php
<?php
// app/AppKernel.php

public function registerBundles()
{
    $bundles = array(
        // ...
        new Ticketpark\ImageBundle\TicketparkImageBundle(),
    );
}
```
## Usage of Resizer
Use the file handler service in a controller to resize files

``` php
// Resize based on pixels
$resizer = $this->get('ticketpark.image.resizer');
$pathToFileOrUrl = '/path/to/image.png'; // or url: http://foo.com/someimage.jpg
$style = 'crop'; // 'crop' or 'resize'
$maxSizeInPixel = 300;
$pathToResizedImage = $resizer->resize($pathToFileOrUrl, $style, $maxSizeInPixel);

// Resize based on pixel density (dpi)
// Learn more here: http://en.wikipedia.org/wiki/Dots_per_inch
$pathToFileOrUrl = '/path/to/image.png'; // or url: http://foo.com/someimage.jpg
$style = 'crop'; // 'crop' or 'resize'
$maxSizeInMm = 50;
$dpi = 150; // values between 150 and 300 are usually used for printing
$pathToResizedImage = $resizer->resizeMm($pathToFileOrUrl, $style, $maxSizeInMm, $dpi);
```
    
There is also a Twig extension, examples:
``` html
<img src="{{ myImage|resize(300, 'resize') }}">
<img src="{{ myImage|resizeMm(50, 150, 'resize') }}">
```


## License
This bundle is under the MIT license. See the complete license in the bundle:

    Resources/meta/LICENSE
