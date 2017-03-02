<?php


namespace QuickTable;


use Bat\StringTool;

class QuickTable
{
    public static function printItem(array $item, array $options = [])
    {
        $tableArgs = [];
        if (array_key_exists('class', $options)) {
            $tableArgs['class'] = $options['class'];
        }
        ?>
        <table<?php echo StringTool::htmlAttributes($tableArgs); ?>>
            <?php foreach ($item as $k => $v): ?>
                <tr>
                    <td><?php echo $k ?></td>
                    <td><?php echo $v ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
        <?php
    }
}