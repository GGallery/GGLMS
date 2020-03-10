///////////////// KENDO HELPER /////////////////

/****************** PANELS ********************/
/// <summary>
/// Function to create a KendoPanelBar.
/// </summary>
/// <param name="id">Requires a string argument in format "#name_id".</param>
/// <param name="expandMode">Requires a string argument specifies how the PanelBar items are displayed when opened and closed.
///     The following values are available: "single" and "multiple".
/// </param>
/// <param name="expandAll">Requires a bool argument to expand all items.</param>
function createPanels(id, expandMode, expandAll) {

    $(id).kendoPanelBar({
        animation: {
            expand: {
                duration: 100,
                effects: "expandVertical fadeIn"
            }
        },
        expandMode: expandMode
    });
    var panelBar = $(id).data("kendoPanelBar");

    if (expandAll) {
        panelBar.expand($(id).children())
    }

}

/// <summary>
/// Function to set a custom javascript function on a PanelBar's event.
/// </summary>
/// <param name="id">Requires a string argument in format "#name_id".</param>
/// <param name="event">Requires a string argument containing the name of event. 
///     List of events managed: "activate", "collapse", "contentLoad", "error", "expand" and "select".
/// </param>
/// <param name="bindAction">Requires a javascript function.</param>
/// <returns>The function returns an event javascript object.</returns>
function setPanels(id, event, bindAction) {

    var tabStrip = $(id).data("kendoPanelBar");
    tabStrip.bind(event, bindAction);

}
/**********************************************/


/***************** TABSTRIP *******************/
/// <summary>
/// Function to create a KendoTabStrip.
/// </summary>
/// <param name="id">Requires a string argument in format "#name_id".</param>
/// <param name="animation">Requires a bool argument.</param>
function createTabStrip(id, animation) {

    var _animation;
    if (animation) {
        _animation = {
            open: {
                effects: "fadeIn",
                duration: 100
            }
        };
    }
    else {
        _animation = false;
    }
    $(id).kendoTabStrip({
        animation: _animation
    });

}

/// <summary>
/// Function to resize the height of all the tabs in a tabstrip.
/// </summary>
/// <param name="id">Requires a string argument in format "#name_id". It must be the id of the kendo tabstrip element.</param>
function resizeTabsHeight(id) {

    var tabsDivs = $(id).children(".k-content");
    tabsDivs.outerHeight(Math.floor($(id).height() - $(id).children(".k-tabstrip-items").outerHeight(true)));

}

/// <summary>
/// Function to set a custom javascript function on a TabStrip's event.
/// </summary>
/// <param name="id">Requires a string argument in format "#name_id".</param>
/// <param name="event">Requires a string argument containing the name of event. 
///     List of events managed: "change".
/// </param>
/// <param name="bindAction">Requires a javascript function.</param>
/// <returns>The function returns an event javascript object.</returns>
function setTabStrip(id, event, bindAction) {

    var tabStrip = $(id).data("kendoTabStrip");
    tabStrip.bind(event.toLowerCase(), bindAction);

}

/// <summary>
/// Function to get the activeTab's index.
/// </summary>
/// <param name="id">Requires a string argument in format "#name_id".</param>
/// <returns>The method returns an integer.</returns>
function getActiveTabIndex(id) {

    return $(id).data("kendoTabStrip").select().index();

}
/**********************************************/


/****************** TOOLBAR *******************/
/// <summary>
/// Function to create a KendoToolBar.
/// </summary>
/// <param name="id">Requires a string argument in format "#name_id".</param>
function createToolBar(id) {

    $(id).kendoToolBar();

}

/// <summary>
/// Function to set a custom javascript function on a toolbar's event.
/// </summary>
/// <param name="id">Requires a string argument in format "#name_id".</param>
/// <param name="event">Requires a string argument containing the name of event. 
///     List of events managed: "click", "toggle", "open", "close", "overflowOpen", "overflowClose".
/// </param>
/// <param name="bindAction">Requires a javascript function.</param>
/// <returns>The function returns an event javascript object.</returns>
function setToolBar(id, event, bindAction) {

    var toolbar = $(id).data("kendoToolBar");
    toolbar.bind(event.toLowerCase(), bindAction);

}

/// <summary>
/// Function to add a button in the kendoToolBar.
/// </summary>
/// <param name="id">Requires a string argument in format "#name_id".</param>
/// <param name="buttonId">Requires a string argument specifies the ID of the button.</param>
/// <param name="icon">Requires a string argument containing the icon for the item.
///     The icon should be one of the existing in the Kendo UI theme sprite.
/// </param>
/// <param name="type">Requires a string argument containing the type of button.
///     List of button's type managed: "button", "splitButton".
/// </param>
/// <param name="text">Requires a string argument specifies the text of the menu button.</param>
/// <param name="togglable">Requires a bool argument specifies if the button is togglable.</param>
/// <param name="overflow">Requires a string argument specifies how the button behaves when the ToolBar is resized.
///     Possible values are: "always", "never" or "auto" .
/// </param>
/// <param name="menuButtons">Requires an array object argument specifies the menu buttons of a SplitButton.
///     The menuButtons object must be like: { id: "name", text: "description", icon: "name_icon" }.
/// </param>
/// <param name="enable">Requires a  boolean</param>
function addToolBarButton(id, buttonId, icon, type, text, togglable, overflow, menuButtons, enable) {

    var toolbar = $(id).data("kendoToolBar");
    switch (type.toLowerCase()) {
        case "button":
            toolbar.add({
                id: buttonId,
                icon: (icon === null || icon === "") ? "" : icon,
                type: type,
                text: text,
                togglable: togglable,
                overflow: overflow,
                enable: enable == undefined ? true : enable

            });
            break;
        case "splitbutton":
            toolbar.add({
                id: buttonId,
                icon: (icon === null || icon === "") ? "" : icon,
                type: "splitButton",
                text: text,
                togglable: togglable,
                menuButtons: menuButtons,
                overflow: overflow,
                enable: enable == undefined ? true : enable
            });
            break;
    }
    if (overflow !== "always") {
        toolbar.add({ type: "separator" });
    }
}

/// <summary>
/// Function to enable/disable toolbar button.
/// </summary>
/// <param name="toolbarId">Requires a string argument in format "#name_id".</param>
/// <param name="buttonIdList">Requires an array of string in format "#name_id"
/// <param name="btnType">Requires a string argument containing the type of buttons.Buttons must be of the same type
///     List of button's type managed: "button", "splitButton"..</param>
/// <param name="enable">Boolean</param>
function enableToolBarButton(toolbarId, buttonIdList, btnType, enable) {
    var toolbar = $(toolbarId).data("kendoToolBar");

    switch (btnType) {
        case "splitButton":

            $.each(buttonIdList, function (i, btnItem) {
                toolbar.enable(btnItem, enable);
            });

            break;
        case "button":

            $.each(buttonIdList, function (i, btnItem) {
                toolbar.enable($(btnItem), enable);
            });

            break;
    }

}

/// <summary>
/// Function to add a kendo filter in the toolbar.
/// </summary>
/// <param name="id">Requires a string argument in format "#name_id".</param>
/// <param name="overflow">Requires a string argument specifies how the button behaves when the ToolBar is resized.
///     Possible values are: "always", "never" or "auto" .
/// </param>
/// <param name="label">Requires a string argument to set a label for a the filter.
///     If null or "" the label it's hide. 
/// </param>
/// <param name="idFilter">Requires a string argument in format "name_id".</param>
/// <param name="filterType">Requires a string argument with the type of filter to create. 
///     List of filters managed: ComboBox, DropDownList, MultiSelect, DatePicker, MonthPicker.
/// </param>
/// <param name="dataFilter">Requires an object array argument.</param>
/// <param name="customOption1">Requires a bool argument.
///     With type == "ComboBox" it is not used.
///     With type == "DropDownList" it enable/disable the empty field.
///     With type == "MultiSelect" it enable/disable the select all management.
///     With type == "DatePicker" it enable/disable the filter.
///     With type == "MonthPicker" it enable/disable the filter.
/// </param>
function addToolBarFilterButton(id, overflow, label, idFilter, filterType, dataFilter, customOption1) {

    var toolbar = $(id).data("kendoToolBar");

    if (label !== null && label !== "" && label !== undefined) {
        toolbar.add({
            template: "<label>" + label + "</label>"
        });
    }

    switch (filterType.toLowerCase()) {
        case "combobox":
            toolbar.add({
                template: "<input id='" + idFilter + "' />"
            });
            _createFilterComboBox("#" + idFilter, "value", "text");
            break;
        case "dropdownlist":
            toolbar.add({
                template: "<input id='" + idFilter + "' />"
            });
            _createFilterDropDownList("#" + idFilter, "value", "text", customOption1);
            break;
        case "multiselect":
            toolbar.add({
                template: "<select id='tab1_filterStation' multiple='multiple'></select>"
            });
            _createFilterMultiSelect("#" + idFilter, "value", "text", customOption1);
            break;
        case "datepicker":
            toolbar.add({
                template: "<input id='" + idFilter + "' />"
            });
            _createFilterDatePicker("#" + idFilter, customOption1);
            break;
        case "monthpicker":
            toolbar.add({
                template: "<input id='" + idFilter + "' />"
            });
            _createFilterMonthPicker("#" + idFilter, customOption1);
            break;
    }
    populateFilter("#" + idFilter, filterType, dataFilter);

    if (overflow !== "always") {
        toolbar.add({ type: "separator" });
    }

}
/**********************************************/


/******************* FILTER *******************/
var selectAllId = -2;

