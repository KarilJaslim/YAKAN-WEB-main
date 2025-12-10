// Simplified Yakan Designer for Wizard
class YakanDesigner {
    constructor(canvasId, options = {}) {
        this.canvas = new fabric.Canvas(canvasId, {
            width: options.canvasWidth || 800,
            height: options.canvasHeight || 600,
            backgroundColor: '#f9fafb',
            selection: true,
            preserveObjectStacking: true
        });

        this.patterns = new Map();
        this.selectedColor = '#8B4513';
        this.currentTool = 'select';
        this.history = [];
        this.historyIndex = -1;
        this.maxHistory = 50;

        this.init();
    }

    init() {
        this.setupPatterns();
        this.setupCanvasEvents();
        this.saveState();
    }

    setupPatterns() {
        // Define pattern types
        this.patterns.set('sussuh', {
            name: 'Sussuh',
            type: 'polygon',
            points: [[0, -20], [10, -10], [20, 0], [10, 10], [0, 20], [-10, 10], [-20, 0], [-10, -10]],
            defaultColor: '#8B4513'
        });

        this.patterns.set('banga', {
            name: 'Banga',
            type: 'circle',
            radius: 15,
            petals: 6,
            defaultColor: '#D2691E'
        });

        this.patterns.set('kabkab', {
            name: 'Kabkab',
            type: 'star',
            points: 5,
            outerRadius: 20,
            innerRadius: 10,
            defaultColor: '#A0522D'
        });

        this.patterns.set('sinag', {
            name: 'Sinag',
            type: 'sun',
            radius: 15,
            rays: 8,
            defaultColor: '#FFD700'
        });

        this.patterns.set('alon', {
            name: 'Alon',
            type: 'wave',
            width: 40,
            height: 10,
            defaultColor: '#4682B4'
        });

        this.patterns.set('dalisay', {
            name: 'Dalisay',
            type: 'flower',
            petals: 8,
            radius: 12,
            defaultColor: '#FF69B4'
        });

        // New pattern types
        this.patterns.set('lakbay', {
            name: 'Lakbay',
            type: 'arrow',
            width: 30,
            height: 20,
            defaultColor: '#9333EA'
        });

        this.patterns.set('sining', {
            name: 'Sining',
            type: 'diamond',
            width: 25,
            height: 25,
            defaultColor: '#10B981'
        });

        this.patterns.set('kalikasan', {
            name: 'Kalikasan',
            type: 'leaf',
            width: 20,
            height: 30,
            defaultColor: '#059669'
        });

        this.patterns.set('paruparo', {
            name: 'Paru-Paro',
            type: 'butterfly',
            width: 35,
            height: 25,
            defaultColor: '#EC4899'
        });

        this.patterns.set('bituin', {
            name: 'Bituin',
            type: 'star',
            points: 8,
            outerRadius: 18,
            innerRadius: 8,
            defaultColor: '#F59E0B'
        });

        this.patterns.set('dagat', {
            name: 'Dagat',
            type: 'wave',
            width: 45,
            height: 12,
            defaultColor: '#0891B2'
        });
    }

    setupCanvasEvents() {
        this.canvas.on('mouse:down', (options) => {
            if (this.currentTool === 'pattern') {
                const pointer = this.canvas.getPointer(options.e);
                
                // Check if clicking on a pattern area
                if (options.target && options.target.data && options.target.data.isPatternArea) {
                    this.addPatternToArea(options.target, pointer.x, pointer.y);
                } else if (!options.target) {
                    // Check if clicking within any pattern area
                    const patternArea = this.findPatternAreaAt(pointer.x, pointer.y);
                    if (patternArea) {
                        this.addPatternToArea(patternArea, pointer.x, pointer.y);
                    } else {
                        // Show hint that user should click on the product
                        this.showNotification('Click on the product shape to add patterns', 'warning');
                    }
                }
            }
        });

        this.canvas.on('object:modified', () => {
            this.saveState();
        });

        this.canvas.on('object:added', () => {
            this.saveState();
        });
    }

