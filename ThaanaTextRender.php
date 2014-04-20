<?php

// {{{ Thaana Text Render

/**
 * Renders given Thaana text as an image, with configuration options for font, font size and color.
 *
 * This class currently provides the following functions:
 * 
 * render()
 * renderImage()
 * setFontPath()
 * setFont()
 * setFontSize()
 * setTextColor()
 * setTextLineSpacing()
 * setBgColor()
 * setShadow()
 *
 * Simple usage:
 * include('thaanaTextRender.php');
 * $ttr = new ThaanaTextRender('./fonts/', 'a_waheed', 18, 'white', 0, 50, 'rgb(0,0,0)', 127, 2, '#555555', 50);
 * $ttr->renderImage('swaincsc', 100, 50, 'output.png', 'png')
 *
 *
 * @author Jawish Hameed
 * @link http://www.jawish.org/
 * @copyright  2011 Jawish Hameed
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 * @version Release: 1.0
 */

class ThaanaTextRender {
	
	// {{{ properties
	
	private $fontPath = './';
	private $fontFile;
	private $fontSize = 12;
	private $textColor = array('r' => 0, 'g' => 0, 'b' => 0);
	private $textAlpha = 0;
	private $textLineSpacing = 0;
	private $bgColor = array('r' => 255, 'g' => 255, 'b' => 255);
	private $bgAlpha = 0;
	private $bgTransparent = true;
	private $shadowOffset = false;
	private $shadowColor = '#EBEBEB';
	private $shadowAlpha = 0;
	private $width;
	
	// }}}
	
	
	// {{{ __construct()
	
	/**
	 *
	 *
	 */
	public function __construct($fontPath=NULL, $fontName=NULL, $fontSize=NULL, $textColor=NULL, $textAlpha=NULL, $textLineSpacing=NULL, $bgColor=NULL, $bgAlpha=NULL, $shadowOffset=NULL, $shadowColor=NULL, $shadowAlpha=NULL)
	{
		// Initialize with specified defaults:
		if (!empty($fontPath)) $this->setFontPath($fontPath);
		if (!empty($fontName)) $this->setFont($fontName);
		if (!empty($fontSize)) $this->setFontSize($fontSize);
		if (!empty($textColor)) $this->setTextColor($textColor, $textAlpha);
		if (!empty($textLineSpacing)) $this->setTextLineSpacing($textLineSpacing);
		if (!empty($bgColor)) $this->setBgColor($bgColor, $bgAlpha);
		if (!empty($shadowOffset)) $this->setShadow($shadowOffset, $shadowColor, $shadowAlpha);
	}
	
	// }}}
	
	
	// {{{ render()
	
	/**
	 * Render the given ASCII Thaana text and return image handle
	 *
	 * @param string $text			Text to render in Dhivehi as ASCII Thaana
	 * @param integer $width		Width of the rendered image
	 * @param integer $linespacing	Line spacing
	 * @return string				Image identifier handle
	 *
	 * @access public
	 */
	public function render($text, $width=NULL)
	{	
		try
		{
			// Word wrap text
			$lines = $this->wordWrap($text, $width);
			
			// Set linespacing to 1x if is not specified
			$linespacing = ($this->textLineSpacing > 0) ? $this->textLineSpacing : $lines['maxheight'];
			
			// Calculate text width and height
			$width = max($width, $lines['maxwidth']);
			$height = $linespacing * count($lines['text']);
			
			// Create image
			$im = imagecreatetruecolor($width, $height);
			
			// Setup alpha channel handling
			imagesavealpha($im, true);
			imagealphablending($im, true);
			
			// Allocate required colors
			$bgColor = imagecolorallocatealpha($im, $this->bgColor['r'], $this->bgColor['g'], $this->bgColor['b'], $this->bgAlpha);
			$fontColor = imagecolorallocatealpha($im, $this->textColor['r'], $this->textColor['g'], $this->textColor['b'], $this->textAlpha);
			
			// Set shadow color if shadow in use
			if (is_numeric($this->shadowOffset))
			{
				// Allocate shadow color
				$shadowColor = imagecolorallocatealpha($im, $this->shadowColor['r'], $this->shadowColor['g'], $this->shadowColor['b'], $this->shadowAlpha);
			}
			
			// Set background color
			imagefill($im, 0, 0, $bgColor);
			
			// Process the lines
			foreach ($lines['text'] as $lineno => $line)
			{
				// Reverse line
				$line = strrev($line);
				
				// Calculate bounding box and line width without the new word
				$bbox = imagettfbbox($this->fontSize, 0, $this->fontFile, $line);
				
				// Calculate X axis offset
				$xpos = ($width - abs($bbox[2] - $bbox[0])) - abs($bbox[0]);
				
				// Calculate Y axis offset
				$ypos = ($linespacing * $lineno) + abs($bbox[7]);
				
				// Render text shadow
				if ($this->shadowOffset > 0)
				{
					imagettftext($im, $this->fontSize, 0, $xpos + $this->shadowOffset, 
								 $ypos + $this->shadowOffset, $shadowColor, $this->fontFile, $line
								 );
				}
				
				// Render text
				imagettftext($im, $this->fontSize, 0, $xpos, $ypos, $fontColor, $this->fontFile, $line);
			}
		
		}
		catch (Exception $e)
		{
			// An error had occured:
			
			// Create a 1x1 pixel image
			$im = imagecreatetruecolor(1, 1);
		}
		
		// Return rendered image handle
		return $im;
	}
	
