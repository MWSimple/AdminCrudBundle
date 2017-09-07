// Get the ul that holds the collection
var $collection;
// setup an "add a tag" link
var $addLink = $('<hr><a href="#" class="add_link btn btn-primary"><i class="glyphicon glyphicon-plus"></i></a>');
var $newLinkLi = $('<div></div>').append($addLink);

jQuery(document).ready(function() {
    // Get the ul that holds the collection of tags
    $collection = $('.collection');

    // add the "add a tag" anchor and li to the tags ul
    $collection.append($newLinkLi);
    
    // count the current form inputs we have (e.g. 2), use that as the new
    // index when inserting a new item (e.g. 2)
    $collection.data('index', $collection.find(':input').length);
    
    $(".add_link").on('click', function(e) {
        // prevent the link from creating a "#" on the URL
        e.preventDefault();

        // add a new tag form (see next code block)
        addForm($collection, $newLinkLi, '__name__');

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

});
