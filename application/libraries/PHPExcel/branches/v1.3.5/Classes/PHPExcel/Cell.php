<?php
/**
 * PHPExcel
 *
 * Copyright (c) 2006 - 2007 PHPExcel, Maarten Balliauw
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
 * @package    PHPExcel
 * @copyright  Copyright (c) 2006 - 2007 PHPExcel (http://www.codeplex.com/PHPExcel)
 * @license    http://www.gnu.org/licenses/lgpl.txt	LGPL
 * @version    ##VERSION##, ##DATE##
 */


/** PHPExcel_Cell_DataType */
require_once 'PHPExcel/Cell/DataType.php';

/** PHPExcel_Cell_DataValidation */
require_once 'PHPExcel/Cell/DataValidation.php';

/** PHPExcel_Worksheet */
require_once 'PHPExcel/Worksheet.php';

/** PHPExcel_Calculation */
require_once 'PHPExcel/Calculation.php';


/**
 * PHPExcel_Cell
 *
 * @category   PHPExcel
 * @package    PHPExcel
 * @copyright  Copyright (c) 2006 - 2007 PHPExcel (http://www.codeplex.com/PHPExcel)
 */
class PHPExcel_Cell
{
	/**
	 * Column of the cell
	 *
	 * @var string
	 */
	private $_column;
	
	/**
	 * Row of the cell
	 *
	 * @var int
	 */
	private $_row;
	
	/**
	 * Value of the cell
	 *
	 * @var mixed
	 */
	private $_value;
	
	/**
	 * Type of the cell data
	 *
	 * @var string
	 */
	private $_dataType;
	
	/**
	 * Data validation
	 *
	 * @var PHPExcel_Cell_DataValidation
	 */
	private $_dataValidation;
	
	/**
	 * Parent worksheet
	 *
	 * @var PHPExcel_Worksheet
	 */
	private $_parent;
	   
    /**
     * Create a new Cell
     *
     * @param 	string 				$pColumn
     * @param 	int 				$pRow
     * @param 	mixed 				$pValue
     * @param 	string 				$pDataType
     * @param 	PHPExcel_Worksheet	$pSheet
     * @throws	Exception
     */
    public function __construct($pColumn = 'A', $pRow = 1, $pValue = null, $pDataType = null, $pSheet = null)
    {
    	// Initialise cell coordinate
    	$this->_column = strtoupper($pColumn);
    	$this->_row = $pRow;
    	
    	// Initialise cell value
    	$this->_value = $pValue;
    	
    	// Set datatype?
    	if (!is_null($pDataType)) {
    		$this->_dataType = $pDataType;
    	}
 	
    	// Set worksheet?
    	if ($pSheet instanceof PHPExcel_Worksheet) {
    		$this->_parent = $pSheet;
    	} else {
    		throw new Exception("Invalid parent worksheet passed.");
    	}
    }
    
    /**
     * Get cell coordinate column
     *
     * @return string
     */
    public function getColumn()
    {
    	return strtoupper($this->_column);
    }
    
    /**
     * Get cell coordinate row
     *
     * @return int
     */
    public function getRow()
    {
    	return $this->_row;
    }
    
    /**
     * Get cell coordinate
     *
     * @return string
     */
    public function getCoordinate()
    {
    	return $this->getColumn() . $this->getRow();
    }
    
    /**
     * Get cell value
     *
     * @return mixed
     */
    public function getValue()
    {
    	return $this->_value;
    }
    
    /**
     * Set cell value
     *
     * This clears the cell formula.
     *
     * @param mixed $pValue
     * @param bool 	$pUpdateDataType
     */
    public function setValue($pValue = null, $pUpdateDataType = true)
    {
    	$this->_value = $pValue;
    	
    	if ($pUpdateDataType) {
    		$this->_dataType = PHPExcel_Cell_DataType::dataTypeForValue($pValue);
    	}
    }
    
    /**
     * Get caluclated cell value
     *
     * @return mixed
     */
    public function getCalculatedValue()
    {
    	if ($this->_dataType != PHPExcel_Cell_DataType::TYPE_FORMULA) {
    		return $this->_value;
    	} else {
    		return PHPExcel_Calculation::getInstance()->calculate($this);
    	}
    }
    
    /**
     * Get cell data type
     *
     * @return string
     */
    public function getDataType()
    {
    	return $this->_dataType;
    }
    
    /**
     * Set cell data type
     *
     * @param string $pDataType
     */
    public function setDataType($pDataType = PHPExcel_Cell_DataType::TYPE_STRING)
    {
    	$this->_dataType = $pDataType;
    }
    
    /**
     * Has Data validation?
     *
     * @return boolean
     */
    public function hasDataValidation()
    {
    	return !is_null($this->_dataValidation);
    }
    
    /**
     * Get Data validation
     *
     * @return PHPExcel_Cell_DataValidation
     */
    public function getDataValidation()
    {
    	if (is_null($this->_dataValidation)) {
    		$this->_dataValidation = new PHPExcel_Cell_DataValidation($this);
    	}
    	
    	return $this->_dataValidation;
    }
    
