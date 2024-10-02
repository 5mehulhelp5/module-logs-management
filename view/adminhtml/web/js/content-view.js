define([
    'jquery',
    'mage/translate'
], function ($) {
    'use strict';

    return function (config) {
        function handleClick(item) {
            $.ajax({
                url: config.baseUrl,
                type: 'GET',
                dataType: 'json',
                data: {
                    path: $(item).data('item-path')
                },
                showLoader: true,
                success: function (response) {
                    const logWrapper = $('#log-content');
                    logWrapper.empty().append(response);
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    console.error($.mage.__('Error while loading: '), textStatus, errorThrown);
                }
            });
        }

        // Function to check if there are any file nodes in the category tree
        function checkTreeLoading() {
            const fileNodes = $('[data-item-type="file"]');
            if (fileNodes.length > 0) {
                observer.disconnect();
                fileNodes.each(function (index, item) {
                    $(item).on('click', function () {
                        handleClick(item);
                    });
                });
            }
        }

        // Initialize MutationObserver to watch for changes in the directory tree
        const observer = new MutationObserver(checkTreeLoading);
        observer.observe(document.querySelector('#directory-tree'), { childList: true, subtree: true });

        // Initial check for file nodes
        checkTreeLoading();
    };
});
