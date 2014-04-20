## Overview
The rationale for the class was that the use of images to display Thaana means that the information can be viewed on a large variety of devices and does away with the requirement for the device to support Thaana fonts. It can also be used for any purpose that requires dynamically generating an image with Thaana text in it, for example watermarking images or nice headings with your Thaana font of choice.

The class makes use of the powerful image manipulation services provided by the GD library to create images from text and hence inherits the wide range of features it offers. However, since the GD library does not support right-to-left scripts and does not offer line wrapping to fit text within a bounding box, the class requires the use of non-Unicode Thaana fonts and non-Unicode Thaana text. You can make use of my Thaana conversions class for PHP to dynamically convert Unicode Thaana text to ASCII Thaana text before passing it to this class for rendering.

## Functions exposed
- render()
- renderImage()
- setFontPath()
- setFont()
- setFontSize()
- setTextColor()
- setTextLineSpacing()
- setBgColor()
- setShadow()

## Requirements
- PHP 5
- GD for PHP

## Usage
The class is well commented so have a look through the code for more details on the functions and their arguments.

The class takes ASCII Thaana text and an optional width, filename, filetype for the render functions. The render() function returns a handle that can be used with the PHP image functions for further processing if required. The renderImage() allows the output to be returned directly or saved to a file.

If the width is specified, the resultant image will be constraint to that width and text will be wrapped around and split into lines as appropriate. Specify either "gif", "png" or "jpg" for the class to save the rendered output. If the filename is omitted, then it will return the output directly.

The class will accept any HTML/CSS color specification (i.e. "#FFF000", "red", "rgb(127, 127, 127)") as a valid color for use as text color, background color and shadow color. You can also specify alpha values to accompany them in order to have transparency effects. Set the background alpha to 127 to produce text on a transparent background.

All fonts have to be placed in the font path directory and should be named, in lower case, exactly as the font names passed to the class.

### Examples

#### Saving to a file
This example shows how to render the text and save the output. It sets all the properties for font, background and shadows at object initialization. The renderImage() function has the filename and filetype specified for saving the rendered output.

```php
<?php
// Load the class
include('thaanaTextRender.php');

// Initialize object
$ttr = new ThaanaTextRender('./fonts/', 'a_waheed', 20, 'white', 0, 50, 'rgb(0,0,0)', 0, 2, '#555555', 80);

// Render image and save output
$ttr->renderImage('swaincsc', 300, 'output.png', 'png');
```

#### Output directly to browser
This example is ideal for use in scripts that dynamically generate the required rendering and return the output directly to the browser.

```php
<?php
// Load the class
include('thaanaTextRender.php');

// Initialize object
$ttr = new ThaanaTextRender('./fonts/', 'a_waheed', 16, 'white', 0, 50, 'rgb(0,0,0)', 127);

// Render image and save output
$ttr->renderImage('gUgulunc aepwlc awaimUvIaW vWdwkurW kwhwlw aeve. gUgulc aencDcroaiDc fOnutwkwSc mihWru vwnI mUvI scTUDiaOaeac dIfw aeve.', 300, NULL, 'png');
```

You could use it by, for example, calling it via the image tag.
```html
<img src="script.php" />
```

## Demo
You can check out the original demo I posted to see it in action.
http://labs.jawish.org/ttr/


## License
This script is released under the Open Source MIT License, allowing its use in both personal and commercial applications as long as the copyright and license permission notice remains intact.
