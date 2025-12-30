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
        this.selectedPattern = null; // Add this to track selected pattern
        this.history = [];
        this.historyIndex = -1;
        this.maxHistory = 50;

        this.init();
    }

    init() {
        this.setupPatterns();
        this.setupCanvasEvents();
        this.loadPatternLibrary();
        this.setupLivePreview();
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
    }

    loadPatternLibrary() {
        const libraryContainer = document.getElementById('patternLibrary');
        if (!libraryContainer) return;

        libraryContainer.innerHTML = '';

        this.patterns.forEach((pattern, key) => {
            const patternCard = document.createElement('div');
            patternCard.className = 'pattern-card bg-white border-2 border-gray-200 rounded-lg p-3 cursor-pointer hover:border-purple-500 hover:shadow-lg transition-all transform hover:scale-105';
            patternCard.dataset.patternType = key;

            // Create preview canvas
            const previewCanvas = document.createElement('canvas');
            previewCanvas.width = 80;
            previewCanvas.height = 80;
            previewCanvas.className = 'w-full h-20 mb-2 rounded bg-gray-50';

            // Draw pattern preview
            this.drawPatternPreview(previewCanvas, key, pattern.defaultColor);

            // Pattern info
            const patternName = document.createElement('div');
            patternName.className = 'text-xs font-semibold text-gray-900 text-center';
            patternName.textContent = pattern.name;

            patternCard.appendChild(previewCanvas);
            patternCard.appendChild(patternName);

            // Add click event
            patternCard.addEventListener('click', () => {
                this.selectPattern(key);
            });

            // Add hover event for live preview
            patternCard.addEventListener('mouseenter', () => {
                this.showPatternPreview(key);
            });

            libraryContainer.appendChild(patternCard);
        });
    }

    drawPatternPreview(canvas, patternType, color) {
        const ctx = canvas.getContext('2d');
        const centerX = canvas.width / 2;
        const centerY = canvas.height / 2;
        const scale = 0.5; // Scale down for preview

        ctx.fillStyle = color;
        ctx.strokeStyle = '#ffffff';
        ctx.lineWidth = 1;

        const pattern = this.patterns.get(patternType);
        if (!pattern) return;

        switch (pattern.type) {
            case 'polygon':
                this.drawPolygonPreview(ctx, centerX, centerY, pattern.points, scale);
                break;
            case 'circle':
                this.drawCirclePreview(ctx, centerX, centerY, pattern.radius * scale);
                break;
            case 'star':
                this.drawStarPreview(ctx, centerX, centerY, pattern.outerRadius * scale, pattern.innerRadius * scale, pattern.points);
                break;
            case 'sun':
                this.drawSunPreview(ctx, centerX, centerY, pattern.radius * scale, pattern.rays);
                break;
            case 'wave':
                this.drawWavePreview(ctx, centerX, centerY, pattern.width * scale, pattern.height * scale);
                break;
            case 'flower':
                this.drawFlowerPreview(ctx, centerX, centerY, pattern.radius * scale, pattern.petals);
                break;
        }
    }

    drawPolygonPreview(ctx, x, y, points, scale) {
        ctx.beginPath();
        points.forEach((point, index) => {
            const scaledX = x + point[0] * scale;
            const scaledY = y + point[1] * scale;
            if (index === 0) {
                ctx.moveTo(scaledX, scaledY);
            } else {
                ctx.lineTo(scaledX, scaledY);
            }
        });
        ctx.closePath();
        ctx.fill();
        ctx.stroke();
    }

    drawCirclePreview(ctx, x, y, radius) {
        ctx.beginPath();
        ctx.arc(x, y, radius, 0, 2 * Math.PI);
        ctx.fill();
        ctx.stroke();
    }

    drawStarPreview(ctx, x, y, outerRadius, innerRadius, points) {
        const angle = Math.PI / points;
        ctx.beginPath();
        
        for (let i = 0; i < 2 * points; i++) {
            const radius = i % 2 === 0 ? outerRadius : innerRadius;
            const starX = x + radius * Math.sin(i * angle);
            const starY = y - radius * Math.cos(i * angle);
            
            if (i === 0) {
                ctx.moveTo(starX, starY);
            } else {
                ctx.lineTo(starX, starY);
            }
        }
        ctx.closePath();
        ctx.fill();
        ctx.stroke();
    }

    drawSunPreview(ctx, x, y, radius, rays) {
        // Draw center circle
        ctx.beginPath();
        ctx.arc(x, y, radius, 0, 2 * Math.PI);
        ctx.fill();
        ctx.stroke();

        // Draw rays
        for (let i = 0; i < rays; i++) {
            const angle = (360 / rays) * i * Math.PI / 180;
            const rayLength = radius * 0.8;
            
            ctx.save();
            ctx.translate(x, y);
            ctx.rotate(angle);
            
            ctx.beginPath();
            ctx.rect(-1.5, -radius * 0.9, 3, rayLength);
            ctx.fill();
            ctx.stroke();
            
            ctx.restore();
        }
    }

    drawWavePreview(ctx, x, y, width, height) {
        ctx.beginPath();
        const steps = 20;
        for (let i = 0; i <= steps; i++) {
            const waveX = x - width/2 + (width / steps) * i;
            const waveY = y + Math.sin((i / steps) * Math.PI * 2) * height / 2;
            
            if (i === 0) {
                ctx.moveTo(waveX, waveY);
            } else {
                ctx.lineTo(waveX, waveY);
            }
        }
        ctx.closePath();
        ctx.fill();
        ctx.stroke();
    }

    drawFlowerPreview(ctx, x, y, radius, petals) {
        // Draw petals
        for (let i = 0; i < petals; i++) {
            const angle = (360 / petals) * i * Math.PI / 180;
            const petalX = x + radius * 0.5 * Math.cos(angle);
            const petalY = y + radius * 0.5 * Math.sin(angle);
            
            ctx.beginPath();
            ctx.ellipse(petalX, petalY, radius * 0.6, radius * 0.3, angle, 0, 2 * Math.PI);
            ctx.fill();
            ctx.stroke();
        }

        // Draw center
        ctx.beginPath();
        ctx.arc(x, y, radius * 0.3, 0, 2 * Math.PI);
        ctx.fillStyle = '#FFD700';
        ctx.fill();
        ctx.stroke();
        ctx.fillStyle = this.selectedColor; // Reset to original color
    }

    selectPattern(patternType) {
        this.selectedPattern = patternType;
        this.setTool('pattern');

        // Update UI to show selected pattern
        this.updateSelectedPatternDisplay(patternType);

        // Highlight selected pattern card
        document.querySelectorAll('.pattern-card').forEach(card => {
            card.classList.remove('border-purple-600', 'bg-purple-50');
            card.classList.add('border-gray-200');
        });

        const selectedCard = document.querySelector(`[data-pattern-type="${patternType}"]`);
        if (selectedCard) {
            selectedCard.classList.remove('border-gray-200');
            selectedCard.classList.add('border-purple-600', 'bg-purple-50');
        }
    }

    updateSelectedPatternDisplay(patternType) {
        const display = document.getElementById('selectedPatternDisplay');
        const preview = document.getElementById('selectedPatternPreview');
        const name = document.getElementById('selectedPatternName');

        if (!display || !preview || !name) return;

        const pattern = this.patterns.get(patternType);
        if (pattern) {
            display.classList.remove('hidden');
            name.textContent = pattern.name;

            // Create small preview
            const canvas = document.createElement('canvas');
            canvas.width = 32;
            canvas.height = 32;
            this.drawPatternPreview(canvas, patternType, this.selectedColor);
            
            preview.style.backgroundImage = `url(${canvas.toDataURL()})`;
            preview.style.backgroundSize = 'contain';
            preview.style.backgroundRepeat = 'no-repeat';
            preview.style.backgroundPosition = 'center';
        }
    }

    clearSelectedPattern() {
        this.selectedPattern = null;
        
        const display = document.getElementById('selectedPatternDisplay');
        if (display) {
            display.classList.add('hidden');
        }

        // Remove highlights from pattern cards
        document.querySelectorAll('.pattern-card').forEach(card => {
            card.classList.remove('border-purple-600', 'bg-purple-50');
            card.classList.add('border-gray-200');
        });
    }

    showPatternPreview(patternType) {
        // This could show a larger preview in a tooltip or modal
        // For now, the hover effect on the card is sufficient
    }

    setupCanvasEvents() {
        this.canvas.on('mouse:down', (options) => {
            if (this.currentTool === 'pattern' && !options.target) {
                const pointer = this.canvas.getPointer(options.e);
                // Use selected pattern if available, otherwise use random
                if (this.selectedPattern) {
                    this.addPattern(this.selectedPattern, pointer.x, pointer.y);
                } else {
                    this.addRandomPattern(pointer.x, pointer.y);
                }
            }
        });

        this.canvas.on('object:modified', () => {
            this.saveState();
            this.updateLivePreview();
        });

        this.canvas.on('object:added', () => {
            this.saveState();
            this.updateLivePreview();
        });

        this.canvas.on('object:removed', () => {
            this.saveState();
            this.updateLivePreview();
        });
    }

    setupLivePreview() {
        this.livePreviewCanvas = document.getElementById('livePreviewCanvas');
        if (this.livePreviewCanvas) {
            this.livePreviewCtx = this.livePreviewCanvas.getContext('2d');
            this.updateLivePreview();
        }
    }

    updateLivePreview() {
        if (!this.livePreviewCanvas || !this.livePreviewCtx) return;

        // Clear the preview canvas
        this.livePreviewCtx.clearRect(0, 0, this.livePreviewCanvas.width, this.livePreviewCanvas.height);

        // Calculate scale factor
        const scaleX = this.livePreviewCanvas.width / this.canvas.width;
        const scaleY = this.livePreviewCanvas.height / this.canvas.height;
        const scale = Math.min(scaleX, scaleY);

        // Save context state
        this.livePreviewCtx.save();

        // Apply scaling
        this.livePreviewCtx.scale(scale, scale);

        // Draw canvas content to preview
        const canvasDataUrl = this.canvas.toDataURL();
        const img = new Image();
        img.onload = () => {
            this.livePreviewCtx.drawImage(img, 0, 0);
            this.livePreviewCtx.restore();
        };
        img.src = canvasDataUrl;
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
        
        // Update current color display
        const colorDisplay = document.getElementById('currentColorDisplay');
        if (colorDisplay) {
            colorDisplay.style.backgroundColor = color;
        }

        // Update active object color if one is selected
        const activeObject = this.canvas.getActiveObject();
        if (activeObject) {
            activeObject.set('fill', color);
            this.canvas.renderAll();
            this.updateLivePreview();
            this.saveState();
        }

        // Update selected pattern preview if a pattern is selected
        if (this.selectedPattern) {
            this.updateSelectedPatternDisplay(this.selectedPattern);
            this.refreshPatternLibraryPreviews();
        }

        // Update color button highlights
        document.querySelectorAll('.color-btn').forEach(btn => {
            btn.classList.remove('ring-2', 'ring-purple-500', 'ring-offset-2');
        });

        const activeColorBtn = document.querySelector(`[onclick*="${color}"]`);
        if (activeColorBtn) {
            activeColorBtn.classList.add('ring-2', 'ring-purple-500', 'ring-offset-2');
        }
    }

    refreshPatternLibraryPreviews() {
        // Refresh all pattern previews with the new selected color
        this.patterns.forEach((pattern, key) => {
            const patternCard = document.querySelector(`[data-pattern-type="${key}"]`);
            if (patternCard) {
                const previewCanvas = patternCard.querySelector('canvas');
                if (previewCanvas) {
                    // Clear and redraw with new color
                    const ctx = previewCanvas.getContext('2d');
                    ctx.clearRect(0, 0, previewCanvas.width, previewCanvas.height);
                    this.drawPatternPreview(previewCanvas, key, this.selectedColor);
                }
            }
        });
    }

    setSelectedPattern(patternType) {
        this.selectedPattern = patternType;
        // Also set the tool to pattern mode
        this.setTool('pattern');
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

    getDesignData() {
        // Get canvas as image
        const image = this.canvas.toDataURL({
            format: 'png',
            quality: 0.9,
            multiplier: 2
        });

        // Get metadata
        const metadata = {
            patternCount: this.getPatternCount(),
            canvasWidth: this.canvas.width,
            canvasHeight: this.canvas.height,
            selectedTool: this.currentTool,
            selectedColor: this.selectedColor,
            selectedPattern: this.selectedPattern,
            createdAt: new Date().toISOString(),
            patterns: []
        };

        // Get pattern details
        const objects = this.canvas.getObjects();
        objects.forEach(obj => {
            if (obj.type === 'path' || obj.type === 'circle' || obj.type === 'polygon') {
                metadata.patterns.push({
                    type: obj.type,
                    fill: obj.fill,
                    stroke: obj.stroke,
                    left: obj.left,
                    top: obj.top,
                    scaleX: obj.scaleX,
                    scaleY: obj.scaleY,
                    angle: obj.angle
                });
            }
        });

        return {
            image: image,
            metadata: metadata
        };
    }

    exportDesign() {
        const metadata = {
            patterns: this.canvas.getObjects().map(obj => ({
                type: obj.patternType || 'custom',
                position: { x: obj.left, y: obj.top },
                scale: obj.scaleX,
                rotation: obj.angle || 0,
                color: obj.fill
            })),
            canvasSize: {
                width: this.canvas.width,
                height: this.canvas.height
            },
            backgroundColor: this.canvas.backgroundColor
        };

        return {
            image: this.canvas.toDataURL({
                format: 'png',
                quality: 0.9,
                multiplier: 2
            }),
            metadata: metadata
        };
    }

    loadProductShape(shape) {
        if (!shape || !shape.path) return;
        
        // Clear canvas first
        this.canvas.clear();
        this.canvas.setBackgroundColor('#ffffff', this.canvas.renderAll.bind(this.canvas));
        
        // Create product shape as a path
        fabric.loadSVGFromString(shape.path, (objects, options) => {
            if (objects && objects.length > 0) {
                const svgObj = fabric.util.groupSVGElements(objects, options);
                
                // Center and scale the shape
                const canvasWidth = this.canvas.width;
                const canvasHeight = this.canvas.height;
                const shapeWidth = shape.width || 400;
                const shapeHeight = shape.height || 300;
                
                const scale = Math.min(canvasWidth / shapeWidth, canvasHeight / shapeHeight) * 0.8;
                
                svgObj.set({
                    left: canvasWidth / 2,
                    top: canvasHeight / 2,
                    originX: 'center',
                    originY: 'center',
                    scaleX: scale,
                    scaleY: scale,
                    fill: '#f3f4f6',
                    stroke: '#d1d5db',
                    strokeWidth: 2,
                    selectable: false,
                    evented: false,
                    objectCaching: false
                });
                
                this.canvas.add(svgObj);
                this.canvas.sendToBack(svgObj);
                this.canvas.renderAll();
                
                // Save state after loading shape
                this.saveState();
            }
        });
    }
}
