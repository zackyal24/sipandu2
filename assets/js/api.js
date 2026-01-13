// Simple fetch helper that injects Authorization header and returns a rich response object
(function(){
  async function fetchWithAuth(url, options = {}) {
    options.headers = options.headers || {};
    const token = localStorage.getItem('sipandu_token');
    if (token) {
      options.headers['Authorization'] = 'Bearer ' + token;
    }

    const res = await fetch(url, options);
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
})();
