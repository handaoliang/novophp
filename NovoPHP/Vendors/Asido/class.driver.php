<?php
/**
* @author Kaloyan K. Tsvetkov <kaloyan@kaloyan.info>
* @license http://opensource.org/licenses/lgpl-license.php GNU Lesser General Public License Version 2.1
* @package Asido
* @subpackage Asido.Driver
* @version $Id: class.driver.php 17 2007-12-05 11:57:55Z Mrasnika $
*/

/////////////////////////////////////////////////////////////////////////////

/**
* Asido abstract driver
*
* @package Asido
* @subpackage Asido.Driver
* @abstract
*/
Abstract Class asido_driver {

	// -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- 

	/**
	* Maps to supported mime types
	* @var array
	* @access protected
	*/
	Protected $__mime = array(

		// support reading
		//
		'read' => array(
			),

		// support writing
		//
		'write' => array(
			),
		);

	// -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- 

	/**
	* Checks whether the environment is compatible with this driver
	*
	* @return boolean
	* @access public
	* @abstract
	*/
	Abstract Public Function is_compatible();
	
	// -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- 
	
	/**
	* Resize an image
	*
	* @param asido_tmp $tmp
	* @param integer $width
	* @param integer $height
	* @param mixed $mode
	* @return boolean
	* @access public
	*/
	Public Function resize(asido_tmp $tmp, $width, $height, $mode) {
		
		// no params ?
		//
		if (!$width && !$height) {
			trigger_error(
				sprintf(
					'Neither width nor height provided '
						. ' for resizing operation '
						. ' of "%s" ',
					$tmp->source_filename
					),
				E_USER_WARNING
				);
			return false;
			}

		// resize by only one parameter ?
		//
		if (!$width || !$height) {
			$mode = ASIDO_RESIZE_PROPORTIONAL;
			
			if (!$width) {
				$width = floor(
					$tmp->image_width * $height / $tmp->image_height
					);
				}
			if (!$height) {
				$height = floor(
					$tmp->image_height * $width / $tmp->image_width
					);
				}
			}

		// stretch or proportional
		//
		switch ($mode) {

			case ASIDO_RESIZE_STRETCH:
				$p_width = $width;
				$p_height = $height;
				break;
				
			case ASIDO_RESIZE_FIT:
				
				if (($tmp->image_height <= $height) && ($tmp->image_width <= $width)) {
					$p_width = $tmp->image_width;
					$p_height = $tmp->image_height;
					
					// break the switch\case
					//
					break;
					}

				// if indeed has to be resized, fall
				// back to the proportional resize
				//
				;

			default:
			case ASIDO_RESIZE_PROPORTIONAL:
				
				$p1_width = $width;
				$p1_height = round(
					$tmp->image_height * $width / $tmp->image_width
					);

				if ($p1_height - $height > 1) {
					$p_height = $height;
					$p_width = round(
						$tmp->image_width * $height / $tmp->image_height
						);
					} else {
					$p_width = $p1_width;
					$p_height = $p1_height;
					}
				break;
			}

		// do the resize
		//
		$r = $this->__resize($tmp, $p_width, $p_height);
	
		// new dimensions ?
		//
		$tmp->image_width = $p_width;
		$tmp->image_height = $p_height;
		
		return $r;
		}

	/**
	* Convert an image from one file-type to another
	*
	* @param asido_tmp $tmp
	* @param string $mime_type
	* @return boolean
	* @access public
	*/
	Public Function convert(asido_tmp $tmp, $mime_type) {
		
		$mime_type = strToLower($mime_type);
		
		// supported format ? (for writing)
		//
		if (!$this->supported($mime_type, ASIDO_SUPPORT_WRITE)) {
			trigger_error(
				sprintf(
					'Requested conversion format "%s" is not supported',
					$mime_type
					),
				E_USER_WARNING
				);
			return false;
			}

		$tmp->save = $mime_type;
		return true;
		}

	/**
	* Watermark an image 
	*
	* @param asido_tmp $tmp
	* @param string $watermark_image
	* @param mixed $position
	* @param mixed $scalable
	* @param float $scalable_factor
	* @return boolean
	* @access public
	*/
	Public Function watermark(asido_tmp $tmp, $watermark_image, $position, $scalable, $scalable_factor) {
		
		// open
		//
		$wi = new asido_image();
		if (!@$wi->source($watermark_image)) {
			trigger_error(
				sprintf(
					'Watermark image "%s" is '
						. ' either missing or '
						. ' is not readable',
					$watermark_image
					),
				E_USER_WARNING
				);
			return false;
			}
		
		$wt = $this->prepare($wi);

		// dimensions
		//
		$target_width = $tmp->image_width;
		$target_height = $tmp->image_height;

		$watermark_width =& $wt->image_width;
		$watermark_height =& $wt->image_height;
		
		// watermark scalable ?
		//
		if ((ASIDO_WATERMARK_SCALABLE_ENABLED == $scalable)
				&& ($watermark_width > $target_width * $scalable_factor
					|| $watermark_height > $target_height * $scalable_factor)
			){
			
			// jump thru tha loop
			//
			$t2 = $this->__tmpimage($wt->source);
			
			if ($this->resize($t2,
					intval($target_width * $scalable_factor),
					intval($target_height * $scalable_factor),
					ASIDO_RESIZE_PROPORTIONAL)
				){

				@unlink($t2->source_filename);
				$this->__destroy_target($wt);

				// new watermark created, destroy old
				//
				$this->__destroy_source($wt);

				// copy new watermark
				//
				$wt->source = $t2->target;
				$this->__destroy_source($t2);
					// ^
					// DO NOT UNSET $t2->target!!!

				// adjust watermark dimensions
				//
				$watermark_width = $t2->image_width;
				$watermark_height =$t2->image_height;
				}
			}
		
		
		// position
		//
		switch ($position) {
			
			// tile watermark
			//
			case ASIDO_WATERMARK_TILE :
				$watermark_x = 1;
				$watermark_y = 1;
				
				// create tile
				//
				for ($x = 0; $x < ceil($target_width / $watermark_width); $x++) {
					for ($y = 0; $y < ceil($target_height / $watermark_height); $y++) {
						
						// skip the first one
						//
						if (!$x && !$y) continue;
						
						// copy the watermark
						//
						$this->__copy($tmp, $wt,
							$watermark_x + $x*$watermark_width,
							$watermark_y + $y*$watermark_height
							);
						}
					}
				break;
			
			// top left, north west
			//
			case ASIDO_WATERMARK_TOP_LEFT :
				$watermark_x = 1;
				$watermark_y = 1;
				break;
			
			// top center, north
			//
			case ASIDO_WATERMARK_TOP_CENTER :
				$watermark_x = ($target_width - $watermark_width)/2;
				$watermark_y = 1;
				break;
			
			// top right, north east
			//
			case ASIDO_WATERMARK_TOP_RIGHT :
				$watermark_x = $target_width - $watermark_width ;
				$watermark_y = 1;
				break;
			
			// middle left, west
			//
			case ASIDO_WATERMARK_MIDDLE_LEFT :
				$watermark_x = 1;
				$watermark_y = ($target_height - $watermark_height)/2;
				break;
			
			// middle center, center
			//
			case ASIDO_WATERMARK_MIDDLE_CENTER :
				$watermark_x = ($target_width - $watermark_width)/2;
				$watermark_y = ($target_height - $watermark_height)/2;
				break;
			
			// middle right, east
			//
			case ASIDO_WATERMARK_MIDDLE_RIGHT :

				$watermark_x = $target_width - $watermark_width ;
				$watermark_y = ($target_height - $watermark_height)/2;
				break;
			
			// bottom left, south west
			//
			case ASIDO_WATERMARK_BOTTOM_LEFT :
				$watermark_x = 1;
				$watermark_y = $target_height - $watermark_height ;
				break;
			
			// bottom center, south
			//
			case ASIDO_WATERMARK_BOTTOM_CENTER :
				$watermark_x = ($target_width - $watermark_width)/2;
				$watermark_y = $target_height - $watermark_height ;
				break;
			
			default :
			
			// bottom right, south east
			//
			case ASIDO_WATERMARK_BOTTOM_RIGHT :
				$watermark_x = $target_width - $watermark_width ;
				$watermark_y = $target_height - $watermark_height ;
				break;
			}

		// copy the watermark
		//
		$this->__copy($tmp, $wt, $watermark_x, $watermark_y);
		
		// destroy watermark image
		//
		$this->__destroy_source($wt);
		return true;
		}

	/**
	* Make the image greyscale
	*
	* @param asido_tmp $tmp
	* @return boolean
	* @access public
	*/
	Public Function grayscale(asido_tmp $tmp) {
		return $this->__grayscale($tmp);
		}

	/**
	* Rotate the image clockwise
	*
	* @param asido_tmp $tmp
	* @param float $angle
	* @param asido_color $color
	* @return boolean
	* @access public
	*/
	Public Function rotate(asido_tmp $tmp, $angle, asido_color $color=null) {
		
		// color ?
		//
		if (!isset($color)) {
			$color = new asido_color;
			$color->set(255, 255, 255);
			}
		
		return $this->__rotate($tmp, $angle, $color);
		}

	/**
	* Resize an image by "framing" it with the provided width and height
	*
	* @param asido_tmp $tmp
	* @param integer $width
	* @param integer $height
	* @param asido_color $color
	* @return boolean
	* @access public
	*/
	Public Function frame(asido_tmp $tmp, $width, $height, asido_color $color=null) {
		
		// color ?
		//
		if (!isset($color)) {
			$color = new asido_color;
			$color->set(255, 255, 255);
			}
		
		// resize it
		//
		if (!$this->resize($tmp, $width, $height, ASIDO_RESIZE_FIT)) {
			return false;
			}
		
		// get canvas
		//
		if (!$t2 = $this->__canvas($width, $height, $color)) {
			trigger_error(
				sprintf(
					'Unable to get a canvas '
						. ' with %s pixels '
						. ' width and %s '
						. ' pixels height',
					$width, $height
					),
				E_USER_WARNING
				);
			return false;
			}
		
		// target
		//
		$t3 = new asido_tmp;
		$t3->source =& $tmp->target;
		$t3->image_width = $tmp->image_width;
		$t3->image_height = $tmp->image_height;
		
		// apply the image
		//
		if (!$this->__copy(
			$t2, $t3,
			round(($t2->image_width - $t3->image_width)/2),
			round(($t2->image_height - $t3->image_height)/2)
			)) {
			trigger_error(
				'Failed to copy to the passepartout image',
				E_USER_WARNING
				);
			return false;
			}
		
		// cook the result
		//
		$this->__destroy_target($tmp);
		$tmp->target = $t2->target;
		$tmp->image_width = $t2->image_width;
		$tmp->image_height = $t2->image_height;
		
		return true;
		}

	/**
	* Resize an image by "framing" it with the provided width and height
	*
	* @param asido_tmp $tmp
	* @param string $applied_image	filepath to the image that is going to be copied
	* @param integer $x
	* @param integer $y
	* @return boolean
	* @access public
	*/
	Public Function copy(asido_tmp $tmp, $applied_image, $x, $y) {

		// open
		//
		$ci = new asido_image;
		if (!@$ci->source($applied_image)) {
			trigger_error(
				sprintf(
					'The image that is going to '
						. ' be copied "%s" is '
						. ' either missing or '
						. ' is not readable',
					$applied_image
					),
				E_USER_WARNING
				);
			return false;
			}
		
		$ct = $this->prepare($ci);

		if (!$this->__copy($tmp, $ct, $x, $y)) {
			trigger_error(
				'Failed to copy the image',
				E_USER_WARNING
				);
			return false;
			}
		
		$this->__destroy_source($ct);
		$this->__destroy_target($ct);

		return true;
		}

	/**
	* Crop the image 
	*
	* @param asido_tmp $tmp
	* @param integer $x
	* @param integer $y
	* @param integer $width
	* @param integer $height
	* @return boolean
	* @access public
	*/
	Public Function crop(asido_tmp $tmp, $x, $y, $width, $height) {
		return $this->__crop($tmp, $x, $y, $width, $height);
		}

	/**
	* Vertically mirror (flip) the image
	* 
	* @param asido_tmp $tmp
	* @return boolean
	* @access public
	*/
	Public Function flip(asido_tmp $tmp) {
		return $this->__flip($tmp);
		}

	/**
	* Horizontally mirror (flop) the image
	* 
	* @param asido_tmp $tmp
	* @return boolean
	* @access public
	*/
	Public Function flop(asido_tmp $tmp) {
		return $this->__flop($tmp);
		}

	// -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- 

	/**
	* Prepare an image for processing it
	*
	* @param asido_image $image
	* @return asido_tmp
	* @access public
	*/
	Public Function prepare(asido_image $image) {

		// create new temporary object
		//
		$tmp = new asido_tmp;
		$tmp->source_filename = $image->source();
		$tmp->target_filename = $image->target();

		// failed opening ?
		//
		if (!$this->__open($tmp)) {
			trigger_error(
				'Unable to open source image',
				E_USER_WARNING
				);
			return false;
			}
		
		return $tmp;
		}

	/**
	* Save an image after being processed
	*
	* @param asido_tmp $tmp
	* @return boolean
	* @access public
	*/
	Public Function save(asido_tmp $tmp) {
		return $this->__write($tmp);
		}

	// -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- 

	/**
	* Copy one image to another
	*
	* @param asido_tmp $tmp_target
	* @param asido_tmp $tmp_source
	* @param integer $destination_x
	* @param integer $destination_y
	* @return boolean
	* @access protected
	* @abstract
	*/
	Abstract Protected Function __copy(asido_tmp $tmp_target, asido_tmp $tmp_source, $destination_x, $destination_y);

	/**
	* Do the actual resize of an image
	*
	* @param asido_tmp $tmp
	* @param integer $width
	* @param integer $height
	* @return boolean
	* @access protected
	* @abstract
	*/
	Abstract Protected Function __resize(asido_tmp $tmp, $width, $height);

	/**
	* Make the image greyscale
	*
	* @param asido_tmp $tmp
	* @return boolean
	* @access protected
	* @abstract
	*/
	Abstract Protected Function __grayscale(asido_tmp $tmp);

	/**
	* Rotate the image clockwise
	*
	* @param asido_tmp $tmp
	* @param float $angle
	* @param asido_color $color
	* @return boolean
	* @access protected
	* @abstract
	*/
	Abstract Protected Function __rotate(asido_tmp $tmp, $angle, asido_color $color);

	/**
	* Get canvas
	*
	* @param integer $width
	* @param integer $height
	* @param asido_color $color
	* @return asido_tmp
	* @access protected
	* @abstract
	*/
	Abstract Protected Function __canvas($width, $height, asido_color $color);

	/**
	* Crop the image 
	*
	* @param asido_tmp $tmp
	* @param integer $x
	* @param integer $y
	* @param integer $width
	* @param integer $height
	* @return boolean
	* @access protected
	* @abstract
	*/
	Abstract Protected Function __crop(asido_tmp $tmp, $x, $y, $width, $height);

	/**
	* Vertically mirror (flip) the image
	* 
	* @param asido_tmp $tmp
	* @return boolean
	* @access protected
	* @abstract
	*/
	Abstract Protected Function __flip(asido_tmp $tmp);

	/**
	* Horizontally mirror (flop) the image
	* 
	* @param asido_tmp $tmp
	* @return boolean
	* @access protected
	* @abstract
	*/
	Abstract Protected Function __flop(asido_tmp $tmp);

	// -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- 

	/**
	* Get supported mime-types
	*
	* @param mixed $mode
	* @return array
	* @access public
	*/	
	Public Function get_supported_types($mode) {
		
		switch ($mode) {
			
			case 'ASIDO_SUPPORT_READ':
				return array_unique(
					array_values($this->__mime['read'])
					);
				break;
				
			case 'ASIDO_SUPPORT_WRITE':
				return array_unique(
					array_values($this->__mime['write'])
					);
				break;
				
			default :
			case 'ASIDO_SUPPORT_READ_WRITE' :
				return array_unique(
					array_intersect(
						array_values($this->__mime['write']),
						array_values($this->__mime['read'])
						)
					);
				break;
			}
		}

	/**
	* Returnes whether an image format is supported or not
	*
	* @param string $mime_type
	* @param mixed $mode
	* @return boolean
	* @access public
	*/
	Public Function supported($mime_type, $mode) {
		return in_array(
			strToLower($mime_type),
			$this->get_supported_types($mode)
			);
		}

	// -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- 

	/**
	* Open the source and target image for processing it
	*
	* @param asido_tmp $tmp
	* @return boolean
	* @access protected
	* @abstract
	*/
	Abstract Protected Function __open(asido_tmp $tmp);

	/**
	* Write the image after being processed
	*
	* @param asido_tmp $tmp
	* @return boolean
	* @access protected
	* @abstract
	*/
	Abstract Protected Function __write(asido_tmp $tmp);
	
	// -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- 
	
	/**
	* Return a name for a temporary file
	* @return string
	* @access protected
	*/
	Protected Function __tmpfile() {
		return tempnam(-1, null) . '.PNG';
		}
	
	/**
	* Generate a temporary object for the provided argument
	*
	* @param mixed $handler
	* @param string $filename the filename will be automatically generated 
	*	on the fly, but if you want you can use the filename provided by 
	*	this argument
	* @return asido_tmp
	* @access protected
	* @abstract
	*/
	Abstract Protected Function __tmpimage($handler, $filename=null);

	// -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- 

	/**
	* Destroy the source for the provided temporary object
	*
	* @param asido_tmp $tmp
	* @return boolean
	* @access protected
	* @abstract
	*/	
	Abstract Protected Function __destroy_source(asido_tmp $tmp);

	/**
	* Destroy the target for the provided temporary object
	*
	* @param asido_tmp $tmp
	* @return boolean
	* @access protected
	* @abstract
	*/	
	Abstract Protected Function __destroy_target(asido_tmp $tmp);
	
	// -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- 
	
//--end-of-class--	
}

/////////////////////////////////////////////////////////////////////////////

?>