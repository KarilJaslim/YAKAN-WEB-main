import { useState, useCallback } from 'react';
import ApiService from '../services/api';

export const useShippingFee = () => {
  const [shippingRate, setShippingRate] = useState(null);
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState(null);

  /**
   * Fetch active shipping rate
   */
  const fetchShippingRate = useCallback(async () => {
    try {
      setLoading(true);
      setError(null);
      const response = await ApiService.getShippingRate();
      
      if (response?.success) {
        setShippingRate(response.data);
        return response.data;
      } else {
        setError(response?.message || 'Failed to fetch shipping rate');
        return null;
      }
    } catch (err) {
      const errorMsg = err?.message || 'Error fetching shipping rate';
      setError(errorMsg);
      console.error('[useShippingFee] Error:', errorMsg);
      return null;
    } finally {
      setLoading(false);
    }
  }, []);

  /**
   * Calculate shipping fee based on distance or coordinates
   */
  const calculateFee = useCallback(async (distanceKm, latitude, longitude) => {
    try {
      setLoading(true);
      setError(null);
      
      if (!distanceKm && (!latitude || !longitude)) {
        setError('Invalid distance or coordinates');
        return null;
      }

      const payload = {};
      if (distanceKm) {
        payload.distance_km = distanceKm;
      }
      if (latitude && longitude) {
        payload.latitude = latitude;
        payload.longitude = longitude;
      }

      const response = await ApiService.calculateShippingFee(payload);
      
      if (response?.success) {
        return response.data;
      } else {
        setError(response?.message || 'Failed to calculate shipping fee');
        return null;
      }
    } catch (err) {
      const errorMsg = err?.message || 'Error calculating shipping fee';
      setError(errorMsg);
      console.error('[useShippingFee] Error:', errorMsg);
      return null;
    } finally {
      setLoading(false);
    }
  }, []);

  return {
    shippingRate,
    loading,
    error,
    fetchShippingRate,
    calculateFee,
  };
};

export default useShippingFee;