/// <summary>
/// Function to create a filter.
/// </summary>
/// <param name="id">Requires a string argument in format "#name_id".</param>
/// <param name="type">Requires a string argument with the type of filter to create. 
///     List of filters managed: ComboBox, DropDownList, MultiSelect, DatePicker, MonthPicker, TimePicker.
/// </param>
/// <param name="valueField">Requires a string argument with the valueField's name to bind data.
///     If empty or null is "value" by default.
///     With type == "DatePicker" it is not used.
///     With type == "MonthPicker" it is not used.
///     With type == "TimePicker" it is not used.
/// </param>
/// <param name="textField">Requires a string argument with the textField's name to bind data. 
///     If empty or null is "text" by default.
///     With type == "DatePicker" it is not used.
///     With type == "MonthPicker" it is not used.
///     With type == "TimePicker" it is not used.
/// </param>
/// <param name="customOption1">Requires a bool argument.
///     With type == "ComboBox" it is not used.
///     With type == "DropDownList" it enable/disable the empty field.
///     With type == "MultiSelect" it enable/disable the select all management.
///     With type == "DatePicker" it enable/disable the filter.
///     With type == "MonthPicker" it enable/disable the filter.
///     With type == "TimePicker" it enable/disable the filter.
/// </param>
function createFilter(id, type, valueField, textField, customOption1) {

    var nameValueField = (valueField === null || valueField === "" || valueField === undefined) ? "value" : valueField;
    var nameTextField = (textField === null || textField === "" || textField === undefined) ? "text" : textField;
    switch (type.toLowerCase()) {
        case "combobox":
            _createFilterComboBox(id, nameValueField, nameTextField);
            break;
        case "dropdownlist":
            _createFilterDropDownList(id, nameValueField, nameTextField, customOption1);
            break;
        case "multiselect":
            _createFilterMultiSelect(id, nameValueField, nameTextField, customOption1);
            break;
        case "datepicker":
            _createFilterDatePicker(id, customOption1);
            break;
        case "monthpicker":
            _createFilterMonthPicker(id, customOption1);
            break;
        case "datetimepicker":
            _createFilterDateTimePicker(id, customOption1);
            break;
        case "timepicker":
            _createFilterTimePicker(id, customOption1);
            break;
    }

}

/// <summary>
/// Function to populate the filter's dataSource.
/// </summary>
/// <param name="id">Requires a string argument in format "#name_id".</param>
/// <param name="type">Requires a string argument with the type of filter to populate. 
///     List of filters managed: ComboBox, DropDownList, MultiSelect.
/// </param>
/// <param name="valueField">Requires a string argument with the valueField's name to bind data.</param>
/// <param name="textField">Requires a string argument with the textField's name to bind data.</param>
/// <param name="data">Requires an object array argument.</param>

function populateFilter(id, type, data, skipForceEnable) {

    switch (type.toLowerCase()) {
        case "combobox":
            var comboBox = $(id).data("kendoComboBox");
            comboBox.setDataSource(data);
            if (!skipForceEnable) {
                comboBox.enable();
            }
            break;
        case "dropdownlist":
            var dropDownList = $(id).data("kendoDropDownList");
            dropDownList.setDataSource(data);
            if (!skipForceEnable) {
                dropDownList.enable();
            }
            break;
        case "multiselect":

            var multiSelect = $(id).data("kendoMultiSelect");

            if (multiSelect.dataSource.data().length > 0) {
                var selectAllItem = multiSelect.dataSource.data()[0];
                multiSelect.dataSource.data(data); //populate multiselect
                var filterData = multiSelect.dataSource.data();
                filterData.splice(0, 0, selectAllItem); //insert SelectAll in first position
                multiSelect.dataSource.data(filterData);
            }
            else {
                multiSelect.setDataSource(data);
            }
            if (!skipForceEnable) {
                multiSelect.enable();
            }
            break;
    }

}

/// <summary>
/// Function to set a custom javascript function on a filter's event.
/// </summary>
/// <param name="id">Requires a string argument in format "#name_id".</param>
/// <param name="type">Requires a string argument with the type of filter to populate. 
///     List of filters managed: ComboBox, DropDownList, MultiSelect, DatePicker, MonthPicker, TimePicker.
/// </param>
/// <param name="event">Requires a string argument containing the name of event. 
///     List of events managed: "open", "close", "change", "select", "filtering", "dataBinding", "dataBound", "cascade".
///     PLEASE NOTE: event select can't be changed on a multiselect filter because it is set to select all management.
/// </param>
/// <param name="bindAction">Requires a javascript function.</param>
/// <returns>The function returns an event javascript object.</returns>
function setFilter(id, type, event, bindAction) {

    var privateFunction = "";
    var filter;
    switch (type.toLowerCase()) {
        case "combobox":
            filter = $(id).data("kendoComboBox");
            break;
        case "dropdownlist":
            filter = $(id).data("kendoDropDownList");
            break;
        case "multiselect":
            filter = $(id).data("kendoMultiSelect");
            switch (event.toLowerCase()) {
                case "select":
                    return;// this event is a private functionality                    
                    break;
            }
            break;
        case "datepicker":
            filter = $(id).data("kendoDatePicker");
            break;
        case "datetimepicker":
            filter = $(id).data("kendoDateTimePicker");
            break;
        case "monthpicker":
            filter = $(id).data("kendoDatePicker");
            break;
        case "timepicker":
            filter = $(id).data("kendoTimePicker");
            break;
    }
    filter.bind(event.toLowerCase(), bindAction);

}

/// <summary>
/// Function to deselect all filter's items.
/// </summary>
/// <param name="id">Requires a string argument in format "#name_id".</param>
/// <param name="type">Requires a string argument with the type of filter to populate. 
///     List of filters managed: ComboBox, DropDownList, MultiSelect.
/// </param>
/// <param name="disable">Requires a bool argument.</param>
function resetFilter(id, type, disable) {

    var filter;
    switch (type.toLowerCase()) {
        case "combobox":
            filter = $(id).data("kendoComboBox");
            filter.value("");//clean filter
            break;
        case "dropdownlist":
            filter = $(id).data("kendoDropDownList");
            filter.value("");//clean filter
            break;
        case "multiselect":
            filter = $(id).data("kendoMultiSelect");
            filter.value("");//clean filter
            break;
        case "datetimepicker":
            filter = $(id).data("kendoDateTimePicker");
            filter.value(null);//clean filter
            break;
        case "datepicker":
            filter = $(id).data("kendoDatePicker");
            filter.value(null);//clean filter
            break;
    }

    if (disable) {
        filter.enable(false);//disable filter
    }
    else {
        filter.enable(true);//enable filter
    }

}

/// <summary>
/// Function to select all multiselect's values.
/// </summary>
/// <param name="id">Requires a string argument in format "#name_id".</param>
function selectAllMultiSelectValues(id) {
    var multiSelect = $(id).data("kendoMultiSelect");
    var itemIdList = $.map(multiSelect.element.find("option"), function (item) {
        return $(item).attr("value");
    });
    multiSelect.value(itemIdList);
}

/// <summary>
/// Function to get the current filter's values selected.
/// </summary>
/// <param name="id">Requires a string argument in format "#name_id".</param>
/// <param name="type">Requires a string argument with the type of filter to populate. 
///     List of filters managed: ComboBox, DropDownList, MultiSelect, DatePicker and MonthPicker.
/// </param>
/// <returns>The function returns:
///     - with type = ComboBox, DropDownList and MultiSelect an array containing the filter's value;
///     - with type = DatePicker and DateTimePicker and TimePicker the date value selected (null if empty);
///     - with type = MonthPicker an object containing the firstDay and the lastDay of the selected month (firstDay=null and lastDay=null if empty);
/// </returns>
function getFilterSelectedValues(id, type) {

    switch (type.toLowerCase()) {
        case "combobox":
            if ($(id).data("kendoComboBox").value() !== "") {
                var value = $(id).data("kendoComboBox").value();
                if (isNaN(parseInt(value))) {
                    return [value]; //return string in Array
                }
                else {
                    return [parseInt(value)]; //return int in Array
                }
            }
            else {
                return new Array(); //return empty Array
            }
        case "dropdownlist":
            if ($(id).data("kendoDropDownList").value() !== "") {
                var value = $(id).data("kendoDropDownList").value();
                if (isNaN(parseInt(value))) {
                    return [value]; //return string in Array
                }
                else {
                    return [parseInt(value)]; //return int in Array
                }
            }
            else {
                return new Array(); //return empty Array
            }
        case "multiselect":
            var values = $(id).data("kendoMultiSelect").value();
            if ($.inArray(selectAllId, values) > -1) {
                values.splice($.inArray(selectAllId, values), 1);
                return values;
            }
            else {
                return values;
            }
        case "datepicker":
            var selectedDate = $(id).data("kendoDatePicker").value();
            if (selectedDate !== null) {

                //avoid datePicker date reference
                var tmp = new Date(selectedDate);

                //set time 00:00:00:00
                tmp.setHours(0);
                tmp.setMinutes(0);
                tmp.setSeconds(0);
                tmp.setMilliseconds(0);

                //correct time offset
                tmp.setHours(tmp.getHours() - tmp.getTimezoneOffset() / 60);
                tmp.setMinutes(tmp.getMinutes() - tmp.getTimezoneOffset() % 60);
                return tmp;
            }
            else {
                return null; //null if no data selected
            }
        case "monthpicker":
            var selectedDate = $(id).data("kendoDatePicker").value();
            if (selectedDate !== null) {

                //avoid datePicker date reference                
                var firstDay = new Date(selectedDate.getFullYear(), selectedDate.getMonth(), 1);
                var lastDay = new Date(selectedDate.getFullYear(), selectedDate.getMonth() + 1, 0);

                //set firstDay time 00:00:00:00
                firstDay.setHours(0);
                firstDay.setMinutes(0);
                firstDay.setSeconds(0);
                firstDay.setMilliseconds(0);

                //set lastDay time 00:00:00:00
                lastDay.setHours(0);
                lastDay.setMinutes(0);
                lastDay.setSeconds(0);
                lastDay.setMilliseconds(0);

                //correct time offset
                firstDay.setHours(firstDay.getHours() - firstDay.getTimezoneOffset() / 60);
                firstDay.setMinutes(firstDay.getMinutes() - firstDay.getTimezoneOffset() % 60);
                lastDay.setHours(lastDay.getHours() - lastDay.getTimezoneOffset() / 60);
                lastDay.setMinutes(lastDay.getMinutes() - lastDay.getTimezoneOffset() % 60);

                return {
                    "firstDay": firstDay,
                    "lastDay": lastDay
                };
            }
            else {
                return {
                    "firstDay": null,
                    "lastDay": null
                }; //null if no data selected
            }
            break;
        case "datetimepicker":
            var selectedDate = $(id).data("kendoDateTimePicker").value();
            if (selectedDate !== null) {
                //avoid datePicker date reference
                var tmp = new Date(selectedDate);
                //correct time offset
                tmp.setHours(tmp.getHours() - tmp.getTimezoneOffset() / 60);
                tmp.setMinutes(tmp.getMinutes() - tmp.getTimezoneOffset() % 60);
                return tmp;
            }
            else {
                return null; //null if no data selected
            }
            break;
        case "timepicker":
            var selectedDate = $(id).data("kendoTimePicker").value();
            if (selectedDate !== null) {
                //avoid timePicker date reference
                var tmp = new Date(selectedDate);
                //correct time offset
                tmp.setHours(tmp.getHours() - tmp.getTimezoneOffset() / 60);
                tmp.setMinutes(tmp.getMinutes() - tmp.getTimezoneOffset() % 60);
                return tmp;
            }
            else {
                return null; //null if no data selected
            }
            break;

    }

}

