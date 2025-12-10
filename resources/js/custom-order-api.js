class CustomOrderAPI {
    constructor(baseURL = '/api/v1') {
        this.baseURL = baseURL;
        this.token = localStorage.getItem('auth_token');
    }

    // Helper method to make API requests
    async request(endpoint, options = {}) {
        const url = `${this.baseURL}${endpoint}`;
        const config = {
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                ...(this.token && { 'Authorization': `Bearer ${this.token}` }),
                ...options.headers
            },
            ...options
        };

        try {
            const response = await fetch(url, config);
            const data = await response.json();

            if (!response.ok) {
                throw new Error(data.message || 'API request failed');
            }

            return data;
        } catch (error) {
            console.error('API Error:', error);
            throw error;
        }
    }

    // Get catalog (categories and products)
    async getCatalog() {
        return this.request('/custom-orders/catalog');
    }

    // Get pricing estimate
    async getPricingEstimate(orderData) {
        return this.request('/custom-orders/pricing-estimate', {
            method: 'POST',
            body: JSON.stringify(orderData)
        });
    }

    // Upload design file
    async uploadDesign(file) {
        const formData = new FormData();
        formData.append('file', file);

        return this.request('/custom-orders/upload-design', {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                ...(this.token && { 'Authorization': `Bearer ${this.token}` })
            },
            body: formData
        });
    }

    // Get user's custom orders
    async getCustomOrders(page = 1, perPage = 10) {
        return this.request(`/custom-orders?page=${page}&per_page=${perPage}`);
    }

    // Get specific custom order
    async getCustomOrder(id) {
        return this.request(`/custom-orders/${id}`);
    }

    // Create new custom order
    async createCustomOrder(orderData) {
        return this.request('/custom-orders', {
            method: 'POST',
            body: JSON.stringify(orderData)
        });
    }

    // Update custom order
    async updateCustomOrder(id, orderData) {
        return this.request(`/custom-orders/${id}`, {
            method: 'PUT',
            body: JSON.stringify(orderData)
        });
    }

    // Cancel custom order
    async cancelCustomOrder(id) {
        return this.request(`/custom-orders/${id}/cancel`, {
            method: 'POST'
        });
    }

    // Get custom order statistics
    async getStatistics() {
        return this.request('/custom-orders/statistics');
    }
}

// Custom Order Form Handler
class CustomOrderForm {
    constructor(api) {
        this.api = api;
        this.form = null;
        this.currentStep = 1;
        this.totalSteps = 4;
        this.orderData = {};
        this.designFiles = [];
    }

    initialize(formSelector) {
        this.form = document.querySelector(formSelector);
        if (!this.form) return;

        this.setupEventListeners();
        this.loadCatalog();
        this.updateStepIndicator();
    }

    setupEventListeners() {
        // Form navigation
        document.querySelectorAll('[data-step-action]').forEach(button => {
            button.addEventListener('click', (e) => {
                const action = e.target.dataset.stepAction;
                this.handleStepAction(action);
            });
        });

        // File upload
        const designUpload = document.querySelector('#design_upload');
        if (designUpload) {
            designUpload.addEventListener('change', (e) => this.handleFileUpload(e));
        }

        // Pricing estimate
        const pricingForm = document.querySelector('#pricing-form');
        if (pricingForm) {
            pricingForm.addEventListener('submit', (e) => this.handlePricingEstimate(e));
        }

        // Main form submission
        if (this.form) {
            this.form.addEventListener('submit', (e) => this.handleFormSubmit(e));
        }
    }

    async loadCatalog() {
        try {
            const response = await this.api.getCatalog();
            this.populateCategorySelect(response.data);
        } catch (error) {
            console.error('Failed to load catalog:', error);
            this.showError('Failed to load product catalog');
        }
    }

    populateCategorySelect(categories) {
        const categorySelect = document.querySelector('#category_id');
        if (!categorySelect) return;

        categorySelect.innerHTML = '<option value="">Select a category</option>';
        
        categories.forEach(category => {
            const option = document.createElement('option');
            option.value = category.id;
            option.textContent = category.name;
            categorySelect.appendChild(option);
        });

        categorySelect.addEventListener('change', (e) => {
            this.populateProductSelect(categories, e.target.value);
        });
    }

