import { 
    Vector2, 
    Vector3, 
    Raycaster, 
    Plane, 
    Object3D, 
    Scene, 
    WebGLRenderer, 
    PerspectiveCamera, 
    AmbientLight, 
    DirectionalLight, 
    CanvasTexture, 
    MeshBasicMaterial, 
    DoubleSide, 
    PlaneGeometry, 
    Mesh, 
    InstancedMesh, 
    MeshStandardMaterial,
    Clock
} from 'three';
import { GLTFLoader } from 'three/examples/jsm/loaders/GLTFLoader.js';
import RAPIER from '@dimforge/rapier3d-compat';

class LegoSimulation {
    constructor(container) {
        this.container = container;
        this.clock = new Clock();
        
        // Fixed timestep pour Rapier (recommandé)
        this.accumulator = 0;
        this.fixedTimeStep = 1 / 60; // 60 FPS
        
        // --- MODIF PARAMS ---
        this.params = {
            brickCount: 550, // Un peu moins pour la densité
            brickScale: 0.06,
            gravity: -9.81,
            colors: [0x937ff5, 0x6047b8, 0xb7abff, 0xe2deff, 0x221f24, 0x8b858f, 0xeceaed, 0x9f8cff, 0xcbc6ce],
            forceRadius: 1.2,    // Réduit (était 2.0)
            forceStrength: 0.8,  // Nettement réduit (était 2.5) pour un glissement fluide
            warmupSteps: 0,      // Désactivé pour améliorer la performance de chargement
            baseVisibleWidth: window.innerWidth < 768 ? 14 : 20 // Plus petit sur mobile = Plus de zoom
        };

        this.instances = new Map();
        this.walls = []; // Pour pouvoir les supprimer au resize
        this.isPaused = true;
        this.mouse = new Vector2();
        this.mouseActive = false; // Remplace le hack 999,999
        this.mouseWorld = new Vector3();
        this.raycaster = new Raycaster();
        this.interactionPlane = new Plane(new Vector3(0, 1, 0), 0);
        this.dummy = new Object3D();
        
        // Stockage pour cleanup
        this.animationId = null;
        this.resizeHandler = this.onResize.bind(this);
        this.mouseMoveHandler = this.onMouseMove.bind(this);
        this.keyDownHandler = this.onKeyDown.bind(this);

        this.createLoader();
        this.init();
    }

    async init() {
        try {
            this.showLoader();
            
            await RAPIER.init();
            this.world = new RAPIER.World({ x: 0, y: this.params.gravity, z: 0 });
            
            this.setupScene();
            this.updatePhysicsBounds(); // Calcule les murs selon la taille réelle
            await this.loadAndCreateBricks();
            
            // Le warmup est désactivé pour une meilleure performance au chargement
            this.updateAllMatrices(true);

            this.setupEvents();
            this.animate();
            
            this.hideLoader();
        } catch (error) {
            console.error("Lego Scene: Init error", error);
            this.hideLoader();
        }
    }

    setupScene() {
        this.scene = new Scene();
        // Fond transparent pour laisser voir le texte en dessous
        this.scene.background = null;

        this.renderer = new WebGLRenderer({ 
            antialias: true, 
            powerPreference: "high-performance",
            alpha: true // Active la transparence du canvas
        });
        this.renderer.setClearColor(0x000000, 0); // Fond complètement transparent
        this.renderer.setPixelRatio(Math.min(window.devicePixelRatio, 2));
        this.renderer.setSize(this.container.clientWidth, this.container.clientHeight);
        // Ombres désactivées : le sol physique n'est pas un mesh Three, le plan texte ne reçoit pas d'ombre
        // Économie GPU significative
        this.renderer.shadowMap.enabled = false;
        this.container.appendChild(this.renderer.domElement);

        // Caméra responsive : on fixe la position y mais on va bouger le FOV
        this.camera = new PerspectiveCamera(25, this.container.clientWidth / this.container.clientHeight, 0.1, 1000);
        this.camera.position.set(0, 40, 0);
        this.camera.lookAt(0, 0, 0);
        this.updateCameraFOV();

        const ambient = new AmbientLight(0xffffff, 1.0);
        const dirLight = new DirectionalLight(0xffffff, 0.8);
        dirLight.position.set(15, 35, 15);
        // Plus besoin de castShadow vu que les ombres sont désactivées
        this.scene.add(ambient, dirLight);
        
        // Créer le plan 3D avec texte et bouton
        this.createTextPlane();
    }

    createLoader() {
        this.loader = document.createElement('div');
        this.loader.className = 'lego-loader';
        this.loader.innerHTML = `
            <div class="lego-loader-spinner"></div>
            <div class="lego-loader-text">Chargement de la scène...</div>
        `;
        this.container.appendChild(this.loader);
    }

