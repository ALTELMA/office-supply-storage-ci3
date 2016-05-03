<?php
/**
 * PHPExcel
 *
 * Copyright (c) 2006 - 2008 PHPExcel
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
 * You should have received a copy of tshhe GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA
 *
 * @category   PHPExcel
 * @package    PHPExcel_Reader
 * @copyright  Copyright (c) 2006 - 2008 PHPExcel (http://www.codeplex.com/PHPExcel)
 * @license    http://www.gnu.org/licenses/old-licenses/lgpl-2.1.txt	LGPL
 * @version    ##VERSION##, ##DATE##
 */

// Original file header of ParseXL (used as the base for this class):
// --------------------------------------------------------------------------------
// Adapted from Excel_Spreadsheet_Reader developed by users bizon153,
// trex005, and mmp11 (SourceForge.net)
// http://sourceforge.net/projects/phpexcelreader/
// Primary changes made by canyoncasa (dvc) for ParseXL 1.00 ...
//	 Modelled moreso after Perl Excel Parse/Write modules
//	 Added Parse_Excel_Spreadsheet object
//		 Reads a whole worksheet or tab as row,column array or as
//		 associated hash of indexed rows and named column fields
//	 Added variables for worksheet (tab) indexes and names
//	 Added an object call for loading individual woorksheets
//	 Changed default indexing defaults to 0 based arrays
//	 Fixed date/time and percent formats
//	 Includes patches found at SourceForge...
//		 unicode patch by nobody
//		 unpack("d") machine depedency patch by matchy
//		 boundsheet utf16 patch by bjaenichen
//	 Renamed functions for shorter names
//	 General code cleanup and rigor, including <80 column width
//	 Included a testcase Excel file and PHP example calls
//	 Code works for PHP 5.x

// Primary changes made by canyoncasa (dvc) for ParseXL 1.10 ...
// http://sourceforge.net/tracker/index.php?func=detail&aid=1466964&group_id=99160&atid=623334
//	 Decoding of formula conditions, results, and tokens.
//	 Support for user-defined named cells added as an array "namedcells"
//		 Patch code for user-defined named cells supports single cells only.
//		 NOTE: this patch only works for BIFF8 as BIFF5-7 use a different
//		 external sheet reference structure


/** PHPExcel */
require_once 'PHPExcel.php';

/** PHPExcel_Reader_IReader */
require_once 'PHPExcel/Reader/IReader.php';

/** PHPExcel_Shared_OLERead */
require_once 'PHPExcel/Shared/OLERead.php';


// ParseXL definitions
define('XLS_BIFF8', 0x600);
define('XLS_BIFF7', 0x500);
define('XLS_WorkbookGlobals', 0x5);
define('XLS_Worksheet', 0x10);

define('XLS_Type_BOF', 0x809);
define('XLS_Type_EOF', 0x0a);
define('XLS_Type_BOUNDSHEET', 0x85);
define('XLS_Type_DIMENSION', 0x200);
define('XLS_Type_ROW', 0x208);
define('XLS_Type_DBCELL', 0xd7);
define('XLS_Type_FILEPASS', 0x2f);
define('XLS_Type_NOTE', 0x1c);
define('XLS_Type_TXO', 0x1b6);
define('XLS_Type_RK', 0x7e);
define('XLS_Type_RK2', 0x27e);
define('XLS_Type_MULRK', 0xbd);
define('XLS_Type_MULBLANK', 0xbe);
define('XLS_Type_INDEX', 0x20b);
define('XLS_Type_SST', 0xfc);
define('XLS_Type_EXTSST', 0xff);
define('XLS_Type_CONTINUE', 0x3c);
define('XLS_Type_LABEL', 0x204);
define('XLS_Type_LABELSST', 0xfd);
define('XLS_Type_NUMBER', 0x203);
define('XLS_Type_EXTSHEET', 0x17);
define('XLS_Type_NAME', 0x18);
define('XLS_Type_ARRAY', 0x221);
define('XLS_Type_STRING', 0x207);
define('XLS_Type_FORMULA', 0x406);
define('XLS_Type_FORMULA2', 0x6);
define('XLS_Type_FORMAT', 0x41e);
define('XLS_Type_XF', 0xe0);
define('XLS_Type_BOOLERR', 0x205);
define('XLS_Type_UNKNOWN', 0xffff);
define('XLS_Type_NINETEENFOUR', 0x22);
define('XLS_Type_MERGEDCELLS', 0xe5);
define('XLS_Type_CODEPAGE',0x42);

define('XLS_utcOffsetDays' , 25569);
define('XLS_utcOffsetDays1904', 24107);
define('XLS_SecInADay', 24 * 60 * 60);

define('XLS_DEF_NUM_FORMAT', "%s");

/**
 * PHPExcel_Reader_Excel5
 *
 * This class uses {@link http://sourceforge.net/projects/phpexcelreader/parseXL}
 *
 * @category   PHPExcel
 * @package    PHPExcel_Reader
 * @copyright  Copyright (c) 2006 - 2008 PHPExcel (http://www.codeplex.com/PHPExcel)
 */
class PHPExcel_Reader_Excel5 implements PHPExcel_Reader_IReader
{
	var $_boundsheets = array();
	var $_formatRecords = array();
	var $_sst = array();
	var $_sheets = array();
	// dvc: added list of names and their sheet associated indexes
	var $_namedcells = array();
	var $_data;
	var $_pos;
	var $_ole;
	var $_defaultEncoding;
	var $_codepage;
	var $_defaultFormat = XLS_DEF_NUM_FORMAT;
	var $_columnsFormat = array();
	var $_rowoffset = 1;
	var $_coloffset = 1;
	// dvc: added for external sheets references
	var $_extshref = array();

	var $_dateFormats = array (
		// dvc: fixed known date formats
		0xe => 'd/m/Y',
		0xf => 'd-M-y',
		0x10 => 'd-M',
		0x11 => 'M-y',
		0x12 => 'h:i A',
		0x13 => 'h:i:s A',
		0x14 => 'H:i',
		0x15 => 'H:i:s',
		0x16 => 'd/m/Y H:i',
		0x2d => 'i:s',
		0x2e => 'H:i:s',
		0x2f => 'i:s'
	);

	// dvc: separated percent formats
	var $_percentFormats = array(
		0x9 => '%1.0f%%',
		0xa => '%1.2f%%'
	);

