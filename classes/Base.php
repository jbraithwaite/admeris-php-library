<?php namespace Admeris;

use Exception;

abstract class Base
{
  /**
   * Magic getter.
   *
   * @access public
   * @param string $name
   * @return mixed
   */
  public function __get($name)
  {
    $method = "_get_". $name;

    if (method_exists($this, $method))
    {
      return $this->$method();
    }

    throw new Exception("Getter for the field {$name} doesn't exist");
  }

  /**
   * Magic setter.
   *
   * @access public
   * @param string $name
   * @return mixed
   */
  public function __set($name, $value)
  {
    $method = "_set_". $name;

    if (method_exists($this, $method))
    {
      return $this->$method($value);
    }

    throw new Exception("Setter for the field {$name} doesn't exist");
  }

  public function get_param($key)
  {
    if (isset($this->params) && is_array($this->params) && array_key_exists($key, $this->params))
    {
      return $this->params[$key];
    }

    return NULL;
  }
}