/* vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker: */
/**
 * @package
 * @file                 $RCSfile: Comnovo.util.js,v $
 * @version              $Revision: 1.0 $
 * @modifiedby           $Author: handaoliang $
 * @lastmodified         $Date: 2012/04/18 18:05:10 $
 * @copyright            Copyright (c) 2012, Comnovo Inc.
 **/

var Comnovo = {
    version:"0.1",
    //mailReg:/^([a-zA-Z0-9_\.\-\+])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/,
    mailReg:/^([a-zA-Z0-9_\.\-\+])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z]{2,4})+$/,
    Browser: (function(){
        var ua = navigator.userAgent;
        var isOpera = Object.prototype.toString.call(window.opera) == '[object Opera]';
        return {
            IE:             !!window.attachEvent && !isOpera,
            Opera:          isOpera,
            WebKit:         ua.indexOf('AppleWebKit/') > -1,
            Gecko:          ua.indexOf('Gecko') > -1 && ua.indexOf('KHTML') === -1,
            MobileSafari:   /Apple.*Mobile/.test(ua)
        }
    })()
};

(function(base) {

    /**
     * Create Util
     **/
    var util = null;
    if(base && typeof base == "object"){
        if(!base.util){
            base.util = {};
        }
        util = base.util;
    }else{
        window.util = {};
        util = window.util;
    }

    /**
     * typeof object
     * @param {number,string,array,object,null,undefined,function} o
     * */
    util.typeOf = function (o) {
        return o === null ? o + '' :
            Object.prototype.toString.call(o).slice(8, -1).toLowerCase();
    };


    /**
     * -----------------------------------------------------------------
     * - The Base common function:
     * -     create Element
     * -     createRandElementID
     * -     createTimeStamp
     * -     etc......
     * -----------------------------------------------------------------
    **/
    /**
     * Create a html element
     * @htmlTagName, the name string, for example "div"
     **/
    util.createElement = function(htmlTagName, elementID, className) {
        var obj = document.createElement(htmlTagName);
        if(typeof elementID != "undefined"){
            obj.setAttribute("id",elementID);
        }
        if(typeof className != "undefined"){
            obj.setAttribute("class",className);
        }
        return obj;
    };


    /**
     * Create a rand element id (string)
     * @None
     **/
    util.createRandElementID = function() {
        var now = new Date().getTime ();
        return now + ":" + Math.floor(Math.random() * 100000000);
    };


    /**
     * Create a time stamp
     * @None
     **/
    util.createTimeStamp = function() {
        var now = new Date().getTime();;
        return (now - now % 1000) / 1000;
    };


    /**
     * Parse json data
     * @JSONData, string
     * @Return JSON Object.
     **/
    util.parseJSON = function(JSONData) {
        var returnJSON = false;
        if (String.prototype.parseJSON)
        {
            var s = String("(" + JSONData.responseText + ")");
            returnJSON = s.parseJSON(function(k,v){return v;});
        }else{
            returnJSON = eval("(" + JSONData.responseText.unfilterJSON() + ")");
        }
        if (!returnJSON.timestamp){
            returnJSON.timestamp = createTimeStamp();
        }
        return returnJSON;
    };


    /**
     * Parse json date
     * @Array Object
     * @Return bool value
     **/
    util.isArray = function(obj) {
        if(obj.constructor.toString().indexOf("Array") == -1){
            return false;
        }else{
            return true;
        }
    };


    /**
     * Check if item in array
     * @Array, items;
     * @Return True or False
     **/
    util.inArray = function(myArray, item) {
        var i;
        for (i=0; i < myArray.length; i++) {
            if (myArray[i] === item) {
                return true;
            }
        }
        return false;
    };


    /**
     * Remove a item from Array by item value
     * @Array, items;
     * @Return Array
     **/
    util.removeArrayItemByVal = function(myArray, itemToRemove) {
        var j = 0;
        while (j < myArray.length) {
            if (myArray[j] == itemToRemove) {
                myArray.splice(j, 1);
            }
            j++;
        }
        return myArray;
    };


    /**
     * Remove a item from Array by item ID
     * @Array, items id;
     * @Return Array
     **/
    util.removeArrayItemById = function(myArray, itemIDToRemove){
        if(!util.isArray(myArray) || isNaN(itemIDToRemove)){
            return false;
        }
        myArray.splice(itemIDToRemove, 1);
        return myArray;
    };


    /**
     * check if string contain special chars
     * @str, string
     * @Return bool value
     **/
    util.checkSpecialString = function(str) {
        var specialStringReg = RegExp(/[(\ )(\~)(\!)(\@)(\#)(\$)(\%)(\^)(\&)(\*)(\()(\))(\-)(\_)(\+)(\=)(\[)(\])(\{)(\})(\|)(\\)(\;)(\:)(\')(\")(\,)(\.)(\/)(\<)(\>)(\?)(\)]+/);
        return (specialStringReg.test(str));
    };


    /**
     * trim
     * @string.trim;
     * @Return String
     **/
    util.trim = function(str) {
        //return str.replace(/(^\s*)|(\s*$)/g, "");
         return str.replace(/(^[\s　]*)|([\s　]*$)/g, "");
    };


    /**
     * test and verify string is chinese chars
     * @string str
     * @Return True || False
     * */
    util.isCN = function(str) {
        var strREG = /^[u4E00-u9FA5]+$/;
        var re = new RegExp(strREG);
        if(!re.test(str)){
            return false;
        }
        return true;
    };


    /**
     * toDBC
     * @string.toDBC;
     * @Return String
     **/
    util.toDBC = function(str) {
        var DBCStr = "";
        for(var i=0; i<str.length; i++){
            var c = str.charCodeAt(i);
            if(c == 12288) {
                DBCStr += String.fromCharCode(32);
                continue;
            }
            if (c > 65280 && c < 65375) {
                DBCStr += String.fromCharCode(c - 65248);
                continue;
            }
            DBCStr += String.fromCharCode(c);
        }
        return DBCStr;
    };


    /**
     * get Windows size
     * @Return Object
     **/
    util.getWindowSize = function() {
        var window_width = window.innerWidth;
        var window_height = window.innerHeight;
        if (jQuery.browser.msie)
        {
            if (document.compatMode && document.compatMode != "BackCompat")
            {
                window_width = document.documentElement.clientWidth;
                window_height = document.documentElement.clientHeight;
            } else {
                window_width = document.body.clientWidth;
                window_height = document.body.clientHeight;
            }
        }
        return { "width":window_width, "height":window_height};
    };


    /**
     * get page size
     * @Return String
     **/
    util.getPageSize = function() {
        var xScroll, yScroll;
        if (window.innerHeight && window.scrollMaxY) { // Mozilla
            xScroll = document.body.scrollWidth;
            yScroll = window.innerHeight + window.scrollMaxY;
        } else if (document.body.scrollHeight > document.body.offsetHeight){ // all but Explorer Mac
            xScroll = document.body.scrollWidth;
            yScroll = document.body.scrollHeight;
        } else { // Explorer Mac...would also work in Explorer 6 Strict, Mozilla and Safari
            xScroll = document.body.offsetWidth;
            yScroll = document.body.offsetHeight;
        }
        var windowWidth, windowHeight;
        if (self.innerHeight) {	// all except Explorer
            windowWidth = self.innerWidth;
            windowHeight = self.innerHeight;
        } else if (document.documentElement && document.documentElement.clientHeight) { // Explorer 6 Strict Mode
            windowWidth = document.documentElement.clientWidth;
            windowHeight = document.documentElement.clientHeight;
        } else if (document.body) { // other Explorers
            windowWidth = document.body.clientWidth;
            windowHeight = document.body.clientHeight;
        }

        // for small pages with total size less then the viewport
        var pageWidth = (xScroll<windowWidth) ? windowWidth: xScroll;
        var pageHeight = (yScroll<windowHeight) ? windowHeight : yScroll;

        return {PageW:pageWidth, PageH:pageHeight, WinW:windowWidth, WinH:windowHeight};
    };


    /**
     * -----------------------------------------------------------------
     * - The function blew will be used to:
     * -     manage the namespace
     * -     analyze string, host
     * -     optimize the array
     * -----------------------------------------------------------------
    **/

    /**
     * Create the namespace if not existed
     * @ns, the namespace string, for example "abc.de"
     **/
    util.initNameSpace = function(ns) {
        var parentObj = window;
        var arNs = ns.split('.');
        do {
            var nowNs = arNs.shift();
            if (typeof parentObj[nowNs] != 'object') {
                parentObj[nowNs] = {};
            }
            parentObj = parentObj[nowNs];
        } while (arNs.length > 0)
        return util.decode(ns);
    };


    /**
     * Decode a json string
     * @param {String} json '{name: "Lucy", age: 18}'
     */
    util.decode = function(json) {
        return eval("(" + json + ")");
    };


    /*
     * Analyze URI
     * @uri, this value must be absolute URI, if not, the string will not be analyze correctly
     * return one object, which contains {http, domain, port, parameter, command}
     *  http's value should be https or http, which will be used to check whether this is on the SSL level
     */
    util.analyzeURI = function(uri)
    {
        //var match = uri.match(/^(([^:\/?#]+):)?(\/\/([^/?#]*))?([^?#]*)(\?([^#]*))?(#(.*))?/i);
        var match = uri.match(/^(([^:\/?#]+):)?(\/\/([^/?#]*))?([^?#]*)(\?([^#]*))?(#(.*))?$/i);
        // Example of "https://www.autodesk.com:90/labs/domain.aspx?test=1&value=2#mm=1"
        // 0 : https://www.autodesk.com:90/labs/domain.aspx?test=1&value=2#mm=1
        // 1 : https:
        // 2 : https
        // 3 : //www.autodesk.com:90
        // 4 : www.autodesk.com:90
        // 5 : /labs/domain.aspx
        // 6 : ?test=1&value=2
        // 7 : test=1&value=2
        // 8 : #mm=1
        // 9 : mm=1
        var ret = {};
        var arr = match[4].split(":");
        if(arr.length == 2){
            ret.domain = arr[0];
            ret.port = arr[1];
        }else if(arr.length == 1){
            ret.domain = arr[0];
            ret.port = "80";
        }
        ret.http = match[2];
        ret.param = match[7];
        ret.command = match[9];
        return ret;
    };


    /*
     * This function will be used to get one value from the URI
     * @url, the URI string
     * @name, the key which you want to get
     */
    util.getUrlParam = function(url, name)
    {
        var regexS = "[\\?&]" + name + "=([^&#]*)";
        var regex = new RegExp( regexS );
        var tmpURL = url;
        var results = regex.exec( tmpURL );
        if (results){
            return results[1];
        }
        return null;
    };


    /*
     * This function will get the value from the object
     * @obj, the JSON format of the object, for example {a:{b:[1,2,3]}}
     * @arrPath, the path of the object, for example ["a", "b"]
     * if using the value in the example, the result should be [1,2,3]
     */
    util.getValueFromObj = function(obj, arrPath){
        var ret = obj;
        if(!ret){
            return null;
        }
        for(var i=0;i<arrPath.length;++i){
            if(!ret[arrPath[i]]){
                return null;
            }
            ret = ret[arrPath[i]];
        }
        return ret;
    };

    /*
     * This function will check a object
     * @obj, the JSON format of the object, for example {a:{b:[1,2,3]}}
     * if the object is not empty it will return true else return false
     */
    util.isEmptyObj = function(obj) {
        for(var name in obj){
            if(obj.hasOwnProperty(name))
            {
                return false;
            }
        }
        return true;
    };

    /*
     * This function will be used to encode the string with XML
     * @str, the string to be encoded
     */
    util.escapeXml = function(str){
        if (!str){
            return "";
        }
        return str.replace(/&/gm, "&amp;").replace(/</gm, "&lt;").replace(/>/gm, "&gt;").replace(/"/gm, "&quot;");
    };


    /*
     * This function will be used to decode the string with XML
     * @str, the string to be decoded
     */
    util.unescapeXml = function(str)
    {
        if (!str){
            return "";
        }
        return str.replace(/&quot;/gm, "\"").replace(/&gt;/gm, ">").replace(/&lt;/gm, "<").replace(/&amp;/gm, "&");
    };


    /*
     * This function will be used to decode the string with HTML
     * @str, the string to be decoded
     */
    util.decodeHTML = function(str){
        str = "" + str;
        return str.replace(/&nbsp;/g, " ")
            .replace(/&amp/g, "&")
            .replace(/&gt;/g, ">")
            .replace(/&lt;/g, "<")
            .replace(/&quot;/g, "'");
    };


    /*
     * This function will be used to encode the string with HTML
     * @str, the string to be encoded
     */
    util.encodeHTML = function(text){
        return text.replace(/&/g, '&amp').replace(/'/g, '&quot;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
    };


    /*
     * This function will be used to delete all html tag
     * @str, the string to be replaced
     */
    util.delHtmlTag = function(str){
        return str.replace(/<[^>]+>/ig,"");
    };


    /*
     *
     * strip html tag
     * @str, the string to be replaced
     * @allowed, the tag allowed
     *
     */
    util.stripTags = function(str, allowed){
       // making sure the allowed arg is a string containing only tags in lowercase (<a><b><c>)
       allowed = (((allowed || "") + "").toLowerCase().match(/<[a-z][a-z0-9]*>/g) || []).join('');
       var tags = /<\/?([a-z][a-z0-9]*)\b[^>]*>/gi;
       var commentsAndPhpTags = /<!--[\s\S]*?-->|<\?(?:php)?[\s\S]*?\?>/gi;
       return str.replace(commentsAndPhpTags, '').replace(tags, function($0, $1){
          return allowed.indexOf('<' + $1.toLowerCase() + '>') > -1 ? $0 : '';
       });
    };


    /**
     * -----------------------------------------------------------------
     * - The function blew Manage the Cookie:
     * -     setCookie
     * -     delCookie
     * -     getCookie
     -----------------------------------------------------------------
    **/

    /*
     * This function will be used to get the cookie according to the given name
     * @name, the name of the cookie
     */
    util.getCookie = function(name) {
        var arr = document.cookie.match(new RegExp("(^| )"+name+"=([^;]*)(;|$)"));
        if(arr != null){
            return unescape(arr[2]);
        }
        return null;
    };


    /*
     * This function will be used to set the cookie by given data and expire date
     * @name, the name of the cookie
     * @value, the value of the cookie
     * @expires, the expire time of the cookie, counting by DAY
     * @domainValue, the domain of the Cookie
     */
    util.setCookie = function(name, value, expires, domainValue) {
        var cookieValue = name + "="+ escape(value);

        if(domainValue!=null) {
            cookieValue += ";domain="+domainValue;
        }
        if(expires !=null && expires !=0){
            var exp  = new Date();
            exp.setTime(exp.getTime() + expires*60*60*24);
            cookieValue += ";expires=" + exp.toGMTString();
        }
        document.cookie = cookieValue;
    };


    /*
     * This function will be used to delete the cookie by given name
     * @name, the name of the cookie
     */
    util.delCookie = function(name, path, domain)
    {
        var cookies_val=this.getCookie(name);
        if(cookies_val != null) {
            document.cookie = name + "=" + cookies_val
                            + ((path) ? ";path=" + path : "")
                            + ((domain) ? ";domain=" + domain : "")
                            + ";expires=Thu, 01-Jan-1970 00:00:01 GMT";
        }
    };


    /**
     * -----------------------------------------------------------------
     * - The function blew will be used the event:
     * -     bind Events for a DOM object
     * -     init eveything when DOM ready
     * -     prepare the function for the callback function
     * -----------------------------------------------------------------
    **/

    /*
     * This function will create the callback function by given value and scope
     * @fun, the function
     * @scope, the scope of the function
     * @param1~param5, the parameter that this functin will be used
     */
    util.bind = function(func, scope, param1, param2, param3, param4, param5){
        return function(){
            return func.call(scope, param1, param2, param3, param4, param5);
        }
    };


    /*
     * This function will be fired when the DOM tree ready for multipulate, different with onload
     * @fn, the function you want to add
     */
    util.domReady = function(fn){
        if(document.addEventListener){ //W3C
            document.addEventListener("DOMContentLoaded", fn, false);
        }else{ //IE
            var oldfun = document.onreadystatechange;
            document.onreadystatechange = function(){
                if(typeof oldfun == "function"){
                    oldfun();
                }
                if(document.readyState == "interactive"
                    || document.readyState == "complete"){
                    if(typeof fn == "function"){
                        fn();
                    }
                    fn = null;
                }
            }
        }
    };


    /*
     * This function will be used to add the function and trigger it after the document ready
     * @func, the function callback
     */
    util.addLoadEvent = function(func){
        if(util.loadReady
            || document.readyState == "complete"
            /*|| document.readyState == "interactive"*/){
            func();
            util.loadReady = true;
            return;
        }
        if(!util.loadReadyCbQueue){
            util.loadReadyCbQueue = [func];
        }else{
            util.loadReadyCbQueue.push(func);
        }
    };


    var _loadComplete = function(){
        util.loadReady = true;
        for(var i=0;!!util.loadReadyCbQueue && i<util.loadReadyCbQueue.length;++i){
            util.loadReadyCbQueue[i]();
        }
        util.loadReadyCbQueue = [];
    };

    util.domReady(_loadComplete);


    /*
     * This function will be used to bind the event for the element
     * @el, the element which dom will be binded
     * @evType, the event name, for example "mousedown"
     * @useCapture, is capture or buble, default is buble
     */
    util.bindEvent = function(el, evType, callback, useCapture){
        el = util.id(el);
        if (el.addEventListener) {
            el.addEventListener(evType, callback, useCapture);
            return true;
        } else if (el.attachEvent) {
            var r = el.attachEvent('on' + evType, callback);
            return r;
        } else {
            el['on' + evType] = callback;
        }
    };


    /*
     * This function will be used to get the position of the mouse
     * @e, the event object
     * return {x,y}
     */
    util.getCurPos = function(e)
    {
        e = e || window.event;
        var D = document.documentElement;
        if( e.pageX ){
            return {
                "x": e.pageX,
                "y": e.pageY
            };
        }
        return {
            "x": e.clientX + D.scrollLeft - D.clientLeft,
            "y": e.clientY + D.scrollTop - D.clientTop
        };
    };


    /*
     * This function will be used to check whether the mouse is in the given DOM Elememnt
     * @e, the event object
     * @el, the element
     */
    util.isMouseIn = function(e, el){
        var c = util.getCurPos(e);
        var p = util.getObjPos(el);
        if(c.x >= p.x && c.x <= p.x + el.offsetWidth && c.y >= p.y && c.y <= p.y + el.offsetHeight){
            return true;
        }
        return false;
    };


    util.makeDrag = function(sender, container, fnMousedown, fnMousemove, fnMouseup)
    {
        if(!container){
            container = sender;
        }
        this.makeDrag_flag = false;
        if(typeof fnMousedown == "function"){
            this.fnMousedown = fnMousedown;
        }
        if(typeof fnMousemove == "function"){
            this.fnMousemove = fnMousemove;
        }
        if(typeof fnMouseup == "function"){
            this.fnMouseup = fnMouseup;
        }
        var mousemoveEvent = document.onmousemove;
        var mouseupEvent = document.onmouseup;
        var me = this;
        util.bindEvent(sender, "mousedown", function(e){
            if(me.fnMousedown){
                me.fnMousedown(e);
            }
            var oPos = util.getObjPos(container);
            var cPos = util.getCurPos(e);
            me.makeDrag_flag = true;
            document.onmouseup = function(e){
                me.makeDrag_flag = false;
                // Problem: should we store the events
                document.onmousemove =mousemoveEvent;
                document.onmouseup = mouseupEvent;
                if(me.fnMouseup){
                    me.fnMouseup(e);
                }
                return false;
            };
            document.onmousemove = function(e){
                if(me.makeDrag_flag){
                    if(me.fnMousemove){
                        me.fnMousemove(e);
                    }
                    document.body.appendChild(container);
                    container.style.position = "absolute";
                    container.style.zIndex = "99999";
                    var Pos = util.getCurPos(e);
                    container.style.left = Pos.x - cPos.x + oPos.x + "px";
                    container.style.top = Pos.y - cPos.y + oPos.y + "px";
                }
                return false;
            };
            try{
                // Firefox is different with IE
                e.preventDefault();
            }catch(e){}
            return false;
        });
    };


    // -----------------------------------------------------------------
    // - The function blew will be used to manage the DOM ELement:
    // -     selector of the Element
    // -     find position or something else like this
    // -     manage the CSS and style
    // -----------------------------------------------------------------

    /*
     * Check whether the element has the given class
     * @el, the DOM elemenet
     * @name, the name of the class
     */
    util.hasClass = function(el, name){
        var className = "" + util.id(el).className;
        var arr = className.split(" ");
        for(var i=0;i<arr.length;++i){
            if(arr[i] == name){
                return true;
            }
        }
        return false;
    };


    /*
     * This function will be used to add the class to the element
     * @el, the DOM elemenet
     * @name, the class name
     */
    util.addClass = function(el, name){
        var className = "" + util.id(el).className;
        var arr = className.split(" ");
        for(var i=0;i<arr.length;++i){
            if(arr[i] == name){
                return;
            }
        }
        arr.push(name);
        el.className = arr.join(" ");
    };


    /*
     * This function will be used to remove the class from the given element
     * @el, the DOM element
     * @name, the name of the class
     */
    util.removeClass = function(el, name){
        var className = "" + util.id(el).className;
        var arr = className.split(" ");
        for(var i=arr.length-1;i>=0;--i){
            if(arr[i] == name){
                arr.splice(i, 1);
            }
        }
        el.className = arr.join(" ");
    };


    /*
     * This function will be used to find the element deeply
     * @el, the root DOM element
     * @config, the path, for example:[{"tagName", "class"},{},...]
     * This function will return one Error that match the condition
     */
    util.findElements = function(el, config) {
        var findEls = function(el, config){
            var els = el.children;
            var ret = [];
            for(var i=0;i<els.length;++i){
                if(els[i].tagName
                    && (!config.tagName || config.tagName.length == 0 || els[i].tagName.toLowerCase() == config.tagName.toLowerCase())
                    && (!config.className || config.className.length == 0 || util.hasClass(els[i], config.className))){
                        ret.push(els[i]);
                }
            }
            return ret;
        };
        var els = [];
        if(!config || config.length == 0){
            return
        }
        els = findEls(el, config[0]);
        for(var i=1;i<config.length;++i){
            if(els.length > 0){
                els = findEls(els[0], config[i]);
            }else{
                break;
            }
        }
        return els;
    };


    util.hideFlash = function() {
        if(util.flashHide){
            return;
        }
        util.flashHide = true;
        var objs = document.getElementsByTagName("object");
        for (var i = 0, n = objs.length; i < n; i++) {
            objs[i]._width = objs[i].width;
            objs[i]._height = objs[i].height;
            objs[i].width = 1;
            objs[i].height = 1;
        }
        objs = document.getElementsByTagName("embed");
        for (var j = 0, l = objs.length; j < l; j++) {
            objs[j]._width = objs[j].width;
            objs[j]._height = objs[j].height;
            objs[j].width = 1;
            objs[j].height = 1;
        }
        objs = document.getElementsByTagName("iframe");
        for (var j = 0, l = objs.length; j < l; j++) {
            objs[j]._visibility = objs[j].style.visibility;
            objs[j].style.visibility = "hidden";
        }
        objs = document.getElementsByTagName("select");
        for (var j = 0, l = objs.length; j < l; j++) {
            objs[j]._visibility = objs[j].style.visibility;
            objs[j].style.visibility = "hidden";
        }
        objs = null;
    };


    util.restoreFlash = function() {
        if(!util.flashHide){
            return;
        }
        util.flashHide = false;
        var objs = document.getElementsByTagName("object");
        for (var i = 0, n = objs.length; i < n; i++) {
            if(objs[i]._width){
                objs[i].width = objs[i]._width;
            }
            if(objs[i]._height){
                objs[i].height = objs[i]._height;
            }
        }

        objs = document.getElementsByTagName("embed");
        for (var j = 0, l = objs.length; j < l; j++) {
            if(objs[j]._width){
                objs[j].width = objs[j]._width;
            }
            if(objs[j]._height){
                objs[j].height = objs[j]._height;
            }
        }

        objs = document.getElementsByTagName("iframe");
        for (var j = 0, l = objs.length; j < l; j++) {
            if(typeof objs[j]._visibility == "string"){
                objs[j].style.visibility = objs[j]._visibility;
            }
        }
        objs = document.getElementsByTagName("select");
        for (var j = 0, l = objs.length; j < l; j++) {
            if(typeof objs[j]._visibility == "string"){
                objs[j].style.visibility = objs[j]._visibility;
            }
        }
        objs = null;
    };


    /*
     * This function will be used to find the element by given tagName and className
     * @tag, the tag name of the elements
     * @className, the classname of the elements
     */
    util.getElementsByClass = function(tag, className){
        var arr = document.getElementsByTagName(tag);
        var ret = [];
        for(var i=0;i<arr.length;++i){
            if(util.hasClass(arr[i], className)){
                ret.push(arr[i]);
            }
        }
        return ret;
    };


    /*
     * This function will get the position of the object
     * @obj, the element, or the element id
     * @bFixed, if this value is true, we will try to give out the position against the first parent whose position is fixed or absolute
     * This funcrion will return the result by {x, y}
     */
    util.getObjPos = function(obj, bFixed){
        obj = util.id(obj);
        var x = y = 0;
        if(!bFixed && obj.getBoundingClientRect){
            var box = obj.getBoundingClientRect();
            var D = document.documentElement;
            x = box.left + Math.max(D.scrollLeft, document.body.scrollLeft) - D.clientLeft;
            y = box.top + Math.max(D.scrollTop, document.body.scrollTop) - D.clientTop;
        }else{
            for(;obj!= document.body; obj = obj.offsetParent){
                if(bFixed && (obj.style.position.toLowerCase() == "fixed" || obj.style.position.toLowerCase() == "absolute")){
                    break;
                }
                x += obj.offsetLeft;
                y += obj.offsetTop;
            }
        }
        return {
            "x": x,
            "y": y
        };
    };


    /*
     * This function will be used to check whether the parent is descendant of the node
     * @parent, the parent node
     * @node, the child node
     */
    util.isDescendant = function(parent, node){
        if ( !parent || !node ){
            return true;
        }
        while ( node = node.parentNode ){
            if ( node == parent ){
                return true;
            }
        }
        return false;
    };


    /*
     * This function will be used to remove all the Child under one element
     * @e, the element whose children will be removed
     */
    util.deleteChildNodes = function(e){
        while(e.hasChildNodes()){
            e.removeChild(e.lastChild);
        }
    };


    /*
     * This function will be used to get the Document Object by ID
     * @id, the DOM object or it's ID
     */
    util.id = function(id)
    {
        if(typeof id === "string"){
            return document.getElementById(id);
        }
        return id;
    };


    /*
     * This function will be used to get the value or set the value
     * @el, the element
     * @styleName, the style name
     * @styleValue, the style value
     */
    util.css = function(el, styleName, styleValue){
        el = util.id(el);
        try{
            el.style[styleName] = styleValue;
        }catch(e){
            alert(e);
        }
        if(styleName.toLowerCase() == "opacity"){
            el.style.filter = 'alpha(opacity=' + (100 * styleValue) + ')';
        }
        return el.style[styleName];
    };


    /*
     * This function will be used to get the destance of the scoll top to Document top
     */
    util.getScrollTop = function() {
        return window.pageYOffset || document.documentElement.scrollTop || document.body.scrollTop;
    };


    /*
     * This function will be used to get the destance of the scoll left to Document left
     */
    util.getScrollLeft = function() {
        return window.pageXOffset || document.documentElement.scrollLeft || document.body.scrollLeft;
    };


    /*
     * Get the Height of the Window
     */
    util.getClientHeight = function() {
        return (document.compatMode == "CSS1Compat")? document.documentElement.clientHeight : document.body.clientHeight;
    };


    /*
     * Get the Width of the Window
     */
    util.getClientWidth = function() {
        return (document.compatMode == "CSS1Compat")? document.documentElement.clientWidth : document.body.clientWidth;
    };


    util.getScrollWidth = function() {
        return (document.compatMode == "CSS1Compat")? document.documentElement.scrollWidth : document.body.scrollWidth;
    };


    util.getScrollHeight = function() {
        return (document.compatMode == "CSS1Compat")? document.documentElement.scrollHeight : document.body.scrollHeight;
    };


    /*
     * Get if the scroll on bottom
     */
    util.scrollBottom = function(divScroll){
        if(!divScroll){
            if(util.getScrollTop()+util.getClientHeight()==util.getScrollHeight()){
                return true;
            }
            return false;
        }else{
            if($(divScroll)[0].scrollTop==0&&$(divScroll)[0].clientHeight+$(divScroll)[0].scrollTop==$(divScroll)[0].scrollHeight){
                return false; //no scroll
            }else if($(divScroll)[0].clientHeight+$(divScroll)[0].scrollTop==$(divScroll)[0].scrollHeight){
                return true; //The scroll is on bottom
            }
            return false;
        }
    };


    /**
     * count object items
     */
    util.count = function(object) {
        var num = 0;
        for (var i in object) {
            num++;
        }
        return num;
    };


    /**
     * get browser available size
     */
    util.getClientSize = function() {
        return {
            width  : window.innerWidth  || document.documentElement.clientWidth,
            height : window.innerHeight || document.documentElement.clientHeight
        };
    };


    /**
     * recursive clone a javascript object
     */
    util.clone = function(variable) {
        switch (Object.prototype.toString.call(variable)) {
            case '[object Object]':       // Object instanceof Object
                var variableNew = {};
                for (var i in variable) {
                    variableNew[i] = this.clone(variable[i]);
                }
                break;
            case '[object Array]':        // Object instanceof Array
                variableNew = [];
                for (i in variable) {
                    variableNew.push(this.clone(variable[i]));
                }
                break;
            default:                      // typeof Object === 'function' || etc
                variableNew = variable;
        }
        return variableNew;
    };

    util.getUTF8StringLength = function(str){
        if(typeof str == "undefined" || str == ""){ return 0; }
        var len = 0;
        for (var i = 0; i < str.length; i++){
            charCode = str.charCodeAt(i);
            if (charCode < 0x007f){
                len += 1;
            } else if ((0x0080 <= charCode) && (charCode <= 0x07ff)){
                len += 2;
            } else if ((0x0800 <= charCode) && (charCode <= 0xffff)){
                len += 3;
            }
        }
        return len;
    };

})(Comnovo);
