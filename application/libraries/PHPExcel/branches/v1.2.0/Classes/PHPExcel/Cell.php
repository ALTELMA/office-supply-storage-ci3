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
     * Create a new Cell
     *
     * @param string $pColumn
     * @param int $pRow
     * @param mixed $pValue
     * @param string $pDataType
     */
    public function __construct($pColumn = 'A', $pRow = 1, $pValue = null, $pDataType = null)
    {
    	// Initialise cell coordinate
    	$this->_column = strtoupper($pColumn);
    	$this->_row = $pRow;
    	
    	// Initialise cell value
    	$this->_value = $pValue;
    	
    	if (!is_null($pDataType)) {
    		$this->_dataType = $pDataType;
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
     * @param bool $pUpdateDataType
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
    	return $this->_value;
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
     * Coordinate from string
     *
     * @param 	string 	$pCoordinateString
     * @return 	array 	Array containing column and row (indexes 0 and 1)
     * @throws	Exception
     */
    public static function coordinateFromString($pCoordinateString = 'A1')
    {
    	if (!eregi(':', $pCoordinateString)) {
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
    	} else {
    		throw new Exception("Coordinate string should not be a cell range.");
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
     * @param int $pColumnIndex Column index
     * @return string
     */
    public static function stringFromColumnIndex($pColumnIndex = 0)
    {
        // Convert column to string
        $returnValue = '';
        
        // Determine column string
        if ($pColumnIndex <= 25) {
        	$returnValue = chr(65 + $pColumnIndex);
        } else {
        	$iRemainder = number_format(($pColumnIndex / 26), 0) - 1;
        	$returnValue = chr(65 + $iRemainder) . PHPExcel_Cell::stringFromColumnIndex( ($pColumnIndex % 26) );
        }
        
        // Return
        return $returnValue;
    }
    
	/**
	 * Compare 2 cells
	 *
	 * @param 	PHPExcel_Cell	$a	Cell a
	 * @param 	PHPExcel_Cell	$a	Cell b
	 * @return 	int	Result of comparison (always -1 or 1, never zero!)
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
     * Duplicate object
     *
     * Duplicates the current object, also duplicating referenced objects (deep cloning).
     * Standard PHP clone does not copy referenced objects!
     *
     * @return PHPExcel_Cell
     */
	public function duplicate() {
		return unserialize(serialize($this));
	}
}
