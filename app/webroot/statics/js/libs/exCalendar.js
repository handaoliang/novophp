/**
 * @Description:    Excellent Calendar control.
 * @Author:         HanDaoliang <handaoliang@gmail.com>
 * @createDate:     Sup 15,2011
 * @CopyRights:		http://www.handaoliang.com
**/

//Set exCalendar container.
var exCalendarContainerClassName = 'exCalendarContainer';
var exCalendarJSSourceID = "exCalendarJsSource";
var exCallBackHandlerArr = {};
var exCalendarModel = {};
var dateTimeFieldBoxArr = {};
var autoHiddenCalendarContainer = {};

var exLang = {
    weekArr: ["日","一","二","三","四","五","六"],
    longWeekArr:["星期日","星期一","星期二","星期三","星期四","星期五","星期六"],
    //monthArr: ["一月","二月","三月","四月","五月","六月","七月","八月","九月","十月","十一月","十二月"],
    monthArr: ["01 月","02 月","03 月","04 月","05 月","06 月","07 月","08 月","09 月","10 月","11 月","12 月"],
    todayBtnStr: "今天",
    timeBtnStr: "时间",
    timeAllDay: "全天",
    errorMsg: []
};

function exCalendar() {
    var currentYearObj = {};
    var currentMonthObj = {};
    var currentDayObj = {};

    var selectedYear = 0;
    var selectedMonth = 0;
    var selectedDay = 0;

    var weekArr = ["Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat"];
    var monthArr = ["January","February","March","April","May","June","July","August","September","October","November","December"];

    this.initialize = initialize;
    function initialize(exCalendarContainerID, textFieldContainerID, calendarCallBack, calendarModel, autoHiddenContainer) {
        //set language...
        weekArr = exLang.weekArr;
        monthArr = exLang.monthArr;

        //set container id
        if (typeof exCalendarContainerID != "undefined"){
            calendarContainerID = exCalendarContainerID;
        }else{
            calendarContainerID = 'exCalendarContainer';
        }


        if(typeof textFieldContainerID != "undefined"){
            dateTimeFieldBoxArr[exCalendarContainerID] = document.getElementById(textFieldContainerID);
        }else{
            alert("You must set text field document id");
            return;
        }

        //set callback function
        if(typeof calendarCallBack != "undefined"){
            exCallBackHandlerArr[exCalendarContainerID] = calendarCallBack;
        }else{
            exCallBackHandlerArr[exCalendarContainerID] = null;
        }

        if(typeof calendarModel != "undefined"){
            exCalendarModel[exCalendarContainerID] = calendarModel;
        }else{
            exCalendarModel[exCalendarContainerID] = "normal";
        }

        //是否自动隐藏。
        if(typeof autoHiddenContainer != "undefined"){
            autoHiddenCalendarContainer[exCalendarContainerID] = autoHiddenContainer;
        }else{
            autoHiddenCalendarContainer[exCalendarContainerID] = null;
        }

        //设置统一的ClassName以方便样式控制。
        exCalendarContainerObj = document.getElementById(calendarContainerID);
        exCalendarContainerObj.className = exCalendarContainerClassName;

        this.showExCalendar(calendarContainerID);
    }


    function getDaysInMonth(year, month) {
        return [31,((!(year % 4 ) && ( (year % 100 ) || !( year % 400 ) ))?29:28),31,30,31,30,31,31,30,31,30,31][month-1];
    }

    function getDayOfWeek(year, month, day) {
        var date = new Date(year,month-1,day)
            return date.getDay();
    }

    function setElementProperty(eleProperty, eleValue, elementId){
        var myElement = elementId;
        var elementObj = null;

        if(typeof(myElement) == "object"){
            elementObj = myElement;
        } else {
            elementObj = document.getElementById(myElement);
        }
        if((elementObj != null) && (elementObj.style != null)){
            elementObj = elementObj.style;
            elementObj[ eleProperty ] = eleValue;
        }
    }

    function setProperty(eleProperty, eleValue, exCalendarContainerID) {
        setElementProperty(eleProperty, eleValue, exCalendarContainerID);
    }

    this.setDate = setDate;
    function setDate(sYear, sMonth, sDay, calContainerID)
    {
        if (dateTimeFieldBoxArr[calContainerID]) {
            monthVar = sMonth >= 10 ? sMonth : "0" + sMonth;
            dayVar = sDay >= 10 ? sDay : "0" + sDay;
            var displayDateString = monthVar + "-" + dayVar + "-" + sYear;
            //var displayDateString = sYear + "年" + monthVar + "月" + dayVar + "日";

            //save standard time.
            //var standardDateString = sYear + "-" + monthVar + "-" + dayVar + " 00:00:00";
            var standardDateString = sYear + "-" + monthVar + "-" + dayVar;

            //set dateTimeFieldBoxArr[calContainerID] value.
            dateTimeFieldBoxArr[calContainerID].value = standardDateString;
            if(typeof exCallBackHandlerArr[calContainerID] == "function"){
                exCallBackHandlerArr[calContainerID](displayDateString, standardDateString);
            }

            //Refresh date table display
            refreshDateTables(sYear, sMonth, sDay, calContainerID);

            //如果自动隐藏的对象存在。则隐藏。
            if(autoHiddenCalendarContainer[calContainerID]){
                setProperty('display', 'none', autoHiddenCalendarContainer[calContainerID]);
            }
        }

        return;
    }

    this.changeMonth = changeMonth;
    function changeMonth(change, calContainerID) {
        currentMonthObj[calContainerID] += change;
        currentDayObj[calContainerID] = 0;
        if(currentMonthObj[calContainerID] > 12) {
            currentMonthObj[calContainerID] = 1;
            currentYearObj[calContainerID]++;
        } else if(currentMonthObj[calContainerID] < 1) {
            currentMonthObj[calContainerID] = 12;
            currentYearObj[calContainerID]--;
        }

        exCalendarObj = document.getElementById(calContainerID);
        exCalendarObj.innerHTML = drawExCalendar(calContainerID);
    }

    this.changeYear = changeYear;
    function changeYear(change, calContainerID) {
        currentYearObj[calContainerID] += change;
        currentDayObj[calContainerID] = 0;
        calendar = document.getElementById(calContainerID);
        calendar.innerHTML = drawExCalendar(calContainerID);
    }

    function getCurrentYear() {
        var year = new Date().getYear();
        if(year < 1900) year += 1900;
        return year;
    }

    function getCurrentMonth() {
        return new Date().getMonth() + 1;
    }

    function getCurrentDay() {
        return new Date().getDate();
    }

    this.refreshDateTables = refreshDateTables;
    function refreshDateTables(currentYear, currentMonth, currentDay, calContainerID){
        var dateStringID = calContainerID+"_"+currentYear + "_" + currentMonth + "_" + currentDay;
        var dateArr = document.getElementsByName(calContainerID+"_cal_date_link");
        for(i=0; i<dateArr.length; i++){
            if(dateArr[i].className == "current"){
                dateArr[i].className = "";
            }else{
                classNameArr = dateArr[i].className.split(" ");
                if(classNameArr.length >= 2){
                    for(j=0; j<classNameArr.length; j++){
                        if(classNameArr[j] == "current"){
                            classNameArr.splice(j,1);
                        }
                    }
                }
                curClassName = classNameArr.join(" ");
                dateArr[i].className = curClassName;
            }
        }
        var currentDateLinkObj = document.getElementById(dateStringID);
        if(currentDateLinkObj.className == ""){
            currentDateLinkObj.className = "current";
        }else{
            currentDateLinkObj.className = currentDateLinkObj.className + " current";
        }
    }

    function drawExCalendar(calendarContainerID) {
        //当天
        var thisYear = getCurrentYear();
        var thisMonth = getCurrentMonth();
        var thisDay = getCurrentDay();
        var todayTimestamp = Date.parse(thisYear + '/' + thisMonth + '/' + thisDay + ' 00:00:00');

        //计算一月内的时间。
        var dayOfMonth = 1;
        var validDay = 0;
        var startDayOfWeek = getDayOfWeek(currentYearObj[calendarContainerID], currentMonthObj[calendarContainerID], dayOfMonth);
        var daysInMonth = getDaysInMonth(currentYearObj[calendarContainerID], currentMonthObj[calendarContainerID]);
        var css_class = null; //CSS class for each day

        var excalCon = "<div class='exCalDays'><table cellspacing='0' cellpadding='0' border='1' bordercolor='D3D4D4' id='exCalDateTable'>";
        excalCon += "<tr class='header'>";
        excalCon += "  <td class='previous'><a href='javascript:exCal.changeCalendarMonth(-1,\""+calendarContainerID+"\");'>&laquo;</a></td>";
        excalCon += "  <td colspan='5' class='title'>" + currentYearObj[calendarContainerID] + "&nbsp;年&nbsp;&nbsp;" + monthArr[currentMonthObj[calendarContainerID]-1] + "</td>";
        excalCon += "  <td class='next'><a href='javascript:exCal.changeCalendarMonth(1,\""+calendarContainerID+"\");'>&raquo;</a></td>";
        excalCon += "</tr>";
        excalCon += "<tr><th>"+ weekArr[0] +"</th><th>"+ weekArr[1] +"</th><th>"+ weekArr[2] +"</th><th>"+ weekArr[3] +"</th><th>"+ weekArr[4] +"</th><th>"+ weekArr[5] +"</th><th>"+ weekArr[6] +"</th></tr>";

        if(exCalendarModel[calendarContainerID] == "log"){
            //日志模式的日历
            for(var week=0; week<6; week++)
            {
                excalCon += "<tr>";
                for(var dayOfWeek=0; dayOfWeek < 7; dayOfWeek++) {
                    if(week == 0 && startDayOfWeek == dayOfWeek) {
                        validDay = 1;
                    } else if (validDay == 1 && dayOfMonth > daysInMonth) {
                        validDay = 0;
                    }

                    if(validDay) {
                        var curTimestamp = Date.parse(currentYearObj[calendarContainerID] + '/' + currentMonthObj[calendarContainerID] + '/' + dayOfMonth + ' 00:00:00');
                        //console.log(curTimestamp);
                        if (dayOfWeek == 0 || dayOfWeek == 6) {
                            cssClass = 'weekend';
                            if(curTimestamp > todayTimestamp){
                                cssClass = 'weekend invalid';
                            }else{
                                if(dayOfMonth == thisDay && currentMonthObj[calendarContainerID] == thisMonth && currentYearObj[calendarContainerID] == thisYear
                                        && dayOfMonth == selectedDay && currentYearObj[calendarContainerID] == selectedYear && currentMonthObj[calendarContainerID] == selectedMonth ){
                                    cssClass = 'weekend current today';
                                }else if (dayOfMonth == selectedDay && currentYearObj[calendarContainerID] == selectedYear && currentMonthObj[calendarContainerID] == selectedMonth) {
                                    cssClass = 'weekend current';
                                }else if(dayOfMonth == thisDay && currentMonthObj[calendarContainerID] == thisMonth && currentYearObj[calendarContainerID] == thisYear){
                                    cssClass = 'weekend today';
                                }
                            }
                        } else {
                            cssClass = 'weekday';
                            if(curTimestamp > todayTimestamp){
                                cssClass = 'weekday invalid';
                            }else{
                                if(dayOfMonth == thisDay && currentMonthObj[calendarContainerID] == thisMonth && currentYearObj[calendarContainerID] == thisYear
                                        && dayOfMonth == selectedDay && currentYearObj[calendarContainerID] == selectedYear && currentMonthObj[calendarContainerID] == selectedMonth ){
                                    cssClass = 'weekday current today';
                                }else if (dayOfMonth == selectedDay && currentYearObj[calendarContainerID] == selectedYear && currentMonthObj[calendarContainerID] == selectedMonth) {
                                    cssClass = 'weekday current';
                                }else if(dayOfMonth == thisDay && currentMonthObj[calendarContainerID] == thisMonth && currentYearObj[calendarContainerID] == thisYear){
                                    cssClass = 'weekday today';
                                }
                            }
                        }

                        if(curTimestamp > todayTimestamp){
                            excalCon += "<td><a class='"+cssClass+"' id='"+calendarContainerID+"_"+currentYearObj[calendarContainerID]+"_"+currentMonthObj[calendarContainerID]+"_"+dayOfMonth
                                        + "' name='"+calendarContainerID+"_cal_date_link' href='javascript:;'>"+dayOfMonth+"</a></td>";
                        }else{
                             excalCon += "<td><a class='"+cssClass+"' id='"+calendarContainerID+"_"+currentYearObj[calendarContainerID]+"_"+currentMonthObj[calendarContainerID]+"_"+dayOfMonth
                                        + "' name='"+calendarContainerID+"_cal_date_link' onclick=\"javascript:exCal.setCalendarDate("
                                        + currentYearObj[calendarContainerID]+","+currentMonthObj[calendarContainerID]+","+dayOfMonth+",'"+calendarContainerID+"'"
                                        + ")\" href='javascript:;'>"+dayOfMonth+"</a></td>";
                        }
                        dayOfMonth++;
                    } else {
                        excalCon += "<td class='empty'>&nbsp;</td>";
                    }
                }
                excalCon += "</tr>";
            }
            //结束日志形式的日期选择模式。
        }else{
            //正常模式的日历
            for(var week=0; week<6; week++)
            {
                excalCon += "<tr>";
                for(var dayOfWeek=0; dayOfWeek < 7; dayOfWeek++) {
                    if(week == 0 && startDayOfWeek == dayOfWeek) {
                        validDay = 1;
                    } else if (validDay == 1 && dayOfMonth > daysInMonth) {
                        validDay = 0;
                    }

                    if(validDay) {
                        var curTimestamp = Date.parse(currentYearObj[calendarContainerID] + '/' + currentMonthObj[calendarContainerID] + '/' + dayOfMonth + ' 00:00:00');
                        //console.log(curTimestamp);
                        if (dayOfWeek == 0 || dayOfWeek == 6) {
                            cssClass = 'weekend';
                            if(curTimestamp < todayTimestamp){
                                cssClass = 'weekend invalid';
                            }else{
                                if(dayOfMonth == thisDay && currentMonthObj[calendarContainerID] == thisMonth && currentYearObj[calendarContainerID] == thisYear
                                        && dayOfMonth == selectedDay && currentYearObj[calendarContainerID] == selectedYear && currentMonthObj[calendarContainerID] == selectedMonth ){
                                    cssClass = 'weekend current today';
                                }else if (dayOfMonth == selectedDay && currentYearObj[calendarContainerID] == selectedYear && currentMonthObj[calendarContainerID] == selectedMonth) {
                                    cssClass = 'weekend current';
                                }else if(dayOfMonth == thisDay && currentMonthObj[calendarContainerID] == thisMonth && currentYearObj[calendarContainerID] == thisYear){
                                    cssClass = 'weekend today';
                                }
                            }
                        } else {
                            cssClass = 'weekday';
                            if(curTimestamp < todayTimestamp){
                                cssClass = 'weekday invalid';
                            }else{
                                if(dayOfMonth == thisDay && currentMonthObj[calendarContainerID] == thisMonth && currentYearObj[calendarContainerID] == thisYear
                                        && dayOfMonth == selectedDay && currentYearObj[calendarContainerID] == selectedYear && currentMonthObj[calendarContainerID] == selectedMonth ){
                                    cssClass = 'weekday current today';
                                }else if (dayOfMonth == selectedDay && currentYearObj[calendarContainerID] == selectedYear && currentMonthObj[calendarContainerID] == selectedMonth) {
                                    cssClass = 'weekday current';
                                }else if(dayOfMonth == thisDay && currentMonthObj[calendarContainerID] == thisMonth && currentYearObj[calendarContainerID] == thisYear){
                                    cssClass = 'weekday today';
                                }
                            }
                        }

                        if(curTimestamp < todayTimestamp){
                            excalCon += "<td><a class='"+cssClass+"' id='"+calendarContainerID+"_"+currentYearObj[calendarContainerID]+"_"+currentMonthObj[calendarContainerID]+"_"+dayOfMonth
                                        + "' name='"+calendarContainerID+"_cal_date_link' href='javascript:;'>"+dayOfMonth+"</a></td>";
                        }else{
                             excalCon += "<td><a class='"+cssClass+"' id='"+calendarContainerID+"_"+currentYearObj[calendarContainerID]+"_"+currentMonthObj[calendarContainerID]+"_"+dayOfMonth
                                        + "' name='"+calendarContainerID+"_cal_date_link' onclick=\"javascript:exCal.setCalendarDate("
                                        + currentYearObj[calendarContainerID]+","+currentMonthObj[calendarContainerID]+","+dayOfMonth+",'"+calendarContainerID+"'"
                                        + ")\" href='javascript:;'>"+dayOfMonth+"</a></td>";
                        }
                        dayOfMonth++;
                    } else {
                        excalCon += "<td class='empty'>&nbsp;</td>";
                    }
                }
                excalCon += "</tr>";
            }
            //结束生成正常模式的日历
        }

        excalCon += "</table></div>";

        return excalCon;
    }

    function isArray(obj) {
        if(obj.constructor.toString().indexOf("Array") == -1){
            return false;
        }else{
            return true;
        }
    };

    function removeArrayItemById(myArray, itemIDToRemove) {
        if(!isArray(myArray) || isNaN(itemIDToRemove)){
            return false;
        }
        myArray.splice(itemIDToRemove, 1);
        return myArray;
    };

    this.showExCalendar= showExCalendar;
    function showExCalendar(calendarContainerID) {

        //如果当前填充字段不为空，则取过来。
        if(dateTimeFieldBoxArr[calendarContainerID]) {
            try {
                var dateString = new String(dateTimeFieldBoxArr[calendarContainerID].value);
                var dateParts = dateString.split("-");

                selectedYear = parseInt(dateParts[0],10);
                selectedMonth = parseInt(dateParts[1],10);
                selectedDay = parseInt(dateParts[2],10);

            } catch(e) {}
        }

        //如果当前填充字段为空，则取当天日期。
        if (!(selectedYear && selectedMonth && selectedDay)) {
            selectedMonth = getCurrentMonth();
            selectedDay = getCurrentDay();
            selectedYear = getCurrentYear();
        }

        currentYearObj[calendarContainerID] = selectedYear;
        currentMonthObj[calendarContainerID] = selectedMonth;
        currentDayObj[calendarContainerID] = selectedDay;

        if(document.getElementById){
            //console.log(calendarContainerID)
            calendar = document.getElementById(calendarContainerID);
            calendar.innerHTML = drawExCalendar(calendarContainerID);
            //setProperty('display', 'block', calendarContainerID);
        }
    }

}

var exCal = {
    version : "0.1"
};

(function(){

    //new calendar object.
    var exCalObj = new exCalendar();

    //main function
    exCal.initExCalendar = function(calendarContainerID, textFieldContainerID, calendarCallBack, calendarModel, autoHiddenContainer){
        exCalObj.initialize(calendarContainerID, textFieldContainerID, calendarCallBack, calendarModel, autoHiddenContainer);
    };

    //clear calendar..
    exCal.clearCalendar = function() {
        exCalObj.clearDate();
    };

    //hidden calendar
    exCal.hideCalendar = function() {
        if (exCalObj.visible()) {
            exCalObj.hide();
        }
    };

    //set calendar date
    exCal.setCalendarDate = function(year, month, day, calContainerID) {
        exCalObj.setDate(year, month, day, calContainerID);
    };

    //while user change year
    exCal.changeCalendarYear = function(change) {
        exCalObj.changeYear(change);
    };

    //while user change month.
    exCal.changeCalendarMonth = function(change, calContainerID) {
        exCalObj.changeMonth(change, calContainerID);
    };

    document.write("<div id='exCalendarContainer'></div>");
})();
