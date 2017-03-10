<?php


//--------------------------------------------
// CONFIG
//--------------------------------------------
use AssetsList\AssetsList;
use Cache\Cache;
use CompteMail\CompteMail;
use DirScanner\YorgDirScannerTool;
use Project\Project;
use UserHasTask\UserHasTask;
use Util\GeneralUtil;


$userId = $_SESSION['user_selected'];
$hasAdminPower = (array_key_exists("connected_user_id", $_SESSION) && (int)$userId === (int)$_SESSION['connected_user_id'] && false !== $_SESSION['connected_user_id']);
$hasMailPower = true;

$projectId2Labels = Project::getId2Labels($userId);


$projectId = null;

if (array_key_exists('project_id', $_GET)) {
    $projectId = (int)$_GET['project_id'];
} elseif (array_key_exists('project_id', $_SESSION)) {
    $projectId = (int)$_SESSION['project_id'];
} else {
    $projectId = (int)key($projectId2Labels);
}


if (array_key_exists('start', $_GET)) {
    $periodStartDate = $_GET['start'];
} elseif (array_key_exists('periodStartDate', $_SESSION)) {
    $periodStartDate = $_SESSION['periodStartDate'];
} else {
    $periodStartDate = date("Y-m-d 00:00:00");
}


if (array_key_exists('interval', $_GET)) {
    $periodInterval = $_GET['interval'];
} elseif (array_key_exists('periodInterval', $_SESSION)) {
    $periodInterval = $_SESSION['periodInterval'];
} else {
    $periodInterval = 86400;
}


if (array_key_exists('segments', $_GET)) {
    $periodNbSegments = $_GET['segments'];
} elseif (array_key_exists('periodNbSegments', $_SESSION)) {
    $periodNbSegments = $_SESSION['periodNbSegments'];
} else {
    $periodNbSegments = 30;
}


$defaultTaskColor = '#733a4a';

//a($periodStartDate, $periodInterval, $periodNbSegments);
if (null === $projectId) {
    $projectId = 0;
}

//--------------------------------------------
// SCRIPT
//--------------------------------------------
$projectInfo = Project::getInfo($projectId);
$projectName = $projectInfo["name"];

list($cursorTaskId, $cursorDatetime) = Project::getProjectCursorInfo($projectInfo["current"]);
$cursorTime = GeneralUtil::gmMysqlToTime($cursorDatetime);


//$_SESSION['taskOpenStates'] = [];
$taskOpenStates = (array_key_exists("taskOpenStates", $_SESSION)) ? $_SESSION["taskOpenStates"] : [];
//a($taskOpenStates);


AssetsList::css("/style/roadmaps.css");
AssetsList::css("/iconfont/material-icons.css");
AssetsList::css("/libs/screendebug/css/screendebug.css");
AssetsList::js("/libs/simpledrag/simpledrag.js");
AssetsList::js("/libs/screendebug/js/screendebug.js");
AssetsList::js("/libs/calendar/calendar.js");


$_periodIntervals = [
    1 => "1 jour",
    2 => "2 jours",
    5 => "5 jours",
    7 => "1 semaine",
    14 => "2 semaines",
    30 => "environ 1 mois",
    60 => "environ 2 mois",
    90 => "environ 3 mois",
    180 => "environ 6 mois",
    365 => "environ 1 an",
];
$periodIntervals = [];
foreach ($_periodIntervals as $k => $v) {
    $periodIntervals[$k * 86400] = $v;
}


?>
<div class="action-topcontainer">
    <?php if (true === $hasAdminPower): ?>
        <div>
            <button class="project-add">Ajouter un nouveau projet</button>
        </div>
        <div>
            <button class="project-duplicate">Dupliquer un projet</button>
        </div>
        <div style="flex: auto">
            <button class="project-delete">Supprimer un projet</button>
        </div>

        <!--        <div>-->
        <!--            <button class="project-save">Faire une sauvegarde</button>-->
        <!--        </div>-->
        <!---->
        <!--        <div style="flex: auto">-->
        <!--            <button class="project-restore">Restaurer une sauvegarde</button>-->
        <!--        </div>-->

    <?php endif; ?>
    <i class="material-icons period-prev">arrow_back</i>
    <i class="material-icons period-next">arrow_forward</i>
    <div>
        <label>
            Date de début
            <input type="text" id="period_datestart" value="<?php echo substr($periodStartDate, 0, 10); ?>">
        </label>
    </div>
    <div>
        <label>Intervalle</label>
        <select id="period_interval">
            <?php foreach ($periodIntervals as $key => $label):
                $s = ($key === $periodInterval) ? 'selected="selected"' : '';
                ?>
                <option <?php echo $s; ?> value="<?php echo $key; ?>"><?php echo $label; ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div>
        <label>
            Nombre de segments
            <select id="period_segments">
                <?php for ($i = 1; $i <= 365; $i++):
                    $s = ($i === $periodNbSegments) ? 'selected="selected"' : '';
                    ?>
                    <option <?php echo $s; ?> value="<?php echo $i; ?>"><?php echo $i; ?></option>
                <?php endfor; ?>
            </select>
        </label>
    </div>

</div>


