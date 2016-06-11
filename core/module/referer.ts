//const url = require('url');
import * as url from 'url';
export class RefererType {

	private paidSearchs = [
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

	private isPaidSearch(url_string: string, referer: string): boolean {
		var url_parts = url.parse(url_string, true);

		var medium = url_parts.query.utm_medium;
		if (medium == "cpc" || medium == "ppc" || medium == "paid-search" || medium == "Paid search" || medium == "paidsearch")
			return true;
	
		 url_parts = url.parse(referer, true);
		for (var l of this.paidSearchs) 
			if (url_parts.hostname.endsWith(l)) 
				return true;
		
		return false;
	}

	public getRefType(referer: string, medium: string): string {
				return "";
	}
}