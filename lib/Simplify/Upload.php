<?php

/**
 * SimplifyPHP Framework
 *
 * This file is part of SimplifyPHP Framework.
 *
 * SimplifyPHP Framework is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * SimplifyPHP Framework is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @author Rodrigo Rutkoski Rodrigues, <rutkoski@gmail.com>
 * @copyright Copyright 2008 Rodrigo Rutkoski Rodrigues
 */

/**
 * File Upload Component
 *
 * @example
 *    $upload = new UploadComponent();
 *
 *    // optional
 *    $upload->set('files_dir', s::config()->get('files_dir'));
 *
 *    try {
 *      $upload->upload('my_file');
 *    } catch (UploadException $e) {
 *      echo $upload->getError();
 *    } catch (ValidationException $e) {
 *      echo $upload->getError();
 *    }
 *
 *    echo $upload->getUploadedPath();
 *
 * @author Rodrigo Rutkoski Rodrigues, <rutkoski@gmail.com>
 * @package simplify_components
 */
class Simplify_Upload
{

  /**
   *
   */
  protected $file;

  /**
   *
   */
  protected $error;

  /**
   *
   */
  protected $uploadedPath;

  /**
   *
   */
  protected $mimeType;

  /**
   *
   */
  protected $fileSize;

  /**
   *
   */
  public $filename = null;

  /**
   *
   */
  public $useDateBasedPath = false;

  /**
   *
   */
  public $hashFilename = false;

  /**
   *
   */
  public $replaceIfExists = false;

  /**
   *
   */
  public $uploadPath = null;

  /**
   *
   */
  public $extensions;

  /**
   *
   */
  public $fileMimeType;

  /**
   *
   */
  public $maxFileSize;

  public function __construct()
  {
    //
  }

  public function getUploadPath()
  {
    if (empty($this->uploadPath)) {
      $this->uploadPath = s::config()->get('files_path');
    }
    
    return $this->uploadPath;
  }

  /**
   * Get upload error, if any.
   *
   * @return mixed error string or boolean false if there are no errors
   */
  public function getError()
  {
    return $this->error;
  }

  /**
   * Get the path of the uploaded file.
   *
   * @return string
   */
  public function getUploadedPath()
  {
    if (empty($this->uploadedPath))
      throw new Simplify_UploadException('No file has been uploaded');
    
    return $this->getUploadPath() . $this->uploadedPath;
  }

  /**
   * Get the uploaded file size.
   *
   * @return int
   */
  public function getFileSize()
  {
    /*if (! $this->uploaded())
      throw new UploadException('No file has been uploaded');*/
    if (empty($this->fileSize)) {
      $this->fileSize = $this->file['size'];
    }
    
    return $this->fileSize;
  }

  /**
   * Get the uploaded file mime type.
   *
   * @return string
   */
  public function getMimeType()
  {
    if (empty($this->mimeType)) {
      if (isset($this->file['type'])) {
        $this->mimeType = $this->file['type'];
      }
    }
    
    return $this->mimeType;
  }

  /**
   * Process a file upload.
   *
   * @param string $name field name
   * @param int $index file index (for multiple file uploads)
   * @return void
   */
  public function upload($name, $index = null)
  {
    $this->file = null;
    $this->error = false;
    $this->mimeType = null;
    $this->fileSize = null;
    $this->uploadedPath = null;
    
    $this->validateUpload($name, $index);
    $this->validateFile();
    $this->moveFile();
  }

  /**
   * Check if file has been sent to the server.
   *
   * @return boolean
   */
  protected function uploaded()
  {
    return empty($this->file) || !empty($this->error);
  }

  /**
   * Check if file has been uploaded.
   *
   * @return boolean
   */
  protected function moved()
  {
    return $this->uploaded() && !empty($this->uploadedPath);
  }

  /**
   *
   */
  protected function validateUpload($name, $index = null)
  {
    if (is_null($index)) {
      if (!isset($_FILES[$name])) {
        $this->error = sprintf(__('No such index: $_FILES[%s]'), $name);
        throw new Exception($this->error);
      }
      
      $this->file = $_FILES[$name];
    }
    
    else {
      if (!isset($_FILES[$name]['name'][$index])) {
        $this->error = sprintf(__('No such index: $_FILES[%s][%s]'), $name, $index);
        throw new Exception('File not found');
      }
      
      $this->file = array('name' => $_FILES[$name]['name'][$index], 'type' => $_FILES[$name]['type'][$index], 'tmp_name' => $_FILES[$name]['tmp_name'][$index], 'error' => $_FILES[$name]['error'][$index], 'size' => $_FILES[$name]['size'][$index]);
    }
    
    $this->getPHPErrorMessage();
  }

