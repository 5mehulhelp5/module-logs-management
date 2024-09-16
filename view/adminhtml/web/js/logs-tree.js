define([
    'jquery',
    'jstree'
], function ($) {
    'use strict';

    return function (config, element) {
        $(element).jstree({
            'core': {
                'data': config.treeData
            }
        });
    };
});