<?php
$hasProject = (count($projectId2Labels) > 0);
$tasks = [];
$plots = [];
if (true === $hasProject):
    ?>
    <div class="panes-container" style="height: 100%">
        <div id="three">
            <?php


            $p = \Period\Period::create();
            $p->setStartDate($periodStartDate);
            $p->setEndDate(gmdate("Y-m-d H:i:s", GeneralUtil::gmMysqlToTime($periodStartDate) + $periodInterval * $periodNbSegments));
            $p->setInterval($periodInterval);


            $helper = new \Period\InlinePeriodHelper($p);


            // note: the cache is not efficient -> not using it now but maybe one day, but use chrome and it's good.
            //            $cacheName = 'project-' . $projectId . "-tasks.php";
            //            $tasks = Cache::getArray($cacheName);
            //            if (false === $tasks) {
            //                $tasks = \Task\TaskUtil::getTasksByProject($projectId, $p);
            //                Cache::cacheArray($cacheName, $tasks);
            //            }

            $tasks = \Task\TaskUtil::getTasksByProject($projectId, $p);


            function justDate($dateTime)
            {
                return substr($dateTime, 0, 10);
            }

            $months = [
                "1" => "jan",
                "2" => "fév",
                "3" => "mar",
                "4" => "avr",
                "5" => "mai",
                "6" => "juin",
                "7" => "juil",
                "8" => "aoû",
                "9" => "sep",
                "10" => "oct",
                "11" => "nov",
                "12" => "déc",
            ];

            function formatDate($timestamp, $months)
            {
                $month = (int)date('m', $timestamp);
                $monthName = $months[$month];
                return gmdate("d", $timestamp) . " " . ucfirst($monthName);
            }


            function isFilled($plotTime, $timeStart, $timeEnd)
            {
                return ($plotTime >= $timeStart && $plotTime < $timeEnd);
            }


            $sBaby = (true === $hasAdminPower) ? "" : "baby";

            ?>
            <div class="roadmaps" id="roadmaps">
                <table class="roadmaps-table <?php echo $sBaby; ?>" id="roadmaps-table">
                    <tr>
                        <th class="first-column-cell">
                            <table>
                                <tr>
                                    <td>
                                        <select class="label" id="project-selector">
                                            <option <?php if (0 === $projectId) {
                                                echo 'selected="selected"';
                                            } ?> value="0">Choisissez un projet
                                            </option>
                                            <?php foreach ($projectId2Labels as $id => $label):
                                                $s = ($id === $projectId) ? ' selected="selected"' : '';
                                                ?>
                                                <option <?php echo $s; ?>
                                                        value="<?php echo $id; ?>"><?php echo $label; ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </td>
                                    <?php if (true === $hasAdminPower): ?>
                                        <td>
                                            <button class="add-child-trigger"><i
                                                        class="material-icons add-child-trigger">add</i>
                                            </button>
                                        </td>
                                    <?php endif; ?>
                                    <td>
                                        <button class="tasks-closeall-trigger"><i
                                                    class="material-icons tasks-closeall-trigger">expand_less</i>
                                        </button>
                                    </td>
                                    <td>
                                        <button class="tasks-openall-trigger"><i
                                                    class="material-icons tasks-openall-trigger">expand_more</i>
                                        </button>
                                    </td>
                                </tr>
                            </table>
                        </th>
                        <th style="clear: both;">Start date</th>
                        <th>End date</th>
                        <?php if (true === $hasMailPower): ?>
                            <th>Email</th>
                        <?php endif; ?>
                        <?php
                        $plots = $helper->getTimeScalePlots();
                        foreach ($plots as $k => $time) {
                            ?>
                            <th class="th-time" title="<?php echo date("Y", $time); ?>">
                                <?php echo formatDate($time, $months); ?>
                            </th>
                            <?php
                        }

                        ?>
                    </tr>
                    <?php
                    $i = 0;
                    $sStyle = '';
                    foreach ($tasks as $task):
                        $sStyle .= ".filled.task-" . $task['id'] . ' { background-color: ' . $task['color'] . ' }' . PHP_EOL;
                        $ml = $task['level'] * 20 + 10;
                        $style = ' style="padding-left: ' . $ml . 'px"';
                        $sOddEven = (0 == $i++ % 2) ? "even" : "odd";
                        $taskId = $task['id'];


                        /**
                         * Rules for handling persistent node states with tables
                         * -----------------------------
                         * Basically, all nodes are opened at first opening (A node is a parent, a leaf is a child).
                         * When a node is clicked, it's state is saved into the php session.
                         * So, next time the page opens, the node's state is restored.
                         *
                         * The php layer is first executed, and basically assigns the data-opened attribute to the nodes
                         * (continue reading, it should make sense in the end); it also updates the toggler text (+/-).
                         * The php layer takes its data from the session.
                         *
                         *
                         * Javascript code is executed, and does the following:
                         *
                         * - if a node is opened/closed, it updates the toggler's text (+/-),
                         *                 and it also applies the attribute: data-opened, which value
                         *                  is 1 if opened, and 0 if closed.
                         *
                         * - When the page opens, it parses all items (nodes/leaves), and for each of them decide whether or not
                         *          the item should be visible or not.
                         *          It not visible, the item gets the closed class, which has "display: none".
                         *          To decide whether or not an item is visible, we use this:
                         *              - it's visible if:
                         *                  - it does not have a closed ancestor (an ancestor with data-opened=0)
                         *
                         *
                         *
                         *
                         */
                        $sBtnText = "-";
                        $sOpened = "1";
                        if (array_key_exists($task['id'], $taskOpenStates) && false === $taskOpenStates[$task['id']]) {
                            $sBtnText = "+";
                            $sOpened = "0";
                        }

                        $sLabel = "label";
                        if (false === $hasAdminPower) {
                            $sLabel = "labelbr";
                        }
                        $sHasChildren = "";
                        if (true === $task['hasChildren']) {
                            $sHasChildren = "has-children";
                        }


                        ?>
                        <tr class="<?php echo $sOddEven; ?> level-<?php echo $task['level']; ?> <?php echo $sHasChildren; ?>"
                            data-level="<?php echo $task['level']; ?>"
                            data-opened="<?php echo $sOpened; ?>"
                            data-color="<?php echo htmlspecialchars($task['color']); ?>"
                        >
                            <td<?php echo $style; ?> class="infocell">

                                <div class="first-column-cell">
                                    <?php if (true === $task['hasChildren']): ?>
                                        <button class="toggler"><?php echo $sBtnText; ?></button>
                                    <?php endif; ?>
                                    <span class="<?php echo $sLabel; ?> task-info-trigger"><span
                                                class="labelonly task-info-trigger"><?php echo $task['label'] . '</span>' . " (" . $task['id'] . ")"; ?></span>

                                        <?php if (true === $hasAdminPower): ?>
                                            <button class="add-child-trigger"><i
                                                        class="material-icons add-child-trigger">add</i></button>
                                            <button class="leftmove-trigger"><i class="material-icons leftmove-trigger">keyboard_arrow_left</i></button>
                                            <button class="rightmove-trigger"><i
                                                        class="material-icons rightmove-trigger">keyboard_arrow_right</i></button>


                                            <button class="remove-child-trigger"><i
                                                        class="material-icons remove-child-trigger">delete</i>
                                </button>

                                            <div class="sort">
                                    <button class="sort-up-trigger"><i class="material-icons sort-up-trigger">arrow_drop_up</i>
                                    </button>
                                    <button class="sort-down-trigger"><i class="material-icons sort-down-trigger">arrow_drop_down</i>
                                    </button>
                                </div>
                                        <?php endif; ?>
                                </div>

                            </td>

                            <?php
                            $class = "";
                            $class2 = "";
                            $class3 = "";
                            if (true === $hasAdminPower) {
                                $class = "start-date-update-trigger";
                                $class2 = "end-date-update-trigger";
                                $class3 = "mail-conf-trigger";
                            }
                            ?>

                            <td data-id="<?php echo $task['id']; ?>"
                                class="infocell <?php echo $class; ?>"
                                data-date="<?php echo $task['start_date']; ?>"
                                title="<?php echo $task['start_date']; ?>"><?php echo justDate($task['start_date']); ?></td>
                            <td data-id="<?php echo $task['id']; ?>"
                                class="infocell <?php echo $class2; ?>"
                                data-date="<?php echo $task['end_date']; ?>"
                                title="<?php echo $task['end_date']; ?>"><?php echo justDate($task['end_date']); ?></td>


                            <?php if (true === $hasMailPower): ?>
                                <td data-id="<?php echo $task['id']; ?>"
                                    class="infocell <?php echo $class3; ?>"
                                    style="text-align: center;"
                                >
                                    <?php
                                    $hasBeenSent = UserHasTask::getMailHasBeenSent($userId, $taskId);
                                    $sClass = ($hasBeenSent) ? 'sent' : '';
                                    ?>
                                    <button class="mail-send-trigger fake-btn">
                                        <i class="mail-send-trigger material-icons <?php echo $sClass; ?>">email</i>
                                    </button>
                                </td>
                            <?php endif; ?>

                            <?php
                            foreach ($plots as $k => $time):
                                ?>
                                <td class="cell task-<?php echo $task['id']; ?>">
                                    <div class="token-grab-handle"></div>
                                    <div class="token-resize-handle-left token-resize-handle"></div>
                                    <div class="token-resize-handle-right token-resize-handle"></div>
                                </td>
                                <?php
                            endforeach;
                            ?>
                        </tr>
                    <?php endforeach; ?>
                </table>
            </div>
        </div>
        <div id="four"></div>
    </div>
    <style>
        <?php echo $sStyle; ?>
    </style>
