/* Simple client-side auth helpers for demo/login state */
(function(){
  const Auth = {
    isLoggedIn() { return !!localStorage.getItem('sipandu_user'); },
    getUser() {
      try { return JSON.parse(localStorage.getItem('sipandu_user')); } catch(e) { return null; }
    },
    requireAuth(redirectTo) {
      if (!this.isLoggedIn()) {
        window.location.href = redirectTo || '/index.html';
      }
    },
    requireRole(requiredRole, redirectTo) {
      const user = this.getUser();
      if (!user || user.role !== requiredRole) {
        window.location.href = redirectTo || '/index.html';
      }
    },
    logout(redirectTo) {
      localStorage.removeItem('sipandu_user');
      localStorage.removeItem('sipandu_token');
      window.location.href = redirectTo || '/index.html';
    }
  };

  window.SipanduAuth = Auth;
  window.requireAuth = Auth.requireAuth.bind(Auth);
  window.requireRole = Auth.requireRole.bind(Auth);
  window.logout = Auth.logout.bind(Auth);
  window.getSipanduUser = Auth.getUser.bind(Auth);
})();
