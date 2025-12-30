// Configuration file for API and app settings
// Update the API_BASE_URL when your Laravel backend is ready

export const API_CONFIG = {
  // ⚠️ UPDATE THIS WITH YOUR LARAVEL BACKEND URL
  // Using ngrok tunnel for reliable mobile app access
  API_BASE_URL: 'https://transpleural-exigently-marlee.ngrok-free.dev/api/v1',
  
  // Base URL for storage/uploads (images, files, etc.)
  STORAGE_BASE_URL: 'https://transpleural-exigently-marlee.ngrok-free.dev/uploads',
  
  // Polling interval for order status updates (in milliseconds)
  POLLING_INTERVAL: 10000, // 10 seconds
  
  // Request timeout - increased for network latency
  REQUEST_TIMEOUT: 90000, // 90 seconds
  
  // API Endpoints
  ENDPOINTS: {
    // Auth endpoints
    AUTH: {
      REGISTER: '/register',
      LOGIN: '/login',
      LOGOUT: '/logout',
      REFRESH_TOKEN: '/refresh-token',
      GET_USER: '/user',
    },
    
    // Products endpoints
    PRODUCTS: {
      LIST: '/products',
      GET: '/products/:id',
      SEARCH: '/products/search',
    },
    
    // Cart/Order endpoints
    ORDERS: {
      CREATE: '/orders',
      LIST: '/orders',
      GET: '/orders/:id',
      UPDATE: '/orders/:id',
      CANCEL: '/orders/:id/cancel',
      STATUS: '/orders/:id/status',
    },
    
    // Payment endpoints
    PAYMENT: {
      UPLOAD_PROOF: '/payments/upload-proof',
      VERIFY: '/payments/verify',
      STATUS: '/payments/:orderId/status',
    },
    
    // User endpoints
    USER: {
      GET_PROFILE: '/user/profile',
      UPDATE_PROFILE: '/user/profile',
      GET_ADDRESSES: '/user/addresses',
      CREATE_ADDRESS: '/user/addresses',
      UPDATE_ADDRESS: '/user/addresses/:id',
      DELETE_ADDRESS: '/user/addresses/:id',
    },

    // Chat endpoints
    CHAT: {
      LIST: '/chats',
      GET: '/chats/:id',
      CREATE: '/chats',
      SEND_MESSAGE: '/chats/:id/messages',
      UPDATE_STATUS: '/chats/:id/status',
    },

    // Custom Orders endpoints
    CUSTOM_ORDERS: {
      LIST: '/custom-orders',
      GET: '/custom-orders/:id',
      CREATE: '/custom-orders',
      UPDATE_STATUS: '/custom-orders/:id/status',
      CANCEL: '/custom-orders/:id/cancel',
    },

    // Shipping endpoints
    SHIPPING: {
      GET_RATE: '/shipping/rate',
      CALCULATE_FEE: '/shipping/calculate-fee',
    },
  },
};

export default API_CONFIG;