    findPatternAreaAt(x, y) {
        const objects = this.canvas.getObjects();
        for (let obj of objects) {
            if (obj.data && obj.data.isPatternArea) {
                const bounds = obj.getBoundingRect();
                if (x >= bounds.left && x <= bounds.left + bounds.width &&
                    y >= bounds.top && y <= bounds.top + bounds.height) {
                    return obj;
                }
            }
        }
        return null;
    }

    addPatternToArea(patternArea, x, y) {
        // Get the pattern type based on selected color/tool
        const patternType = this.getPatternTypeFromColor();
        const patternObject = this.createPatternObject(patternType, x, y);
        
        if (patternObject) {
            // Add metadata about which area this pattern belongs to
            patternObject.data = {
                ...patternObject.data,
                patternArea: patternArea.data.areaName,
                productType: patternArea.data.productType
            };
            
            this.canvas.add(patternObject);
            this.canvas.setActiveObject(patternObject);
            this.showNotification(`Pattern added to ${patternArea.data.areaName}`, 'success');
        }
    }

    getPatternTypeFromColor() {
        // Map selected colors to pattern types
        const colorPatternMap = {
            '#8B4513': 'sussuh',
            '#D2691E': 'banga', 
            '#A0522D': 'kabkab',
            '#FFD700': 'sinag',
            '#4682B4': 'alon',
            '#FF69B4': 'dalisay',
            '#9333EA': 'sussuh',
            '#10B981': 'banga',
            '#059669': 'sinag',
            '#EC4899': 'dalisay',
            '#F59E0B': 'kabkab',
            '#0891B2': 'alon'
        };
        
        return colorPatternMap[this.selectedColor] || 'sussuh';
    }

    showNotification(message, type = 'info') {
        // Create notification element
        const notification = document.createElement('div');
        notification.className = `fixed top-4 right-4 px-4 py-2 rounded-lg text-white text-sm font-medium z-50 transition-all transform translate-x-full`;
        
        // Set color based on type
        const colors = {
            'success': 'bg-green-500',
            'error': 'bg-red-500', 
            'warning': 'bg-yellow-500',
            'info': 'bg-blue-500'
        };
        
        notification.classList.add(colors[type] || colors.info);
        notification.textContent = message;
        
        document.body.appendChild(notification);
        
        // Animate in
        setTimeout(() => {
            notification.classList.remove('translate-x-full');
        }, 100);
        
        // Remove after 3 seconds
        setTimeout(() => {
            notification.classList.add('translate-x-full');
            setTimeout(() => {
                document.body.removeChild(notification);
            }, 300);
        }, 3000);
    }

    createPatternObject(type, x, y) {
        const pattern = this.patterns.get(type);
        if (!pattern) return null;

        let object;
        const fill = this.selectedColor;

        switch (pattern.type) {
            case 'polygon':
                object = new fabric.Polygon(pattern.points, {
                    left: x,
                    top: y,
                    fill: fill,
                    stroke: '#ffffff',
                    strokeWidth: 1,
                    patternType: type,
                    originX: 'center',
                    originY: 'center'
                });
                break;

            case 'circle':
                object = new fabric.Circle({
                    left: x,
                    top: y,
                    radius: pattern.radius,
                    fill: fill,
                    stroke: '#ffffff',
                    strokeWidth: 1,
                    patternType: type,
                    originX: 'center',
                    originY: 'center'
                });
                break;

            case 'star':
                object = this.createStar(x, y, pattern.outerRadius, pattern.innerRadius, pattern.points, fill);
                break;

            case 'sun':
                object = this.createSun(x, y, pattern.radius, pattern.rays, fill);
                break;

            case 'wave':
                object = this.createWave(x, y, pattern.width, pattern.height, fill);
                break;

            case 'flower':
                object = this.createFlower(x, y, pattern.radius, pattern.petals, fill);
                break;

            case 'arrow':
                object = this.createArrow(x, y, pattern.width, pattern.height, fill);
                break;

            case 'diamond':
                object = this.createDiamond(x, y, pattern.width, pattern.height, fill);
                break;

            case 'leaf':
                object = this.createLeaf(x, y, pattern.width, pattern.height, fill);
                break;

            case 'butterfly':
                object = this.createButterfly(x, y, pattern.width, pattern.height, fill);
                break;

            default:
                object = new fabric.Circle({
                    left: x,
                    top: y,
                    radius: 15,
                    fill: fill,
                    patternType: type,
                    originX: 'center',
                    originY: 'center'
                });
        }

        if (object) {
            object.set({
                hasControls: true,
                hasBorders: true,
                cornerColor: '#9333ea',
                borderColor: '#9333ea',
                cornerSize: 8,
                transparentCorners: false
            });
        }

        return object;
    }

