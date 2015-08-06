var content = [];
var article = 0;
var current = '';
var loading = false;
var page = 0;
var feadId = 0;
var folderId = 0;
var disableScroll = false;
var speed = 125;
var type = null;
var isStatic = false;
var addFeadContent = null;
var language = null;
var color = null;
var storedMenu = null;

$(function () {
    $('#email').attr('type', 'email');
    $(document).ready(function () {
        $('.firstFocus').focus();
    });
});

$(function () {
    $(".sortable").sortable({ distance: 10, connectWith: '.feadList',
        start: function (event, ui) {
            $.each($('ul.sortable'), function () {
                $(this).append('<span class="placeholder"><br></span>');
            });
        },
        stop: function (event, ui) {
            $.each($('.sortable ul'), function () {
                var placeholder = $(this).find('.placeholder').remove();
            });
        },
        beforeStop: function (event, ui) {
            var struct = [];
            var folder = {
                id: 0,
                folder: 0,
                feads: []
            };
            struct.push(folder);
            var map = [ 0 ];

            feadList('.sortable', struct, map);
            struct[map[folder.id]] = folder;

            var url = '/fead/update?struct=' + JSON.stringify(struct);
            $.ajax(url);
        }}).disableSelection();
});

function feadList(element, struct, map) {
    var folder = struct[map[$(element).data('folder')]];

    element = element + ' li';
    $.each($(element), function () {
        // folder
        if ('folder' === $(this).data('type')) {
            var folderId = $(this).data('id');
            map[folderId] = struct.length;
            struct.push({
                id: folderId,
                folder: $(this).parent().data('folder'),
                feads: []
            });
        }

        var inFolder = $(this).parent().data('folder');
        if (undefined === inFolder) {
            inFolder = 0;
        }

        folder = struct[map[inFolder]];

        if ('folder' === $(this).data('type')) {
            folder.feads.push('f ' + folderId);
        }
        else {
            if ($(this).data('id')) {
                folder.feads.push($(this).data('id'));
            }
        }

        struct[map[inFolder]] = folder;
    });
}

function addFead(url) {
    var value;
    if (undefined === url) {
        url = $('#url').val();
        value = url;
    }
    url = '/fead/add?url=' + encodeURI(url);

    console.log(url);

    var element = '#addFead';
    var storage = $(element).html();
    $(element).html('<div class="center"><img src="/img/' + color + '/icon.png" id="loading"></div>');
    rotateLoad();
    $.ajax(url, {success: function (data) {
        console.log(data);
        var feads = [];
        if ('{' === data[0]) {
            data = JSON.parse(data);
            var feads = [];
            if (undefined !== data['success']) {
                document.location = '/home/fead/id/' + data['success'];
            }
            else if (undefined !== data['feads']) {
                feads = data['feads'];
            }
        }

        if (0 === feads.length) {
            $(element).html(storage);
            $('#url').val(value);
            $(element + ' ul').remove();
            $('#url').parent().append('<ul class="errors"><li>' + data['error'] + '</li></ul>');
        }
        else {
            element = $(element);
            element.html('');
            $.each(feads, function (index, value) {
                element.append('<div class="' + value['type'] + '"><a href="#" onclick="addFead(\'' +
                    value['url'] + '\')">' + value['title'] + '</a></div>');
            });
        }
    }});
}
function setTitle(title) {
    $('#title').html(title);
}
function showAddFead() {
    var element = '#menu';
    var label = $('#addFeadLabel').html();
    storedMenu = $(element).html();
    if (true || '' === $(element).html() && null === addFeadContent) {
        $.ajax('/fead/add', {success: function (data) {
            $(element).html(data);
            $(element).html('<h2><a href="#" onclick="backToMenu()" class="icon-chevron-left"></a>' + label +
                '</h2><div id="addFead">' + $(element + ' form').html() +
                '<div class="clearfix"></div></div>');
            addFeadContent = $(element).html();
            $('#url').attr('type', 'url');
            $('#url').focus();
        }});
    }
    else if ('' === $(element).html()) {
        $(element).html(addFeadContent);
        $('#url').focus();
    }
    else {
        $(element).html('');
    }
}

function backToMenu() {
    $('#menu').html(storedMenu);
}