	// dvc: removed exponentials to format as default strings.
	var $_numberFormats = array(
		0x1 => '%1.0f',
		0x2 => '%1.2f',
		0x3 => '%1.0f',
		0x4 => '%1.2f',
		0x5 => '%1.0f',
		0x6 => '$%1.0f',
		0x7 => '$%1.2f',
		0x8 => '$%1.2f',
		0x25 => '%1.0f',
		0x26 => '%1.0f',
		0x27 => '%1.2f',
		0x28 => '%1.2f',
		0x29 => '%1.0f',
		0x2a => '$%1.0f',
		0x2b => '%1.2f',
		0x2c => '$%1.2f'
	);

	/**
	 * Loads PHPExcel from file
	 *
	 * @param 	string 		$pFilename
	 * @throws 	Exception
	 */
	public function load($pFilename)
	{
		// Check if file exists
		if (!file_exists($pFilename)) {
			throw new Exception("Could not open " . $pFilename . " for reading! File does not exist.");
		}

		// Initialisations
		$excel = new PHPExcel;
		$excel->removeSheetByIndex(0);

		// Use ParseXL for the hard work.
		$this->_ole =& new PHPExcel_Shared_OLERead();

		$this->_rowoffset = $this->_coloffset = 0;
		$this->_defaultEncoding = 'ISO-8859-1';
		$this->_encoderFunction = function_exists('mb_convert_encoding') ?
			'mb_convert_encoding' : 'iconv';

		// get excel data
		$this->_read($pFilename);

		foreach($this->_boundsheets as $index => $details) {
			$sheet = &$excel->createSheet();
			$sheet->setTitle((string) $details['name']);

			// read all the columns of all the rows !
			$numrows = $this->_sheets[$index]['numRows'];
			$numcols = $this->_sheets[$index]['numCols'];
			for ($row = 0; $row < $numrows; $row++) {
				for ($col = 0; $col < $numcols; $col++) {
					@$cellcontent = $this->_sheets[$index]['cells'][$row][$col];
					@$cellinfo = $this->_sheets[$index]['cellsInfo'][$row][$col];
					if(is_null($cellcontent)) continue;

					$sheet->setCellValueByColumnAndRow($col, $row + 1,
						$cellcontent);
				}
			}
		};

		return $excel;
	}

	// set $encoder for method of UTF-16LE encoding
	private function _setUTFEncoder($encoder = 'iconv')
	{
		$this->_encoderFunction = '';
		if ($encoder == 'iconv'){
			$this->_encoderFunction = function_exists('iconv') ? 'iconv' : 'mb_convert_encoding';

		} elseif ($encoder == 'mb') {

		}
	}

	private function _read($pFilename)
	{
		$res = $this->_ole->read($pFilename);

		// oops, something goes wrong (Darko Miljanovic)
		if($res === false) { // check error code
			if($this->_ole->error == 1) { // bad file
				throw new Exception('The filename ' . $pFilename . ' is not readable');
			} elseif($this->_ole->error == 2) {
				throw new Exception('The filename ' . $pFilename . ' is not recognised as an Excel file');
			}
			// check other error codes here (eg bad fileformat, etc...)
		}

		$this->_data = $this->_ole->getWorkBook();
		$this->_pos = 0;
		$this->_parse();
	}


