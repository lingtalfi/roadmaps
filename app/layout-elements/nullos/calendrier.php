<?php


//--------------------------------------------
// CONFIG
//--------------------------------------------
use AssetsList\AssetsList;
use Project\Project;
use Util\GeneralUtil;


$projectId = 1;
$periodStartDate = array_key_exists('periodStartDate', $_SESSION) ? $_SESSION['periodStartDate'] : $projectStartDate . " 00:00:00";
$periodInterval = array_key_exists('periodInterval', $_SESSION) ? (int)$_SESSION['periodInterval'] : 86400;
$periodNbSegments = array_key_exists('periodNbSegments', $_SESSION) ? (int)$_SESSION['periodNbSegments'] : 30;


//a($periodStartDate, $periodInterval, $periodNbSegments);


//--------------------------------------------
// SCRIPT
//--------------------------------------------
$projectName = Project::getName($projectId);
$projectStartDate = Project::getStartDate($projectId);


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
                <?php for ($i = 1; $i <= 100; $i++):
                    $s = ($i === $periodNbSegments) ? 'selected="selected"' : '';
                    ?>
                    <option <?php echo $s; ?> value="<?php echo $i; ?>"><?php echo $i; ?></option>
                <?php endfor; ?>
            </select>
        </label>
    </div>
</div>
<div class="panes-container" style="height: 100%">
    <div id="three">
        <?php


        $p = \Period\Period::create();
        $p->setStartDate($periodStartDate);
        $p->setEndDate(gmdate("Y-m-d H:i:s", GeneralUtil::gmMysqlToTime($periodStartDate) + $periodInterval * $periodNbSegments));
        $p->setInterval($periodInterval);


        $helper = new \Period\InlinePeriodHelper($p);


        $tasks = \Task\TaskUtil::getTasksByProject(1, $p);


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

        ?>
        <div class="roadmaps" id="roadmaps">
            <table class="roadmaps-table" id="roadmaps-table">
                <tr>
                    <th class="first-column-cell">
                        <span class="label">
                        <?php echo $projectName; ?>
                            </span>

                        <button class="add-child-trigger"><i class="material-icons add-child-trigger">add</i>
                        </button>

                    </th>
                    <th style="clear: both;">Start date</th>
                    <th>End date</th>
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
                    ?>
                    <tr class="<?php echo $sOddEven; ?> level-<?php echo $task['level']; ?> parent-open"
                        data-level="<?php echo $task['level']; ?>"
                        data-color="<?php echo htmlspecialchars($task['color']); ?>"
                    >
                        <td<?php echo $style; ?> class="infocell">

                            <div class="first-column-cell">
                                <?php if (true === $task['hasChildren']): ?>
                                    <button class="toggler">-</button>
                                <?php endif; ?>
                                <span class="label task-info-trigger"><span
                                            class="labelonly task-info-trigger"><?php echo $task['label'] . '</span>' . " (" . $task['id'] . ")"; ?></span>

                                <button class="add-child-trigger"><i
                                            class="material-icons add-child-trigger">add</i></button>


                                <button class="remove-child-trigger"><i class="material-icons remove-child-trigger">delete</i>
                                </button>


                                <div class="sort">
                                    <button class="sort-up-trigger"><i class="material-icons sort-up-trigger">arrow_drop_up</i>
                                    </button>
                                    <button class="sort-down-trigger"><i class="material-icons sort-down-trigger">arrow_drop_down</i>
                                    </button>
                                </div>
                            </div>

                        </td>
                        <td data-id="<?php echo $task['id']; ?>"
                            class="infocell start-date-update-trigger"
                            data-date="<?php echo $task['start_date']; ?>"
                            title="<?php echo $task['start_date']; ?>"><?php echo justDate($task['start_date']); ?></td>
                        <td data-id="<?php echo $task['id']; ?>"
                            class="infocell end-date-update-trigger"
                            data-date="<?php echo $task['end_date']; ?>"
                            title="<?php echo $task['end_date']; ?>"><?php echo justDate($task['end_date']); ?></td>

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

<script>


    function toggleChildrenTasks(jToggler) {
        var jTr = jToggler.closest("tr");
        var hasOpen = jTr.hasClass("parent-open");
        if (true === hasOpen) {
            jToggler.text("+");
            jTr.removeClass("parent-open");
        }
        else {
            jToggler.text("-");
            jTr.addClass("parent-open");
        }
        var aChildren = getChildrenTasksByToggler(jTr);

        for (var i in aChildren) {
            var jChildren = aChildren[i];
            if (true === hasOpen) {
                jChildren.removeClass("open");
                jChildren.addClass("close");
            }
            else {
                jChildren.addClass("open");
                jChildren.removeClass("close");
            }
        }
    }

    /**
     * Children are subsequent items with higher level
     */
    function getChildrenTasksByToggler(jTr) {
        var level = jTr.attr("data-level");
        var aTrs = [];
        while (true) {
            var jNextTr = jTr.next();
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
            jTr = jNextTr;
        }
        return aTrs;
    }

    $(document).ready(function () {





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


        //----------------------------------------
        // TABLE
        //----------------------------------------
        var jTable = $("#roadmaps-table");
        $("body").on('click', function (e) {
            var jTarget = $(e.target);
            if (jTarget.hasClass("toggler")) {
                e.preventDefault();
                toggleChildrenTasks(jTarget);
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
                        my: "center",
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


                            $.post('/services/roadmaps.php?action=calendrier-task-create', {
                                'projectId': <?php echo $projectId; ?>,
                                'parentId': parentId,
                                'label': label,
                                'date': date,
                                'hour': hour,
                                'minute': minute,
                                'duration': duree,
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
            else if (jTarget.hasClass("task-info-trigger")) {
                e.preventDefault();
                var jTr = jTarget.closest('tr');
                var id = jTr.attr("data-id");
                var color = jTr.attr("data-color");
                var label = jTr.find(".labelonly").text();

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
                        var jColor = jDial.find('.color');
                        var jSpreadColor = jDial.find('.apply-color-to-children');
                        jColor.val(color);
                        var aChildrenTrs = getChildrenTasksByToggler(jTr);


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
                            var spread = jDial.find('.apply-color-to-children').prop("checked");

                            $.post('/services/roadmaps.php?action=calendrier-task-update', {
                                'id': id,
                                'label': label,
                                'color': color,
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
        });


//        $(document).tooltip();


        //----------------------------------------
        // SORT ARROWS
        //----------------------------------------
        function updateSortArrows() {
            jTable.find('tr').each(function () {
                var hasPrevSibling = trHasPrevSibling($(this));
                var hasNextSibling = trHasNextSibling($(this));


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
            });
        }

        function trHasPrevSibling(jTr) {
            var level = jTr.attr("data-level");
            var jPrevSiblings = jTr.prevAll("[data-level=" + level + "]");
            return (jPrevSiblings.length > 0);
        }

        function trHasNextSibling(jTr) {
            var level = jTr.attr("data-level");
            var jNextSiblings = jTr.nextAll("[data-level=" + level + "]");
            return (jNextSiblings.length > 0);
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
            plots: plots
        });
        oCalendar.draw(function () {
            updateSortArrows();
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
         title="Créer une tâche"
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
                <td>Ajouter</td>
                <td><select class="position">
                        <option value="first">au début</option>
                        <option value="last">à la fin</option>
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
                <td>Couleur</td>
                <td><input type="color" class="color"></td>
            </tr>
            <tr>
                <td>Appliquer la couleur aux enfants également</td>
                <td><input type="checkbox" class="apply-color-to-children"></td>
            </tr>
        </table>
    </div>
</div>