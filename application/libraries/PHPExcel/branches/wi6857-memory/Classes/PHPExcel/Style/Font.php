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


/** PHPExcel_Style_Color */
require_once 'PHPExcel/Style/Color.php';

/** PHPExcel_IComparable */
require_once 'PHPExcel/IComparable.php';


/**
 * PHPExcel_Style_Font
 *
 * @category   PHPExcel
 * @package    PHPExcel_Style
 * @copyright  Copyright (c) 2006 - 2009 PHPExcel (http://www.codeplex.com/PHPExcel)
 */
class PHPExcel_Style_Font implements PHPExcel_IComparable
{
	/* Underline types */
	const UNDERLINE_NONE					= 'none';
	const UNDERLINE_DOUBLE					= 'double';
	const UNDERLINE_DOUBLEACCOUNTING		= 'doubleAccounting';
	const UNDERLINE_SINGLE					= 'single';
	const UNDERLINE_SINGLEACCOUNTING		= 'singleAccounting';
	
	/**
	 * Name
	 *
	 * @var string
	 */
	private $_name;
	
	/**
	 * Bold
	 *
	 * @var boolean
	 */
	private $_bold;
	
	/**
	 * Italic
	 *
	 * @var boolean
	 */
	private $_italic;
	
	/**
	 * Superscript
	 *
	 * @var boolean
	 */
	private $_superScript;
	
	/**
	 * Subscript
	 *
	 * @var boolean
	 */
	private $_subScript;
	
	/**
	 * Underline
	 *
	 * @var string
	 */
	private $_underline;
	
	/**
	 * Striketrough
	 *
	 * @var boolean
	 */
	private $_striketrough;
	
