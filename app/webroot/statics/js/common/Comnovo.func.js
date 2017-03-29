/* vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker: */
/**
 * @package
 * @file                 $RCSfile: Comnovo.func.js,v $
 * @version              $Revision: 1.0 $
 * @modifiedby           $Author: handaoliang $
 * @lastmodified         $Date: 2012/04/18 18:05:10 $
 * @copyright            Copyright (c) 2012, Comnovo Inc.
 **/

var moduleNameSpace = "Comnovo.common.func";
var nameSpace = Comnovo.util.initNameSpace(moduleNameSpace);

(function(ns){
    ns.verifyUserName = function(userName, minLength, maxLength){
        if(typeof userName == "undefined" || userName == ""){ return false; }
        var nameLength = ns.getUTF8StringLength(userName);
        var nameREG = "^[0-9a-zA-Z_\u4e00-\u9fa5]+$";
        var re = new RegExp(nameREG);
        if(!re.test(userName) || nameLength < minLength || nameLength > maxLength){
            return false;
        }
        return true;
    };

    ns.verifyUserNameDetail = function(userName, minLength, maxLength){
        if(typeof userName == "undefined" || userName == ""){ return false; }
        var nameLength = ns.getUTF8StringLength(userName);
        var nameREG = "^[0-9a-zA-Z_\u4e00-\u9fa5]+$";
        var re = new RegExp(nameREG);
        if(!re.test(userName)){
            return 1;
        }
        if(nameLength < minLength){
            return 2;
        }
        if(nameLength > maxLength){
            return 3;
        }
        return 0;
    };

    ns.verifyCategoryName = function(categoryName){
        if(typeof categoryName == "undefined" || categoryName == ""){ return false; }
        var nameLength = ns.getUTF8StringLength(categoryName);
        var nameREG = "^[\u4e00-\u9fa5\/]+$";
        var re = new RegExp(nameREG);
        if(!re.test(categoryName) || nameLength < 6 || nameLength > 30){
            return false;
        }
        return true;
    };

    ns.verifyEmailAddress = function(emailAddress)
    {
        var emailSpecialChars = ["_", "-"];
        var emailFirstChars = emailAddress.substring(0, 1);
        if(Comnovo.util.inArray(emailSpecialChars, emailFirstChars)){
            return false;
        }
        
        if(!emailAddress.match(Comnovo.mailReg))
        {
            return false;
        }

        return true;
    };

    ns.getUTF8StringLength = function(str){
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

    //倒计时跳转函数=============
    ns.windowRedirect = function(sec, uri, eid){
        for(var i=sec; i>=0; i--) { 
            window.setTimeout('Comnovo.common.func.updateTimeInfo('+i+',"'+uri+'","'+eid+'")', (sec-i)*1000); 
        } 
    };

    ns.updateTimeInfo = function(num, redirectURI, showTimeElementId){
        jQuery("#"+showTimeElementId).html(num);
        if(num == 0) { window.location.href = redirectURI; }
    };

    //===========================
    //

    ns.showGlobalAlertDialog = function(dialogMessage, autoRedirectURI)
    {
        var openGlobalAlertModalDialog = function (d) {
            var self = this;
            self.container = d.container[0];
            d.overlay.fadeIn('fast', function () {
                jQuery("#global_alert_modal", self.container).show();
                d.container.fadeIn('fast', function () {
                    jQuery("#global_alert_modal", self.container).show();
                });
            })
        };
        var closeGlobalAlertModalDialog = function (d) {
            this.close();
            if(typeof autoRedirectURI != "undefined"){
                window.location.href=autoRedirectURI;
            }
        };

        if(typeof dialogMessage != "undefined"){
            jQuery("#global_alert_modal .con").html(dialogMessage);
            ns.globalModalDialog = jQuery("#global_alert_modal").modal({
                overlayId: 'modal_overlay',
                containerId: 'global_alert_modal_parent',
                closeHTML: '<a class="modal_close_img" title="关闭窗口"></a>',
                opacity: 65,
                closeClass:'modal_close_btn',
                onOpen: openGlobalAlertModalDialog,
                onClose: closeGlobalAlertModalDialog,
                overlayClose:true
            });

            if(typeof autoRedirectURI != "undefined"){
                Comnovo.common.func.windowRedirect(5, autoRedirectURI, 'rd_time');
                jQuery("#alert_rdtime_container").show();
            }
        }
    };

})(nameSpace);
