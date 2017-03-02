<?php


namespace Layout\Body\GroupedItems;


use AssetsList\AssetsList;

class GroupedItemsLayout
{
    private $textAlphabeticalOrder;
    private $textGroupedByFiles;
    private $textAllItems;
    private $theme;
    private $_alpha;
    private $_groupByFiles;
    private $_groups;

    public function __construct()
    {
        $this->textAlphabeticalOrder = 'alphabetical order';
        $this->textGroupedByFiles = 'grouped';
        $this->textAllItems= 'All items';
        $this->theme = 'bluesy';
        $this->_alpha = true;
        $this->_groupByFiles = true;
    }


       public function alpha($alpha)
    {
        $this->_alpha = (bool)$alpha;
        return $this;
    }

    public function groupByFiles($groupByFiles)
    {
        $this->_groupByFiles = (bool)$groupByFiles;
        return $this;
    }


    public function texts(array $texts)
    {
        if(array_key_exists('alpha', $texts)){
            $this->textAlphabeticalOrder = $texts['alpha'];
        }
        if(array_key_exists('group', $texts)){
            $this->textGroupedByFiles = $texts['group'];
        }
        if(array_key_exists('all', $texts)){
            $this->textAllItems = $texts['all'];
        }
        return $this;
    }




    public function groups(array $groups){
        $this->_groups = $groups;
        return $this;
    }


    public function onPreferencesChange($func)
    {
        if (array_key_exists("gi-alpha", $_GET)) {
            $alpha = (array_key_exists('gi-alpha', $_GET)) ? (bool)$_GET['gi-alpha'] : $this->_alpha;
            $group = (array_key_exists('gi-group', $_GET)) ? (bool)$_GET['gi-group'] : $this->_groupByFiles;
            $newPrefs = [
                'alpha' => $alpha,
                'group' => $group,
            ];
            call_user_func($func, $newPrefs);
        }
    }

    public function display()
    {
        AssetsList::css('/style/grouped-items.css');
        $n = rand(0, 10000);
        $id = 'groupeditems-' . $n;
        $alphaId = $id . '-a';
        $groupId = $id . '-g';
        $alphaInputId = $id . '-ia';
        $groupInputId = $id . '-ig';

        $alpha = (array_key_exists('gi-alpha', $_GET)) ? (bool)$_GET['gi-alpha'] : $this->_alpha;
        $group = (array_key_exists('gi-group', $_GET)) ? (bool)$_GET['gi-group'] : $this->_groupByFiles;
        $checked = 'checked="checked"';

        ?>
        <div class="body-top">
            <form method="get" action="" id="<?php echo $id; ?>">
                <div class="box">
                    <label>
                        <input <?php echo (true === $group) ? $checked : ''; ?> type="checkbox"
                                                                                id="<?php echo $groupId; ?>">
                        <input type="hidden" name="gi-group" value="<?php echo (int)$group; ?>"
                               id="<?php echo $groupInputId; ?>">
                        <span><?php echo $this->textGroupedByFiles; ?></span>
                    </label>
                </div>
                <div class="box">
                    <label>
                        <input <?php echo (true === $alpha) ? $checked : ''; ?> type="checkbox"
                                                                                id="<?php echo $alphaId; ?>">
                        <input type="hidden" name="gi-alpha" value="<?php echo (int)$alpha; ?>"
                               id="<?php echo $alphaInputId; ?>">
                        <span><?php echo $this->textAlphabeticalOrder; ?></span>
                    </label>
                </div>
                <div style="display: none">
                    <?php
                    $getLeftOver = array_diff(array_keys($_GET), ['gi-alpha', 'gi-group']);
                    foreach ($getLeftOver as $name) {
                        $value = $_GET[$name];
                        ?>
                        <input type="hidden"
                               name="<?php echo htmlspecialchars($name); ?>"
                               value="<?php echo htmlspecialchars($value); ?>"
                        />
                        <?php
                    }
                    ?>
                </div>
            </form>
        </div>
          <script>
            var form = document.getElementById("<?php echo $id; ?>");
            var alphaCheckbox = document.getElementById("<?php echo $alphaId; ?>");
            var groupCheckbox = document.getElementById("<?php echo $groupId; ?>");
            var alphaInput = document.getElementById("<?php echo $alphaInputId; ?>");
            var groupInput = document.getElementById("<?php echo $groupInputId; ?>");

            alphaCheckbox.addEventListener('change', function () {
                var isNowChecked = alphaCheckbox.checked;
                if (false === isNowChecked) {
                    alphaInput.value = "0";
                }
                else {
                    alphaInput.value = "1";
                }
                form.submit();
            });
            groupCheckbox.addEventListener('change', function () {
                var isNowChecked = groupCheckbox.checked;
                if (false === isNowChecked) {
                    groupInput.value = "0";
                }
                else {
                    groupInput.value = "1";
                }
                form.submit();
            });
        </script>

        <div class="body-content">
        <div class="grouped-items <?php echo $this->theme; ?>">
            <?php

            if(true === $group){
                $groups = $this->_groups;
                if(true === $alpha){
                    foreach($groups as &$items){
                        sort($items);
                    }
                }
                $this->displayGroupedItems($groups);
            }
            else{
                $items = [];
                foreach($this->_groups as $_items){
                    foreach($_items as $item){
                        $items[] = $item;
                    }
                }
                if(true === $alpha){
                    sort($items);
                }
                $this->displayUngroupedItems($items);
            }
            ?>
        </div>
        <?php
    }

    //------------------------------------------------------------------------------/
    //
    //------------------------------------------------------------------------------/
    private function displayGroupedItems(array $groups)
    {
        foreach ($groups as $label => $group):
            ?>
                <h4><?php echo $label; ?></h4>
                <table>
                <?php foreach ($group as $value): ?>
                    <tr>
                        <td><?php echo $value; ?></td>
                    </tr>
                    <?php endforeach; ?>
                </table>
            <?php
        endforeach;
    }

    private function displayUngroupedItems(array $items)
    {
        ?>
        <?php if(null !== $this->textAllItems): ?>
        <h4><?php echo $this->textAllItems; ?></h4>
        <?php endif; ?>
        <table>
        <?php foreach ($items as $value): ?>
            <tr>
                <td><?php echo $value; ?></td>
            </tr>
            <?php endforeach; ?>
        </table>
        <?php
    }

}