    createStar(x, y, outerRadius, innerRadius, points, fill) {
        const angle = Math.PI / points;
        const starPoints = [];
        
        for (let i = 0; i < 2 * points; i++) {
            const radius = i % 2 === 0 ? outerRadius : innerRadius;
            starPoints.push({
                x: radius * Math.sin(i * angle),
                y: -radius * Math.cos(i * angle)
            });
        }

        return new fabric.Polygon(starPoints, {
            left: x,
            top: y,
            fill: fill,
            stroke: '#ffffff',
            strokeWidth: 1,
            patternType: 'star',
            originX: 'center',
            originY: 'center'
        });
    }

    createSun(x, y, radius, rays, fill) {
        const group = new fabric.Group([], {
            left: x,
            top: y,
            patternType: 'sun',
            originX: 'center',
            originY: 'center'
        });

        // Center circle
        const center = new fabric.Circle({
            radius: radius,
            fill: fill,
            stroke: '#ffffff',
            strokeWidth: 1,
            originX: 'center',
            originY: 'center'
        });

        // Rays
        const rayGroup = new fabric.Group([]);
        for (let i = 0; i < rays; i++) {
            const angle = (360 / rays) * i;
            const ray = new fabric.Rect({
                width: 3,
                height: radius * 0.8,
                fill: fill,
                angle: angle,
                originY: 'center',
                left: 0,
                top: -radius * 0.9
            });
            rayGroup.addWithUpdate(ray);
        }

        group.addWithUpdate(center);
        group.addWithUpdate(rayGroup);

        return group;
    }

    createWave(x, y, width, height, fill) {
        const points = [];
        const steps = 20;
        for (let i = 0; i <= steps; i++) {
            const waveX = (width / steps) * i - width / 2;
            const waveY = Math.sin((i / steps) * Math.PI * 2) * height / 2;
            points.push({ x: waveX, y: waveY });
        }

        return new fabric.Polygon(points, {
            left: x,
            top: y,
            fill: fill,
            stroke: '#ffffff',
            strokeWidth: 1,
            patternType: 'wave',
            originX: 'center',
            originY: 'center'
        });
    }

    createFlower(x, y, radius, petals, fill) {
        const group = new fabric.Group([], {
            left: x,
            top: y,
            patternType: 'flower',
            originX: 'center',
            originY: 'center'
        });

        // Petals
        for (let i = 0; i < petals; i++) {
            const angle = (360 / petals) * i;
            const petal = new fabric.Ellipse({
                rx: radius * 0.6,
                ry: radius * 0.3,
                fill: fill,
                stroke: '#ffffff',
                strokeWidth: 1,
                angle: angle,
                originX: 'center',
                originY: 'center',
                left: radius * 0.5 * Math.cos(angle * Math.PI / 180),
                top: radius * 0.5 * Math.sin(angle * Math.PI / 180)
            });
            group.addWithUpdate(petal);
        }

        // Center
        const center = new fabric.Circle({
            radius: radius * 0.3,
            fill: '#FFD700',
            stroke: '#ffffff',
            strokeWidth: 1,
            originX: 'center',
            originY: 'center'
        });
        group.addWithUpdate(center);

        return group;
    }

