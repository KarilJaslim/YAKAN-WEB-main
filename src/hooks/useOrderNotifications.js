import { useEffect } from 'react';
import { useNotification } from '../context/NotificationContext';
import orderPollingService from '../services/orderPollingService';

export const useOrderNotifications = (orderId, enabled = true) => {
  const {
    notifyOrderConfirmed,
    notifyOrderProcessing,
    notifyOrderShipped,
    notifyOrderDelivered,
    notifyOrderCancelled,
    notifyPaymentVerified,
    addNotification,
  } = useNotification();

  useEffect(() => {
    if (!enabled || !orderId) return;

    const handleStatusChange = (change) => {
      const { type, newStatus, order } = change;

      if (type === 'payment') {
        // Handle payment status changes
        if (newStatus === 'verified') {
          notifyPaymentVerified(order.order_ref);
        } else if (newStatus === 'paid') {
          addNotification('💳 Payment received!', 'success', 4000);
        } else if (newStatus === 'failed') {
          addNotification('❌ Payment failed. Please try again.', 'error', 5000);
        }
      } else {
        // Handle order status changes
        switch (newStatus) {
          case 'confirmed':
            notifyOrderConfirmed(order.order_ref);
            break;
          case 'processing':
            notifyOrderProcessing(order.order_ref);
            break;
          case 'shipped':
            notifyOrderShipped(order.order_ref);
            break;
          case 'delivered':
            notifyOrderDelivered(order.order_ref);
            break;
          case 'cancelled':
            notifyOrderCancelled(order.order_ref);
            break;
          case 'pending_confirmation':
            addNotification('⏳ Waiting for admin confirmation...', 'info', 4000);
            break;
          default:
            addNotification(`Order status: ${newStatus}`, 'info', 3000);
        }
      }
    };

    // Start polling
    orderPollingService.startPolling(orderId, handleStatusChange, 5000);

    // Cleanup
    return () => {
      orderPollingService.stopPolling(orderId);
    };
  }, [orderId, enabled, notifyOrderConfirmed, notifyOrderProcessing, notifyOrderShipped, notifyOrderDelivered, notifyOrderCancelled, notifyPaymentVerified, addNotification]);
};

export default useOrderNotifications;
