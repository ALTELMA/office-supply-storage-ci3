<?php
/**
 * PHPExcel
 *
 * Copyright (c) 2006 - 2007 PHPExcel, Maarten Balliauw
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License along
 * with this program; if not, write to the Free Software Foundation, Inc.,
 * 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.
 *
 * @category   PHPExcel
 * @package    PHPExcel_Style
 * @copyright  Copyright (c) 2006 - 2007 PHPExcel (http://www.codeplex.com/PHPExcel)
 * @license    http://www.gnu.org/licenses/gpl.txt	GPL
 */


/** PHPExcel_Style_Color */
require_once 'PHPExcel/Style/Color.php';

/** PHPExcel_IComparable */
require_once 'PHPExcel/IComparable.php';


/**
 * PHPExcel_Style_Fill
 *
 * @category   PHPExcel
 * @package    PHPExcel_Style
 * @copyright  Copyright (c) 2006 - 2007 PHPExcel (http://www.codeplex.com/PHPExcel)
 */
class PHPExcel_Style_Fill implements PHPExcel_IComparable
{
	/* Fill types */
	const FILL_NONE							= 'none';
	const FILL_SOLID						= 'solid';
	const FILL_GRADIENT_LINEAR				= 'linear';
	const FILL_GRADIENT_PATH				= 'path';
	const FILL_PATTERN_DARKDOWN				= 'darkDown';
	const FILL_PATTERN_DARKGRAY				= 'darkGray';
	const FILL_PATTERN_DARKGRID				= 'darkGrid';
	const FILL_PATTERN_DARKHORIZONTAL		= 'darkHorizontal';
	const FILL_PATTERN_DARKTRELLIS			= 'darkTrellis';
	const FILL_PATTERN_DARKUP				= 'darkUp';
	const FILL_PATTERN_DARKVERTICAL			= 'darkVertical';
	const FILL_PATTERN_GRAY0625				= 'gray0625';
	const FILL_PATTERN_GRAY125				= 'gray125';
	const FILL_PATTERN_LIGHTDOWN			= 'lightDown';
	const FILL_PATTERN_LIGHTGRAY			= 'lightGray';
	const FILL_PATTERN_LIGHTGRID			= 'lightGrid';
	const FILL_PATTERN_LIGHTHORIZONTAL		= 'lightHorizontal';
	const FILL_PATTERN_LIGHTTRELLIS			= 'lightTrellis';
	const FILL_PATTERN_LIGHTUP				= 'lightUp';
	const FILL_PATTERN_LIGHTVERTICAL		= 'lightVertical';
	const FILL_PATTERN_MEDIUMGRAY			= 'mediumGray';

	/**
	 * Fill type
	 *
	 * @var string
	 */
	private $_fillType;
	
	/**
	 * Rotation
	 *
	 * @var double
	 */
	private $_rotation;
	
	/**
	 * Start color
	 * 
	 * @var PHPExcel_Style_Color
	 */
	private $_startColor;
	
	/**
	 * End color
	 * 
	 * @var PHPExcel_Style_Color
	 */
	private $_endColor;
		
    /**
     * Create a new PHPExcel_Style_Fill
     */
    public function __construct()
    {
    	// Initialise values
    	$this->_fillType			= PHPExcel_Style_Fill::FILL_NONE;
    	$this->_rotation			= 0;
		$this->_startColor			= new PHPExcel_Style_Color(PHPExcel_Style_Color::COLOR_WHITE);
		$this->_endColor			= new PHPExcel_Style_Color(PHPExcel_Style_Color::COLOR_BLACK);
    }
    
    /**
     * Get Fill Type
     *
     * @return string
     */
    public function getFillType() {
    	return $this->_fillType;
    }
    
    /**
     * Set Fill Type
     *
     * @param string $pValue	PHPExcel_Style_Fill fill type
     */
    public function setFillType($pValue = PHPExcel_Style_Fill::FILL_NONE) {
    	$this->_fillType = $pValue;
    }
    
    /**
     * Get Rotation
     *
     * @return double
     */
    public function getRotation() {
    	return $this->_rotation;
    }
    
    /**
     * Set Rotation
     *
     * @param double $pValue
     */
    public function setRotation($pValue = 0) {
    	$this->_rotation = $pValue;
    }
    
    /**
     * Get Start Color
     *
     * @return PHPExcel_Style_Color
     */
    public function getStartColor() {
    	return $this->_startColor;
    }
    
    /**
     * Set Start Color
     *
     * @param 	PHPExcel_Style_Color $pValue
     * @throws 	Exception
     */
    public function setStartColor($pValue = null) {
    	if ($pValue instanceof PHPExcel_Style_Color) {
    		$this->_startColor = $pValue;
    	} else {
    		throw new Exception("Invalid PHPExcel_Style_Color passed.");
    	}
    }
    
    /**
     * Get End Color
     *
     * @return PHPExcel_Style_Color
     */
    public function getEndColor() {
    	return $this->_endColor;
    }
    
    /**
     * Set End Color
     *
     * @param 	PHPExcel_Style_Color $pValue
     * @throws 	Exception
     */
    public function setEndColor($pValue = null) {
    	if ($pValue instanceof PHPExcel_Style_Color) {
    		$this->_endColor = $pValue;
    	} else {
    		throw new Exception("Invalid PHPExcel_Style_Color passed.");
    	}
    }

	/**
	 * Get hash code
	 *
	 * @return string	Hash code
	 */	
	public function getHashCode() {
    	return md5(
    		  $this->_fillType
    		. $this->_rotation
    		. $this->_startColor->getHashCode()
    		. $this->_endColor->getHashCode()
    		. __CLASS__
    	);
    }
        
    /**
     * Duplicate object
     *
     * Duplicates the current object, also duplicating referenced objects (deep cloning).
     * Standard PHP clone does not copy referenced objects!
     *
     * @return PHPExcel_Style_Fill
     */
	public function duplicate() {
		return unserialize(serialize($this));
	}
}