    populateProductSelect(categories, categoryId) {
        const productSelect = document.querySelector('#product_id');
        if (!productSelect) return;

        const category = categories.find(c => c.id == categoryId);
        if (!category || !category.products) return;

        productSelect.innerHTML = '<option value="">Select a product</option>';
        
        category.products.forEach(product => {
            const option = document.createElement('option');
            option.value = product.id;
            option.textContent = product.name;
            productSelect.appendChild(option);
        });
    }

    handleStepAction(action) {
        switch (action) {
            case 'next':
                if (this.validateCurrentStep()) {
                    this.saveCurrentStepData();
                    this.currentStep++;
                    this.showStep(this.currentStep);
                    this.updateStepIndicator();
                }
                break;
            case 'prev':
                this.currentStep--;
                this.showStep(this.currentStep);
                this.updateStepIndicator();
                break;
            case 'submit':
                this.handleFormSubmit();
                break;
        }
    }

    validateCurrentStep() {
        const stepElement = document.querySelector(`[data-step="${this.currentStep}"]`);
        const requiredFields = stepElement.querySelectorAll('[required]');
        let isValid = true;

        requiredFields.forEach(field => {
            if (!field.value.trim()) {
                field.classList.add('border-red-500');
                isValid = false;
            } else {
                field.classList.remove('border-red-500');
            }
        });

        if (!isValid) {
            this.showError('Please fill in all required fields');
        }

        return isValid;
    }

    saveCurrentStepData() {
        const stepElement = document.querySelector(`[data-step="${this.currentStep}"]`);
        const inputs = stepElement.querySelectorAll('input, select, textarea');

        inputs.forEach(input => {
            if (input.type === 'checkbox') {
                this.orderData[input.name] = input.checked;
            } else if (input.type === 'file') {
                // Handle file separately
            } else {
                this.orderData[input.name] = input.value;
            }
        });
    }

    showStep(stepNumber) {
        // Hide all steps
        document.querySelectorAll('[data-step]').forEach(step => {
            step.classList.add('hidden');
        });

        // Show current step
        const currentStepElement = document.querySelector(`[data-step="${stepNumber}"]`);
        if (currentStepElement) {
            currentStepElement.classList.remove('hidden');
        }

        // Update navigation buttons
        this.updateNavigationButtons();
    }

    updateStepIndicator() {
        document.querySelectorAll('.step-indicator').forEach((indicator, index) => {
            const stepNumber = index + 1;
            
            if (stepNumber < this.currentStep) {
                indicator.classList.add('completed');
                indicator.classList.remove('active');
            } else if (stepNumber === this.currentStep) {
                indicator.classList.add('active');
                indicator.classList.remove('completed');
            } else {
                indicator.classList.remove('active', 'completed');
            }
        });
    }

    updateNavigationButtons() {
        const prevButton = document.querySelector('[data-step-action="prev"]');
        const nextButton = document.querySelector('[data-step-action="next"]');
        const submitButton = document.querySelector('[data-step-action="submit"]');

        if (prevButton) {
            prevButton.style.display = this.currentStep === 1 ? 'none' : 'inline-block';
        }

        if (nextButton) {
            nextButton.style.display = this.currentStep === this.totalSteps ? 'none' : 'inline-block';
        }

        if (submitButton) {
            submitButton.style.display = this.currentStep === this.totalSteps ? 'inline-block' : 'none';
        }
    }

    async handleFileUpload(event) {
        const file = event.target.files[0];
        if (!file) return;

        try {
            this.showLoading('Uploading design...');
            const response = await this.api.uploadDesign(file);
            this.designFiles.push(response.data);
            this.showSuccess('Design uploaded successfully');
            this.updateFileList();
        } catch (error) {
            this.showError('Failed to upload design: ' + error.message);
        } finally {
            this.hideLoading();
        }
    }

