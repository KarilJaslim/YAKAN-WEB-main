import ApiService from './api';

class OrderPollingService {
  constructor() {
    this.pollingIntervals = {};
    this.lastOrderStates = {};
  }

  /**
   * Start polling for order updates
   * @param {number} orderId - Order ID to poll
   * @param {function} onStatusChange - Callback when status changes
   * @param {number} interval - Polling interval in ms (default 5000)
   */
  startPolling(orderId, onStatusChange, interval = 5000) {
    // Clear existing polling for this order
    if (this.pollingIntervals[orderId]) {
      clearInterval(this.pollingIntervals[orderId]);
    }

    // Store initial state
    this.lastOrderStates[orderId] = null;

    // Start polling
    this.pollingIntervals[orderId] = setInterval(async () => {
      try {
        const response = await ApiService.getOrder(orderId);

        if (response.success && response.data) {
          const currentOrder = response.data;
          const lastState = this.lastOrderStates[orderId];

          // Check if status changed
          if (lastState && lastState.status !== currentOrder.status) {
            console.log(`📢 Order ${orderId} status changed: ${lastState.status} → ${currentOrder.status}`);
            onStatusChange({
              orderId,
              oldStatus: lastState.status,
              newStatus: currentOrder.status,
              order: currentOrder,
            });
          }

          // Check if payment status changed
          if (lastState && lastState.payment_status !== currentOrder.payment_status) {
            console.log(`💳 Order ${orderId} payment status changed: ${lastState.payment_status} → ${currentOrder.payment_status}`);
            onStatusChange({
              orderId,
              type: 'payment',
              oldStatus: lastState.payment_status,
              newStatus: currentOrder.payment_status,
              order: currentOrder,
            });
          }

          // Update stored state
          this.lastOrderStates[orderId] = currentOrder;
        }
      } catch (error) {
        console.error(`Error polling order ${orderId}:`, error);
      }
    }, interval);

    console.log(`🔄 Started polling for order ${orderId} every ${interval}ms`);
  }

  /**
   * Stop polling for a specific order
   */
  stopPolling(orderId) {
    if (this.pollingIntervals[orderId]) {
      clearInterval(this.pollingIntervals[orderId]);
      delete this.pollingIntervals[orderId];
      delete this.lastOrderStates[orderId];
      console.log(`⏹️ Stopped polling for order ${orderId}`);
    }
  }

  /**
   * Stop all polling
   */
  stopAllPolling() {
    Object.keys(this.pollingIntervals).forEach(orderId => {
      this.stopPolling(orderId);
    });
  }

  /**
   * Get all active polling orders
   */
  getActivePollingOrders() {
    return Object.keys(this.pollingIntervals);
  }

  /**
   * Check if polling for specific order
   */
  isPolling(orderId) {
    return !!this.pollingIntervals[orderId];
  }
}

export default new OrderPollingService();