	private function _parse()
	{
		$pos = 0;
		$code = ord($this->_data[$pos]) | ord($this->_data[$pos + 1]) << 8;
		$length = ord($this->_data[$pos + 2]) | ord($this->_data[$pos + 3]) << 8;
		$version = ord($this->_data[$pos + 4]) | ord($this->_data[$pos + 5]) << 8;
		$substreamType = ord($this->_data[$pos + 6]) | ord($this->_data[$pos + 7]) << 8;


		if (($version != XLS_BIFF8) && ($version != XLS_BIFF7)) {
			return false;
		}
		if ($substreamType != XLS_WorkbookGlobals){
			return false;
		}
		$pos += $length + 4;
		$code = ord($this->_data[$pos]) | ord($this->_data[$pos + 1]) << 8;

		$length = ord($this->_data[$pos + 2]) | ord($this->_data[$pos + 3]) << 8;
		while ($code != XLS_Type_EOF){
			switch ($code) {
				case XLS_Type_SST:
					/**
					 * SST - Shared String Table
					 *
					 * This record contains a list of all strings used anywhere
					 * in the workbook. Each string occurs only once. The
					 * workbook uses indexes into the list to reference the
					 * strings.
					 *
					 * --	"OpenOffice.org's Documentation of the Microsoft
					 * 		Excel File Format"
					 **/
					$spos = $pos + 4;
					$limitpos = $spos + $length;
					$uniqueStrings = $this->_GetInt4d($this->_data, $spos + 4);
					$spos += 8;
					for ($i = 0; $i < $uniqueStrings; $i++) {
						// Read in the number of characters
						if ($spos == $limitpos) {
							$opcode = ord($this->_data[$spos]) | ord($this->_data[$spos + 1])<<8;
							$conlength = ord($this->_data[$spos + 2]) | ord($this->_data[$spos + 3])<<8;
							if ($opcode != 0x3c) {
								return -1;
							}
							$spos += 4;
							$limitpos = $spos + $conlength;
						}
						$numChars = ord($this->_data[$spos]) | (ord($this->_data[$spos + 1]) << 8);
						$spos += 2;
						$optionFlags = ord($this->_data[$spos]);
						$spos++;
						$asciiEncoding = (($optionFlags & 0x01) == 0) ;
						$extendedString = ( ($optionFlags & 0x04) != 0);
						// See if string contains formatting information
						$richString = ( ($optionFlags & 0x08) != 0);
						if ($richString) { // Read in the crun
							$formattingRuns = ord($this->_data[$spos]) | (ord($this->_data[$spos + 1]) << 8);
							$spos += 2;
						}
						if ($extendedString) { // Read in cchExtRst
							$extendedRunLength = $this->_GetInt4d($this->_data, $spos);
							$spos += 4;
						}
						$len = ($asciiEncoding)? $numChars : $numChars*2;
						if ($spos + $len < $limitpos) {
							$retstr = substr($this->_data, $spos, $len);
							$spos += $len;

						} else { // found countinue
							$retstr = substr($this->_data, $spos, $limitpos - $spos);
							$bytesRead = $limitpos - $spos;
							$charsLeft = $numChars - (($asciiEncoding) ? $bytesRead : ($bytesRead / 2));
							$spos = $limitpos;
							while ($charsLeft > 0){
								$opcode = ord($this->_data[$spos]) | ord($this->_data[$spos + 1]) << 8;
								$conlength = ord($this->_data[$spos + 2]) | ord($this->_data[$spos + 3]) << 8;
								if ($opcode != 0x3c) {
									return -1;
								}
								$spos += 4;
								$limitpos = $spos + $conlength;
								$option = ord($this->_data[$spos]);
								$spos += 1;
								if ($asciiEncoding && ($option == 0)) {
									$len = min($charsLeft, $limitpos - $spos);
									$retstr .= substr($this->_data, $spos, $len);
									$charsLeft -= $len;
									$asciiEncoding = true;

								} elseif (!$asciiEncoding && ($option != 0)) {
									$len = min($charsLeft * 2, $limitpos - $spos);
									$retstr .= substr($this->_data, $spos, $len);
									$charsLeft -= $len/2;
									$asciiEncoding = false;

								} elseif (!$asciiEncoding && ($option == 0)) {
									// Bummer - the string starts off as Unicode,
									// but after the continuation it is in
									// straightforward ASCII encoding
									$len = min($charsLeft, $limitpos - $spos);
									for ($j = 0; $j < $len; $j++) {
										$retstr .= $this->_data[$spos + $j].chr(0);
									}
									$charsLeft -= $len;
									$asciiEncoding = false;
								} else {
									$newstr = '';
									for ($j = 0; $j < strlen($retstr); $j++) {
										$newstr = $retstr[$j].chr(0);
									}
									$retstr = $newstr;
									$len = min($charsLeft * 2, $limitpos - $spos);
									$retstr .= substr($this->_data, $spos, $len);
									$charsLeft -= $len/2;
									$asciiEncoding = false;
								}
								$spos += $len;
							}
						}
						//$retstr = ($asciiEncoding) ?
						//	$retstr : $this->_encodeUTF16($retstr);
						// convert string according codepage and BIFF version

						if($version == XLS_BIFF8) {
							$retstr = $this->_encodeUTF16($retstr, $asciiEncoding);

						} else {
							$retstr = $this->_decodeCodepage($retstr);
						}

						if ($richString){
							$spos += 4 * $formattingRuns;
						}
						// For extended strings, skip over the extended string data
						if ($extendedString) {
							$spos += $extendedRunLength;
						}
						$this->_sst[] = $retstr;
					}
					break;

				case XLS_Type_FILEPASS:
					/**
					 * SHEETPROTECTION
					 *
					 * This record is part of the File Protection Block. It
					 * contains information about the read/write password of the
					 * file. All record contents following this record will be
					 * encrypted.
					 *
					 * --	"OpenOffice.org's Documentation of the Microsoft
					 * 		Excel File Format"
					 */
					return false;
					break;

				case XLS_Type_EXTSHEET:
					// external sheet references provided for named cells
					if ($version == XLS_BIFF8) {
						$xpos = $pos + 4;
						$xcnt = ord($this->_data[$xpos]) | ord($this->_data[$xpos + 1]) << 8;
						for ($x = 0; $x < $xcnt; $x++) {
							$this->_extshref[$x] = ord($this->_data[$xpos + 4 + 6*$x]) |
								ord($this->_data[$xpos + 5 + 6*$x]) << 8;
						}
					}
					//print_r($this->_extshref);
					break;

				case XLS_Type_NAME:
					/**
					 * DEFINEDNAME
					 *
					 * This record is part of a Link Table. It contains the name
					 * and the token array of an internal defined name. Token
					 * arrays of defined names contain tokens with aberrant
					 * token classes.
					 *
					 * --	"OpenOffice.org's Documentation of the Microsoft
					 * 		Excel File Format"
					 */
					// retrieves named cells
					$npos = $pos + 4;
					$opts = ord($this->_data[$npos]) | ord($this->_data[$npos + 1]) << 8;
					$nlen = ord($this->_data[$npos + 3]);
					$flen = ord($this->_data[$npos + 4]) | ord($this->_data[$npos + 5]) << 8;
					$fpos = $npos + 14 + 1 + $nlen;
					$nstr = substr($this->_data, $npos + 15, $nlen);
					$ftoken = ord($this->_data[$fpos]);
					if ($ftoken == 58 && $opts == 0 && $flen == 7) {
						$xref = ord($this->_data[$fpos + 1]) | ord($this->_data[$fpos + 2]) << 8;
						$frow = ord($this->_data[$fpos + 3]) | ord($this->_data[$fpos + 4]) << 8;
						$fcol = ord($this->_data[$fpos + 5]);
						if (array_key_exists($xref,$this->_extshref)) {
							$fsheet = $this->_extshref[$xref];
						} else {
							$fsheet = '';
						}
						$this->_namedcells[$nstr] = array(
							'sheet' => $fsheet,
							'row' => $frow,
							'column' => $fcol
						);
					}
					break;

				case XLS_Type_FORMAT:
					/**
					 * FORMAT
					 *
					 * This record contains information about a number format.
					 * All FORMAT records occur together in a sequential list.
					 *
					 * In BIFF2-BIFF4 other records referencing a FORMAT record
					 * contain a zero-based index into this list. From BIFF5 on
					 * the FORMAT record contains the index itself that will be
					 * used by other records.
					 *
					 * --	"OpenOffice.org's Documentation of the Microsoft
					 * 		Excel File Format"
					 */
					$indexCode = ord($this->_data[$pos + 4]) | ord($this->_data[$pos + 5]) << 8;
					if ($version == XLS_BIFF8) {
						$numchars = ord($this->_data[$pos + 6]) | ord($this->_data[$pos + 7]) << 8;
						if (ord($this->_data[$pos + 8]) == 0){
							$formatString = substr($this->_data, $pos + 9, $numchars);
						} else {
							$formatString = substr($this->_data, $pos + 9, $numchars*2);
						}
					} else {
						$numchars = ord($this->_data[$pos + 6]);
						$formatString = substr($this->_data, $pos + 7, $numchars*2);
					}
					$this->_formatRecords[$indexCode] = $formatString;

					break;

				case XLS_Type_XF:
					/**
					 * XF – Extended Format
					 *
					 * This record contains formatting information for cells,
					 * rows, columns or styles.
					 *
					 * --	"OpenOffice.org's Documentation of the Microsoft
					 * 		Excel File Format"
					 */
					$indexCode = ord($this->_data[$pos + 6]) | ord($this->_data[$pos + 7]) << 8;
					if (array_key_exists($indexCode, $this->_dateFormats)) {
						$this->_formatRecords['xfrecords'][] = array(
							'type' => 'date',
							'format' => $this->_dateFormats[$indexCode],
							'code' => $indexCode
						);
					} elseif (array_key_exists($indexCode, $this->_percentFormats)) {
						$this->_formatRecords['xfrecords'][] = array(
							'type' => 'percent',
							'format' => $this->_percentFormats[$indexCode],
							'code' => $indexCode
						);
					} elseif (array_key_exists($indexCode, $this->_numberFormats)) {
						$this->_formatRecords['xfrecords'][] = array(
							'type' => 'number',
							'format' => $this->_numberFormats[$indexCode],
							'code' => $indexCode
						);
					} else {
						if ($indexCode > 0 && isset($this->_formatRecords[$indexCode])) {
							// custom formats...
							$formatstr = $this->_formatRecords[$indexCode];
							if ($formatstr) {
								// dvc: reg exp changed to custom date/time format chars
								if (preg_match("/^[hmsdy]/i", $formatstr)) {
									// custom datetime format
									// dvc: convert Excel formats to PHP date formats
									// first remove escapes related to non-format characters
									$formatstr = str_replace('\\', '', $formatstr);
									// 4-digit year
									$formatstr = str_replace('yyyy', 'Y', $formatstr);
									// 2-digit year
									$formatstr = str_replace('yy', 'y', $formatstr);
									// first letter of month - no php equivalent
									$formatstr = str_replace('mmmmm', 'M', $formatstr);
									// full month name
									$formatstr = str_replace('mmmm', 'F', $formatstr);
									// short month name
									$formatstr = str_replace('mmm', 'M', $formatstr);
									// mm is minutes if time or month w/leading zero
									$formatstr = str_replace(':mm', ':i', $formatstr);
									// tmp place holder
									$formatstr = str_replace('mm', 'x', $formatstr);
									// month no leading zero
									$formatstr = str_replace('m', 'n', $formatstr);
									// month leading zero
									$formatstr = str_replace('x', 'm', $formatstr);
									// 12-hour suffix
									$formatstr = str_replace('AM/PM', 'A', $formatstr);
									// tmp place holder
									$formatstr = str_replace('dd', 'x', $formatstr);
									// days no leading zero
									$formatstr = str_replace('d', 'j', $formatstr);
									// days leading zero
									$formatstr = str_replace('x', 'd', $formatstr);
									// seconds
									$formatstr = str_replace('ss', 's', $formatstr);
									// fractional seconds - no php equivalent
									$formatstr = str_replace('.S', '', $formatstr);
									if (! strpos($formatstr,'A')) { // 24-hour format
										$formatstr = str_replace('h', 'H', $formatstr);
										}
									// user defined flag symbol????
									$formatstr = str_replace(';@', '', $formatstr);
									$this->_formatRecords['xfrecords'][] = array(
										'type' => 'date',
										'format' => $formatstr,
										'code' => $indexCode
									);
								}
								// dvc: new code for custom percent formats
								else if (preg_match('/%$/', $formatstr)) { // % number format
									if (preg_match('/\.[#0]+/i',$formatstr,$m)) {
										$s = substr($m[0],0,1).(strlen($m[0])-1);
										$formatstr = str_replace($m[0],$s,$formatstr);
									}
									if (preg_match('/^[#0]+/',$formatstr,$m)) {
										$formatstr = str_replace($m[0],strlen($m[0]),$formatstr);
									}
									$formatstr = '%' . str_replace('%',"f%%",$formatstr);
									$this->_formatRecords['xfrecords'][] = array(
										'type' => 'percent',
										'format' => $formatstr,
										'code' => $indexCode
									);
								}
								// dvc: code for other unknown formats
								else {
									// dvc: changed to add format to unknown for debug
									$this->_formatRecords['xfrecords'][] = array(
										'type' => 'other',
										'format' => $this->_defaultFormat,
										'code' => $indexCode
									);
								}
							}

						} else {
							// dvc: changed to add format to unknown for debug
							if (isset($this->_formatRecords[$indexCode])) {
								$formatstr = $this->_formatRecords[$indexCode];
								$type = 'undefined';
							} else {
								$formatstr = $this->_defaultFormat;
								$type = 'default';
							}
							$this->_formatRecords['xfrecords'][] = array(
								'type' => $type,
								'format' => $formatstr,
								'code' => $indexCode
							);
						}
					}
					break;

				case XLS_Type_NINETEENFOUR:
					/**
					 * DATEMODE
					 *
					 * This record specifies the base date for displaying date
					 * values. All dates are stored as count of days past this
					 * base date. In BIFF2-BIFF4 this record is part of the
					 * Calculation Settings Block. In BIFF5-BIFF8 it is
					 * stored in the Workbook Globals Substream.
					 *
					 * --	"OpenOffice.org's Documentation of the Microsoft
					 * 		Excel File Format"
					 */
					$this->_nineteenFour = (ord($this->_data[$pos + 4]) == 1);
					break;

				case XLS_Type_BOUNDSHEET:
					/**
					 * SHEET
					 *
					 * This record is  located in the  Workbook Globals
					 * Substream  and represents a sheet inside the workbook.
					 * One SHEET record is written for each sheet. It stores the
					 * sheet name and a stream offset to the BOF record of the
					 * respective Sheet Substream within the Workbook Stream.
					 *
					 * --	"OpenOffice.org's Documentation of the Microsoft
					 * 		Excel File Format"
					 */
					$rec_offset = $this->_GetInt4d($this->_data, $pos + 4);
					$rec_typeFlag = ord($this->_data[$pos + 8]);
					$rec_visibilityFlag = ord($this->_data[$pos + 9]);
					$rec_length = ord($this->_data[$pos + 10]);



					if ($version == XLS_BIFF8) {
						$compressedUTF16 = ((ord($this->_data[$pos + 11]) & 0x01) == 0);
						$rec_length = ($compressedUTF16) ? $rec_length : $rec_length*2;
						$rec_name = $this->_encodeUTF16(substr($this->_data, $pos + 12, $rec_length), $compressedUTF16);
					} elseif ($version == XLS_BIFF7) {
						$rec_name		= substr($this->_data, $pos + 11, $rec_length);
					}
					$this->_boundsheets[] = array(
						'name' => $rec_name,
						'offset' => $rec_offset
					);
					break;

				case XLS_Type_CODEPAGE:
					/**
					 * CODEPAGE
					 *
					 * This record stores the text encoding used to write byte
					 * strings, stored as MS Windows code page identifier.
					 *
					 * --	"OpenOffice.org's Documentation of the Microsoft
					 * 		Excel File Format"
					 */
					$codepage = $this->_GetInt2d($this->_data, $pos + 4);
					switch($codepage) {
						case 367: // ASCII
							$this->_codepage ="ASCII";
							break;
						case 437: //OEM US
							$this->_codepage ="CP437";
							break;
						case 720: //OEM Arabic
							// currently not supported by libiconv
							$this->_codepage = "";
							break;
						case 737: //OEM Greek
							$this->_codepage ="CP737";
							break;
						case 775: //OEM Baltic
							$this->_codepage ="CP775";
							break;
						case 850: //OEM Latin I
							$this->_codepage ="CP850";
							break;
						case 852: //OEM Latin II (Central European)
							$this->_codepage ="CP852";
							break;
						case 855: //OEM Cyrillic
							$this->_codepage ="CP855";
							break;
						case 857: //OEM Turkish
							$this->_codepage ="CP857";
							break;
						case 858: //OEM Multilingual Latin I with Euro
							$this->_codepage ="CP858";
							break;
						case 860: //OEM Portugese
							$this->_codepage ="CP860";
							break;
						case 861: //OEM Icelandic
							$this->_codepage ="CP861";
							break;
						case 862: //OEM Hebrew
							$this->_codepage ="CP862";
							break;
						case 863: //OEM Canadian (French)
							$this->_codepage ="CP863";
							break;
						case 864: //OEM Arabic
							$this->_codepage ="CP864";
							break;
						case 865: //OEM Nordic
							$this->_codepage ="CP865";
							break;
						case 866: //OEM Cyrillic (Russian)
							$this->_codepage ="CP866";
							break;
						case 869: //OEM Greek (Modern)
							$this->_codepage ="CP869";
							break;
						case 874: //ANSI Thai
							$this->_codepage ="CP874";
							break;
						case 932: //ANSI Japanese Shift-JIS
							$this->_codepage ="CP932";
							break;
						case 936: //ANSI Chinese Simplified GBK
							$this->_codepage ="CP936";
							break;
						case 949: //ANSI Korean (Wansung)
							$this->_codepage ="CP949";
							break;
						case 950: //ANSI Chinese Traditional BIG5
							$this->_codepage ="CP950";
							break;
						case 1200: //UTF-16 (BIFF8)
							$this->_codepage ="UTF-16LE";
							break;
						case 1250:// ANSI Latin II (Central European)
							$this->_codepage ="CP1250";
							break;
						case 1251: //ANSI Cyrillic
							$this->_codepage ="CP1251";
							break;
						case 1252: //ANSI Latin I (BIFF4-BIFF7)
							$this->_codepage ="CP1252";
							break;
						case 1253: //ANSI Greek
							$this->_codepage ="CP1253";
							break;
						case 1254: //ANSI Turkish
							$this->_codepage ="CP1254";
							break;
						case 1255: //ANSI Hebrew
							$this->_codepage ="CP1255";
							break;
						case 1256: //ANSI Arabic
							$this->_codepage ="CP1256";
							break;
						case 1257: //ANSI Baltic
							$this->_codepage ="CP1257";
							break;
						case 1258: //ANSI Vietnamese
							$this->_codepage ="CP1258";
							break;
						case 1361: //ANSI Korean (Johab)
							$this->_codepage ="CP1361";
							break;
						case 10000: //Apple Roman
							// currently not supported by libiconv
							$this->_codepage = "";
							break;
						case 32768: //Apple Roman
							// currently not supported by libiconv
							$this->_codepage = "";
							break;
						case 32769: //ANSI Latin I (BIFF2-BIFF3)
							// currently not supported by libiconv
							$this->_codepage = "";
							break;
					}
					break;
			}
			$pos += $length + 4;
			$code = ord($this->_data[$pos]) | ord($this->_data[$pos + 1]) << 8;
			$length = ord($this->_data[$pos + 2]) | ord($this->_data[$pos + 3]) << 8;
		}

		foreach ($this->_boundsheets as $key => $val){
			$this->_sn = $key;
			$this->_parsesheet($val['offset']);
		}

		return true;
	}

