<?php
// ------------------------------------------------------------------------ //
// XOOPS - PHP Content Management System					  //
// Copyright (c) 2000 XOOPS.org						   //
// <http://www.xoops.org/>							 //
// ------------------------------------------------------------------------ //
// This program is free software; you can redistribute it and/or modify	 //
// it under the terms of the GNU General Public License as published by	 //
// the Free Software Foundation; either version 2 of the License, or		//
// (at your option) any later version.									  //
// //
// You may not change or alter any portion of this comment or credits	   //
// of supporting developers from this source code or any supporting		 //
// source code which is considered copyrighted (c) material of the		  //
// original comment or credit authors.									  //
// //
// This program is distributed in the hope that it will be useful,		  //
// but WITHOUT ANY WARRANTY; without even the implied warranty of		   //
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the			//
// GNU General Public License for more details.							 //
// //
// You should have received a copy of the GNU General Public License		//
// along with this program; if not, write to the Free Software			  //
// Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307 USA //
// ------------------------------------------------------------------------ //
// Author: Kazumi Ono (AKA onokazu)										  //
// URL: http://www.myweb.ne.jp/, http://www.xoops.org/, http://jp.xoops.org/ //
// Project: The XOOPS Project												//
// ------------------------------------------------------------------------- //
/**
 * !
 * Example
 *
 * include_once 'uploader.php';
 * $allowed_mimetypes = array('image/gif', 'image/jpeg', 'image/pjpeg', 'image/x-png');
 * $maxfilesize = 50000;
 * $maxfilewidth = 120;
 * $maxfileheight = 120;
 * $uploader = new XoopsMediaUploader('/home/xoops/uploads', $allowed_mimetypes, $maxfilesize, $maxfilewidth, $maxfileheight);
 * if( $uploader->fetchMedia($HTTP_POST_VARS['uploade_file_name'])) {
 * if( !$uploader->upload()) {
 * echo $uploader->getErrors();
 * } else {
 * echo '<h4>File uploaded successfully!</h4>'
 * echo 'Saved as: ' . $uploader->getSavedFileName() . '<br />';
 * echo 'Full path: ' . $uploader->getSavedDestination();
 * }
 * } else {
 * echo $uploader->getErrors();
 * }
 */

/**
 * Upload Media files
 *
 * Example of usage:
 * <code>
 * include_once 'uploader.php';
 * $allowed_mimetypes = array('image/gif', 'image/jpeg', 'image/pjpeg', 'image/x-png');
 * $maxfilesize = 50000;
 * $maxfilewidth = 120;
 * $maxfileheight = 120;
 * $uploader = new XoopsMediaUploader('/home/xoops/uploads', $allowed_mimetypes, $maxfilesize, $maxfilewidth, $maxfileheight);
 * if( $uploader->fetchMedia($HTTP_POST_VARS['uploade_file_name'])) {
 *			if( !$uploader->upload()) {
 *			   echo $uploader->getErrors();
 *			} else {
 *			   echo '<h4>File uploaded successfully!</h4>'
 *			   echo 'Saved as: ' . $uploader->getSavedFileName() . '<br />';
 *			   echo 'Full path: ' . $uploader->getSavedDestination();
 *			}
 * } else {
 *			echo $uploader->getErrors();
 * }
 * </code>
 *
 * @package kernel
 * @subpackage core
 * @author Kazumi Ono <onokazu@xoops.org>
 * @copyright (c) 2000-2003 The Xoops Project - www.xoops.org
 */
mt_srand((double) microtime() * 1000000);