/// <summary>
/// Function to enanble timeSelection for  dateTimePicker filter. 
///NB: FilterType must be DateTimePicker 
/// </summary>
/// <param name="id">Requires a string argument in format "#name_id".</param>  // deprecated
function enableTimeSelection(dateTimeFilterId) {

    var filter = $(dateTimeFilterId).data("kendoDateTimePicker");
    var selectedDate = filter.value();
    //filter.options.format = "dd/MM/yyyy HH:mm";
    $('.k-i-clock').show();

    var userCulture = ($.cookie('UserCulture') == undefined) ? "en-us" : $.cookie('UserCulture').toLowerCase();
    switch (userCulture) {
        // valid for input format and parse format
        case "it-it":
            filter.options.format = "dd/MM/yyyy HH:mm";
            filter.options.parseFormats = ["dd/MM/yyyy HH:mm"];
            break;
        case "en-us":
            filter.options.format = "MM/dd/yyyy HH:mm";
            filter.options.parseFormats = ["MM/dd/yyyy HH:mm"];
            break;
        default:
            filter.options.format = "MM/dd/yyyy HH:mm";
            filter.options.parseFormats = ["MM/dd/yyyy HH:mm"];
            break;

    }


    //if (selectedDate !== null) {

    //    var year = selectedDate.getFullYear();
    //    var month = selectedDate.getMonth();
    //    var day = selectedDate.getDate();

    //    var newStart = new Date(year, month, day, 0, 0, 0, 0);
    //    filter.value(newStart);
    //}



}

/// <summary>
/// Function to disable timeSelection  on datetimePicker 
///NB: FilterType must be DateTimePicker 
/// </summary>
/// <param name="id">Requires a string argument in format "#name_id".</param>
function disableTimeSelection(dateTimeFilterId) {

    var filter = $(dateTimeFilterId).data("kendoDateTimePicker");
    var selectedDate = filter.value();

    $('.k-i-clock').hide();


    var userCulture = ($.cookie('UserCulture') == undefined) ? "en-us" : $.cookie('UserCulture').toLowerCase();
    switch (userCulture) {
        // valid for input format and parse format
        case "it-it":
            filter.options.format = "dd/MM/yyyy";
            filter.options.parseFormats = ["dd/MM/yyyy"];
            break;
        case "en-us":
            filter.options.format = "MM/dd/yyyy";
            filter.options.parseFormats = ["MM/dd/yyyy"];
            break;
        default:
            filter.options.format = "MM/dd/yyyy";
            filter.options.parseFormats = ["MM/dd/yyyy"];
            break;

    }


    if (selectedDate !== null) {

        var year = selectedDate.getFullYear();
        var month = selectedDate.getMonth();
        var day = selectedDate.getDate();

        var newDate = new Date(year, month, day, 0, 0, 0, 0);
        filter.value(newDate);


    }

}  // deprecated
/**********************************************/


/******************* CHART ********************/
//var chartSeriesColors = [
//        "#de5b5b", "#dede5b", "#5bde5b", "#5bdede", "#5b5bde",
//        "#de7c5b", "#bdde5b", "#5bde7c", "#5bbdde", "#7c5bde",
//        "#de9c5b", "#9cde5b", "#5bde9c", "#5b9cde", "#9c5bde",
//        "#debd5b", "#7cde5b", "#5bdebd", "#5b7cde", "#bd5bde"
//];

var chartSeriesColors = [
        "#5bdede", "#5b5bde", "#de5b5b", "#dede5b", "#5bde5b",
        "#5bbdde", "#7c5bde", "#de7c5b", "#bdde5b", "#5bde7c",
        "#5b9cde", "#9c5bde", "#de9c5b", "#9cde5b", "#5bde9c",
        "#5b7cde", "#bd5bde", "#debd5b", "#7cde5b", "#5bdebd"
];

/// <summary>
/// Functions to create a Chart. List of charts managed: PieChart, BarChart, StackedBarChart, MultiAxisBarChart, LineChart.
/// </summary>
/// <param name="id">Requires a string argument in format "#name_id".</param>
/// <param name="legendVisible">Requires a bool argument.</param>
/// <param name="legendPosition">Requires a string argument.
///     List of position managed: top, bottom, left, right, custom (the legend is positioned using legend.offsetX and legend.offsetY).
/// </param>
/// <param name="labelsVisible">Requires a bool argument.</param>
/// <param name="titlePosition">Requires a string argument.
///     List of position managed: top, bottom.
/// </param>
/// <param name="titleVisible">Requires a bool argument.</param>
/// <param name="tooltipVisible">Requires a bool argument.</param>
/// <param name="animation">Requires a bool argument.</param>
function createPieChart(id, legendVisible, legendPosition, labelsVisible, titlePosition, titleVisible, tooltipVisible, animation) {

    $(id).kendoChart({
        legend: {
            visible: legendVisible,
            position: legendPosition
        },
        seriesColors: chartSeriesColors,
        seriesDefaults: {
            type: "pie",
            labels: {
                visible: labelsVisible,
                background: "transparent"
            }
        },
        theme: "Bootstrap",
        title: {
            position: titlePosition,
            visible: titleVisible
        },
        tooltip: {
            visible: tooltipVisible
        },
        transitions: animation
    });

    //hide chart because it's empty at the moment
    $(id).css("opacity", 0);

}
function createBarChart(id, legendVisible, legendPosition, labelsVisible, titlePosition, titleVisible, tooltipVisible, animation) {

    $(id).kendoChart({
        categoryAxis: {
            labels: {
                rotation: "auto"
            },
            line: {
                visible: true
            },
            majorGridLines: {
                visible: true
            }
        },
        legend: {
            visible: legendVisible,
            position: legendPosition
        },
        pannable: {
            lock: "y"
        },
        seriesColors: chartSeriesColors,
        seriesDefaults: {
            type: "column",
            labels: {
                visible: labelsVisible,
                background: "transparent"
            }
        },
        theme: "Bootstrap",
        title: {
            position: titlePosition,
            visible: titleVisible
        },
        tooltip: {
            visible: tooltipVisible
        },
        transitions: animation,
        valueAxis: {
            majorGridLines: {
                visible: true
            },
            visible: true
        },
        zoomable: {
            mousewheel: {
                lock: "y"
            },
            selection: {
                lock: "y"
            }
        }
    });

    //hide chart because it's empty at the moment
    $(id).css("opacity", 0);

    //double click to reset the zoom
    $(id).dblclick(function (e) {
        var chartId = "#" + this.id;
        $(chartId).data("kendoChart").redraw();
    });

}
function createStackedBarChart(id, legendVisible, legendPosition, labelsVisible, titlePosition, titleVisible, tooltipVisible, animation) {

    $(id).kendoChart({
        categoryAxis: {
            labels: {
                rotation: "auto"
            },
            line: {
                visible: true
            },
            majorGridLines: {
                visible: true
            }
        },
        legend: {
            visible: legendVisible,
            position: legendPosition
        },
        pannable: {
            lock: "y"
        },
        seriesColors: chartSeriesColors,
        seriesDefaults: {
            type: "column",
            stack: true,
            labels: {
                visible: labelsVisible,
                background: "transparent"
            }
        },
        theme: "Bootstrap",
        title: {
            position: titlePosition,
            visible: titleVisible
        },
        tooltip: {
            visible: tooltipVisible
        },
        transitions: animation,
        valueAxes: {
            majorGridLines: {
                visible: true
            },
            visible: true
        },
        zoomable: {
            mousewheel: {
                lock: "y"
            },
            selection: {
                lock: "y"
            }
        }
    });

    //hide chart because it's empty at the moment
    $(id).css("opacity", 0);

    //double click to reset the zoom
    $(id).dblclick(function (e) {
        var chartId = "#" + this.id;
        $(chartId).data("kendoChart").redraw();
    });

}
function createMultiAxisBarChart(id, legendVisible, legendPosition, labelsVisible, titlePosition, titleVisible, tooltipVisible, animation) {

    $(id).kendoChart({
        categoryAxis: {
            labels: {
                rotation: "auto"
            },
            line: {
                visible: true
            },
            majorGridLines: {
                visible: true
            }
        },
        legend: {
            visible: legendVisible,
            position: legendPosition
        },
        pannable: {
            lock: "y"
        },
        seriesColors: chartSeriesColors,
        seriesDefaults: {
            type: "column",
            labels: {
                visible: labelsVisible,
                background: "transparent"
            }
        },
        theme: "Bootstrap",
        title: {
            position: titlePosition,
            visible: titleVisible
        },
        tooltip: {
            visible: tooltipVisible
        },
        transitions: animation,
        valueAxes: {
            majorGridLines: {
                visible: true
            },
            visible: true
        },
        zoomable: {
            mousewheel: {
                lock: "y"
            },
            selection: {
                lock: "y"
            }
        }
    });

    //hide chart because it's empty at the moment
    $(id).css("opacity", 0);

    //double click to reset the zoom
    $(id).dblclick(function (e) {
        var chartId = "#" + this.id;
        $(chartId).data("kendoChart").redraw();
    });

}
function createWaterfallChart(id, legendVisible, legendPosition, labelsVisible, titlePosition, titleVisible, tooltipVisible, animation) {

    $(id).kendoChart({
        categoryAxis: {
            labels: {
                rotation: "auto"
            },
            line: {
                visible: true
            },
            majorGridLines: {
                visible: true
            }
        },
        legend: {
            visible: legendVisible,
            position: legendPosition
        },
        pannable: {
            lock: "y"
        },
        seriesDefaults: {
            type: "waterfall",//"horizontalWaterfall"
            labels: {
                visible: labelsVisible,
                background: "transparent"
            }
        },
        theme: "Bootstrap",
        title: {
            position: titlePosition,
            visible: titleVisible
        },
        tooltip: {
            visible: tooltipVisible
        },
        transitions: animation,
        valueAxis: {
            majorGridLines: {
                visible: true
            },
            visible: true
        }
    });

    //hide chart because it's empty at the moment
    $(id).css("opacity", 0);


}
function createLineChart(id, legendVisible, legendPosition, labelsVisible, titlePosition, titleVisible, tooltipVisible, animation) {

    $(id).kendoChart({
        categoryAxis: {
            labels: {
                rotation: "auto"
            },
            line: {
                visible: true
            },
            majorGridLines: {
                visible: true
            }
        },
        legend: {
            visible: legendVisible,
            position: legendPosition
        },
        pannable: {
            lock: "y"
        },
        seriesDefaults: {
            type: "line",
            style: "smooth",
            labels: {
                visible: labelsVisible,
                background: "transparent"
            }
        },
        theme: "Bootstrap",
        title: {
            position: titlePosition,
            visible: titleVisible
        },
        tooltip: {
            visible: tooltipVisible
        },
        transitions: animation,
        valueAxis: {
            majorGridLines: {
                visible: true
            },
            line: {
                visible: false
            },
            visible: true
        },
        zoomable: {
            mousewheel: {
                lock: "y"
            },
            selection: {
                lock: "y"
            }
        }
    });

    //hide chart because it's empty at the moment
    $(id).css("opacity", 0);

    //double click to reset the zoom
    $(id).dblclick(function (e) {
        var chartId = "#" + this.id;
        $(chartId).data("kendoChart").redraw();
    });

}