  /**
   *
   * @throws UploadException
   */
  protected function moveFile()
  {
    $path = $this->getUploadPath();
    
    if (!sy_path_is_absolute($path)) {
      $path = s::config()->get('www_dir') . $path;
    }
    
    $filename = empty($this->filename) ? $this->file['name'] : $this->filename;
    
    $subpath = '';
    
    if ($this->useDateBasedPath) {
      $subpath = $this->findDateBasedPath($path);
    }
    
    $subpath .= '/';
    
    if (!is_writable($path . $subpath)) {
      $this->error = __('Upload path does not exist or is not writable: <b>' . sy_fix_path($path . $subpath) . '</b>');
      throw new Simplify_UploadException($this->error);
    }
    
    if ($this->hashFilename) {
      $filename = $this->genHashFilename($path . $subpath, '/' . $filename);
    }
    elseif ($this->replaceIfExists === false) {
      $filename = $this->findUniqueFilename($path . $subpath, '/' . $filename);
    }
    
    if (!@move_uploaded_file($this->file['tmp_name'], $path . $subpath . $filename)) {
      $this->error = __('Could not move uploaded file');
      throw new Simplify_UploadException($this->error);
    }
    
    chmod($path . $subpath . $filename, 0644);
    
    $this->uploadedPath = $subpath . $filename;
  }

  /**
   *
   */
  protected function validateFile()
  {
    // validate extension
    $this->mimeType = $this->file['type'];
    
    if ($this->extensions) {
      $type = implode('|', (array) $this->extensions);
      
      if (!preg_match('/\.(' . $type . ')$/i', $this->file['name'])) {
        $this->error = __('Invalid file type');
        throw new Simplify_Validation_ValidationException($this->error);
      }
    }
    
    // validate mime type
    if ($this->fileMimeType) {
      if (!preg_match('#' . $this->fileMimeType . '#i', $this->getMimeType())) {
        $this->error = sprintf(__('Invalid mime type. Required: %s Found: %s', $type, $this->getMimeType()));
        throw new Simplify_Validation_ValidationException($this->error);
      }
    }
    
    // validate file size
    if ($this->maxFileSize) {
      if ($this->getFileSize() > $this->maxFileSize) {
        $this->error = __('Maximum file size exceded');
        throw new Simplify_Validation_ValidationException($this->error);
      }
    }
  }

  /**
   *
   */
  protected function getPHPErrorMessage()
  {
    switch ($this->file['error']) {
      case UPLOAD_ERR_INI_SIZE :
        $this->error = __('The uploaded file exceeds the upload_max_filesize directive in php.ini.');
        break;
      case UPLOAD_ERR_FORM_SIZE :
        $this->error = __('The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form.');
        break;
      case UPLOAD_ERR_PARTIAL :
        $this->error = __('The uploaded file was only partially uploaded.');
        break;
      case UPLOAD_ERR_NO_FILE :
        $this->error = __('No file was uploaded.');
        break;
      case UPLOAD_ERR_NO_TMP_DIR :
        $this->error = __('Missing a temporary folder. Introduced in PHP 4.3.10 and PHP 5.0.3.');
        break;
      //case UPLOAD_ERR_CANT_WRITE: $this->error = __('Failed to write file to disk. Introduced in PHP 5.1.0.'); break;
      //case UPLOAD_ERR_EXTENSION: $this->error = __('File upload stopped by extension. Introduced in PHP 5.2.0.'); break;
      default :
        $this->error = false;
    }
    
    if (!empty($this->error)) {
      throw new Simplify_UploadException($this->error, $this->file['error']);
    }
  }

  /**
   *
   */
  protected function findUniqueFilename($base, $filename)
  {
    $suffixPrecision = $this->params->get('unique_filename_zeros', 4);
    
    $info = pathinfo($base . $filename);
    $this->set('info', $info);
    $dir = $info['dirname'];
    if (!isset($info['filename'])) {
      $info['filename'] = substr($info['basename'], 0, strpos($info['basename'], '.' . $info['extension']));
    }
    $basename = strtolower($info['basename']);
    
    $filename = $dir . '/' . $basename;
    
    if (!@file_exists($filename)) {
      return $basename;
    }
    
    if (isset($info['extension'])) {
      $name = strtolower($info['filename']);
      $ext = strtolower($info['extension']);
    }
    else {
      $p = strrpos($basename, '.');
      $name = substr($basename, 0, $p);
      $ext = substr($basename, $p + 1);
    }
    
    $suffix = 0;
    if (preg_match('/^(.*)_([0-9]{' . $suffixPrecision . '})$/', $name, $o)) {
      $name = $o[1];
      $suffix = (int) ($o[2]);
    }
    
    while (@file_exists($filename)) {
      $suffix++;
      $filename = $dir . '/' . $name . '_' . sprintf('%0' . $suffixPrecision . 'u', $suffix) . '.' . $ext;
    }
    
    return $name . '_' . sprintf('%0' . $suffixPrecision . 'u', $suffix) . '.' . $ext;
  }

  /**
   *
   */
  protected function findDateBasedPath($base)
  {
    $Y = date('Y');
    $d = date('d');
    $m = date('m');
    
    $path = "/$Y/$m/$d";
    
    if (!@file_exists($base . $path)) {
      if (!@mkdir($base . $path, 0644, true)) {
        $this->error = __('Date based path could not be created.');
        throw new Simplify_UploadException($this->error);
      }
    }
    
    return $path;
  }

  /**
   *
   */
  protected function genHashFilename($base, $filename)
  {
    $extension = pathinfo($base . $filename, PATHINFO_EXTENSION);
    
    do {
      $path = sy_fix_path($base . $filename);
      $hash = md5($path . mktime());
      $path = sy_fix_path($base . '/' . $hash . '.' . $extension);
    }
    while (file_exists($path));
    
    return $hash . '.' . $extension;
  }

}