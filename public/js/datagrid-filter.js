! function(t) {
    function e(e) {
        return t(e).data("treegrid") ? "treegrid" : "datagrid"
    }
    var r = t.fn.datagrid.methods.autoSizeColumn,
        i = t.fn.datagrid.methods.loadData,
        a = t.fn.datagrid.methods.appendRow,
        n = t.fn.datagrid.methods.deleteRow;
    t.extend(t.fn.datagrid.methods, {
        autoSizeColumn: function(e, i) {
            return e.each(function() {
                var e = t(this).datagrid("getPanel").find(".datagrid-header .datagrid-filter-c");
                e.css({
                    width: "1px",
                    height: 0
                }), r.call(t.fn.datagrid.methods, t(this), i), e.css({
                    width: "",
                    height: ""
                }), u(this, i)
            })
        },
        loadData: function(e, r) {
            return e.each(function() {
                t.data(this, "datagrid").filterSource = null
            }), i.call(t.fn.datagrid.methods, e, r)
        },
        appendRow: function(e, r) {
            var i = a.call(t.fn.datagrid.methods, e, r);
            return e.each(function() {
                var e = t(this).data("datagrid");
                e.filterSource && (e.filterSource.total++, e.filterSource.rows != e.data.rows && e.filterSource.rows.push(r))
            }), i
        },
        deleteRow: function(e, r) {
            return e.each(function() {
                var e = t(this).data("datagrid"),
                    i = e.options;
                if (e.filterSource && i.idField)
                    if (e.filterSource.rows == e.data.rows) e.filterSource.total--;
                    else
                        for (var a = 0; a < e.filterSource.rows.length; a++) {
                            if (e.filterSource.rows[a][i.idField] == e.data.rows[r][i.idField]) {
                                e.filterSource.rows.splice(a, 1), e.filterSource.total--;
                                break
                            }
                        }
            }), n.call(t.fn.datagrid.methods, e, r)
        }
    });
    var o = t.fn.treegrid.methods.loadData,
        l = t.fn.treegrid.methods.append,
        d = t.fn.treegrid.methods.insert,
        f = t.fn.treegrid.methods.remove;
    t.extend(t.fn.treegrid.methods, {
        loadData: function(e, r) {
            return e.each(function() {
                t.data(this, "treegrid").filterSource = null
            }), o.call(t.fn.treegrid.methods, e, r)
        },
        append: function(e, r) {
            return e.each(function() {
                var e = t(this).data("treegrid");
                if (e.options.oldLoadFilter) {
                    var i = m(this, r.data, r.parent);
                    e.filterSource.total += i.length, e.filterSource.rows = e.filterSource.rows.concat(i), t(this).treegrid("loadData", e.filterSource)
                } else l(t(this), r)
            })
        },
        insert: function(e, r) {
            return e.each(function() {
                var e = t(this).data("treegrid"),
                    i = e.options;
                if (i.oldLoadFilter) {
                    r.before || r.after;
                    var a = function(t) {
                            for (var r = e.filterSource.rows, a = 0; a < r.length; a++)
                                if (r[a][i.idField] == t) return a;
                            return -1
                        }(r.before || r.after),
                        n = a >= 0 ? e.filterSource.rows[a]._parentId : null,
                        o = m(this, [r.data], n),
                        l = e.filterSource.rows.splice(0, a >= 0 ? r.before ? a : a + 1 : e.filterSource.rows.length);
                    l = (l = l.concat(o)).concat(e.filterSource.rows), e.filterSource.total += o.length, e.filterSource.rows = l, t(this).treegrid("loadData", e.filterSource)
                } else d(t(this), r)
            })
        },
        remove: function(e, r) {
            return e.each(function() {
                var e = t(this).data("treegrid");
                if (e.filterSource)
                    for (var i = e.options, a = e.filterSource.rows, n = 0; n < a.length; n++)
                        if (a[n][i.idField] == r) {
                            a.splice(n, 1), e.filterSource.total--;
                            break
                        }
            }), f(e, r)
        }
    });
    var s = {
        filterMenuIconCls: "icon-ok",
        filterBtnIconCls: "icon-filter",
        filterBtnPosition: "right",
        filterPosition: "bottom",
        remoteFilter: !1,
        clientPaging: !0,
        showFilterBar: !0,
        filterDelay: 400,
        filterRules: [],
        filterMatchingType: "all",
        filterIncludingChild: !1,
        filterMatcher: function(r) {
            var i = e(this),
                a = t(this),
                n = t.data(this, i).options;
            if (n.filterRules.length) {
                var o = [];
                if ("treegrid" == i) {
                    var l = {};
                    for (var d in t.map(r.rows, function(e) {
                            if (c(e, e[n.idField])) {
                                l[e[n.idField]] = e;
                                for (var i = h(r.rows, e._parentId); i;) l[i[n.idField]] = i, i = h(r.rows, i._parentId);
                                if (n.filterIncludingChild) {
                                    var a = function(e, r) {
                                        var i = g(e, r),
                                            a = t.extend(!0, [], i);
                                        for (; a.length;) {
                                            var o = a.shift(),
                                                l = g(e, o[n.idField]);
                                            i = i.concat(l), a = a.concat(l)
                                        }
                                        return i
                                    }(r.rows, e[n.idField]);
                                    t.map(a, function(t) {
                                        l[t[n.idField]] = t
                                    })
                                }
                            }
                        }), l) o.push(l[d])
                } else
                    for (var f = 0; f < r.rows.length; f++) {
                        var u = r.rows[f];
                        c(u, f) && o.push(u)
                    }
                r = {
                    total: r.total - (r.rows.length - o.length),
                    rows: o
                }
            }
            return r;

            function c(e, r) {
                n.val == t.fn.combogrid.defaults.val && (n.val = s.val);
                var i = n.filterRules;
                if (!i.length) return !0;
                for (var o = 0; o < i.length; o++) {
                    var l = i[o],
                        d = a.datagrid("getColumnOption", l.field),
                        f = d && d.formatter ? d.formatter(e[l.field], e, r) : void 0,
                        u = n.val.call(a[0], e, l.field, f);
                    null == u && (u = "");
                    var c = n.operators[l.op].isMatch(u, l.value);
                    if ("any" == n.filterMatchingType) {
                        if (c) return !0
                    } else if (!c) return !1
                }
                return "all" == n.filterMatchingType
            }

            function h(t, e) {
                for (var r = 0; r < t.length; r++) {
                    var i = t[r];
                    if (i[n.idField] == e) return i
                }
                return null
            }

            function g(t, e) {
                for (var r = [], i = 0; i < t.length; i++) {
                    var a = t[i];
                    a._parentId == e && r.push(a)
                }
                return r
            }
        },
        defaultFilterType: "text",
        defaultFilterOperator: "contains",
        defaultFilterOptions: {
            onInit: function(r) {
                var i = e(r),
                    a = t(r)[i]("options"),
                    n = this.filterOptions,
                    o = t(this).attr("name"),
                    l = t(this);

                function d() {
                    var e = t(r)[i]("getFilterRule", o),
                        d = l.val();
                    if (n.options.prompt && n.options.prompt == d && (d = ""), "" != d) {
                        if (e && e.value != d || !e) {
                            var f = e ? e.op : n && n.defaultFilterOperator || a.defaultFilterOperator;
                            t(r)[i]("addFilterRule", {
                                field: o,
                                op: f,
                                value: d
                            }), t(r)[i]("doFilter")
                        }
                    } else e && (t(r)[i]("removeFilterRule", o), t(r)[i]("doFilter"))
                }
                l.data("textbox") && (l = l.textbox("textbox")), l.focus(function(e) {
                    t(r).datagrid("getPanel").find("td.datagrid-row-selected").removeClass("datagrid-row-selected")
                })
                l.data("textbox") && (l = l.textbox("textbox")), l.off(".filter").on("keydown.filter", function(e) {
                    
                    t(this);
                    this.timer && clearTimeout(this.timer), 13 == e.keyCode ? d() : a.filterDelay && (this.timer = setTimeout(function() {
                        d()
                    }, a.filterDelay))
                })
            }
        },
        filterStringify: function(t) {
            return JSON.stringify(t)
        },
        val: function(t, e, r) {
            return r || t[e]
        },
        onClickMenu: function(t, e) {}
    };

    function u(e, r) {
        var i = !1,
            a = t(e),
            n = a.datagrid("getPanel").find("div.datagrid-header"),
            o = n.find(".datagrid-header-row:not(.datagrid-filter-row)");
        (r ? n.find('.datagrid-filter[name="' + r + '"]') : n.find(".datagrid-filter")).each(function() {
            var e = t(this).attr("name"),
                r = a.datagrid("getColumnOption", e),
                n = t(this).closest("div.datagrid-filter-c"),
                l = n.find("a.datagrid-filter-btn"),
                d = o.find('td[field="' + e + '"] .datagrid-cell')._outerWidth();
            d != function(e) {
                var r = 0;
                return t(e).children(":visible").each(function() {
                    r += t(this)._outerWidth()
                }), r
            }(n) && this.filter.resize(this, d - l._outerWidth()), n.width() > r.boxWidth + r.deltaWidth - 1 && (r.boxWidth = n.width() - r.deltaWidth + 1, r.width = r.boxWidth + r.deltaWidth, i = !0)
        }), i && t(e).datagrid("fixColumnSize")
    }

    function c(e, r) {
        return t(e).datagrid("getPanel").find("div.datagrid-header").find('tr.datagrid-filter-row td[field="' + r + '"] .datagrid-filter')
    }

    function h(r, i) {
        for (var a = e(r), n = t(r)[a]("options").filterRules, o = 0; o < n.length; o++)
            if (n[o].field == i) return o;
        return -1
    }

    function g(r, i) {
        var a = e(r),
            n = t(r)[a]("options"),
            o = n.filterRules;
        if ("nofilter" == i.op) p(r, i.field);
        else {
            var l = h(r, i.field);
            l >= 0 ? t.extend(o[l], i) : o.push(i)
        }
        var d = c(r, i.field);
        if (d.length) {
            if ("nofilter" != i.op) {
                var f = d.val();
                d.data("textbox") && (f = d.textbox("getText")), f != i.value && d[0].filter.setValue(d, i.value)
            }
            var s = d[0].menu;
            if (s) {
                s.find("." + n.filterMenuIconCls).removeClass(n.filterMenuIconCls);
                var u = s.menu("findItem", n.operators[i.op].text);
                s.menu("setIcon", {
                    target: u.target,
                    iconCls: n.filterMenuIconCls
                })
            }
        }
    }

    function p(r, i) {
        var a = e(r),
            n = t(r),
            o = n[a]("options");
        if (i) {
            var l = h(r, i);
            l >= 0 && o.filterRules.splice(l, 1), d([i])
        } else {
            o.filterRules = [], d(n.datagrid("getColumnFields", !0).concat(n.datagrid("getColumnFields")))
        }

        function d(t) {
            for (var e = 0; e < t.length; e++) {
                var i = c(r, t[e]);
                if (i.length) {
                    i[0].filter.setValue(i, "");
                    var a = i[0].menu;
                    a && a.find("." + o.filterMenuIconCls).removeClass(o.filterMenuIconCls)
                }
            }
        }
    }

    function v(r) {
        var i = e(r),
            a = t.data(r, i),
            n = a.options;
        n.remoteFilter ? t(r)[i]("load") : ("scrollview" == n.view.type && a.data.firstRows && a.data.firstRows.length && (a.data.rows = a.data.firstRows), t(r)[i]("getPager").pagination("refresh", {
            pageNumber: 1
        }), t(r)[i]("options").pageNumber = 1, t(r)[i]("loadData", a.filterSource || a.data))
    }

    function m(e, r, i) {
        var a = t(e).treegrid("options");
        if (!r || !r.length) return [];
        var n = [];
        return t.map(r, function(t) {
            t._parentId = i, n.push(t), n = n.concat(m(e, t.children, t[a.idField]))
        }), t.map(n, function(t) {
            t.children = void 0
        }), n
    }

    function w(r, i) {
        i = i || [];
        var a = e(r),
            n = t.data(r, a),
            o = n.options;
        o.filterRules.length || (o.filterRules = []), o.filterCache = o.filterCache || {};
        var l = t.data(r, "datagrid").options,
            d = l.onResize;
        l.onResize = function(t, e) {
            u(r), d.call(this, t, e)
        };
        var f = l.onBeforeSortColumn;
        l.onBeforeSortColumn = function(t, e) {
            var r = f.call(this, t, e);
            return 0 != r && (o.isSorting = !0), r
        };
        var s = o.onResizeColumn;
        o.onResizeColumn = function(e, i) {
            var a = t(this).datagrid("getPanel").find(".datagrid-header .datagrid-filter-c"),
                n = a.find(".datagrid-filter:focus");
            a.css({
                width: "1px",
                height: 0
            }), t(r).datagrid("fitColumns"), o.fitColumns ? u(r) : u(r, e), a.css({
                width: "",
                height: ""
            }), n.blur().focus(), s.call(r, e, i)
        };
        var c = o.onBeforeLoad;
        if (o.onBeforeLoad = function(t, e) {
                t && (t.filterRules = o.filterStringify(o.filterRules)), e && (e.filterRules = o.filterStringify(o.filterRules));
                var r = c.call(this, t, e);
                if (0 != r && o.url)
                    if ("datagrid" == a) n.filterSource = null;
                    else if ("treegrid" == a && n.filterSource)
                    if (t) {
                        for (var i = t[o.idField], l = n.filterSource.rows || [], d = 0; d < l.length; d++)
                            if (i == l[d]._parentId) return !1
                    } else n.filterSource = null;
                return r
            }, ("detailview" == o.view.type || "scrollview" == o.view.type) && o.frozenColumns && o.frozenColumns.length) {
            var h = o.view.onBeforeRender;
            o.view.onBeforeRender = function(e) {
                if (h.call(o.view, e), !o.detailviewFilterInited) {
                    o.detailviewFilterInited = !0;
                    var r = t(e).datagrid("getColumnFields", !0);
                    o.rownumbers && r.unshift("_");
                    var i = t(e).data("datagrid").dc.header1.find(".datagrid-filter-row");
                    if (i.length < r.length) {
                        var a = t.inArray("_expander", r);
                        if (a >= 0) {
                            var n = i.children().eq(a);
                            n.length ? t('<td class="_expander"></td>').insertBefore(n) : t('<td class="_expander"></td>').appendTo(i)
                        }
                    }
                }
            }
        }

        function p(e) {
            var i = n.dc,
                l = t(r).datagrid("getColumnFields", e);
            e && o.rownumbers && l.unshift("_");
            var d = (e ? i.header1 : i.header2).find("table.datagrid-htable");
            d.find(".datagrid-filter").each(function() {
                this.filter.destroy && this.filter.destroy(this), this.menu && t(this.menu).menu("destroy")
            }), d.find("tr.datagrid-filter-row").remove();
            var f = t('<tr class="datagrid-header-row datagrid-filter-row"></tr>');
            "bottom" == o.filterPosition ? f.appendTo(d.find("tbody")) : f.prependTo(d.find("tbody")), o.showFilterBar || f.hide();
            for (var s = 0; s < l.length; s++) {
                var c = l[s],
                    h = t(r).datagrid("getColumnOption", c),
                    g = t("<td></td>").attr("field", c).appendTo(f);
                if (h && h.hidden && g.hide(), "_" != c && (!h || !h.checkbox && !h.expander)) {
                    var p = S(c);
                    p ? t(r)[a]("destroyFilter", c) : p = t.extend({}, {
                        field: c,
                        type: o.defaultFilterType,
                        options: o.defaultFilterOptions
                    });
                    var v = o.filterCache[c];
                    if (v) v.appendTo(g);
                    else {
                        v = t('<div class="datagrid-filter-c"></div>').appendTo(g);
                        var m = o.filters[p.type],
                            F = m.init(v, t.extend({
                                height: o.editorHeight
                            }, p.options || {}));
                        F.addClass("datagrid-filter").attr("name", c), F[0].filter = m, F[0].filterOptions = p, F[0].menu = w(v, p.op), p.op && p.op.length ? p.options && p.options.onInit ? p.options.onInit.call(F[0], r) : p.defaultFilterOperator && o.defaultFilterOptions.onInit.call(F[0], r) : o.defaultFilterOptions.onInit.call(F[0], r), o.filterCache[c] = v, u(r, c)
                    }
                }
            }
        }

        function w(e, i) {
            if (!i) return null;
            var a = t('<a class="datagrid-filter-btn">&nbsp;</a>').addClass(o.filterBtnIconCls);
            a.css("height", o.editorHeight), "right" == o.filterBtnPosition ? a.appendTo(e) : a.prependTo(e);
            var n = t("<div></div>").appendTo("body");
            return t.map(["nofilter"].concat(i), function(e) {
                var r = o.operators[e];
                r && t("<div></div>").attr("name", e).html(r.text).appendTo(n)
            }), n.menu({
                alignTo: a,
                onClick: function(e) {
                    var i = t(this).menu("options").alignTo,
                        a = i.closest("td[field]"),
                        n = a.attr("field"),
                        l = a.find(".datagrid-filter"),
                        d = l[0].filter.getValue(l);
                    0 != o.onClickMenu.call(r, e, i, n) && (g(r, {
                        field: n,
                        op: e.name,
                        value: d
                    }), v(r))
                }
            }), a[0].menu = n, a.on("click", {
                menu: n
            }, function(e) {
                return t(this.menu).menu("show"), !1
            }), n
        }

        function S(t) {
            for (var e = 0; e < i.length; e++) {
                var r = i[e];
                if (r.field == t) return r
            }
            return null
        }
        o.loadFilter = function(r, i) {
            var a = o.oldLoadFilter.call(this, r, i);
            return function(r, i) {
                var a = e(this),
                    n = t.data(this, a),
                    o = n.options;
                if ("datagrid" == a && t.isArray(r)) r = {
                    total: r.length,
                    rows: r
                };
                else if ("treegrid" == a && t.isArray(r)) {
                    var l = m(this, r, i);
                    r = {
                        total: l.length,
                        rows: l
                    }
                }
                if (!o.remoteFilter || o.clientPaging) {
                    if (n.filterSource) {
                        if (o.isSorting) o.isSorting = void 0;
                        else if ("datagrid" == a) n.filterSource = r;
                        else if (n.filterSource.total += r.length, n.filterSource.rows = n.filterSource.rows.concat(r.rows), i) return o.filterMatcher.call(this, r)
                    } else n.filterSource = r;
                    if (!o.remoteSort && o.sortName) {
                        var d = o.sortName.split(","),
                            f = o.sortOrder.split(","),
                            s = t(this);
                        n.filterSource.rows.sort(function(t, e) {
                            for (var r = 0, i = 0; i < d.length; i++) {
                                var a = d[i],
                                    n = f[i];
                                if (0 != (r = (s.datagrid("getColumnOption", a).sorter || function(t, e) {
                                        return t == e ? 0 : t > e ? 1 : -1
                                    })(t[a], e[a]) * ("asc" == n ? 1 : -1))) return r
                            }
                            return r
                        })
                    }(r = o.filterMatcher.call(this, {
                        total: n.filterSource.total,
                        rows: n.filterSource.rows,
                        footer: n.filterSource.footer || []
                    })).filterRows = r.rows
                }
                if (o.pagination && o.clientPaging) {
                    var u = (s = t(this))[a]("getPager");
                    if (u.pagination({
                            onSelectPage: function(t, e) {
                                o.pageNumber = t, o.pageSize = e, u.pagination("refresh", {
                                    pageNumber: t,
                                    pageSize: e
                                }), o.clientPaging ? s[a]("loadData", n.filterSource) : s[a]("reload")
                            },
                            onBeforeRefresh: function() {
                                return s[a]("reload"), !1
                            }
                        }), "datagrid" == a) {
                        var c = p(r.rows);
                        o.pageNumber = c.pageNumber, r.rows = c.rows
                    } else {
                        var h = [],
                            g = [];
                        t.map(r.rows, function(t) {
                            t._parentId ? g.push(t) : h.push(t)
                        }), r.total = h.length, c = p(h), o.pageNumber = c.pageNumber, r.rows = c.rows.concat(g)
                    }
                }
                return t.map(r.rows, function(t) {
                    t.children = void 0
                }), r;

                function p(t) {
                    for (var e = [], r = o.pageNumber; r > 0;) {
                        var i = (r - 1) * parseInt(o.pageSize),
                            a = i + parseInt(o.pageSize);
                        if ((e = t.slice(i, a)).length) break;
                        r--
                    }
                    return {
                        pageNumber: r > 0 ? r : 1,
                        rows: e
                    }
                }
            }.call(this, a, i)
        }, n.dc.view2.children(".datagrid-header").off(".filter").on("focusin.filter", function(e) {
            var r = t(this);
            setTimeout(function() {
                n.dc.body2._scrollLeft(r._scrollLeft())
            }, 0)
        }), t("#datagrid-filter-style").length || t("head").append('<style id="datagrid-filter-style">a.datagrid-filter-btn{display:inline-block;width:22px;height:100%;margin:0;vertical-align:middle;cursor:pointer;opacity:0.6;filter:alpha(opacity=60);}a:hover.datagrid-filter-btn{opacity:1;filter:alpha(opacity=100);}.datagrid-filter-row .textbox,.datagrid-filter-row .textbox .textbox-text{-moz-border-radius:0;-webkit-border-radius:0;border-radius:0;}.datagrid-filter-row input{margin:0;-moz-border-radius:0;-webkit-border-radius:0;border-radius:0;}.datagrid-filter-c{overflow:hidden}.datagrid-filter-cache{position:absolute;width:10px;height:10px;left:-99999px;}</style>'), p(!0), p(), o.fitColumns && setTimeout(function() {
            u(r)
        }, 0), t.map(o.filterRules, function(t) {
            g(r, t)
        })
    }
    t.extend(t.fn.datagrid.defaults, s), t.extend(t.fn.treegrid.defaults, s), t.fn.datagrid.defaults.filters = t.extend({}, t.fn.datagrid.defaults.editors, {
        label: {
            init: function(e, r) {
                return t("<span></span>").appendTo(e)
            },
            getValue: function(e) {
                return t(e).html()
            },
            setValue: function(e, r) {
                t(e).html(r)
            },
            resize: function(e, r) {
                t(e)._outerWidth(r)._outerHeight(22)
            }
        }
    }), t.fn.treegrid.defaults.filters = t.fn.datagrid.defaults.filters, t.fn.datagrid.defaults.operators = {
        nofilter: {
            text: "No Filter"
        },
        contains: {
            text: "Contains",
            isMatch: function(t, e) {
                return t = String(t), e = String(e), t.toLowerCase().indexOf(e.toLowerCase()) >= 0
            }
        },
        equal: {
            text: "Equal",
            isMatch: function(t, e) {
                return t == e
            }
        },
        notequal: {
            text: "Not Equal",
            isMatch: function(t, e) {
                return t != e
            }
        },
        beginwith: {
            text: "Begin With",
            isMatch: function(t, e) {
                return t = String(t), e = String(e), 0 == t.toLowerCase().indexOf(e.toLowerCase())
            }
        },
        endwith: {
            text: "End With",
            isMatch: function(t, e) {
                return t = String(t), e = String(e), -1 !== t.toLowerCase().indexOf(e.toLowerCase(), t.length - e.length)
            }
        },
        less: {
            text: "Less",
            isMatch: function(t, e) {
                return t < e
            }
        },
        lessorequal: {
            text: "Less Or Equal",
            isMatch: function(t, e) {
                return t <= e
            }
        },
        greater: {
            text: "Greater",
            isMatch: function(t, e) {
                return t > e
            }
        },
        greaterorequal: {
            text: "Greater Or Equal",
            isMatch: function(t, e) {
                return t >= e
            }
        }
    }, t.fn.treegrid.defaults.operators = t.fn.datagrid.defaults.operators, t.extend(t.fn.datagrid.methods, {
        isFilterEnabled: function(r) {
            var i = e(r[0]);
            return !!t.data(r[0], i).options.oldLoadFilter
        },
        enableFilter: function(r, i) {
            return r.each(function() {
                var r = e(this),
                    a = t.data(this, r).options;
                if (a.oldLoadFilter) {
                    if (!i) return;
                    t(this)[r]("disableFilter")
                }
                a.oldLoadFilter = a.loadFilter, w(this, i), t(this)[r]("resize"), a.filterRules.length && (a.remoteFilter ? v(this) : a.data && v(this))
            })
        },
        disableFilter: function(r) {
            return r.each(function() {
                var r = e(this),
                    i = t.data(this, r),
                    a = i.options;
                if (a.oldLoadFilter) {
                    var n = t(this).data("datagrid").dc,
                        o = n.view.children(".datagrid-filter-cache");
                    for (var l in o.length || (o = t('<div class="datagrid-filter-cache"></div>').appendTo(n.view)), a.filterCache) t(a.filterCache[l]).appendTo(o);
                    var d = i.data;
                    i.filterSource && (d = i.filterSource, t.map(d.rows, function(t) {
                        t.children = void 0
                    })), n.header1.add(n.header2).find("tr.datagrid-filter-row").remove(), a.loadFilter = a.oldLoadFilter || void 0, a.oldLoadFilter = null, t(this)[r]("resize"), t(this)[r]("loadData", d)
                }
            })
        },
        destroyFilter: function(r, i) {
            return r.each(function() {
                var r = e(this),
                    a = t.data(this, r).options;
                if (i) o(i);
                else {
                    for (var n in a.filterCache) o(n);
                    t(this).datagrid("getPanel").find(".datagrid-header .datagrid-filter-row").remove(), t(this).data("datagrid").dc.view.children(".datagrid-filter-cache").remove(), a.filterCache = {}, t(this)[r]("resize"), t(this)[r]("disableFilter")
                }

                function o(e) {
                    var r = t(a.filterCache[e]),
                        i = r.find(".datagrid-filter");
                    if (i.length) {
                        var n = i[0].filter;
                        n.destroy && n.destroy(i[0])
                    }
                    r.find(".datagrid-filter-btn").each(function() {
                        t(this.menu).menu("destroy")
                    }), r.remove(), a.filterCache[e] = void 0
                }
            })
        },
        getFilterRule: function(r, i) {
            return function(r, i) {
                var a = e(r),
                    n = t(r)[a]("options").filterRules,
                    o = h(r, i);
                return o >= 0 ? n[o] : null
            }(r[0], i)
        },
        addFilterRule: function(t, e) {
            return t.each(function() {
                g(this, e)
            })
        },
        removeFilterRule: function(t, e) {
            return t.each(function() {
                p(this, e)
            })
        },
        doFilter: function(t) {
            return t.each(function() {
                v(this)
            })
        },
        getFilterComponent: function(t, e) {
            return c(t[0], e)
        },
        resizeFilter: function(t, e) {
            return t.each(function() {
                u(this, e)
            })
        }
    })
}(jQuery);