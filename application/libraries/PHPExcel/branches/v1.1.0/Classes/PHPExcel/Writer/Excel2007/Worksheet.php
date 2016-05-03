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
 * @package    PHPExcel_Writer_Excel2007
 * @copyright  Copyright (c) 2006 - 2007 PHPExcel (http://www.codeplex.com/PHPExcel)
 * @license    http://www.gnu.org/licenses/gpl.txt	GPL
 */


/** PHPExcel_Writer_Excel2007 */
require_once 'PHPExcel/Writer/Excel2007.php';

/** PHPExcel_Writer_Excel2007_WriterPart */
require_once 'PHPExcel/Writer/Excel2007/WriterPart.php';


/**
 * PHPExcel_Writer_Excel2007_Worksheet
 *
 * @category   PHPExcel
 * @package    PHPExcel_Writer_Excel2007
 * @copyright  Copyright (c) 2006 - 2007 PHPExcel (http://www.codeplex.com/PHPExcel)
 */
class PHPExcel_Writer_Excel2007_Worksheet extends PHPExcel_Writer_Excel2007_WriterPart
{
	/**
	 * Write worksheet to XML format
	 *
	 * @param 	PHPExcel_Worksheet 	$pSheet
	 * @param 	string[] 				$pStringTable
	 * @return 	string 					XML Output
	 * @throws 	Exception
	 */
	public function writeWorksheet($pSheet = null, $pStringTable = null)
	{
		if (!is_null($pSheet)) {
			// Create XML writer
			$objWriter = new xmlWriter();
			$objWriter->openMemory();
			
			// XML header
			$objWriter->startDocument('1.0','UTF-8','yes');
			
			// Worksheet
			$objWriter->startElement('worksheet');
			$objWriter->writeAttribute('xml:space', 'preserve');
			$objWriter->writeAttribute('xmlns', 'http://schemas.openxmlformats.org/spreadsheetml/2006/main');
			$objWriter->writeAttribute('xmlns:r', 'http://schemas.openxmlformats.org/officeDocument/2006/relationships');
			
				// sheetPr
				$this->_writeSheetPr($objWriter, $pSheet);
			
				// Dimension
				$this->_writeDimension($objWriter, $pSheet);
				
				// sheetViews
				$this->_writeSheetViews($objWriter);
			
				// sheetFormatPr
				$this->_writeSheetFormatPr($objWriter);
				
				// cols
				$this->_writeCols($objWriter, $pSheet);
						
				// sheetData
				$this->_writeSheetData($objWriter, $pSheet, $pStringTable);
				
				// sheetProtection
				$this->_writeSheetProtection($objWriter);
				
				// conditionalFormatting
				$this->_writeConditionalFormatting($objWriter, $pSheet);
				
				// Print options
				$this->_writePrintOptions($objWriter);
								
				// Page margins
				$this->_writePageMargins($objWriter, $pSheet);

				// Page setup
				$this->_writePageSetup($objWriter, $pSheet);
				
				// Header / footer
				$this->_writeHeaderFooter($objWriter, $pSheet);
				
				// Breaks
				$this->_writeBreaks($objWriter, $pSheet);
				
				// Drawings
				$this->_writeDrawings($objWriter, $pSheet);
				
			$objWriter->endElement();

			// Return
			return $objWriter->outputMemory(true);
		} else {
			throw new Exception("Invalid PHPExcel_Worksheet object passed.");
		}
	}
	
	/**
	 * Write SheetPr
	 *
	 * @param 	xmlWriter 				$objWriter 		XML Writer
	 * @param 	PHPExcel_Worksheet	$pSheet			Worksheet
	 * @throws 	Exception
	 */
	private function _writeSheetPr($objWriter = null, $pSheet = null)
	{
		if ($objWriter instanceof xmlWriter && $pSheet instanceof PHPExcel_Worksheet) {
			// sheetPr
			$objWriter->startElement('sheetPr');
			$objWriter->writeAttribute('codeName', $pSheet->getTitle());
			$objWriter->endElement();
		} else {
			throw new Exception("Invalid parameters passed.");
		}
	}
	
	/**
	 * Write Dimension
	 *
	 * @param 	xmlWriter 				$objWriter 		XML Writer
	 * @param 	PHPExcel_Worksheet	$pSheet			Worksheet
	 * @throws 	Exception
	 */
	private function _writeDimension($objWriter = null, $pSheet = null)
	{
		if ($objWriter instanceof xmlWriter && $pSheet instanceof PHPExcel_Worksheet) {
			// dimension
			$objWriter->startElement('dimension');
			$objWriter->writeAttribute('ref', $pSheet->calculateWorksheetDimension());
			$objWriter->endElement();
		} else {
			throw new Exception("Invalid parameters passed.");
		}
	}
	
	/**
	 * Write SheetViews
	 *
	 * @param 	xmlWriter $objWriter 		XML Writer
	 * @throws 	Exception
	 */
	private function _writeSheetViews($objWriter = null)
	{
		if ($objWriter instanceof xmlWriter) {
			// sheetViews
			$objWriter->startElement('sheetViews');
				
				// sheetView
				$objWriter->startElement('sheetView');
				$objWriter->writeAttribute('tabSelected', 		'1');
				$objWriter->writeAttribute('workbookViewId',	'0');
						
					// Selection
					$objWriter->writeElement('selection', null);
						
				$objWriter->endElement();
				
			$objWriter->endElement();
		} else {
			throw new Exception("Invalid parameters passed.");
		}
	}
	
	/**
	 * Write SheetFormatPr
	 *
	 * @param 	xmlWriter $objWriter 		XML Writer
	 * @throws 	Exception
	 */
	private function _writeSheetFormatPr($objWriter = null)
	{
		if ($objWriter instanceof xmlWriter) {
			// sheetFormatPr
			$objWriter->startElement('sheetFormatPr');
			$objWriter->writeAttribute('defaultRowHeight', '12.75');
			$objWriter->endElement();
		} else {
			throw new Exception("Invalid parameters passed.");
		}
	}
	
	/**
	 * Write Cols
	 *
	 * @param 	xmlWriter 				$objWriter 		XML Writer
	 * @param 	PHPExcel_Worksheet	$pSheet			Worksheet
	 * @throws 	Exception
	 */
	private function _writeCols($objWriter = null, $pSheet = null)
	{
		if ($objWriter instanceof xmlWriter && $pSheet instanceof PHPExcel_Worksheet) {
			// cols
			$objWriter->startElement('cols');
					
				// Check if there is at least one column dimension specified. If not, create one.
				if (count($pSheet->getColumnDimensions()) == 0) {
					$pSheet->getColumnDimension('A')->setWidth(10);
				}
					
				// Loop trough column dimensions
				foreach ($pSheet->getColumnDimensions() as $colDimension) {
					// col
					$objWriter->startElement('col');
					$objWriter->writeAttribute('min', 	PHPExcel_Cell::columnIndexFromString($colDimension->getColumnIndex()));
					$objWriter->writeAttribute('max', 	PHPExcel_Cell::columnIndexFromString($colDimension->getColumnIndex()));
						
					if ($colDimension->getWidth() < 0) {
						$objWriter->writeAttribute('bestFit', 		'1');
						$objWriter->writeAttribute('width', 		'10');
					} else {
						$objWriter->writeAttribute('width', 		$colDimension->getWidth());
						$objWriter->writeAttribute('customWidth',	'1');
					}
					$objWriter->endElement();
				}
				
			$objWriter->endElement();
		} else {
			throw new Exception("Invalid parameters passed.");
		}
	}

	/**
	 * Write SheetProtection
	 *
	 * @param 	xmlWriter $objWriter 		XML Writer
	 * @throws 	Exception
	 */
	private function _writeSheetProtection($objWriter = null)
	{
		if ($objWriter instanceof xmlWriter) {
			// sheetProtection
			$objWriter->startElement('sheetProtection');
			$objWriter->writeAttribute('objects', 	'0');
			$objWriter->writeAttribute('scenarios', '0');
			$objWriter->endElement();
		} else {
			throw new Exception("Invalid parameters passed.");
		}
	}

	/**
	 * Write ConditionalFormatting
	 *
	 * @param 	xmlWriter $objWriter 		XML Writer
	 * @param 	PHPExcel_Worksheet	$pSheet			Worksheet
	 * @throws 	Exception
	 */
	private function _writeConditionalFormatting($objWriter = null, $pSheet = null)
	{
		if ($objWriter instanceof xmlWriter && $pSheet instanceof PHPExcel_Worksheet) {
			// Conditional id
			$id = 1;
			
			// Loop trough styles in the current worksheet
			foreach ($pSheet->getStyles() as $cellCoodrinate => $style) {
				if (count($style->getConditionalStyles()) > 0) {
					foreach ($style->getConditionalStyles() as $conditional) {
						
						if ($conditional->getConditionType() != PHPExcel_Style_Conditional::CONDITION_NONE) {
							// conditionalFormatting
							$objWriter->startElement('conditionalFormatting');
							$objWriter->writeAttribute('sqref', 	$cellCoodrinate);
							
								// cfRule
								$objWriter->startElement('cfRule');
								$objWriter->writeAttribute('type', 		$conditional->getConditionType());
								$objWriter->writeAttribute('dxfId', 	$this->getParentWriter()->getStylesConditionalHashTable()->getIndexForHashCode( $conditional->getHashCode() ));
								$objWriter->writeAttribute('priority', 	$id++);
								
								if ($conditional->getConditionType() == PHPExcel_Style_Conditional::CONDITION_CELLIS
									&& $conditional->getOperatorType() != PHPExcel_Style_Conditional::OPERATOR_NONE) {
									$objWriter->writeAttribute('operator', 	$conditional->getOperatorType());
									
									// Formula
									$objWriter->writeElement('formula',	$conditional->getCondition());		
								}		
								
								$objWriter->endElement();
							
							$objWriter->endElement();
						}

					}
				}
			}
		} else {
			throw new Exception("Invalid parameters passed.");
		}
	}
		
	/**
	 * Write PrintOptions
	 *
	 * @param 	xmlWriter $objWriter 		XML Writer
	 * @throws 	Exception
	 */
	private function _writePrintOptions($objWriter = null)
	{
		if ($objWriter instanceof xmlWriter) {
			// printOptions
			$objWriter->writeElement('printOptions', null);
		} else {
			throw new Exception("Invalid parameters passed.");
		}
	}
	
	/**
	 * Write PageMargins
	 *
	 * @param 	xmlWriter 				$objWriter 		XML Writer
	 * @param 	PHPExcel_Worksheet	$pSheet			Worksheet
	 * @throws 	Exception
	 */
	private function _writePageMargins($objWriter = null, $pSheet = null)
	{
		if ($objWriter instanceof xmlWriter && $pSheet instanceof PHPExcel_Worksheet) {
			// pageMargins
			$objWriter->startElement('pageMargins');
			$objWriter->writeAttribute('left', 		$pSheet->getPageMargins()->getLeft());
			$objWriter->writeAttribute('right', 	$pSheet->getPageMargins()->getRight());
			$objWriter->writeAttribute('top', 		$pSheet->getPageMargins()->getTop());
			$objWriter->writeAttribute('bottom', 	$pSheet->getPageMargins()->getBottom());
			$objWriter->writeAttribute('header', 	$pSheet->getPageMargins()->getHeader());
			$objWriter->writeAttribute('footer', 	$pSheet->getPageMargins()->getFooter());
			$objWriter->endElement();	
		} else {
			throw new Exception("Invalid parameters passed.");
		}
	}
	
	/**
	 * Write PageSetup
	 *
	 * @param 	xmlWriter 				$objWriter 		XML Writer
	 * @param 	PHPExcel_Worksheet	$pSheet			Worksheet
	 * @throws 	Exception
	 */
	private function _writePageSetup($objWriter = null, $pSheet = null)
	{
		if ($objWriter instanceof xmlWriter && $pSheet instanceof PHPExcel_Worksheet) {
			// pageSetup
			$objWriter->startElement('pageSetup');
			$objWriter->writeAttribute('paperSize',		$pSheet->getPageSetup()->getPaperSize());
			$objWriter->writeAttribute('orientation', 	$pSheet->getPageSetup()->getOrientation());
			$objWriter->endElement();
		} else {
			throw new Exception("Invalid parameters passed.");
		}
	}
	
	/**
	 * Write Header / Footer
	 *
	 * @param 	xmlWriter 				$objWriter 		XML Writer
	 * @param 	PHPExcel_Worksheet	$pSheet			Worksheet
	 * @throws 	Exception
	 */
	private function _writeHeaderFooter($objWriter = null, $pSheet = null)
	{
		if ($objWriter instanceof xmlWriter && $pSheet instanceof PHPExcel_Worksheet) {
			// headerFooter
			$objWriter->startElement('headerFooter');
			$objWriter->writeAttribute('differentOddEven', 	($pSheet->getHeaderFooter()->getDifferentOddEven() ? 'true' : 'false'));
			$objWriter->writeAttribute('differentFirst', 	($pSheet->getHeaderFooter()->getDifferentFirst() ? 'true' : 'false'));
			$objWriter->writeAttribute('scaleWithDoc', 		($pSheet->getHeaderFooter()->getScaleWithDocument() ? 'true' : 'false'));
			$objWriter->writeAttribute('alignWithMargins', 	($pSheet->getHeaderFooter()->getAlignWithMargins() ? 'true' : 'false'));
				
				$objWriter->writeElement('oddHeader', 		$pSheet->getHeaderFooter()->getOddHeader());
				$objWriter->writeElement('oddFooter', 		$pSheet->getHeaderFooter()->getOddFooter());
				$objWriter->writeElement('evenHeader', 		$pSheet->getHeaderFooter()->getEvenHeader());
				$objWriter->writeElement('evenFooter', 		$pSheet->getHeaderFooter()->getEvenFooter());
				$objWriter->writeElement('firstHeader', 	$pSheet->getHeaderFooter()->getFirstHeader());
				$objWriter->writeElement('firstFooter', 	$pSheet->getHeaderFooter()->getFirstFooter());
			$objWriter->endElement();
		} else {
			throw new Exception("Invalid parameters passed.");
		}
	}
	
	/**
	 * Write Breaks
	 *
	 * @param 	xmlWriter 				$objWriter 		XML Writer
	 * @param 	PHPExcel_Worksheet		$pSheet			Worksheet
	 * @throws 	Exception
	 */
	private function _writeBreaks($objWriter = null, $pSheet = null)
	{
		if ($objWriter instanceof xmlWriter && $pSheet instanceof PHPExcel_Worksheet) {
			// Get row and column breaks
			$aRowBreaks = array();
			$aColumnBreaks = array();
			foreach ($pSheet->getBreaks() as $cell => $breakType) {
				if ($breakType == PHPExcel_Worksheet::BREAK_ROW) {
					array_push($aRowBreaks, $cell);
				} else if ($breakType == PHPExcel_Worksheet::BREAK_COLUMN) {
					array_push($aColumnBreaks, $cell);
				}
			}
			
			// rowBreaks
			if (count($aRowBreaks) > 0) {
				$objWriter->startElement('rowBreaks');
				$objWriter->writeAttribute('count', 			count($aRowBreaks));
				$objWriter->writeAttribute('manualBreakCount', 	count($aRowBreaks));
				
					foreach ($aRowBreaks as $cell) {
						$coords = PHPExcel_Cell::coordinateFromString($cell);
						
						$objWriter->startElement('brk');
						$objWriter->writeAttribute('id', 	$coords[1]);
						$objWriter->writeAttribute('man', 	'1');
						$objWriter->endElement();
					}
					
				$objWriter->endElement();
			}
			
			// Second, write column breaks
			if (count($aColumnBreaks) > 0) {
				$objWriter->startElement('colBreaks');
				$objWriter->writeAttribute('count', 			count($aColumnBreaks));
				$objWriter->writeAttribute('manualBreakCount', 	count($aColumnBreaks));
				
					foreach ($aColumnBreaks as $cell) {
						$coords = PHPExcel_Cell::coordinateFromString($cell);
						
						$objWriter->startElement('brk');
						$objWriter->writeAttribute('id', 	PHPExcel_Cell::columnIndexFromString($coords[0]) - 1);
						$objWriter->writeAttribute('man', 	'1');
						$objWriter->endElement();
					}
					
				$objWriter->endElement();
			}
		} else {
			throw new Exception("Invalid parameters passed.");
		}
	}
	
	/**
	 * Write SheetData
	 *
	 * @param 	xmlWriter 				$objWriter 		XML Writer
	 * @param 	PHPExcel_Worksheet	$pSheet			Worksheet
	 * @param 	string[]				$pStringTable	String table
	 * @throws 	Exception
	 */
	private function _writeSheetData($objWriter = null, $pSheet = null, $pStringTable = null)
	{
		if ($objWriter instanceof xmlWriter && $pSheet instanceof PHPExcel_Worksheet && is_array($pStringTable)) {
			// Flipped stringtable, for faster index searching
			$aFlippedStringTable = array_flip($pStringTable);
			
			// sheetData
			$objWriter->startElement('sheetData');

				// Get column count
				$colCount = PHPExcel_Cell::columnIndexFromString($pSheet->getHighestColumn());
			
				// Loop trough cells
				$currentRow = -1;
				$cellCollection = $pSheet->getCellCollection();
				foreach ($cellCollection as $cell) {					
					if ($currentRow != $cell->getRow()) {
						// End previous row?
						if ($currentRow != -1) {
							$objWriter->endElement();
						}

						// Set current row
						$currentRow = $cell->getRow();

						// Get row dimension
						$rowDimension = $pSheet->getRowDimension($currentRow);
			
						// Start a new row
						$objWriter->startElement('row');
						$objWriter->writeAttribute('r', 	$currentRow);
						$objWriter->writeAttribute('spans',	'1:' . $colCount);
									
						// Row dimensions
						if ($rowDimension->getRowHeight() >= 0) {
							$objWriter->writeAttribute('customHeight', 	'1');
							$objWriter->writeAttribute('ht', 			$rowDimension->getRowHeight());
						}
					}
					
					// Write cell
					$this->_writeCell($objWriter, $pSheet, $cell, $pStringTable, $aFlippedStringTable);
				}

				// End last row?
				if ($currentRow != -1) {
					$objWriter->endElement();
				}
					
			$objWriter->endElement();
		} else {
			throw new Exception("Invalid parameters passed.");
		}
	}
	
	/**
	 * Write Cell
	 *
	 * @param 	xmlWriter 				$objWriter 				XML Writer
	 * @param 	PHPExcel_Worksheet	$pSheet					Worksheet
	 * @param 	PHPExcel_Cell		$pCell					Cell
	 * @param 	string[]				$pStringTable			String table
	 * @param 	string[]				$pFlippedStringTable	String table (flipped), for faster index searching
	 * @throws 	Exception
	 */
	private function _writeCell($objWriter = null, $pSheet = null, $pCell = null, $pStringTable = null, $pFlippedStringTable = null)
	{
		if ($objWriter instanceof xmlWriter && $pSheet instanceof PHPExcel_Worksheet && $pCell instanceof PHPExcel_Cell && is_array($pStringTable) && is_array($pFlippedStringTable)) {
			// Cell
			$objWriter->startElement('c');
			$objWriter->writeAttribute('r', $pCell->getCoordinate());
			
			// Sheet styles
			$aStyles = $pSheet->getStyles();
			if (isset($aStyles[$pCell->getCoordinate()])) {
				// Fix for work item 836 - http://www.codeplex.com/PHPExcel/WorkItem/View.aspx?WorkItemId=836
				// Seems the style can be empty after files pass Excel2007_Reader...
				if ($this->getParentWriter()->getStylesHashTable()->getIndexForHashCode( $aStyles[$pCell->getCoordinate()]->getHashCode() ) !== false) {
					$objWriter->writeAttribute('s', $this->getParentWriter()->getStylesHashTable()->getIndexForHashCode( $aStyles[$pCell->getCoordinate()]->getHashCode() ));
				}
			}
			
			// If cell value is supplied, write cell value
			if ($pCell->getValue() != '') {
				// Map type
				$mappedType = $pCell->getDataType();
								
				// Write data type depending on its type
				switch (strtolower($mappedType)) {
					case 's': 	// String
						$objWriter->writeAttribute('t', $mappedType);
						break;
					case 'b': 	// Boolean
						$objWriter->writeAttribute('t', $mappedType);
						break;
					case 'f': 	// Formula
						$objWriter->writeAttribute('t', 'str');
						break;
				}
									
				// Write data depending on its type
				switch (strtolower($mappedType)) {
					case 's': 	// String
						$objWriter->writeElement('v', $pFlippedStringTable[$pCell->getValue()]); //array_search($pCell->getValue(), $pStringTable));
						break;
					case 'f': 	// Formula
						$objWriter->writeElement('f', substr($pCell->getValue(), 1));
						break;
					case 'n': 	// Numeric
						$objWriter->writeElement('v', $pCell->getValue());
						break;
					case 'b': 	// Boolean
						$objWriter->writeElement('v', ($pCell->getValue() ? '1' : '0'));
						break;
				}
			}

			$objWriter->endElement();
		} else {
			throw new Exception("Invalid parameters passed.");
		}
	}
	
	/**
	 * Write Drawings
	 *
	 * @param 	xmlWriter 				$objWriter 		XML Writer
	 * @param 	PHPExcel_Worksheet	$pSheet			Worksheet
	 * @throws 	Exception
	 */
	private function _writeDrawings($objWriter = null, $pSheet = null)
	{
		if ($objWriter instanceof xmlWriter && $pSheet instanceof PHPExcel_Worksheet) {
			// If sheet contains drawings, add the relationships
			if ($pSheet->getDrawingCollection()->count() > 0) {
				$objWriter->startElement('drawing');
				$objWriter->writeAttribute('r:id', 'rId1');
				$objWriter->endElement();
			}			
		} else {
			throw new Exception("Invalid parameters passed.");
		}
	}
}
