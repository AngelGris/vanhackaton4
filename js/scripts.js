$(function() {
    $("form.menu-search input").keypress(function (e) {
        if ((e.which && e.which == 13) || (e.keyCode && e.keyCode == 13)) {
            $("form.menu-search").submit();
            return false;
        } else {
            return true;
        }
    });
});