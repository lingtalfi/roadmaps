<?php


namespace Layout\Body\Key2ValueForm;



use AssetsList\AssetsList;

class Key2ValueForm
{


    public static function demo()
    {
        AssetsList::css('/style/key2value-form.css');
        ?>
        <form action="" method="post" class="key2value-form">
            <table>
                <tr>
                    <th>Keys</th>
                    <th>Values</th>
                </tr>
                <?php for ($i = 0; $i < 5; $i++): ?>
                    <tr>
                        <td>crud</td>
                        <td><input type="text" name="links[crud]" value="http://crud.com"></td>
                    </tr>
                <?php endfor; ?>
            </table>
        </form>
        <?php
    }

}