function WECreateCookie(name, value, days) {
	var date = new Date();
	date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
	var expires = '; expires=' + date.toGMTString();
	document.cookie = name + '=' + value + expires + '; path=/';
}

function WEReadCookie(name) {
	var nameEQ = name + '=';
	var ca = document.cookie.split(';');
	for (var i = 0; i < ca.length; i++) {
		var c = ca[i];
		while (c.charAt(0) == ' ') {
			c = c.substring(1, c.length);
		}
		if (c.indexOf(nameEQ) == 0) {
			return c.substring(nameEQ.length, c.length);
		}
	}
	return null;
}

function WECheckCookies() {
	if (WEReadCookie('cookies_accepted') != 'Y') {
		var message_container = document.createElement('div');
		message_container.id = 'cookies-message-container';
		var html_code = '<div id="cookies-message" style="z-index: 999; position: fixed; left: 0px; bottom: 0px; width: 100%; padding: 10px 0px; border-top: 1px solid #BBBBBB; background: #EAEAEA; text-align: center;">Strona korzysta z plików cookies w celu realizacji usług zgodnie z <a href="/prywatnosc">Polityką Prywatności</a>.<br />Można określić warunki przechowywania i dostępu do cookies w przeglądarce. <a id="accept-cookies-checkbox" name="accept-cookies" href="javascript:WECloseCookiesWindow();" rel="nofollow">Rozumiem</a></div>';
		message_container.innerHTML = html_code;
		document.body.appendChild(message_container);
	}
}

function WECloseCookiesWindow() {
	WECreateCookie('cookies_accepted', 'Y', 365);
	document.getElementById('cookies-message-container').removeChild(document.getElementById('cookies-message'));
}

window.onload = WECheckCookies;