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


/** PHPExcel */
require_once 'PHPExcel.php';

/** PHPExcel_Cell */
require_once 'PHPExcel/Cell.php';

/** PHPExcel_Worksheet_RowDimension */
require_once 'PHPExcel/Worksheet/RowDimension.php';

/** PHPExcel_Worksheet_ColumnDimension */
require_once 'PHPExcel/Worksheet/ColumnDimension.php';

/** PHPExcel_Worksheet_PageSetup */
require_once 'PHPExcel/Worksheet/PageSetup.php';

/** PHPExcel_Worksheet_PageMargins */
require_once 'PHPExcel/Worksheet/PageMargins.php';

/** PHPExcel_Worksheet_HeaderFooter */
require_once 'PHPExcel/Worksheet/HeaderFooter.php';

/** PHPExcel_Worksheet_Drawing */
require_once 'PHPExcel/Worksheet/Drawing.php';

/** PHPExcel_Worksheet_Protection */
require_once 'PHPExcel/Worksheet/Protection.php';

/** PHPExcel_Style */
require_once 'PHPExcel/Style.php';

/** PHPExcel_IComparable */
require_once 'PHPExcel/IComparable.php';

/** PHPExcel_Shared_PasswordHasher */
require_once 'PHPExcel/Shared/PasswordHasher.php';


/**
 * PHPExcel_Worksheet
 *
 * @category   PHPExcel
 * @package    PHPExcel
 * @copyright  Copyright (c) 2006 - 2007 PHPExcel (http://www.codeplex.com/PHPExcel)
 */
class PHPExcel_Worksheet implements PHPExcel_IComparable
{
	/* Break types */
	const BREAK_NONE	= 0;
	const BREAK_ROW		= 1;
	const BREAK_COLUMN	= 2;
	
	/**
	 * Parent spreadsheet
	 *
	 * @var PHPExcel
	 */
	private $_parent;
	
	/**
	 * Collection of cells
	 *
	 * @var PHPExcel_Cell[]
	 */
	private $_cellCollection = array();
	
	/**
	 * Collection of row dimensions
	 *
	 * @var PHPExcel_Worksheet_RowDimension[]
	 */
	private $_rowDimensions = array();
	
	/**
	 * Collection of column dimensions
	 *
	 * @var PHPExcel_Worksheet_ColumnDimension[]
	 */
	private $_columnDimensions = array();
	
	/**
	 * Collection of drawings
	 *
	 * @var PHPExcel_Worksheet_Drawing[]
	 */
	private $_drawingCollection = null;
	
	/**
	 * Worksheet title
	 *
	 * @var string
	 */
	private $_title;
	
	/**
	 * Page setup
	 *
	 * @var PHPExcel_Worksheet_PageSetup
	 */
	private $_pageSetup;
	
	/**
	 * Page margins
	 *
	 * @var PHPExcel_Worksheet_PageMargins
	 */
	private $_pageMargins;
	
	/**
	 * Page header/footer
	 *
	 * @var PHPExcel_Worksheet_HeaderFooter
	 */
	private $_headerFooter;
	
	/**
	 * Protection
	 *
	 * @var PHPExcel_Worksheet_Protection
	 */
	private $_protection;
	
	/**
	 * Collection of styles
	 *
	 * @var PHPExcel_Style[]
	 */
	private $_styles = array();
	
	/**
	 * Is the current cell collection sorted already?
	 *
	 * @var boolean
	 */
	private $_cellCollectionIsSorted = false;
	
	/**
	 * Collection of breaks
	 *
	 * @var array
	 */
	private $_breaks = array();
	
	/**
	 * Collection of merged cell ranges
	 *
	 * @var array
	 */
	private $_mergeCells = array();
	
	/**
	 * Collection of protected cell ranges
	 *
	 * @var array
	 */
	private $_protectedCells = array();
	
	/**
	 * Autofilter Range
	 * 
	 * @var string
	 */
	private $_autoFilter = '';
	