/// <summary>
/// Functions to draw a PieChart.
/// </summary>
/// <param name="id">Requires a string argument in format "#name_id".</param>
/// <param name="dataSet">Requires an object array argument to bind chart's dataSource.
///     The dataSource of the chart which is used to display the series.
/// </param>
/// <param name="series">Requires an object array argument.
///     The pie chart series configuration is: { field: "field_name", categoryField: "field_name" }.
///     The series type is determined by the value of the type field.   
/// </param>
/// <param name="labelsTemplate">Requires a string argument with the template which renders the chart series label.
///     To consult Telerik documentation to see the fields which can be used in the template.
/// </param>
/// <param name="tooltipTemplate">Requires a string argument with the template which renders the tooltip.
///     To consult Telerik documentation to see the fields which can be used in the template.
/// </param>
/// <param name="title">Requires a string argument.</param>
/// <param name="colorField"> Requires a string argument with the data item field which contains the series color.
///     If empty or null is set by default the chartSeriesColors array defined into this helper.
/// </param>
function drawPieChart(id, dataSet, series, labelsTemplate, tooltipTemplate, title, colorField) {

    var chart = $(id).data("kendoChart");
    chart.setDataSource(dataSet);
    chart.options.series = series;
    chart.options.seriesDefaults.labels.template = labelsTemplate;
    chart.options.tooltip.template = tooltipTemplate;
    chart.options.title.text = title;
    if (colorField !== undefined && colorField !== "" && colorField !== null) { chart.options.seriesDefaults.colorField = colorField };
    $(id).css("opacity", 1); //show chart
    chart.refresh();

}

/// <summary>
/// Functions to draw a BarChart.
/// </summary>
/// <param name="id">Requires a string argument in format "#name_id".</param>
/// <param name="dataSet">Requires an object array argument to bind chart's dataSource.
///     The dataSource of the chart which is used to display the series.
/// </param>
/// <param name="series">Requires an object array argument.
///     The bar chart series configuration is: { field: "field_name", categoryField: "field_name" }.
///     The series type is determined by the value of the type field.   
/// </param>
/// <param name="valueAxisLabelsFormat">Requires a string argument with the format used to display the labels.
///     To consult Telerik documentation to see the fields which can be used in the template.
/// </param>
/// <param name="tooltipTemplate">Requires a string argument with the template which renders the tooltip.
///     Uses kendo.format. Contains one placeholder ("{0}") which represents the category value.
/// </param>
/// <param name="title">Requires a string argument.</param>
/// <param name="colorField"> Requires a string argument with the data item field which contains the series color.
///     If empty or null is set by default the chartSeriesColors array defined into this helper.
/// </param>
function drawBarChart(id, dataSet, series, valueAxisLabelsFormat, tooltipTemplate, title, colorField) {

    var chart = $(id).data("kendoChart");
    chart.setDataSource(dataSet);
    chart.options.series = series;
    chart.options.valueAxis.labels.format = valueAxisLabelsFormat;
    chart.options.tooltip.template = tooltipTemplate;
    chart.options.title.text = title;
    if (colorField !== undefined && colorField !== "") { chart.options.seriesDefaults.colorField = colorField };
    $(id).css("opacity", 1); //show chart
    chart.refresh();
}

/// <summary>
/// Functions to draw a StackedBarChart.
/// </summary>
/// <param name="id">Requires a string argument in format "#name_id".</param>
/// <param name="dataSet">Requires an object array argument to bind chart's dataSource.
///     The dataSource of the chart which is used to display the series.
/// </param>
/// <param name="series">Requires an object array argument.
///     The stacked bar chart series configuration is: { name: "field_description", field: "field_name", categoryField: "field_name" }.
///     The bar chart no stacked series configuration is: { name: "field_description", field: "field_name", categoryField: "field_name", stack: false }.
///     The series type is determined by the value of the type field. 
/// </param>
/// <param name="tooltipTemplate">Requires a string argument with the template which renders the tooltip.
///     To consult Telerik documentation to see the fields which can be used in the template.
/// </param>
/// <param name="title">Requires a string argument.</param>
/// <param name="colorField"> Requires a string argument with the data item field which contains the series color.
///     If empty or null is set by default the chartSeriesColors array defined into this helper.
/// </param>
function drawStackedBarChart(id, dataSet, series, tooltipTemplate, title, colorField) {

    var chart = $(id).data("kendoChart");
    chart.setDataSource(dataSet);
    chart.options.series = series;
    chart.options.tooltip.template = tooltipTemplate;
    chart.options.title.text = title;
    if (colorField !== undefined && colorField !== "") { chart.options.seriesDefaults.colorField = colorField };
    $(id).css("opacity", 1); //show chart
    chart.refresh();

}

/// <summary>
/// Functions to draw a MultiAxisBarChart.
/// </summary>
/// <param name="id">Requires a string argument in format "#name_id".</param>
/// <param name="dataSet">Requires an object array argument to bind chart's dataSource.
///     The dataSource of the chart which is used to display the series.
/// </param>
/// <param name="series">Requires an object array argument.
///     The multi axis bar chart series configuration is: { name: "field_description", field: "field_name", categoryField: "field_name", axis: "name_axis" }.
///     The series type is determined by the value of the type field. 
/// </param>
/// <param name="valueAxis">Requires an object array argument with the value axis configuration options.
///     The valueAxis configuration is: { name: "name_axis", title: { text: "description" } }.
/// </param>
/// <param name="categoryAxisCrossingValues">Requires an integer array with maxlength == 2.
///     It set value at which the Y axis crosses this axis.
/// </param>
/// <param name="tooltipTemplate">Requires a string argument with the template which renders the tooltip.
///     To consult Telerik documentation to see the fields which can be used in the template.
/// </param>
/// <param name="title">Requires a string argument.</param>
/// <param name="colorField"> Requires a string argument with the data item field which contains the series color.
///     If empty or null is set by default the chartSeriesColors array defined into this helper.
/// </param>
function drawMultiAxisBarChart(id, dataSet, series, valueAxis, categoryAxisCrossingValues, categoryAxisLabelTemplate, tooltipTemplate, title, colorField) {

    var chart = $(id).data("kendoChart");
    chart.setDataSource(dataSet);
    chart.options.series = series;
    chart.options.valueAxis = valueAxis;
    chart.options.categoryAxis.axisCrossingValues = categoryAxisCrossingValues;
    if (categoryAxisLabelTemplate !== undefined && categoryAxisLabelTemplate !== "") { chart.options.categoryAxis.labels.template = categoryAxisLabelTemplate };
    chart.options.tooltip.template = tooltipTemplate;
    chart.options.title.text = title;
    if (colorField !== undefined && colorField !== "") { chart.options.seriesDefaults.colorField = colorField };
    $(id).css("opacity", 1); //show chart
    chart.refresh();
}

/// <summary>
/// Functions to draw a WaterfallChart.
/// </summary>
/// <param name="id">Requires a string argument in format "#name_id".</param>
/// <param name="dataSet">Requires an object array argument to bind chart's dataSource.
///     The dataSource of the chart which is used to display the series.
/// </param>
/// <param name="series">Requires an object array argument.
///     The multi axis bar chart series configuration is: { name: "field_description", field: "field_name", categoryField: "field_name", colorField: "color_name" }.
///     The series type is determined by the value of the type field. 
/// </param>
/// <param name="tooltipTemplate">Requires a string argument with the template which renders the tooltip.
///     To consult Telerik documentation to see the fields which can be used in the template.
/// </param>
/// <param name="title">Requires a string argument.</param>
/// <param name="colorField"> Requires a string argument with the data item field which contains the series color.
///     If empty or null is set by default the chartSeriesColors array defined into this helper.
/// </param>
function drawWaterfallChart(id, dataSet, series, tooltipTemplate, title, colorField) {

    var chart = $(id).data("kendoChart");
    chart.setDataSource(dataSet);
    chart.options.series = series;
    chart.options.tooltip.template = tooltipTemplate;
    chart.options.title.text = title;
    if (colorField !== undefined && colorField !== "") { chart.options.seriesDefaults.colorField = colorField };
    $(id).css("opacity", 1); //show chart
    chart.refresh();

}

