<?php


namespace Layout\Body\Tabby;


use Icons\Icons;

class TabbyTabs
{

    private $leftTabs;
    private $rightTabs;
    private $showEmptyBadges;

    private function __construct()
    {
        $this->leftTabs = [];
        $this->rightTabs = [];
        $this->showEmptyBadges = false;
    }

    public static function create()
    {
        return new self();
    }

    public function addLeftTab($label, $url)
    {
        $ret = new TabbyTab();
        $ret->label($label)->url($url);
        $this->leftTabs[] = $ret;
        return $ret;
    }

    public function addRightTab($label, $url)
    {
        $ret = new TabbyTab();
        $ret->label($label)->url($url);
        $this->rightTabs[] = $ret;
        return $ret;
    }

    public function display()
    {
        ?>
        <div class="tabs">
            <?php
            foreach ($this->leftTabs as $tab) {
                $this->displayTab($tab);
            }
            ?>
            <div class="tab spacer"></div>
            <?php
            foreach ($this->rightTabs as $tab) {
                $this->displayTab($tab);
            }
            ?>
        </div>
        <?php
    }


    //--------------------------------------------
    //
    //--------------------------------------------
    private function displayTab(TabbyTab $tab)
    {
        ?>
        <div class="tab">
            <a href="<?php echo htmlspecialchars($tab->getUrl()) ?>">
                <?php
                if (null !== ($icon = $tab->getIcon())) {
                    Icons::printIcon($icon);
                }
                ?>
                <span><?php echo $tab->getLabel(); ?></span>
                <?php

                if (null !== ($badge = $tab->getBadge())) {
                    if (
                        true === $this->showEmptyBadges ||
                        (false === $this->showEmptyBadges && (int)$badge > 0)

                    ) {

                        $c = '';
                        $type = $tab->getBadgeType();
                        if (null !== $type) {
                            $c = "badge-$type";
                        }
                        ?>
                        <span class="badge <?php echo $c; ?>"><?php echo $badge; ?></span>
                        <?php
                    }
                }
                ?>
            </a>
        </div>
        <?php
    }

}