	private function _parsesheet($spos)
	{
		$cont = true;
		// read BOF
		$code = ord($this->_data[$spos]) | ord($this->_data[$spos + 1]) << 8;
		$length = ord($this->_data[$spos + 2]) | ord($this->_data[$spos + 3]) << 8;
		$version = ord($this->_data[$spos + 4]) | ord($this->_data[$spos + 5]) << 8;
		$substreamType = ord($this->_data[$spos + 6]) | ord($this->_data[$spos + 7]) << 8;

		if (($version != XLS_BIFF8) && ($version != XLS_BIFF7)) {
			return -1;
		}
		if ($substreamType != XLS_Worksheet) {
			return -2;
		}

		$spos += $length + 4;
		while($cont) {
			$lowcode = ord($this->_data[$spos]);
			if ($lowcode == XLS_Type_EOF) {
				break;
			}

			$code = $lowcode | ord($this->_data[$spos + 1]) << 8;
			$length = ord($this->_data[$spos + 2]) | ord($this->_data[$spos + 3]) << 8;
			$spos += 4;
			$this->_sheets[$this->_sn]['maxrow'] = $this->_rowoffset - 1;
			$this->_sheets[$this->_sn]['maxcol'] = $this->_coloffset - 1;
			unset($this->_rectype);
			unset($this->_formula);
			unset($this->_formula_result);
			$this->_multiplier = 1; // need for format with %

			switch ($code) {
				case XLS_Type_DIMENSION:
					/**
					 * DIMENSION
					 *
					 * This record contains the range address of the used area
					 * in the current sheet.
					 *
					 * --	"OpenOffice.org's Documentation of the Microsoft
					 * 		Excel File Format"
					 */
					if (!isset($this->_numRows)) {
						if (($length == 10) ||	($version == XLS_BIFF7)){
							$this->_sheets[$this->_sn]['numRows'] = ord($this->_data[$spos + 2]) | ord($this->_data[$spos + 3]) << 8;
							$this->_sheets[$this->_sn]['numCols'] = ord($this->_data[$spos + 6]) | ord($this->_data[$spos + 7]) << 8;
						} else {
							$this->_sheets[$this->_sn]['numRows'] = ord($this->_data[$spos + 4]) | ord($this->_data[$spos + 5]) << 8;
							$this->_sheets[$this->_sn]['numCols'] = ord($this->_data[$spos + 10]) | ord($this->_data[$spos + 11]) << 8;
						}
					}
					break;

				case XLS_Type_MERGEDCELLS:
					/**
					 * MERGEDCELLS
					 *
					 * This record contains the addresses of merged cell ranges
					 * in the current sheet.
					 *
					 * --	"OpenOffice.org's Documentation of the Microsoft
					 * 		Excel File Format"
					 */
					$cellRanges = ord($this->_data[$spos]) |
						ord($this->_data[$spos + 1]) << 8;

					for ($i = 0; $i < $cellRanges; $i++) {
						$fr = ord($this->_data[$spos + 8*$i + 2]) | ord($this->_data[$spos + 8*$i + 3]) << 8;
						$lr = ord($this->_data[$spos + 8*$i + 4]) | ord($this->_data[$spos + 8*$i + 5]) << 8;
						$fc = ord($this->_data[$spos + 8*$i + 6]) | ord($this->_data[$spos + 8*$i + 7]) << 8;
						$lc = ord($this->_data[$spos + 8*$i + 8]) | ord($this->_data[$spos + 8*$i + 9]) << 8;
						if ($lr - $fr > 0) {
							$this->_sheets[$this->_sn]['cellsInfo'][$fr + 1][$fc + 1]['rowspan'] = $lr - $fr + 1;
						}
						if ($lc - $fc > 0) {
							$this->_sheets[$this->_sn]['cellsInfo'][$fr + 1][$fc + 1]['colspan'] = $lc - $fc + 1;
						}
					}
					break;

				case XLS_Type_RK:
				case XLS_Type_RK2:
					/**
					 * RK
					 *
					 * This record represents a cell that contains an RK value
					 * (encoded integer or floating-point value). If a
					 * floating-point value cannot be encoded to an RK value,
					 * a NUMBER record will be written. This record replaces the
					 * record INTEGER written in BIFF2.
					 *
					 * --	"OpenOffice.org's Documentation of the Microsoft
					 * 		Excel File Format"
					 */
					$row = ord($this->_data[$spos]) | ord($this->_data[$spos + 1]) << 8;
					$column = ord($this->_data[$spos + 2]) | ord($this->_data[$spos + 3]) << 8;
					$rknum = $this->_GetInt4d($this->_data, $spos + 6);
					$numValue = $this->_GetIEEE754($rknum);

					if ($this->_isDate($spos)) {
						list($string, $raw) = $this->_createDate($numValue);
					} else {
						$raw = $numValue;
						if (isset($this->_columnsFormat[$column + 1])){
							$this->_curformat = $this->_columnsFormat[$column + 1];
						}
						$string = sprintf($this->_curformat,$numValue*$this->_multiplier);
					}
					$this->_addcell($row, $column, $string, $raw);
					break;

				case XLS_Type_LABELSST:
					/**
					 * LABELSST
					 *
					 * This record represents a cell that contains a string. It
					 * replaces the LABEL record and RSTRING record used in
					 * BIFF2-BIFF5.
					 *
					 * --	"OpenOffice.org's Documentation of the Microsoft
					 * 		Excel File Format"
					 */
					$row = ord($this->_data[$spos]) | ord($this->_data[$spos + 1]) << 8;
					$column = ord($this->_data[$spos + 2]) | ord($this->_data[$spos + 3]) << 8;
					$xfindex = ord($this->_data[$spos + 4]) | ord($this->_data[$spos + 5]) << 8;
					$index = $this->_GetInt4d($this->_data, $spos + 6);
					$this->_addcell($row, $column, $this->_sst[$index]);
					break;

				case XLS_Type_MULRK:
					/**
					 * MULRK – Multiple RK
					 *
					 * This record represents a cell range containing RK value
					 * cells. All cells are located in the same row.
					 *
					 * --	"OpenOffice.org's Documentation of the Microsoft
					 * 		Excel File Format"
					 */
					$row = ord($this->_data[$spos]) | ord($this->_data[$spos + 1]) << 8;
					$colFirst = ord($this->_data[$spos + 2]) | ord($this->_data[$spos + 3]) << 8;
					$colLast = ord($this->_data[$spos + $length - 2]) | ord($this->_data[$spos + $length - 1]) << 8;
					$columns = $colLast - $colFirst + 1;
					$tmppos = $spos + 4;

					for ($i = 0; $i < $columns; $i++) {
						$numValue = $this->_GetIEEE754($this->_GetInt4d($this->_data, $tmppos + 2));
						if ($this->_isDate($tmppos-4)) {
							list($string, $raw) = $this->_createDate($numValue);
						} else {
							$raw = $numValue;
							if (isset($this->_columnsFormat[$colFirst + $i + 1])){
								$this->_curformat = $this->_columnsFormat[$colFirst+ $i + 1];
							}
							$string = sprintf($this->_curformat, $numValue *
								$this->_multiplier);
						}
						$tmppos += 6;
						$this->_addcell($row, $colFirst + $i, $string, $raw);
					}
					break;

				case XLS_Type_NUMBER:
					/**
					 * NUMBER
					 *
					 * This record represents a cell that contains a
					 * floating-point value.
					 *
					 * --	"OpenOffice.org's Documentation of the Microsoft
					 * 		Excel File Format"
					 */
					$row = ord($this->_data[$spos]) | ord($this->_data[$spos + 1]) << 8;
					$column = ord($this->_data[$spos + 2]) | ord($this->_data[$spos + 3]) << 8;

					if ($this->_isDate($spos)) {
						$numValue = $this->_createNumber($spos);
						list($string, $raw) = $this->_createDate($numValue);
					} else {
						if (isset($this->_columnsFormat[$column + 1])) {
							$this->_curformat = $this->_columnsFormat[$column + 1];
						}
						$raw = $this->_createNumber($spos);
						$string = sprintf($this->_curformat, $raw * $this->_multiplier);
					}
					$this->_addcell($row, $column, $string, $raw);
					break;

				case XLS_Type_FORMULA:
				case XLS_Type_FORMULA2:
					/**
					 * FORMULA
					 *
					 * This record contains the token array and the result of a
					 * formula cell.
					 *
					 * --	"OpenOffice.org's Documentation of the Microsoft
					 * 		Excel File Format"
					 */
					$row = ord($this->_data[$spos]) | ord($this->_data[$spos + 1]) << 8;
					$column = ord($this->_data[$spos + 2]) | ord($this->_data[$spos + 3]) << 8;
					$xfindex = ord($this->_data[$spos + 4]) | ord($this->_data[$spos + 5]) << 8;

					if ((ord($this->_data[$spos + 6]) == 0) &&
					(ord($this->_data[$spos + 12]) == 255) &&
					(ord($this->_data[$spos + 13]) == 255)) {
						//String formula. Result follows in appended STRING record
						$this->_formula_result = 'string';
						$soff = $spos + $length;
						$scode = ord($this->_data[$soff]) | ord($this->_data[$soff + 1])<<8;
						$sopt = ord($this->_data[$soff + 6]);
						// only reads byte strings...
						if ($scode == XLS_Type_STRING && $sopt == '0') {
							$slen = ord($this->_data[$soff + 4]) | ord($this->_data[$soff + 5]) << 8;
							$string = substr($this->_data, $soff + 7, ord($this->_data[$soff + 4]) | ord($this->_data[$soff + 5]) << 8);
						} else {
							$string = 'NOT FOUND';
						}
						$raw = $string;

					} elseif ((ord($this->_data[$spos + 6]) == 1) &&
					(ord($this->_data[$spos + 12]) == 255) &&
					(ord($this->_data[$spos + 13]) == 255)) {
						//Boolean formula. Result is in +2; 0=false,1=true
						$this->_formula_result = 'boolean';
						$raw = ord($this->_data[$spos + 8]);
						if ($raw) {
							$string = "TRUE";
						} else {
							$string = "FALSE";
						}

					} elseif ((ord($this->_data[$spos + 6]) == 2) &&
					(ord($this->_data[$spos + 12]) == 255) &&
					(ord($this->_data[$spos + 13]) == 255)) {
						//Error formula. Error code is in +2
						$this->_formula_result = 'error';
						$raw = ord($this->_data[$spos + 8]);
						$string = 'ERROR:'.$raw;

					} elseif ((ord($this->_data[$spos + 6]) == 3) &&
					(ord($this->_data[$spos + 12]) == 255) &&
					(ord($this->_data[$spos + 13]) == 255)) {
						//Formula result is a null string
						$this->_formula_result = 'null';
						$raw = '';
						$string = '';

					} else {
						// forumla result is a number, first 14 bytes like _NUMBER record
						$this->_formula_result = 'number';
						if ($this->_isDate($spos)) {
							$numValue = $this->_createNumber($spos);
							list($string, $raw) = $this->_createDate($numValue);
						} else {
							if (isset($this->_columnsFormat[$column + 1])){
								$this->_curformat = $this->_columnsFormat[$column + 1];
							}
							$raw = $this->_createNumber($spos);
							$string = sprintf($this->_curformat, $raw * $this->_multiplier);
						}
					}
					// save the raw formula tokens for end user interpretation
					// Excel stores as a token record
					$this->_rectype = 'formula';
					// read formula record tokens ...
					$tokenlength = ord($this->_data[$spos + 20]) | ord($this->_data[$spos + 21]) << 8;
					for ($i = 0; $i < $tokenlength; $i++) {
						$this->_formula[$i] = ord($this->_data[$spos + 22 + $i]);
					}
					$this->_addcell($row, $column, $string, $raw);
					break;

				case XLS_Type_BOOLERR:
					/**
					 * BOOLERR
					 *
					 * This record represents a Boolean value or error value
					 * cell.
					 *
					 * --	"OpenOffice.org's Documentation of the Microsoft
					 * 		Excel File Format"
					 */
					$row = ord($this->_data[$spos]) | ord($this->_data[$spos + 1]) << 8;
					$column = ord($this->_data[$spos + 2]) | ord($this->_data[$spos + 3]) << 8;
					$string = ord($this->_data[$spos + 6]);
					$this->_addcell($row, $column, $string);
					break;

				case XLS_Type_ROW:
					/**
					 * ROW
					 *
					 * This record contains the properties of a single row in a
					 * sheet. Rows and cells in a sheet are divided into blocks
					 * of 32 rows.
					 *
					 * --	"OpenOffice.org's Documentation of the Microsoft
					 * 		Excel File Format"
					 */
				case XLS_Type_DBCELL:
					/**
					 * DBCELL
					 *
					 * This record is written once in a Row Block. It contains
					 * relative offsets to calculate the stream position of the
					 * first cell record for each row. The offset list in this
					 * record contains as many offsets as ROW records are
					 * present in the Row Block.
					 *
					 * --	"OpenOffice.org's Documentation of the Microsoft
					 * 		Excel File Format"
					 */
				case XLS_Type_MULBLANK:
					/**
					 * MULBLANK – Multiple BLANK
					 *
					 * This record represents a cell range of empty cells. All
					 * cells are located in the same row
					 *
					 * --	"OpenOffice.org's Documentation of the Microsoft
					 * 		Excel File Format"
					 */
					break;

				case XLS_Type_LABEL:
					/**
					 * LABEL
					 *
					 * This record represents a cell that contains a string. In
					 * BIFF8 it is usually replaced by the LABELSST record.
					 * Excel still uses this record, if it copies unformatted
					 * text cells to the clipboard.
					 *
					 * --	"OpenOffice.org's Documentation of the Microsoft
					 * 		Excel File Format"
					 */
					$row = ord($this->_data[$spos]) | ord($this->_data[$spos + 1]) << 8;
					$column = ord($this->_data[$spos + 2]) | ord($this->_data[$spos + 3]) << 8;
					$this->_addcell($row, $column, substr($this->_data, $spos + 8,
						ord($this->_data[$spos + 6]) | ord($this->_data[$spos + 7]) << 8));
					break;

				case XLS_Type_EOF:
					$cont = false;
					break;

				default:
					break;
			}
			$spos += $length;
		}
		if (!isset($this->_sheets[$this->_sn]['numRows'])){
			$this->_sheets[$this->_sn]['numRows'] = $this->_sheets[$this->_sn]['maxrow'];
		}
		if (!isset($this->_sheets[$this->_sn]['numCols'])){
			$this->_sheets[$this->_sn]['numCols'] = $this->_sheets[$this->_sn]['maxcol'];
		}
	}