/// <summary>
/// Functions to draw a LineChart.
/// </summary>
/// <param name="id">Requires a string argument in format "#name_id".</param>
/// <param name="dataSet">Requires an object array argument to bind chart's dataSource.
///     The dataSource of the chart which is used to display the series.
/// </param>
/// <param name="series">Requires an object array argument.
///     The line chart series configuration is: { name: "field_description", field: "field_name", categoryField: "field_name", colorField[or color]: "color_name" }.
///     The series type is determined by the value of the type field. 
/// </param>
/// <param name="tooltipTemplate">Requires a string argument with the template which renders the tooltip.
///     To consult Telerik documentation to see the fields which can be used in the template.
/// </param>
/// <param name="title">Requires a string argument.</param>
/// <param name="colorField"> Requires a string argument with the data item field which contains the series color.
///     If empty or null is set by default the chartSeriesColors array defined into this helper.
/// </param>
function drawLineChart(id, dataSet, series, valueAxisLabelsFormat, tooltipTemplate, title, colorField) {

    var chart = $(id).data("kendoChart");
    chart.setOptions({
        series: series,
        valueAxis: [{
            labels: { format: valueAxisLabelsFormat }
        }],
        tooltip: {
            template: tooltipTemplate
        },
        title: {
            text: title,
        }
    });
    if (colorField !== undefined && colorField !== "") {
        chart.options.seriesDefaults.colorField = colorField
    };
    chart.setDataSource(dataSet);
    $(id).css("opacity", 1); //show chart
    chart.refresh();

}

/// <summary>
/// Function to set a custom javascript function on a chart's event.
/// </summary>
/// <param name="id">Requires a string argument in format "#name_id".</param>
/// <param name="event">Requires a string argument containing the name of event. 
///     List of events managed: "seriesClick".
/// </param>
/// <param name="bindAction">Requires a javascript function.</param>
/// <returns>The function returns an event javascript object.</returns>
function setChart(id, event, bindAction) {
    var chart = $(id).data("kendoChart");
    switch (event.toLowerCase()) {
        case "seriesclick":
            chart.unbind("seriesClick", chart.options.seriesClick);
            if (bindAction !== null) {
                chart.bind("seriesClick", bindAction);
            }
            break;
        default:
            chart.bind(event, bindAction);
    }
}
/**********************************************/


/******************** POPUP *******************/
/// <summary>
/// Function to create a kendoWindow.
/// </summary>
/// <param name="id">Requires a string argument in format "#name_id".</param>
/// <param name="width">Requires a string argument in format "100px".
///     If set string "auto" the width is set automaticcally with the windows dimension.
/// </param>
/// <param name="height">Requires a string argument in format "100px".
///     If set string "auto" the height is set automatically with the windows dimension.
/// </param>
/// <param name="resizable">Requires a bool argument for enables (true) or disables (false) the ability for users to resize popup.</param>
/// <param name="actions">Requires an object array argument containing the buttons for interacting with the popup.
///     Predefined array values are "Close", "Refresh", "Minimize", and "Maximize".
/// </param>
function createPopup(id, width, height, resizable, actions) {

    $(id).hide();
    var popup = $(id).kendoWindow({
        position: { top: "1%", left: "1%" },
        modal: false,
        minWidth: 300//235
    }).data("kendoWindow");

    // set a id to the wrapper
    var popupWrapper = $(id).data("kendoWindow").wrapper;
    var popupWrapperId = id + "-wrapper";
    $(popupWrapper).attr("id", popupWrapperId.replace("#", ''));

    // bind the responsive functionality on open and on resize
    // It is necessary to pass the id of the wrapper because it holds the width of the window even before it is opened
    // popup.bind("open", function () {
    //     responsiveContentManagement(popupWrapperId);
    // });
    // popup.bind("resize", function () {
    //     responsiveContentManagement(popupWrapperId);
    // });

    //custom settings
    if (width !== "auto") {
        popup.setOptions({ width: width });
    }
    if (height !== "auto") {
        popup.setOptions({ height: height });
    }
    if (resizable !== undefined && resizable !== null && resizable !== "") {
        popup.setOptions({ resizable: resizable });
    }
    if (actions !== undefined && actions !== null && actions !== "") {
        popup.setOptions({ actions: actions });
    }
    else {
        popup.setOptions({ actions: ["Close"] }); //custom defaults
    }

    popup.close();

    return popup;

}

/// <summary>
/// Function to open a kendoWindow.
/// </summary>
/// <param name="id">Requires a string argument in format "#name_id".</param>
/// <param name="title">Requires a string argument containing the window title bar. 
///     If empty or null is set by default to false and the window will be displayed without a title bar.
/// </param>
/// <param name="maximize">Requires a bool argument.</param>
/// <param name="center">Requires a bool argument.</param>
function openPopup(id, title, maximize, center, modal) {

    var popup = $(id).data("kendoWindow");

    if (modal !== undefined && modal !== null && modal !== "") {
        popup.setOptions({ modal: modal });
    }

    popup.setOptions({ title: title });
    if (popup.element.is(":hidden")) {
        popup.open();
        if (center) {
            popup.center();
        }
        if (maximize) {
            popup.maximize();
        }
    }

}

/// <summary>
/// Function to close a kendoWindow.
/// </summary>
/// <param name="id">Requires a string argument in format "#name_id".</param>
function closePopup(id) {

    var popup = $(id).data("kendoWindow");
    popup.close();
    //popup.setOptions({ modal: false });

}

/// <summary>
/// Function to set a custom javascript function on a chart's event.
/// </summary>
/// <param name="id">Requires a string argument in format "#name_id".</param>
/// <param name="event">Requires a string argument containing the name of event. 
///     List of events managed: "open", "activate", "deactivate", "close", "refresh", "resize", "resizeEnd", "dragstart", "dragend", "error".
/// </param>
/// <param name="bindAction">Requires a javascript function.</param>
/// <returns>The function returns an event javascript object.</returns>
function setPopup(id, event, bindAction) {

    var popup = $(id).data("kendoWindow");
    popup.bind(event.toLowerCase(), bindAction);

}

/// <summary>
/// Function to set the width of a popup window and to center it.
/// </summary>
/// <param name="id">Requires a string argument in format "#name_id".</param>
/// <param name="maxWidth">Requires a number argument containing the maximum width. 
/// </param>
function resizeAndCenterPopup(id, maxWidth) {

    var popupWidth = Math.min(maxWidth, $(window).width() * 0.9);
    $(id).data("kendoWindow").setOptions({
        width: popupWidth
    });

    var wrapper = $(id).data("kendoWindow").wrapper;
    responsiveContentManagement("#" + $(wrapper).attr("id"));
    $(id).data("kendoWindow").center();

}

/// <summary>
/// Function to set the width of a popup window and to center it.
/// </summary>
/// <param name="id">Requires a string argument in format "#name_id".</param>
/// <param name="backgroundColor">Requires a hexColor for backGround header </param>
/// <param name="textColor">Requires a hexColor for text font header </param>
function setPopupHeaderColor(id, backgroundColor, textColor) {
    $(id).parent().find('.k-window-titlebar,.k-window-actions').css('backgroundColor', backgroundColor);
    $(id).parent().find('.k-window-titlebar,.k-window-actions').css('color', textColor);
}
/**********************************************/


/****************** SPLITTER ******************/
/// <summary>
/// Function to create a kendoSplitter.
/// </summary>
/// <param name="id">Requires a string argument in format "#name_id".</param>
/// <param name="orientation">Requires a string argument Specifies the orientation of the widget.
///     Supported values are "horizontal" and "vertical".
/// </param>
/// <param name="panes">Requires an object array argument of pane definitions.
///     The object configuration is:  { collapsible: true, size: "30%" } where:
///         - "collapsible" specifies whether a pane is initially collapsed (true) or expanded (true);
///         - "size" Specifies the size of a pane defined as pixels (i.e. "200px") or as a percentage (i.e. "50%").
///     To consult Telerik documentation if you need other panes configuration.
/// </param>
function createSplitter(id, orientation, panes) {

    $(id).kendoSplitter({
        orientation: orientation,
        panes: panes
    });

}

/// <summary>
/// Function to set a custom javascript function on a splitter's event.
/// </summary>
/// <param name="id">Requires a string argument in format "#name_id".</param>
/// <param name="event">Requires a string argument containing the name of event. 
///     List of events managed: "expand", "collapse", "contentLoad", "error", "resize", "layoutChange".
/// </param>
/// <param name="bindAction">Requires a javascript function.</param>
/// <returns>The function returns an event javascript object.</returns>
function setSplitter(id, event, bindAction) {

    var splitter = $(id).data("kendoSplitter");
    splitter.bind(event.toLowerCase(), bindAction);

}
/**********************************************/


/****************** SORTABLE ******************/
/// <summary>
/// Function to create a kendoSortable.
/// </summary>
/// <param name="id">Requires a string argument in format "#name_id" with one sortable list or in format "#name_id, #name_id" with 2 connected sortable lists.</param>
/// <param name="connectWith">Requires a string argument with the Selector's name which determines if items from the current Sortable widget can be accepted from another Sortable container(s).</param>
/// <param name="items">Requires a string argument with the Selector which determines the items.</param>
/// <param name="container">Requires a jQuery Selector argument that determines the container to which boundaries the hint movement will be constrained.</param>
/// <param name="hintConfig">Requires a javascript function provides a way for customization of the sortable item hint.
///     If a function is supplied, it receives one argument - the draggable element's jQuery object. 
///     If hint function is not provided the widget will clone dragged item and use it as a hint.
/// </param>
/// <param name="placeholderConfig">Requires a javascript function provides a way for customization of the sortable item placeholder. 
///     If a function is supplied, it receives one argument - the draggable element's jQuery object.
///     If placeholder function is not provided the widget will clone dragged item, remove its ID attribute, set its visibility to hidden and use it as a placeholder.
/// </param>
/// <param name="placeholder">Requires a string argument.</param>
function createSortable(id, connectWith, items, container, hintConfig, placeholderConfig) {

    $(id).kendoSortable({
        cursor: "move",
        connectWith: connectWith,
        items: items,
        container: (container === undefined || container === null) ? "" : container,
        hint: (hintConfig === undefined || hintConfig === null) ? "" : hintConfig,
        placeholder: (placeholderConfig === undefined || placeholderConfig === null) ? "" : placeholderConfig
    });

}

