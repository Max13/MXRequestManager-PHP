<?php namespace MX\RestManager\Models;

/**
 * @brief       Headers
 *
 * @details     Headers model for RestManager
 */

class Headers extends Base
{
    // ---------- Attributes ---------- //

    /**
     * Enforced properties
     */
    protected $m_enforcedProperties = array(
        'accept',
        'acceptEncoding',
        'acceptLanguage',
        'contentType',
        'userAgent',
    );

    // ---------- /Attributes ---------- //
}