	/**
	 * Create a new worksheet
	 *
	 * @param PHPExcel 		$pParent
	 * @param string 		$pTitle
	 */
	public function __construct($pParent = null, $pTitle = 'Worksheet')
	{
		// Set parent and title
		if (!is_null($pParent) && $pParent instanceof PHPExcel) {
			$this->_parent = $pParent;
			$this->setTitle($pTitle);
		} else {
			throw new Exception("Invalid PHPExcel object given.");
		}
		
		// Set page setup
		$this->_pageSetup 			= new PHPExcel_Worksheet_PageSetup();
		
		// Set page margins
		$this->_pageMargins 		= new PHPExcel_Worksheet_PageMargins();
		
		// Set page header/footer
		$this->_headerFooter 		= new PHPExcel_Worksheet_HeaderFooter();
		
    	// Create a default style and a default gray125 style
    	$this->_styles['default'] 	= new PHPExcel_Style();
    	$this->_styles['gray125'] 	= new PHPExcel_Style();
    	$this->_styles['gray125']->getFill()->setFillType(PHPExcel_Style_Fill::FILL_PATTERN_GRAY125);
    	
    	// Drawing collection
    	$this->_drawingCollection 	= new ArrayObject();
    	
    	// Protection
    	$this->_protection			= new PHPExcel_Worksheet_Protection();
	}
	
	/**
	 * Get collection of cells
	 *
	 * @return PHPExcel_Cell[]
	 */
	public function getCellCollection()
	{
        // Re-order cell collection?
        if (!$this->_cellCollectionIsSorted) {
        	usort($this->_cellCollection, array('PHPExcel_Cell', 'compareCells'));
        }

		return $this->_cellCollection;
	}
	
	/**
	 * Get collection of row dimensions
	 *
	 * @return PHPExcel_Worksheet_RowDimension[]
	 */
	public function getRowDimensions()
	{
		return $this->_rowDimensions;
	}
	
	/**
	 * Get collection of column dimensions
	 *
	 * @return PHPExcel_Worksheet_ColumnDimension[]
	 */
	public function getColumnDimensions()
	{
		return $this->_columnDimensions;
	}
	
	/**
	 * Get collection of drawings
	 *
	 * @return PHPExcel_Worksheet_Drawing[]
	 */
	public function getDrawingCollection()
	{
		return $this->_drawingCollection;
	}
	
    /**
     * Calculate worksheet dimension
     *
     * @return string  String containing the dimension of this worksheet
     */
    public function calculateWorksheetDimension()
    {        
        // Return
        return 'A1' . ':' .  $this->getHighestColumn() . $this->getHighestRow();
    }
    
    /**
     * Get parent
     *
     * @return PHPExcel
     */
    public function getParent() {
    	return $this->_parent;
    }
    
	/**
	 * Get title
	 *
	 * @return string
	 */
	public function getTitle()
	{
		return $this->_title;
	}
	
    /**
     * Set title
     *
     * @param string $pValue String containing the dimension of this worksheet
     */
    public function setTitle($pValue = 'Worksheet')
    {
    	// Loop trough all sheets in parent PHPExcel and verify unique names
    	$titleCount	= 0;
    	$aNames 	= $this->getParent()->getSheetNames();
 
		foreach ($aNames as $strName) {
			if ($strName == $pValue || substr($strName, 0, strrpos($strName, ' ')) == $pValue) {
				$titleCount++;
			}
		}

		// Eventually, add a number to the sheet name
		if ($titleCount > 0) {
			$this->setTitle($pValue . ' ' . $titleCount);
			return;
		}
				
		// Set title
        $this->_title = $pValue;
    }
    
    /**
     * Get page setup
     *
     * @return PHPExcel_Worksheet_PageSetup
     */
    public function getPageSetup()
    {
    	return $this->_pageSetup;
    }
    
