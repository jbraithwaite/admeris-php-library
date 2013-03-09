<?php namespace Admeris;

use Admeris\Base;

class AvsResponse extends Base {

  private $avsResponseCode;
  private $streetMatched;
  private $zipMatched;
  private $zipType;
  private $avsErrorCode;
  private $avsErrorMessage;

  function _get_avsErrorCode() {return $this->avsErrorCode;}
  function _get_avsErrorMessage() {return $this->avsErrorMessage;}
  function _get_avsResponseCode() {return $this->avsResponseCode;}
  function _get_zipType() {return $this->zipType;}

  function _set_avsResponseCode($data) { $this->avsResponseCode = $data; }
  function _set_streetMatched($data) { $this->streetMatched = $data; }
  function _set_zipMatched($data) { $this->zipMatched = $data; }
  function _set_zipType($data) { $this->zipType = $data; }
  function _set_avsErrorCode($data) { $this->avsErrorCode = $data; }
  function _set_avsErrorMessage($data) { $this->avsErrorMessage = $data; }

  function isAvsPerformed()
  {
    return $this->avsErrorMessage == null && $this->avsErrorCode ==null;
  }

  function isStreetFormatValid()
  {
    return $this->streetMatched != null;
  }

  function isStreetFormatValidAndMatched()
  {
    return $this->isStreetFormatValid() == TRUE && $this->streetMatched == TRUE;
  }

  function isZipFormatValid()
  {
    return $this->zipMatched != null;
  }

  function isZipFormatValidAndMatched()
  {
    return $this->isZipFormatValid() == TRUE && $this->zipMatched == TRUE;
  }
}