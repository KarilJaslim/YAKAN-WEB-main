// src/screens/OrderDetailsScreen.js - Enhanced UI/UX
import { useState, useEffect } from 'react';
import {
  View,
  Text,
  StyleSheet,
  ScrollView,
  TouchableOpacity,
  Alert,
  ActivityIndicator,
  Animated,
} from 'react-native';
import AsyncStorage from '@react-native-async-storage/async-storage';
import colors from '../constants/colors';
import { trackingStages } from '../constants/tracking';
import ApiService from '../services/api';
import useOrderNotifications from '../hooks/useOrderNotifications';

const normalizeStatus = (apiStatus, paymentStatus, fallback) => {
  const map = {
    pending: 'pending_payment',
    pending_payment: 'pending_payment',
    payment_verified: 'payment_verified',
    pending_confirmation: 'pending_confirmation',
    confirmed: 'pending_confirmation',
    processing: 'processing',
    shipped: 'shipped',
    delivered: 'delivered',
    cancelled: 'cancelled',
  };

  let status = map[apiStatus] || apiStatus || fallback;

  // If payment is already paid/verified but status is still pending-ish, push to payment_verified
  if ((paymentStatus === 'paid' || paymentStatus === 'verified') && (!status || status === 'pending' || status === 'pending_payment' || status === 'pending_confirmation')) {
    status = 'payment_verified';
  }

  return status || 'pending_payment';
};