    /**
     * Set page setup
     *
     * @param PHPExcel_Worksheet_PageSetup	$pValue
     */
    public function setPageSetup($pValue)
    {
    	if ($pValue instanceof PHPExcel_Worksheet_PageSetup) {
    		$this->_pageSetup = $pValue;
    	} else {
    		throw new Exception("Invalid PHPExcel_Worksheet_PageSetup object passed.");
    	}
    }
    
    /**
     * Get page margins
     *
     * @return PHPExcel_Worksheet_PageMargins
     */
    public function getPageMargins()
    {
    	return $this->_pageMargins;
    }
    
    /**
     * Set page margins
     *
     * @param PHPExcel_Worksheet_PageMargins	$pValue
     */
    public function setPageMargins($pValue)
    {
    	if ($pValue instanceof PHPExcel_Worksheet_PageMargins) {
    		$this->_pageMargins = $pValue;
    	} else {
    		throw new Exception("Invalid PHPExcel_Worksheet_PageMargins object passed.");
    	}
    }
    
    /**
     * Get page header/footer
     *
     * @return PHPExcel_Worksheet_HeaderFooter
     */
    public function getHeaderFooter()
    {
    	return $this->_headerFooter;
    }
    
    /**
     * Set page header/footer
     *
     * @param PHPExcel_Worksheet_HeaderFooter	$pValue
     */
    public function setHeaderFooter($pValue)
    {
    	if ($pValue instanceof PHPExcel_Worksheet_HeaderFooter) {
    		$this->_headerFooter = $pValue;
    	} else {
    		throw new Exception("Invalid PHPExcel_Worksheet_HeaderFooter object passed.");
    	}
    }
    
    /**
     * Get Protection
     *
     * @return PHPExcel_Worksheet_Protection
     */
    public function getProtection()
    {
    	return $this->_protection;
    }
    
    /**
     * Set Protection
     *
     * @param PHPExcel_Worksheet_Protection	$pValue
     */
    public function setProtection($pValue)
    {
    	if ($pValue instanceof PHPExcel_Worksheet_Protection) {
    		$this->_protection = $pValue;
    	} else {
    		throw new Exception("Invalid PHPExcel_Worksheet_Protection object passed.");
    	}
    }
    
    /**
     * Get highest worksheet column
     *
     * @return string Highest column name
     */
    public function getHighestColumn()
    {
        // Highest column
        $highestColumn = 'A';
        $highestColumnInteger = 1;
               
        // Loop trough cells
        foreach ($this->_cellCollection as $cell) {
        	if (PHPExcel_Cell::columnIndexFromString($highestColumn) < PHPExcel_Cell::columnIndexFromString($cell->getColumn())) {
        		$highestColumn = $cell->getColumn();
        	}
        }

        // Return
        return $highestColumn;
    }
    
    /**
     * Get highest worksheet row
     *
     * @return int Highest row number
     */
    public function getHighestRow()
    {       
        // Highest row
        $highestRow = 1;
        
        // Loop trough cells
        foreach ($this->_cellCollection as $cell) {
        	if ($cell->getRow() > $highestRow) {
        		$highestRow = $cell->getRow();
        	}
        }
        
        // Return
        return $highestRow;
    }
    
    /**
     * Set a cell value
     *
     * @param string $pCoordinate	Coordinate of the cell
     * @param string $pValue		Value of the cell
     */
    public function setCellValue($pCoordinate = 'A1', $pValue = '')
    {
    	$this->getCell($pCoordinate)->setValue($pValue);
    }
    
    /**
     * Set a cell value by using numeric cell coordinates
     *
     * @param string $pColumn		Numeric column coordinate of the cell
     * @param string $pRow			Numeric row coordinate of the cell
     * @param string $pValue		Value of the cell
     */
    public function setCellValueByColumnAndRow($pColumn = 0, $pRow = 0, $pValue = '')
    {
    	$this->setCellValue(PHPExcel_Cell::stringFromColumnIndex($pColumn) . $pRow, $pValue);
    }
    
