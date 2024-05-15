$(document).ready(function () {

    $(document).on("click", ".MultiCheckBox", function () {
        var detail = $(this).next();
        detail.show();
    });

    CreateMultiCheckBox("#test", { width: '100%', defaultText: 'Select statuses', height: 'auto' });

    $(document).on("click", ".MultiCheckBoxDetailHeader input", function (e) {
        e.stopPropagation();
        var hc = $(this).prop("checked");
        $(this).closest(".MultiCheckBoxDetail").find(".MultiCheckBoxDetailBody input").prop("checked", hc);
        UpdateSelect($(this).closest(".MultiCheckBoxDetail").next());
    });

    $(document).on("click", ".MultiCheckBoxDetailHeader", function (e) {
        var inp = $(this).find("input");
        var chk = inp.prop("checked");
        inp.prop("checked", !chk);
        $(this).closest(".MultiCheckBoxDetail").find(".MultiCheckBoxDetailBody input").prop("checked", !chk);
        UpdateSelect($(this).closest(".MultiCheckBoxDetail").next());
    });

    $(document).on("click", ".MultiCheckBoxDetail .cont input", function (e) {
        e.stopPropagation();
        UpdateSelect($(this).closest(".MultiCheckBoxDetail").next());
        var val = $(".MultiCheckBoxDetailBody input:checked").length == $(".MultiCheckBoxDetailBody input").length;
        $(".MultiCheckBoxDetailHeader input").prop("checked", val);
    });

    $(document).on("click", ".MultiCheckBoxDetail .cont", function (e) {
        var inp = $(this).find("input");
        var chk = inp.prop("checked");
        inp.prop("checked", !chk);
        var multiCheckBoxDetail = $(this).closest(".MultiCheckBoxDetail");
        var multiCheckBoxDetailBody = $(this).closest(".MultiCheckBoxDetailBody");
        UpdateSelect(multiCheckBoxDetail.next());
        var val = $(".MultiCheckBoxDetailBody input:checked").length == $(".MultiCheckBoxDetailBody input").length;
        $(".MultiCheckBoxDetailHeader input").prop("checked", val);
    });

    $(document).mouseup(function (e) {
        var container = $(".MultiCheckBoxDetail");
        if (!container.is(e.target) && container.has(e.target).length === 0) {
            container.hide();
        }
    });

});

var defaultMultiCheckBoxOption = { width: '220px', defaultText: 'Select Below', height: '200px' };

function CreateMultiCheckBox(selector, options) {
    var localOption = {};
    localOption.width = (options != null && options.width != null && options.width != undefined) ? options.width : defaultMultiCheckBoxOption.width;
    localOption.defaultText = (options != null && options.defaultText != null && options.defaultText != undefined) ? options.defaultText : defaultMultiCheckBoxOption.defaultText;
    localOption.height = (options != null && options.height != null && options.height != undefined) ? options.height : defaultMultiCheckBoxOption.height;

    $(selector).hide().attr("multiple", "multiple");
    var divSel = $("<div class='MultiCheckBox'>" + localOption.defaultText + "<span class='k-icon k-i-arrow-60-down'><svg aria-hidden='true' focusable='false' data-prefix='fas' data-icon='sort-down' role='img' xmlns='http://www.w3.org/2000/svg' viewBox='0 0 320 512' class='svg-inline--fa fa-sort-down fa-w-10 fa-2x'><path fill='currentColor' d='M41 288h238c21.4 0 32.1 25.9 17 41L177 448c-9.4 9.4-24.6 9.4-33.9 0L24 329c-15.1-15.1-4.4-41 17-41z' class=''></path></svg></span></div>").insertBefore(selector);
    divSel.css({ "width": localOption.width });

    var detail = $("<div class='MultiCheckBoxDetail'><div class='MultiCheckBoxDetailHeader'><input type='checkbox' name='statuses[]' class='mulinput statuses' value='-1982' /><div>Select All</div></div><div class='MultiCheckBoxDetailBody'></div></div>").insertAfter(divSel);
    detail.css({ "width": parseInt(options.width) + 10, "max-height": localOption.height });
    var multiCheckBoxDetailBody = detail.find(".MultiCheckBoxDetailBody");

    $(selector).find("option").each(function () {
        var val = $(this).attr("value") || '';
        multiCheckBoxDetailBody.append("<div class='cont'><div><input type='checkbox' name='statuses[]' class='mulinput statuses' value='" + val + "' /></div><div>" + $(this).text() + "</div></div>");
    });

    multiCheckBoxDetailBody.css("max-height", (parseInt($(".MultiCheckBoxDetail").css("max-height")) - 28) + "px");
    multiCheckBoxDetailBody.css("background","white");
}

function UpdateSelect($element) {
    var arr = [];
    $element.prev().find(".mulinput:checked").each(function () {
        arr.push($(this).val());
    });
    $element.val(arr);
}