    /**
     * Set Data validation
     *
     * @param 	PHPExcel_Cell_DataValidation	$pDataValidation
     * @throws 	Exception
     */
    public function setDataValidation($pDataValidation = null)
    {
    	if ($pDataValidation instanceof PHPExcel_Cell_DataValidation) {
    		$this->_dataValidation = $pDataValidation;
    		$this->_dataValidation->setParent($this);
    	} else {
    		throw new Exception("Invalid PHPExcel_Cell_DataValidation object passed.");
    	}
    }
    
    /**
     * Get parent
     *
     * @return PHPExcel_Worksheet
     */
    public function getParent() {
    	return $this->_parent;
    }
    
    /**
     * Coordinate from string
     *
     * @param 	string 	$pCoordinateString
     * @return 	array 	Array containing column and row (indexes 0 and 1)
     * @throws	Exception
     */
    public static function coordinateFromString($pCoordinateString = 'A1')
    {
    	if (eregi(':', $pCoordinateString)) {
    		throw new Exception('Cell coordinate string can not be a range of cells.');
    	} else if (eregi('\$', $pCoordinateString)) {
    		throw new Exception('Cell coordinate string must not be absolute.');
    	} else if ($pCoordinateString == '') {
    		throw new Exception('Cell coordinate can not be zero-length string.');
    	} else {
	    	// Column
	    	$column = '';
	    	
	    	// Row
	    	$row = '';
	    	
	    	// Calculate column
	    	for ($i = 0; $i < strlen($pCoordinateString); $i++) {
		    	if (!is_numeric(substr($pCoordinateString, $i, 1))) {
		    		$column .= strtoupper(substr($pCoordinateString, $i, 1));
		    	} else {
		    		$row = substr($pCoordinateString, $i);
		    		break;
		    	}
	    	}
	    	
	    	// Return array
	    	return array($column, $row);
    	}
    }
    
    /**
     * Make string coordinate absolute
     *
     * @param 	string 	$pCoordinateString
     * @return 	string	Absolute coordinate
     * @throws	Exception
     */
    public static function absoluteCoordinate($pCoordinateString = 'A1')
    {
    	if (!eregi(':', $pCoordinateString)) {
	    	// Return value
	    	$returnValue = '';
	    	
	    	// Create absolute coordinate
	    	list($column, $row) = PHPExcel_Cell::coordinateFromString($pCoordinateString);
	    	$returnValue = '$' . $column . '$' . $row;
	    	
	    	// Return
	    	return $returnValue;
    	} else {
    		throw new Exception("Coordinate string should not be a cell range.");
    	}
    }
    
    /**
     * Split range into coordinate strings
     *
     * @param 	string 	$pRange
     * @return 	array	Array containg two coordinate strings
     */
    public static function splitRange($pRange = 'A1:A1')
    {
    	return explode(':', $pRange);
    }
    
    /**
     * Columnindex from string
     *
     * @param 	string $pString
     * @return 	int Column index (base 1 !!!)
     * @throws 	Exception
     */
    public static function columnIndexFromString($pString = 'A')
    {
    	// Convert to uppercase
    	$pString = strtoupper($pString);
    	
    	// Convert column to integer
    	if (strlen($pString) == 1) {
    		$result = 0;
    		$result += (ord(substr($pString, 0, 1)) - 65);
    		$result += 1;
    		
    		return $result;
    	} else if (strlen($pString) == 2) {
    		$result = 0;
    		$result += ( (1 + (ord(substr($pString, 0, 1)) - 65) )    * 26);
    		$result += (ord(substr($pString, 1, 2)) - 65);
    		$result += 1;
    		
    		return $result;
    	} else {
    		throw new Exception("Column string index can not be longer than 2 characters.");
    	}
    }
    
    /**
     * String from columnindex 
     *
     * @param int $pColumnIndex Column index (base 0 !!!)
     * @return string
     */
    public static function stringFromColumnIndex($pColumnIndex = 0)
    {
        // Convert column to string
        $returnValue = '';
        // Determine column string
        if ($pColumnIndex < 26) {
        	$returnValue = chr(65 + $pColumnIndex);
        } else {
        	
        	$iRemainder = (int)($pColumnIndex / 26) -1;
        	$returnValue = PHPExcel_Cell::stringFromColumnIndex( $iRemainder  ).chr(65 + $pColumnIndex%26) ;
        }
        // Return
        return $returnValue;
    }
    
	/**
	 * Compare 2 cells
	 *
	 * @param 	PHPExcel_Cell	$a	Cell a
	 * @param 	PHPExcel_Cell	$a	Cell b
	 * @return 	int		Result of comparison (always -1 or 1, never zero!)
	 */
	public static function compareCells($a, $b)
	{
		if ($a->getRow() < $b->getRow()) {
			return -1;
		} elseif ($a->getRow() > $b->getRow()) {
			return 1;
		} elseif (PHPExcel_Cell::columnIndexFromString($a->getColumn()) < PHPExcel_Cell::columnIndexFromString($b->getColumn())) {
			return -1;
		} else {
			return 1;
		}
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