    /**
     * Get cell at a specific coordinate
     *
     * @param 	string 			$pCoordinate	Coordinate of the cell
     * @return 	PHPExcel_Cell 	Cell that was found
     */
    public function getCell($pCoordinate = 'A1')
    {
    	// Coordinates
    	$aCoordinates = PHPExcel_Cell::coordinateFromString($pCoordinate);
    	
        // Cell exists?
        if (!isset($this->_cellCollection[ strtoupper($pCoordinate) ])) {
        	$this->_cellCollection[ strtoupper($pCoordinate) ] = new PHPExcel_Cell($aCoordinates[0], $aCoordinates[1]);
        	$this->_cellCollectionIsSorted = false;
        }
        return $this->_cellCollection[ strtoupper($pCoordinate) ];
    }
    
    /**
     * Get cell at a specific coordinate by using numeric cell coordinates
     *
     * @param 	string $pColumn		Numeric column coordinate of the cell
     * @param 	string $pRow		Numeric row coordinate of the cell
     * @return 	PHPExcel_Cell 		Cell that was found
     */
    public function getCellByColumnAndRow($pColumn = 0, $pRow = 0)
    {
    	return $this->getCell(PHPExcel_Cell::stringFromColumnIndex($pColumn) . $pRow);
    }
    
    /**
     * Get row dimension at a specific row
     *
     * @param int $pRow	Numeric index of the row
     * @return PHPExcel_Worksheet_RowDimension
     */
    public function getRowDimension($pRow = 0)
    {
    	// Found
    	$found = null;
    	
        // Loop trough rows
        foreach ($this->_rowDimensions as $row) {
        	if ($row->getRowIndex() == $pRow) {
        		$found = $row;
        		break;
        	}
        }
        
        // Found? If not, create a new one
        if (is_null($found)) {
        	$found = new PHPExcel_Worksheet_RowDimension($pRow);
        	
        	$this->_rowDimensions[] = $found;
        }
        
        // Return
        return $found;
    }
       
    /**
     * Get column dimension at a specific column
     *
     * @param string $pColumn	String index of the column
     * @return PHPExcel_Worksheet_ColumnDimension
     */
    public function getColumnDimension($pColumn = 'A')
    {
    	// Found
    	$found = null;
    	
        // Loop trough columns
        foreach ($this->_columnDimensions as $column) {
        	if ($column->getColumnIndex() == $pColumn) {
        		$found = $column;
        		break;
        	}
        }
        
        // Found? If not, create a new one
        if (is_null($found)) {
        	$found = new PHPExcel_Worksheet_ColumnDimension($pColumn);
        	
        	$this->_columnDimensions[] = $found;
        }
        
        // Return
        return $found;
    }
    
    /**
     * Get column dimension at a specific column by using numeric cell coordinates
     *
     * @param 	string $pColumn		Numeric column coordinate of the cell
     * @param 	string $pRow		Numeric row coordinate of the cell
     * @return 	PHPExcel_Worksheet_ColumnDimension
     */
    public function getColumnDimensionByColumn($pColumn = 0)
    {
        return $this->getColumnDimension(PHPExcel_Cell::stringFromColumnIndex($pColumn));
    }
    
    /**
     * Get styles
     *
     * @return PHPExcel_Style[]
     */
    public function getStyles()
    {
    	return $this->_styles;
    }
    
    /**
     * Get style for cell
     *
     * @param 	string 	$pCellCoordinate	Cell coordinate to get style for
     * @return 	PHPExcel_Style
     */
    public function getStyle($pCellCoordinate = 'A1')
    {
    	if ($pCellCoordinate != '') {
    		// Create a cell for this coordinate.
    		// Reason: When we have an empty cell that has style information,
    		// it should exist for our IWriter
    		$this->getCell($pCellCoordinate);
    		
    		// Check if we already have style information for this cell.
    		// If not, create a new style.
    		if (isset($this->_styles[$pCellCoordinate])) {
    			return $this->_styles[$pCellCoordinate];
    		} else {
    			$newStyle = new PHPExcel_Style();
    			$this->_styles[$pCellCoordinate] = $newStyle;
    			return $newStyle;
    		}
    	}
    	
    	return null;
    }
    
