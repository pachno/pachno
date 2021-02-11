import $ from "jquery";
import Pachno from "../classes/pachno";

// Pachno.Main.toggleFavouriteArticle = function (url, article_id)
// {
//     Pachno.Helpers.fetch(url, {
//         loading: {
//             indicator: '#article_favourite_indicator_' + article_id,
//             hide: ['article_favourite_normal_' + article_id, 'article_favourite_faded_' + article_id]
//         },
//         success: {
//             callback: function (json) {
//                 if ($('#article_favourite_faded_' + article_id)) {
//                     if (json.starred) {
//                         $('#article_favourite_faded_' + article_id).hide();
//                         $('#article_favourite_indicator_' + article_id).hide();
//                         $('#article_favourite_normal_' + article_id).show();
//                     } else {
//                         $('#article_favourite_normal_' + article_id).hide();
//                         $('#article_favourite_indicator_' + article_id).hide();
//                         $('#article_favourite_faded_' + article_id).show();
//                     }
//                 } else if (json.subscriber != '') {
//                     $('#subscribers_list').append(json.subscriber);
//                 }
//             }
//         }
//     });
// };

const setupListeners = function () {
    const $body = $('body');
    $body.off('click', '.trigger-toggle-favourite');
    $body.on('click', '.trigger-toggle-favourite', function () {
        const $element = $(this);
        const url = $element.data('url');

        $element.addClass('submitting');
        Pachno.fetch(url, { method: 'POST' })
            .then((json) => {
                if (json.starred) {
                    $element.addClass('starred');
                } else {
                    $element.removeClass('starred');
                }
                $element.removeClass('submitting');
            });
    });
}

export {
    setupListeners
}
