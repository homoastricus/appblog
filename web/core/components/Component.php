<?php

namespace App\Core\Components;
class Component
{
    /**
     * Settings for this Component
     *
     * @var array
     */
    public array $settings = array();

    /**
     * Other Components this component uses.
     *
     * @var array
     */
    public array $components = array();


    /**
     * Constructor
     *
     * @param array $settings Array of configuration settings.
     */
    public function __construct(array $settings = array())
    {
        $this->settings = $settings;
    }

}