    /**
     * Get style for cell by using numeric cell coordinates
     *
     * @param 	string $pColumn		Numeric column coordinate of the cell
     * @param 	string $pRow		Numeric row coordinate of the cell
     * @return 	PHPExcel_Style
     */
    public function getStyleByColumnAndRow($pColumn = 0, $pRow = 0)
    {
    	return $this->getStyle(PHPExcel_Cell::stringFromColumnIndex($pColumn) . $pRow);
    }
    
    /**
     * Duplicate cell style to a range of cells
     *
     * Please note that this will overwrite existing cell styles for cells in range!
     *
     * @param 	PHPExcel_Style	$pCellStyle	Cell style to duplicate
     * @param 	string			$pRange		Range of cells (i.e. "A1:B10"), or just one cell (i.e. "A1")
     * @throws	Exception
     */
    public function duplicateStyle($pCellStyle = null, $pRange = '')
    {
    	if ($pCellStyle instanceof PHPExcel_Style) {
    		// Is it a cell range or a single cell?
    		$rangeA 	= '';
    		$rangeB 	= '';
    		if (strpos($pRange, ':') === false) {
    			$rangeA = $pRange;
    			$rangeB = $pRange;
    		} else {
    			list($rangeA, $rangeB) = explode(':', $pRange);
    		}
    		
    		// Calculate range outer borders
    		$rangeStart = PHPExcel_Cell::coordinateFromString($rangeA);
    		$rangeEnd 	= PHPExcel_Cell::coordinateFromString($rangeB);
    		
    		// Translate column into index
    		$rangeStart[0]	= PHPExcel_Cell::columnIndexFromString($rangeStart[0]) - 1;
    		$rangeEnd[0]	= PHPExcel_Cell::columnIndexFromString($rangeEnd[0]) - 1;
    		
    		// Make sure we can loop upwards on rows and columns
    		if ($rangeStart[0] > $rangeEnd[0] && $rangeStart[1] > $rangeEnd[1]) {
    			$tmp = $rangeStart;
    			$rangeStart = $rangeEnd;
    			$rangeEnd = $tmp;
    		}
    		   		
    		// Loop trough cells and apply styles
    		for ($col = $rangeStart[0]; $col <= $rangeEnd[0]; $col++) {
    			for ($row = $rangeStart[1]; $row <= $rangeEnd[1]; $row++) {
    				$this->_styles[ PHPExcel_Cell::stringFromColumnIndex($col) . $row ] = $pCellStyle->duplicate();
    			}
    		}
    	} else {
    		throw new Exception("Invalid PHPExcel_Style object passed.");
    	}
    }
    
    /**
     * Set break on a cell
     *
     * @param 	string			$pCell		Cell coordinate (e.g. A1)
     * @param 	int				$pBreak		Break type (type of PHPExcel_Worksheet::BREAK_*)
     * @throws	Exception
     */
    public function setBreak($pCell = 'A1', $pBreak = PHPExcel_Worksheet::BREAK_NONE)
    {
    	if ($pCell != '') {
    		$this->_breaks[strtoupper($pCell)] = $pBreak;
    	} else {
    		throw new Exception('No cell coordinate specified.');
    	}
    }
    
    /**
     * Set break on a cell by using numeric cell coordinates
     *
     * @param 	string 	$pColumn	Numeric column coordinate of the cell
     * @param 	string 	$pRow		Numeric row coordinate of the cell
     * @param 	int		$pBreak		Break type (type of PHPExcel_Worksheet::BREAK_*)
     * @throws	Exception
     */
    public function setBreakByColumnAndRow($pColumn = 0, $pRow = 0, $pBreak = PHPExcel_Worksheet::BREAK_NONE)
    {
    	$this->setBreak(PHPExcel_Cell::stringFromColumnIndex($pColumn) . $pRow, $pBreak);
    }
    