/// <summary>
/// Function to set a custom javascript function on a sortable's event.
/// </summary>
/// <param name="id">Requires a string argument in format "#name_id" with one sortable list or in format "#name_id, #name_id" with 2 connected sortable lists.</param>
/// <param name="event">Requires a string argument containing the name of event. 
///     List of events managed: "start", "beforeMove", "move", "end", "change", "cancel".
/// </param>
/// <param name="bindAction">Requires a javascript function.</param>
/// <returns>The function returns an event javascript object.</returns>
function setSortable(id, event, bindAction) {

    var sortable = $(id).data("kendoSortable");
    sortable.bind(event.toLowerCase(), bindAction);

}
/**********************************************/


/**************** NOTIFICATION ****************/
/// <summary>
/// Function to create a kendoNotification.
/// </summary>
/// <param name="id">Requires a string argument in format "#name_id".</param>
/// <param name="autoHide">Requires a int argument indicates the period in milliseconds after which a notification disappears automatically.
///     Setting a 0 value disables this behavior.
/// </param>
/// <param name="hideOnClick">Requires a bool argument determines whether notifications can be hidden by clicking anywhere on their content.</param>
function createNotification(id, autoHideAfter, hideOnClick) {

    //custom template
    var infoTemplate = '<div class="notification-style" style="text-align: center;">'
                        + '<h4>#= title #</h4>'
                        + '<p>#= message #</p>'
                    + '</div>';
    var warningTemplate = '<div class="notification-style" style="text-align: center;">'
                        + '<h4>#= title #</h4>'
                        + '<p>#= message #</p>'
                    + '</div>';
    var errorTemplate = '<div class="notification-style" style="text-align: center;">'
                        + '<h4>#= title #</h4>'
                        + '<p>#= message #</p>'
                    + '</div class="notification-style">';
    var successTemplate = '<div style="text-align: center;">'
                        + '<h4>#= title #</h4>'
                        + '<p>#= message #</p>'
                    + '</div>';

    $(id).kendoNotification({
        position: { //position set 
            pinned: true,
            top: null,
            left: null,
            bottom: 20,
            right: 20
        },
        stacking: "up",
        templates: [{
            type: "info",
            template: infoTemplate
        }, {
            type: "warning",
            template: warningTemplate
        }, {
            type: "error",
            template: errorTemplate
        }, {
            type: "success",
            template: successTemplate
        }],
        width: 350,
        show: function (e) {
            e.element.parent().css({
                zIndex: 22222
            });
        },
        autoHideAfter: (autoHideAfter === null || autoHideAfter === undefined || autoHideAfter === "") ? 5000 : autoHideAfter,
        hideOnClick: (hideOnClick === null || hideOnClick === undefined || hideOnClick === "") ? false : hideOnClick
    });

}

/// <summary>
/// Function to show a kendoNotification.
/// </summary>
/// <param name="id">Requires a string argument in format "#name_id".</param>
/// <param name="type">Requires a string argument describes the HTML markup of the different notification types as Kendo UI template strings.
///     The built-in types are "info", "success", "warning" and "error".
/// </param>
/// <param name="title">Requires a string argument.</param>
/// <param name="message">Requires a string argument.</param>
function showNotification(id, type, title, message) {

    $(id).data("kendoNotification").show({
        title: title,
        message: message
    }, type);

}
/**********************************************/


/*********** PIVOTGRID [not used] *************/
/// <summary>
/// Function to create a kendoPivotGrid.
/// </summary>
/// <param name="id">Requires a string argument in format "#name_id".</param>
/// <param name="height">Requires a int argument are treated as pixels.</param>
/// <param name="reorderable">Requires a bool argument and if set to false the user will not be able to add/close/reorder current fields for columns/rows/measures.</param>
/// <param name="dataCellTemplate">Requires a function containing the template which renders the content of the data cell.
///     To consult Telerik documentation to see the fields which can be used in the template.
/// <param name="columnHeaderTemplate">Requires a function containing the template which renders the content of the column header cell.
///     To consult Telerik documentation to see the fields which can be used in the template.
/// <param name="rowHeaderTemplate">Requires a function containing the template which renders the content of the row header cell.
///     To consult Telerik documentation to see the fields which can be used in the template.
function createPivotGrid(id, height, reorderable, dataCellTemplate, columnHeaderTemplate, rowHeaderTemplate) {

    $(id).kendoPivotGrid({
        height: height,
        reorderable: reorderable,
        columnHeaderTemplate: (columnHeaderTemplate === null || columnHeaderTemplate === "" || columnHeaderTemplate === undefined) ? null : columnHeaderTemplate,
        rowHeaderTemplate: (rowHeaderTemplate === null || rowHeaderTemplate === "" || columnHeaderTemplate === undefined) ? null : rowHeaderTemplate,
        dataCellTemplate: (dataCellTemplate === null || dataCellTemplate === "" || columnHeaderTemplate === undefined) ? null : dataCellTemplate
    });

}

/// <summary>
/// Function to set a custom javascript function on a kendoPivotGrid's event.
/// </summary>
/// <param name="id">Requires a string argument in format "#name_id".</param>
/// <param name="event">Requires a string argument containing the name of event. 
///     List of events managed: "dataBinding", "dataBound", "expandMember", "collapseMember", "excelExport", "pdfExport".
/// </param>
/// <param name="bindAction">Requires a javascript function.</param>
/// <returns>The function returns an event javascript object.</returns>
function setPivotGrid(id, event, bindAction) {

    var pivotGrid = $(id).data("kendoPivotGrid");
    pivotGrid.bind(event, bindAction);

}

/// <summary>
/// Function to set the data source of the kendoPivotGrid.
/// </summary>
/// <param name="id">Requires a string argument in format "#name_id".</param>
/// <param name="kendoPivotDataSource">Requires a kendo.data.PivotDataSource.</param>
function populatePivotGrid(id, kendoPivotDataSource) {

    var pivotGrid = $(id).data("kendoPivotGrid");
    pivotGrid.setDataSource(kendoPivotDataSource);

}

/// <summary>
/// Function to hide PivotGrid column totals.
/// Usually it must be to set on dataBound event.
/// </summary>
/// <param name="id">Requires a string argument in format "#name_id".</param>
/// <param name="e">Requires a javascript event object.</param>
function hidePivotGridColumnTotals(id, e) {

    try {

        var firstDimensionPath = JSON.parse($(e.sender.columnsHeader).find("tr:first-child th > span").attr("data-path"));

        //management second dimension column totals when the first dimension are expanding
        if ($(e.sender.columnsHeader).find("tr:first-child th:first-child span").hasClass("k-i-arrow-s")
            && $(e.sender.columnsHeader).find("tr:nth-child(3) th:nth-last-child(2) span[data-path*='" + firstDimensionPath[0] + "']").hasClass("k-i-arrow-s")) {

            $(e.sender.columnsHeader).find("tr:nth-child(3) th:nth-last-child(2) > span").trigger("click");//close second dimension
            return;
        }

        //management second dimension column totals when the first dimension are collapsing
        if ($(e.sender.columnsHeader).find("tr:first-child th > span").hasClass("k-i-arrow-e")
            && $(e.sender.columnsHeader).find("tr:nth-child(2) th > span[data-path*='" + firstDimensionPath[0] + "']").hasClass("k-i-arrow-e")) {

            $(e.sender.columnsHeader).find("tr:nth-child(2) th > span").trigger("click");//open second dimension
            return;
        }

        //hide column totals
        $(e.sender.columnsHeader).find("tr:nth-child(3) > th").removeClass("mpm-hide");
        $.each($(e.sender.columnsHeader).find("tr"), function () {
            if ($(this).children().length > 1) {
                var dataPath = $(this).children().children().attr("data-path");
                if (dataPath !== undefined) {
                    $(this).children().last().addClass("mpm-hide");
                }
            }
        });
        $(e.sender.columnsHeader).find("tr > .k-alt").addClass("mpm-hide");
        $(e.sender.content).find("tr > .k-alt").addClass("mpm-hide");

        //show row totals
        $(e.sender.content).find(".k-grid-footer").removeClass("mpm-hide");
        $(id + " .k-widget .k-grid-footer").removeClass("mpm-hide");

        //set colgroups whithout column totals to render correct the pivotgrid
        var correctColspan = "";
        if ($(e.sender.columnsHeader).find("tr:first-child th > span").hasClass("k-i-arrow-e")
            && $(e.sender.columnsHeader).find("tr:nth-child(2) th > span[data-path*='" + firstDimensionPath[0] + "']").hasClass("k-i-arrow-s")) {

            $.each($(e.sender.columnsHeader).find("tr:nth-child(2) [colspan]"), function () {
                if (!$(this).hasClass("mpm-hide")) {
                    for (var i = 0; i < parseInt($(this).attr("colspan")) ; i++) {
                        correctColspan += "<col>";
                    }
                }
            });
        }
        if ($(e.sender.columnsHeader).find("tr:first-child th:first-child span").hasClass("k-i-arrow-s")
            && $(e.sender.columnsHeader).find("tr:nth-child(3) th:last-child span[data-path*='" + firstDimensionPath[0] + "']").hasClass("k-i-arrow-e")) {

            $.each($(e.sender.columnsHeader).find("tr:nth-child(3) [colspan]"), function (tag_position) {
                if (!$(this).hasClass("mpm-hide")) {
                    for (var i = 0; i < parseInt($(this).attr("colspan")) ; i++) {
                        correctColspan += "<col>";
                    }
                    $(e.sender.columnsHeader).find("tr:nth-child(2) th:nth-child(" + (tag_position + 1) + ")").attr("colspan", $(this).attr("colspan"));
                }
            });
        }
        $(e.sender.columnsHeaderTree.root).find("table colgroup").html(correctColspan);
        $(e.sender.contentTree.root).find("table colgroup").html(correctColspan);

        //custom fix: wait 60 milliseconds the end of kendo render to set the correct colspan dimension
        setTimeout(function () {
            $(e.sender.columnsHeader).find("tr:first-child th:first-child").attr("colspan", $(e.sender.columnsHeader).find("table colgroup col").length);
        }, 60);

    }
    catch (err) {
        console.log("Exception in kendo.helper.js - " + arguments.callee.name + ": [" + err.name + "] " + err.message)
    }

}

