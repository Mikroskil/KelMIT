mw.loader.using( 'mediawiki.legacy.ajax' ).done( function () {

(function ($, mw) {
    /** Advanced Site Notices ********
     * Allow to custom dynamic site notices
     * Maintainer: [[zh:User:PhiLiP]]
     */
    if (typeof(window.customASNInterval) == 'undefined') {
        window.customASNInterval = 15;
    }
    $(function () {
        $('#mw-dismissable-notice').css('display', 'none');
        if (window.closeASNForever || mw.config.get('wgAction') == 'edit' || mw.config.get('wgAction') == 'submit') {
            return;
        }

        var ln = $('#siteNotice');
        if (!ln.length) {
            return;
        }
        var cname = 'dismissASN';
        var cval = $.cookie(cname);
        if (cval == '') {
            cval = 0;
        }
        var rev = 0;
        var toid = null;

        var tb = $('<table id="asn-dismissable-notice" width="100%" style="background: transparent;"/>');
        var ct = $('<div id="advancedSiteNotices" style="height: 100px;"/>');
        var sd = $('<a href="#" title="Tutup"><img src="//upload.wikimedia.org/wikipedia/foundation/2/20/CloseWindow19x19.png" alt="Tutup" border="0"></a>');
        tb.append($('<tr/>').append($('<td/>').append(ct)).append($('<td style="vertical-align: top;"/>').append(sd)));
        var nts = null;

        sd.click(function () {
            $.cookie(cname, rev, {
                expires: 30,
                path: '/'
            });
            clearTimeout(toid);
            tb.remove();
            return false;
        });

        var getAttrs = function (nt) {
            var only = {
                sysop: nt.hasClass('only_sysop'),
                logged: nt.hasClass('only_logged'),
                anon: nt.hasClass('only_anon'),
            };
            only['usr'] = only['sysop'] || only['logged'] || only['anon'];
            return only;
        };

        var loadNotices = function (pos) {
            if (!tb.length) {
                return;
            }
            ct.css('min-height', ct.height() + 'px');
            var l = nts.length;
            var nt = null;
            var rt = 0;
            while (!nt || nt.attr('class')) {
                if (nt) {
                    var only = getAttrs(nt);
                    if (!only['usr'] || (($.inArray('sysop', mw.config.get('wgUserGroups')) != -1) && only['sysop']) || (mw.config.get('wgUserName') && only['logged']) || (!mw.config.get('wgUserName') && only['anon'])) {
                        break;
                    }
                }
                pos = pos % l;
                nt = $(nts[pos++]);
                rt++;
                if (rt == l) {
                    return;
                }
            }
            nt = nt.html();
            if (ct.html()) {
                ct.stop().fadeOut(function () {
                    ct.html(nt).fadeIn();
                });
            } else if (rev == cval) {
                return;
            } else {
                tb.appendTo(ln);
                ct.html(nt).fadeIn();
            }
            toid = setTimeout(function () {
                loadNotices(pos)
            }, window.customASNInterval * 1000);
        };

        $.get(mw.config.get('wgScript'), {
            title: 'Template:AdvancedSiteNotices/ajax',
            action: 'render'
        }, function (d) {
            nts = $('li', $(d).filter('ul.sitents'));
            rev = parseInt($('.nts_revision', d).text());
            var l = nts.length;
            loadNotices(Math.ceil(Math.random() * l));
        });
    });
})(jQuery, mediaWiki);

} );