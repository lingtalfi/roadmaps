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
                        data-level="<?php echo $task['level']; ?>">
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

                        $cpt = 0;
                        $wasLast = false;
                        foreach ($plots as $k => $time):

                            $leftOffset = 0;
                            $s = "";
                            $isFilled = isFilled($time, $task['timeStart'], $task['timeEnd']);
                            if (true === $isFilled) {
                                $s = "filled";
                            }


                            $isLeftBorder = false;
                            $isRightBorder = false;

                            if (true === $isFilled && 0 === $cpt) {
                                $cpt++;
                                if ($task['timeStart'] >= $plots[0]) {
                                    $isLeftBorder = true;
                                } else {
                                    $start = $plots[0];
                                    while (true) {
                                        $start -= $periodInterval;
                                        if ($start < $task['timeStart']) {
                                            break;
                                        }
                                        $leftOffset++;
                                    }
                                }
                            }

                            $isLast = (false === $wasLast && $cpt > 0 && array_key_exists($k + 1, $plots) && (false === isFilled($plots[$k + 1], $task['timeStart'], $task['timeEnd'])));
                            if (true === $isLast) {
                                $isRightBorder = $isLast;
                                $wasLast = true;
                            }


                            if (true === $isLeftBorder) {
                                $s .= " left-border";
                            }
                            if (true === $isRightBorder) {
                                $s .= " right-border";
                            }

                            $sOffset = "";
                            if ($leftOffset > 0) {
                                $sOffset = 'data-offset="' . $leftOffset . '"';
                            }


                            ?>
                            <td class="<?php echo $s; ?> cell" <?php echo $sOffset; ?>>
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
        var periodInterval = <?php echo $periodInterval; ?>;
        var plots = <?php echo json_encode($plots); ?>;
        var dragType = null; // left|right|grab
        var thTimeOffsets = []; // collection of th's offsets, so that we can then estimate the position of the mouse
        $("#roadmaps-table").find('.th-time').each(function () {
            thTimeOffsets.push($(this).offset().left);
        });
        var reversedThTimeOffsets = thTimeOffsets.slice().reverse();
        var nbIndexes = reversedThTimeOffsets.length;


        $("body").on('mousedown', function (e) {
            var jTarget = $(e.target);
            if (jTarget.hasClass("token-resize-handle")) {
                e.preventDefault();
                if (jTarget.hasClass("token-resize-handle-left")) {
                    dragType = "left";
                }
                else {
                    dragType = "right";
                }
                startDragging(e);
            }
            else if (jTarget.hasClass("token-grab-handle")) {
                e.preventDefault();
                dragType = "grab";
                startDragging(e);
            }
        });

        var nbTh = $("#roadmaps-table").find('th').length;
        var nbThTime = $("#roadmaps-table").find('th.th-time').length;
        var nbNonThTime = nbTh - nbThTime;


        function getTokenIndexByMouse(e) {
            var xpos = e.pageX;
            for (var i in reversedThTimeOffsets) {
                if (reversedThTimeOffsets[i] < xpos) {
                    break;
                }
            }
            return nbIndexes - 1 - i;
        }


        function getStartIndex(e) {
            var jEl = $(e.target).closest("tr").find('td.filled:first');
            return jEl.index() - nbNonThTime;
        }

        function getEndIndex(e) {
            var jEl = $(e.target).closest("tr").find('td.filled:last');
            return jEl.index() - nbNonThTime;
        }

        function getChildrenTrs(jTd) {
            var jTr = jTd.parent();
            var level = jTr.attr("data-level");
            var ret = [];
            while (true) {
                jTr = jTr.next();
                if (jTr) {
                    if (jTr.attr("data-level") > level) {
                        ret.push(jTr);
                    }
                    else {
                        break;
                    }
                }
                else {
                    break;
                }
            }
            if (ret.length > 0) {
                return ret;
            }
            return null;
        }

        function getParentTrs(jTd) {
            var jTr = jTd.parent();
            var level = parseInt(jTr.attr("data-level"));
            var ret = [];
            while (level > 0) {
                jTr = jTr.prev();
                if (jTr) {
                    var taskLevel = parseInt(jTr.attr("data-level"));
                    if (taskLevel < level) {
                        ret.push(jTr);
                    }
                    if (0 === taskLevel) {
                        break;
                    }
                }
                else {
                    break;
                }
            }
            if (ret.length > 0) {
                return ret;
            }
            return null;
        }


        var jDrag = null;
        var startIndex = 0; // index of the start_date
        var endIndex = 0; // index of the end_date
        var clickIndex = 0; // index where the user clicked
        var jFirst = null;
        var currentIndex = 0;
        var indexOffset = 0;
        var aChildrenTrs = null;
        var aParentTrs = null;
        /**
         * array of items for a given task (used in grab mode only), each item is an array containing:
         *          - 0: jTr
         *          - 1: start_date index
         *          - 2: end_date index
         *          - 3: leftOffset
         *          - 4: rightOffset
         */
        var aIndexesTree = null;


        function getIndexTree(jTd) {
            var ret = [];
            var jTr = jTd.parent();
            var jFirst = jTr.find(".filled:first");
            var jLast = jTr.find(".filled:last");
            var leftOffset = jFirst.attr("data-offset") ? jFirst.attr("data-offset") : 0;
            var rightOffset = jLast.attr("data-offset") ? jLast.attr("data-offset") : 0;
            ret.push([jTd.parent(), startIndex, endIndex, leftOffset, rightOffset]);

            for (var i in aChildrenTrs) {
                var jChildTr = aChildrenTrs[i];
                var jEl = jChildTr.find('td.filled:first');
                var leftOffset = jEl.attr("data-offset") ? jEl.attr("data-offset") : 0;
                var startInd = jEl.index() - nbNonThTime;
                jEl = jChildTr.find('td.filled:last');
                var rightOffset = jEl.attr("data-offset") ? jEl.attr("data-offset") : 0;
                var endInd = jEl.index() - nbNonThTime;
                ret.push([jChildTr, startInd, endInd, leftOffset, rightOffset]);
            }
            return ret;
        }


        function moveLeft(e) {
            currentIndex = getTokenIndexByMouse(e);
            indexOffset = endIndex - currentIndex;

            if (indexOffset >= 0) {
                jFirst.prev().nextAll().slice(0, currentIndex).removeClass("filled");
                jFirst.prev().nextAll().slice(currentIndex, endIndex).addClass("filled");
                if (null !== aChildrenTrs) {
                    for (var i in aChildrenTrs) {
                        var jTr = aChildrenTrs[i];
                        jTr.find("td").removeClass("childrenmask");
                        jTr.find("td:first").nextAll(":gt(" + (nbNonThTime - 2) + ")").slice(0, currentIndex).addClass("childrenmask");
                    }
                }
                if (null !== aParentTrs) {
                    for (var i in aParentTrs) {
                        var jTr = aParentTrs[i];
                        jTr.find("td.filled:last").next().prevAll().not(".infocell").addClass("superfilled");
                        jTr.find("td:first").nextAll(":gt(" + (nbNonThTime - 2) + ")").slice(0, currentIndex).removeClass("superfilled");
                    }
                }
            }
            debug(e);
        }

        function moveRight(e) {
            currentIndex = getTokenIndexByMouse(e);
            indexOffset = currentIndex - startIndex;

            if (indexOffset >= 0) {
                jFirst.nextAll().slice(startIndex, currentIndex).addClass("filled");
                jFirst.nextAll().slice(currentIndex).removeClass("filled");
                if (null !== aChildrenTrs) {
                    for (var i in aChildrenTrs) {
                        var jTr = aChildrenTrs[i];
                        jTr.find("td").removeClass("childrenmask");
                        jTr.find("td:first").nextAll(":gt(" + nbNonThTime + ")").slice(currentIndex - 1).addClass("childrenmask");
                    }
                }
                if (null !== aParentTrs) {
                    for (var i in aParentTrs) {
                        var jTr = aParentTrs[i];
                        jTr.find("td.filled:first").prev().nextAll().addClass("superfilled");
                        jTr.find("td:first").nextAll(":gt(" + nbNonThTime + ")").slice(currentIndex - 1).removeClass("superfilled");
                    }
                }
            }
            debug(e);
        }

        function redraw(jTr, _startIndex, _endIndex) {
            jTr.find("td").removeClass("filled left-border right-border");
            jTr.find("td.cell").each(function (index) {
                if (index >= _startIndex && index <= _endIndex) {
                    $(this).addClass("filled");
                }
            });
        }

        function moveGrab(e) {
            currentIndex = getTokenIndexByMouse(e);
            indexOffset = currentIndex - clickIndex;

            for (var i in aIndexesTree) {
                var item = aIndexesTree[i];
                var theStartIndex = item[1] + indexOffset;
                var theEndIndex = item[2] + indexOffset;
                theStartIndex -= item[3];
                if (theStartIndex < 0) {
                    theStartIndex = 0;
                }

                redraw(item[0], theStartIndex, theEndIndex);
            }
            debug(e);
        }


        function startDragging(e) {
            jDrag = $(e.target).closest("td");
            aChildrenTrs = getChildrenTrs(jDrag);
            aParentTrs = getParentTrs(jDrag);
            jFirst = $(e.target).closest("tr").find("td.cell:first");


            startIndex = getStartIndex(e);
            endIndex = getEndIndex(e);

            if ("left" === dragType) {
                window.addEventListener('mousemove', moveLeft);
            }
            else if ("right" === dragType) {
                window.addEventListener('mousemove', moveRight);
            }
            else if ("grab" === dragType) {
                clickIndex = getTokenIndexByMouse(e);
                aIndexesTree = getIndexTree(jDrag);
                window.addEventListener('mousemove', moveGrab);
            }
        }

        window.addEventListener('mouseup', function (e) {
            if (null !== jDrag) {


                // cleaning up
                jDrag.closest('tr').find("td").removeClass("left-border right-border");
                var jTdFilled = jDrag.closest('tr').find("td.filled");
                jTdFilled.first().addClass("left-border");
                jTdFilled.last().addClass("right-border");


                jDrag = null;


                // update db
                var fixedCurrentIndex = currentIndex;
                if ("left" === dragType && fixedCurrentIndex > endIndex) {
                    fixedCurrentIndex = endIndex;
                }
                if ("right" === dragType && fixedCurrentIndex < startIndex) {
                    fixedCurrentIndex = startIndex;
                }

                var time = plots[fixedCurrentIndex];
                // todo: update db?


                // cleaning up and apply visual changes
                if ("left" === dragType) {

                    //----------------------------------------
                    // LEFT HANDLE
                    //----------------------------------------
                    // childrenmask
                    for (var i in aChildrenTrs) {
                        var jTr = aChildrenTrs[i];


                        jTr.find("td.left-border").removeClass("left-border");
                        var jTdLast = jTr.find("td.childrenmask:last").next();
                        if (jTdLast && jTdLast.hasClass("filled")) {
                            jTdLast.addClass("left-border");
                        }
                        else {
                            jTr.find("td.filled:first").addClass("left-border");
                        }
                        jTr.find("td.childrenmask").removeClass("filled childrenmask");
                        jTr.find("td.right-border").not('.filled').removeClass("right-border");
                        jTr.find("td.left-border").not('.filled').removeClass("left-border");
                    }

                    // superfill
                    for (var i in aParentTrs) {
                        var jTr = aParentTrs[i];
                        var jTdLastSuperfilled = jTr.find(".superfilled:first");
                        if (jTdLastSuperfilled) {
                            var jTdLastFilled = jTr.find(".filled:first");
                            var superfillWins = (jTdLastSuperfilled.offset().left < jTdLastFilled.offset().left);
                            var jTdLast = null;
                            if (true === superfillWins) {
                                jTdLast = jTdLastSuperfilled;
                            }
                            else {
                                jTdLast = jTdLastFilled;

                            }

                            jTr.find("td.filled:last").prevUntil(jTdLast.prev()).addClass("filled");

                            jTdLast.prevAll().removeClass("filled");
                            jTr.find(".superfilled").removeClass("superfilled");
                            jTr.find(".left-border").removeClass("left-border");
                            jTr.find("td.filled:first").addClass("left-border");

                        }
                    }
                }
                else if ("right" === dragType) {
                    //----------------------------------------
                    // RIGHT HANDLE
                    //----------------------------------------
                    // childrenmask
                    for (var i in aChildrenTrs) {
                        var jTr = aChildrenTrs[i];

                        jTr.find("td.right-border").removeClass("right-border");
                        var jTdLast = jTr.find("td.childrenmask:first").prev();
                        if (jTdLast && jTdLast.hasClass("filled")) {
                            jTdLast.addClass("right-border");
                        }
                        else {
                            jTr.find("td.filled:last").addClass("right-border");
                        }
                        jTr.find("td.childrenmask").removeClass("childrenmask filled");
                        jTr.find("td.right-border").not('.filled').removeClass("right-border");
                        jTr.find("td.left-border").not('.filled').removeClass("left-border");
                    }

                    // superfill
                    for (var i in aParentTrs) {
                        var jTr = aParentTrs[i];
                        var jTdLastSuperfilled = jTr.find(".superfilled:last");
                        if (jTdLastSuperfilled) {
                            var jTdLastFilled = jTr.find(".filled:last");
                            var superfillWins = (jTdLastSuperfilled.offset().left > jTdLastFilled.offset().left);
                            var jTdLast = null;
                            if (true === superfillWins) {
                                jTdLast = jTdLastSuperfilled;
                            }
                            else {
                                jTdLast = jTdLastFilled;

                            }
                            if (jTdLast.next()) {
                                jTr.find("td.filled:first").nextUntil(jTdLast.next()).addClass("filled");
                            }
                            else {
                                jTr.find("td.filled:first").nextAll().addClass("filled");
                            }
                            jTdLast.nextAll().removeClass("filled");
                            jTr.find(".superfilled").removeClass("superfilled");
                            jTr.find(".right-border").removeClass("right-border");
                            jTr.find("td.filled:last").addClass("right-border");

                        }
                    }
                }
                else if ("grab" === dragType) {
                    console.log(aIndexesTree);
                    for(var i in aIndexesTree){
                        var item = aIndexesTree[i];
                        var realStart = item
                    }
                }


                window.removeEventListener('mousemove', moveLeft);
                window.removeEventListener('mousemove', moveRight);
                window.removeEventListener('mousemove', moveGrab);
            }
        });


        function debug(e, tmp) {
            screenDebug({
                mousePageX: e.pageX,
                startIndex: startIndex,
                endIndex: endIndex,
                currentIndex: currentIndex,
                tmp: tmp,
                indexOffset: indexOffset
            });
        }

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