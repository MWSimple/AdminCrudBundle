(function ($) {
    $.fn.embed = function () {
        $(this).attr('class')
        // Get the ul that holds the collection of tags
        var $collection = $(this);
        var $collectionClass = $collection.attr('class');
        // setup an "add a tag" link
        var $addLink = $('<div class="col-md-12"><a href="#" class="add_link_'+$collectionClass+' btn btn-primary"><i class="glyphicon glyphicon-plus"></i></a></div>');
        var $newLinkLi = $('<div class="row"></div>').append($addLink);

        // add the "add a tag" anchor and li to the tags ul
        $collection.append($newLinkLi);
        
        // count the current form inputs we have (e.g. 2), use that as the new
        // index when inserting a new item (e.g. 2)
        $collection.data('index', $collection.find(':input').length);
        
        $(".add_link_"+$collectionClass).on('click', function(e) {
            // prevent the link from creating a "#" on the URL
            e.preventDefault();

            // add a new tag form (see next code block)
            addForm($collection, $newLinkLi);

            $('html, body').stop().animate({
                scrollTop: $($newLinkLi).prev().offset().top
            }, 1000);
        });

        $collection.delegate('.delete_link', 'click', function(e) {
            // prevent the link from creating a "#" on  the URL
            e.preventDefault();
            // remove the li for the tag form
            removeForm(jQuery(this).closest('.rowremove'));
        });
    }
})(jQuery);
