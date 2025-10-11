// utils/auth.js

export const getToken = () => {
  const token = localStorage.getItem('bearer_token');

  // ✅ OPTIONAL: Add token validity check (e.g., expiration)
  if (!token) return null;

  try {
    const payload = JSON.parse(atob(token.split('.')[1])); // decode JWT
    const isExpired = payload.exp * 1000 < Date.now();
    if (isExpired) {
      localStorage.removeItem('bearer_token'); // cleanup
      return null;
    }
    return token;
  } catch (e) {
    return null;
  }
};

// 🔐 For protected routes
export const requireAuth = () => {
  const token = getToken();
  if (!token) {
    throw new Response("Unauthorized", {
      status: 302,
      headers: { Location: "/login" },
    });
  }
  return null;
};

// 🚫 For login/public routes
export const redirectIfAuthenticated = () => {
  const token = getToken();
  if (token) {
    throw new Response("Already logged in", {
      status: 302,
      headers: { Location: "/dashboard" },
    });
  }
  return null;
};
