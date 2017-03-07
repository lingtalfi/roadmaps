if ("undefined" === typeof window.Calendar) {
    (function () {
        window.Calendar = function (options) {
            this.options = Object.assign({
                table: null,
                offsetToFirstTd: 3,
                tasks: {},
                periodInterval: 86400,
                plots: []
            }, options);

            this.jTable = this.options.table;
            this.plots = this.options.plots;
            this.tasks = null;
            this.jFirstTr = null;


            //----------------------------------------
            // DRAG VARS
            //----------------------------------------
            this.dragType = null;
            this.reversedThTimeOffsets = 0;
            this.nbIndexes = 0;
            this.plotsMaxIndex = 0;
            this.currentIndex = 0;
            this.indexOffset = 0;
            this.nbNonThTime = 0;
            this.jFirst = null;
            this.jDrag = null;
            this.jDragTr = null;
            this.aChildrenTrs = null;
            this.aParentTrs = null;
            this.clickIndex = null;
            this.updatedId = null;
            this.currentTask = null;
        };


        window.Calendar.prototype = {
            draw: function (fnDrawAfter) {
                this.prepare();

                var tasks = this.options.tasks;
                for (var i in tasks) {
                    var task = tasks[i];
                    this.drawTask(task);
                }
                fnDrawAfter && fnDrawAfter();
            },
            drawTask: function (task) {
                var plots = this.options.plots;
                var jTd = this.getFirstTdByTask(task);
                var cptFilled = 0;
                for (var i in plots) {
                    var time = plots[i];

                    //----------------------------------------
                    // FILLED?
                    //----------------------------------------
                    var isFilled = isCellFilled(time, task);
                    // console.log(jTd, isFilled, time, task['timeStart'], task['timeEnd']);
                    if (true === isFilled) {
                        jTd.addClass("filled");
                        cptFilled++;
                    }
                    else {
                        jTd.removeClass("filled");
                    }

                    //----------------------------------------
                    // BORDERS?
                    //----------------------------------------
                    var hasLeftBorder = false;
                    if (isFilled) {
                        if (1 === cptFilled) { // first filled
                            hasLeftBorder = true;
                            if ('0' === i) {
                                var baseTime = time;
                                var offset = 0;
                                while (true) {
                                    baseTime -= this.options.periodInterval;
                                    if (baseTime < task.timeStart) {
                                        break;
                                    }
                                    offset++;
                                }
                                if (offset > 0) {
                                    hasLeftBorder = false;
                                }
                            }
                        }
                    }
                    if (true === hasLeftBorder) {
                        jTd.addClass("left-border");
                    }
                    else {
                        jTd.removeClass("left-border");
                    }

                    var hasRightBorder = false;
                    if (isFilled) {
                        var nextIndex = parseInt(i) + 1;
                        var nextPlot = 0;
                        if (nextIndex in plots) {
                            nextPlot = plots[nextIndex];
                        }
                        else {
                            nextPlot = plots[i] + this.options.periodInterval;
                        }
                        var isNextFilled = isCellFilled(nextPlot, task);
                        if (false === isNextFilled) {
                            hasRightBorder = true;
                        }
                    }
                    if (true === hasRightBorder) {
                        jTd.addClass("right-border");
                    }
                    else {
                        jTd.removeClass("right-border");
                    }


                    jTd = jTd.next();
                }
            },
            getFirstTdByTask: function (task) {
                var jTr = this.getTrByTask(task);
                return jTr.find("td.cell:first");
            },
            getTrByTask: function (task) {
                return this.jTable.find("tr[data-id=" + task.id + "]");
            },
            prepare: function () {
                this.jFirstTr = getFirstTaskTr(this.jTable);
                var jTr = this.jFirstTr;
                this.tasks = {};
                for (var i in this.options.tasks) {
                    var task = this.options.tasks[i];

                    var newTask = {
                        id: task['id'],
                        // dateStart: task['start_date'],
                        timeStart: task['timeStart'],
                        timeEnd: task['timeEnd'],
                        plotStart: this.getPlotIndex(task["id"], task['timeStart'], this.plots, true),
                        plotEnd: this.getPlotIndex(task["id"], task['timeEnd'], this.plots, false)
                    };


                    this.tasks[task['id']] = newTask;
                    jTr.attr("data-id", task['id']);
                    jTr.data("original", newTask);
                    jTr = jTr.next();
                }

                // collection of th's offsets, so that we can then estimate the position of the mouse
                var thTimeOffsets = [];
                this.jTable.find('.th-time').each(function () {
                    thTimeOffsets.push($(this).offset().left);
                });
                this.reversedThTimeOffsets = thTimeOffsets.slice().reverse();
                this.nbIndexes = this.reversedThTimeOffsets.length;
                this.plotsMaxIndex = this.nbIndexes - 1;


                var nbTh = this.jTable.find('th').length;
                var nbThTime = this.jTable.find('th.th-time').length;
                this.nbNonThTime = nbTh - nbThTime;
            },
            listen: function () {


                var zis = this;

                $("body").on('mousedown', function (e) {
                    var jTarget = $(e.target);
                    if (jTarget.hasClass("token-resize-handle")) {
                        e.preventDefault();
                        if (jTarget.hasClass("token-resize-handle-left")) {
                            zis.dragType = "left";
                        }
                        else {
                            zis.dragType = "right";
                        }
                        zis.startDragging(e);
                    }
                    else if (jTarget.hasClass("token-grab-handle")) {
                        e.preventDefault();
                        zis.dragType = "grab";
                        zis.startDragging(e);
                    }
                });


                window.addEventListener('mouseup', function (e) {
                    if (null !== zis.jDrag) {
                        zis.jDrag = null;

                        //----------------------------------------
                        // COMMIT GUI AND MODEL
                        //----------------------------------------
                        zis.jTable.find('tr.redrawn').each(function () {
                            var redraw = $(this).data("redrawn");
                            var original = $(this).data("original");

                            $(this).find('.left-border, .right-border').removeClass('left-border right-border');
                            if (redraw.plotStart >= 0) {
                                $(this).find("td.filled:first").addClass('left-border');
                            }
                            if (redraw.plotEnd <= zis.plotsMaxIndex) {
                                $(this).find("td.filled:last").addClass('right-border');
                            }
                            $(this).removeClass('redrawn');
                            $(this).data('redrawn', null);


                            // now apply into gui memory
                            var originalTask = zis.getTask($(this));
                            var originalPlotStart = originalTask.plotStart;
                            var originalPlotEnd = originalTask.plotEnd;
                            originalTask.plotStart = redraw.plotStart;
                            originalTask.plotEnd = redraw.plotEnd;

                            // now update the db
                            var id = getTaskId($(this));
                            if (id === zis.updatedId) {
                                /**
                                 * To update the db, we need to pass it the number of seconds that we have
                                 * added/removed to the original event.
                                 *
                                 */
                                var offsetStart = zis.options.periodInterval * (redraw.plotStart - originalPlotStart);
                                var offsetEnd = zis.options.periodInterval * (redraw.plotEnd - originalPlotEnd);

                                var url = "/services/roadmaps.php?action=calendrier-update-" + zis.dragType;




                                $.post(url, {
                                    id: id,
                                    offsetLeft: offsetStart,
                                    offsetRight: offsetEnd,
                                    alignedStart: originalTask.timeStart,
                                    alignedEnd: originalTask.timeEnd
                                }, function (data) {
                                    if ("ok" === data) {
                                        window.location.reload();
                                    }
                                }, 'json');
                            }

                        });
                    }
                    $(window).off('mousemove.calendar');
                });


            },
            startDragging: function (e) {

                var zis = this;
                this.jDragTr = $(e.target).closest("tr");
                this.updatedId = getTaskId(this.jDragTr);

                if ('left' === this.dragType) {
                    this.jDrag = $(e.target).closest("td");
                    this.jFirst = $(e.target).closest("tr").find("td.cell:first");
                    this.aChildrenTrs = this.getChildrenTrs(this.jDrag);
                    this.aParentTrs = this.getParentTrs(this.jDrag);
                    this.clickIndex = this.getTokenIndexByMouse(e);


                    $(window).on('mousemove.calendar', function (e) {
                        zis.moveLeft(e);
                    });
                }
                else if ('right' === this.dragType) {
                    this.jDrag = $(e.target).closest("td");
                    this.jFirst = $(e.target).closest("tr").find("td.cell:first");
                    this.aChildrenTrs = this.getChildrenTrs(this.jDrag);
                    this.aParentTrs = this.getParentTrs(this.jDrag);
                    this.clickIndex = this.getTokenIndexByMouse(e);
                    $(window).on('mousemove.calendar', function (e) {
                        zis.moveRight(e);
                    });
                }
                else if ('grab' === this.dragType) {
                    this.jDrag = $(e.target).closest("td");
                    this.jFirst = $(e.target).closest("tr").find("td.cell:first");
                    this.aChildrenTrs = this.getChildrenTrs(this.jDrag);
                    this.aParentTrs = this.getParentTrs(this.jDrag);
                    this.clickIndex = this.getTokenIndexByMouse(e);
                    $(window).on('mousemove.calendar', function (e) {
                        zis.moveGrab(e);
                    });
                }
            },
            moveLeft: function (e) {

                this.currentIndex = this.getTokenIndexByMouse(e);

                var offset = this.currentIndex - this.clickIndex;
                var task = this.getTask(this.jDragTr);
                this.currentTask = task;

                var start = task["plotStart"] + offset;
                if (start > task['plotEnd']) {
                    start = task['plotEnd'];
                }
                var drawTask = {
                    plotStart: start,
                    plotEnd: task["plotEnd"]
                };


                this.redrawTask(this.jDragTr, drawTask);
                if (null !== this.aChildrenTrs) {
                    for (var i in this.aChildrenTrs) {
                        var jTr = this.aChildrenTrs[i];
                        var trTask = this.getTask(jTr);
                        var plotStart = trTask.plotStart;
                        if (start > plotStart) {
                            plotStart = start;
                        }
                        var trDrawTask = {
                            plotStart: plotStart,
                            plotEnd: trTask.plotEnd
                        };
                        this.redrawTask(jTr, trDrawTask);
                    }
                }

                if (null !== this.aParentTrs) {
                    for (var i in this.aParentTrs) {
                        var jTr = this.aParentTrs[i];
                        var trTask = this.getTask(jTr);
                        var plotStart = trTask.plotStart;
                        if (start < plotStart) {
                            plotStart = start;
                        }
                        var trDrawTask = {
                            plotStart: plotStart,
                            plotEnd: trTask.plotEnd
                        };
                        this.redrawTask(jTr, trDrawTask);
                    }
                }

            },
            moveRight: function (e) {

                this.currentIndex = this.getTokenIndexByMouse(e);
                var offset = this.currentIndex - this.clickIndex;
                var task = this.getTask(this.jDragTr);
                this.currentTask = task;

                var end = task["plotEnd"] + offset;
                if (end < task['plotStart']) {
                    end = task['plotStart'];
                }
                var drawTask = {
                    plotStart: task["plotStart"],
                    plotEnd: end
                };

                this.redrawTask(this.jDragTr, drawTask);

                if (null !== this.aChildrenTrs) {
                    for (var i in this.aChildrenTrs) {
                        var jTr = this.aChildrenTrs[i];
                        var trTask = this.getTask(jTr);
                        var plotEnd = trTask.plotEnd;
                        if (end < plotEnd) {
                            plotEnd = end;
                        }
                        var trDrawTask = {
                            plotStart: trTask.plotStart,
                            plotEnd: plotEnd
                        };
                        this.redrawTask(jTr, trDrawTask);
                    }
                }

                if (null !== this.aParentTrs) {
                    for (var i in this.aParentTrs) {
                        var jTr = this.aParentTrs[i];
                        var trTask = this.getTask(jTr);
                        var plotEnd = trTask.plotEnd;
                        if (end > plotEnd) {
                            plotEnd = end;
                        }
                        var trDrawTask = {
                            plotStart: trTask.plotStart,
                            plotEnd: plotEnd
                        };
                        this.redrawTask(jTr, trDrawTask);
                    }
                }

            },
            moveGrab: function (e) {

                this.currentIndex = this.getTokenIndexByMouse(e);

                var offset = this.currentIndex - this.clickIndex;
                var task = this.getTask(this.jDragTr);
                this.currentTask = task;
                var start = task["plotStart"] + offset;
                var end = task["plotEnd"] + offset;
                this.debug();
                var drawTask = {
                    plotStart: start,
                    plotEnd: end
                };
                this.redrawTask(this.jDragTr, drawTask);

                if (null !== this.aChildrenTrs) {
                    for (var i in this.aChildrenTrs) {
                        var jTr = this.aChildrenTrs[i];
                        var trTask = this.getTask(jTr);
                        var plotStart = trTask.plotStart;
                        var plotEnd = trTask.plotEnd;


                        plotStart += offset;
                        plotEnd += offset;

                        var trDrawTask = {
                            plotStart: plotStart,
                            plotEnd: plotEnd
                        };
                        this.redrawTask(jTr, trDrawTask);
                    }
                }

                if (null !== this.aParentTrs) {
                    for (var i in this.aParentTrs) {
                        var jTr = this.aParentTrs[i];
                        var trTask = this.getTask(jTr);
                        var plotStart = trTask.plotStart;
                        var plotEnd = trTask.plotEnd;


                        if (offset > 0 && end > plotEnd) {
                            plotEnd = end;
                        }
                        if (offset < 0 && start < plotStart) {
                            plotStart = start;
                        }

                        var trDrawTask = {
                            plotStart: plotStart,
                            plotEnd: plotEnd
                        };
                        this.redrawTask(jTr, trDrawTask);
                    }
                }


            },
            redrawTask: function (jTr, drawTask) {
                jTr.data('redrawn', drawTask);
                jTr.addClass('redrawn');
                var jTd = jTr.find(".cell:first");
                var start = drawTask.plotStart;
                var end = drawTask.plotEnd;
                for (var i in this.plots) {
                    if (i >= start && i <= end) {
                        jTd.addClass('filled');
                    }
                    else {
                        jTd.removeClass('filled');
                    }
                    jTd = jTd.next();
                }
            },
            getTask: function (jTr) {
                var id = jTr.attr("data-id");
                return this.tasks[id];
            },
            getTokenIndexByMouse: function (e) {
                var xpos = e.pageX;
                for (var i in this.reversedThTimeOffsets) {
                    if (this.reversedThTimeOffsets[i] < xpos) {
                        break;
                    }
                }
                return this.nbIndexes - 1 - i;
            },
            getMaxIndex: function (e) {
                var jEl = $(e.target).closest("tr").find('td.filled:last');
                return jEl.index() - this.nbNonThTime;
            },
            getChildrenTrs: function (jTd) {
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
            },
            getParentTrs: function (jTd) {
                var jTr = jTd.parent();
                var level = parseInt(jTr.attr("data-level"));
                var ret = [];
                while (level > 0) {
                    jTr = jTr.prev();
                    if (jTr.length) {
                        var taskLevel = parseInt(jTr.attr("data-level"));
                        if (taskLevel < level) {
                            ret.push(jTr);
                            level = taskLevel;
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
            },
            getPlotIndex: function (taskId, time, plots, isStart) {
                var broken = false;
                if (time < plots[0]) {
                    var index = 0;
                    var plotTime = plots[0];
                    while (time < plotTime) {
                        plotTime -= this.options.periodInterval;
                        index--;
                    }
                    return (true === isStart) ? index : index - 1;
                }
                else if (time > plots[plots.length - 1]) {
                    var index = plots.length - 1;
                    var plotTime = plots[plots.length - 1];
                    while (time > plotTime) {
                        plotTime += this.options.periodInterval;
                        index++;
                    }
                    return (true === isStart) ? index : index - 1;
                }

                for (var i in plots) {
                    if (
                        (true === isStart && time < plots[i]) ||
                        (false === isStart && time <= plots[i])
                    ) {
                        broken = true;
                        break;
                    }
                }


                return (true === broken) ? parseInt(i) - 1 : parseInt(i);
            },
            debug: function () {
                // screenDebug({
                //     currentIndex: this.currentIndex,
                //     clickIndex: this.clickIndex,
                //     currentTaskStart: this.currentTask['plotStart'],
                //     currentTaskEnd: this.currentTask['plotEnd']
                // });
            }
        };


        //----------------------------------------
        //
        //----------------------------------------
        /**
         * Returns the first tr that contains task items
         */
        function getFirstTaskTr(jTable) {
            return jTable.find("tr:first").next();
        }

        function isCellFilled(plotTime, task) {
            return (plotTime >= task.timeStart && plotTime < task.timeEnd);
        }

        function getTaskId(jTr) {
            return jTr.attr('data-id');
        }


    })();
}

