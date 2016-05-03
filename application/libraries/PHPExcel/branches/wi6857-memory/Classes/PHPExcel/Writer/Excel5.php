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
 * @package    PHPExcel_Writer_Excel5
 * @copyright  Copyright (c) 2006 - 2009 PHPExcel (http://www.codeplex.com/PHPExcel)
 * @license	http://www.gnu.org/licenses/old-licenses/lgpl-2.1.txt	LGPL
 * @version	##VERSION##, ##DATE##
 */


/** PHPExcel_IWriter */
require_once 'PHPExcel/Writer/IWriter.php';

/** PHPExcel_Cell */
require_once 'PHPExcel/Cell.php';

/** PHPExcel_Writer_Excel5_Workbook */
require_once 'PHPExcel/Writer/Excel5/Workbook.php';

/** PHPExcel_HashTable */
require_once 'PHPExcel/HashTable.php';


/**
 * PHPExcel_Writer_Excel5
 *
 * @category   PHPExcel
 * @package    PHPExcel_Writer_Excel5
 * @copyright  Copyright (c) 2006 - 2009 PHPExcel (http://www.codeplex.com/PHPExcel)
 */
class PHPExcel_Writer_Excel5 implements PHPExcel_Writer_IWriter {
	/**
	 * PHPExcel object
	 *
	 * @var PHPExcel
	 */
	private $_phpExcel;

	/**
	 * Temporary storage directory
	 *
	 * @var string
	 */
	private $_tempDir = '';

	/**
	 * Create a new PHPExcel_Writer_Excel5
	 *
	 * @param	PHPExcel	$phpExcel	PHPExcel object
	 */
	public function __construct(PHPExcel $phpExcel) {
		$this->_phpExcel	= $phpExcel;
		$this->_tempDir		= '';
	}

	/**
	 * Save PHPExcel to file
	 *
	 * @param	string		$pFileName
	 * @throws	Exception
	 */
	public function save($pFilename = null) {

		// check for iconv support
		if (!function_exists('iconv')) {
			throw new Exception("Cannot write .xls file without PHP support for iconv");
		}

		$phpExcel = $this->_phpExcel;

		// garbage collect
		$this->_phpExcel->garbageCollect();

		$workbook = new PHPExcel_Writer_Excel5_Workbook($pFilename, $phpExcel);
		$workbook->setVersion(8);

		// Set temp dir
		if ($this->_tempDir != '') {
			$workbook->setTempDir($this->_tempDir);
		}

		$saveDateReturnType = PHPExcel_Calculation_Functions::getReturnDateType();
		PHPExcel_Calculation_Functions::setReturnDateType(PHPExcel_Calculation_Functions::RETURNDATE_EXCEL);

		// add 15 identical cell style Xfs
		$cellStyleXfCollection = $this->_phpExcel->getCellStyleXfCollection();
		for ($i = 0; $i < 15; ++$i) {
			$workbook->addXfWriter($cellStyleXfCollection[0], true);
		}

		// add all the cell Xfs
		foreach ($this->_phpExcel->getCellXfCollection() as $style) {
			$workbook->addXfWriter($style, false);
		}

		// Add empty sheets
		foreach ($phpExcel->getSheetNames() as $sheetIndex => $sheetName) {
			$phpSheet  = $phpExcel->getSheet($sheetIndex);
			$worksheet = $workbook->addWorksheet($phpSheet, $xfIndexes);
		}

		PHPExcel_Calculation_Functions::setReturnDateType($saveDateReturnType);

		$workbook->close();
	}

	/**
	 * Get an array of all styles
	 *
	 * @param	PHPExcel				$pPHPExcel
	 * @return	PHPExcel_Style[]		All styles in PHPExcel
	 * @throws	Exception
	 */
	private function _allStyles(PHPExcel $pPHPExcel = null)
	{
		// Get an array of all styles
		$aStyles		= array();

		for ($i = 0; $i < $pPHPExcel->getSheetCount(); ++$i) {
			foreach ($pPHPExcel->getSheet($i)->getStyles() as $style) {
				$aStyles[] = $style;
			}
		}

		return $aStyles;
	}

	/**
	 * Get temporary storage directory
	 *
	 * @return string
	 */
	public function getTempDir() {
		return $this->_tempDir;
	}

	/**
	 * Set temporary storage directory
	 *
	 * @param	string	$pValue		Temporary storage directory
	 * @throws	Exception	Exception when directory does not exist
	 */
	public function setTempDir($pValue = '') {
		if (is_dir($pValue)) {
			$this->_tempDir = $pValue;
		} else {
			throw new Exception("Directory does not exist: $pValue");
		}
	}
}
