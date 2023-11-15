tarteaucitronForceLanguage = 'fr';

tarteaucitron.init({
	"privacyUrl": "", /* Privacy policy url */

	"hashtag": "#tarteaucitron", /* Open the panel with this hashtag */
	"cookieName": "tarteaucitron", /* Cookie name */

	"orientation": "bottom", /* Banner position (top - bottom) */
	"showAlertSmall": false, /* Show the small banner on bottom right */
	"cookieslist": true, /* Show the cookie list */

	"adblocker": false, /* Show a Warning if an adblocker is detected */
	"AcceptAllCta": true, /* Show the accept all button when highPrivacy on */
	"highPrivacy": true, /* Disable auto consent */
	"handleBrowserDNTRequest": false, /* If Do Not Track == 1, disallow all */

	"removeCredit": true, /* Remove credit link */
	"moreInfoLink": false, /* Show more info link */
	"useExternalCss": false, /* If false, the tarteaucitron.css file will be loaded */

	"readmoreLink": policyUrl /* Change the default readmore link */
});
tarteaucitron.user.gtagUa = googleGtm;
tarteaucitron.user.gtagMore = function () {
	dataLayer/* add here your optionnal gtag() */
};
(tarteaucitron.job = tarteaucitron.job || []).push('gtag');
tarteaucitron.user.googletagmanagerId = googleTagUa;
(tarteaucitron.job = tarteaucitron.job || []).push('googletagmanager');