	private function _isDate($spos)
	{
		$xfindex = ord($this->_data[$spos + 4]) | ord($this->_data[$spos + 5]) << 8;
		$this->_curformat = $this->_formatRecords['xfrecords'][$xfindex]['format'];
		$this->_fmtcode = $this->_formatRecords['xfrecords'][$xfindex]['code'];

		if ($this->_formatRecords['xfrecords'][$xfindex]['type'] == 'date') {
			$this->_rectype = 'date';
			return true;

		} else if (($xfindex == 0x9) || ($xfindex == 0xa) || ($this->_formatRecords['xfrecords'][$xfindex]['type'] == 'percent')) {
			$this->_rectype = 'number';
			$this->_multiplier = 100;
			}

		else if ($this->_formatRecords['xfrecords'][$xfindex]['type'] == 'number') {
			$this->_rectype = 'number';

		} else {
			$this->_rectype = 'unknown';
		}
		return false;
	}

	private function _createDate($numValue)
	{
		if ($numValue > 1){
			$utcDays = $numValue - ($this->_nineteenFour ? XLS_utcOffsetDays1904 : XLS_utcOffsetDays);
			$utcValue = round(($utcDays * XLS_SecInADay));
			// dvc: excel returns local date/time as absolutes,
			// i.e. 1 hr = 0.04166, 1 day = 1,
			// so need to treat as GMT to translate
			$string = gmdate ($this->_curformat, $utcValue);
			$raw = $utcValue;
		} else {
			// assume a time format...
			$raw = $numValue;
			$hours = round($numValue * 24);
			$mins = round($numValue * 24*60) - $hours * 60;
			$secs = round($numValue * XLS_SecInADay) - $hours *60*60 - $mins * 60;
			$string = date ($this->_curformat, mktime($hours, $mins, $secs));
		}
		return array($string, $raw);
	}

