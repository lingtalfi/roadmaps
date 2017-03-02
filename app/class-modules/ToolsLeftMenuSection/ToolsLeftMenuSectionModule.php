<?php

namespace ToolsLeftMenuSection;


use Layout\LayoutServices;
use Layout\LayoutHelper;
use Privilege\Privilege;

class ToolsLeftMenuSectionModule
{
    public static function displayLeftMenuBlocks()
    {
        ?>
        <section class="section-block tools">
        <?php LayoutHelper::displayLeftMenuExpandableTitle(__("Tools")); ?>
        <ul class="linkslist">
            <?php ToolsLeftMenuSectionServices::displayToolsLeftMenuLinks(); ?>
        </ul>
        </section><?php

    }
}