    addRandomPattern(x, y) {
        const types = Array.from(this.patterns.keys());
        const randomType = types[Math.floor(Math.random() * types.length)];
        this.addPattern(randomType, x, y);
    }

    addPattern(patternType, x, y) {
        const object = this.createPatternObject(patternType, x, y);
        if (object) {
            this.canvas.add(object);
            this.canvas.setActiveObject(object);
            this.canvas.renderAll();
        }
    }

    setSelectedColor(color) {
        this.selectedColor = color;
        const activeObject = this.canvas.getActiveObject();
        if (activeObject) {
            activeObject.set('fill', color);
            this.canvas.renderAll();
            this.saveState();
        }
    }

    setTool(tool) {
        this.currentTool = tool;
        if (tool === 'select') {
            this.canvas.selection = true;
            this.canvas.defaultCursor = 'default';
        } else {
            this.canvas.selection = false;
            this.canvas.defaultCursor = 'crosshair';
        }

        // Update UI
        document.querySelectorAll('.tool-btn').forEach(btn => {
            btn.classList.remove('active', 'bg-purple-600', 'text-white');
            btn.classList.add('bg-gray-200', 'text-gray-700');
        });
        
        const activeBtn = document.querySelector(`[onclick*="${tool}"]`);
        if (activeBtn) {
            activeBtn.classList.remove('bg-gray-200', 'text-gray-700');
            activeBtn.classList.add('active', 'bg-purple-600', 'text-white');
        }
    }

    deleteSelected() {
        const activeObjects = this.canvas.getActiveObjects();
        if (activeObjects.length > 0) {
            activeObjects.forEach(obj => this.canvas.remove(obj));
            this.canvas.discardActiveObject();
            this.canvas.renderAll();
            this.saveState();
        }
    }

    clear() {
        if (confirm('Are you sure you want to clear all patterns?')) {
            this.canvas.clear();
            this.canvas.backgroundColor = '#f9fafb';
            this.canvas.renderAll();
            this.saveState();
        }
    }

    saveState() {
        const state = JSON.stringify(this.canvas.toJSON());
        this.history = this.history.slice(0, this.historyIndex + 1);
        this.history.push(state);
        
        if (this.history.length > this.maxHistory) {
            this.history.shift();
        } else {
            this.historyIndex++;
        }
    }

    undo() {
        if (this.historyIndex > 0) {
            this.historyIndex--;
            this.loadState(this.history[this.historyIndex]);
        }
    }

    redo() {
        if (this.historyIndex < this.history.length - 1) {
            this.historyIndex++;
            this.loadState(this.history[this.historyIndex]);
        }
    }

    loadState(state) {
        this.canvas.loadFromJSON(state, () => {
            this.canvas.renderAll();
        });
    }

    zoomIn() {
        const zoom = this.canvas.getZoom();
        this.canvas.setZoom(Math.min(zoom * 1.1, 3));
        this.canvas.renderAll();
    }

    zoomOut() {
        const zoom = this.canvas.getZoom();
        this.canvas.setZoom(Math.max(zoom * 0.9, 0.3));
        this.canvas.renderAll();
    }

    resetZoom() {
        this.canvas.setZoom(1);
        this.canvas.renderAll();
    }

    getZoom() {
        return this.canvas.getZoom();
    }

    getPatternCount() {
        return this.canvas.getObjects().length;
    }

    exportDesign() {
        const metadata = {
            patterns: this.canvas.getObjects().map(obj => ({
                type: obj.patternType || 'custom',
                position: { x: obj.left, y: obj.top },
                scale: obj.scaleX,
                rotation: obj.angle || 0,
                color: obj.fill,
                patternArea: obj.data?.patternArea || null,
                productType: obj.data?.productType || null
            })),
            canvasSize: {
                width: this.canvas.width,
                height: this.canvas.height
            },
            product: this.currentProduct || null
        };

        return {
            image: this.canvas.toDataURL('image/png'),
            metadata: metadata
        };
    }

