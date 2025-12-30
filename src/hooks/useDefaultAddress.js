import { useState, useEffect } from 'react';
import AsyncStorage from '@react-native-async-storage/async-storage';

/**
 * Hook to manage user's default delivery address
 * Automatically loads, saves, and manages the default address
 */
export const useDefaultAddress = () => {
  const [defaultAddress, setDefaultAddress] = useState(null);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);

  // Load default address on mount
  useEffect(() => {
    loadDefaultAddress();
  }, []);

  /**
   * Load default address from storage
   */
  const loadDefaultAddress = async () => {
    try {
      setLoading(true);
      const addresses = await AsyncStorage.getItem('savedAddresses');
      
      if (addresses) {
        const parsedAddresses = JSON.parse(addresses);
        const defaultAddr = parsedAddresses.find(addr => addr.isDefault);
        
        if (defaultAddr) {
          setDefaultAddress(defaultAddr);
        } else if (parsedAddresses.length > 0) {
          // If no default, set first as default
          parsedAddresses[0].isDefault = true;
          await AsyncStorage.setItem('savedAddresses', JSON.stringify(parsedAddresses));
          setDefaultAddress(parsedAddresses[0]);
        }
      }
      setError(null);
    } catch (err) {
      console.error('Error loading default address:', err);
      setError(err.message);
    } finally {
      setLoading(false);
    }
  };

  /**
   * Set a specific address as default
   */
  const setAsDefault = async (addressId) => {
    try {
      const addresses = await AsyncStorage.getItem('savedAddresses');
      if (addresses) {
        const parsedAddresses = JSON.parse(addresses);
        const updatedAddresses = parsedAddresses.map(addr =>
          addr.id === addressId 
            ? { ...addr, isDefault: true }
            : { ...addr, isDefault: false }
        );
        
        await AsyncStorage.setItem('savedAddresses', JSON.stringify(updatedAddresses));
        
        const newDefault = updatedAddresses.find(addr => addr.id === addressId);
        setDefaultAddress(newDefault);
        
        return true;
      }
      return false;
    } catch (err) {
      console.error('Error setting default address:', err);
      setError(err.message);
      return false;
    }
  };

  /**
   * Update default address
   */
  const updateDefaultAddress = async (updatedAddress) => {
    try {
      const addresses = await AsyncStorage.getItem('savedAddresses');
      if (addresses) {
        const parsedAddresses = JSON.parse(addresses);
        const updatedAddresses = parsedAddresses.map(addr =>
          addr.id === updatedAddress.id ? updatedAddress : addr
        );
        
        await AsyncStorage.setItem('savedAddresses', JSON.stringify(updatedAddresses));
        setDefaultAddress(updatedAddress);
        
        return true;
      }
      return false;
    } catch (err) {
      console.error('Error updating default address:', err);
      setError(err.message);
      return false;
    }
  };

  /**
   * Create a new default address
   */
  const createDefaultAddress = async (newAddress) => {
    try {
      const addresses = await AsyncStorage.getItem('savedAddresses');
      const existingAddresses = addresses ? JSON.parse(addresses) : [];
      
      // Set all existing as non-default
      const updatedExisting = existingAddresses.map(addr => ({
        ...addr,
        isDefault: false
      }));
      
      // Create new address with isDefault = true
      const addressWithId = {
        ...newAddress,
        id: Date.now().toString(),
        isDefault: true
      };
      
      const allAddresses = [...updatedExisting, addressWithId];
      await AsyncStorage.setItem('savedAddresses', JSON.stringify(allAddresses));
      setDefaultAddress(addressWithId);
      
      return addressWithId;
    } catch (err) {
      console.error('Error creating default address:', err);
      setError(err.message);
      return null;
    }
  };

  /**
   * Get formatted address string
   */
  const getFormattedAddress = () => {
    if (!defaultAddress) return '';
    
    const parts = [
      defaultAddress.street,
      defaultAddress.barangay,
      defaultAddress.city,
      defaultAddress.province,
      defaultAddress.postalCode
    ].filter(Boolean);
    
    return parts.join(', ');
  };

  return {
    defaultAddress,
    loading,
    error,
    loadDefaultAddress,
    setAsDefault,
    updateDefaultAddress,
    createDefaultAddress,
    getFormattedAddress,
    hasDefaultAddress: !!defaultAddress,
  };
};

export default useDefaultAddress;