	private function _createNumber($spos)
	{
		$rknumhigh = $this->_GetInt4d($this->_data, $spos + 10);
		$rknumlow = $this->_GetInt4d($this->_data, $spos + 6);
		$sign = ($rknumhigh & 0x80000000) >> 31;
		$exp = ($rknumhigh & 0x7ff00000) >> 20;
		$mantissa = (0x100000 | ($rknumhigh & 0x000fffff));
		$mantissalow1 = ($rknumlow & 0x80000000) >> 31;
		$mantissalow2 = ($rknumlow & 0x7fffffff);
		$value = $mantissa / pow( 2 , (20- ($exp - 1023)));

		if ($mantissalow1 != 0) {
			$value += 1 / pow (2 , (21 - ($exp - 1023)));
		}

		$value += $mantissalow2 / pow (2 , (52 - ($exp - 1023)));
		if ($sign) {
			$value = -1 * $value;
		}

		return	$value;
	}

	private function _addcell($row, $col, $string, $raw = '')
	{
		$this->_sheets[$this->_sn]['maxrow'] =
			max($this->_sheets[$this->_sn]['maxrow'], $row + $this->_rowoffset);
		$this->_sheets[$this->_sn]['maxcol'] =
			max($this->_sheets[$this->_sn]['maxcol'], $col + $this->_coloffset);
		$this->_sheets[$this->_sn]['cells'][$row +
			$this->_rowoffset][$col + $this->_coloffset] = $string;

		if ($raw) {
			$this->_sheets[$this->_sn]['cellsInfo'][$row + $this->_rowoffset][$col + $this->_coloffset]['raw'] = $raw;
		}

		if (isset($this->_rectype)) {
			$this->_sheets[$this->_sn]['cellsInfo'][$row +
				$this->_rowoffset][$col + $this->_coloffset]['type'] =
					$this->_rectype;

			if (isset($this->_curformat)) {
				$this->_sheets[$this->_sn]['cellsInfo'][$row +
					$this->_rowoffset][$col + $this->_coloffset]['format'] =
						$this->_curformat;
			}

			if (isset($this->_fmtcode)) {
				$this->_sheets[$this->_sn]['cellsInfo'][$row +
					$this->_rowoffset][$col + $this->_coloffset]['code'] =
						$this->_fmtcode;
			}

			if (isset($this->_formula)) {
				$this->_sheets[$this->_sn]['cellsInfo'][$row +
					$this->_rowoffset][$col + $this->_coloffset]['formula_tokens'] =
						$this->_formula;
			}

			if (isset($this->_formula_result)) {
				$this->_sheets[$this->_sn]['cellsInfo'][$row +
					$this->_rowoffset][$col + $this->_coloffset]['formula_result'] =
						$this->_formula_result;
			}
		}
	}