    showLoader() {
        if (this.loader) {
            this.loader.style.opacity = '1';
            this.loader.style.visibility = 'visible';
        }
    }

    hideLoader() {
        if (this.loader) {
            this.loader.style.opacity = '0';
            setTimeout(() => {
                if (this.loader) {
                    this.loader.style.visibility = 'hidden';
                }
            }, 300);
        }
    }

    createTextPlane() {
        // Créer un canvas pour dessiner le texte (optimisé: 512x128 au lieu de 2048x512)
        const canvas = document.createElement('canvas');
        canvas.width = 512;
        canvas.height = 128;
        const ctx = canvas.getContext('2d');
        
        // Fond complètement transparent
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        
        // Dessiner le texte au centre
        ctx.fillStyle = 'rgba(168, 85, 247, 0.5)';
        ctx.font = '8px Arial';
        ctx.textAlign = 'center';
        ctx.textBaseline = 'middle';
        ctx.fillText('Je ne m’attendais vraiment pas à vous voir ici...', canvas.width / 2, canvas.height / 2);
        
        // Créer la texture
        const texture = new CanvasTexture(canvas);
        texture.needsUpdate = true;
        
        // Créer le matériau transparent
        const material = new MeshBasicMaterial({
            map: texture,
            transparent: true,
            opacity: 1,
            side: DoubleSide
        });
        
        // Créer le plan
        const planeWidth = 30;
        const planeHeight = 7.5;
        const geometry = new PlaneGeometry(planeWidth, planeHeight);
        this.textPlane = new Mesh(geometry, material);
        
        // Positionner le plan au sol (y = 0.1 pour être juste au-dessus du sol)
        this.textPlane.position.set(0, 0.1, 0);
        this.textPlane.rotation.x = -Math.PI / 2; // Rotation pour être horizontal
        this.textPlane.name = 'textPlane';
        
        this.scene.add(this.textPlane);
    }


    // --- MODIFIE LA MÉTHODE updateCameraFOV ---
    updateCameraFOV() {
        const aspect = this.container.clientWidth / this.container.clientHeight;
        // Si mobile, on veut voir moins de largeur pour "zoomer" sur le tas
        const targetWidth = window.innerWidth < 768 ? 14 : this.params.baseVisibleWidth;
        // Calcul du FOV vertical pour maintenir cette largeur cible
        const vFOV = 2 * Math.atan((targetWidth / aspect) / (2 * this.camera.position.y)) * (180 / Math.PI);
        this.camera.fov = vFOV;
        this.camera.aspect = aspect;
        this.camera.updateProjectionMatrix();
    }

    updatePhysicsBounds() {
        // Supprimer les anciens murs s'ils existent
        this.walls.forEach(w => this.world.removeRigidBody(w));
        this.walls = [];

        const aspect = this.container.clientWidth / this.container.clientHeight;
        const vFOV = this.camera.fov * Math.PI / 180;
        const visibleHeight = 2 * Math.tan(vFOV / 2) * this.camera.position.y;
        const visibleWidth = visibleHeight * aspect;

        // "Triche" : On agrandit les murs de 5% par rapport à la vue
        // pour que les briques soient coupées par les bords de l'écran
        const offset = 1.05; 
        const wallH = (visibleHeight / 2) * offset;
        const wallW = (visibleWidth / 2) * offset;

        const ground = this.world.createRigidBody(RAPIER.RigidBodyDesc.fixed());
        this.world.createCollider(RAPIER.ColliderDesc.cuboid(wallW * 2, 0.5, wallH * 2), ground);
        this.walls.push(ground);

        const addWall = (x, z, hx, hz) => {
            // Murs hauts pour contenir les explosions (calculé dynamiquement selon le nombre de briques)
            // On estime qu'un empilement max fait ~brickCount/10 briques de haut
            const wallHeight = Math.max(50, this.params.brickCount / 5); // Minimum 50 pour sécurité
            const b = this.world.createRigidBody(RAPIER.RigidBodyDesc.fixed().setTranslation(x, wallHeight, z));
            this.world.createCollider(RAPIER.ColliderDesc.cuboid(hx, wallHeight, hz), b);
            this.walls.push(b);
        };

        addWall(wallW, 0, 0.5, wallH); 
        addWall(-wallW, 0, 0.5, wallH);
        addWall(0, wallH, wallW, 0.5);
        addWall(0, -wallH, wallW, 0.5);
    }

