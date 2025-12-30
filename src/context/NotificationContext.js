import React, { createContext, useContext, useState, useCallback, useEffect } from 'react';

const NotificationContext = createContext();

export const NotificationProvider = ({ children }) => {
  const [notifications, setNotifications] = useState([]);

  // Add a notification
  const addNotification = useCallback((message, type = 'info', duration = 4000) => {
    const id = Date.now();
    const notification = {
      id,
      message,
      type, // 'success', 'error', 'info', 'warning'
      timestamp: new Date(),
    };

    setNotifications(prev => [notification, ...prev]);

    // Auto-remove after duration
    if (duration > 0) {
      setTimeout(() => {
        removeNotification(id);
      }, duration);
    }

    return id;
  }, []);

  // Remove a notification
  const removeNotification = useCallback((id) => {
    setNotifications(prev => prev.filter(notif => notif.id !== id));
  }, []);

  // Clear all notifications
  const clearNotifications = useCallback(() => {
    setNotifications([]);
  }, []);

  // Order-specific notifications
  const notifyOrderCreated = useCallback((orderRef) => {
    addNotification(
      `✅ Order ${orderRef} created successfully!`,
      'success',
      5000
    );
  }, [addNotification]);

  const notifyOrderConfirmed = useCallback((orderRef) => {
    addNotification(
      `🎉 Order ${orderRef} confirmed by admin!`,
      'success',
      5000
    );
  }, [addNotification]);

  const notifyOrderProcessing = useCallback((orderRef) => {
    addNotification(
      `⚙️ Order ${orderRef} is being processed...`,
      'info',
      4000
    );
  }, [addNotification]);

  const notifyOrderShipped = useCallback((orderRef) => {
    addNotification(
      `📦 Order ${orderRef} has been shipped!`,
      'success',
      5000
    );
  }, [addNotification]);

  const notifyOrderDelivered = useCallback((orderRef) => {
    addNotification(
      `🚚 Order ${orderRef} delivered successfully!`,
      'success',
      5000
    );
  }, [addNotification]);

  const notifyOrderCancelled = useCallback((orderRef) => {
    addNotification(
      `❌ Order ${orderRef} has been cancelled.`,
      'error',
      5000
    );
  }, [addNotification]);

  const notifyPaymentVerified = useCallback((orderRef) => {
    addNotification(
      `💳 Payment for order ${orderRef} verified!`,
      'success',
      5000
    );
  }, [addNotification]);

  const notifyError = useCallback((message) => {
    addNotification(message, 'error', 5000);
  }, [addNotification]);

  const value = {
    notifications,
    addNotification,
    removeNotification,
    clearNotifications,
    notifyOrderCreated,
    notifyOrderConfirmed,
    notifyOrderProcessing,
    notifyOrderShipped,
    notifyOrderDelivered,
    notifyOrderCancelled,
    notifyPaymentVerified,
    notifyError,
  };

  return (
    <NotificationContext.Provider value={value}>
      {children}
    </NotificationContext.Provider>
  );
};

export const useNotification = () => {
  const context = useContext(NotificationContext);
  if (!context) {
    throw new Error('useNotification must be used within NotificationProvider');
  }
  return context;
};
