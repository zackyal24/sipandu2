// Global helper untuk fetch dengan Authorization header dari localStorage
(function(){
  const API_BASE = window.API_BASE || 'http://localhost:5000/api';

  async function fetchWithAuth(url, options = {}) {
    const token = localStorage.getItem('sipandu_token');
    const headers = Object.assign({}, options.headers || {});
    if (token) headers['Authorization'] = 'Bearer ' + token;
    headers['Content-Type'] = headers['Content-Type'] || 'application/json';
    const res = await fetch(url, Object.assign({}, options, { headers }));
    const text = await res.text();
    let data = null;
    try { data = text ? JSON.parse(text) : null; } catch(e) { data = null; }
    return { ok: res.ok, status: res.status, data, rawText: text };
  }

  window.API_BASE = API_BASE;
  window.fetchWithAuth = fetchWithAuth;
})();
