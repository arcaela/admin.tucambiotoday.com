! function(e) {
	"use strict";

	function t(e, t) {
		if (this.createTextRange) {
			var a = this.createTextRange();
			a.collapse(!0), a.moveStart("character", e), a.moveEnd("character", t - e), a.select()
		} else this.setSelectionRange && (this.focus(), this.setSelectionRange(e, t))
	}

	function a(e) {
		var t = this.value.length;
		if (e = "start" == e.toLowerCase() ? "Start" : "End", document.selection) {
			var a, i, n, l = document.selection.createRange();
			return (a = l.duplicate()).expand("textedit"), a.setEndPoint("EndToEnd", l), n = (i = a.text.length - l.text.length) + l.text.length, "Start" == e ? i : n
		}
		return void 0 !== this["selection" + e] && (t = this["selection" + e]), t
	}
	var i = { codes: { 46: 127, 188: 44, 109: 45, 190: 46, 191: 47, 192: 96, 220: 92, 222: 39, 221: 93, 219: 91, 173: 45, 187: 61, 186: 59, 189: 45, 110: 46 }, shifts: { 96: "~", 49: "!", 50: "@", 51: "#", 52: "$", 53: "%", 54: "^", 55: "&", 56: "*", 57: "(", 48: ")", 45: "_", 61: "+", 91: "{", 93: "}", 92: "|", 59: ":", 39: '"', 44: "<", 46: ">", 47: "?" } };
	$.fn.number = function(e, n, l, s) {
		s = void 0 === s ? "," : s, n = void 0 === n ? 0 : n;
		var r = "\\u" + ("0000" + (l = void 0 === l ? "." : l).charCodeAt(0).toString(16)).slice(-4),
			h = new RegExp("[^" + r + "0-9]", "g"),
			u = new RegExp(r, "g");
		return !0 === e ? this.is("input:text") ? this.on({
			"keydown.format": function(e) {
				var r = $(this),
					h = r.data("numFormat"),
					u = e.keyCode ? e.keyCode : e.which,
					o = "",
					c = a.apply(this, ["start"]),
					v = a.apply(this, ["end"]),
					d = "",
					p = !1;
				if (i.codes.hasOwnProperty(u) && (u = i.codes[u]), !e.shiftKey && u >= 65 && u <= 90 ? u += 32 : !e.shiftKey && u >= 69 && u <= 105 ? u -= 48 : e.shiftKey && i.shifts.hasOwnProperty(u) && (o = i.shifts[u]), "" == o && (o = String.fromCharCode(u)), 8 != u && 45 != u && 127 != u && o != l && !o.match(/[0-9]/)) {
					var g = e.keyCode ? e.keyCode : e.which;
					if (46 == g || 8 == g || 127 == g || 9 == g || 27 == g || 13 == g || (65 == g || 82 == g || 80 == g || 83 == g || 70 == g || 72 == g || 66 == g || 74 == g || 84 == g || 90 == g || 61 == g || 173 == g || 48 == g) && !0 === (e.ctrlKey || e.metaKey) || (86 == g || 67 == g || 88 == g) && !0 === (e.ctrlKey || e.metaKey) || g >= 35 && g <= 39 || g >= 112 && g <= 123) return;
					return e.preventDefault(), !1
				}
				if (0 == c && v == this.value.length ? 8 == u ? (c = v = 1, this.value = "", h.init = n > 0 ? -1 : 0, h.c = n > 0 ? -(n + 1) : 0, t.apply(this, [0, 0])) : o == l ? (c = v = 1, this.value = "0" + l + new Array(n + 1).join("0"), h.init = n > 0 ? 1 : 0, h.c = n > 0 ? -(n + 1) : 0) : 45 == u ? (c = v = 2, this.value = "-0" + l + new Array(n + 1).join("0"), h.init = n > 0 ? 1 : 0, h.c = n > 0 ? -(n + 1) : 0, t.apply(this, [2, 2])) : (h.init = n > 0 ? -1 : 0, h.c = n > 0 ? -n : 0) : h.c = v - this.value.length, h.isPartialSelection = c != v, n > 0 && o == l && c == this.value.length - n - 1) h.c++, h.init = Math.max(0, h.init), e.preventDefault(), p = this.value.length + h.c;
				else if (45 != u || 0 == c && 0 != this.value.indexOf("-"))
					if (o == l) h.init = Math.max(0, h.init), e.preventDefault();
					else if (n > 0 && 127 == u && c == this.value.length - n - 1) e.preventDefault();
				else if (n > 0 && 8 == u && c == this.value.length - n) e.preventDefault(), h.c--, p = this.value.length + h.c;
				else if (n > 0 && 127 == u && c > this.value.length - n - 1) {
					if ("" === this.value) return;
					"0" != this.value.slice(c, c + 1) && (d = this.value.slice(0, c) + "0" + this.value.slice(c + 1), r.val(d)), e.preventDefault(), p = this.value.length + h.c
				} else if (n > 0 && 8 == u && c > this.value.length - n) {
					if ("" === this.value) return;
					"0" != this.value.slice(c - 1, c) && (d = this.value.slice(0, c - 1) + "0" + this.value.slice(c), r.val(d)), e.preventDefault(), h.c--, p = this.value.length + h.c
				} else 127 == u && this.value.slice(c, c + 1) == s ? e.preventDefault() : 8 == u && this.value.slice(c - 1, c) == s ? (e.preventDefault(), h.c--, p = this.value.length + h.c) : n > 0 && c == v && this.value.length > n + 1 && c > this.value.length - n - 1 && isFinite(+o) && !e.metaKey && !e.ctrlKey && !e.altKey && 1 === o.length && (d = v === this.value.length ? this.value.slice(0, c - 1) : this.value.slice(0, c) + this.value.slice(c + 1), this.value = d, p = c);
				else e.preventDefault();
				!1 !== p && t.apply(this, [p, p]), r.data("numFormat", h)
			},
			"keyup.format": function(e) {
				var i, l = $(this),
					s = l.data("numFormat"),
					r = e.keyCode ? e.keyCode : e.which,
					h = a.apply(this, ["start"]),
					u = a.apply(this, ["end"]);
				0 !== h || 0 !== u || 189 !== r && 109 !== r || (l.val("-" + l.val()), h = 1, s.c = 1 - this.value.length, s.init = 1, l.data("numFormat", s), i = this.value.length + s.c, t.apply(this, [i, i])), "" === this.value || (r < 48 || r > 57) && (r < 96 || r > 105) && 8 !== r && 46 !== r && 110 !== r || (l.val(l.val()), n > 0 && (s.init < 1 ? (h = this.value.length - n - (s.init < 0 ? 1 : 0), s.c = h - this.value.length, s.init = 1, l.data("numFormat", s)) : h > this.value.length - n && 8 != r && (s.c++, l.data("numFormat", s))), 46 != r || s.isPartialSelection || (s.c++, l.data("numFormat", s)), i = this.value.length + s.c, t.apply(this, [i, i]))
			},
			"paste.format": function(e) {
				var t = $(this),
					a = e.originalEvent,
					i = null;
				return window.clipboardData && window.clipboardData.getData ? i = window.clipboardData.getData("Text") : a.clipboardData && a.clipboardData.getData && (i = a.clipboardData.getData("text/plain")), t.val(i), e.preventDefault(), !1
			}
		}).each(function() {
			var e = $(this).data("numFormat", { c: -(n + 1), decimals: n, thousands_sep: s, dec_point: l, regex_dec_num: h, regex_dec: u, init: !!this.value.indexOf(".") });
			"" !== this.value && e.val(e.val())
		}) : this.each(function() {
			var e = $(this),
				t = +e.text().replace(h, "").replace(u, ".");
			e.number(isFinite(t) ? +t : 0, n, l, s)
		}) : this.text($.number.apply(window, arguments))
	};
	var n = null,
		l = null;
	$.isPlainObject($.valHooks.text) ? ($.isFunction($.valHooks.text.get) && (n = $.valHooks.text.get), $.isFunction($.valHooks.text.set) && (l = $.valHooks.text.set)) : $.valHooks.text = {}, $.valHooks.text.get = function(e) {
		var t, a = $(e).data("numFormat");
		return a ? "" === e.value ? "" : (t = +e.value.replace(a.regex_dec_num, "").replace(a.regex_dec, "."), (0 === e.value.indexOf("-") ? "-" : "") + (isFinite(t) ? t : 0)) : $.isFunction(n) ? n(e) : void 0
	}, $.valHooks.text.set = function(e, t) {
		var a = $(e).data("numFormat");
		if (a) {
			var i = $.number(t, a.decimals, a.dec_point, a.thousands_sep);
			return $.isFunction(l) ? l(e, i) : e.value = i
		}
		return $.isFunction(l) ? l(e, t) : void 0
	}, $.number = function(e, t, a, i) {
		i = void 0 === i ? "1000" !== new Number(1e3).toLocaleString() ? new Number(1e3).toLocaleString().charAt(1) : "" : i, a = void 0 === a ? new Number(.1).toLocaleString().charAt(1) : a, t = isFinite(+t) ? Math.abs(t) : 0;
		var n = "\\u" + ("0000" + a.charCodeAt(0).toString(16)).slice(-4),
			l = "\\u" + ("0000" + i.charCodeAt(0).toString(16)).slice(-4);
		e = (e + "").replace(".", a).replace(new RegExp(l, "g"), "").replace(new RegExp(n, "g"), ".").replace(new RegExp("[^0-9+-Ee.]", "g"), "");
		var s = isFinite(+e) ? +e : 0,
			r = "";
		return (r = (t ? function(e, t) { return "" + +(Math.round(("" + e).indexOf("e") > 0 ? e : e + "e+" + t) + "e-" + t) }(s, t) : "" + Math.round(s)).split("."))[0].length > 3 && (r[0] = r[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, i)), (r[1] || "").length < t && (r[1] = r[1] || "", r[1] += new Array(t - r[1].length + 1).join("0")), r.join(a)
	}
}(jQuery);