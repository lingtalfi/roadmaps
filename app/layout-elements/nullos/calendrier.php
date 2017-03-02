<div class="panes-container" style="height: 100%">
    <div id="three">
        <?php
        \AssetsList\AssetsList::css("/style/roadmaps.css");


        $p = \Period\Period::create();
        $p->setStartDate(date("Y-m-d H:i:s"));
        $p->setEndDate(date("Y-m-d H:i:s", time() + 30 * 86400));
        $p->setInterval(86400);


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
            <table class="roadmaps-table">
                <tr>
                    <th>Label</th>
                    <th>Start date</th>
                    <th>End date</th>
                    <?php
                    $plots = $helper->getTimeScalePlots();
                    foreach ($plots as $time) {
                        ?>
                        <th>
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
                        <td<?php echo $style; ?>>
                            <?php if (true === $task['hasChildren']): ?>
                                <button class="toggler">-</button>
                            <?php endif; ?>
                            <?php echo $task['label']; ?>
                        </td>
                        <td data-id="<?php echo $task['id']; ?>" class="start-date-update-trigger"><?php echo justDate($task['start_date']); ?></td>
                        <td data-id="<?php echo $task['id']; ?>" class="end-date-update-trigger"><?php echo justDate($task['end_date']); ?></td>

                        <?php foreach ($plots as $time):
                            $s = "";
                            if (true === isFilled($time, $task['timeStart'], $task['timeEnd'])) {
                                $s = "filled";
                            }
                            ?>
                            <td class="<?php echo $s; ?>"></td>
                        <?php endforeach; ?>
                    </tr>
                <?php endforeach; ?>
            </table>
        </div>
    </div>
    <div id="four"></div>
</div>
<script>


    function toggle(jToggler) {
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
        var aChildren = getChildrenByToggler(jTr);

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
    function getChildrenByToggler(jTr) {
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
                toggle(jTarget);
            }
            else if (jTarget.hasClass("start-date-update-trigger")) {
                e.preventDefault();

                if ('undefined' !== typeof $("#dialog-update-date").dialog('instance')) {
                    $("#dialog-update-date").dialog("close");
                }

                $("#dialog-update-date").dialog({
                    position: {
                        my: "center",
                        at: "center",
                        of: jTarget
                    },
                    width: 600,
                    open: function (event, ui) {

                    }
                });
            }

        });
    });


</script>

<div style="display: none">
    <div id="dialog-update-date" title="sds">
        zegzeog izoj
    </div>
</div>