function menu(disableHide) {
    var element = $('#menu').parent();
    if (element.hasClass('hidden')) {
        element.removeClass('hidden');
    }
    else if (!disableHide) {
        element.addClass('hidden');
    }
}
function setFeadId(id) {
    feadId = id;
}
function setFolder(id) {
    if (0 !== id) {
        folderId = id;
        feadId = 0;
    }
}
function leadingZero(number) {
    if (10 > number) {
        return'0' + number;
    }
    return number;
}
function longDate(time) {
    date = '';
    now = new Date();
    date = 'hh:ii';
    if ('de' === language) {
        date = date.replace('hh', leadingZero(time.getHours()));
    }
    else {
        date = date.replace('hh', leadingZero((0 === time.getHours() % 12) ? '12' : time.getHours() % 12));
        if (12 <= time.getHours()) {
            date += ' PM';
        }
        else {
            date += ' AM';
        }
    }
    date = date.replace('ii', leadingZero(time.getMinutes()));
    if (time.getDay() !== now.getDay() || time.getMonth() !== now.getMonth()) {
        if ('de' === language) {
            date = 'dd.mm.yy ' + date;
        }
        else {
            date = 'mm/dd/yy ' + date;
        }
        date = date.replace('dd', leadingZero(time.getDate()));
        date = date.replace('mm', leadingZero(time.getMonth() + 1));
        if (time.getFullYear() !== now.getFullYear()) {
            date = date.replace('yy', leadingZero(time.getFullYear() % 100));
        }
        else {
            var dot = ('de' === language) ? '.' : '';
            date = date.substring(0, 5) + dot + date.substring(8);
        }
    }
    return date;
}
function expand(id, fead) {
    if ('' !== current) {
        $(current).html('');
    }
    var element = '.article.' + id + ' .content';
    if (current === element && 0 !== article) {
        $(element).html('');
        article = 0;
    }
    else {
        var time = new Date(Math.floor(($('.article.' + id + ' .time').data('time') + new Date().getUTCOffset() * 36) * 1000));
        var date = longDate(time);
        current = element;
        article = id;
        $(element).show();
        if (undefined === content[id]) {
            $(element).html('<div class="center"><img src="/img/' + color + '/icon.png" id="loading"></div>');
            rotateLoad();
            $.ajax('/article/show/id/' + id + '/fead/' + fead, {success: function (data) {
                if (current === element) {
                    $(element).html(data);
                    content[id] = data;
                    $('.article.' + id + ' .detailTime').html(date);
                }
            }});
        }
        else {
            $(element).html(content[id]);
            $('.article.' + id + ' .detailTime').html(date);
        }
        $('body').scrollTop($('#' + id).offset().top - 51);
        if ($('.article.' + id + ' .title p').hasClass('unread')) {
            $('.article.' + id + ' .title p').removeClass('unread');
            var unread = $('#menuFead ul').find('[data-id="' + fead + '"] .unread');
            if (unread.html().trim() > 1) {
                unread.html(unread.html().trim() - 1);
            }
            else {
                unread.html("&nbsp;");
            }
        }
    }
}
function favorite(id) {
    var url = '/article/favorite/id/' + id;
    $.ajax(url, {success: function (data) {
        var fav = $('.article.' + id + ' .favorite');
        if (fav.hasClass('icon-star')) {
            fav.removeClass('icon-star');
            fav.addClass('icon-star-empty');
        }
        else {
            fav.removeClass('icon-star-empty');
            fav.addClass('icon-star');
        }
        if ('.article.' + id + ' .content' === current) {
            $('.meta .fav').html(data);
        }
    }, error: function () {
        alert('error');
    }});
}

function deleteFead(id, text) {
    var url = '/fead/delete/id/' + id;
    if (confirm(text)) {
        $.ajax(url, {success: function () {
            location.reload();
        }});
    }
}

function deleteFolder(id, text) {
    var url = '/fead/delete/folder/' + id;
    if (confirm(text)) {
        $.ajax(url, {success: function () {
            location.reload();
        }});
    }
}