class xformsMediaUploader {
	var $mediaName;
	var $mediaType;
	var $mediaSize;
	var $mediaTmpName;
	var $mediaError;
	var $uploadDir = '';
	var $allowedExtensions;
	var $allowedMimeTypes;
	var $maxFileSize;
	var $maxWidth;
	var $maxHeight;
	var $targetFileName;
	var $prefix;
	var $ext;
	var $dimension;
	var $errors = array();
	var $savedDestination;
	var $savedFileName;
	var $noadmin_sizecheck;
	/**
	 * Constructor
	 *
	 * @param string $uploadDir
	 * @param array $allowedMimeTypes
	 * @param int $maxFileSize
	 * @param int $maxWidth
	 * @param int $maxHeight
	 * @param int $cmodvalue
	 */
	function xformsMediaUploader($uploadDir=null, $maxFileSize=0, $allowedExtensions=0, $allowedMimeTypes=0, $maxWidth=0, $maxHeight=0){
		if( !empty($maxFileSize) ){
			$this->maxFileSize = intval($maxFileSize);
		}
		if( !empty($allowedExtensions) ){
			$this->allowedExtensions = $allowedExtensions;
		}
		if( is_array($allowedMimeTypes) ){
			$this->allowedMimeTypes = $allowedMimeTypes;
		}
		if( !empty($maxWidth) ){
			$this->maxWidth = intval($maxWidth);
		}
		if( !empty($maxHeight) ){
			$this->maxHeight = intval($maxHeight);
		}
		if( is_dir($uploadDir) ){
			$this->uploadDir = $uploadDir;
		}
	}

	function noAdminSizeCheck($value){
		$this->noadmin_sizecheck = $value;
	}

	/**
	 * Fetch the uploaded file
	 *
	 * @param string $media_name Name of the file field
	 * @param int $index Index of the file (if more than one uploaded under that name)
	 * @global $HTTP_POST_FILES
	 * @return bool
	 */
	function fetchMedia($media_name, $index = null, &$ele){
		if( !isset($_FILES[$media_name]) ){
			$this->setErrors('You either did not choose a file to upload or the server has insufficient read/writes to upload this file.');
			return false;
		}elseif( is_array($_FILES[$media_name]['name']) && isset($index) ){
			$index = intval($index);
			$this->mediaName = (get_magic_quotes_gpc()) ? stripslashes($_FILES[$media_name]['name'][$index]) : $_FILES[$media_name]['name'][$index];
			$this->mediaType = $_FILES[$media_name]['type'][$index];
			$this->mediaSize = $_FILES[$media_name]['size'][$index];
			$this->mediaTmpName = $_FILES[$media_name]['tmp_name'][$index];
			$this->mediaError = !empty($_FILES[$media_name]['error'][$index]) ? $_FILES[$media_name]['error'][$index] : 0;
		}else{
			$media_name = @$_FILES[$media_name];
			$this->mediaName = (get_magic_quotes_gpc()) ? stripslashes($media_name['name']) : $media_name['name'];
			$this->mediaName = $media_name['name'];
			$this->mediaType = $media_name['type'];
			$this->mediaSize = $media_name['size'];
			$this->mediaTmpName = $media_name['tmp_name'];
			$this->mediaError = !empty($media_name['error']) ? $media_name['error'] : 0;
		}
		$this->dimension = getimagesize($this->mediaTmpName);
		$this->errors = array();

		if( !is_uploaded_file($this->mediaTmpName) ){
			if( $ele->getVar('ele_req') != 1 ){
				return false;
			}
			switch ($this->mediaError){
				case 0: // no error; possible file attack!
					$this->setErrors('There was a problem with your upload. Error: 0');
					break;
				case 1: // uploaded file exceeds the upload_max_filesize directive in php.ini
					//if( $this->noadmin_sizecheck)
					//{
					//	return true;
					//}
					$this->setErrors('The file you are trying to upload is too big. Error: 1');
					break;
				case 2: // uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the html form
					$this->setErrors('The file you are trying to upload is too big. Error: 2');
					break;
				case 3: // uploaded file was only partially uploaded
					$this->setErrors('The file you are trying upload was only partially uploaded. Error: 3');
					break;
				case 4: // no file was uploaded
					$this->setErrors('No file selected for upload. Error: 4');
					break;
				default: // a default error, just in case!  :)
					$this->setErrors('No file selected for upload. Error: 5');
					break;
			}
			return false;
		}

		if( intval($this->mediaSize) < 0 ){
			$this->setErrors('Invalid File Size');
			return false;
		}

		if( $this->mediaName == '' ){
			$this->setErrors('Filename Is Empty');
			return false;
		}

        if( preg_match('/\.(php|php4|php3|phtml|cgi|pl|py|asp)$/i', $this->mediaName) ){
            $this->setErrors('Filename rejected');
            return false;
		}

		if( $this->mediaTmpName == 'none' ){
			$this->setErrors('No file uploaded');
			return false;
		}

		if( !$this->checkMaxFileSize() ){
			$this->setErrors(sprintf('File Size: %u. Maximum Size Allowed: %u' , $this->mediaSize, $this->maxFileSize));
		}

		if( is_array($this->dimension) ){
			if( !$this->checkMaxWidth($this->dimension[0])){
				$this->setErrors(sprintf('File width: %u. Maximum width allowed: %u', $this->dimension[0], $this->maxWidth));
			}
			if( !$this->checkMaxHeight($this->dimension[1])){
				$this->setErrors(sprintf('File height: %u. Maximum height allowed: %u', $this->dimension[1], $this->maxHeight));
			}
		}elseif( $ele->getVar('ele_type') == 'uploadimg' ){
			$this->setErrors('Could not detect uploaded image size');
		}

		if( !$this->checkMimeType() ){
			$this->setErrors('MIME type not allowed: ' . $this->mediaType);
		}

		if( !$this->checkExtension() ){
			$this->setErrors('Extension not allowed: ' . $this->mediaName);
		}

		return count($this->errors) > 0 ? false : true;
	}

