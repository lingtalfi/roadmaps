//------------------------------------------------------------------------------/
// z: a customized javascript library for your projects
//------------------------------------------------------------------------------/
// by lingtalfi, created 2016-09-20
if (!window.z) {


    //------------------------------------------------------------------------------/
    // CORE
    //------------------------------------------------------------------------------/
    // This method is only useful if you intend to loop over a collection of elements.
    // If you target a single element, you should use methods like document.getElementById
    // or document.querySelector.
    window.z = function (selector, context) {
        context = context || document;
        return Array.prototype.slice.call(context.querySelectorAll(selector));
    };


    //------------------------------------------------------------------------------/
    // AJAX
    //------------------------------------------------------------------------------/
    // https://plainjs.com/javascript/ajax/send-ajax-get-and-post-requests-47/
    window.z.ajaxGet = function (url, success) {
        var xhr = window.XMLHttpRequest ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
        xhr.open('GET', url);
        xhr.onreadystatechange = function () {
            if (xhr.readyState > 3 && xhr.status == 200) success(xhr.responseText);
        };
        xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
        xhr.send();
        return xhr;
    };

    window.z.ajaxPost = function (url, data, success) {
        var params = typeof data == 'string' ? data : Object.keys(data).map(
                function (k) {
                    return encodeURIComponent(k) + '=' + encodeURIComponent(data[k])
                }
            ).join('&');

        var xhr = window.XMLHttpRequest ? new XMLHttpRequest() : new ActiveXObject("Microsoft.XMLHTTP");
        xhr.open('POST', url);
        xhr.onreadystatechange = function () {
            if (xhr.readyState > 3 && xhr.status == 200) {
                success(xhr.responseText);
            }
        };
        xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.send(params);
        return xhr;
    };

    //------------------------------------------------------------------------------/
    // TRAVERSING
    //------------------------------------------------------------------------------/
    // closest, the code below is for ie (other modern browsers have native closest implementation)
    // https://plainjs.com/javascript/traversing/get-closest-element-by-selector-39/
    (function () {
        // matches polyfill
        this.Element && function (ElementPrototype) {
            ElementPrototype.matches = ElementPrototype.matches ||
                ElementPrototype.matchesSelector ||
                ElementPrototype.webkitMatchesSelector ||
                ElementPrototype.msMatchesSelector ||
                function (selector) {
                    var node = this, nodes = (node.parentNode || node.document).querySelectorAll(selector), i = -1;
                    while (nodes[++i] && nodes[i] != node);
                    return !!nodes[i];
                }
        }(Element.prototype);

        // closest polyfill
        this.Element && function (ElementPrototype) {
            ElementPrototype.closest = ElementPrototype.closest ||
                function (selector) {
                    var el = this;
                    while (el.matches && !el.matches(selector)) el = el.parentNode;
                    return el.matches ? el : null;
                }
        }(Element.prototype);
    })();

    //------------------------------------------------------------------------------/
    // UTILITIES
    //------------------------------------------------------------------------------/
    window.z.getCookie = function (name) {
        var v = document.cookie.match('(^|;) ?' + name + '=([^;]*)(;|$)');
        return v ? v[2] : null;
    };
    window.z.setCookie = function (name, value, days) {
        var d = new Date;
        d.setTime(d.getTime() + 24 * 60 * 60 * 1000 * days);
        document.cookie = name + "=" + value + ";path=/;expires=" + d.toGMTString();
    };
    window.z.deleteCookie = function (name) {
        window.z.setCookie(name, '', -1);
    };
}