const OrderDetailsScreen = ({ navigation, route }) => {
  const [order, setOrder] = useState(null);
  const [loading, setLoading] = useState(true);
  const scaleAnim = new Animated.Value(0);
  const slideAnim = new Animated.Value(100);

  // Enable order notifications
  const orderId = order?.backendOrderId || order?.id;
  useOrderNotifications(orderId, !!orderId);

  useEffect(() => {
    loadOrderDetails();
    // Trigger animations
    Animated.parallel([
      Animated.spring(scaleAnim, {
        toValue: 1,
        useNativeDriver: true,
      }),
      Animated.timing(slideAnim, {
        toValue: 0,
        duration: 500,
        useNativeDriver: true,
      }),
    ]).start();
  }, []);

  useEffect(() => {
    const unsubscribe = navigation.addListener('focus', () => {
      if (order?.backendOrderId || order?.id) {
        refreshFromApi(order.backendOrderId || order.id);
      }
    });
    return unsubscribe;
  }, [navigation, order]);

  // Poll periodically while on this screen to pick up admin status changes
  useEffect(() => {
    if (!order?.backendOrderId && !order?.id) return;
    const backendId = order.backendOrderId || order.id;
    const interval = setInterval(() => {
      refreshFromApi(backendId);
    }, 8000);
    return () => clearInterval(interval);
  }, [order?.backendOrderId, order?.id]);

  const refreshFromApi = async (backendId) => {
    if (!backendId) return;
    try {
      const res = await ApiService.getOrder(backendId);
      if (res?.success && res.data) {
        // Some endpoints wrap the order inside data/order, others return the order directly
        const apiOrder = res.data.data?.order || res.data.data || res.data;
        setOrder((prev) => {
          const normalizedStatus = normalizeStatus(apiOrder.status, apiOrder.payment_status, prev?.status);
          return {
            ...prev,
            ...apiOrder,
            backendOrderId: apiOrder.id || prev?.backendOrderId,
            orderRef: apiOrder.orderRef || apiOrder.order_ref || prev?.orderRef,
            status: normalizedStatus,
            paymentStatus: apiOrder.payment_status || prev?.paymentStatus,
            subtotal: apiOrder.subtotal ?? prev?.subtotal ?? 0,
            shippingFee: apiOrder.shipping_fee ?? prev?.shippingFee ?? 0,
            total: apiOrder.total ?? apiOrder.total_amount ?? prev?.total ?? 0,
          };
        });
      }
    } catch (err) {
      console.warn('Failed to refresh order from API', err?.message || err);
    }
  };

  const loadOrderDetails = async () => {
    try {
      // First, try to get from route params
      if (route.params?.orderData) {
        console.log('Order from params:', route.params.orderData);
        setOrder(route.params.orderData);
        // Attempt to refresh from API to get latest status
        const backendId = route.params.orderData.backendOrderId || route.params.orderData.id;
        if (backendId) refreshFromApi(backendId);
        setLoading(false);
        return;
      }

      // If no params, try to get orderRef and load from storage
      if (route.params?.orderRef) {
        const ordersJson = await AsyncStorage.getItem('pendingOrders');
        if (ordersJson) {
          const orders = JSON.parse(ordersJson);
          const foundOrder = orders.find(o => o.orderRef === route.params.orderRef);
          if (foundOrder) {
            console.log('Order from storage:', foundOrder);
            setOrder(foundOrder);
            if (foundOrder.backendOrderId || foundOrder.id) {
              refreshFromApi(foundOrder.backendOrderId || foundOrder.id);
            }
          }
        }
      }
    } catch (error) {
      console.error('Error loading order:', error);
      Alert.alert('Error', 'Failed to load order details');
    } finally {
      setLoading(false);
    }
  };

  const formatDate = (dateString) => {
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', {
      month: 'short',
      day: 'numeric',
      year: 'numeric',
      hour: '2-digit',
      minute: '2-digit',
    });
  };

  const handleContactSeller = () => {
    Alert.alert(
      'Contact Seller',
      'How would you like to contact the seller?',
      [
        {
          text: 'Messenger',
          onPress: () => Alert.alert('Opening Messenger...'),
        },
        {
          text: 'Call',
          onPress: () => Alert.alert('Opening Phone...'),
        },
        {
          text: 'Chat',
          onPress: () => Alert.alert('Opening Chat...'),
        },
        {
          text: 'Cancel',
          style: 'cancel',
        },
      ]
    );
  };

  const handleReturnRequest = () => {
    Alert.alert(
      'Return Request',
      'Do you want to initiate a return for this order?',
      [
        {
          text: 'Yes',
          onPress: () => Alert.alert('Return initiated', 'Seller has been notified'),
        },
        {
          text: 'No',
          style: 'cancel',
        },
      ]
    );
  };

  // Show loading state
  if (loading) {
    return (
      <View style={styles.loadingContainer}>
        <ActivityIndicator size="large" color={colors.primary} />
        <Text style={styles.loadingText}>Loading order details...</Text>
      </View>
    );
  }

  // Show error if no order found
  if (!order) {
    return (
      <View style={styles.errorContainer}>
        <Text style={styles.errorText}>Order not found</Text>
        <TouchableOpacity 
          style={styles.backButton}
          onPress={() => navigation.goBack()}
        >
          <Text style={styles.backButtonText}>Go Back</Text>
        </TouchableOpacity>
      </View>
    );
  }

  const currentStageIndex = trackingStages.findIndex(s => s.key === order.status);
  const displayStageIndex = Math.max(0, currentStageIndex);

  return (
    <View style={styles.container}>
      {/* Enhanced Header */}
      <View style={styles.header}>
        <TouchableOpacity onPress={() => navigation.goBack()} style={styles.backBtn}>
          <Text style={styles.backButtonHeader}>← Back</Text>
        </TouchableOpacity>
        <Text style={styles.title}>Order Tracking</Text>
        <View style={{ width: 50 }} />
      </View>

      <ScrollView style={styles.content} showsVerticalScrollIndicator={false}>
        {/* Order Reference Card - Enhanced */}
        <Animated.View 
          style={[
            styles.orderRefCard,
            {
              transform: [
                { scale: scaleAnim },
                { translateY: slideAnim }
              ]
            }
          ]}
        >
          <View style={styles.orderRefContent}>
            <View>
              <Text style={styles.orderRefLabel}>Order Reference</Text>
              <Text style={styles.orderRefNumber}>{order.orderRef}</Text>
              <Text style={styles.orderDate}>{formatDate(order.date)}</Text>
            </View>
            <View style={[styles.statusBadge, { backgroundColor: trackingStages[displayStageIndex].color }]}>
              <Text style={styles.statusText}>
                {trackingStages[displayStageIndex].label}
              </Text>
            </View>
          </View>
          
          {/* Progress Bar */}
          <View style={styles.progressContainer}>
            <View style={styles.progressBar}>
              <View 
                style={[
                  styles.progressFill,
                  { 
                    width: `${((currentStageIndex + 1) / trackingStages.length) * 100}%`,
                    backgroundColor: trackingStages[displayStageIndex].color
                  }
                ]}
              />
            </View>
            <Text style={styles.progressText}>
              {currentStageIndex + 1} of {trackingStages.length} steps
            </Text>
          </View>
        </Animated.View>

        {/* Status Summary Card */}
        <View style={styles.statusSummaryCard}>
          <View style={styles.statusSummaryItem}>
            <Text style={styles.statusSummaryIcon}>📦</Text>
            <Text style={styles.statusSummaryLabel}>Status</Text>
            <Text style={styles.statusSummaryValue}>{trackingStages[displayStageIndex].label}</Text>
          </View>
          <View style={styles.statusSummaryDivider} />
          <View style={styles.statusSummaryItem}>
            <Text style={styles.statusSummaryIcon}>💰</Text>
            <Text style={styles.statusSummaryLabel}>Total</Text>
            <Text style={styles.statusSummaryValue}>₱{(parseFloat(order.total) || 0).toFixed(2)}</Text>
          </View>
          <View style={styles.statusSummaryDivider} />
          <View style={styles.statusSummaryItem}>
            <Text style={styles.statusSummaryIcon}>✓</Text>
            <Text style={styles.statusSummaryLabel}>Payment</Text>
            <Text style={[styles.statusSummaryValue, { color: order.paymentStatus === 'paid' ? '#22c55e' : '#f59e0b' }]}>
              {order.paymentStatus === 'paid' ? 'Paid' : 'Pending'}
            </Text>
          </View>
        </View>

        {/* Tracking Timeline - Enhanced */}
        <View style={styles.trackingSection}>
          <Text style={styles.sectionTitle}>Order Timeline</Text>
          
          <View style={styles.timeline}>
            {trackingStages.map((stage, index) => {
              const isCompleted = index <= currentStageIndex;
              const isCurrent = index === currentStageIndex;

              return (
                <View key={stage.key} style={styles.timelineItem}>
                  {/* Timeline Line */}
                  {index < trackingStages.length - 1 && (
                    <View
                      style={[
                        styles.timelineLine,
                        isCompleted && styles.timelineLineCompleted,
                      ]}
                    />
                  )}

                  {/* Stage Indicator - Enhanced */}
                  <View
                    style={[
                      styles.stageIndicator,
                      isCompleted && styles.stageIndicatorCompleted,
                      isCurrent && styles.stageIndicatorCurrent,
                      { borderColor: stage.color, backgroundColor: isCompleted ? stage.color : colors.white },
                    ]}
                  >
                    <Text style={[styles.stageIcon, isCompleted && styles.stageIconCompleted]}>
                      {isCompleted ? stage.icon : index + 1}
                    </Text>
                  </View>

                  {/* Stage Info */}
                  <View style={[styles.stageInfo, isCurrent && styles.stageInfoCurrent]}>
                    <Text style={[styles.stageLabel, isCompleted && styles.stageLabelCompleted]}>
                      {stage.label}
                    </Text>
                    <Text style={styles.stageDescription}>{stage.description}</Text>
                  </View>
                </View>
              );
            })}
          </View>
        </View>

        {/* Items Summary - Enhanced */}
        <View style={styles.itemsSection}>
          <Text style={styles.sectionTitle}>Items ({order.items?.length || 0})</Text>
          
          {order.items && order.items.length > 0 ? (
            order.items.map((item, index) => (
              <View key={index} style={styles.itemCard}>
                <View style={styles.itemHeader}>
                  <View style={styles.itemNumberBadge}>
                    <Text style={styles.itemNumber}>{index + 1}</Text>
                  </View>
                  <View style={styles.itemDetails}>
                    <Text style={styles.itemName}>{item.name}</Text>
                    <Text style={styles.itemQuantity}>Qty: {item.quantity}</Text>
                  </View>
                  <Text style={styles.itemPrice}>₱{(parseFloat(item.price) || 0).toFixed(2)}</Text>
                </View>
              </View>
            ))
          ) : (
            <View style={styles.emptyCard}>
              <Text style={styles.emptyText}>No items in order</Text>
            </View>
          )}
        </View>

        {/* Order Summary - Enhanced */}
        <View style={styles.summaryCard}>
          <Text style={styles.summaryTitle}>Order Summary</Text>
          <View style={styles.summaryRow}>
            <Text style={styles.summaryLabel}>Subtotal</Text>
            <Text style={styles.summaryValue}>₱{(parseFloat(order.subtotal) || 0).toFixed(2)}</Text>
          </View>
          <View style={styles.summaryRow}>
            <Text style={styles.summaryLabel}>Shipping</Text>
            <Text style={styles.summaryValue}>₱{(parseFloat(order.shippingFee) || 0).toFixed(2)}</Text>
          </View>
          <View style={[styles.summaryRow, styles.summaryTotal]}>
            <Text style={styles.summaryTotalLabel}>Total</Text>
            <Text style={styles.summaryTotalValue}>₱{(parseFloat(order.total) || 0).toFixed(2)}</Text>
          </View>
        </View>

        {/* Shipping Address - Enhanced */}
        {order.shippingAddress && (
          <View style={styles.addressSection}>
            <Text style={styles.sectionTitle}>Delivery Address</Text>
            <View style={styles.addressCard}>
              <View style={styles.addressHeader}>
                <Text style={styles.addressIcon}>📍</Text>
                <Text style={styles.addressHeaderText}>Delivery Location</Text>
              </View>
              <Text style={styles.addressName}>{order.shippingAddress.fullName || 'N/A'}</Text>
              <Text style={styles.addressPhone}>{order.shippingAddress.phoneNumber || 'N/A'}</Text>
              <View style={styles.addressDivider} />
              <Text style={styles.addressText}>
                {order.shippingAddress.street || 'N/A'}
              </Text>
              <Text style={styles.addressText}>
                {order.shippingAddress.city || 'N/A'}, {order.shippingAddress.province || 'N/A'} {order.shippingAddress.postalCode || ''}
              </Text>
            </View>
          </View>
        )}

        {/* Courier Information - Enhanced */}
        {order.courier_name && (
          <View style={styles.courierSection}>
            <Text style={styles.sectionTitle}>Courier Information</Text>
            <View style={styles.courierCard}>
              <View style={styles.courierHeader}>
                <Text style={styles.courierIcon}>🚚</Text>
                <Text style={styles.courierName}>{order.courier_name}</Text>
              </View>
              {order.courier_contact && (
                <View style={styles.courierDetail}>
                  <Text style={styles.courierLabel}>Contact:</Text>
                  <Text style={styles.courierValue}>{order.courier_contact}</Text>
                </View>
              )}
              {order.courier_tracking_url && (
                <TouchableOpacity 
                  style={styles.courierTrackButton}
                  onPress={() => {
                    Alert.alert('Track on Courier', 'Opening courier tracking page...');
                  }}
                >
                  <Text style={styles.courierTrackButtonText}>Track on Courier Website →</Text>
                </TouchableOpacity>
              )}
            </View>
          </View>
        )}

        {/* Order Received Button - Enhanced */}
        {order.tracking_status === 'Out for Delivery' && order.status !== 'completed' && (
          <TouchableOpacity 
            style={styles.orderReceivedButton}
            onPress={() => {
              Alert.alert(
                'Order Received',
                'Confirm that you have received this order?',
                [
                  {
                    text: 'Yes, Confirm',
                    onPress: () => {
                      Alert.alert('Success', 'Thank you for confirming! You can now leave a review.');
                      navigation.navigate('Home');
                    },
                  },
                  {
                    text: 'Cancel',
                    style: 'cancel',
                  },
                ]
              );
            }}
          >
            <Text style={styles.orderReceivedIcon}>✓</Text>
            <Text style={styles.orderReceivedText}>Order Received - Leave Review</Text>
          </TouchableOpacity>
        )}

        {/* Action Buttons */}
        <View style={styles.actionsSection}>
          <TouchableOpacity style={styles.actionButton} onPress={handleContactSeller}>
            <Text style={styles.actionButtonIcon}>💬</Text>
            <Text style={styles.actionButtonText}>Contact Seller</Text>
          </TouchableOpacity>

          {currentStageIndex >= trackingStages.length - 1 && (
            <TouchableOpacity style={styles.actionButton} onPress={handleReturnRequest}>
              <Text style={styles.actionButtonIcon}>↩️</Text>
              <Text style={styles.actionButtonText}>Return/Exchange</Text>
            </TouchableOpacity>
          )}
        </View>

        {/* Help Section */}
        <TouchableOpacity style={styles.helpSection}>
          <Text style={styles.helpIcon}>❓</Text>
          <View style={styles.helpContent}>
            <Text style={styles.helpText}>Need help with your order?</Text>
            <Text style={styles.helpSubtext}>Contact our support team</Text>
          </View>
          <Text style={styles.helpArrow}>→</Text>
        </TouchableOpacity>

        <View style={{ height: 40 }} />
      </ScrollView>
    </View>
  );
};

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: colors.background,
  },
  loadingContainer: {
    flex: 1,
    justifyContent: 'center',
    alignItems: 'center',
    backgroundColor: colors.background,
  },
  loadingText: {
    marginTop: 10,
    fontSize: 14,
    color: colors.textLight,
  },
  errorContainer: {
    flex: 1,
    justifyContent: 'center',
    alignItems: 'center',
    backgroundColor: colors.background,
    padding: 20,
  },
  errorText: {
    fontSize: 16,
    color: colors.textLight,
    marginBottom: 20,
  },
  header: {
    backgroundColor: colors.primary,
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    paddingHorizontal: 15,
    paddingVertical: 15,
    paddingTop: 40,
  },
  backButtonHeader: {
    color: colors.white,
    fontSize: 16,
    fontWeight: '600',
  },
  title: {
    color: colors.white,
    fontSize: 18,
    fontWeight: 'bold',
  },
  content: {
    flex: 1,
    padding: 15,
  },
  orderRefCard: {
    backgroundColor: colors.white,
    borderRadius: 12,
    padding: 15,
    marginBottom: 20,
    borderWidth: 1,
    borderColor: colors.border,
  },
  orderRefLabel: {
    fontSize: 12,
    color: colors.textLight,
    marginBottom: 4,
  },
  orderRefNumber: {
    fontSize: 16,
    fontWeight: 'bold',
    color: colors.text,
    marginBottom: 4,
  },
  orderDate: {
    fontSize: 12,
    color: colors.textLight,
  },
  statusBadge: {
    paddingHorizontal: 12,
    paddingVertical: 8,
    borderRadius: 20,
  },
  statusText: {
    color: colors.white,
    fontSize: 12,
    fontWeight: '600',
  },
  trackingSection: {
    marginBottom: 25,
  },
  sectionTitle: {
    fontSize: 16,
    fontWeight: 'bold',
    color: colors.text,
    marginBottom: 15,
  },
  timeline: {
    paddingVertical: 10,
  },
  timelineItem: {
    flexDirection: 'row',
    marginBottom: 25,
    position: 'relative',
  },
  timelineLine: {
    position: 'absolute',
    left: 20,
    top: 50,
    width: 3,
    height: 60,
    backgroundColor: '#DDD',
  },
  timelineLineCompleted: {
    backgroundColor: colors.primary,
  },
  stageIndicator: {
    width: 40,
    height: 40,
    borderRadius: 20,
    borderWidth: 3,
    justifyContent: 'center',
    alignItems: 'center',
    marginRight: 15,
    backgroundColor: colors.white,
  },
  stageIndicatorCompleted: {
    backgroundColor: colors.primary,
  },
  stageIndicatorCurrent: {
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.25,
    shadowRadius: 3,
    elevation: 5,
  },
  stageIcon: {
    fontSize: 18,
    fontWeight: 'bold',
    color: colors.text,
  },
  stageIconCompleted: {
    color: colors.white,
  },
  stageInfo: {
    flex: 1,
    justifyContent: 'center',
  },
  stageLabel: {
    fontSize: 14,
    fontWeight: '600',
    color: colors.textLight,
    marginBottom: 4,
  },
  stageLabelCompleted: {
    color: colors.text,
  },
  stageDescription: {
    fontSize: 12,
    color: colors.textLight,
  },
  itemsSection: {
    marginBottom: 20,
  },
  itemCard: {
    backgroundColor: colors.white,
    borderRadius: 10,
    padding: 12,
    marginBottom: 10,
    borderWidth: 1,
    borderColor: colors.border,
  },
  itemHeader: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    marginBottom: 6,
  },
  itemName: {
    fontSize: 14,
    fontWeight: '600',
    color: colors.text,
    flex: 1,
  },
  itemPrice: {
    fontSize: 14,
    fontWeight: 'bold',
    color: colors.primary,
  },
  itemQuantity: {
    fontSize: 12,
    color: colors.textLight,
    marginBottom: 4,
  },
  itemTotal: {
    fontSize: 12,
    color: colors.text,
    fontWeight: '600',
  },
  emptyCard: {
    backgroundColor: colors.white,
    borderRadius: 10,
    padding: 20,
    alignItems: 'center',
    borderWidth: 1,
    borderColor: colors.border,
  },
  emptyText: {
    fontSize: 14,
    color: colors.textLight,
    marginBottom: 4,
  },
  emptySubtext: {
    fontSize: 12,
    color: colors.textLight,
  },
  summaryCard: {
    backgroundColor: colors.white,
    borderRadius: 12,
    padding: 15,
    marginBottom: 20,
    borderWidth: 1,
    borderColor: colors.border,
  },
  summaryRow: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    paddingVertical: 10,
    borderBottomWidth: 1,
    borderBottomColor: colors.border,
  },
  summaryTotal: {
    borderBottomWidth: 0,
    marginTop: 5,
    paddingVertical: 12,
  },
  summaryLabel: {
    fontSize: 14,
    color: colors.text,
  },
  summaryValue: {
    fontSize: 14,
    fontWeight: '600',
    color: colors.text,
  },
  summaryTotalLabel: {
    fontSize: 16,
    fontWeight: 'bold',
    color: colors.text,
  },
  summaryTotalValue: {
    fontSize: 16,
    fontWeight: 'bold',
    color: colors.primary,
  },
  addressSection: {
    marginBottom: 20,
  },
  addressCard: {
    backgroundColor: colors.white,
    borderRadius: 12,
    padding: 15,
    borderWidth: 1,
    borderColor: colors.border,
  },
  addressLabel: {
    fontSize: 14,
    fontWeight: '600',
    color: colors.text,
    marginBottom: 8,
  },
  addressName: {
    fontSize: 14,
    fontWeight: '600',
    color: colors.text,
    marginBottom: 4,
  },
  addressText: {
    fontSize: 13,
    color: colors.text,
    lineHeight: 20,
    marginBottom: 4,
  },
  actionsSection: {
    flexDirection: 'row',
    gap: 10,
    marginBottom: 20,
  },
  actionButton: {
    flex: 1,
    backgroundColor: colors.white,
    borderRadius: 10,
    paddingVertical: 12,
    alignItems: 'center',
    borderWidth: 1,
    borderColor: colors.border,
  },
  actionButtonIcon: {
    fontSize: 24,
    marginBottom: 4,
  },
  actionButtonText: {
    fontSize: 12,
    fontWeight: '600',
    color: colors.text,
  },
  helpSection: {
    backgroundColor: colors.white,
    borderRadius: 12,
    padding: 15,
    flexDirection: 'row',
    alignItems: 'center',
    borderWidth: 1,
    borderColor: colors.border,
    marginBottom: 20,
  },
  helpIcon: {
    fontSize: 24,
    marginRight: 12,
  },
  helpText: {
    fontSize: 14,
    fontWeight: '600',
    color: colors.text,
    flex: 1,
  },
  helpArrow: {
    fontSize: 18,
    color: colors.primary,
  },
  backButton: {
    backgroundColor: colors.primary,
    paddingHorizontal: 20,
    paddingVertical: 12,
    borderRadius: 8,
  },
  backButtonText: {
    color: colors.white,
    fontSize: 14,
    fontWeight: '600',
  },
  courierSection: {
    marginBottom: 20,
  },
  courierCard: {
    backgroundColor: colors.white,
    borderRadius: 12,
    padding: 15,
    borderWidth: 1,
    borderColor: colors.border,
  },
  courierRow: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    paddingVertical: 10,
    borderBottomWidth: 1,
    borderBottomColor: colors.border,
  },
  courierLabel: {
    fontSize: 14,
    color: colors.textLight,
  },
  courierValue: {
    fontSize: 14,
    fontWeight: '600',
    color: colors.text,
  },
  courierTrackButton: {
    marginTop: 12,
    paddingVertical: 12,
    paddingHorizontal: 15,
    backgroundColor: colors.primary,
    borderRadius: 8,
    alignItems: 'center',
  },
  courierTrackButtonText: {
    color: colors.white,
    fontSize: 14,
    fontWeight: '600',
  },
  orderReceivedButton: {
    backgroundColor: '#22c55e',
    borderRadius: 12,
    padding: 15,
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'center',
    marginBottom: 20,
  },
  orderReceivedIcon: {
    fontSize: 24,
    color: colors.white,
    marginRight: 10,
  },
  orderReceivedText: {
    color: colors.white,
    fontSize: 14,
    fontWeight: '600',
  },
  // Missing styles for enhanced UI
  backBtn: {
    padding: 5,
  },
  orderRefContent: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    width: '100%',
  },
  progressContainer: {
    marginTop: 15,
    paddingTop: 15,
    borderTopWidth: 1,
    borderTopColor: colors.border,
  },
  progressBar: {
    height: 6,
    backgroundColor: '#E5E7EB',
    borderRadius: 3,
    overflow: 'hidden',
  },
  progressFill: {
    height: '100%',
    borderRadius: 3,
  },
  progressText: {
    fontSize: 12,
    color: colors.textLight,
    marginTop: 8,
    textAlign: 'center',
  },
  statusSummaryCard: {
    backgroundColor: colors.white,
    borderRadius: 12,
    padding: 15,
    marginBottom: 20,
    flexDirection: 'row',
    justifyContent: 'space-around',
    alignItems: 'center',
    borderWidth: 1,
    borderColor: colors.border,
  },
  statusSummaryItem: {
    alignItems: 'center',
    flex: 1,
  },
  statusSummaryIcon: {
    fontSize: 24,
    marginBottom: 8,
  },
  statusSummaryLabel: {
    fontSize: 12,
    color: colors.textLight,
    marginBottom: 4,
  },
  statusSummaryValue: {
    fontSize: 14,
    fontWeight: '600',
    color: colors.text,
  },
  statusSummaryDivider: {
    width: 1,
    height: 50,
    backgroundColor: colors.border,
  },
  stageInfoCurrent: {
    backgroundColor: '#F0F9FF',
    padding: 10,
    borderRadius: 8,
    marginLeft: -10,
    paddingLeft: 10,
  },
  itemNumberBadge: {
    width: 28,
    height: 28,
    borderRadius: 14,
    backgroundColor: colors.primary,
    justifyContent: 'center',
    alignItems: 'center',
    marginRight: 12,
  },
  itemNumber: {
    color: colors.white,
    fontSize: 12,
    fontWeight: 'bold',
  },
  itemDetails: {
    flex: 1,
  },
  summaryTitle: {
    fontSize: 16,
    fontWeight: 'bold',
    color: colors.text,
    marginBottom: 10,
  },
  addressHeader: {
    flexDirection: 'row',
    alignItems: 'center',
    marginBottom: 12,
  },
  addressIcon: {
    fontSize: 20,
    marginRight: 8,
  },
  addressHeaderText: {
    fontSize: 14,
    fontWeight: '600',
    color: colors.text,
  },
  addressPhone: {
    fontSize: 13,
    color: colors.textLight,
    marginBottom: 8,
  },
  addressDivider: {
    height: 1,
    backgroundColor: colors.border,
    marginVertical: 10,
  },
  courierHeader: {
    flexDirection: 'row',
    alignItems: 'center',
    marginBottom: 12,
  },
  courierIcon: {
    fontSize: 24,
    marginRight: 10,
  },
  courierName: {
    fontSize: 16,
    fontWeight: '600',
    color: colors.text,
  },
  courierDetail: {
    flexDirection: 'row',
    alignItems: 'center',
    marginBottom: 8,
  },
  helpContent: {
    flex: 1,
  },
  helpSubtext: {
    fontSize: 12,
    color: colors.textLight,
    marginTop: 2,
  },
});

export default OrderDetailsScreen;