	/**
	 * Set the target filename
	 *
	 * @param string $value
	 */
	function setTargetFileName($value){
		$this->targetFileName = strval(trim($value));
	}

	/**
	 * Set the prefix
	 *
	 * @param string $value
	 */
	function setPrefix($value){
		$this->prefix = strval(trim($value));
	}

	/**
	 * Get the uploaded filename
	 *
	 * @return string
	 */
	function getMediaName(){
		return $this->mediaName;
	}

	/**
	 * Get the type of the uploaded file
	 *
	 * @return string
	 */
	function getMediaType(){
		return $this->mediaType;
	}

	/**
	 * Get the size of the uploaded file
	 *
	 * @return int
	 */
	function getMediaSize(){
		return $this->mediaSize;
	}

	/**
	 * Get the temporary name that the uploaded file was stored under
	 *
	 * @return string
	 */
	function getMediaTmpName(){
		return $this->mediaTmpName;
	}

	/**
	 * Get the saved filename
	 *
	 * @return string
	 */
	function getSavedFileName(){
		return $this->savedFileName;
	}

	/**
	 * Get the destination the file is saved to
	 *
	 * @return string
	 */
	function getSavedDestination(){
		return $this->savedDestination;
	}

	/**
	 * Check the file and copy it to the destination
	 *
	 * @return bool
	 */
	function upload($chmod = 0644){
		if( $this->uploadDir == '' ){
			$this->setErrors('Upload directory not set');
			return false;
		}

		if( !is_dir($this->uploadDir) ){
			$this->setErrors('Failed opening directory: ' . $this->uploadDir);
		}

		if( !is_writeable($this->uploadDir) ){
			$this->setErrors('Failed opening directory with write permission: ' . $this->uploadDir);
		}
		
		if( !is_dir($this->uploadDir) ){
			mkdir($this->uploadDir);
			chmod($this->uploadDir, 0777);
		}elseif( !is_writeable($this->uploadDir) ){
			chmod($this->uploadDir, 0777);
		}

		if( !$this->checkMaxFileSize() ){
			$this->setErrors(sprintf('File Size: %u. Maximum Size Allowed: %u' , $this->mediaSize, $this->maxFileSize));
		}

		if( is_array($this->dimension) ){
			if( !$this->checkMaxWidth($this->dimension[0])){
				$this->setErrors(sprintf('File width: %u. Maximum width allowed: %u', $this->dimension[0], $this->maxWidth));
			}
			if( !$this->checkMaxHeight($this->dimension[1])){
				$this->setErrors(sprintf('File height: %u. Maximum height allowed: %u', $this->dimension[1], $this->maxHeight));
			}
		}

		if( !$this->checkMimeType() ){
			$this->setErrors('MIME type not allowed: ' . $this->mediaType);
		}

		if( !$this->checkExtension() ){
			$this->setErrors('Extension not allowed: ' . $this->mediaName);
		}

		if( count($this->errors) > 0 ){
			return false;
		}

		if( !$this->_copyFile($chmod) ){
			$this->setErrors('Failed uploading file: ' . $this->mediaName);
		}

		if( count($this->errors) > 0 ){
			return false;
		}
		return true;
	}

