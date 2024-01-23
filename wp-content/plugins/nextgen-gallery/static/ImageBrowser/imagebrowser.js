document.addEventListener(
    "DOMContentLoaded",
    function(event) {
    document.querySelectorAll('div.ngg-imagebrowser')
            .forEach(function (gallery) {
        new NggPaginatedGallery(
            gallery.dataset.nextgenGalleryId,
            '.ngg-imagebrowser'
        );
    });
});