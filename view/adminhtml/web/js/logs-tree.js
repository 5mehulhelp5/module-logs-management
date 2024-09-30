define([
    'jquery',
    'jstree'
], function ($) {
    'use strict';

    // disable default closing node behavior
    $.jstree.plugins.noclose = function () {
        this.close_node = $.noop;
    }

    return function (config, element) {
        $(element).jstree({
            'core': {
                'data': config.treeData
            },
            'plugins': ['noclose']
        });
    };
});
