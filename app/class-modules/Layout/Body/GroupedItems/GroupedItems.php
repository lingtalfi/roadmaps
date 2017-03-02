<?php


namespace Layout\Body\GroupedItems;


use AssetsList\AssetsList;

class GroupedItems
{

    public static function demo()
    {
        AssetsList::css('/style/grouped-items.css');
        ?>
        <div class="body-top">
            <div class="box">
                <label>
                    <input type="checkbox"> alphabetical order
                </label>
            </div>
            <div class="box">
                <label>
                    <input type="checkbox"> grouped by files
                </label>
            </div>
        </div>
        <div class="body-content">
            <div class="grouped-items bluesy">
                <h4>core/privilege/list-of-all-privileges.md</h4>
                <table>
                    <tr>
                        <td>check the following</td>
                    </tr>
                    <tr>
                        <td>tools to list all privileges</td>
                    </tr>
                </table>
                <h4>modules/crud-module/crud-page.md</h4>
                <table>
                    <tr>
                        <td>images of list and forms views</td>
                    </tr>
                </table>
                <h4>modules/quickdoc-module.md</h4>
                <table>
                    <tr>
                        <td>anything notation</td>
                    </tr>
                </table>
                <h4>modules/sqltools-module/execute-sql-page.md</h4>
                <table>
                    <tr>
                        <td>check link</td>
                    </tr>
                </table>
                <h4>todolist.md</h4>
                <table>
                    <tr>
                        <td>tool to create new module?</td>
                    </tr>
                    <tr>
                        <td>menu links</td>
                    </tr>
                </table>
            </div>


        <?php
    }
}