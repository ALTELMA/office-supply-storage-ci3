<?php
/**
 * PHPExcel
 *
 * Copyright (c) 2006 - 2009 PHPExcel
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 * 
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 * 
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA
 *
 * @category   PHPExcel
 * @package    PHPExcel_Worksheet_ChartDrawing
 * @copyright  Copyright (c) 2006 - 2009 PHPExcel (http://www.codeplex.com/PHPExcel)
 * @license    http://www.gnu.org/licenses/old-licenses/lgpl-2.1.txt	LGPL
 * @version    ##VERSION##, ##DATE##
 */


/** PHPExcel_IComparable */
require_once 'PHPExcel/IComparable.php';

/**
 * PHPExcel_Worksheet_ChartDrawing_Legend
 *
 * @category   PHPExcel
 * @package    PHPExcel_Worksheet_ChartDrawing
 * @copyright  Copyright (c) 2006 - 2009 PHPExcel (http://www.codeplex.com/PHPExcel)
 */
class PHPExcel_Worksheet_ChartDrawing_Legend implements PHPExcel_IComparable
{
	/** Legend positions */
	const POSITION_RIGHT 	= 'r';
	const POSITION_LEFT 	= 'l';
	const POSITION_BOTTOM 	= 'b';
	const POSITION_TOP 		= 't';
	const POSITION_TOPRIGHT	= 'tr';

	/**
	 * Legend position
	 * 
	 * @var string
	 */
	private $_position = self::POSITION_RIGHT;
	
	/**
	 * Allow overlay of other elements?
	 * 
	 * @var boolean
	 */
	private $_overlay = true;
	
	/**
     * Create a new PHPExcel_Worksheet_ChartDrawing_Legend
     */
    public function __construct()
    {
    	$self->_position = self::POSITION_RIGHT;
    	$self->_overlay = true;
    }
    
    /**
     * Get legend position
     * 
     * @return string
     */
    public function getPosition() {
    	return $this->_position;
    }
    
    /**
     * Set legend position
     * 
     * @param string $position
     */
    public function setPosition($position = self::POSITION_RIGHT) {
    	$this->_position = $position;
    }
    
	/**
     * Get allow overlay of other elements?
     * 
     * @return boolean
     */
    public function getOverlay() {
    	return $this->_overlay;
    }
    
    /**
     * Set allow overlay of other elements?
     * 
     * @param boolean $value
     */
    public function setOverlay($value) {
    	$this->_overlay = $value;
    }
	
	/**
	 * Get hash code
	 *
	 * @return string	Hash code
	 */	
	public function getHashCode() {
    	return md5(
    		  $this->_position
    		. ($this->_overlay ? 't' : 'f')
    		. __CLASS__
    	);
    }
    
    /**
     * Hash index
     *
     * @var string
     */
    private $_hashIndex;
    
	/**
	 * Get hash index
	 * 
	 * Note that this index may vary during script execution! Only reliable moment is
	 * while doing a write of a workbook and when changes are not allowed.
	 *
	 * @return string	Hash index
	 */
	public function getHashIndex() {
		return $this->_hashIndex;
	}
	
	/**
	 * Set hash index
	 * 
	 * Note that this index may vary during script execution! Only reliable moment is
	 * while doing a write of a workbook and when changes are not allowed.
	 *
	 * @param string	$value	Hash index
	 */
	public function setHashIndex($value) {
		$this->_hashIndex = $value;
	}
        
	/**
	 * Implement PHP __clone to create a deep clone, not just a shallow copy.
	 */
	public function __clone() {
		$vars = get_object_vars($this);
		foreach ($vars as $key => $value) {
			if (is_object($value)) {
				$this->$key = clone $value;
			} else {
				$this->$key = $value;
			}
		}
	}
}
