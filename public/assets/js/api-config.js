// API Configuration
// Gunakan origin halaman agar cocok dengan port lokal/produksi
const API_BASE_URL = `${window.location.origin}/api`;

// Simple fetch helper that injects Authorization header and returns a rich response object
(function(){
  async function fetchWithAuth(url, options = {}) {
    // Gunakan API_BASE_URL jika url tidak dimulai dengan http
    const fullUrl = url.startsWith('http') ? url : `${API_BASE_URL}${url}`;
    
    options.headers = options.headers || {};
    const token = localStorage.getItem('sipandu_token');
    if (token) {
      options.headers['Authorization'] = 'Bearer ' + token;
    }

    const res = await fetch(fullUrl, options);
    let parsed = null;
    let text = null;

    try {
      parsed = await res.json();
    } catch (e) {
      try {
        text = await res.text();
      } catch (_) {
        text = null;
      }
    }

    return {
      ok: res.ok,
      status: res.status,
      data: parsed,
      raw: parsed || text,
      success: parsed && typeof parsed.success !== 'undefined' ? parsed.success : res.ok,
      error: parsed?.error
    };
  }

  window.fetchWithAuth = fetchWithAuth;
  window.API_BASE_URL = API_BASE_URL;
})();
