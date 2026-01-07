// Simple fetch helper that injects Authorization header when token present
(function(){
  async function fetchWithAuth(url, options = {}){
    options.headers = options.headers || {};
    const token = localStorage.getItem('sipandu_token');
    if (token) {
      options.headers['Authorization'] = 'Bearer ' + token;
    }
    const res = await fetch(url, options);
    if (!res.ok) {
      const txt = await res.text();
      const err = new Error('Network response was not ok: ' + res.status);
      err.body = txt;
      throw err;
    }
    return res.json();
  }

  window.fetchWithAuth = fetchWithAuth;
})();
