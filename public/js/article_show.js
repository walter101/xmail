$(document).ready(function() {
    $('.js-like-article').on('click', function(e) {
        e.preventDefault();

        /*
            $link is referentie naar tag waarop is geklikt
            Je klikt op het item waarop de class js-like-article staat
            Daarna vraag je de attr('href') van dit article
            Dan heb je de url
        */
        var $link = $(e.currentTarget);
        $link.toggleClass('fa-heart-o').toggleClass('fa-heart');
        currentHeartCount = document.getElementsByName('js-like-article-count').value;

        $.ajax({
            method: 'POST',
            url: $link.attr('href'),
            data: {'heartCount' : 10 }
        }).done(function(data) {
            $('.js-like-article-count').html(data.hearts);
        })
    });
});
