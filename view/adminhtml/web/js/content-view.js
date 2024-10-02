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
                    const note = $('div.note');
                    const content = $('#log-content');

                    if (response.error) {
                        content.empty().css('display', 'none');
                        note.css('display', 'flex').children('span').empty().append(response.message);
                    } else {
                        note.css('display', 'none').children('span').empty();
                        content.empty().append(response.content).css('display', 'block');
                    }
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
