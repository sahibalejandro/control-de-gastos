<?php
/**
 * QuarkPHP Framework
 * Copyright (C) 2012 Sahib Alejandro Jaramillo Leo
 *
 * @link http://quarkphp.com
 * @license GNU General Public License (http://www.gnu.org/licenses/gpl.html)
 */

class TwitterAPIException extends Exception
{
  /**
   * When the direct response after curl_exec is an XML with error.
   * @var int
   */
  const ERR_RESPONSE = 1;
  
  /**
   * When a request (json) comes with error message.
   * @var int
   */
  const ERR_REQUEST  = 2;
}
