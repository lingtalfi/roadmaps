<?php


//--------------------------------------------
// CONFIG
//--------------------------------------------
use AssetsList\AssetsList;
use Project\Project;

$projectId = 1;


//--------------------------------------------
// SCRIPT
//--------------------------------------------
$projectName = Project::getName($projectId);

AssetsList::css("/style/roadmaps.css");
AssetsList::css("/libs/screendebug/css/screendebug.css");
AssetsList::js("/libs/simpledrag/simpledrag.js");
AssetsList::js("/libs/screendebug/js/screendebug.js");
AssetsList::js("/libs/calendar/calendar.js");

?>
<div class="panes-container" style="height: 100%">
    <div id="three">
        <?php

        $periodInterval = 86400;

        $p = \Period\Period::create();
        $p->setStartDate(date("Y-m-d 00:00:00"));
        $p->setEndDate(date("Y-m-d 00:00:00", time() + 30 * 86400));
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
            return date("d", $timestamp) . " " . ucfirst($monthName);
        }


        function isFilled($plotTime, $timeStart, $timeEnd)
        {
            return ($plotTime >= $timeStart && $plotTime < $timeEnd);
        }

        ?>
        <div class="roadmaps" id="roadmaps">
            <table class="roadmaps-table" id="roadmaps-table">
                <tr>
                    <th><?php echo $projectName; ?></th>
                    <th>Start date</th>
                    <th>End date</th>
                    <?php
                    $plots = $helper->getTimeScalePlots();
                    foreach ($plots as $time) {
                        ?>
                        <th class="th-time">
                            <?php echo formatDate($time, $months); ?>
                        </th>
                        <?php
                    }

                    ?>
                </tr>
                <?php
                $i = 0;
                foreach ($tasks as $task):
                    $ml = $task['level'] * 20 + 10;
                    $style = ' style="padding-left: ' . $ml . 'px"';
                    $sOddEven = (0 == $i++ % 2) ? "even" : "odd";
                    ?>
                    <tr class="<?php echo $sOddEven; ?> level-<?php echo $task['level']; ?> parent-open"
                        data-level="<?php echo $task['level']; ?>"
                    >
                        <td<?php echo $style; ?> class="infocell">
                            <?php if (true === $task['hasChildren']): ?>
                                <button class="toggler">-</button>
                            <?php endif; ?>
                            <?php echo $task['label'] . " (" . $task['id'] . ")"; ?>
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
                            <td class="cell">
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

                        var hour = parseInt(horaire.substr(0, 2));
                        var minute = parseInt(horaire.substr(3, 2));

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
        });


        $(document).tooltip();


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
        oCalendar.draw();
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
                    <?php for ($i = 0; $i < 24; $i++): ?>
                        <option value="<?php echo $i; ?>"><?php echo sprintf("%02s", $i); ?></option>
                    <?php endfor; ?>
                </select>
                H
                <select class="minute">
                    <?php for ($i = 0; $i < 60; $i++): ?>
                        <option value="<?php echo $i; ?>"><?php echo sprintf("%02s", $i); ?></option>
                    <?php endfor; ?>
                </select>
                m
            </li>
            <!--            <li style="margin-top: 10px" class="decaler">-->
            <!--                Décaler les tâches suivantes <input type="checkbox" class="decaler-checkbox">-->
            <!--            </li>-->
        </ul>
    </div>
</div>