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
 * @package    PHPExcel_Style
 * @copyright  Copyright (c) 2006 - 2009 PHPExcel (http://www.codeplex.com/PHPExcel)
 * @license    http://www.gnu.org/licenses/old-licenses/lgpl-2.1.txt	LGPL
 * @version    ##VERSION##, ##DATE##
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
 * @copyright  Copyright (c) 2006 - 2009 PHPExcel (http://www.codeplex.com/PHPExcel)
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
	 * @var PHPExcel_Style_Border
	 */
	private $_left;
	
	/**
	 * Right
	 *
	 * @var PHPExcel_Style_Border
	 */
	private $_right;
	
	/**
	 * Top
	 *
	 * @var PHPExcel_Style_Border
	 */
	private $_top;
	
	/**
	 * Bottom
	 *
	 * @var PHPExcel_Style_Border
	 */
	private $_bottom;
	
	/**
	 * Diagonal
	 *
	 * @var PHPExcel_Style_Border
	 */
	private $_diagonal;
	
	/**
	 * Vertical
	 *
	 * @var PHPExcel_Style_Border
	 */
	private $_vertical;
	
	/**
	 * Horizontal
	 *
	 * @var PHPExcel_Style_Border
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
	 * Parent Borders
	 *
	 * @var _parentPropertyName string
	 */
	private $_parentPropertyName;

	/**
	 * Supervisor?
	 *
	 * @var boolean
	 */
	private $_isSupervisor;

	/**
	 * Parent. Only used for supervisor
	 *
	 * @var PHPExcel_Style
	 */
	private $_parent;

	/**
     * Create a new PHPExcel_Style_Borders
     */
    public function __construct($isSupervisor = false)
    {
    	// Supervisor?
		$this->_isSupervisor = $isSupervisor;

    	// Initialise values
    	$this->_left				= new PHPExcel_Style_Border($isSupervisor);
    	$this->_right				= new PHPExcel_Style_Border($isSupervisor);
    	$this->_top					= new PHPExcel_Style_Border($isSupervisor);
    	$this->_bottom				= new PHPExcel_Style_Border($isSupervisor);
    	$this->_diagonal			= new PHPExcel_Style_Border($isSupervisor);
    	$this->_vertical			= new PHPExcel_Style_Border($isSupervisor);
    	$this->_horizontal			= new PHPExcel_Style_Border($isSupervisor);
		$this->_diagonalDirection	= PHPExcel_Style_Borders::DIAGONAL_NONE;
    	$this->_outline				= true;

		// bind parent if we are a supervisor
		if ($isSupervisor) {
			$this->_left->bindParent($this, '_left');
			$this->_right->bindParent($this, '_right');
			$this->_top->bindParent($this, '_top');
			$this->_bottom->bindParent($this, '_bottom');
			$this->_diagonal->bindParent($this, '_diagonal');
			$this->_vertical->bindParent($this, '_vertical');
			$this->_horizontal->bindParent($this, '_horizontal');
		}
    }

	/**
	 * Bind parent. Only used for supervisor
	 *
	 * @param PHPExcel_Style $parent
	 */
	public function bindParent($parent)
	{
		$this->_parent = $parent;
	}

	/**
	 * Is this a supervisor or a real style component?
	 *
	 * @return boolean
	 */
	public function getIsSupervisor()
	{
		return $this->_isSupervisor;
	}

	/**
	 * Get the shared style component for the currently active cell in currently active sheet.
	 * Only used for style supervisor
	 *
	 * @return PHPExcel_Style_Borders
	 */
	public function getSharedComponent()
	{
		return $this->_parent->getSharedComponent()->getBorders();
	}

	/**
	 * Get the currently active sheet. Only used for supervisor
	 *
	 * @return PHPExcel_Worksheet
	 */
	public function getActiveSheet()
	{
		return $this->_parent->getActiveSheet();
	}

	/**
	 * Get the currently active cell coordinate in currently active sheet.
	 * Only used for supervisor
	 *
	 * @return string E.g. 'A1'
	 */
	public function getSelectedCell()
	{
		return $this->getActiveSheet()->getSelectedCell();
	}

	/**
	 * Build style array from subcomponents
	 *
	 * @param array $array
	 * @return array
	 */
	public function getStyleArray($array)
	{
		return array('borders' => $array);
	}

	/**
     * Apply styles from array
     * 
     * <code>
     * $objPHPExcel->getActiveSheet()->getStyle('B2')->getBorders()->applyFromArray(
     * 		array(
     * 			'bottom'     => array(
     * 				'style' => PHPExcel_Style_Border::BORDER_DASHDOT,
     * 				'color' => array(
     * 					'rgb' => '808080'
     * 				)
     * 			),
     * 			'top'     => array(
     * 				'style' => PHPExcel_Style_Border::BORDER_DASHDOT,
     * 				'color' => array(
     * 					'rgb' => '808080'
     * 				)
     * 			)
     * 		)
     * );
     * </code>
     * <code>
     * $objPHPExcel->getActiveSheet()->getStyle('B2')->getBorders()->applyFromArray(
     * 		array(
     * 			'allborders' => array(
     * 				'style' => PHPExcel_Style_Border::BORDER_DASHDOT,
     * 				'color' => array(
     * 					'rgb' => '808080'
     * 				)
     * 			)
     * 		)
     * );
     * </code>
     * 
     * @param	array	$pStyles	Array containing style information
     * @throws	Exception
     */
    public function applyFromArray($pStyles = null) {
        if (is_array($pStyles)) {
            if (array_key_exists('allborders', $pStyles)) {
        		$this->getLeft()->applyFromArray($pStyles['allborders']);
        		$this->getRight()->applyFromArray($pStyles['allborders']);
        		$this->getTop()->applyFromArray($pStyles['allborders']);
        		$this->getBottom()->applyFromArray($pStyles['allborders']);
        	}
        	if (array_key_exists('left', $pStyles)) {
        		$this->getLeft()->applyFromArray($pStyles['left']);
        	}
        	if (array_key_exists('right', $pStyles)) {
        		$this->getRight()->applyFromArray($pStyles['right']);
        	}
        	if (array_key_exists('top', $pStyles)) {
        		$this->getTop()->applyFromArray($pStyles['top']);
        	}
        	if (array_key_exists('bottom', $pStyles)) {
        		$this->getBottom()->applyFromArray($pStyles['bottom']);
        	}
        	if (array_key_exists('diagonal', $pStyles)) {
        		$this->getDiagonal()->applyFromArray($pStyles['diagonal']);
        	}
        	if (array_key_exists('vertical', $pStyles)) {
        		$this->getVertical()->applyFromArray($pStyles['vertical']);
        	}
        	if (array_key_exists('horizontal', $pStyles)) {
        		$this->getHorizontal()->applyFromArray($pStyles['horizontal']);
        	}
        	if (array_key_exists('diagonaldirection', $pStyles)) {
        		$this->setDiagonalDirection($pStyles['diagonaldirection']);
        	}
        	if (array_key_exists('outline', $pStyles)) {
        		$this->setOutline($pStyles['outline']);
        	}
    	} else {
    		throw new Exception("Invalid style array passed.");
    	}
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
		if ($this->_isSupervisor) {
			return $this->getSharedComponent()->getDiagonalDirection();
		}
    	return $this->_diagonalDirection;
    }
    
    /**
     * Set DiagonalDirection
     *
     * @param int $pValue
     */
    public function setDiagonalDirection($pValue = PHPExcel_Style_Borders::DIAGONAL_NONE) {
        if ($pValue == '') {
    		$pValue = PHPExcel_Style_Borders::DIAGONAL_NONE;
    	}
		if ($this->_isSupervisor) {
			$styleArray = $this->getStyleArray(array('diagonaldirection' => $pValue));
			$this->getActiveSheet()->duplicateStyleArray($styleArray, $this->getSelectedCell());
		} else {
			$this->_diagonalDirection = $pValue;
		}
    }
    
    /**
     * Get Outline
     *
     * @return boolean
     */
    public function getOutline() {
		if ($this->_isSupervisor) {
			return $this->getSharedComponent()->getOutline();
		}
    	return $this->_outline;
    }
    
    /**
     * Set Outline
     *
     * @param boolean $pValue
     */
    public function setOutline($pValue = true) {
        if ($pValue == '') {
    		$pValue = true;
    	}
		if ($this->_isSupervisor) {
			$styleArray = $this->getStyleArray(array('outline' => $pValue));
			$this->getActiveSheet()->duplicateStyleArray($styleArray, $this->getSelectedCell());
		} else {
			$this->_outline = $pValue;
		}
    }
    
	/**
	 * Get hash code
	 *
	 * @return string	Hash code
	 */	
	public function getHashCode() {
		if ($this->_isSupervisor) {
			return $this->getSharedComponent()->getHashcode();
		}
    	return md5(
    		  $this->getLeft()->getHashCode()
    		. $this->getRight()->getHashCode()
    		. $this->getTop()->getHashCode()
    		. $this->getBottom()->getHashCode()
    		. $this->getDiagonal()->getHashCode()
    		. $this->getVertical()->getHashCode()
    		. $this->getHorizontal()->getHashCode()
    		. $this->getDiagonalDirection()
    		. ($this->getOutline() ? 't' : 'f')
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