    async loadAndCreateBricks() {
        const loader = new GLTFLoader();
        let gltf;
        
        try {
            // Tenter d'abord le GLB optimisé
            gltf = await loader.loadAsync('/wp-content/themes/Portfolio/resources/models/LegoBrick-optimized.glb');
        } catch (error) {
            // Fallback sur le GLTF original
            gltf = await loader.loadAsync('/wp-content/themes/Portfolio/resources/models/LegoBrick.gltf');
        }
        
        let masterMesh = null;
        
        // Traverser toute la scène pour trouver le mesh
        gltf.scene.traverse(c => { 
            if (c.isMesh && !masterMesh) masterMesh = c;
        });
        
        // Chercher dans les enfants si traverse ne trouve rien
        if (!masterMesh && gltf.scene.children.length > 0) {
            masterMesh = gltf.scene.children.find(c => c.isMesh || c.type === 'Mesh');
        }
        
        // Recherche récursive dans les groupes
        if (!masterMesh) {
            const findMeshInChildren = (obj) => {
                if (obj.isMesh || obj.type === 'Mesh') return obj;
                if (obj.children) {
                    for (let child of obj.children) {
                        const found = findMeshInChildren(child);
                        if (found) return found;
                    }
                }
                return null;
            };
            masterMesh = findMeshInChildren(gltf.scene);
        }
        
        if (!masterMesh || !masterMesh.geometry) {
            console.error('Lego Scene: Aucun mesh trouvé dans le fichier');
            throw new Error('Impossible de trouver le mesh dans le fichier GLB/GLTF');
        }

        const geometry = masterMesh.geometry.clone();
        geometry.scale(this.params.brickScale, this.params.brickScale, this.params.brickScale);
        geometry.computeBoundingBox();
        const center = new Vector3();
        geometry.boundingBox.getCenter(center);
        geometry.translate(-center.x, -center.y, -center.z);
        const halfSize = new Vector3().copy(geometry.boundingBox.max);

        this.params.colors.forEach((color) => {
            const count = Math.ceil(this.params.brickCount / this.params.colors.length);
            const mesh = new InstancedMesh(geometry, new MeshStandardMaterial({ color, roughness: 0.4, metalness: 0.1 }), count);
            // Plus besoin de castShadow vu que les ombres sont désactivées
            this.scene.add(mesh);

            const bodies = [];
            const aspect = this.container.clientWidth / this.container.clientHeight;
            const spreadW = (this.params.baseVisibleWidth / 2) * 0.8;
            const spreadH = (spreadW / aspect) * 0.8;

            for (let i = 0; i < count; i++) {
                const x = (Math.random() - 0.5) * spreadW * 2;
                const z = (Math.random() - 0.5) * spreadH * 2;
                const body = this.world.createRigidBody(RAPIER.RigidBodyDesc.dynamic().setTranslation(x, 2 + i * 0.1, z).setCanSleep(true));
                this.world.createCollider(RAPIER.ColliderDesc.cuboid(halfSize.x, halfSize.y, halfSize.z), body);
                bodies.push(body);
            }
            this.instances.set(color, { mesh, bodies, count });
        });
    }

    setupEvents() {
        new IntersectionObserver(e => this.isPaused = !e[0].isIntersecting).observe(this.container);

        this.container.addEventListener('mousemove', this.mouseMoveHandler);
        window.addEventListener('resize', this.resizeHandler);
        window.addEventListener('keydown', this.keyDownHandler);
    }

    onKeyDown(e) {
        // Option + G sur Mac (Alt + G sur Windows)
        // Sur Mac, Option+G produit le caractère "ﬁ", on utilise donc e.code
        if (e.altKey && e.code === 'KeyG') {
            console.log("Lego Scene: Explosion triggered! 💥");
            e.preventDefault();
            this.explodeBricks();
        }
    }

    explodeBricks() {
        const explosionCenter = new Vector3(0, 0, 0);
        const explosionForce = 8; // Force de l'explosion
        
        this.instances.forEach(data => {
            data.bodies.forEach(body => {
                body.wakeUp();
                const pos = body.translation();
                
                // Calcul de la direction depuis le centre
                const direction = new Vector3(
                    pos.x - explosionCenter.x,
                    pos.y - explosionCenter.y,
                    pos.z - explosionCenter.z
                );
                
                const distance = direction.length();
                if (distance < 0.1) return; // Évite la division par zéro
                
                direction.normalize();
                
                // Force diminue avec la distance mais reste significative
                const forceMagnitude = explosionForce * (1 + Math.random() * 0.5);
                
                body.applyImpulse({
                    x: direction.x * forceMagnitude,
                    y: direction.y * forceMagnitude + 8, // Boost vertical pour l'effet d'explosion
                    z: direction.z * forceMagnitude
                }, true);
                
                // Ajoute une rotation aléatoire
                body.applyTorqueImpulse({
                    x: (Math.random() - 0.5) * 5,
                    y: (Math.random() - 0.5) * 5,
                    z: (Math.random() - 0.5) * 5
                }, true);
            });
        });
    }