    /**
     * Get breaks
     *
     * @return array[]
     */
    public function getBreaks()
    {
    	return $this->_breaks;
    }
    
    /**
     * Set merge on a cell range
     *
     * @param 	string			$pRange		Cell range (e.g. A1:E1)
     * @throws	Exception
     */
    public function mergeCells($pRange = 'A1:A1')
    {
    	if (eregi(':', $pRange)) {
    		$this->_mergeCells[$pRange] = $pRange;
    	} else {
    		throw new Exception('Merge must be set on a range of cells.');
    	}
    }
    
    /**
     * Remove merge on a cell range
     *
     * @param 	string			$pRange		Cell range (e.g. A1:E1)
     * @throws	Exception
     */
    public function unmergeCells($pRange = 'A1:A1')
    {
    	if (eregi(':', $pRange)) {
    		if (isset($this->_mergeCells[$pRange])) {
    			unset($this->_mergeCells[$pRange]);
    		} else {
    			throw new Exception('Cell range ' . $pRange . ' not known as merged.');
    		}
    	} else {
    		throw new Exception('Merge can only be removed from a range of cells.');
    	}
    }
    
    /**
     * Get merge cells
     *
     * @return array[]
     */
    public function getMergeCells()
    {
    	return $this->_mergeCells;
    }
    
    /**
     * Set protection on a cell range
     *
     * @param 	string			$pRange				Cell (e.g. A1) or cell range (e.g. A1:E1)
     * @param 	string			$pPassword			Password to unlock the protection
     * @param 	boolean 		$pAlreadyHashed 	If the password has already been hashed, set this to true
     * @throws	Exception
     */
    public function protectCells($pRange = 'A1', $pPassword = '', $pAlreadyHashed = false)
    {
    	if (!$pAlreadyHashed) {
    		$pPassword = PHPExcel_Shared_PasswordHasher::hashPassword($pPassword);
    	}
    	$this->_protectedCells[$pRange] = $pPassword;
    }
    
    /**
     * Remove protection on a cell range
     *
     * @param 	string			$pRange		Cell (e.g. A1) or cell range (e.g. A1:E1)
     * @throws	Exception
     */
    public function unprotectCells($pRange = 'A11')
    {
    	if (isset($this->_protectedCells[$pRange])) {
    		unset($this->_protectedCells[$pRange]);
    	} else {
    		throw new Exception('Cell range ' . $pRange . ' not known as protected.');
    	}
    }
    
    /**
     * Get protected cells
     *
     * @return array[]
     */
    public function getProtectedCells()
    {
    	return $this->_protectedCells;
    }
    
    /**
     * Get Autofilter Range
     *
     * @return string
     */
    public function getAutoFilter()
    {
    	return $this->_autoFilter;
    }
    
    /**
     * Set Autofilter Range
     *
     * @param 	string		$pRange		Cell range (i.e. A1:E10)
     * @throws 	Exception
     */
    public function setAutoFilter($pRange = '')
    {
    	if (eregi(':', $pRange)) {
    		$this->_autoFilter = $pRange;
    	} else {
    		throw new Exception('Autofilter must be set on a range of cells.');
    	}
    }
    
	/**
	 * Get hash code
	 *
	 * @return string	Hash code
	 */	
	public function getHashCode() {
    	return md5(
    		  $this->_title
    		. $this->_autoFilter
    		. $this->_protection->isProtectionEnabled()
    		. $this->calculateWorksheetDimension()
    		. __CLASS__
    	);
    }
        
    /**
     * Duplicate object
     *
     * Duplicates the current object, also duplicating referenced objects (deep cloning).
     * Standard PHP clone does not copy referenced objects!
     *
     * @return PHPExcel_Worksheet
     */
	public function duplicate() {
		return unserialize(serialize($this));
	}
}
