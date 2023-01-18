jQuery(function($) {
    let $news_list = $('#bookly-news-list'),
        $template = $('#bookly-news-template'),
        $more = $('#bookly-more-news'),
        page = 1;

    loadNews();

    $more.on('click', function () {
        page++;
        loadNews();
    });

    function loadNews() {
        let ladda = Ladda.create($more[0]);
        ladda.start();
        $.ajax({
            url: ajaxurl,
            type: 'GET',
            data: {
                action: 'bookly_get_news',
                csrf_token: BooklyL10nGlobal.csrf_token,
                page: page,
            },
            dataType: 'json',
            success: function (response) {
                $.each(response.data, function (id, news) {
                    let media = '';
                    if (news.media_type === 'youtube') {
                        media = '<iframe class="card-img-top rounded-top" src="' + news.media_url + '" frameborder="0" width="476" height="261" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>';
                    } else if (news.media_type === 'image') {
                        media = '<img class="card-img-top rounded-top" style="height: 261px;object-fit: cover;" src="' + news.media_url + '" alt="Card image cap">';
                    }
                    $news_list.append(
                        $template.clone().show().html()
                        .replace(/{{id}}/g, news.id)
                        .replace(/{{title}}/g, news.title)
                        .replace(/{{text}}/g, news.text)
                        .replace(/{{date}}/g, news.created_at)
                        .replace(/{{media}}/g, media)
                        .replace(/{{button}}/g, news.button_text === null ? '' : '<a class="btn btn-primary" href="' + news.button_url + '" target="_blank">' + news.button_text + '</a>')
                        .replace(/{{border}}/g, news.seen === '0' ? ' border-danger' : '')
                    );
                });
                if (!response.more) {
                    $more.hide();
                }
                ladda.stop();
            }
        });
    }
});