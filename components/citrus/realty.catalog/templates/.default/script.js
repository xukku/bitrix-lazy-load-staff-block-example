var readyFunc = function() {

    var $pageHeaders = $('h1'),
        $firstH1 = $($pageHeaders[0]);

    if ($pageHeaders.length > 1) { // detail page loaded by component in ajax-mode: fix duplicate page header
        $firstH1.parents('header')
            .css({display: 'none'})
            .parents('section')
            .removeClass('section section--page-wrapper _with-padding _compact');
    } else {
        $firstH1.parents('header')
            .css({display: ''})
            .parents('section')
            .addClass('section section--page-wrapper _with-padding _compact');
    }
};

$(readyFunc);
BX.addCustomEvent('onAjaxSuccess', readyFunc);
BX.addCustomEvent('onComponentAjaxHistorySetState', readyFunc);
