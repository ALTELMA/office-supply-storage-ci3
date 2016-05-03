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
 * @package    PHPExcel_Writer_Excel2007
 * @copyright  Copyright (c) 2006 - 2007 PHPExcel (http://www.codeplex.com/PHPExcel)
 * @license    http://www.gnu.org/licenses/lgpl.txt	LGPL
 * @version    ##VERSION##, ##DATE##
 */


/** PHPExcel */
require_once 'PHPExcel.php';

/** PHPExcel_Writer_Excel2007 */
require_once 'PHPExcel/Writer/Excel2007.php';

/** PHPExcel_Writer_Excel2007_WriterPart */
require_once 'PHPExcel/Writer/Excel2007/WriterPart.php';

/** PHPExcel_Cell */
require_once 'PHPExcel/Cell.php';


/**
 * PHPExcel_Writer_Excel2007_Workbook
 *
 * @category   PHPExcel
 * @package    PHPExcel_Writer_Excel2007
 * @copyright  Copyright (c) 2006 - 2007 PHPExcel (http://www.codeplex.com/PHPExcel)
 */
class PHPExcel_Writer_Excel2007_Workbook extends PHPExcel_Writer_Excel2007_WriterPart
{
	/**
	 * Write workbook to XML format
	 *
	 * @param 	PHPExcel	$pPHPExcel
	 * @return 	string 		XML Output
	 * @throws 	Exception
	 */
	public function writeWorkbook($pPHPExcel = null)
	{
		if ($pPHPExcel instanceof PHPExcel) {					
			// Create XML writer
			$objWriter = new xmlWriter();
			$objWriter->openMemory();
			
			// XML header
			$objWriter->startDocument('1.0','UTF-8','yes');

			// workbook
			$objWriter->startElement('workbook');
			$objWriter->writeAttribute('xml:space', 'preserve');
			$objWriter->writeAttribute('xmlns', 'http://schemas.openxmlformats.org/spreadsheetml/2006/main');
			$objWriter->writeAttribute('xmlns:r', 'http://schemas.openxmlformats.org/officeDocument/2006/relationships');
  
				// fileVersion
				$this->_writeFileVersion($objWriter);
							
				// workbookPr
				$this->_writeWorkbookPr($objWriter);
				
				// bookViews
				//$this->_writeBookViews($objWriter, $pPHPExcel);
				
				// workbookProtection
				$this->_writeWorkbookProtection($objWriter, $pPHPExcel);
			
				// sheets
				$this->_writeSheets($objWriter, $pPHPExcel);
				
				// definedNames
				$this->_writeDefinedNames($objWriter, $pPHPExcel);
				
				// calcPr
				$this->_writeCalcPr($objWriter);
  
			$objWriter->endElement();

			// Return
			return $objWriter->outputMemory(true);
		} else {
			throw new Exception("Invalid PHPExcel object passed.");
		}
	}
				
	/**
	 * Write file version
	 *
	 * @param 	xmlWriter $objWriter 		XML Writer
	 * @throws 	Exception
	 */
	private function _writeFileVersion($objWriter = null)
	{
		if ($objWriter instanceof xmlWriter) {
			$objWriter->startElement('fileVersion');
			$objWriter->writeAttribute('appName', 'xl');
			$objWriter->writeAttribute('lastEdited', '4');
			$objWriter->writeAttribute('lowestEdited', '4');
			$objWriter->writeAttribute('rupBuild', '4505');
			$objWriter->endElement();
		} else {
			throw new Exception("Invalid parameters passed.");
		}
	}

	/**
	 * Write WorkbookPr
	 *
	 * @param 	xmlWriter $objWriter 		XML Writer
	 * @throws 	Exception
	 */
	private function _writeWorkbookPr($objWriter = null)
	{
		if ($objWriter instanceof xmlWriter) {
			$objWriter->startElement('workbookPr');
			$objWriter->writeAttribute('codeName', 'ThisWorkbook');
			$objWriter->endElement();
		} else {
			throw new Exception("Invalid parameters passed.");
		}
	}
	
	/**
	 * Write BookViews
	 *
	 * @param 	xmlWriter $objWriter 		XML Writer
	 * @param 	PHPExcel	$pPHPExcel
	 * @throws 	Exception
	 */
	private function _writeBookViews($objWriter = null, $pPHPExcel = null)
	{
		if ($objWriter instanceof xmlWriter && $pPHPExcel instanceof PHPExcel) {
			// bookViews
			$objWriter->startElement('bookViews');
			
				// workbookView
				$objWriter->startElement('workbookView');
				$objWriter->writeAttribute('activeTab', $pPHPExcel->getActiveSheetIndex());
				$objWriter->endElement();
		
			$objWriter->endElement();
		} else {
			throw new Exception("Invalid parameters passed.");
		}
	}
	
	/**
	 * Write WorkbookProtection
	 *
	 * @param 	xmlWriter $objWriter 		XML Writer
	 * @param 	PHPExcel	$pPHPExcel
	 * @throws 	Exception
	 */
	private function _writeWorkbookProtection($objWriter = null, $pPHPExcel = null)
	{
		if ($objWriter instanceof xmlWriter && $pPHPExcel instanceof PHPExcel) {
			if ($pPHPExcel->getSecurity()->isSecurityEnabled()) {
				$objWriter->startElement('workbookProtection');
				$objWriter->writeAttribute('lockRevision',		($pPHPExcel->getSecurity()->getLockRevision() ? 'true' : 'false'));
				$objWriter->writeAttribute('lockStructure', 	($pPHPExcel->getSecurity()->getLockStructure() ? 'true' : 'false'));
				$objWriter->writeAttribute('lockWindows', 		($pPHPExcel->getSecurity()->getLockWindows() ? 'true' : 'false'));
				
				if ($pPHPExcel->getSecurity()->getRevisionsPassword() != '') {
					$objWriter->writeAttribute('revisionsPassword',	$pPHPExcel->getSecurity()->getRevisionsPassword());
				}
				
				if ($pPHPExcel->getSecurity()->getWorkbookPassword() != '') {
					$objWriter->writeAttribute('workbookPassword',	$pPHPExcel->getSecurity()->getWorkbookPassword());	
				}
				
				$objWriter->endElement();
			}
		} else {
			throw new Exception("Invalid parameters passed.");
		}
	}
			
	/**
	 * Write calcPr
	 *
	 * @param 	xmlWriter $objWriter 		XML Writer
	 * @throws 	Exception
	 */
	private function _writeCalcPr($objWriter = null)
	{
		if ($objWriter instanceof xmlWriter) {
			$objWriter->startElement('calcPr');
			$objWriter->writeAttribute('calcId', '122211');
			$objWriter->writeAttribute('calcMode', 'auto');
			$objWriter->writeAttribute('fullCalcOnLoad', 'true');
			$objWriter->endElement();
		} else {
			throw new Exception("Invalid parameters passed.");
		}
	}	
	
	/**
	 * Write sheets
	 *
	 * @param 	xmlWriter 	$objWriter 		XML Writer
	 * @param 	PHPExcel	$pPHPExcel
	 * @throws 	Exception
	 */
	private function _writeSheets($objWriter = null, $pPHPExcel = null)
	{
		if ($objWriter instanceof xmlWriter && $pPHPExcel instanceof PHPExcel) {
			// Write sheets
			$objWriter->startElement('sheets');
				
			for ($i = 0; $i < $pPHPExcel->getSheetCount(); $i++) {
				// sheet
				$this->_writeSheet(
					$objWriter,
					$pPHPExcel->getSheet($i)->getTitle(),
					($i + 1),
					($i + 1 + 3)
				);
			}
				
			$objWriter->endElement();
		} else {
			throw new Exception("Invalid parameters passed.");
		}
	}
					
	/**
	 * Write sheet
	 *
	 * @param 	xmlWriter $objWriter 		XML Writer
	 * @param 	string 	$pSheetname 		Sheet name
	 * @param 	int		$pSheetId	 		Sheet id
	 * @param 	int		$pRelId				Relationship ID
	 * @throws 	Exception
	 */
	private function _writeSheet($objWriter = null, $pSheetname = '', $pSheetId = 1, $pRelId = 1)
	{
		if ($objWriter instanceof xmlWriter && $pSheetname != '') {
			// Write sheet
			$objWriter->startElement('sheet');
			$objWriter->writeAttribute('name', 		$pSheetname);
			$objWriter->writeAttribute('sheetId', 	$pSheetId);
			$objWriter->writeAttribute('r:id', 		'rId' . $pRelId);
			$objWriter->endElement();
		} else {
			throw new Exception("Invalid parameters passed.");
		}
	}
	
	/**
	 * Write Defined Names
	 *
	 * @param 	xmlWriter 	$objWriter 		XML Writer
	 * @param 	PHPExcel	$pPHPExcel
	 * @throws 	Exception
	 */
	private function _writeDefinedNames($objWriter = null, $pPHPExcel = null)
	{
		if ($objWriter instanceof xmlWriter && $pPHPExcel instanceof PHPExcel) {
			// Write defined names
			$objWriter->startElement('definedNames');
				
			for ($i = 0; $i < $pPHPExcel->getSheetCount(); $i++) {
				// definedName for autoFilter
				if ($pPHPExcel->getSheet($i)->getAutoFilter() != '') {
					$objWriter->startElement('definedName');
					$objWriter->writeAttribute('name',			'_xlnm._FilterDatabase');
					$objWriter->writeAttribute('localSheetId',	$i);
					$objWriter->writeAttribute('hidden',		'1');
						
					// Create absolute coordinate and write as raw text
					$range = PHPExcel_Cell::splitRange($pPHPExcel->getSheet($i)->getAutoFilter());
					$range[0] = PHPExcel_Cell::absoluteCoordinate($range[0]);
					$range[1] = PHPExcel_Cell::absoluteCoordinate($range[1]);
					$range = implode(':', $range);
					
					$objWriter->writeRaw('\'' . $pPHPExcel->getSheet($i)->getTitle() . '\'!' . $range);
						
					$objWriter->endElement();
				}
			}
				
			$objWriter->endElement();
		} else {
			throw new Exception("Invalid parameters passed.");
		}
	}
}
