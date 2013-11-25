<?php namespace MX\RestManager\Models;

use MX\Misc\Cases;

/**
 * @brief       Request
 *
 * @details     Request model for RestManager
 */

class Request extends Base
{
    // ---------- Attributes ---------- //

    /**
     * Enforced properties
     */
    protected $m_enforcedProperties = array(
        'baseUrl',
        'path',
        'authUsername',
        'authPassword',
        'method',
        'parameters',
        'body',
        'headers',
    );

    // ---------- /Attributes ---------- //

    // ---------- Methods ---------- //

    /**
     * Set property(ies)
     *
     * Setting properties with this method allow you to chain them
     *
     * @param[in]   $name   Name of the internal property (lowerCamelCased)
     * @param[in]   $val    Value of the internal property
     * @return  \MX\Models\Request  Return this model
     */
    public function set($key, $val)
    {
        if (array_key_exists($this->m_properties, $key)) {
            $this->m_properties[$key] = $val;
        } else {
            throw new \Exception("The property \"$key\" doesn't exists");
        }

        return ($this);
    }

    /**
     * Set permanent headers
     *
     * Set the permament headers for successive requests, chainable
     *
     * @param[in]   $name   Name of the permanent header
     * @param[in]   $val    Value of the header
     * @return  \MX\Models\Request  Return this model
     */
    public function setHeader($key, $val)
    {
        $headerName = CasesTransform::from($key)->toCamelCase(true);

        if (array_key_exists($this->m_permanentHeaders, $headerName)) {
            $this->m_permanentHeaders[$headerName] = $val;
        } else {
            throw new \Exception("The property \"$key\" doesn't exist");
        }

        return ($this);
    }

    /**
     * Get permanent headers
     *
     * @return  array|object  Permanent headers
     */
    public function getHeaders()
    {
        return ($this->m_permanentHeaders);
    }

    // ---------- /Methods ---------- //

    // ---------- Magic methods ---------- //

    /**
     * __set
     */
    public function __set($key, $val)
    {
        $this->set($key, $val);
    }

    /**
     * __get
     */
    public function __get($key)
    {
        $method = 'get'.ucfirst(strtolower($key));

        if (array_key_exists($this->m_properties, $key)) {
            return ($this->m_properties[$key]);
        } elseif (is_callable(array($this, $method))) {
            return ($this->$method());
        } else {
            throw new \Exception("The property \"$key\" doesn't exist");
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