	/**
	 * Copy the file to its destination
	 *
	 * @return bool
	 */
	function _copyFile($chmod){
		$matched = array();
		if( !preg_match("/\.([a-zA-Z0-9]+)$/", $this->mediaName, $matched) ){
			return false;
		}
		if( isset($this->targetFileName) ){
			$this->savedFileName = $this->targetFileName;
		}elseif( isset($this->prefix) ){
			$this->savedFileName = uniqid($this->prefix) . '.' . strtolower($matched[1]);
		}else{
			$this->savedFileName = strtolower($this->mediaName);
		}
		$this->savedFileName = preg_replace('!\s+!', '_', $this->savedFileName);
		$this->savedDestination = $this->uploadDir . $this->savedFileName;
		if( is_file($this->savedDestination) && !!is_dir($this->savedDestination) ){
			$this->setErrors('File ' . $this->mediaName . ' already exists on the server. Please rename this file and try again.<br />');
			return false;
		}
		if( !move_uploaded_file($this->mediaTmpName, $this->savedDestination) ){
			return false;
		}
		@chmod($this->savedDestination, $chmod);
		return true;
	}

	/**
	 * Is the file the right size?
	 *
	 * @return bool
	 */
	function checkMaxFileSize(){
		if( $this->noadmin_sizecheck ){
			return true;
		}
		if( $this->mediaSize > $this->maxFileSize ){
			return false;
		}
		return true;
	}

	/**
	 * Is the picture the right width?
	 *
	 * @return bool
	 */
	function checkMaxWidth($dimension){
		if( !isset($this->maxWidth) ){
			return true;
		}
		if( $dimension > $this->maxWidth ){
			return false;
		}
		return true;
	}

	/**
	 * Is the picture the right height?
	 *
	 * @return bool
	 */
	function checkMaxHeight($dimension){
		if( !isset($this->maxHeight) ){
			return true;
		}
		if( $dimension > $this->maxWidth ){
			return false;
		}
		return true;
	}

	/**
	 * Is the file the right Mime type
	 *
	 * (is there a right type of mime? ;-)
	 *
	 * @return bool
	 */
	function checkMimeType(){
		if( count($this->allowedMimeTypes) > 0 && !in_array($this->mediaType, $this->allowedMimeTypes) ){
			return false;
		}else{
			return true;
		}
	}

	/**
	 * Is the file the right extension
	 * 
	 * @return  bool
	 **/
	function checkExtension(){
		$ext = substr(strrchr($this->mediaName, '.'), 1 );
		if( !empty($this->allowedExtensions) && !in_array(strtolower($ext), $this->allowedExtensions) ){
			return false;
		}else{
			$this->ext = $ext;
			return true;
		}
	}

	/**
	 * Add an error
	 *
	 * @param string $error
	 */
	function setErrors($error){
		$this->errors[] = trim($error);
	}

	/**
	 * Get generated errors
	 *
	 * @param bool $ashtml Format using HTML?
	 * @return array |string	Array of array messages OR HTML string
	 */
	function &getErrors($ashtml = true){
		if( !$ashtml ){
			return $this->errors;
		}else{
			$ret = '';
			if( count($this->errors) > 0){
				$ret = '<h4>Errors Returned While Uploading</h4>';
				foreach ($this->errors as $error)
				{
					$ret .= $error . '<br />';
				}
			}
			return $ret;
		}
	}
}

?>