<?php


namespace Layout\Body\Tabby;


use AssetsList\AssetsList;
use Icons\Icons;
use Layout\Body\GroupedItems\GroupedItems;
use Layout\Body\Key2ValueForm\Key2ValueForm;

class Tabby
{

    public static function demo()
    {
        AssetsList::css('/style/tabby.css');
        ?>
        <div class="tabby">
            <div class="tabs">
                <div class="tab">
                    <a href="#">
                        <?php Icons::printIcon("link"); ?>
                        <span>Link</span>
                    </a>
                </div>
                <div class="tab">
                    <a href="#">
                        <?php Icons::printIcon("image"); ?>
                        <span>Images</span>
                        <span class="badge badge-error">6</span>
                    </a>
                </div>
                <div class="tab">
                    <a href="#">
                        <span>Others</span>
                        <span class="badge badge">new</span>
                    </a>
                </div>
                <div class="tab">
                    <a href="#">
                        <span>Fun</span>
                        <span class="badge badge-success">4</span>
                    </a>
                </div>
                <div class="tab spacer"></div>
                <div class="tab">
                    <a href="#">
                        <?php Icons::printIcon("settings"); ?>
                        <span>Config</span>
                    </a>
                </div>
            </div>
            <div class="body">
                <div class="body-top">
                    <div class="box">
                        <select>
                            <option value="0">Show unresolved only</option>
                            <option value="1">Show resolved only</option>
                            <option value="2">Show all</option>
                        </select>
                    </div>
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
                    <h3>I'm the tabby content</h3>
                    <p>
                        Lorem ipsum dolor sit amet, consectetur adipisicing elit. Accusamus consequuntur cupiditate
                        deserunt eaque earum facilis iure maiores minima, minus nam nemo, numquam praesentium quae qui
                        rem repellendus similique sunt ut?
                    </p>
                    <h3>I'm another tabby content</h3>
                    <p>
                        Lorem ipsum dolor sit amet, consectetur adipisicing elit. Accusamus consequuntur cupiditate
                        deserunt eaque earum facilis iure maiores minima, minus nam nemo, numquam praesentium quae qui
                        rem repellendus similique sunt ut?
                    </p>
                    <?php
                    Key2ValueForm::demo();
                    ?>
                </div>
            </div>
        </div>
        <?php
    }


    public static function demo2()
    {
        AssetsList::css('/style/tabby.css');
        ?>
        <div class="tabby">
            <div class="tabs">
                <div class="tab">
                    <a href="#">
                        <?php Icons::printIcon("link"); ?>
                        <span>Link</span>
                    </a>
                </div>
                <div class="tab">
                    <a href="#">
                        <?php Icons::printIcon("image"); ?>
                        <span>Images</span>
                        <span class="badge badge-error">6</span>
                    </a>
                </div>
                <div class="tab">
                    <a href="#">
                        <span>Others</span>
                        <span class="badge badge">new</span>
                    </a>
                </div>
                <div class="tab">
                    <a href="#">
                        <span>Fun</span>
                        <span class="badge badge-success">4</span>
                    </a>
                </div>
                <div class="tab spacer"></div>
                <div class="tab">
                    <a href="#">
                        <?php Icons::printIcon("settings"); ?>
                        <span>Config</span>
                    </a>
                </div>
            </div>
            <div class="body">
                <?php GroupedItems::demo(); ?>
            </div>
        </div>
        <?php
    }

}