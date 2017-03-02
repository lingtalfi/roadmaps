<?php


namespace Util;


class RowsRenderer
{

    private $values;
    private $showKeys;

    public function __construct()
    {
        $this->values = [];
        $this->showKeys = false;
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

    public function setShowKeys($b)
    {
        $this->showKeys = $b;
        return $this;
    }

    public function render()
    {
        ?>
        <table class="rows-renderer-table">
            <?php foreach ($this->values as $k => $item):

                ?>
                <tr>
                    <?php foreach ($item as $k => $v): ?>
                        <?php if (true === $this->showKeys): ?>
                            <td><?php echo $k; ?></td>
                        <?php endif; ?>
                        <td><?php echo $v; ?></td>
                    <?php endforeach; ?>
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