	// }}}
	
	
	// {{{ renderImage()
	
	/**
	 * Render the given text and output directly or save
	 *
	 * @param string $text			Text string to render
	 * @param integer $width		Width of the rendered image
	 * @param string $filename		Filename to save the rendered image as. Set to NULL to output directly.
	 * @param string $imagetype		Image type. Can be 'png', 'jpg', 'jpeg' or 'gif'
	 * @return string				Image identifier handle
	 *
	 * @access public
	 */
	public function renderImage($text, $width=NULL, $filename=NULL, $imagetype = 'png')
	{
		// Render and return image handle
		$im = $this->render($text, $width);
		
		// Select output image type
		switch ($imagetype)
		{
			case 'png':
				// Render as png
				header("Content-type: image/png");
				imagepng($im, $filename);
				break;
			
			case 'jpg':
			case 'jpeg':
				// Render as jpg
				header("Content-type: image/jpeg");
				imagejpeg($im, $filename);
				break;
				
			case 'gif':
				// Render as gif
				header("Content-type: image/gif");
				imagegif($im, $filename);
				break;
		}
	}
	
	// }}}
	
	
	// {{{ wordWrap()
	
	/**
	 * Word wrap the text at the given width boundary
	 * Returns the wrapped text as an array of lines.
	 * Returns max line width and max line height details
	 *
	 * @param string $text		Text to word wrap
	 * @param integer $width	Width boundary to word wrap
	 * @return array			Word wrapped line data as an array with elements [text, linewidth, lineheight]
	 *
	 * @access private
	 */
	private function wordWrap($text, $width=NULL)
	{
		// Check if wrapping is necessary
		if (empty($width))
		{
			// No width set -> no wrapping required
			return explode("\r\n", $text);
		}
		
		// Initialize variables for line data
		$lines = array();
		$lineno = -1;
		$maxlinewidth = 0;
		$maxlineheight = 0;
		
		// Get paragraphs
		$text = explode("\n", $text);
		
		// Loop through paragraphs
		for ($x = 0; $x < count($text); $x++)
		{
			
			// Convert the text to an array
			$word = strtok($text[$x], ' ');
						
			// Begin a new line
			$lines[++$lineno] = '';
			
			// Loop through words
			while ($word !== false)
			{
				
				// Calculate bounding box for the current line + current word
				$bbox = imagettfbbox($this->fontSize, 0, $this->fontFile, $lines[$lineno] . $word . ' ');
				
				// Calculate line width and height
				$linewidth = abs($bbox[2] - $bbox[0]);
				$lineheight = abs($bbox[7] - $bbox[1]);
				
				// Check if line + word exceeds max width allowed
				if ($linewidth > $width)
				{
					// Width exceeds:
					
					// Start a new line and add current word to it
					$lines[++$lineno] = $word . ' ';
					
				}
				else
				{
					// Width ok:
					
					// Add word to current line
					$lines[$lineno] .= $word . ' ';
					
					// Store the line width/height if largest
					if ($linewidth > $maxlinewidth) $maxlinewidth = $linewidth;
					if ($lineheight > $maxlineheight) $maxlineheight = $lineheight;
				}
				
				// Get next word
				$word = strtok(' ');
			}
		}
		
		// Return the wordwrapped text
		return array('text' => $lines, 'maxwidth' => $maxlinewidth, 'maxheight' => $maxlineheight);
	}
	
	// }}}
	
	
	// {{{ setFont()
	
	/**
	 * Set the name of the font to use by checking a given list of fonts for a useable font.
	 * Falls back to the default if no font in the list is found.
	 * Expects fonts to be in the specified font path with lowercase filenames and named as given in the font list.
	 *
	 * @param string $fontNames		HTML/CSS font names list
	 * @param string $default		Name of font to default to in case of failure
	 * @return boolean 				True if font set successfully, false otherwise
	 *
	 * @access public
	 */
	public function setFont($fontNames, $default=NULL)
	{
		// Get the list of fonts specified
		$fontNames = explode(',', $fontNames);
		
		// Add the default font as the last font to check
		if ($default != NULL) array_push($fontNames, $default);
		
		// Loop through the specified list to find one that can be used
		foreach ($fontNames as $fontName)
		{
			// Cleanup font name by removing extra whitespace and quotes
			$fontName = str_replace(array('\'', '"'), '', strtolower(trim($fontName)));
			
			// Check if the given font exists in the fonts store
			if (file_exists($this->fontPath . $fontName . '.ttf'))
			{
				// Given font exists:
				
				// Useable font found, set it for use
				$this->fontFile = $this->fontPath . $fontName . '.ttf';
				
				return true;
			}
		}
		
		// No useable font found
		return false;
	}
	
