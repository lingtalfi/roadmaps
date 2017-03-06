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
            this.currentIndex = 0;
            this.indexOffset = 0;
            this.maxIndex = 0;
            this.nbNonThTime = 0;
            this.jFirst = null;
            this.jDrag = null;
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
                    this.tasks[task['id']] = task;
                    jTr.attr("data-id", task['id']);
                    jTr = jTr.next();
                }


                // collection of th's offsets, so that we can then estimate the position of the mouse
                var thTimeOffsets = [];
                this.jTable.find('.th-time').each(function () {
                    thTimeOffsets.push($(this).offset().left);
                });
                this.reversedThTimeOffsets = thTimeOffsets.slice().reverse();
                this.nbIndexes = this.reversedThTimeOffsets.length;


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
                        // cleaning up
                        zis.jDrag.closest('tr').find("td").removeClass("left-border right-border");
                        var jTdFilled = zis.jDrag.closest('tr').find("td.filled");
                        jTdFilled.first().addClass("left-border");
                        jTdFilled.last().addClass("right-border");


                        zis.jDrag = null;


                        // update db
                        var fixedCurrentIndex = zis.currentIndex;
                        if ("left" === zis.dragType && fixedCurrentIndex > zis.maxIndex) {
                            fixedCurrentIndex = zis.maxIndex;
                        }
                        // if ("right" === zis.dragType && fixedCurrentIndex < startIndex) {
                        //     fixedCurrentIndex = startIndex;
                        // }

                        var time = zis.plots[fixedCurrentIndex];
                        // todo: update db?


                        // cleaning up and apply visual changes
                        if ("left" === zis.dragType) {

                            //----------------------------------------
                            // LEFT HANDLE
                            //----------------------------------------
                            // childrenmask
                            for (var i in zis.aChildrenTrs) {
                                var jTr = zis.aChildrenTrs[i];


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
                            for (var i in zis.aParentTrs) {
                                var jTr = zis.aParentTrs[i];
                                var jTdLastSuperfilled = jTr.find(".superfilled:first");
                                if (jTdLastSuperfilled.length) {
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
                        else if ("right" === zis.dragType) {
                            //----------------------------------------
                            // RIGHT HANDLE
                            //----------------------------------------
                            // childrenmask
                            for (var i in zis.aChildrenTrs) {
                                var jTr = zis.aChildrenTrs[i];

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
                            for (var i in zis.aParentTrs) {
                                var jTr = zis.aParentTrs[i];
                                var jTdLastSuperfilled = jTr.find(".superfilled:last");
                                if (jTdLastSuperfilled.length) {
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
                    }
                    $(window).off('mousemove.calendar');
                });


            },
            getIndexTree: function (jTd) {
                var ret = [];
                var jTr = jTd.parent();
                var jFirst = jTr.find(".filled:first");
                var jLast = jTr.find(".filled:last");
                var leftOffset = jFirst.attr("data-offset-left") ? jFirst.attr("data-offset-left") : 0;
                var rightOffset = jLast.attr("data-offset-right") ? jLast.attr("data-offset-right") : 0;
                ret.push([jTd.parent(), startIndex, endIndex, leftOffset, rightOffset]);

                for (var i in this.aChildrenTrs) {
                    var jChildTr = this.aChildrenTrs[i];
                    var jEl = jChildTr.find('td.filled:first');
                    var leftOffset = jEl.attr("data-offset-left") ? jEl.attr("data-offset-left") : 0;
                    var startInd = jEl.index() - this.nbNonThTime;
                    jEl = jChildTr.find('td.filled:last');
                    var rightOffset = jEl.attr("data-offset-right") ? jEl.attr("data-offset-right") : 0;
                    var endInd = jEl.index() - this.nbNonThTime;
                    /**
                     * startInd: index of the first filled element of the line
                     * endInd: index of the last filled element of the line
                     * leftOffset: index offset from the task's start date to the first filled element of the line
                     * rightOffset: index offset from the task's end date to the last filled element of the line
                     */
                    ret.push([jChildTr, startInd, endInd, leftOffset, rightOffset]);
                }
                return ret;
            },
            startDragging: function (e) {

                var zis = this;
                this.jDrag = $(e.target).closest("td");
                this.jFirst = $(e.target).closest("tr").find("td.cell:first");
                this.aChildrenTrs = this.getChildrenTrs(this.jDrag);
                this.aParentTrs = this.getParentTrs(this.jDrag);


                if ('left' === this.dragType) {

                    this.maxIndex = this.getMaxIndex(e);
                    $(window).on('mousemove.calendar', function (e) {
                        zis.moveLeft(e);
                    });
                }
                else if ("right" === this.dragType) {
                    this.minIndex = this.getMinIndex(e);
                    $(window).on('mousemove.calendar', function (e) {
                        zis.moveRight(e);
                    });
                }
                else if ("grab" === dragType) {
                    this.clickIndex = this.getTokenIndexByMouse(e);
                    this.aIndexesTree = this.getIndexTree(this.jDrag);
                    $(window).on('mousemove.calendar', function (e) {
                        zis.moveGrab(e);
                    });
                }
            },
            moveLeft: function (e) {
                this.currentIndex = this.getTokenIndexByMouse(e);

                this.indexOffset = this.maxIndex - this.currentIndex;

                if (this.indexOffset >= 0) {
                    this.jFirst.prev().nextAll().slice(0, this.currentIndex).removeClass("filled");
                    this.jFirst.prev().nextAll().slice(this.currentIndex, this.maxIndex).addClass("filled");
                    if (null !== this.aChildrenTrs) {
                        for (var i in this.aChildrenTrs) {
                            var jTr = this.aChildrenTrs[i];
                            jTr.find("td").removeClass("childrenmask");
                            jTr.find("td:first").nextAll(":gt(" + (this.nbNonThTime - 2) + ")").slice(0, this.currentIndex).addClass("childrenmask");
                        }
                    }
                    if (null !== this.aParentTrs) {
                        for (var i in this.aParentTrs) {
                            var jTr = this.aParentTrs[i];
                            var jLast = jTr.find("td.filled:last");
                            jLast.addClass("superfilled");
                            jLast.prevAll().not(".infocell").addClass("superfilled");
                            jTr.find("td:first").nextAll(":gt(" + (this.nbNonThTime - 2) + ")").slice(0, this.currentIndex).removeClass("superfilled");
                        }
                    }
                }
            },
            moveRight: function (e) {
                this.currentIndex = this.getTokenIndexByMouse(e);
                this.indexOffset = this.currentIndex - this.minIndex;

                if (this.indexOffset >= 0) {
                    this.jFirst.nextAll().slice(this.startIndex, this.currentIndex).addClass("filled");
                    this.jFirst.nextAll().slice(this.currentIndex).removeClass("filled");
                    if (null !== this.aChildrenTrs) {
                        for (var i in this.aChildrenTrs) {
                            var jTr = this.aChildrenTrs[i];
                            jTr.find("td").removeClass("childrenmask");
                            jTr.find("td:first").nextAll(":gt(" + this.nbNonThTime + ")").slice(this.currentIndex - 1).addClass("childrenmask");
                        }
                    }
                    if (null !== this.aParentTrs) {
                        for (var i in this.aParentTrs) {
                            var jTr = this.aParentTrs[i];
                            var jFirstTr = jTr.find("td.filled:first");
                            jFirstTr.addClass("superfilled");
                            jFirstTr.nextAll().addClass("superfilled");
                            jTr.find("td:first").nextAll(":gt(" + this.nbNonThTime + ")").slice(this.currentIndex - 1).removeClass("superfilled");
                        }
                    }
                }
            },
            moveGrab: function (e) {
                this.currentIndex = this.getTokenIndexByMouse(e);
                this.indexOffset = this.currentIndex - this.clickIndex;

                for (var i in this.aIndexesTree) {
                    var item = this.aIndexesTree[i];
                    var theStartIndex = item[1] + this.indexOffset;
                    var theEndIndex = item[2] + this.indexOffset;
                    theStartIndex -= item[3];
                    if (theStartIndex < 0) {
                        theStartIndex = 0;
                    }

                    this.redraw(item[0], theStartIndex, theEndIndex);
                }
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
            getMinIndex: function (e) {
                var jEl = $(e.target).closest("tr").find('td.filled:first');
                return jEl.index() - this.nbNonThTime;
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


    })();
}

