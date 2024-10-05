define([
    'jquery',
    'mage/translate'
], function ($) {
    'use strict';

    return function (config) {

        /**
         * Function to handle error state
         *
         * @param {object} content
         * @param {object} note
         * @param {string} message
         */
        function handleErrorState(content, note, message)
        {
            console.log(typeof content);
            content.empty().hide();
            note.show().children('span').empty().append(message);
        }

        /**
         * Function to handle success state
         *
         * @param {object} content
         * @param {object} note
         * @param {string} data
         */
        function handleSuccessState(content, note, data)
        {
            note.hide().children('span').empty();
            content.empty().append(data).show();
        }

        /**
         * Function to handle click event on a file item of the directory tree
         *
         * @param {object} item
         */
        function handleClick(item)
        {
            const path = $(item).data('item-path');
            const note = $('div.note');
            const content = $('#log-content');

            $.ajax({
                url: config.baseUrl,
                type: 'GET',
                dataType: 'json',
                data: { path: path },
                showLoader: true,
                success(response) {
                    const message = response.message || '';

                    if (response.error) {
                        handleErrorState(content, note, message);
                    } else {
                        handleSuccessState(content, note, response.content);
                    }
                },
                error(jqXHR, textStatus, errorThrown) {
                    console.error($.mage.__('Error while loading: '), textStatus, errorThrown);
                }
            });
        }

        /**
         * Function to check if there are any file nodes in the category tree
         */
        function checkTreeLoading()
        {
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
