<?php

namespace Layout;

use AssetsList\AssetsList;
use Icons\Icons;
use Icons\IconsFactory;
use Privilege\Privilege;
use Privilege\PrivilegeUser;
use Users\Users;

class Layout
{

    private $elementFiles;
    private $elementsDir;

    protected $onDisplayBefore;


    protected function __construct()
    {
        $this->elementsDir = APP_ROOT_DIR . "/layout-elements/nullos";
    }

    public static function create()
    {
        return new self;
    }


    /**
     * array of elementType => fileName
     */
    public function setElementFiles(array $files)
    {
        $this->elementFiles = $files;
        return $this;
    }

    public function display()
    {
        if (is_callable($this->onDisplayBefore)) {
            call_user_func($this->onDisplayBefore, $this);
        }

        $exception = null;
        ob_start();


        $calendrierQueryString = "";
        if (array_key_exists('calendrierQueryString', $_SESSION)) {
            $calendrierQueryString = '?' . $_SESSION['calendrierQueryString'];
        }

        ?>


        <body class="holygrail">
        <!--        <div class="topbar">--><?php ////LayoutServices::displayTopBar();
        ?><!--</div>-->




        <div class="topmenu">

            <ul>
                <li style="flex: auto">
                    <select id="users-selector">
                        <?php



                        $users = Users::getId2Labels();
                        $userSelected = null;
                        if (array_key_exists('user_selected', $_SESSION) && null !== $_SESSION['user_selected']) {
                            $userSelected = $_SESSION['user_selected'];
                        } else {
                            $userSelected = key($users);
                            $_SESSION['user_selected'] = $userSelected;
                        }
                        foreach ($users as $id => $label) {
                            $s = ((int)$id === (int)$userSelected) ? ' selected="selected"' : '';
                            ?>
                            <option <?php echo $s; ?> value="<?php echo $id; ?>"><?php echo $label; ?></option>
                            <?php
                        }
                        ?>
                    </select>
                </li>
                <li><a id="calendrier-topmenu-link"
                       href="/calendrier<?php echo $calendrierQueryString; ?>">Calendrier</a></li>
            </ul>
        </div>

        <div class="body panes-container">
            <div id="one" class="pane-main leftmenu">
                <section class="header">
                    <span class="title"><?php echo ucfirst(WEBSITE_NAME); ?></span>
                    <a href="<?php echo url('/?disconnect=1'); ?>"><?php echo Icons::printIcon('exit'); ?></a>
                </section>
                <?php LayoutServices::displayLeftMenuBlocks(); ?>
            </div>
            <div id="two" class="pane-aux right-pane">
                <?php
                try {
                    // we just prevent the exception from breaking the ob_start
                    $this->includeElement('body');
                } catch (\Exception $exception) {
                    if (false === LayoutConfig::showBodyExceptionInYourFace()) {
                        Goofy::alertError(\Helper::defaultLogMsg(), false, false);
                        \Logger::log($exception, "layout.body");
                    } else {
                        a($exception);
                    }
                }
                ?>
            </div>
        </div>
        <div class="bottombar"></div>


        <script>
            Split(['#one', '#two'], {
                sizes: [20, 80],
                minSize: 20
            });

            // expandable left menu sections
            var leftPane = document.getElementById('one');
            leftPane.addEventListener('click', function (e) {
                var expander = e.target.closest('.expander');
                if (null !== expander) {
                    var section = expander.closest('.section-block');
                    if (null !== section) {
                        section.classList.toggle('closed');
                        var span = section.querySelector('.expander-label');
                        if (null !== span) {
                            var isOpen = "1";
                            if (section.classList.contains('closed')) {
                                isOpen = "0";
                            }
                            var label = encodeURIComponent(span.textContent);
                            z.setCookie('leftmenu-' + label, isOpen, 7);
                        }
                    }
                }
            });

            [].forEach.call(leftPane.querySelectorAll('.expander-label'), function (item) {
                var label = 'leftmenu-' + encodeURIComponent(item.textContent);
                var cookieVal = z.getCookie(label);
                if ('0' === cookieVal) {
                    item.closest('.section-block').classList.add('closed');
                }
            });


        </script>


        <?php IconsFactory::printIconsDefinitions(); ?>

        </body>
        <?php
        $body = ob_get_clean();


        ?>


        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <title><?php echo ucfirst(WEBSITE_NAME); ?></title>
            <link rel="stylesheet" href="<?php echo url('/style/style.css'); ?>">
            <link rel="stylesheet" href="<?php echo url('/style/layout.css'); ?>">
            <link rel="stylesheet" href="<?php echo url('/style/pastel-theme.css'); ?>">
            <link rel="stylesheet" href="<?php echo url('/libs/jquery-ui-1.12.1/jquery-ui.min.css'); ?>">
            <script src="<?php echo url('/libs/zquery/zquery.js'); ?>"></script>


            <script src="<?php echo url('/libs/jquery-ui-1.12.1/external/jquery/jquery.js'); ?>"></script>
            <script src="<?php echo url('/libs/jquery-ui-1.12.1/jquery-ui.js'); ?>"></script>
            <script src="<?php echo url('/libs/split/split.js'); ?>"></script>
            <?php
            AssetsList::displayList();
            ?>
        </head>
        <?php echo $body; ?>
        </html>
        <?php
    }


    private function includeElement($key)
    {
        $fileName = $this->elementFiles[$key];
        $file = $this->elementsDir . "/" . $fileName;
        require_once $file;
    }
}