	/**
	 * Foreground color
	 * 
	 * @var PHPExcel_Style_Color
	 */
	private $_color;	
	
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
     * Create a new PHPExcel_Style_Font
     */
    public function __construct($isSupervisor = false)
    {
    	// Supervisor?
		$this->_isSupervisor = $isSupervisor;

    	// Initialise values
    	$this->_name				= 'Calibri';
    	$this->_size				= 11;
		$this->_bold				= false;
		$this->_italic				= false;
		$this->_superScript			= false;
		$this->_subScript			= false;
		$this->_underline			= PHPExcel_Style_Font::UNDERLINE_NONE;
		$this->_striketrough		= false;
		$this->_color				= new PHPExcel_Style_Color(PHPExcel_Style_Color::COLOR_BLACK, $isSupervisor);

		// bind parent if we are a supervisor
		if ($isSupervisor) {
			$this->_color->bindParent($this, '_color');
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
	 * @return PHPExcel_Style_Font
	 */
	public function getSharedComponent()
	{
		return $this->_parent->getSharedComponent()->getFont();
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
		return array('font' => $array);
	}

    /**
     * Apply styles from array
     * 
     * <code>
     * $objPHPExcel->getActiveSheet()->getStyle('B2')->getFont()->applyFromArray(
     * 		array(
     * 			'name'      => 'Arial',
     * 			'bold'      => true,
     * 			'italic'    => false,
     * 			'underline' => PHPExcel_Style_Font::UNDERLINE_DOUBLE,
     * 			'strike'    => false,
     * 			'color'     => array(
     * 				'rgb' => '808080'
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
        	if (array_key_exists('name', $pStyles)) {
    			$this->setName($pStyles['name']);
    		}
        	if (array_key_exists('bold', $pStyles)) {
    			$this->setBold($pStyles['bold']);
    		}
    		if (array_key_exists('italic', $pStyles)) {
    			$this->setItalic($pStyles['italic']);
    		}
			if (array_key_exists('superScript', $pStyles)) {
    			$this->setSuperScript($pStyles['superScript']);
    		}
			if (array_key_exists('subScript', $pStyles)) {
    			$this->setSubScript($pStyles['subScript']);
    		}
            if (array_key_exists('underline', $pStyles)) {
    			$this->setUnderline($pStyles['underline']);
    		}
            if (array_key_exists('strike', $pStyles)) {
    			$this->setStriketrough($pStyles['strike']);
    		}
            if (array_key_exists('color', $pStyles)) {
    			$this->getColor()->applyFromArray($pStyles['color']);
    		}
        	if (array_key_exists('size', $pStyles)) {
    			$this->setSize($pStyles['size']);
    		}
    	} else {
    		throw new Exception("Invalid style array passed.");
    	}
    }
    
    /**
     * Get Name
     *
     * @return string
     */
    public function getName() {
		if ($this->_isSupervisor) {
			return $this->getSharedComponent()->getName();
		}
    	return $this->_name;
    }
    
    /**
     * Set Name
     *
     * @param string $pValue
     */
    public function setName($pValue = 'Calibri') {
   		if ($pValue == '') {
    		$pValue = 'Calibri';
    	}
		if ($this->_isSupervisor) {
			$styleArray = $this->getStyleArray(array('name' => $pValue));
			$this->getActiveSheet()->duplicateStyleArray($styleArray, $this->getSelectedCell());
		} else {
			$this->_name = $pValue;
		}
    }
    
    /**
     * Get Size
     *
     * @return double
     */
    public function getSize() {
		if ($this->_isSupervisor) {
			return $this->getSharedComponent()->getSize();
		}
    	return $this->_size;
    }
    
    /**
     * Set Size
     *
     * @param double $pValue
     */
    public function setSize($pValue = 10) {
    	if ($pValue == '') {
    		$pValue = 10;
    	}
		if ($this->_isSupervisor) {
			$styleArray = $this->getStyleArray(array('size' => $pValue));
			$this->getActiveSheet()->duplicateStyleArray($styleArray, $this->getSelectedCell());
		} else {
			$this->_size = $pValue;
		}
    }
    
    /**
     * Get Bold
     *
     * @return boolean
     */
    public function getBold() {
		if ($this->_isSupervisor) {
			return $this->getSharedComponent()->getBold();
		}
    	return $this->_bold;
    }
    
    /**
     * Set Bold
     *
     * @param boolean $pValue
     */
    public function setBold($pValue = false) {
    	if ($pValue == '') {
    		$pValue = false;
    	}
		if ($this->_isSupervisor) {
			$styleArray = $this->getStyleArray(array('bold' => $pValue));
			$this->getActiveSheet()->duplicateStyleArray($styleArray, $this->getSelectedCell());
		} else {
			$this->_bold = $pValue;
		}
    }
    
    /**
     * Get Italic
     *
     * @return boolean
     */
    public function getItalic() {
		if ($this->_isSupervisor) {
			return $this->getSharedComponent()->getItalic();
		}
    	return $this->_italic;
    }
    
    /**
     * Set Italic
     *
     * @param boolean $pValue
     */
    public function setItalic($pValue = false) {
    	if ($pValue == '') {
    		$pValue = false;
    	}
		if ($this->_isSupervisor) {
			$styleArray = $this->getStyleArray(array('italic' => $pValue));
			$this->getActiveSheet()->duplicateStyleArray($styleArray, $this->getSelectedCell());
		} else {
			$this->_italic = $pValue;
		}
    }
	
    /**
     * Get SuperScript
     *
     * @return boolean
     */
    public function getSuperScript() {
		if ($this->_isSupervisor) {
			return $this->getSharedComponent()->getSuperScript();
		}
    	return $this->_superScript;
    }
    
    /**
     * Set SuperScript
     *
     * @param boolean $pValue
     */
    public function setSuperScript($pValue = false) {
    	if ($pValue == '') {
    		$pValue = false;
    	}
		if ($this->_isSupervisor) {
			$styleArray = $this->getStyleArray(array('superScript' => $pValue));
			$this->getActiveSheet()->duplicateStyleArray($styleArray, $this->getSelectedCell());
		} else {
			$this->_superScript = $pValue;
			$this->_subScript = !$pValue;
		}
    }
	
	    /**
     * Get SubScript
     *
     * @return boolean
     */
    public function getSubScript() {
		if ($this->_isSupervisor) {
			return $this->getSharedComponent()->getSubScript();
		}
    	return $this->_subScript;
    }
    
    /**
     * Set SubScript
     *
     * @param boolean $pValue
     */
    public function setSubScript($pValue = false) {
    	if ($pValue == '') {
    		$pValue = false;
    	}
		if ($this->_isSupervisor) {
			$styleArray = $this->getStyleArray(array('subScript' => $pValue));
			$this->getActiveSheet()->duplicateStyleArray($styleArray, $this->getSelectedCell());
		} else {
			$this->_subScript = $pValue;
			$this->_superScript = !$pValue;
		}
    }
    
    /**
     * Get Underline
     *
     * @return string
     */
    public function getUnderline() {
		if ($this->_isSupervisor) {
			return $this->getSharedComponent()->getUnderline();
		}
    	return $this->_underline;
    }
    
    /**
     * Set Underline
     *
     * @param string $pValue	PHPExcel_Style_Font underline type
     */
    public function setUnderline($pValue = PHPExcel_Style_Font::UNDERLINE_NONE) {
    	if ($pValue == '') {
    		$pValue = PHPExcel_Style_Font::UNDERLINE_NONE;
    	}
		if ($this->_isSupervisor) {
			$styleArray = $this->getStyleArray(array('underline' => $pValue));
			$this->getActiveSheet()->duplicateStyleArray($styleArray, $this->getSelectedCell());
		} else {
			$this->_underline = $pValue;
		}
    }
    
    /**
     * Get Striketrough
     *
     * @return boolean
     */
    public function getStriketrough() {
		if ($this->_isSupervisor) {
			return $this->getSharedComponent()->getStriketrough();
		}
    	return $this->_striketrough;
    }
    
    /**
     * Set Striketrough
     *
     * @param boolean $pValue
     */
    public function setStriketrough($pValue = false) {
    	if ($pValue == '') {
    		$pValue = false;
    	}
		if ($this->_isSupervisor) {
			$styleArray = $this->getStyleArray(array('strike' => $pValue));
			$this->getActiveSheet()->duplicateStyleArray($styleArray, $this->getSelectedCell());
		} else {
			$this->_striketrough = $pValue;
		}
    }

    /**
     * Get Color
     *
     * @return PHPExcel_Style_Color
     */
    public function getColor() {
    	return $this->_color;
    }
    
    /**
     * Set Color
     *
     * @param 	PHPExcel_Style_Color $pValue
     * @throws 	Exception
     */
    public function setColor(PHPExcel_Style_Color $pValue = null) {
		// make sure parameter is a real color and not a supervisor
		$color = $pValue->getIsSupervisor() ? $pValue->getSharedComponent() : $pValue;
		
		if ($this->_isSupervisor) {
			$styleArray = $this->getColor()->getStyleArray(array('argb' => $color->getARGB()));
			$this->getActiveSheet()->duplicateStyleArray($styleArray, $this->getSelectedCell());
		} else {
			$this->_color = $color;
		}
    }

	/**
	 * Get hash code
	 *
	 * @return string	Hash code
	 */	
	public function getHashCode() {
		if ($this->_isSupervisor) {
			return $this->getSharedComponent()->getHashCode();
		}
    	return md5(
    		  $this->_name
    		. $this->_size
    		. ($this->_bold ? 't' : 'f')
    		. ($this->_italic ? 't' : 'f')
			. ($this->_superScript ? 't' : 'f')
			. ($this->_subScript ? 't' : 'f')
    		. $this->_underline
    		. ($this->_striketrough ? 't' : 'f')
    		. $this->_color->getHashCode()
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
