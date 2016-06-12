"use strict";
//const url = require('url');
const url = require('url');
class RefererType {
    constructor() {
        this.organicsearchs = [
            "daum.net",
            "eniro.se",
            "naver.com",
            "google.com",
            "yahoo.com",
            "msn.com",
            "bing.com",
            "aol.com",
            "aol.com",
            "lycos.com",
            "ask.com",
            "altavista.com",
            "ch.netscape.com",
            "cnn.com",
            "about.com",
            "mamma.com",
            "alltheweb.com",
            "voila.fr",
            "search.virgilio.it",
            "bing.com",
            "baidu.com",
            "alice.com",
            "yandex.com",
            "najdi.org.mk",
            "aol.com",
            "mamma.com",
            "seznam.cz",
            "search.com",
            "wp.pl",
            "online.onetcenter.org",
            "szukacz.pl",
            "yam.com",
            "pchome.com",
            "kvasir.no",
            "sesam.no",
            "ozu.es",
            "terra.com",
            "mynet.com",
            "ekolay.net",
            "rambler.ru"];
        this.paidSearchs = [
            "alexa.com",
            "amazon.com",
            "aol.com",
            "askjeeves.com",
            "biglobe.ne.jp",
            "bizrate.com",
            "btopenworld.com",
            "comcast.net",
            "cs.com",
            "dealtime.com",
            "dogpile.com",
            "earthlink.com",
            "excite.com",
            "froogle.com",
            "go.com",
            "disney.com",
            "infospace.com",
            "libero.it",
            "mamma.com",
            "metacrawler.com",
            "mysearch.com",
            "myway.com",
            "netscape.com",
            "nifty.com",
            "nytimes.com",
            "searchalot.com",
            "searchresult.net",
            "sportsline.com",
            "sympatico.ca",
            "teoma.com",
            "updated.com",
            "washingtonpost.com",
            "webcrawler.com",
            "yahoo.co.jp",
            "chip.de",
            "dindel.se",
            "evreka.se",
            "gulasidorna.se",
            "kvasir.dk",
            "kvasir.no",
            "luotain.com",
            "onvista.de",
            "passagen.se",
            "sol.dk",
            "sol.no",
            "suomi24.fi",
            "t-online.de",
            "selfpedia.org"
        ];
        this.socialnets = [
            "43things.com",
            "academia.edu",
            "about.me",
            "activerain.com",
            "advogato.org",
            "addthis.com",
            "allnurses.com",
            "anobii.com",
            "allrecipes.com",
            "ameba.jp",
            "asianave.com",
            "asmallworld.com",
            "athlinks.com",
            "audimated.com",
            "bebo.com",
            "biip.no",
            "blackplanet.com",
            "bolt.com",
            "busuu.com",
            "buzzfeed.com",
            "buzznet.com",
            "cafemom.com",
            "care2.com",
            "caringbridge.org",
            "smates.com",
            "cloob.com",
            "couchsurfing.com",
            "cozycot.com",
            "cross.tv",
            "crunchyroll.com",
            "cucumbertown.com",
            "cyworld.com",
            "dailystrength.org",
            "delicious.com",
            "deviantart.com",
            "diasporafoundation.org",
            "disaboom.com",
            "dol2day.de",
            "dontstayin.com",
            "draugiem.lv",
            "douban.com",
            "doximity.com",
            "dreamwidth.org",
            "dxy.cn",
            "elftown.com",
            "ello.co",
            "elixio.net",
            "englishbaby.com",
            "epernicus.com",
            "eons.com",
            "etoro.com",
            "experienceproject.com",
            "exploroo.com",
            "facebook.com",
            "faceparty.com",
            "fetlife.com",
            "filmaffinity.com",
            "filmow.com",
            "fledgewing.com",
            "flixster.com",
            "flickr.com",
            "fotki.com",
            "fotolog.com",
            "foursquare.com",
            "friendica.com",
            "friendsreunited.co.uk",
            "friendster.com",
            "fuelmyblog.com",
            "gaiaonline.com",
            "gamerdna.com",
            "gapyear.com",
            "gays.com",
            "geni.com",
            "gentlemint.com",
            "tvtag.com",
            "girlsaskguys.com",
            "gogoyoko.com",
            "goodreads.com",
            "goodwizz.com",
            "plus.google.com",
            "govloop.com",
            "grono.net",
            "habbo.com",
            "hi5.com",
            "hospitalityclub.org",
            "hotlist.com",
            "hr.com",
            "hubculture.com",
            "ibibo.com",
            "identi.ca",
            "indabamusic.com",
            "influenster.com",
            "instagram.com",
            "irc-galleria.net",
            "italki.com",
            "mobile.itsmy.com",
            "iwiw.hu",
            "jaiku.com",
            "jiepang.com",
            "kaixin001.com",
            "kiwibox.com",
            "lafango.com",
            "laibhaari.com",
            "last.fm",
            "latenightshots.com",
            "librarything.com",
            "lifeknot.com",
            "linkedin.com",
            "linkexpats.com",
            "listography.com",
            "livejournal.com",
            "livejournal.org",
            "livemocha.com",
            "makeoutclub.com",
            "meetin.org",
            "meetup.com",
            "meettheboss.tv",
            "mymfb.com",
            "mixi.jp",
            "mocospace.com",
            "mog.com",
            "mouthshut.com",
            "mubi.com",
            "myheritage.com",
            "mylife.com",
            "opera.com",
            "myspace.com",
            "nk.pl",
            "twoo.com",
            "forums.nexopia.com",
            "ngopost.in",
            "ning.com",
            "ok.ru",
            "opendiary.com",
            "orkut.com",
            "outeverywhere.com",
            "patientslikeme.com",
            "partyflock.nl",
            "pingsta.com",
            "pinterest.com",
            "plaxo.com",
            "playfire.com",
            "playlist.com",
            "plurk.com",
            "poolwo.com",
            "qapacity.com",
            "quechup.com",
            "qzone.qq.com",
            "raptr.com",
            "ravelry.com",
            "renren.com",
            "reverbnation.com",
            "ryze.com",
            "sciencestage.com",
            "mewe.com",
            "sharethemusic.com",
            "shelfari.com",
            "weibo.com",
            "skoob.com.br",
            "skyrock.com",
            "smartican.com",
            "truex.com",
            "sonico.com",
            "soundcloud.com",
            "spaces.ru",
            "spot.im",
            "spring.me",
            "stage32.com",
            "stickam.com",
            "streetlife.com",
            "studivz.net",
            "studentscircle.net",
            "stumbleupon.com",
            "tagged.com",
            "talkbiznow.com",
            "taltopia.com",
            "taringa.net",
            "termwiki.com",
            "the-sphere.com",
            "travbuddy.com",
            "travellerspoint.com",
            "tsu.co",
            "tribe.net",
            "trombi.com",
            "tuenti.com",
            "tumblr.com",
            "twitter.com",
            "cellufun.com",
            "uplike.com",
            "vk.com",
            "vampirefreaks.com",
            "viadeo.com",
            "virb.com",
            "vox.com",
            "wattpad.com",
            "wayn.com",
            "weeworld.com",
            "weheartit.com",
            "wellwer.com",
            "wepolls.com",
            "wer-kennt-wen.de",
            "weread.com",
            "wiser.org",
            "writeaprisoner.com",
            "xanga.com",
            "xing.com",
            "xt3.com",
            "yammer.com",
            "yelp.com",
            "yookos.com",
            "zoo.gr"];
    }
    isPaidSearch(url_string, referer) {
        url_string = url_string || "";
        referer = referer || "";
        var url_parts = url.parse(url_string, true);
        var medium = url_parts.query.utm_medium;
        if (medium == "cpc" || medium == "ppc" || medium == "paid-search" || medium == "Paid search" || medium == "paidsearch")
            return true;
        url_parts = url.parse(referer, true);
        for (var l of this.paidSearchs)
            if (url_parts.hostname != null && url_parts.hostname.endsWith(l))
                return true;
        return false;
    }
    isOrganicSearch(referer) {
        referer = referer || "";
        var url_parts = url.parse(referer, true);
        for (var l of this.organicsearchs)
            if (url_parts.hostname != null && url_parts.hostname.endsWith(l))
                return true;
        return false;
    }
    isEmail(url_string) {
        url_string = url_string || "";
        var url_parts = url.parse(url_string, true);
        var medium = url_parts.query.utm_medium;
        if (medium == "email" || medium == "emailmarketing" || medium == "email marketing" || medium == "email-marketing")
            return true;
    }
    isReferal(url_string) {
        url_string = url_string || "";
        var url_parts = url.parse(url_string, true);
        var medium = url_parts.query.utm_medium;
        if (medium == "referral")
            return true;
        return false;
    }
    isDirect(url_string, referer) {
        url_string = url_string || "";
        referer = referer || "";
        if (referer == undefined || referer == '')
            return true;
        var url_parts = url.parse(url_string, true);
        var medium = url_parts.query.utm_medium;
        if (medium == "(not set)" || medium == "(none)")
            return true;
        var source = url_parts.query.utm_source;
        if (source == "(direct)")
            return true;
        return false;
    }
    isSocial(url_string, referer) {
        url_string = url_string || "";
        referer = referer || "";
        var url_parts = url.parse(url_string, true);
        var medium = url_parts.query.utm_medium;
        if (medium == "Social" || medium == "social" || medium == "social-network" || medium == "social-media" || medium == "sm" || medium == "social network" || medium == "social media")
            return true;
        url_parts = url.parse(referer, true);
        for (var l of this.paidSearchs)
            if (url_parts.hostname != null && url_parts.hostname.endsWith(l))
                return true;
        return false;
    }
    getTypeName(code) {
        var names = ["Unknown", "Organice Search", "Social Network", "Referral", "Email", "Direct"];
    }
    getRefType(url_string, referer) {
        url_string = url_string || "";
        referer = referer || "";
        if (this.isPaidSearch(url_string, referer))
            return 1;
        if (this.isOrganicSearch(referer))
            return 2;
        if (this.isSocial(url_string, referer))
            return 3;
        if (this.isReferal(url_string))
            return 4;
        if (this.isEmail(url_string))
            return 5;
        if (this.isDirect(url_string, referer))
            return 6;
        return 0; //other advertising
    }
}
exports.RefererType = RefererType;
//# sourceMappingURL=referer.js.map