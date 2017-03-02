<?php


namespace Util;


class ArrayRenderer
{

    private $values;

    public function __construct()
    {
        $this->values = [];
    }

    public static function create()
    {
        return new static();
    }

    public function setValues(array $values)
    {
        $this->values = $values;
        return $this;
    }

    public function render()
    {
        ?>
        <table class="array-renderer-table">
            <?php foreach ($this->values as $k => $v):

                ?>
                <tr>
                    <td><?php echo $k; ?></td>
                    <td><?php echo $v; ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
        <?php
    }


    //------------------------------------------------------------------------------/
    //
    //------------------------------------------------------------------------------/
    protected function transformKeysAndValues()
    {

    }
}