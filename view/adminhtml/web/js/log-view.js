define([
    'jquery',
    'mage/url'
], function ($, urlBuilder) {
    'use strict';

    $(document).ready(function () {
        function checkForNewElements() {
            const fileNodes = $('[data-item-type="file"]');
            if (fileNodes.length > 0) {
                observer.disconnect();
                fileNodes.each(function (index, item) {
                    $(item).on('click', function () {
                        const url = urlBuilder.build('log/log/view');
                        $.ajax({
                            url: url,
                            type: 'GET',
                            dataType: 'json',
                            data: {
                                path: $(item).data('item-path')
                            },
                            success: function(response) {
                                console.log('Odpowiedź JSON:', response);
                            },
                            error: function(jqXHR, textStatus, errorThrown) {
                                console.error('Wystąpił błąd:', textStatus, errorThrown);
                            }
                        });
                    });
                });
            }
        }

        const observer= new MutationObserver(checkForNewElements);
        observer.observe(document.querySelector('#category-tree'), { childList: true, subtree: true });
        checkForNewElements();
    });
});