    onMouseMove(e) {
        this.mouseActive = true;
        const rect = this.container.getBoundingClientRect();
        this.mouse.x = ((e.clientX - rect.left) / rect.width) * 2 - 1;
        this.mouse.y = -((e.clientY - rect.top) / rect.height) * 2 + 1;
        this.raycaster.setFromCamera(this.mouse, this.camera);
        this.raycaster.ray.intersectPlane(this.interactionPlane, this.mouseWorld);
    }

    onResize() {
        this.renderer.setSize(this.container.clientWidth, this.container.clientHeight);
        this.updateCameraFOV();
        this.updatePhysicsBounds();
        this.instances.forEach(d => d.bodies.forEach(b => b.wakeUp()));
    }

    updateAllMatrices(force = false) {
        this.instances.forEach(data => {
            let changed = false;
            for (let i = 0; i < data.count; i++) {
                const b = data.bodies[i];
                if (!force && b.isSleeping()) continue;
                const p = b.translation(); const r = b.rotation();
                this.dummy.position.set(p.x, p.y, p.z);
                this.dummy.quaternion.set(r.x, r.y, r.z, r.w);
                this.dummy.updateMatrix();
                data.mesh.setMatrixAt(i, this.dummy.matrix);
                changed = true;
            }
            if (changed || force) data.mesh.instanceMatrix.needsUpdate = true;
        });
    }

    applyMouseForces() {
        if (!this.mouseActive) return;
        const rSq = this.params.forceRadius ** 2;
        this.instances.forEach(d => d.bodies.forEach(b => {
            const p = b.translation();
            const dx = p.x - this.mouseWorld.x; const dz = p.z - this.mouseWorld.z;
            const dSq = dx * dx + dz * dz;
            if (dSq < rSq && dSq > 0.01) {
                b.wakeUp();
                const dist = Math.sqrt(dSq);
                const f = this.params.forceStrength * (1 - dist / this.params.forceRadius);
                b.applyImpulse({ x: (dx / dist) * f, y: f * 0.5, z: (dz / dist) * f }, true);
            }
        }));
    }

    animate() {
        this.animationId = requestAnimationFrame(() => this.animate());
        
        if (this.isPaused || !this.scene || !this.world) return;
        
        // Fixed timestep avec accumulateur (recommandé par Rapier)
        const deltaTime = Math.min(this.clock.getDelta(), 0.1); // Cap à 100ms pour éviter spiral of death
        this.accumulator += deltaTime;
        
        // Step la physique avec un timestep fixe tant qu'il reste du temps accumulé
        while (this.accumulator >= this.fixedTimeStep) {
            this.applyMouseForces();
            this.world.step();
            this.accumulator -= this.fixedTimeStep;
        }
        
        this.updateAllMatrices();
        this.renderer.render(this.scene, this.camera);
    }

    // --- MÉTHODE DE CLEANUP ---
    destroy() {
        // 1. Stop la boucle
        if (this.animationId) cancelAnimationFrame(this.animationId);

        // 2. Nettoie les listeners
        window.removeEventListener('resize', this.resizeHandler);
        window.removeEventListener('keydown', this.keyDownHandler);
        if (this.container) {
            this.container.removeEventListener('mousemove', this.mouseMoveHandler);
        }
        
        // 3. Nettoie Three.js (Crucial pour la mémoire GPU)
        if (this.scene) {
            this.scene.traverse((object) => {
                if (!object.isMesh) return;
                
                if (object.geometry) object.geometry.dispose();

                if (object.material) {
                    if (Array.isArray(object.material)) {
                        object.material.forEach(m => m.dispose());
                    } else {
                        object.material.dispose();
                    }
                }
            });
        }

        if (this.renderer) {
            this.renderer.dispose();
            this.renderer.forceContextLoss();
            if (this.renderer.domElement && this.renderer.domElement.parentNode) {
                this.renderer.domElement.remove();
            }
        }
        
        // Nettoyer le plan de texte
        if (this.textPlane) {
            if (this.textPlane.material.map) this.textPlane.material.map.dispose();
            if (this.textPlane.material) this.textPlane.material.dispose();
            if (this.textPlane.geometry) this.textPlane.geometry.dispose();
        }
        
        // 4. Nettoie Rapier
        if (this.world) {
            this.walls.forEach(w => {
                try { this.world.removeRigidBody(w); } catch(e) {}
            });
            this.instances.forEach(data => {
                data.bodies.forEach(b => {
                    try { this.world.removeRigidBody(b); } catch(e) {}
                });
            });
        }
        
        // 5. Nettoie le loader
        if (this.loader && this.loader.parentNode) {
            this.loader.parentNode.removeChild(this.loader);
        }
        
        this.renderer = null;
        this.scene = null;
        this.world = null;
        this.instances.clear();
        this.walls = [];
        this.loader = null;
    }
}

// Export pour utilisation avec Barba
export { LegoSimulation };