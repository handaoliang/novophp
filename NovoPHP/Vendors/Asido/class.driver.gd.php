<?php
/**
* @author Kaloyan K. Tsvetkov <kaloyan@kaloyan.info>
* @license http://opensource.org/licenses/lgpl-license.php GNU Lesser General Public License Version 2.1
* @package Asido
* @subpackage Asido.Driver.GD
* @version $Id: class.driver.gd.php 17 2007-12-05 11:57:55Z Mrasnika $
*/

/////////////////////////////////////////////////////////////////////////////

/**
* Quality factor for saving JPEG files
* @see asido_driver_GD::Save()
*/
if (!defined('ASIDO_GD_JPEG_QUALITY')) {
	define('ASIDO_GD_JPEG_QUALITY', 80);
	}

/////////////////////////////////////////////////////////////////////////////

/**
* Asido GD(GD2) driver
*
* @package Asido
* @subpackage Asido.Driver.GD
*/
Class asido_driver_gd Extends asido_driver {

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
		
			// GIF
			//
			'image/gif',
			
			// JPEG
			//
			'application/jpg',
			'application/x-jpg',
			'image/jpg',
			'image/jpeg',
			
			// WBMP
			//
			'image/wbmp',
			
			// XPM
			//
			'image/x-xpixmap',
			'image/x-xpm',
			
			// XBM
			//
			'image/x-xbitmap',
			'image/x-xbm',
			
			// PNG
			//
			'application/png',
			'application/x-png',
			'image/x-png',
			'image/png',
		
			),
		
		// support writing
		//
		'write' => array(
		
			// GIF
			//
			'image/gif',
			
			// JPEG
			//
			'application/jpg',
			'application/x-jpg',
			'image/jpg',
			'image/jpeg',
			
			// WBMP
			//
			'image/wbmp',
			
			// PNG
			//
			'application/png',
			'application/x-png',
			'image/x-png',
			'image/png',
			),	

		);
	
	/**
	* Metaphone map for detecting image file extensions
	* @var array
	* @access private
	*/
	Private $__mime_metaphone = array(
			'JPK' => 'image/jpeg',
			'JP' => 'image/jpeg',
			'JF' => 'image/gif',
			'NK' => 'image/png',
			'BMP' => 'image/wbmp',
			'SPM' => 'image/x-xbm',
			// 'SBM' => 'image/x-xpm',
				// ^
				// XPM is read-only and this map is used for
				// saving files, so this XPM entry is useless
		);

	/**
	* Soundex map for detecting image file extensions
	* @var array
	* @access private
	*/
	Private $__mime_soundex = array(
			'J120' => 'image/jpeg',
			'J100' => 'image/jpeg',
			'G100' => 'image/gif',
			'P520' => 'image/png',
			'B510' => 'image/wbmp',
			'W151' => 'image/wbmp',
		);

	// -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- 

	/**
	* Checks whether the environment is compatible with this driver
	*
	* @return boolean
	* @access public
	*/
	Public Function is_compatible() {
		
		if (!extension_loaded('gd')) {
			trigger_error(
				'The asido_driver_GD driver is unnable to be '
					. ' initialized, because the GD (php_gd2) '
					. ' module is not installed',
				E_USER_ERROR
				);
			return false;
			}
		
		// give access to all the memory
		//
		@ini_set("memory_limit", -1);
		
		return true;
		}
	
	// -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- 

	/**
	* Do the actual resize of an image
	*
	* @param asido_tmp $tmp
	* @param integer $width
	* @param integer $height
	* @return boolean
	* @access protected
	*/
	Protected Function __resize(asido_tmp $tmp, $width, $height) {

		// create new target
		//
		$_ = imageCreateTrueColor($width, $height);
		imageSaveAlpha($_, true);
		imageAlphaBlending($_, false);

		$r = imageCopyResized(
			$_, $tmp->target,
				0,0,
				0,0,
				$width, $height,
				$tmp->image_width, $tmp->image_height
			);

		// set new target
		//
		$this->__destroy_target($tmp);
		$tmp->target = $_;

		return $r;
		}

	/**
	* Copy one image to another
	*
	* @param asido_tmp $tmp_target
	* @param asido_tmp $tmp_source
	* @param integer $destination_x
	* @param integer $destination_y
	* @return boolean
	* @access protected
	*/
	Protected Function __copy(asido_tmp $tmp_target, asido_tmp $tmp_source, $destination_x, $destination_y) {

		imageAlphaBlending($tmp_target->target, true);
		$r = imageCopy($tmp_target->target, $tmp_source->source,
			$destination_x, $destination_y,
			0, 0,
			$tmp_source->image_width, $tmp_source->image_height
			);
		imageAlphaBlending($tmp_target->target, false);
		
		return $r;
		}

	/**
	* Make the image greyscale: supported only for PHP => 5.* and PHP => 4.0.1 except for PHP 4.3.11
	*
	* @param asido_tmp $tmp
	* @return boolean
	* @access protected
	*/
	Protected Function __grayscale(asido_tmp $tmp) {

		// the shorter path: function already exists
		//
		if (function_exists('imagefilter')) {
			return imagefilter($tmp->target, IMG_FILTER_GRAYSCALE);
			return true;
			}

		// a bit wicked path: prior to `PHP 4.3.11` and
		// `PHP 5.0.4` there is a bug w/ imageCopyMergeGray()
		//
		if (	(version_compare(PHP_VERSION, '4.3.11') > 0)
			|| (
			(version_compare(PHP_VERSION, '5.0.4') > 0)
			)) {
			return imageCopyMergeGray($tmp->target, $tmp->target,
				0, 0, 0, 0,
				$tmp->image_width, $tmp->image_height, 0);
			}
		
		return false;
		}

	/**
	* Rotate the image clockwise
	*
	* @param asido_tmp $tmp
	* @param float $angle
	* @param asido_color $color
	* @return boolean
	* @access protected
	*/
	Protected Function __rotate(asido_tmp $tmp, $angle, asido_color $color) {

		// skip full loops
		//
		if (($angle % 360) == 0) {
			return true;
			}
		
		list($r, $g, $b) = $color->get();
		$rotate_color = imageColorAllocate($tmp->target, $r, $g, $b); 
		
		if ($t = imageRotate($tmp->target, $angle * -1, $rotate_color)) {
			imageDestroy($tmp->target);
			$tmp->target = $t;
			
			$tmp->image_width = imageSX($tmp->target);
			$tmp->image_height = imageSY($tmp->target);
			
			return true;
			}

		return false;
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
	* @access protected
	*/
	Protected Function __crop(asido_tmp $tmp, $x, $y, $width, $height) {
		
		$t = imageCreateTrueColor($width, $height);
		imageAlphaBlending($t, true);
		$r = imageCopy($t, $tmp->target,
			0, 0,
			$x, $y,
			$width, $height
			);
		imageAlphaBlending($t, false);
		
		$this->__destroy_target($tmp);
		$tmp->target = $t;
		$tmp->image_width = $width;
		$tmp->image_height = $height;
		
		return $r;
		}

	/**
	* Vertically mirror (flip) the image: not supported
	* 
	* @param asido_tmp $tmp
	* @return boolean
	* @access protected
	*/
	Protected Function __flip(asido_tmp $tmp) {
		return false;
		}

	/**
	* Horizontally mirror (flop) the image: not supported
	* 
	* @param asido_tmp $tmp
	* @return boolean
	* @access protected
	*/
	Protected Function __flop(asido_tmp $tmp) {
		return false;
		}

	// -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- 

	/**
	* Get canvas
	*
	* @param integer $width
	* @param integer $height
	* @param asido_color $color
	* @return asido_tmp
	* @access protected
	*/
	Protected Function __canvas($width, $height, asido_color $color) {
		
		$t = new asido_tmp;
		$t->target = imageCreateTrueColor($width, $height);
		
		list($r, $g, $b) = $color->get();
		imageFill($t->target, 1, 1, 
			imageColorAllocate($t->target, $r, $g, $b)
			);
		$t->image_width = $width;
		$t->image_height = $height;

		return $t;
		}

	/**
	* Generate a temporary object for the provided argument
	*
	* @param mixed &$handler
	* @param string $filename the filename will be automatically generated 
	*	on the fly, but if you want you can use the filename provided by 
	*	this argument
	* @return asido_tmp
	* @access protected
	*/
	Protected Function __tmpimage($handler, $filename=null) {

		if (!isset($filename)) {
			$filename = $this->__tmpfile();
			}

		imageAlphaBlending($handler, 0);
		imageSaveAlpha($handler, 1); 
		imagePNG($handler, $filename);
			// ^
			// PNG: no pixel losts

		return $this->prepare(
			new asido_image($filename)
			);
		}

	// -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- 

	/**
	* Open the source and target image for processing it
	*
	* @param asido_tmp $tmp
	* @return boolean
	* @access protected
	*/
	Protected Function __open(asido_tmp $tmp) {

		$error_source = false;
		$error_target = false;

		// get image dimensions
		//
		if ($i = @getImageSize($tmp->source_filename)) {
			$tmp->image_width = $i[0];
			$tmp->image_height = $i[1];
			}
		
		// image type ?
		//
		switch(@$i[2]) {
			
			case 1:	// GIF
				$error_source = (false == (
					$tmp->source = @imageCreateFromGIF(
						$tmp->source_filename
						)
					));

				$error_target = false == (
					$tmp->target = imageCreateTrueColor(
						$tmp->image_width, $tmp->image_height
						)
					);
				$error_target &= imageCopyResampled(
					$tmp->target, $tmp->source, 
					0, 0, 0, 0,
					$tmp->image_width, $tmp->image_height,
					$tmp->image_width, $tmp->image_height
					);
				
				break;

			case 2: // JPG
				$error_source = (false == (
					$tmp->source = imageCreateFromJPEG(
						$tmp->source_filename
						)
					));
				
				$error_target = (false == (
					$tmp->target = imageCreateFromJPEG(
						$tmp->source_filename
						)
					));
				break;

			case 3: // PNG
				$error_source = (false == (
					$tmp->source = @imageCreateFromPNG(
						$tmp->source_filename
						)
					));

				$error_target = (false == (
					$tmp->target = @imageCreateFromPNG(
						$tmp->source_filename
						)
					));
				break;

			case 15: // WBMP
				$error_source = (false == (
					$tmp->source = @imageCreateFromWBMP(
						$tmp->source_filename
						)
					));

				$error_target = (false == (
					$tmp->target = @imageCreateFromWBMP(
						$tmp->source_filename
						)
					));
				break;

			case 16: // XBM
				$error_source = (false == (
					$tmp->source = @imageCreateFromXBM(
						$tmp->source_filename
						)
					));

				$error_target = (false == (
					$tmp->target = @imageCreateFromXBM(
						$tmp->source_filename
						)
					));
				break;

			case 4: // SWF

			case 5: // PSD

			case 6: // BMP
                $error_source = (false == (
					$tmp->source = $this->_imagecreatefrombmp(
						$tmp->source_filename
						)
					));

				$error_target = (false == (
					$tmp->target = $this->_imagecreatefrombmp(
						$tmp->source_filename
						)
					));
				break;


			case 7: // TIFF(intel byte order)

			case 8: // TIFF(motorola byte order)

			case 9: // JPC

			case 10: // JP2

			case 11: // JPX

			case 12: // JB2

			case 13: // SWC

			case 14: // IFF
				
			default:
				
				$error_source = (false == (
					$tmp->source = @imageCreateFromString(
						file_get_contents(
							$tmp->source_filename
							)
						)
					));

				$error_target = (false == (
					$tmp->source = @imageCreateFromString(
						file_get_contents(
							$tmp->source_filename
							)
						)
					));
				break;
			}

		return !($error_source || $error_target);
		}

	/**
	* Write the image after being processed
	*
	* @param asido_tmp $tmp
	* @return boolean
	* @access protected
	*/
	Protected Function __write(asido_tmp $tmp) {

		// try to guess format from extension
		//
		if (!$tmp->save) {
			$p = pathinfo($tmp->target_filename);

			($tmp->save = $this->__mime_metaphone[metaphone($p['extension'])])
				|| ($tmp->save = $this->__mime_soundex[soundex($p['extension'])]);
			}

		$result = false;
		switch($tmp->save) {

			case 'image/gif' :
				imageTrueColorToPalette($tmp->target, true, 256);
				$result = @imageGIF($tmp->target, $tmp->target_filename);
				break;
				
			case 'image/jpeg' :
				$result = @imageJPEG($tmp->target, $tmp->target_filename, ASIDO_GD_JPEG_QUALITY);
				break;
				
			case 'image/wbmp' :
				$result = @imageWBMP($tmp->target, $tmp->target_filename);
				break;
			
			default :
			case 'image/png' :

				imageSaveAlpha($tmp->target, true);
				imageAlphaBlending($tmp->target, false);
			
				$result = @imagePNG($tmp->target, $tmp->target_filename);
				break;
			}
		
		@$this->__destroy_source($tmp);
		@$this->__destroy_target($tmp);

		return $result;
		}
	
	// -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- 

	/**
	* Destroy the source for the provided temporary object
	*
	* @param asido_tmp $tmp
	* @return boolean
	* @access protected
	* @abstract
	*/	
	Protected Function __destroy_source(asido_tmp $tmp) {
		return imageDestroy($tmp->source);
		}

	/**
	* Destroy the target for the provided temporary object
	*
	* @param asido_tmp $tmp
	* @return boolean
	* @access protected
	* @abstract
	*/	
	Protected Function __destroy_target(asido_tmp $tmp) {
		return imageDestroy($tmp->target);
		}

	// -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- 

    /**
     * BMP 创建函数
     * @author simon
     * @param string $filename path of bmp file
     * @example who use,who knows
     * @return resource of GD
     */ 
    Protected function _imagecreatefrombmp( $filename ) {
        if ( !$f1 = fopen( $filename, "rb" ) )
            return FALSE;
        
        $FILE = unpack( "vfile_type/Vfile_size/Vreserved/Vbitmap_offset", fread( $f1, 14 ) );
        if ( $FILE['file_type'] != 19778 )
            return FALSE;
        
        $BMP = unpack( 'Vheader_size/Vwidth/Vheight/vplanes/vbits_per_pixel' . '/Vcompression/Vsize_bitmap/Vhoriz_resolution' . '/Vvert_resolution/Vcolors_used/Vcolors_important', fread( $f1, 40 ) );
        $BMP['colors'] = pow( 2, $BMP['bits_per_pixel'] );
        if ( $BMP['size_bitmap'] == 0 )
            $BMP['size_bitmap'] = $FILE['file_size'] - $FILE['bitmap_offset'];
        $BMP['bytes_per_pixel'] = $BMP['bits_per_pixel'] / 8;
        $BMP['bytes_per_pixel2'] = ceil( $BMP['bytes_per_pixel'] );
        $BMP['decal'] = ($BMP['width'] * $BMP['bytes_per_pixel'] / 4);
        $BMP['decal'] -= floor( $BMP['width'] * $BMP['bytes_per_pixel'] / 4 );
        $BMP['decal'] = 4 - (4 * $BMP['decal']);
        if ( $BMP['decal'] == 4 )
            $BMP['decal'] = 0;
        $PALETTE = array();
        if ( $BMP['colors'] < 16777216 ){
            $PALETTE = unpack( 'V' . $BMP['colors'], fread( $f1, $BMP['colors'] * 4 ) );
            }
        $IMG = fread( $f1, $BMP['size_bitmap'] );
        $VIDE = chr( 0 );
        $res = imagecreatetruecolor( $BMP['width'], $BMP['height'] );
            $P = 0;
        $Y = $BMP['height'] - 1;
        while( $Y >= 0 ){
                $X = 0;
                while( $X < $BMP['width'] ){
                    if ( $BMP['bits_per_pixel'] == 32 ){
                        $COLOR = unpack( "V", substr( $IMG, $P, 3 ) );
                        $B = ord(substr($IMG, $P,1));
                        $G = ord(substr($IMG, $P+1,1));
                        $R = ord(substr($IMG, $P+2,1));
                        $color = imagecolorexact( $res, $R, $G, $B );
                        if ( $color == -1 )
                            $color = imagecolorallocate( $res, $R, $G, $B );
                        $COLOR[0] = $R*256*256+$G*256+$B;
                        $COLOR[1] = $color;
                    }elseif ( $BMP['bits_per_pixel'] == 24 )
                        $COLOR = unpack( "V", substr( $IMG, $P, 3 ) . $VIDE );
                    elseif ( $BMP['bits_per_pixel'] == 16 ){
                        $COLOR = unpack( "n", substr( $IMG, $P, 2 ) );
                        $COLOR[1] = $PALETTE[$COLOR[1] + 1];
                    }elseif ( $BMP['bits_per_pixel'] == 8 ){
                        $COLOR = unpack( "n", $VIDE . substr( $IMG, $P, 1 ) );
                        $COLOR[1] = $PALETTE[$COLOR[1] + 1];
                    }elseif ( $BMP['bits_per_pixel'] == 4 ){
                        $COLOR = unpack( "n", $VIDE . substr( $IMG, floor( $P ), 1 ) );
                        if ( ($P * 2) % 2 == 0 )
                            $COLOR[1] = ($COLOR[1] >> 4);
                                    else
                                        $COLOR[1] = ($COLOR[1] & 0x0F);
                        $COLOR[1] = $PALETTE[$COLOR[1] + 1];
                    }elseif ( $BMP['bits_per_pixel'] == 1 ){
                        $COLOR = unpack( "n", $VIDE . substr( $IMG, floor( $P ), 1 ) );
                        if ( ($P * 8) % 8 == 0 )
                            $COLOR[1] = $COLOR[1] >> 7;
                        elseif ( ($P * 8) % 8 == 1 )
                            $COLOR[1] = ($COLOR[1] & 0x40) >> 6;
                        elseif ( ($P * 8) % 8 == 2 )
                            $COLOR[1] = ($COLOR[1] & 0x20) >> 5;
                        elseif ( ($P * 8) % 8 == 3 )
                            $COLOR[1] = ($COLOR[1] & 0x10) >> 4;
                        elseif ( ($P * 8) % 8 == 4 )
                            $COLOR[1] = ($COLOR[1] & 0x8) >> 3;
                        elseif ( ($P * 8) % 8 == 5 )
                            $COLOR[1] = ($COLOR[1] & 0x4) >> 2;
                        elseif ( ($P * 8) % 8 == 6 )
                            $COLOR[1] = ($COLOR[1] & 0x2) >> 1;
                        elseif ( ($P * 8) % 8 == 7 )
                            $COLOR[1] = ($COLOR[1] & 0x1);
                        $COLOR[1] = $PALETTE[$COLOR[1] + 1];
                                }else
                                return FALSE;
                    imagesetpixel( $res, $X, $Y, $COLOR[1] );
                            $X++;
                    $P += $BMP['bytes_per_pixel'];
                        }
                    $Y--;
                $P += $BMP['decal'];
                    }
        fclose( $f1 );
            return $res;
    }
//--end-of-class--	
}

/////////////////////////////////////////////////////////////////////////////

?>
