import React from 'react';
import {
  View,
  Text,
  StyleSheet,
  ScrollView,
  TouchableOpacity,
  Linking,
} from 'react-native';

const { width } = require('react-native').Dimensions.get('window');

const CustomOrderScreen = ({ navigation }) => {
  const handleOpenWebsite = () => {
    Linking.openURL('https://yakancustomorder.com').catch(err =>
      console.error('Failed to open URL:', err)
    );
  };

  return (
    <ScrollView style={styles.container}>
      {/* Header */}
      <View style={styles.header}>
        <TouchableOpacity onPress={() => navigation.goBack()}>
          <Text style={styles.backButton}>← Back</Text>
        </TouchableOpacity>
        <Text style={styles.headerTitle}>Create Custom Order</Text>
      </View>

      {/* Teaser Content */}
      <View style={styles.teaserContainer}>
        <View style={styles.teaserCard}>
          <Text style={styles.teaserIcon}>✨</Text>
          <Text style={styles.teaserTitle}>Ready to create your custom Yakan piece?</Text>
          <Text style={styles.teaserSubtitle}>Design your unique traditional weave with our custom order service</Text>
          
          <TouchableOpacity style={styles.linkButton} onPress={handleOpenWebsite}>
            <Text style={styles.linkButtonText}>Click here to proceed!</Text>
            <Text style={styles.linkIcon}>→</Text>
          </TouchableOpacity>

          <View style={styles.websiteBox}>
            <Text style={styles.websiteLabel}>Visit our custom order portal:</Text>
            <TouchableOpacity onPress={handleOpenWebsite}>
              <Text style={styles.websiteLink}>https://yakancustomorder.com</Text>
            </TouchableOpacity>
          </View>

          <View style={styles.featuresContainer}>
            <Text style={styles.featuresTitle}>What you can customize:</Text>
            <View style={styles.featureItem}>
              <Text style={styles.featureBullet}>•</Text>
              <Text style={styles.featureText}>Choose from traditional Yakan patterns</Text>
            </View>
            <View style={styles.featureItem}>
              <Text style={styles.featureBullet}>•</Text>
              <Text style={styles.featureText}>Select your preferred colors</Text>
            </View>
            <View style={styles.featureItem}>
              <Text style={styles.featureBullet}>•</Text>
              <Text style={styles.featureText}>Specify custom sizes and dimensions</Text>
            </View>
            <View style={styles.featureItem}>
              <Text style={styles.featureBullet}>•</Text>
              <Text style={styles.featureText}>Upload your own design inspiration</Text>
            </View>
          </View>
        </View>
      </View>

      <View style={styles.bottomSpace} />
    </ScrollView>
  );
};

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: '#f5f5f5',
  },
  header: {
    backgroundColor: '#8B1A1A',
    padding: 20,
    paddingTop: 50,
  },
  backButton: {
    color: '#fff',
    fontSize: 16,
    marginBottom: 10,
  },
  headerTitle: {
    color: '#fff',
    fontSize: 24,
    fontWeight: 'bold',
  },
  teaserContainer: {
    flex: 1,
    padding: 20,
    justifyContent: 'center',
  },
  teaserCard: {
    backgroundColor: '#fff',
    borderRadius: 16,
    padding: 30,
    alignItems: 'center',
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.1,
    shadowRadius: 8,
    elevation: 5,
  },
  teaserIcon: {
    fontSize: 64,
    marginBottom: 20,
  },
  teaserTitle: {
    fontSize: 22,
    fontWeight: 'bold',
    color: '#8B1A1A',
    textAlign: 'center',
    marginBottom: 12,
  },
  teaserSubtitle: {
    fontSize: 15,
    color: '#666',
    textAlign: 'center',
    marginBottom: 30,
    lineHeight: 22,
  },
  linkButton: {
    backgroundColor: '#8B1A1A',
    paddingVertical: 16,
    paddingHorizontal: 32,
    borderRadius: 8,
    flexDirection: 'row',
    alignItems: 'center',
    gap: 8,
    marginBottom: 25,
    shadowColor: '#8B1A1A',
    shadowOffset: { width: 0, height: 4 },
    shadowOpacity: 0.3,
    shadowRadius: 8,
    elevation: 6,
  },
  linkButtonText: {
    color: '#fff',
    fontSize: 16,
    fontWeight: 'bold',
  },
  linkIcon: {
    color: '#fff',
    fontSize: 20,
    fontWeight: 'bold',
  },
  websiteBox: {
    backgroundColor: '#f9f9f9',
    padding: 20,
    borderRadius: 8,
    borderWidth: 1,
    borderColor: '#e0e0e0',
    width: '100%',
    marginBottom: 25,
  },
  websiteLabel: {
    fontSize: 13,
    color: '#666',
    marginBottom: 8,
    textAlign: 'center',
  },
  websiteLink: {
    fontSize: 15,
    color: '#1E90FF',
    fontWeight: '600',
    textAlign: 'center',
    textDecorationLine: 'underline',
  },
  featuresContainer: {
    width: '100%',
    backgroundColor: '#fff5f5',
    padding: 20,
    borderRadius: 8,
    borderLeftWidth: 4,
    borderLeftColor: '#8B1A1A',
  },
  featuresTitle: {
    fontSize: 16,
    fontWeight: 'bold',
    color: '#333',
    marginBottom: 15,
  },
  featureItem: {
    flexDirection: 'row',
    alignItems: 'flex-start',
    marginBottom: 10,
  },
  featureBullet: {
    fontSize: 16,
    color: '#8B1A1A',
    marginRight: 8,
    fontWeight: 'bold',
  },
  featureText: {
    fontSize: 14,
    color: '#555',
    flex: 1,
    lineHeight: 20,
  },
  bottomSpace: {
    height: 30,
  },
});

export default CustomOrderScreen;