<?php endif; ?>

<script>


    var hasAdminPower = <?php echo (true === $hasAdminPower) ? "true" : "false"; ?>;
    var hasMailPower = <?php echo (true === $hasMailPower) ? "true" : "false"; ?>;


    $(document).ready(function () {


        $("#users-selector").on('change', function () {
            var id = $(this).val();
            $.getJSON('/services/roadmaps.php?action=calendrier-users-change&id=' + id, function (data) {
                if ('ok' === data) {
                    location.reload();
                }
            });
        });

        $("#project-selector").on("change", function () {
            var value = $(this).val();
            $.getJSON("/services/roadmaps.php?action=calendrier-project-change&id=" + value, function (data) {
                if ('ok' === data) {
                    window.location.reload();
                }
            });
        });


        <?php if(true === $hasProject): ?>




        function rgb2hex(rgb) {
            rgb = rgb.match(/^rgba?[\s+]?\([\s+]?(\d+)[\s+]?,[\s+]?(\d+)[\s+]?,[\s+]?(\d+)[\s+]?/i);
            return (rgb && rgb.length === 4) ? "#" +
                ("0" + parseInt(rgb[1], 10).toString(16)).slice(-2) +
                ("0" + parseInt(rgb[2], 10).toString(16)).slice(-2) +
                ("0" + parseInt(rgb[3], 10).toString(16)).slice(-2) : '';
        }


        //----------------------------------------
        // PERIOD FORM
        //----------------------------------------
        var jPeriodStartDate = $("#period_datestart");
        var jPeriodInterval = $("#period_interval");
        var jPeriodSegments = $("#period_segments");
        jPeriodStartDate.datepicker({
            dateFormat: "yy-mm-dd"
        });
        jPeriodStartDate.on('change', function () {
            updatePeriodForm();
        });
        jPeriodInterval.on('change', function () {
            updatePeriodForm();
        });
        jPeriodSegments.on('change', function () {
            updatePeriodForm();
        });


        function updatePeriodForm() {
            $.post("/services/roadmaps.php?action=calendrier-update-period", {
                date_start: jPeriodStartDate.val(),
                interval: jPeriodInterval.val(),
                segments: jPeriodSegments.val()
            }, function (data) {
                if ('ok' === data) {
                    window.location.reload();
                }
            }, 'json');
        }

        <?php endif; ?>


        //----------------------------------------
        // TABLE
        //----------------------------------------
        var jTable = $("#roadmaps-table");
        $("body").on('click', function (e) {
            var jTarget = $(e.target);
            if (jTarget.hasClass("toggler")) {
                e.preventDefault();
                toggleContainer(jTarget.closest('tr'));
            }
            else if (
                jTarget.hasClass("start-date-update-trigger") ||
                jTarget.hasClass("end-date-update-trigger")
            ) {
                e.preventDefault();


                var title = "";
                var action = "";
                var showDecaler = "";

                if (jTarget.hasClass("start-date-update-trigger")) {
                    title = "Modifier la date de départ";
                    action = "calendrier-update-startdate";
                    showDecaler = true;
                }
                else {
                    title = "Modifier la date de fin";
                    action = "calendrier-update-enddate";
                    showDecaler = false;
                }


                if ('undefined' !== typeof $("#dialog-update-date").dialog('instance')) {
                    $("#dialog-update-date").dialog("close");
                }

                $("#dialog-update-date").dialog({
                    title: title,
                    position: {
                        my: "center",
                        at: "center",
                        of: jTarget
                    },
                    width: 600,
                    open: function (event, ui) {
                        var defaultValue = jTarget.text();
                        var jDate = $("#dialog-update-date").find(".datepicker");
                        jDate.datepicker({
                            dateFormat: "yy-mm-dd"
                        });
                        jDate.datepicker("setDate", defaultValue);
                        jDate.blur();

                        var jLiDecaler = $("#dialog-update-date").find(".decaler");
                        if (true === showDecaler) {
                            jLiDecaler.show();
                        }
                        else {
                            jLiDecaler.hide();
                        }


                        var date = jTarget.attr("data-date");
                        var horaire = date.split(" ")[1];

                        var hour = horaire.substr(0, 2);
                        var minute = horaire.substr(3, 2);

                        var jHour = $("#dialog-update-date").find('.hour');
                        var jMinute = $("#dialog-update-date").find('.minute');

                        jHour.val(hour);
                        jMinute.val(minute);


                    },
                    buttons: {
                        "Appliquer": function () {

                            var jDatePicker = $("#dialog-update-date").find('.datepicker');
                            var jHour = $("#dialog-update-date").find('.hour');
                            var jMinute = $("#dialog-update-date").find('.minute');
                            var jCheckDecaler = $("#dialog-update-date").find('.decaler-checkbox');
                            var id = jTarget.attr("data-id");


                            $.post('/services/roadmaps.php?action=' + action, {
                                'id': id,
                                'decaler': jCheckDecaler.prop('checked'),
                                'date': jDatePicker.val(),
                                'hour': jHour.val(),
                                'minute': jMinute.val()
                            }, function (data) {
                                if ('ok' === data) {
                                    location.reload();
                                }
                            }, 'json');
                        },
                        "Annuler": function () {
                            $(this).dialog("close");
                        }
                    }
                });
            }
            else if (jTarget.hasClass("add-child-trigger")) {
                e.preventDefault();

                if ('undefined' !== typeof $("#dialog-create-task").dialog('instance')) {
                    $("#dialog-create-task").dialog("close");
                }

                var parentId = jTarget.closest('tr').attr("data-id");
                if ('undefined' === typeof parentId) {
                    parentId = 0;
                }


                $("#dialog-create-task").dialog({
                    position: {
                        my: "top",
                        at: "center",
                        of: jTarget
                    },
                    width: 600,
                    open: function (event, ui) {


                        var date = "<?php echo gmdate('Y-m-d'); ?>";
                        var jDial = $("#dialog-create-task");
                        var jDate = jDial.find(".datepicker");
                        jDate.datepicker({
                            dateFormat: "yy-mm-dd"
                        });
                        jDate.datepicker("setDate", date);


                        var hour = "00";
                        var minute = "00";

                        var jHour = jDial.find('.hour');
                        var jMinute = jDial.find('.minute');

                        jHour.val(hour);
                        jMinute.val(minute);


                        /**
                         * Use parent color if any parent
                         **/
                        var jTr = jTarget.closest('tr');
                        var color = jTr.find('.filled:first').css('background-color');
                        if (color) {
                            var cssColor = rgb2hex(color);
                            jDial.find('.color').val(cssColor);
                        }




                        $.getJSON("/services/roadmaps.php?action=calendrier-get-bound-comptemail&task_id=" + parentId, function(data){
                            jDial.find('.compte_mail').val(data);
                        });


                    },
                    buttons: {
                        "Appliquer": function () {


                            var jDial = $("#dialog-create-task");
                            var label = jDial.find('.label').val();
                            var date = jDial.find('.datepicker').val();
                            var hour = jDial.find('.hour').val();
                            var minute = jDial.find('.minute').val();
                            var duree = jDial.find('.duration').val();
                            var position = jDial.find('.position').val();
                            var color = jDial.find('.color').val();
                            var compte_mail = jDial.find('.compte_mail').val();


                            $.post('/services/roadmaps.php?action=calendrier-task-create', {
                                'projectId': <?php echo $projectId; ?>,
                                'parentId': parentId,
                                'label': label,
                                'date': date,
                                'hour': hour,
                                'minute': minute,
                                'duration': duree,
                                'color': color,
                                'compte_mail': compte_mail,
                                'position': position
                            }, function (data) {
                                if ('ok' === data) {
                                    location.reload();
                                }
                            }, 'json');
                        },
                        "Annuler": function () {
                            $(this).dialog("close");
                        }
                    }
                });
            }
            else if (jTarget.hasClass("sort-up-trigger")) {
                e.preventDefault();
                var id = jTarget.closest('tr').attr("data-id");
                $.getJSON("/services/roadmaps.php?action=calendrier-sort-up&id=" + id, function (data) {
                    if ("ok" === data) {
                        window.location.reload();
                    }
                });
            }
            else if (jTarget.hasClass("sort-down-trigger")) {
                e.preventDefault();
                var id = jTarget.closest('tr').attr("data-id");
                $.getJSON("/services/roadmaps.php?action=calendrier-sort-down&id=" + id, function (data) {
                    if ("ok" === data) {
                        window.location.reload();
                    }
                });
            }
            else if (jTarget.hasClass("remove-child-trigger")) {
                e.preventDefault();
                var id = jTarget.closest('tr').attr("data-id");
                if (true === window.confirm("Are you sure you want to delete this task?")) {
                    $.getJSON("/services/roadmaps.php?action=calendrier-remove-task&id=" + id, function (data) {
                        if ("ok" === data) {
                            window.location.reload();
                        }
                    });
                }
            }
            else if (jTarget.hasClass("task-info-trigger") && true === hasAdminPower) {
                e.preventDefault();
                var jTr = jTarget.closest('tr');
                var id = jTr.attr("data-id");
                var color = jTr.attr("data-color");
                var label = jTr.find(".labelonly").text();
                var task_id = id;

                var jDial = $("#dialog-taskinfo");

                if ('undefined' !== typeof jDial.dialog('instance')) {
                    jDial.dialog("close");
                }

                jDial.dialog({
                    position: {
                        my: "center",
                        at: "center",
                        of: jTarget
                    },
                    width: 600,
                    open: function () {


                        if (true === hasMailPower) {
                            $.getJSON("/services/roadmaps.php?action=calendrier-get-bound-comptemail&task_id=" + task_id, function (data) {
                                var jCompteMail = jDial.find('.compte_mail');
                                jCompteMail.val(data);
                            });
                        }


                        var jColor = jDial.find('.color');
                        var jSpreadColor = jDial.find('.apply-color-to-children');
                        jColor.val(color);
                        var aChildrenTrs = getChildrenContainers(jTr);


                        jColor.off().on('input', function () {
                            var col = $(this).val();
                            var spread = jSpreadColor.prop('checked');
                            jTr.find(".filled").css("background-color", col);
                            if (true === spread) {
                                for (var i in aChildrenTrs) {
                                    var jTrChild = aChildrenTrs[i];
                                    jTrChild.find(".filled").css("background-color", col);
                                }
                            }
                        });
                        jDial.find('.label').val(label);

                        jSpreadColor.off().on('change', function () {
                            var checked = $(this).prop('checked');
                            var _color = jColor.val();
                            if (true === checked) {
                                for (var i in aChildrenTrs) {
                                    var jTrChild = aChildrenTrs[i];
                                    jTrChild.find(".filled").css("background-color", _color);
                                }
                            }
                            else {
                                for (var i in aChildrenTrs) {
                                    var jTrChild = aChildrenTrs[i];
                                    var oldColor = jTrChild.attr("data-color");
                                    jTrChild.find(".filled").css("background-color", oldColor);
                                }
                            }
                        });


                    },
                    buttons: {
                        "Appliquer": function () {

                            var color = jDial.find('.color').val();
                            var label = jDial.find('.label').val();
                            var compteMail = jDial.find('.compte_mail').val();
                            var spread = jDial.find('.apply-color-to-children').prop("checked");

                            $.post('/services/roadmaps.php?action=calendrier-task-update', {
                                'id': id,
                                'label': label,
                                'color': color,
                                'compte_mail': compteMail,
                                'applyColorToChildren': spread
                            }, function (data) {
                                if ('ok' === data) {
                                    location.reload();
                                }
                            }, 'json');
                        },
                        "Annuler": function () {
                            $(this).dialog("close");
                            jTr.find(".filled").css("background-color", color);
                        }
                    }
                });
            }
            else if (jTarget.hasClass("period-prev") ||
                jTarget.hasClass("period-next")) {
                var dir = "prev";
                if (jTarget.hasClass("period-next")) {
                    dir = "next";
                }

                e.preventDefault();
                $.getJSON("/services/roadmaps.php?action=calendrier-move-period&direction=" + dir, function (data) {
                    if ("ok" === data) {
                        window.location.reload();
                    }
                });
            }
            else if (jTarget.hasClass("leftmove-trigger") ||
                jTarget.hasClass("rightmove-trigger")) {
                var dir = "right";
                if (jTarget.hasClass("leftmove-trigger")) {
                    dir = "left";
                }

                var jTr = jTarget.closest("tr");
                var id = jTr.attr("data-id");
                var prevId = jTr.prev().attr("data-id");

                e.preventDefault();
                $.getJSON("/services/roadmaps.php?action=calendrier-change-parent&id=" + id + "&dir=" + dir + "&prev=" + prevId, function (data) {
                    if ("ok" === data) {
                        window.location.reload();
                    }
                });
            }
            else if (jTarget.hasClass("project-add")) {

                var jDial = $("#dialog-project-create");
                if ('undefined' !== typeof jDial.dialog('instance')) {
                    jDial.dialog("close");
                }
                jDial.dialog({
                    position: {
                        my: "center",
                        at: "center",
                        of: jTarget
                    },
                    width: 600,
                    buttons: {
                        "Appliquer": function () {

                            var name = jDial.find('.name').val();

                            $.post('/services/roadmaps.php?action=calendrier-project-create', {
                                'name': name
                            }, function (data) {
                                if ('ok' === data) {
                                    location.reload();
                                }
                            }, 'json');
                        },
                        "Annuler": function () {
                            $(this).dialog("close");
                        }
                    }
                });
            }
            else if (jTarget.hasClass("project-duplicate")) {

                var jDial = $("#dialog-project-duplicate");
                if ('undefined' !== typeof jDial.dialog('instance')) {
                    jDial.dialog("close");
                }
                jDial.dialog({
                    position: {
                        my: "center",
                        at: "center",
                        of: jTarget
                    },
                    width: 600,
                    buttons: {
                        "Appliquer": function () {

                            var name = jDial.find('.project_name').val();
                            var id = jDial.find('.project_id').val();

                            $.post('/services/roadmaps.php?action=calendrier-project-duplicate', {
                                'id': id,
                                'name': name
                            }, function (data) {
                                if ('ok' === data) {
                                    location.reload();
                                }
                            }, 'json');
                        },
                        "Annuler": function () {
                            $(this).dialog("close");
                        }
                    }
                });
            }
            else if (jTarget.hasClass("project-delete")) {

                var jDial = $("#dialog-project-delete");
                if ('undefined' !== typeof jDial.dialog('instance')) {
                    jDial.dialog("close");
                }
                jDial.dialog({
                    position: {
                        my: "center",
                        at: "center",
                        of: jTarget
                    },
                    width: 600,
                    buttons: {
                        "Appliquer": function () {


                            var id = jDial.find('.project_id').val();

                            $.post('/services/roadmaps.php?action=calendrier-project-delete', {
                                'id': id
                            }, function (data) {
                                if ('ok' === data) {
                                    location.reload();
                                }
                            }, 'json');
                        },
                        "Annuler": function () {
                            $(this).dialog("close");
                        }
                    }
                });
            }
            else if (jTarget.hasClass("project-save")) {
                e.preventDefault();
                var jDial = $("#dialog-backup-create");
                if ('undefined' !== typeof jDial.dialog('instance')) {
                    jDial.dialog("close");
                }
                jDial.dialog({
                    position: {
                        my: "center",
                        at: "center",
                        of: jTarget
                    },
                    width: 600,
                    open: function () {
                        var date = new Date().toISOString().slice(0, 19).replace('T', ' ');
                        date = date.replace(/:/g, "");
                        date = date.replace(" ", "_");
                        date = date.replace(/-/g, "");
                        date += "--<?php echo $projectName; ?>.sql";

                        jDial.find(".name").val(date);
                    },
                    buttons: {
                        "Appliquer": function () {

                            var name = jDial.find('.name').val();

                            $.post('/services/roadmaps.php?action=calendrier-all-save', {
                                'name': name,
                                'pid': <?php echo $projectId; ?>
                            }, function (data) {
                                if ('ok' === data) {
                                    alert("sauvegarde effectuée");
                                    location.reload();
                                }
                            }, 'json');
                        },
                        "Annuler": function () {
                            $(this).dialog("close");
                        }
                    }
                });
            }
            else if (jTarget.hasClass("project-restore")) {
                e.preventDefault();
                var jDial = $("#dialog-backup-restore");
                if ('undefined' !== typeof jDial.dialog('instance')) {
                    jDial.dialog("close");
                }
                jDial.dialog({
                    position: {
                        my: "center",
                        at: "center",
                        of: jTarget
                    },
                    width: 600,
                    buttons: {
                        "Appliquer": function () {

                            var name = jDial.find('.name').val();

                            $.post('/services/roadmaps.php?action=calendrier-all-restore', {
                                'name': name
                            }, function (data) {
                                if ('ok' === data) {
                                    alert("sauvegarde restaurée");
                                    location.reload();
                                }
                            }, 'json');
                        },
                        "Annuler": function () {
                            $(this).dialog("close");
                        }
                    }
                });
            }
            else if (jTarget.hasClass("mail-send-trigger")) {
                e.preventDefault();
                var jDial = $("#dialog-mail-send");
                if ('undefined' !== typeof jDial.dialog('instance')) {
                    jDial.dialog("close");
                }
                jDial.dialog({
                    position: {
                        my: "center",
                        at: "center",
                        of: jTarget
                    },
                    width: 600,
                    open: function () {
                        var taskId = jTarget.closest('tr').attr("data-id");
                        var jSubject = jDial.find(".subject");
                        var jRecipients = jDial.find(".recipients");
                        var jTextarea = jDial.find(".message");

                        $.getJSON("/services/roadmaps.php?action=calendrier-get-mail-template&task_id=" + taskId, function (data) {
                            jSubject.val(data.subject);

                            var s = '';
                            var c = 0;
                            for (var i in data.recipients_info) {
                                var item = data.recipients_info[i];
                                if (c > 0) {
                                    s += ', ';
                                }
                                s += '<span class="recipient">' + item['pseudo'] + ' (' + item['email'] + ')</span>';
                                c++;
                            }
                            jRecipients.html(s);
                            jTextarea.val(data.plain);
                        });
                    },
                    buttons: {
                        "Appliquer": function () {

                            var taskId = jTarget.closest('tr').attr("data-id");
                            var subject = jDial.find('.subject').val();
                            var message = jDial.find('.message').val();


                            $.post('/services/roadmaps.php?action=calendrier-send-notif-mail', {
                                'task_id': taskId,
                                'subject': subject,
                                'message': message
                            }, function (nbSent) {
                                var msg = nbSent + " mail(s) envoyé(s).";
                                if("0" == nbSent){
                                    msg += " Avez-vous bien configuré cette tâche ?";
                                }
                                alert(msg);
                                location.reload();

                            }, 'json');
                        },
                        "Annuler": function () {
                            $(this).dialog("close");
                        }
                    }
                });
            }
            else if (jTarget.hasClass("tasks-closeall-trigger")) {
                e.preventDefault();
                jTable.find("tr.has-children").each(function () {
                    closeContainer($(this));
                });
            }
            else if (jTarget.hasClass("tasks-openall-trigger")) {
                e.preventDefault();
                jTable.find("tr.has-children").each(function () {
                    openContainer($(this));
                });
            }
        });


        $("body").on('dblclick', function (e) {
            var jTarget = $(e.target);
            if (jTarget.hasClass('token-grab-handle')) {
                var jTd = jTarget.closest('td');
                var jTr = jTd.parent();
                var index = jTr.find('.cell').index(jTd);
                var taskId = jTr.attr("data-id");
                var time = plots[index];
                $.getJSON("/services/roadmaps.php?action=calendrier-project-setcursor&time=" + time + '&task_id=' + taskId + "&project_id=<?php echo $projectId; ?>", function (data) {
                    if ('ok' === data) {
                        location.reload();
                    }
                });
            }

        });


//        $(document).tooltip();

        //----------------------------------------
        // CURSOR
        //----------------------------------------
        var cursorTaskId = <?php echo $cursorTaskId; ?>;
        var cursorTime = "<?php echo $cursorTime; ?>";

        function initializeCursor() {
            var jTr = jTable.find("tr[data-id=" + cursorTaskId + "]");
            if (plots.length > 0) {
                var firstPlot = plots[0];
                var lastPlot = plots[plots.length - 1];
                if (cursorTime >= firstPlot && cursorTime <= lastPlot) {
                    var index = 0;
                    for (var i in plots) {
                        if (plots[i] >= cursorTime) {
                            break;
                        }
                        index++;
                    }
                    jTr.find(".cell:nth(" + index + ")").append('<i class="material-icons project-cursor">accessibility</i>');
                    jTable.find("tr:gt(0)").each(function () {
                        $(this).find(".cell:nth(" + index + ")").addClass('project-cursor');
                    });
                }
            }
        }

        //----------------------------------------
        // TOGGLING CONTAINERS
        //----------------------------------------
        function initializeContainers() {
            jTable.find('tr').each(function () {
                updateContainerState($(this));
            });
        }


        function getAncestors(jTr) {
            var aAncestors = [];
            var level = jTr.attr("data-level");

            jTr.prevAll().each(function () {
                if ($(this).attr("data-level") < level) {
                    level--;
                    aAncestors.push($(this));
                }
            });
            return aAncestors;
        }


        function updateContainerState(jTr) {
            var aAncestors = getAncestors(jTr);
            var isOpened = true;
            for (var i in aAncestors) {
                var jParent = aAncestors[i];
                if ("0" === jParent.attr("data-opened")) {
                    isOpened = false;
                    break;
                }
            }
            if (false === isOpened) {
                jTr.addClass("child-close");
            }
            else {
                jTr.removeClass("child-close");
            }
        }


        function toggleContainer(jTr) {
            var opened = true;
            if ('0' === jTr.attr("data-opened")) {
                opened = false;
            }
            if (true === opened) {
                closeContainer(jTr);
            }
            else {
                openContainer(jTr);
            }
        }

        function openContainer(jTr, update) {
            jTr.find(".toggler:first").text("-");
            jTr.attr("data-opened", '1');

            if (false !== update) {
                var id = jTr.attr("data-id");
                $.getJSON("/services/roadmaps.php?action=calendrier-open-container&id=" + id, function (data) {

                });
            }
            var aChildren = getChildrenContainers(jTr);
            for (var i in aChildren) {
                var jChildren = aChildren[i];
                updateContainerState(jChildren);
            }
        }

        function closeContainer(jTr, update) {
            jTr.find(".toggler:first").text("+");
            jTr.attr("data-opened", '0');
            if (false !== update) {
                var id = jTr.attr("data-id");
                $.getJSON("/services/roadmaps.php?action=calendrier-close-container&id=" + id, function (data) {

                });
            }
            var aChildren = getChildrenContainers(jTr);
            for (var i in aChildren) {
                var jChildren = aChildren[i];
                updateContainerState(jChildren);
            }
        }


        /**
         * Children are subsequent items with higher level
         */
        function getChildrenContainers(jTr) {
            var level = jTr.attr("data-level");
            var aTrs = [];
            var jNextTr = jTr;
            while (true) {
                jNextTr = jNextTr.next();
                if (!jNextTr) {
                    break;
                }
                var nextLevel = jNextTr.attr("data-level");
                if (nextLevel > level) {
                    aTrs.push(jNextTr);
                }
                else {
                    break;
                }
            }
            return aTrs;
        }


        //----------------------------------------
        // SORT ARROWS
        //----------------------------------------
        function updateArrows() {
            jTable.find('tr').each(function () {
                var hasPrevSibling = trHasPrevSibling($(this));
                var hasNextSibling = trHasNextSibling($(this));
                var canBecomeChild = trCanBecomeChild($(this));
                var canBecomeParent = trCanBecomeParent($(this));


                if (true === hasPrevSibling) {
                    $(this).addClass('has-prev-sibling');
                }
                else {
                    $(this).removeClass('has-prev-sibling');
                }
                if (true === hasNextSibling) {
                    $(this).addClass('has-next-sibling');
                }
                else {
                    $(this).removeClass('has-next-sibling');
                }
                if (true === canBecomeParent) {
                    $(this).addClass('can-become-parent');
                }
                else {
                    $(this).removeClass('can-become-parent');
                }
                if (true === canBecomeChild) {
                    $(this).addClass('can-become-child');
                }
                else {
                    $(this).removeClass('can-become-child');
                }
            });
        }

        function trHasPrevSibling(jTr) {
            var level = jTr.attr("data-level");
            var cpt = 0;
            var isDead = false;
            jTr.prevAll().each(function () {
                if (false === isDead && level === $(this).attr("data-level")) {
                    cpt++;
                }
                if ($(this).attr("data-level") < level) {
                    isDead = true;
                }
            });
            return (cpt > 0);
        }

        function trHasNextSibling(jTr) {
            var level = jTr.attr("data-level");
            var cpt = 0;
            var isDead = false;
            jTr.nextAll().each(function () {
                if (false === isDead && level === $(this).attr("data-level")) {
                    cpt++;
                }
                if ($(this).attr("data-level") < level) {
                    isDead = true;
                }
            });
            return (cpt > 0);
        }


        function trCanBecomeParent(jTr) {
            var level = jTr.attr("data-level");
            if (level > 0) {
                return true;
            }
            return false;
        }

        function trCanBecomeChild(jTr) {
            var jPrev = jTr.prev();
            if (jPrev.length > 0) {
                var prevLevel = jPrev.attr("data-level");
                if ("undefined" !== typeof prevLevel) {
                    if (prevLevel < jTr.attr("data-level")) {
                        return false;
                    }
                }
                else {
                    return false;
                }
            }
            return true;
        }


        //----------------------------------------
        // GUI DRAG
        //----------------------------------------
        var tasks = <?php echo json_encode($tasks); ?>;
        var periodInterval = <?php echo $periodInterval; ?>;
        var plots = <?php echo json_encode($plots); ?>;

        var oCalendar = new Calendar({
            table: $("#roadmaps-table"),
            tasks: tasks,
            periodInterval: periodInterval,
            babyMode: (false === hasAdminPower),
            plots: plots
        });
        oCalendar.draw(function () {
            updateArrows();
            initializeContainers();
            initializeCursor();
        });
        oCalendar.listen();

    });


</script>

<div style="display: none">
    <div id="dialog-update-date"
         style="text-align: left; box-sizing: border-box; padding-left: 20px">
        <ul style="list-style-type: none">
            <li style="margin-top: 10px">
                <label>
                    Choisissez la date <input type="text" class="datepicker">
                </label>
            </li>
            <li style="margin-top: 10px">
                <label>Choisissez l'horaire</label>
                <select class="hour">
                    <?php for ($i = 0; $i < 24; $i++):
                        $t = sprintf("%02s", $i);
                        ?>
                        <option value="<?php echo $t; ?>"><?php echo $t; ?></option>
                    <?php endfor; ?>
                </select>
                H
                <select class="minute">
                    <?php for ($i = 0; $i < 60; $i++):
                        $t = sprintf("%02s", $i);
                        ?>
                        <option value="<?php echo $t; ?>"><?php echo $t; ?></option>
                    <?php endfor; ?>
                </select>
                m
            </li>
            <!--            <li style="margin-top: 10px" class="decaler">-->
            <!--                Décaler les tâches suivantes <input type="checkbox" class="decaler-checkbox">-->
            <!--            </li>-->
        </ul>
    </div>
    <div id="dialog-create-task"
         title="Créer une tâche enfant"
         class="dialog-standard">
        <table>
            <tr>
                <td>Libellé</td>
                <td><input type="text" class="label"></td>
            </tr>
            <tr>
                <td>Durée (en jours)</td>
                <td><input type="text" class="duration" value="1"></td>
            </tr>
            <tr>
                <td>Date de début</td>
                <td>
                    <input type="text" class="datepicker">
                    <select class="hour">
                        <?php for ($i = 0; $i < 24; $i++):
                            $t = sprintf("%02s", $i);
                            ?>
                            <option value="<?php echo $t; ?>"><?php echo $t; ?></option>
                        <?php endfor; ?>
                    </select>
                    H
                    <select class="minute">
                        <?php for ($i = 0; $i < 60; $i++):
                            $t = sprintf("%02s", $i);
                            ?>
                            <option value="<?php echo $t; ?>"><?php echo $t; ?></option>
                        <?php endfor; ?>
                    </select>
                    m
                </td>
            </tr>
            <tr>
                <td>Couleur</td>
                <td><input type="color" class="color" value="<?php echo $defaultTaskColor; ?>"></td>
            </tr>
            <tr>
                <td>Responsable(s)</td>
                <td>
                    <?php
                    $id2label = CompteMail::getId2Labels();
                    ?>
                    <select multiple class="compte_mail" style="width: 200px;" size="<?php echo count($id2label); ?>">
                        <?php
                        foreach ($id2label as $id => $label):
                            ?>
                            <option value="<?php echo $id; ?>"><?php echo $label; ?></option>
                            <?php
                        endforeach;
                        ?>
                    </select>
                </td>
            </tr>
            <tr>
                <td>Ajouter</td>
                <td><select class="position">
                        <option value="last">à la fin</option>
                        <option value="first">au début</option>
                    </select></td>
            </tr>
        </table>
    </div>
    <div id="dialog-taskinfo"
         title="Modifier les informations d'une tâche"
         class="dialog-standard">
        <table>
            <tr>
                <td>Label</td>
                <td><input type="text" class="label"></td>
            </tr>
            <tr>
                <td>Responsable(s)</td>
                <td>
                    <?php
                    $id2label = CompteMail::getId2Labels();
                    ?>
                    <select multiple class="compte_mail" style="width: 200px;" size="<?php echo count($id2label); ?>">
                        <?php
                        foreach ($id2label as $id => $label):
                            ?>
                            <option value="<?php echo $id; ?>"><?php echo $label; ?></option>
                            <?php
                        endforeach;
                        ?>
                    </select>
                </td>
            </tr>
            <tr>
                <td>Couleur</td>
                <td><input type="color" class="color"></td>
            </tr>
            <tr>
                <td>Appliquer la couleur aux enfants également</td>
                <td><input type="checkbox" class="apply-color-to-children"></td>
            </tr>
        </table>
    </div>
    <div id="dialog-project-create"
         title="Créer un nouveau projet"
         class="dialog-standard">
        <table>
            <tr>
                <td>Nom</td>
                <td><input type="text" class="name"></td>
            </tr>
        </table>
    </div>
    <div id="dialog-project-duplicate"
         title="Dupliquer projet"
         class="dialog-standard">
        <table>
            <tr>
                <td>Project à dupliquer</td>
                <td>
                    <select class="project_id">
                        <?php foreach ($projectId2Labels as $id => $label): ?>
                            <option value="<?php echo $id; ?>"><?php echo $label; ?></option>
                        <?php endforeach; ?>
                    </select>
                </td>
            </tr>
            <tr>
                <td>Nouveau nom</td>
                <td><input type="text" class="project_name"></td>
            </tr>
        </table>
    </div>
    <div id="dialog-project-delete"
         title="Supprimer un projet"
         class="dialog-standard">
        <table>
            <tr>
                <td>Project à supprimer</td>
                <td>
                    <select class="project_id">
                        <?php foreach ($projectId2Labels as $id => $label): ?>
                            <option value="<?php echo $id; ?>"><?php echo $label; ?></option>
                        <?php endforeach; ?>
                    </select>
                </td>
            </tr>
        </table>
    </div>
    <div id="dialog-backup-create"
         title="Créer une nouvelle sauvegarde"
         class="dialog-standard">
        <table style="width: 100%;">
            <tr>
                <td>Nom</td>
                <td><input style="width: 100%;" type="text" class="name"></td>
            </tr>
        </table>
    </div>
    <div id="dialog-backup-restore"
         title="Restaurer une ancienne sauvegarde"
         class="dialog-standard">
        <table>
            <tr>
                <td>Nom</td>
                <td>
                    <select class="name">
                        <?php
                        $dir = APP_ROOT_DIR . "/backup";
                        $files = YorgDirScannerTool::getFilesWithExtension($dir, 'sql', false, true, true);
                        foreach ($files as $f): ?>
                            <option value="<?php echo $f; ?>"><?php echo $f; ?></option>
                        <?php endforeach; ?>
                    </select>
                </td>
            </tr>
        </table>
    </div>
    <div id="dialog-mail-send"
         title="Envoyer un mail"
         class="dialog-standard">
        <table class="longtable">
            <tr>
                <td>Sujet</td>
                <td>
                    <input type="text" class="subject">
                </td>
            </tr>
            <tr>
                <td style="height: 30px">Destinataires</td>
                <td class="recipients">

                </td>
            </tr>
            <tr>
                <td>message</td>
                <td>
                    <textarea rows="10" class="message"></textarea>
                </td>
            </tr>
        </table>
    </div>
</div>