/// <summary>
/// Function to hide PivotGrid row totals.
/// Usually it must be to set on dataBound event.
/// </summary>
/// <param name="id">Requires a string argument in format "#name_id".</param>
/// <param name="e">Requires a javascript event object.</param>
function hidePivotGridRowTotals(id, e) {

    try {

        //open second dimension with row totals when close first dimension
        if ($(e.sender.rowsHeader).find(".k-first span").hasClass("k-i-arrow-e")
            && $(e.sender.rowsHeader).find("td:nth-child(2) span").hasClass("k-i-arrow-e")) {

            $(e.sender.rowsHeader).find("td:nth-child(2) span").trigger("click");
        }

        //close second dimension with row totals when open first dimension
        if ($(e.sender.rowsHeader).find(".k-first span").hasClass("k-i-arrow-s")
            && $(e.sender.rowsHeader).find("td.k-grid-footer.k-first").parent().find("span").hasClass("k-i-arrow-s")) {

            $(e.sender.rowsHeader).find("td.k-grid-footer.k-first").parent().find("span.k-i-arrow-s").trigger("click");
        }

        //show column totals
        $(e.sender.columnsHeader).find("tr > .k-alt").removeClass("mpm-hide");
        $(e.sender.columnsHeader).find("th:last-child > span").parent().removeClass("mpm-hide");
        $(e.sender.content).find("tr > .k-alt").removeClass("mpm-hide");

        //hide row totals
        $(e.sender.content).find(".k-grid-footer").addClass("mpm-hide");
        $(id + " .k-widget .k-grid-footer").addClass("mpm-hide");

    }
    catch (err) {
        console.log("Exception in kendo.helper.js - " + arguments.callee.name + ": [" + err.name + "] " + err.message)
    }

}

/// <summary>
/// Function to expand a column/row tuple member that has children of KendoPivotGrid.
/// </summary>
/// <param name="id">Requires a string argument in format "#name_id".</param>
/// <param name="fieldToExpand">Requires a string argument containing the field's name to expand.</param>
/// <param name="axisToExpand">Requires a string argument containing the axis ("row" or "column").</param>
function expandPivotGridFields(id, fieldToExpand, axisToExpand) {

    try {
        //get field's path list to expan
        var paths = new Array();
        var fieldList = $(id).find('.k-i-arrow-e[data-path*="' + fieldToExpand + '"]');
        $.each(fieldList, function () {
            if (!$(this).parent().hasClass("mpm-hide")) {
                paths.push(JSON.parse($(this).attr("data-path")));
            }
        });

        //expand fiels on axes
        var pivot = $(id).data("kendoPivotGrid");
        var pivotDataSource = $(id).data("kendoPivotGrid").dataSource;
        $.each(paths, function (i, dataPath) {
            switch (axisToExpand) {
                case "row":
                    pivotDataSource.expandRow(dataPath);
                    break;
                case "column":
                    pivotDataSource.expandColumn(dataPath);
                    break;
                default:
                    console.log("kendo.helper.js : Axes '" + axisToExpand + "' not recognized in expandPivotGridFields().");
            }
        });

        //!BUG!
        //custom fix: open and close a second dimension field to render correct the pivot grid 
        if (fieldList !== undefined) {
            $(id).find('.k-i-arrow-s[data-path*="' + paths[paths.length - 2][0] + '"]').trigger("click");
            setTimeout(function () {
                $(id).find('.k-i-arrow-e[data-path*="' + paths[paths.length - 2][0] + '"]').trigger("click");
            }, 60);
        }
    }
    catch (err) {
        console.log("Exception in kendo.helper.js - " + arguments.callee.name + ": [" + err.name + "] " + err.message)
    }

}

/// <summary>
/// Function to hide expand/collapse icon of a PivotGrid's field.
/// </summary>
/// <param name="id">Requires a string argument in format "#name_id".</param>
/// <param name="id">Requires a string argument containing the field's name to hide icon.</param>
function disablePivotGridFields(id, fieldToDisable) {

    $(id).find('.k-icon[data-path*="' + fieldToDisable + '"]').hide();

}
/**********************************************/


/******************** GRID ********************/
/// <summary>
/// Function to set auto resize to kendoGrid and kendoGrid.
/// </summary>
/// <param name="id">Requires a string argument in format "#name_id".</param>
/// <param name="objectIdList"> Requires a list of objects in the same container of grid to resize.</param>
/// <param name="offset">Requires an int argument to set an offset.[deprecated]</param>
function resizeKendoGrid(id, objectIdList, offset) {

    var heightAllObj = 0;
    var currentOffset = (offset !== undefined) ? offset : 0;
    if (objectIdList !== undefined && objectIdList.length > 0) {
        $.each(objectIdList, function (i, objId) {
            heightAllObj += $(objId).outerHeight(true);
        });
    }

    var gridElement = $(id);
    var dataArea = gridElement.find(".k-grid-content");
    var newHeight = Math.floor(gridElement.parent().height() - heightAllObj - currentOffset);
    var diff = Math.ceil(gridElement.height() - dataArea.outerHeight(true));

    var gridElementMargin = gridElement.outerHeight(true) - gridElement.outerHeight();
    gridElement.outerHeight(Math.floor(newHeight - gridElementMargin));

    var dataAreaMargin = dataArea.outerHeight(true) - dataArea.outerHeight();
    dataArea.outerHeight(Math.floor(gridElement.height() - Math.abs(diff + dataAreaMargin)));

    if (gridElement.data("kendoGrid") !== undefined) {
        gridElement.data("kendoGrid").resize(true);
    }

}

/// <summary>
/// Function to set a custom javascript function on a kendoGrid's event.
/// </summary>
/// <param name="id">Requires a string argument in format "#name_id".</param>
/// <param name="event">Requires a string argument containing the name of event. 
///     List of events managed in http://docs.telerik.com/kendo-ui/api/javascript/ui/grid#events.
/// </param>
/// <param name="bindAction">Requires a javascript function.</param>
/// <returns>The function returns an event javascript object.</returns>
function setGrid(id, event, bindAction) {

    var grid = $(id).data("kendoGrid");
    switch (event) {
        case "changeSelectRow":
            if (bindAction === null) {
                $(id).off("dblclick", "tr.k-state-selected");
            }
            else {
                $(id).on("dblclick", "tr.k-state-selected", bindAction);
            }
            break;
        case "dblClickOnCell":
            if (bindAction === null) {
                $(id).off("dblclick", ".cell");
            }
            else {
                $(id).on("dblclick", ".cell", bindAction);
            }
            break;
        default:
            if (bindAction === null) {
                grid.unbind(event);
            }
            grid.bind(event, bindAction);
            grid.dataSource.fetch();
            break;
    }

}

/// <summary>
/// /this methods save grid configuration (excluding data )in local storage with given key
/// </summary>
/// <param name="gridId">Requires a string argument in format "#name_id".</param>
/// <param name="gridConfigurationName">Requires a string argument containing the name of the configuration
///   NB: namingConvention = nameGrid_for_nameReport in this way same partial view in different report can be associated to different configuration
/// </param>
/// <param name="configuration">Requires a grid configuration. If undefined confifguartion is taken directly from the gid</param>
function saveGridConfiguration(gridId, gridConfigurationName, configuration) {

    var gridCurrentOpt;
    var grid = $(gridId).data("kendoGrid");
    if (grid) {

        if (configuration) {
            gridCurrentOpt = configuration;
        }
        else {
            // no configuration provided, taken directly from grid
            gridCurrentOpt = grid.getOptions();
        }

        // remove data so that they are not sotored in the local storage
        gridCurrentOpt.dataSource.data = new Array();

        localStorage[gridConfigurationName] = kendo.stringify(gridCurrentOpt);

        ($("#notification").length > 0) ? showNotification("#notification", "success", textJSLayout["SaveSuccess"], textJSLayout["GridConfigurationSaved"]) : console.log(textJSLayout["GridConfigurationSaved"]);

    }
}

/// <summary>
/// /this methods apply the localStorage configuration with the given name to the given gird.
/// it returns current dataSource filter and grouping settings so that they can be assigned to the dataSource when grid is populated with data
/// </summary>
/// <param name="gridId">Requires a string argument in format "#name_id".</param>
/// <param name="gridConfigurationName">Requires a string argument containing the name of the configuration
///   NB: namingConvention = nameGrid_for_nameReport in this way same partial view in different report can be associated to different configuration
/// </param>
/// <returns>object with filters and group configuration which have to be applied to new dataSource</returns>
function applyGridConfiguration(gridId, gridConfigurationName) {

    var config = { "ConfigurationFilters": new Array(), "ConfigurationGroup": new Array() }

    var grid = $(gridId).data("kendoGrid");
    if (grid) {
        // get options from local sotrage
        var options = localStorage[gridConfigurationName];

        if (options) {
            // getCurrent filter and group
            var currentOpt = JSON.parse(options);
            var currentDsFilters = (currentOpt.dataSource.filter == undefined) ? [] : currentOpt.dataSource.filter;
            var currentDsGroup = (currentOpt.dataSource.group == undefined) ? [] : currentOpt.dataSource.group;

            grid.setOptions(currentOpt);
            /// return  current filters so that tey can be set to the new dataSource
            config.ConfigurationFilters = currentDsFilters;
            config.ConfigurationGroup = currentDsGroup;


        }
    }
    return config;
}

