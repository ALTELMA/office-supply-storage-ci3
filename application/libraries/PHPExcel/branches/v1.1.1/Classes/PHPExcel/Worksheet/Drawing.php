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
 * @package    PHPExcel_Worksheet
 * @copyright  Copyright (c) 2006 - 2007 PHPExcel (http://www.codeplex.com/PHPExcel)
 * @license    http://www.gnu.org/licenses/gpl.txt	GPL
 */


/** PHPExcel_IComparable */
require_once 'PHPExcel/IComparable.php';

/** PHPExcel_Worksheet */
require_once 'PHPExcel/Worksheet.php';

/** PHPExcel_Worksheet_Drawing_Shadow */
require_once 'PHPExcel/Worksheet/Drawing/Shadow.php';

/**
 * PHPExcel_Worksheet_Drawing
 *
 * @category   PHPExcel
 * @package    PHPExcel_Worksheet
 * @copyright  Copyright (c) 2006 - 2007 PHPExcel (http://www.codeplex.com/PHPExcel)
 */
class PHPExcel_Worksheet_Drawing implements PHPExcel_IComparable
{		
	/**
	 * Name
	 *
	 * @var string
	 */
	private $_name;
	
	/**
	 * Description
	 *
	 * @var string
	 */
	private $_description;
	
	/**
	 * Path
	 *
	 * @var string
	 */
	private $_path;
	
	/**
	 * Worksheet
	 *
	 * @var PHPExcel_Worksheet
	 */
	private $_worksheet;
	
	/**
	 * Coordinates
	 *
	 * @var string
	 */
	private $_coordinates;
	
	/**
	 * Offset X
	 *
	 * @var int
	 */
	private $_offsetX;
	
	/**
	 * Offset Y
	 *
	 * @var int
	 */
	private $_offsetY;
	
	/**
	 * Width
	 *
	 * @var int
	 */
	private $_width;
	
	/**
	 * Height
	 *
	 * @var int
	 */
	private $_height;
	
	/**
	 * Proportional resize
	 *
	 * @var boolean
	 */
	private $_resizeProportional;
	
	/**
	 * Rotation
	 *
	 * @var int
	 */
	private $_rotation;
	
	/**
	 * Shadow
	 *
	 * @var PHPExcel_Worksheet_Drawing_Shadow
	 */
	private $_shadow;	
	
    /**
     * Create a new PHPExcel_Worksheet_Drawing
     */
    public function __construct()
    {
    	// Initialise values
    	$this->_name				= '';
    	$this->_description			= '';
    	$this->_path				= '';
    	$this->_worksheet			= null;
    	$this->_coordinates			= 'A1';
    	$this->_offsetX				= 0;
    	$this->_offsetY				= 0;
    	$this->_width				= 0;
    	$this->_height				= 0;
    	$this->_resizeProportional	= true;
    	$this->_rotation			= 0;
    	$this->_shadow				= new PHPExcel_Worksheet_Drawing_Shadow();
    }
       
    /**
     * Get Name
     *
     * @return string
     */
    public function getName() {
    	return $this->_name;
    }
    
    /**
     * Set Name
     *
     * @param string $pValue
     */
    public function setName($pValue = '') {
    	$this->_name = $pValue;
    }
    
    /**
     * Get Filename
     *
     * @return string
     */
    public function getFilename() {
    	return basename($this->_path);
    }
    
    /**
     * Get Extension
     *
     * @return string
     */
    public function getExtension() {
    	return end(explode(".", basename($this->_path)));
    }
    
    /**
     * Get Description
     *
     * @return string
     */
    public function getDescription() {
    	return $this->_description;
    }
    
    /**
     * Set Description
     *
     * @param string $pValue
     */
    public function setDescription($pValue = '') {
    	$this->_description = $pValue;
    }
    
    /**
     * Get Path
     *
     * @return string
     */
    public function getPath() {
    	return $this->_path;
    }
    
    /**
     * Set Path
     *
     * @param 	string $pValue
     * @throws 	Exception
     */
    public function setPath($pValue = '') {
    	if (file_exists($pValue)) {
    		$this->_path = $pValue;
    		
    		if ($this->_width == 0 && $this->_height == 0) {
    			// Get width/height
    			list($this->_width, $this->_height) = getimagesize($pValue);
    		}
    	} else {
    		throw new Exception("File $pValue not found!");
    	}
    }

    /**
     * Get Worksheet
     *
     * @return PHPExcel_Worksheet
     */
    public function getWorksheet() {
    	return $this->_worksheet;
    }
    
    /**
     * Set Worksheet
     *
     * @param 	PHPExcel_Worksheet 	$pValue
     * @param 	bool					$pOverrideOld	If a Worksheet has already been assigned, overwrite it and remove image from old Worksheet?
     * @throws 	Exception
     */
    public function setWorksheet($pValue = null, $pOverrideOld = false) {
    	if ($pValue instanceof PHPExcel_Worksheet) {
    		if (is_null($this->_worksheet)) {
    			// Add drawing to PHPExcel_Worksheet
	    		$this->_worksheet = $pValue;
	    		$this->_worksheet->getDrawingCollection()->append($this);
    		} else {
    			if ($pOverrideOld) {
    				// Remove drawing from old PHPExcel_Worksheet
    				$iterator = $this->_worksheet->getDrawingCollection()->getIterator();
    				
    				while ($iterator->valid()) {
    					if ($iterator->current() == $this) {
    						$this->_worksheet->getDrawingCollection()->offsetUnset( $iterator->key() );
    						$this->_worksheet = null;
    						break;
    					}
    				}
    				
    				// Set new PHPExcel_Worksheet
    				$this->setWorksheet($pValue);
    			} else {
    				throw new Exception("A PHPExcel_Worksheet has already been assigned. Drawings can only exist on one PHPExcel_Worksheet.");
    			}
    		}
    	} else {
    		throw new Exception("Invalid PHPExcel_Worksheet passed.");
    	}
    }
    
    /**
     * Get Coordinates
     *
     * @return string
     */
    public function getCoordinates() {
    	return $this->_coordinates;
    }    
    
    /**
     * Set Coordinates
     *
     * @param string $pValue
     */
    public function setCoordinates($pValue = 'A1') {
    	$this->_coordinates = $pValue;
    }
    
    /**
     * Get OffsetX
     *
     * @return int
     */
    public function getOffsetX() {
    	return $this->_offsetX;
    }
    
    /**
     * Set OffsetX
     *
     * @param int $pValue
     */
    public function setOffsetX($pValue = 0) {
    	$this->_offsetX = $pValue;
    }
    
    /**
     * Get OffsetY
     *
     * @return int
     */
    public function getOffsetY() {
    	return $this->_offsetY;
    }
    
    /**
     * Set OffsetY
     *
     * @param int $pValue
     */
    public function setOffsetY($pValue = 0) {
    	$this->_offsetY = $pValue;
    }
    
    /**
     * Get Width
     *
     * @return int
     */
    public function getWidth() {
    	return $this->_width;
    }
    
    /**
     * Set Width
     *
     * @param int $pValue
     */
    public function setWidth($pValue = 0) {
    	// Resize proportional?
    	if ($this->_resizeProportional && $pValue != 0) {
    		$ratio = $this->_width / $this->_height;    		
    		$this->_height = $ratio * $pValue;
    	}
    	
    	// Set width
    	$this->_width = $pValue;
    }
    
    /**
     * Get Height
     *
     * @return int
     */
    public function getHeight() {
    	return $this->_height;
    }
    
    /**
     * Set Height
     *
     * @param int $pValue
     */
    public function setHeight($pValue = 0) {
    	// Resize proportional?
    	if ($this->_resizeProportional && $pValue != 0) {
    		$ratio = $this->_width / $this->_height;   		
    		$this->_width = $ratio * $pValue;
    	}
    	
    	// Set height
    	$this->_height = $pValue;
    }
    
    /**
     * Get ResizeProportional
     *
     * @return boolean
     */
    public function getResizeProportional() {
    	return $this->_resizeProportional;
    }
    
    /**
     * Set ResizeProportional
     *
     * @param boolean $pValue
     */
    public function setResizeProportional($pValue = true) {
    	$this->_resizeProportional = $pValue;
    }
    
    /**
     * Get Rotation
     *
     * @return int
     */
    public function getRotation() {
    	return $this->_rotation;
    }
    
    /**
     * Set Rotation
     *
     * @param int $pValue
     */
    public function setRotation($pValue = 0) {
    	$this->_rotation = $pValue;
    }
    
    /**
     * Get Shadow
     *
     * @return PHPExcel_Worksheet_Drawing_Shadow
     */
    public function getShadow() {
    	return $this->_shadow;
    }
    
    /**
     * Set Shadow
     *
     * @param 	PHPExcel_Worksheet_Drawing_Shadow $pValue
     * @throws 	Exception
     */
    public function setShadow($pValue = null) {
    	if ($pValue instanceof PHPExcel_Worksheet_Drawing_Shadow) {
    		$this->_shadow = $pValue;
    	} else {
    		throw new Exception("Invalid PHPExcel_Worksheet_Drawing_Shadow passed.");
    	}
    }

	/**
	 * Get hash code
	 *
	 * @return string	Hash code
	 */	
	public function getHashCode() {
    	return md5(
    		  $this->_name
    		. $this->_description
    		. $this->_path
    		. $this->_worksheet->getHashCode()
    		. $this->_coordinates
    		. $this->_offsetX
    		. $this->_offsetY
    		. $this->_width
    		. $this->_height
    		. $this->_rotation
    		. $this->_shadow->getHashCode()
    		. __CLASS__
    	);
    }
        
    /**
     * Duplicate object
     *
     * Duplicates the current object, also duplicating referenced objects (deep cloning).
     * Standard PHP clone does not copy referenced objects!
     *
     * @return PHPExcel_Worksheet_Drawing
     */
	public function duplicate() {
		return unserialize(serialize($this));
	}
}
