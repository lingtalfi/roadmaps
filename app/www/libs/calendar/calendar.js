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
            this.maxIndex = 0;
            this.nbNonThTime = 0;
            this.jFirst = null;
            this.jDrag = null;
            this.jDragTr = null;
            this.aChildrenTrs = null;
            this.aParentTrs = null;
            this.clickIndex = null;
        };


        window.Calendar.prototype = {
            draw: function () {
                this.prepare();


                var tasks = this.options.tasks;
                for (var i in tasks) {
                    var task = tasks[i];
                    this.drawTask(task);
                }
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
                    var isFilled = isTaskFilled(time, task);
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
                        var isNextFilled = isTaskFilled(nextPlot, task);
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
                        // timeStart: task['timeStart'],
                        // timeEnd: task['timeEnd'],
                        plotStart: getPlotIndex(task['timeStart'], this.plots, true),
                        plotEnd: getPlotIndex(task['timeEnd'], this.plots, false)
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
                        // COMMIT GUI
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
                            originalTask.plotStart = redraw.plotStart;
                            originalTask.plotEnd = redraw.plotEnd;


                        });


                    }
                    $(window).off('mousemove.calendar');
                });


            },
            startDragging: function (e) {

                var zis = this;
                this.jDragTr = $(e.target).closest("tr");

                if ('left' === this.dragType) {
                    this.jDrag = $(e.target).closest("td");
                    this.maxIndex = this.getMaxIndex(e);
                    this.jFirst = $(e.target).closest("tr").find("td.cell:first");
                    this.aChildrenTrs = this.getChildrenTrs(this.jDrag);
                    this.aParentTrs = this.getParentTrs(this.jDrag);
                    this.clickIndex = this.getTokenIndexByMouse(e);

                    $(window).on('mousemove.calendar', function (e) {
                        zis.moveLeft(e);
                    });
                }


                // this.jDrag = $(e.target).closest("td");
                // aChildrenTrs = getChildrenTrs(jDrag);
                // aParentTrs = getParentTrs(jDrag);
                // jFirst = $(e.target).closest("tr").find("td.cell:first");
                //
                //
                // startIndex = getStartIndex(e);
                // endIndex = getEndIndex(e);
                //
                // if ("left" === dragType) {
                //     window.addEventListener('mousemove', moveLeft);
                // }
                // else if ("right" === dragType) {
                //     window.addEventListener('mousemove', moveRight);
                // }
                // else if ("grab" === dragType) {
                //     clickIndex = getTokenIndexByMouse(e);
                //     aIndexesTree = getIndexTree(jDrag);
                //     window.addEventListener('mousemove', moveGrab);
                // }
            },
            moveLeft: function (e) {

                this.currentIndex = this.getTokenIndexByMouse(e);

                var offset = this.currentIndex - this.clickIndex;
                var task = this.getTask(this.jDragTr);

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
                // todo here:
                // if (null !== this.aParentTrs) {
                //     for (var i in this.aParentTrs) {
                //         var jTr = this.aParentTrs[i];
                //         var trTask = this.getTask(jTr);
                //         var plotStart = trTask.plotStart;
                //         if (start > plotStart) {
                //             plotStart = start;
                //         }
                //         var trDrawTask = {
                //             plotStart: plotStart,
                //             plotEnd: trTask.plotEnd
                //         };
                //         this.redrawTask(jTr, trDrawTask);
                //     }
                // }

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
            },
            debug: function (e) {
                screenDebug({
                    mousePageX: e.pageX,
                    currentIndex: this.currentIndex,
                    maxIndex: this.maxIndex,
                    indexOffset: this.indexOffset
                });
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

        function isTaskFilled(time, task) {
            return (time >= task.timeStart && time < task.timeEnd);
        }

        function getPlotIndex(time, plots, isStart) {
            var broken = false;
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
        }


    })();
}

