<?php


namespace Layout\Body\Tabby;


class TabbyTab
{
    private $_icon;
    private $_label;
    private $_badge;
    private $_badgeType;
    private $_url;


    public function __construct()
    {
        $this->_icon = null;
        $this->_label = null;
        $this->_badge = null;
        $this->_badgeType = null;
        $this->_url = null;
    }


    public function icon($icon)
    {
        $this->_icon = $icon;
        return $this;
    }

    public function label($label)
    {
        $this->_label = $label;
        return $this;
    }

    /**
     * type:
     *  - null  (neutral)
     *  - success
     *  - error
     */
    public function badge($badge, $type = null)
    {
        $this->_badge = $badge;
        $this->_badgeType = $type;
        return $this;
    }

    public function url($url)
    {
        $this->_url = $url;
        return $this;
    }

    //--------------------------------------------
    //
    //--------------------------------------------
    public function getIcon()
    {
        return $this->_icon;
    }

    public function getLabel()
    {
        return $this->_label;
    }

    public function getBadge()
    {
        return $this->_badge;
    }

    public function getBadgeType()
    {
        return $this->_badgeType;
    }

    public function getUrl()
    {
        return $this->_url;
    }


}