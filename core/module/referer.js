"use strict";
class RefererType {
    constructor() {
        this.paidSearchs = ["alexa.com",
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
            "selfpedia.org"];
    }
    isPaidSearch(url, referer) {
        for (var l of this.paidSearchs) {
        }
    }
    getRefType(referer, medium) {
        return "";
    }
}
exports.RefererType = RefererType;
//# sourceMappingURL=referer.js.map