function unread(id) {
    var url = '/article/unread/id/' + id;
    $.ajax(url, {success: function (data) {
        if ($('.article.' + id + ' .title p').hasClass('unread')) {
            $('.article.' + id + ' .title p').removeClass('unread');
        }
        else {
            $('.article.' + id + ' .title p').first().addClass('unread');
        }
        if ('.article.' + id + ' .content' === current) {
            $('.meta .unread').html(data);
        }
    }, error: function () {
        alert('error');
    }});
}
function refresh() {
    $('#articles').html('<div id="load" class="center"><img src="/img/' + color + '/icon.png" id="loading"></div>');
    rotateLoad();
    page = 0;
    disableScroll = false;
    loadArticles(type, true);
}
$(document).keyup(function (event) {
    if ('body' !== document.activeElement.tagName.toLowerCase()) {
        return;
    }
    else if (13 === event.keyCode) {
        event.defaultValue();
        return;
    }
    if (74 === event.keyCode) {
        if ('' !== current) {
            $(current).parent().next().find('.title .preview p').trigger('click');
        }
        else {
            $('.article').first().find('.title .preview p').trigger('click');
        }
    }
    else if (75 === event.keyCode) {
        $(current).parent().prev().find('.title .preview p').trigger('click');
    }
    else if (27 === event.keyCode) {
        if ('' !== current && 0 !== article) {
            $(current).html('');
            article = 0;
        }
    }
    else if (77 === event.keyCode) {
        var link = $('#menu .selected').prev().find('a.feadTitle').attr('href');
        if (undefined !== link) {
            document.location = link;
        }
    }
    else if (78 === event.keyCode) {
        var link = $('#menu .selected').next().find('a.feadTitle').attr('href');
        if (undefined !== link) {
            document.location = link;
        }
    }
    else if (70 === event.keyCode) {
        if (0 !== article) {
            favorite(article);
        }
    }
    else if (85 === event.keyCode) {
        if (0 !== article) {
            unread(article);
        }
    }
});
function readAll() {
    var url = '/fead/read';
    if (0 !== feadId) {
        url += '/id/' + feadId;
    }
    if (null !== type) {
        url += '/type/' + type;
    }
    $.ajax(url, {success: function () {
        location.reload();
    }, error: function () {
        alert('error');
    }});
}
function loadLatest() {
    var id = $('.article').first().attr('class');
    id = id.substr(8, id.length - 8);
    var url = '/fead/load/id/' + feadId + '/latest/' + id;
    if (null !== type) {
        url += '/type/' + type;
    }
    $.ajax(url, {success: function (data) {
        $('#articles').prepend(data);
    }});
}
function loadArticles(typeDec, loadAlways) {
    if (null !== typeDec) {
        type = typeDec;
    }
    if (disableScroll) {
        return;
    }
    var element = '#load';
    var docViewTop = $(window).scrollTop();
    var docViewBottom = docViewTop + $(window).height();
    var elemTop = $(element).offset().top;
    var elemBottom = elemTop + $(element).height();
    if (((elemTop < docViewBottom) && (elemTop > docViewTop) && !loading) || loadAlways) {
        loading = true;
        rotateLoad();
        var url = '/fead/load/';
        if (0 === folderId) {
            url += 'id/' + feadId;
        }
        else {
            url += 'folder/' + folderId;
        }
        url += '/page/' + page++;

        if (null !== type) {
            url += '/type/' + type;
        }
        $.ajax(url, {success: function (data) {
            if (!loading) {
                return;
            }
            $('#load').remove();

            if (0 != data.length) {
                data = JSON.parse(data);
                var count = 0;
                var last = $('#articles').last().find('.time').data('time');
                if (null !== last) {
                    last = parseInt(last);
                }
                $.each(data, function (index, value) {
                    if (!$('.article.' + value['id']).length && (null === last || last > parseInt(value['timestamp']))) {
                        $('#articles').append(value['content']);
                        count++;
                    }
                });

                loading = false;
                if (0 === count) {
                    refresh();
                    return;
                }

                $('#articles').append('<div id="load" class="center"><img src="/img/' + color + '/icon.png" id="loading" onclick="loadArticles(null, true)"></div>');
                updateArticles(true);
                loadArticles(type, false);
            }
            else {
                loading = false;
                disableScroll = true;
                if (undefined === $('.article').html()) {
                    $('#emptyList').removeClass('hidden');
                }
            }
        }});
    }
}
function setDesign(color) {
    $.ajax('/user/prefs/color/' + color, {success: function () {
        location.reload();
    }});
}
function setMenuStatic(mode) {
    $.ajax('/user/prefs/menuStatic/' + mode, {success: function () {
        location.reload();
    }});
}
function settings(lang, clr) {
    language = lang;
    color = clr;
}
function setLanguage(language) {
    $.ajax('/user/prefs/language/' + language, {success: function () {
        location.reload();
    }});
}
function rotateLoad() {
    var $elie = $('#loading'), degree = 0, timer;
    rotate();
    function rotate() {
        $elie.css({WebkitTransform: 'rotate(' + degree + 'deg)'});
        $elie.css({'-moz-transform': 'rotate(' + degree + 'deg)'});
        timer = setTimeout(function () {
            degree += 2;
            rotate();
        }, 5);
    }

    $("input").toggle(function () {
        clearTimeout(timer);
    }, function () {
        rotate();
    });
}
function loadStaticMenu() {
    isStatic = true;
    if (1024 < document.width) {
        $('.sidebar').removeClass('hidden');
    }
}
function updateArticles(once) {
    var gmt = Math.floor(new Date().getTime() / 1000);
    gmt -= parseInt(new Date().getUTCOffset()) * 36;
    $.each($('#articles .article .time'), function () {
        var diff = Math.floor(gmt - $(this).data('time'));
        if (0 > diff) {
            diff = '';
        }
        else if (60 > diff) {
            diff += 's';
        }
        else if (3600 > diff) {
            diff = Math.floor(diff / 60) + 'm';
        }
        else if (86400 > diff) {
            diff = Math.floor(diff / 3600) + 'h';
        }
        else {
            diff = Math.floor(diff / 86400) + 'd';
        }
        $(this).html(diff);
    });
    if (undefined === once) {
        window.setTimeout(updateArticles, 10000);
    }
}

function sendShortMail(id) {
    $.ajax('/share/add/id/' + id + '/type/email', {success: function (data) {
        if ('' !== data) {
            document.location = data;
        }
    }});
}

function expandShare() {
    var share = $('.meta .share ul');
    console.log(share);
    if (share.hasClass('hidden')) {
        share.removeClass('hidden');
    }
    else {
        share.addClass('hidden');
    }
}

function expandFolder(id) {
    var element = $('#folder' + id);
    if (element.hasClass('hidden')) {
        element.removeClass('hidden');
        element.parent().find('.element span').removeClass('icon-rotate-270');
    }
    else {
        element.addClass('hidden');
        element.parent().find('.element span').addClass('icon-rotate-270');
    }
}