	// }}}
	
	
	// {{{ setFontSize()
	
	/**
	 * Set font size
	 *
	 * @param integer $size		Font size to use
	 * @return boolean			True if successfully set, false otherwise
	 *
	 * @access public
	 */
	public function setFontSize($size)
	{
		// Check if font size is valid
		if (intval($size) > 0)
		{
			// Font size valid
			
			// Save font size
			$this->fontSize = intval($size);
			
			return true;
		}
			
		return false;
	}
	
	// }}}
	
	
	// {{{ setTextColor()
	
	/**
	 * Set text color
	 *
	 * @param string $color		Text color. Can be any HTML/CSS color spec
	 * @param integer $alpha	Alpha value [0-127] for transparency
	 *
	 * @access public
	 */
	public function setTextColor($color, $alpha=NULL)
	{
		// Set text color
		$this->textColor = $this->parseColor($color);
		
		// Set text alpha
		if (!empty($alpha)) $this->textAlpha = intval($alpha);
	}
	
	// }}}
	
	
	/**
	 * Set the line spacing for the text
	 *
	 * @param integer $spacing	Line spacing in pixels
	 *
	 * @access public
	 */
	// {{{ setTextLineSpacing()
	public function setTextLineSpacing($spacing)
	{
		if (!empty($spacing)) $this->textLineSpacing = intval($spacing);
	}
	
	// }}}
	
	
	// {{{ setFontPath()
	
	/**
	 * Set the path to where the TTF font files are stored
	 *
	 * @param string $path		Path to font files
	 * @return boolean			True if font path exists, false if not.
	 *
	 * @access public
	 */
	public function setFontPath($path)
	{
		// Check if the path exists
		if (file_exists($path))
		{
			// Path found:
			
			// Set font path
			$this->fontPath = $path;
			
			return true;
		}
			
		return false;
	}
	
	// }}}
	
	
	// {{{ setBgColor()
	
	/**
	 * Set background color
	 *
	 * @param string $color		HTML/CSS background spec
	 * @param integer $alpha	Alpha value [0-127] for transparency 
	 *
	 * @access public
	 */
	public function setBgColor($color, $alpha=NULL)
	{
		// Set bg color
		$this->bgColor = $this->parseColor($color);
		
		// Set bg alpha
		if (!empty($alpha)) $this->bgAlpha = intval($alpha);
	}
	
	// }}}
	
	
	// {{{ setShadow()
	
	/**
	 * Set shadow
	 *
	 * @param integer $offset		Shadow offset
	 * @param string $color			Color as any HTML/CSS color spec
	 * @param integer $alpha		Alpha value [0-127] for transparency
	 *
	 * @access public
	 */
	public function setShadow($offset, $color, $alpha=NULL)
	{
		// Set shadow offset
		$this->shadowOffset = intval($offset);
		
		// Set shadow color
		$this->shadowColor = $this->parseColor($color);
		
		// Set shadow alpha
		if (!empty($alpha)) $this->shadowAlpha = intval($alpha);
	}
	
	// }}}
	
	
	// {{{ parseColor()
	
	/**
	 * Parse a string for HTML/CSS color and return RGB
	 *
	 * @param string $color     Containing color specification as RGB, hex or named colors as per HTML/CSS
	 * @return array 			RGB color components 'r', 'g', 'b'
	 *
	 * @access private
	 */
	private function parseColor($color)
	{
		// Which color format?
		if (substr($color, 0, 1) == '#')
		{
			// Hex:
			
			// Extract the RGB components
			$int = hexdec(substr($color, 1));
			return array('r' => 0xFF & ($int >> 0x10), 'g' => 0xFF & ($int >> 0x8), 'b' => 0xFF & $int);
		}
		elseif (substr($color, 0, 3) == 'rgb')
		{
			// RGB:
			
			// Extract RGB components
			list($r, $g, $b) = sscanf($color, 'rgb(%d,%d,%d)');
			
			// Return the RGB components
			return array('r' => $r, 'g' => $g, 'b' => $b);
		}
		else
		{
			// Named or other:
			
			// W3C listed named colors required for validation
			$names = array ('aqua' => '#00FFFF', 'black' => '#000000',
							'blue' => '#0000FF', 'fuchsia' => '#FF00FF', 
							'gray' => '#808080', 'green' => '#008000', 
							'lime' => '#00FF00', 'maroon' => '#800000', 
							'navy' => '#000080', 'olive' => '#808000', 
							'purple' => '#800080', 'red' => '#ff0000', 
							'silver' => '#C0C0C0', 'teal' => '#008080', 
							'white' => '#FFFFFF', 'yellow' => '#FFFF00'
							);
			
			// Check if named color is defined
			if (isset($names[strtolower($color)]))
			{
				// Named color defined:
				
				// Recursive call to get the rgb components of the named color
				return $this->parseColor($names[strtolower($color)]);
			}
		}
		
		return array('r' => 0, 'g' => 0, 'b' => 0);
	}
	
	// }}}	
}
?>