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


/** PHPExcel_Style_Border */
require_once 'PHPExcel/Style/Border.php';

/** PHPExcel_IComparable */
require_once 'PHPExcel/IComparable.php';


/**
 * PHPExcel_Style_Borders
 *
 * @category   PHPExcel
 * @package    PHPExcel_Style
 * @copyright  Copyright (c) 2006 - 2007 PHPExcel (http://www.codeplex.com/PHPExcel)
 */
class PHPExcel_Style_Borders implements PHPExcel_IComparable
{
	/* Diagonal directions */
	const DIAGONAL_NONE		= 0;
	const DIAGONAL_UP		= 1;
	const DIAGONAL_DOWN		= 2;
	
	/**
	 * Left
	 *
	 * @var int
	 */
	private $_left;
	
	/**
	 * Right
	 *
	 * @var int
	 */
	private $_right;
	
	/**
	 * Top
	 *
	 * @var int
	 */
	private $_top;
	
	/**
	 * Bottom
	 *
	 * @var int
	 */
	private $_bottom;
	
	/**
	 * Diagonal
	 *
	 * @var int
	 */
	private $_diagonal;
	
	/**
	 * Vertical
	 *
	 * @var int
	 */
	private $_vertical;
	
	/**
	 * Horizontal
	 *
	 * @var int
	 */
	private $_horizontal;
	
	/**
	 * DiagonalDirection
	 *
	 * @var int
	 */
	private $_diagonalDirection;
	
	/**
	 * Outline, defaults to true
	 *
	 * @var boolean
	 */
	private $_outline;
		
    /**
     * Create a new PHPExcel_Style_Borders
     */
    public function __construct()
    {
    	// Initialise values
		$this->_left				= new PHPExcel_Style_Border();
		$this->_right				= new PHPExcel_Style_Border();
		$this->_top					= new PHPExcel_Style_Border();
		$this->_bottom				= new PHPExcel_Style_Border();
		$this->_diagonal			= new PHPExcel_Style_Border();
		$this->_vertical			= new PHPExcel_Style_Border();
		$this->_horizontal			= new PHPExcel_Style_Border();
	
    	$this->_diagonalDirection	= PHPExcel_Style_Borders::DIAGONAL_NONE;
    	$this->_outline				= true;
    }
    
    /**
     * Get Left
     *
     * @return PHPExcel_Style_Border
     */
    public function getLeft() {
    	return $this->_left;
    }
    
    /**
     * Get Right
     *
     * @return PHPExcel_Style_Border
     */
    public function getRight() {
    	return $this->_right;
    }
       
    /**
     * Get Top
     *
     * @return PHPExcel_Style_Border
     */
    public function getTop() {
    	return $this->_top;
    }
    
    /**
     * Get Bottom
     *
     * @return PHPExcel_Style_Border
     */
    public function getBottom() {
    	return $this->_bottom;
    }

    /**
     * Get Diagonal
     *
     * @return PHPExcel_Style_Border
     */
    public function getDiagonal() {
    	return $this->_diagonal;
    }
    
    /**
     * Get Vertical
     *
     * @return PHPExcel_Style_Border
     */
    public function getVertical() {
    	return $this->_vertical;
    }
    
    /**
     * Get Horizontal
     *
     * @return PHPExcel_Style_Border
     */
    public function getHorizontal() {
    	return $this->_horizontal;
    }
    
    /**
     * Get DiagonalDirection
     *
     * @return int
     */
    public function getDiagonalDirection() {
    	return $this->_diagonalDirection;
    }
    
    /**
     * Set DiagonalDirection
     *
     * @param int $pValue
     */
    public function setDiagonalDirection($pValue = PHPExcel_Style_Borders::DIAGONAL_NONE) {
    	$this->_diagonalDirection = $pValue;
    }
    
    /**
     * Get Outline
     *
     * @return boolean
     */
    public function getOutline() {
    	return $this->_outline;
    }
    
    /**
     * Set Outline
     *
     * @param boolean $pValue
     */
    public function setOutline($pValue = true) {
    	$this->_outline = $pValue;
    }
    
	/**
	 * Get hash code
	 *
	 * @return string	Hash code
	 */	
	public function getHashCode() {
    	return md5(
    		  $this->getLeft()->getHashCode()
    		. $this->getRight()->getHashCode()
    		. $this->getTop()->getHashCode()
    		. $this->getBottom()->getHashCode()
    		. $this->getDiagonal()->getHashCode()
    		. $this->getVertical()->getHashCode()
    		. $this->getHorizontal()->getHashCode()
    		. $this->getDiagonalDirection()
    		. $this->getOutline()
    		. __CLASS__
    	);
    }
    
    /**
     * Duplicate object
     *
     * Duplicates the current object, also duplicating referenced objects (deep cloning).
     * Standard PHP clone does not copy referenced objects!
     *
     * @return PHPExcel_Style_Borders
     */
	public function duplicate() {
		return unserialize(serialize($this));
	}
}
