import { useState, useCallback } from 'react';
import { Alert } from 'react-native';
import AsyncStorage from '@react-native-async-storage/async-storage';
import ApiService from './src/services/api';

const ORDERS_KEY = 'pendingOrders';

export const useOrders = () => {
  const [orders, setOrders] = useState([]);
  const [loading, setLoading] = useState(true);
  const [refreshing, setRefreshing] = useState(false);

  const loadOrders = useCallback(async () => {
    setLoading(true);
    try {
      const savedOrders = await AsyncStorage.getItem(ORDERS_KEY);
      const localOrders = savedOrders ? JSON.parse(savedOrders) : [];

      // Refresh statuses from backend when we have an order id
      const normalizeStatus = (apiOrder, fallbackStatus) => {
        // If payment is marked paid/verified and status is still pending, show payment_verified
        if ((apiOrder.payment_status === 'paid' || apiOrder.payment_status === 'verified') && (!apiOrder.status || apiOrder.status === 'pending')) {
          return 'payment_verified';
        }
        // Map backend statuses to mobile timeline keys
        const map = {
          pending: 'pending_payment',
          pending_payment: 'pending_payment',
          payment_verified: 'payment_verified',
          confirmed: 'pending_confirmation',
          processing: 'processing',
          shipped: 'shipped',
          delivered: 'delivered',
          cancelled: 'cancelled',
        };
        return map[apiOrder.status] || fallbackStatus || 'pending_payment';
      };

      const refreshedOrders = await Promise.all(
        localOrders.map(async (order) => {
          if (!order.backendOrderId) return order;

          try {
            const res = await ApiService.getOrder(order.backendOrderId);
            if (res?.success && res.data) {
              const apiOrder = res.data;
              const normalizedStatus = normalizeStatus(apiOrder, order.status);

              return {
                ...order,
                status: normalizedStatus,
                paymentStatus: apiOrder.payment_status || order.paymentStatus,
                total: apiOrder.total_amount ?? apiOrder.total ?? order.total,
                subtotal: apiOrder.subtotal ?? order.subtotal,
                shippingFee: apiOrder.shipping_fee ?? order.shippingFee,
              };
            }
          } catch (err) {
            console.warn('Failed to refresh order from API', err?.message || err);
          }
          return order;
        })
      );

      // Persist refreshed data
      await AsyncStorage.setItem(ORDERS_KEY, JSON.stringify(refreshedOrders));

      setOrders(refreshedOrders.sort((a, b) => new Date(b.date) - new Date(a.date)));
    } catch (error) {
      console.error('Failed to load orders from local storage:', error);
      setOrders([]);
    } finally {
      setLoading(false);
      setRefreshing(false);
    }
  }, []);

  const onRefresh = useCallback(() => {
    setRefreshing(true);
    loadOrders();
  }, [loadOrders]);

  const savePaymentProof = async (order, imageUri) => {
    setLoading(true);
    try {
      const savedOrders = await AsyncStorage.getItem(ORDERS_KEY);
      const currentOrders = savedOrders ? JSON.parse(savedOrders) : [];

      const updatedOrders = currentOrders.map(o => {
        if (o.orderRef === order.orderRef) {
          return {
            ...o,
            paymentProof: imageUri,
            paymentProofDate: new Date().toISOString(),
            status: 'payment_verified',
          };
        }
        return o;
      });

      await AsyncStorage.setItem(ORDERS_KEY, JSON.stringify(updatedOrders));
      Alert.alert(
        'Success!',
        'Payment proof uploaded successfully. Your order will be processed soon.',
        [{ text: 'OK', onPress: () => loadOrders() }]
      );
    } catch (error) {
      console.error('Error saving payment proof:', error);
      Alert.alert('Error', 'Failed to upload payment proof');
    } finally {
      setLoading(false);
    }
  };

  return {
    orders,
    loading,
    refreshing,
    loadOrders,
    onRefresh,
    savePaymentProof,
  };
};