	private function _GetIEEE754($rknum)
	{
		if (($rknum & 0x02) != 0) {
			$value = $rknum >> 2;
		}
		else {
			// changes by mmp, info on IEEE754 encoding from
			// research.microsoft.com/~hollasch/cgindex/coding/ieeefloat.html
			// The RK format calls for using only the most significant 30 bits
			// of the 64 bit floating point value. The other 34 bits are assumed
			// to be 0 so we use the upper 30 bits of $rknum as follows...
			$sign = ($rknum & 0x80000000) >> 31;
			$exp = ($rknum & 0x7ff00000) >> 20;
			$mantissa = (0x100000 | ($rknum & 0x000ffffc));
			$value = $mantissa / pow( 2 , (20- ($exp - 1023)));
			if ($sign) {
				$value = -1 * $value;
			}
			//end of changes by mmp
		}
		if (($rknum & 0x01) != 0) {
			$value /= 100;
		}
		return $value;
	}

	private function _encodeUTF16($string, $compressed = '')
	{
		$result = $string;
		if ($this->_defaultEncoding) {
			if($compressed) {
				$string = $this->_uncompressByteString($string);
			}
			switch ($this->_encoderFunction){
				case 'iconv' :
					$result = iconv('UTF-16LE', $this->_defaultEncoding, $string);
					break;
				case 'mb_convert_encoding' :
					$result = mb_convert_encoding($string, $this->_defaultEncoding,
						'UTF-16LE' );
					break;
			}
		}
		return $result;
	}

	private function _GetInt2d($data, $pos)
	{
		return ord($data[$pos]) | (ord($data[$pos + 1]) << 8);
	}

	private function _GetInt4d($data, $pos)
	{
		return ord($data[$pos]) | (ord($data[$pos + 1]) << 8) |
			(ord($data[$pos + 2]) << 16) | (ord($data[$pos + 3]) << 24);
	}

	private function _uncompressByteString($string)
	{
		$uncompressedString = "";
		for($i = 0; $i < strlen($string); $i++) {
			$uncompressedString .= $string[$i]."\0";
		}

		return $uncompressedString;
	}

	private function _decodeCodepage($string)
	{
		$result = $string;
		if ($this->_defaultEncoding && $this->_codepage) {
			switch ($this->_encoderFunction) {
				case 'iconv' :
					$result = iconv($this->_codepage,$this->_defaultEncoding,$string);
					break;
				case 'mb_convert_encoding' :
					$result = mb_convert_encoding($string, $this->_defaultEncoding, $this->_codepage );
					break;
			}
		}
		return $result;
	}
}