    // New pattern creation methods
    createArrow(x, y, width, height, fill) {
        const points = [
            {x: -width/2, y: -height/4},
            {x: width/4, y: -height/4},
            {x: width/4, y: -height/2},
            {x: width/2, y: 0},
            {x: width/4, y: height/2},
            {x: width/4, y: height/4},
            {x: -width/2, y: height/4}
        ];

        return new fabric.Polygon(points, {
            left: x,
            top: y,
            fill: fill,
            stroke: '#ffffff',
            strokeWidth: 1,
            patternType: 'arrow',
            originX: 'center',
            originY: 'center'
        });
    }

    createDiamond(x, y, width, height, fill) {
        const points = [
            {x: 0, y: -height/2},
            {x: width/2, y: 0},
            {x: 0, y: height/2},
            {x: -width/2, y: 0}
        ];

        return new fabric.Polygon(points, {
            left: x,
            top: y,
            fill: fill,
            stroke: '#ffffff',
            strokeWidth: 1,
            patternType: 'diamond',
            originX: 'center',
            originY: 'center'
        });
    }

    createLeaf(x, y, width, height, fill) {
        const points = [];
        const steps = 20;
        
        for (let i = 0; i <= steps; i++) {
            const t = i / steps;
            const px = (t - 0.5) * width;
            const py = Math.sin(t * Math.PI) * (height/2) * (1 - Math.abs(t - 0.5) * 0.5);
            points.push({x: px, y: -py});
        }

        return new fabric.Polygon(points, {
            left: x,
            top: y,
            fill: fill,
            stroke: '#ffffff',
            strokeWidth: 1,
            patternType: 'leaf',
            originX: 'center',
            originY: 'center'
        });
    }

    createButterfly(x, y, width, height, fill) {
        const leftWing = [
            {x: 0, y: 0},
            {x: -width/2, y: -height/4},
            {x: -width/2, y: -height/2},
            {x: -width/4, y: -height/3},
            {x: 0, y: 0}
        ];

        const rightWing = [
            {x: 0, y: 0},
            {x: width/2, y: -height/4},
            {x: width/2, y: -height/2},
            {x: width/4, y: -height/3},
            {x: 0, y: 0}
        ];

        const leftWingObj = new fabric.Polygon(leftWing, {
            fill: fill,
            stroke: '#ffffff',
            strokeWidth: 1,
            originX: 'center',
            originY: 'center'
        });

        const rightWingObj = new fabric.Polygon(rightWing, {
            fill: fill,
            stroke: '#ffffff',
            strokeWidth: 1,
            originX: 'center',
            originY: 'center'
        });

        const body = new fabric.Circle({
            radius: 3,
            fill: fill,
            originX: 'center',
            originY: 'center'
        });

        return new fabric.Group([leftWingObj, rightWingObj, body], {
            left: x,
            top: y,
            patternType: 'butterfly',
            originX: 'center',
            originY: 'center'
        });
    }

    // Advanced tools methods
    setTool(tool) {
        this.currentTool = tool;
        
        // Update UI to reflect active tool
        document.querySelectorAll('.tool-btn').forEach(btn => {
            btn.classList.remove('bg-gradient-to-r', 'from-purple-600', 'to-purple-700', 'text-white');
            btn.classList.add('bg-gray-200', 'text-gray-700');
        });
        
        const activeBtn = document.querySelector(`[onclick*="${tool}"]`);
        if (activeBtn) {
            activeBtn.classList.remove('bg-gray-200', 'text-gray-700');
            activeBtn.classList.add('bg-gradient-to-r', 'from-purple-600', 'to-purple-700', 'text-white');
        }

        // Handle specific tool setup
        switch(tool) {
            case 'text':
                this.setupTextTool();
                break;
            case 'texture':
                this.setupTextureTool();
                break;
            case 'layer':
                this.showLayerPanel();
                break;
        }
    }

    setupTextTool() {
        this.canvas.on('mouse:down', (options) => {
            if (this.currentTool === 'text' && !options.target) {
                const pointer = this.canvas.getPointer(options.e);
                this.addText(pointer.x, pointer.y);
            }
        });
        
        this.showNotification('Click on the canvas to add text', 'info');
    }

