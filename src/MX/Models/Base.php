<?php namespace MX\RestManager\Models;

/**
 * @brief       Base Model
 *
 * @details     Absctract Base model
 */

abstract class Base
{
    // ---------- Attributes ---------- //

    /**
     * Internal properties
     *
     * @var array Internal model properties
     */
    protected $m_properties = array();

    /**
     * Enforced properties
     *
     * @var array Enforced properties, empty means anything can be set in properties
     */
    protected $m_enforcedProperties = array();

    // ---------- /Attributes ---------- //

    // ---------- Constructors ---------- //

    // public function __construct()
    // {
    //     foreach ($this->m_enforcedProperties as $val) {
    //         $this->m_properties[$val] = null;
    //     }
    // }

    // ---------- /Constructors ---------- //

    // ---------- Magic methods ---------- //

    /**
     * __set
     */
    public function __set($key, $val)
    {
        if (empty($m_enforcedProperties)                        // Anything can be set
            || in_array($key, $this->m_enforcedProperties)) {   // Check if can be set
            $this->m_properties[$key] = $val;
        } else {
            $this->$key = $val;
        }
    }

    /**
     * __get
     */
    public function __get($key)
    {
        // $method = 'get'.ucfirst(strtolower($key));

        // if (array_key_exists($this->m_properties, $key)) {
        //     return ($this->m_properties[$key]);
        // } elseif (is_callable(array($this, $method))) {
        //     return ($this->$method());
        // } else {
        //     throw new \Exception("The property \"$key\" doesn't exist");
        // }

        if ((empty($m_enforcedProperties)                       // Anything can be set
             || in_array($key, $this->m_enforcedProperties))    // Check if can be set
            && array_key_exists($this->m_properties, $key)) {   // Check if exists
            return ($this->m_properties[$key]);
        } else {
            return ($this->$key);
        }
    }

    /**
     * __isset
     */
    public function __isset($key)
    {
        return (isset($this->m_properties[$key]));
    }

    /**
     * __unset
     */
    public function __unset($key)
    {
        unset($this->m_properties[$key]);
    }

    // ---------- /Magic methods ---------- //
}
