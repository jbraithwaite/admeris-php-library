<?php namespace Admeris;

use Admeris\Base;

class Schedule extends Base {

  private $scheduleType;
  private $intervalLength;

  public function __construct($type, $intervalLength)
  {

    switch (strtolower($type)) {
      case 'month':
        $this->scheduleType = 0;
        break;
      case 'week':
        $this->scheduleType = 1;
        break;
      case 'day':
        $this->scheduleType = 2;
        break;
      default:
        throw new Exception("Not a valid schedule type. Use either 'month', 'week' or 'day'", 1);
        break;
    }

    $this->intervalLength = $intervalLength;
  }

  function _get_scheduleType()
  {
    return $this->scheduleType;
  }

  function _get_intervalLength()
  {
    return $this->intervalLength;
  }
}