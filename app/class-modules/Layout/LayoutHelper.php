<?php

namespace Layout;

use Icons\Icons;

class LayoutHelper
{
    public static function displayLeftMenuExpandableTitle($label)
    {
        ?>
        <h3 class="expander">
            <span class="expander-label"><?php echo $label; ?></span>
            <span class="icons">
                        <span class="expand-more"><?php Icons::printIcon('expand-more'); ?></span>
                        <span class="expand-less"><?php Icons::printIcon('expand-less'); ?></span>
                    </span>
        </h3>
        <?php
    }
}