/// <summary>
///this methods apply the given configuration. To use when a configuration not saved in local storage has to be applied- example can be found in report GridStatusList
/// it returns current dataSource filter and grouping settings so that they can be assigned to the dataSource when grid is populated with data
/// </summary>
/// <param name="gridId">Requires a string argument in format "#name_id".</param>
/// <param name="configurationObj">Requires a configurationObjet obtained with kendo method  grid.getOptions()
/// <returns>object with filters and group configuration which have to be applied to new dataSource</returns>
function applyCustomGridConfiguration(gridId, configurationObj) {

    var grid = $(gridId).data("kendoGrid");
    if (grid) {

        var currentDsFilters = configurationObj.dataSource.filter;
        var currentDsGroup = configurationObj.dataSource.group;

        grid.setOptions(configurationObj);

        // return  current filters so that tey can be set to the new dataSource
        return { "ConfigurationFilters": currentDsFilters, "ConfigurationGroup": currentDsGroup }
    }
}

/// <summary>
/// /this methods delete from local storage the configuration with the given name --it returns current dataSource filter and grouping settings so that they can be assigned to the dataSource when grid is populated with data
/// </summary>
/// <param name="gridConfigurationName">Requires a string</param>
function resetGridConfiguration(gridConfigurationName) {
    localStorage.removeItem(gridConfigurationName);

    ($("#notification").length > 0) ? showNotification("#notification", "success", textJSLayout["SaveSuccess"], textJSLayout["GridConfigurationReset"]) : console.log(textJSLayout["GridConfigurationReset"]);

}
/**********************************************/


/**************** PROGRESS BAR ****************/
/// <summary>
/// Function to create a kendo's progress bar.
/// </summary>
/// <param name="id">Requires a string argument in format "#name_id".</param>
/// <param name="type"> Requires a string argument specifies the type of the ProgressBar.
///     The supported types are "value", "percent" and "chunk".
/// </param>
function createProgressBar(id, type, animation) {

    $(id).kendoProgressBar({
        type: (type === undefined || type === "" || type === null) ? "value" : type,
        animation: {
            duration: (animation === true) ? 500 : 0
        }
    });

}

/// <summary>
/// Function to set a custom javascript function on a progressBar's event.
/// </summary>
/// <param name="id">Requires a string argument in format "#name_id".</param>
/// <param name="event">Requires a string argument containing the name of event. 
///     List of events managed: "change" and "complete".
/// </param>
/// <param name="bindAction">Requires a javascript function.</param>
/// <returns>The function returns an event javascript object.</returns>
function setProgressBar(id, event, bindAction) {

    var progressbar = $(id).data("kendoProgressBar");
    progressbar.bind(event, bindAction);

}

/// <summary>
/// Function to create a kendo's progress bar.
/// </summary>
/// <param name="id">Requires a string argument in format "#name_id".</param>
/// <return> Return an int number, the value of progressBar.</return>
function getProgressBarValue(id) {

    return $(id).data("kendoProgressBar").value();

}

/// <summary>
/// Function to create a kendo's progress bar.
/// </summary>
/// <param name="id">Requires a string argument in format "#name_id".</param>
/// <param name="value"> Requires an int argument to be set.</param>
function setProgressBarValue(id, value) {

    $(id).data("kendoProgressBar").value(value);

}
/**********************************************/

/**************** TREELIST ****************/
/// <summary>
/// Function to set auto resize to kendoGrid and kendoTreeList.
/// </summary>
/// <param name="id">Requires a string argument in format "#name_id".</param>
/// <param name="objectIdList"> Requires a list of objects in the same container of treeList to resize.</param>
function resizeKendoTreeList(id, objectIdList) {

    var heightAllObj = 0;
    if (objectIdList !== undefined && objectIdList.length > 0) {
        $.each(objectIdList, function (i, objId) {
            heightAllObj += $(objId).outerHeight(true);
        });
    }

    var treeListElement = $(id);
    var dataArea = treeListElement.find(".k-grid-content");
    var newHeight = Math.floor(treeListElement.parent().height() - heightAllObj);
    var diff = Math.ceil(treeListElement.height() - dataArea.outerHeight(true));

    var treeListElementMargin = treeListElement.outerHeight(true) - treeListElement.outerHeight();
    treeListElement.outerHeight(Math.floor(newHeight - treeListElementMargin));

    var dataAreaMargin = dataArea.outerHeight(true) - dataArea.outerHeight();
    dataArea.outerHeight(Math.floor(treeListElement.height() - Math.abs(diff + dataAreaMargin)));

    if (treeListElement.data("kendoTreeList") !== undefined) {
        treeListElement.data("kendoTreeList").resize(true);
    }

}

/// <summary>
/// Function to set a custom javascript function on a kendoTreeList's event.
/// </summary>
/// <param name="id">Requires a string argument in format "#name_id".</param>
/// <param name="event">Requires a string argument containing the name of event. 
///     List of events managed in http://docs.telerik.com/kendo-ui/api/javascript/ui/grid#events.
/// </param>
/// <param name="bindAction">Requires a javascript function.</param>
/// <returns>The function returns an event javascript object.</returns>
function setTreeList(id, event, bindAction) {

    var treeList = $(id).data("kendoTreeList");
    treeList.bind(event, bindAction);
}
/******************************************/

///TODO... kendoDataSource
function getFieldValueToFilter(list, field) {

    var itemsSelected = new Array();
    var SelectAllId = -2;

    $.each(list, function (i, id) {
        if (id !== SelectAllId) {
            itemsSelected.push({ "field": field, "operator": "eq", "value": id });
        }
    });

    return itemsSelected;

}

function getFieldValueToFilterAway(list, field) {

    var itemsSelected = new Array();
    $.each(list, function (i, id) {
        itemsSelected.push({ "field": field, "operator": "neq", "value": id });
    });
    return itemsSelected;

}


//Private Functions
function _createFilterComboBox(id, valueField, textField) {

    $(id).kendoComboBox({
        dataValueField: valueField,
        dataTextField: textField,
        filter: "contains",
        suggest: true,
        enable: false
    });

}
function _createFilterDropDownList(id, valueField, textField, emptyField) {

    //to fix IE problem
    var optionLabelEmpty = JSON.parse('{ "' + valueField + '" : "empty", "' + textField + '" : "Select..." }');

    $(id).kendoDropDownList({
        dataValueField: valueField,
        dataTextField: textField,
        index: 0,
        enable: false
    });
    if (emptyField) {
        $(id).data("kendoDropDownList").setOptions({
            optionLabel: optionLabelEmpty
        })
    }

}
function _createFilterMultiSelect(id, valueField, textField, selectAllEnabled) {

    $(id).kendoMultiSelect({
        dataValueField: valueField,
        dataTextField: textField,
        tagMode: "single",
        tagTemplate: kendo.template("# if (values.length < maxTotal) {# #:values.length# selected #} else {# All selected #} #"),
        autoClose: false,
        enable: false
    });
    if (selectAllEnabled) {
        var selectAllItem = JSON.parse('{ "' + valueField + '" : ' + selectAllId + ', "' + textField + '" : "All" }');
        $(id).data("kendoMultiSelect").setDataSource([selectAllItem]);
        $(id).data("kendoMultiSelect").bind("select", _selectAllManagement);
    }
    $(id).data("kendoMultiSelect").input.hide(); //for hide the cursor

    //Private functionality
    function _selectAllManagement(e) {

        //start private functionality _selectAllManagement
        var currentMultiSelect = e.sender;
        var itemPressed = e.item;
        var isSelectAllSelected = (currentMultiSelect.element.find('[value="-2"]').attr("selected") === "selected");
        var itemIdList = $.map(currentMultiSelect.element.find("option"), function (item) {
            //get all Id except selectAllId
            if ($(item).attr("value") !== selectAllId.toString()) {
                return $(item).attr("value");
            }
        });

        switch (itemPressed.index()) {
            case 0: //SelectAll pressed 
                if (!isSelectAllSelected) { //Select All Items
                    currentMultiSelect.value(itemIdList);
                }
                else {//Deselect All Items
                    currentMultiSelect.value([selectAllId]);
                }
                break;
            default: //Other multiselect's item pressed
                var itemSelectedListLength = currentMultiSelect.element.find('[selected="selected"]').length;
                var wasItemSelected = itemPressed.hasClass("k-state-selected");

                if (itemSelectedListLength === (itemIdList.length - 1)
                    && wasItemSelected === false
                    && isSelectAllSelected === false) {
                    var itemsSelected = currentMultiSelect.value();
                    itemsSelected.push(selectAllId); //add SelectAll selection
                    currentMultiSelect.value(itemsSelected);
                }
                if (itemSelectedListLength > itemIdList.length
                    && wasItemSelected === true
                    && isSelectAllSelected === true) {
                    currentMultiSelect.value(itemIdList);
                }
                break;
        }
        //end private functionality _selectAllManagement
    }

}
function _createFilterDatePicker(id, disable) {

    $(id).kendoDatePicker();
    if (disable) {
        $(id).data("kendoDatePicker").enable(false);
    } else {
        $(id).data("kendoDatePicker").enable(true);
    }

}
function _createFilterMonthPicker(id, disable) {

    $(id).kendoDatePicker({
        start: "year", // defines the start view
        depth: "year", // defines when the calendar should return date
        format: "MMMM yyyy" // display month and year in the input        
    });
    if (disable) {
        $(id).data("kendoDatePicker").enable(false);
    } else {
        $(id).data("kendoDatePicker").enable(true);
    }

}
function _createFilterDateTimePicker(id, disable) {

    $(id).kendoDateTimePicker();
    if (disable) {
        $(id).data("kendoDateTimePicker").enable(false);
    } else {
        $(id).data("kendoDateTimePicker").enable(true);
    }

}
function _createFilterTimePicker(id, disable) {

    $(id).kendoTimePicker({
        format: "HH:mm",
        parseFormats: ["HH:mm", "HH"]
    });

    // prevent invalid values (on change doesn't work: on focus loss, the change event is triggerd, 
    // but the value returned by widget.value() is not updated 
    $(id).on("blur", function (e) {

        var input = $(this);
        var widget = input.data("kendoTimePicker");

        if (widget && widget.value() === null && input.val()) {
            widget.value("");
        }

    });

    if (disable) {
        $(id).data("kendoTimePicker").enable(false);
    } else {
        $(id).data("kendoTimePicker").enable(true);
    }

}