    async handlePricingEstimate(event) {
        event.preventDefault();
        
        const formData = new FormData(event.target);
        const orderData = Object.fromEntries(formData.entries());

        try {
            this.showLoading('Calculating estimate...');
            const response = await this.api.getPricingEstimate(orderData);
            this.displayPricingEstimate(response.data);
        } catch (error) {
            this.showError('Failed to calculate estimate: ' + error.message);
        } finally {
            this.hideLoading();
        }
    }

    displayPricingEstimate(data) {
        const estimateContainer = document.querySelector('#pricing-estimate');
        if (!estimateContainer) return;

        estimateContainer.innerHTML = `
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <h4 class="font-semibold text-blue-900 mb-2">Estimated Price: ₱${data.estimated_price.toLocaleString()}</h4>
                <div class="text-sm text-blue-700">
                    <p>Base Price: ₱${data.price_breakdown.base_price.toLocaleString()}</p>
                    <p>Labor Cost: ₱${data.price_breakdown.labor_cost.toLocaleString()}</p>
                    <p>Materials Cost: ₱${data.price_breakdown.materials_cost.toLocaleString()}</p>
                    <p>Estimated Production Time: ${data.production_time} days</p>
                    <p class="mt-2 italic">${data.notes}</p>
                </div>
            </div>
        `;
    }

    async handleFormSubmit(event) {
        if (event) {
            event.preventDefault();
        }

        // Save final step data
        this.saveCurrentStepData();

        // Add uploaded files to order data
        if (this.designFiles.length > 0) {
            this.orderData.design_files = this.designFiles;
        }

        try {
            this.showLoading('Submitting custom order...');
            const response = await this.api.createCustomOrder(this.orderData);
            
            this.showSuccess('Custom order submitted successfully!');
            
            // Redirect to order details page
            setTimeout(() => {
                window.location.href = `/custom-orders/${response.data.id}`;
            }, 2000);
            
        } catch (error) {
            this.showError('Failed to submit custom order: ' + error.message);
        } finally {
            this.hideLoading();
        }
    }

    updateFileList() {
        const fileList = document.querySelector('#uploaded-files');
        if (!fileList) return;

        fileList.innerHTML = this.designFiles.map((file, index) => `
            <div class="flex items-center justify-between p-2 bg-gray-50 rounded">
                <span class="text-sm">${file.filename}</span>
                <button type="button" onclick="customOrderForm.removeFile(${index})" 
                        class="text-red-500 hover:text-red-700">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        `).join('');
    }

    removeFile(index) {
        this.designFiles.splice(index, 1);
        this.updateFileList();
    }

    showLoading(message = 'Loading...') {
        const loader = document.querySelector('#loader');
        if (loader) {
            loader.textContent = message;
            loader.style.display = 'block';
        }
    }

    hideLoading() {
        const loader = document.querySelector('#loader');
        if (loader) {
            loader.style.display = 'none';
        }
    }

    showError(message) {
        this.showNotification(message, 'error');
    }

    showSuccess(message) {
        this.showNotification(message, 'success');
    }

    showNotification(message, type = 'info') {
        const notification = document.createElement('div');
        notification.className = `fixed top-4 right-4 p-4 rounded-lg shadow-lg z-50 ${
            type === 'error' ? 'bg-red-500 text-white' :
            type === 'success' ? 'bg-green-500 text-white' :
            'bg-blue-500 text-white'
        }`;
        notification.textContent = message;
        
        document.body.appendChild(notification);
        
        setTimeout(() => {
            notification.remove();
        }, 5000);
    }
}

// Initialize the custom order form
const customOrderAPI = new CustomOrderAPI();
const customOrderForm = new CustomOrderForm(customOrderAPI);

// Auto-initialize when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    customOrderForm.initialize('#custom-order-form');
});

// Export for use in other scripts
window.CustomOrderAPI = CustomOrderAPI;
window.CustomOrderForm = CustomOrderForm;
window.customOrderAPI = customOrderAPI;
window.customOrderForm = customOrderForm;
