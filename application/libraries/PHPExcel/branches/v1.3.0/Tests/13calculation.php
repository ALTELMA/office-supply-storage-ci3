<?php
/**
 * PHPExcel
 *
 * Copyright (C) 2006 - 2007 Maarten Balliauw
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

/** Error reporting */
error_reporting(E_ALL);

/** Include path **/
ini_set('include_path', ini_get('include_path').';../Classes/');

/** PHPExcel */
include 'PHPExcel.php';

/** PHPExcel_Calculation */
require_once 'PHPExcel/Calculation.php';

// List functions
echo date('H:i:s') . " List implemented functions\n";
$objCalc = PHPExcel_Calculation::getInstance();
print_r($objCalc->listFunctionNames());

// Create new PHPExcel object
echo date('H:i:s') . " Create new PHPExcel object\n";
$objPHPExcel = new PHPExcel();

// Add some data, we will use some formulas here
echo date('H:i:s') . " Add some data\n";
$objPHPExcel->getActiveSheet()->setCellValue('A5', 'Sum:');

$objPHPExcel->getActiveSheet()->setCellValue('B1', 'Range 1');
$objPHPExcel->getActiveSheet()->setCellValue('B2', 2);
$objPHPExcel->getActiveSheet()->setCellValue('B3', 8);
$objPHPExcel->getActiveSheet()->setCellValue('B4', 10);
$objPHPExcel->getActiveSheet()->setCellValue('B5', '=SUM(B2:B4)');

$objPHPExcel->getActiveSheet()->setCellValue('C1', 'Range 2');
$objPHPExcel->getActiveSheet()->setCellValue('C2', 3);
$objPHPExcel->getActiveSheet()->setCellValue('C3', 9);
$objPHPExcel->getActiveSheet()->setCellValue('C4', 11);
$objPHPExcel->getActiveSheet()->setCellValue('C5', '=SUM(C2:C4)');

$objPHPExcel->getActiveSheet()->setCellValue('D1', 'Range 3');
$objPHPExcel->getActiveSheet()->setCellValue('D2', 2);
$objPHPExcel->getActiveSheet()->setCellValue('D3', 3);
$objPHPExcel->getActiveSheet()->setCellValue('D4', 4);
$objPHPExcel->getActiveSheet()->setCellValue('D5', '=((D2 * D3) + D4) & " should be 10"');

$objPHPExcel->getActiveSheet()->setCellValue('E1', 'Other functions');
$objPHPExcel->getActiveSheet()->setCellValue('E2', '=PI()');
$objPHPExcel->getActiveSheet()->setCellValue('E3', '=RAND()');
$objPHPExcel->getActiveSheet()->setCellValue('E4', '=RANDBETWEEN(5, 10)');

$objPHPExcel->getActiveSheet()->setCellValue('A7', 'Total of both ranges:');
$objPHPExcel->getActiveSheet()->setCellValue('B7', '=SUM(B5:C5)');

$objPHPExcel->getActiveSheet()->setCellValue('A8', 'Minimum of both ranges:');
$objPHPExcel->getActiveSheet()->setCellValue('B8', '=MIN(B2:C5)');

// Calculated data
echo date('H:i:s') . " Calculated data\n";
echo 'Value of B5: ' . $objPHPExcel->getActiveSheet()->getCell('B5')->getCalculatedValue() . "\r\n";
echo 'Value of B8: ' . $objPHPExcel->getActiveSheet()->getCell('B8')->getCalculatedValue() . "\r\n";
echo 'Value of D5: ' . $objPHPExcel->getActiveSheet()->getCell('D5')->getCalculatedValue() . "\r\n";

echo 'Value of E2: ' . $objPHPExcel->getActiveSheet()->getCell('E2')->getCalculatedValue() . "\r\n";
echo 'Value of E3: ' . $objPHPExcel->getActiveSheet()->getCell('E3')->getCalculatedValue() . "\r\n";
echo 'Value of E4: ' . $objPHPExcel->getActiveSheet()->getCell('E4')->getCalculatedValue() . "\r\n";

// Echo done
echo date('H:i:s') . " Done.\r\n";