    addText(x, y) {
        const text = new fabric.IText('Your Text', {
            left: x,
            top: y,
            fontFamily: 'Arial',
            fontSize: 20,
            fill: this.selectedColor,
            originX: 'center',
            originY: 'center'
        });
        
        this.canvas.add(text);
        this.canvas.setActiveObject(text);
        text.enterEditing();
        
        this.showNotification('Text added - double-click to edit', 'success');
    }

    setupTextureTool() {
        // Create texture overlay
        const texture = new fabric.Rect({
            left: 0,
            top: 0,
            width: this.canvas.width,
            height: this.canvas.height,
            fill: 'transparent',
            selectable: false,
            evented: false
        });
        
        this.canvas.add(texture);
        this.canvas.sendToBack(texture);
        
        this.showNotification('Texture tool activated - select a pattern to apply', 'info');
    }

    uploadImage(event) {
        const file = event.target.files[0];
        if (!file) return;

        const reader = new FileReader();
        reader.onload = (e) => {
            fabric.Image.fromURL(e.target.result, (img) => {
                // Scale image to fit canvas
                const scale = Math.min(
                    this.canvas.width / img.width,
                    this.canvas.height / img.height
                ) * 0.5;
                
                img.scale(scale);
                img.set({
                    left: this.canvas.width / 2,
                    top: this.canvas.height / 2,
                    originX: 'center',
                    originY: 'center'
                });
                
                this.canvas.add(img);
                this.canvas.setActiveObject(img);
                this.showNotification('Image uploaded successfully', 'success');
            });
        };
        
        reader.readAsDataURL(file);
    }

    toggleLayerPanel() {
        const panel = document.getElementById('layerPanel');
        if (!panel) {
            this.createLayerPanel();
        } else {
            panel.classList.toggle('hidden');
        }
    }

    createLayerPanel() {
        const panel = document.createElement('div');
        panel.id = 'layerPanel';
        panel.className = 'fixed right-4 top-20 bg-white rounded-lg shadow-xl p-4 w-64 z-50 hidden';
        
        panel.innerHTML = `
            <h3 class="font-semibold text-gray-900 mb-3">Layers</h3>
            <div id="layerList" class="space-y-2">
                <!-- Layers will be listed here -->
            </div>
        `;
        
        document.body.appendChild(panel);
        this.updateLayerList();
        panel.classList.remove('hidden');
    }

    updateLayerList() {
        const layerList = document.getElementById('layerList');
        if (!layerList) return;
        
        const objects = this.canvas.getObjects();
        layerList.innerHTML = '';
        
        objects.forEach((obj, index) => {
            const layerItem = document.createElement('div');
            layerItem.className = 'flex items-center justify-between p-2 bg-gray-50 rounded hover:bg-gray-100';
            
            layerItem.innerHTML = `
                <span class="text-sm">${obj.type || 'Object'} ${index + 1}</span>
                <div class="flex space-x-2">
                    <button onclick="designer.moveLayer(${index}, 'up')" class="text-gray-600 hover:text-gray-900">
                        ↑
                    </button>
                    <button onclick="designer.moveLayer(${index}, 'down')" class="text-gray-600 hover:text-gray-900">
                        ↓
                    </button>
                    <button onclick="designer.deleteLayer(${index})" class="text-red-600 hover:text-red-900">
                        ×
                    </button>
                </div>
            `;
            
            layerList.appendChild(layerItem);
        });
    }

    moveLayer(index, direction) {
        const objects = this.canvas.getObjects();
        if (direction === 'up' && index < objects.length - 1) {
            this.canvas.bringForward(objects[index]);
        } else if (direction === 'down' && index > 0) {
            this.canvas.sendBackwards(objects[index]);
        }
        
        this.updateLayerList();
        this.canvas.renderAll();
    }

    deleteLayer(index) {
        const objects = this.canvas.getObjects();
        this.canvas.remove(objects[index]);
        this.updateLayerList();
        this.canvas.renderAll();
    }
