var style = "default";
var cookiename = "Layout";
var days = 30;

function switchStyle(s) {
    if (!document.getElementsByTagName) return;
    var el = document.getElementsByTagName("link");
    for (var i = 0; i < el.length; i++ ) {
        if (el[i].getAttribute("rel").indexOf("style") != -1 && el[i].getAttribute("title")) {
            el[i].disabled = true;
            if (el[i].getAttribute("title") == s) el[i].disabled = false;
        }
    }
}

function loadStyle() {
    var c = getStyleCookie();
    if (c && c != style) {
        switchStyle(c);
        style = c;
    }
}

function setStyle(s) {
    if (s != style) {
        switchStyle(s);
        style = s;
        setStyleCookie();
    }
}

window.onload = loadStyle;


function setCookie(name, value, expdays) {
    var now = new Date();
    var exp = new Date(now.getTime() + (1000*60*60*24*expdays));
    document.cookie = name + "=" + escape(value) + ";" +
        "expires=" + exp.toGMTString() + ";" +
        "path=/";
}

function delCookie(name) {
    var now = new Date();
    var exp = new Date(now.getTime() - 1);
    document.cookie = name + "=;" +
        "expires=" + exp.toGMTString() + ";" +
        "path=/";
}

function getCookie(name) {
    var cname = name + "=";
    var dc = document.cookie;
    if (dc.length > 0) {
        var start = dc.indexOf(cname);
        if (start != -1) {
            start += cname.length;
            var stop = dc.indexOf(";", start);
            if (stop == -1) stop = dc.length;
            return unescape(dc.substring(start,stop));
        }
    }
    return null;
}

function setStyleCookie() {
    setCookie(cookiename, style, days);
}

function getStyleCookie() {
    return getCookie(cookiename);
}

function delStyleCookie() {
    delCookie(cookiename);
}