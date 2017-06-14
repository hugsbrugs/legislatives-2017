/* You can open the console in your browser and type 
window.__env to display it. */
(function (window) {
	window.__env = window.__env || {};

	//
	window.__env.defaultPageTitle = 'Guide de survie Politique';
	window.__env.defaultPageDescription = 'Guide des élus français : chaque citoyen trouvera tous ses élus : du maire au président de la république.';
	
	// API url
	window.__env.api = 'https://api.politiques.lol';

	// Base url
//	window.__env.base = '/';

	// Whether or not to enable debug mode
	// Setting this to false will disable console output
	window.__env.enableJsLogDebug = true;
}(this));

