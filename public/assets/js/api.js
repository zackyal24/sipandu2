// Simple fetch helper that injects Authorization header and returns a rich response object
(function(){

  async function fetchWithAuth(url, options = {}) {
    options.headers = options.headers || {};
    const token = localStorage.getItem('sipandu_token');
    if (token) {
      options.headers['Authorization'] = 'Bearer ' + token;
    }

    // Offline check
    if (!navigator.onLine) {
      return {
        ok: false,
        status: 0,
        data: null,
        raw: null,
        success: false,
        error: 'Tidak ada koneksi internet. Silakan cek jaringan Anda.'
      };
    }

    // Timeout logic (default 10s)
    const timeoutMs = options.timeout || 10000;
    const controller = new AbortController();
    options.signal = controller.signal;
    const timeoutId = setTimeout(() => controller.abort(), timeoutMs);

    let res;
    let parsed = null;
    let text = null;
    try {
      res = await fetch(url, options);
    } catch (err) {
      clearTimeout(timeoutId);
      let msg = 'Network error atau server tidak merespons';
      if (err.name === 'AbortError') {
        msg = 'Permintaan ke server melebihi batas waktu (' + (timeoutMs/1000) + ' detik).';
      } else if (!navigator.onLine) {
        msg = 'Tidak ada koneksi internet. Silakan cek jaringan Anda.';
      }
      return {
        ok: false,
        status: 0,
        data: null,
        raw: null,
        success: false,
        error: msg
      };
    }
    clearTimeout(timeoutId);

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
