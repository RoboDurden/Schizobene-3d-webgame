prompt for https://chat.deepseek.com:

**The following is the code of a first-person 3d game where the player flys down into a tunnel and has to avoid objects on a 3x3 grid. \
The code already has a top ten list which is saved localy on the computer.\
Please change this so that the data is sent to a php script which stores the data on a web server in a simple topScores.json text file.\
At the beginning, this online data has to be fetched so every user sees the same top ten list.\
Currently the top ten list only contains a name and the score. When prompted, also add an input field for an email address and a checkbox if the player is female.\
This is the code you have to change:**

```
<!DOCTYPE html>
<html>
<head>
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
	<title>3D Tunnel Game</title>
    <style>
        body { margin: 0; }
        canvas { display: block; }
        #hud {
            position: fixed;
            top: 10px;
            left: 10px;
            color: white;
            font-family: Arial;
            font-size: 20px;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.5);
        }
        #gameOver {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: rgba(0,0,0,0.8);
            padding: 20px;
            color: white;
            text-align: center;
            border-radius: 10px;
        }

        /* Add touch grid styling */
        #touchGrid {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            display: none;
        }
        .grid-line {
            position: absolute;
            background: rgba(255, 255, 255, 0.3);
        }
        .grid-vertical {
            width: 1px;
            height: 100%;
            top: 0;
        }
        .grid-horizontal {
            height: 1px;
            width: 100%;
            left: 0;
        }

    </style>
</head>
<body>
    <div id="hud"></div>
    <div id="gameOver">
        <h2>Game Over!</h2>
        <p>Score: <span id="finalScore">0</span></p>
        <input type="text" id="playerName" placeholder="Enter your name">
        <button onclick="saveScore()">Save Score</button><br/>
		<center><table id="topten"></table>		</center>
    </div>
    <div id="touchGrid">
        <div class="grid-line grid-vertical" style="left: 33.333%"></div>
        <div class="grid-line grid-vertical" style="left: 66.666%"></div>
        <div class="grid-line grid-horizontal" style="top: 33.333%"></div>
        <div class="grid-line grid-horizontal" style="top: 66.666%"></div>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script>
    <script>

		function CPos(x,y)
		{
			this.x = x;
			this.y = y;
		}
        let scene, camera, renderer;
        let playerX = 0, playerY = 0, playerZ = 0;
		let yOld = 0;
        let currentSpeed = 0.05;
        let level = 0;
        let score = 0;
        let lives = 3;
        let walls = [];
        let objectTypes = [];
        let targetObjectType = null;
        let gameActive = true;
        let topScores = JSON.parse(localStorage.getItem('topScores')) || [];

		let rLight = null;
        let gridVisible = false;

        init();
        animate();

        function init() {
            scene = new THREE.Scene();
            camera = new THREE.PerspectiveCamera(75, window.innerWidth / window.innerHeight, 0.1, 1000);
            renderer = new THREE.WebGLRenderer({ antialias: true });
            renderer.setSize(window.innerWidth, window.innerHeight);
            document.body.appendChild(renderer.domElement);

            // Lighting
            rLight = new THREE.PointLight(0xffffff, 1, 100);
            rLight.position.set(0, 0, 0);
            scene.add(rLight);

            scene.add(new THREE.AmbientLight(0x404040));

            generateLevel();
			//lives=1;handleCollision();
        }

        function generateLevel() {
			rLight.intensity = 0;

            walls.forEach(wall => scene.remove(wall.mesh));
            walls = [];
			const distance = 4
            playerZ = -distance -2;

            objectTypes = createShuffledObjectTypes();
            targetObjectType = objectTypes[8];

            // Create 8 regular walls
			let oGap = new CPos(0,0);
            for(let i = 0; i < 8; i++) {
                const zPos = 1 + i * distance;
                oGap = createWall(zPos, objectTypes[i],oGap);
            }

            // Create final wall
            createFinalWall(9*distance-1);

            currentSpeed = 0.02 + level * 0.005;
        }

        function createShuffledObjectTypes() {
            const types = [];
            const shapes = ['cube', 'pyramid', 'ball'];
            const colors = [0xff0000, 0x00ff00, 0x0000ff];
            
            shapes.forEach(shape => {
                colors.forEach(color => {
                    types.push({ shape, color });
                });
            });

            for(let i = types.length - 1; i > 0; i--) {
                const j = Math.floor(Math.random() * (i + 1));
                [types[i], types[j]] = [types[j], types[i]];
            }
            return types;
        }

        function createWall(zPos, type, oGapPrevious) 
		{
			let aGap = [];
            for(let x = -1; x <= 1; x++) 
			{
                for(let y = -1; y <= 1; y++) 
				{
					const iDx = Math.abs(oGapPrevious.x-x);
					if (iDx > 1)	continue;
					const iDy = Math.abs(oGapPrevious.y-y);
					if (iDy > 1)	continue;
					if (iDx + iDy == 0)	continue;
					aGap.push( new CPos(x,y) );
				}
			}
			let oGap =  aGap[Math.floor(Math.random() * aGap.length)];
            const gapX = oGap.x;
            const gapY = oGap.y;

            for(let x = -1; x <= 1; x++) {
                for(let y = -1; y <= 1; y++) {
                    if(x === gapX && y === oGap.y) continue;
                    
                    const mesh = createObjectMesh(type);
                    mesh.position.set(x, y, zPos);
                    scene.add(mesh);
                    walls.push({ 
                        z: zPos, 
                        gapX, 
                        gapY, 
                        mesh, 
                        type, 
                        isFinal: false 
                    });
                }
            }
			return oGap;
        }

        function createFinalWall(zPos) {
            const positions = [];
            for(let x = -1; x <= 1; x++) {
                for(let y = -1; y <= 1; y++) {
                    positions.push({ x, y });
                }
            }
            
            // Shuffle positions
            for(let i = positions.length - 1; i > 0; i--) {
                const j = Math.floor(Math.random() * (i + 1));
                [positions[i], positions[j]] = [positions[j], positions[i]];
            }

            positions.forEach((pos, i) => {
                const type = objectTypes[i];
                const mesh = createObjectMesh(type);
                mesh.position.set(pos.x, pos.y, zPos);
                scene.add(mesh);
                walls.push({
                    z: zPos,
                    x: pos.x,
                    y: pos.y,
                    mesh,
                    type,
                    isFinal: true,
                    isTarget: type === targetObjectType
                });
            });
        }

        function createObjectMesh(type) {
            let geometry;
            switch(type.shape) {
                case 'cube': 
                    geometry = new THREE.BoxGeometry(0.9, 0.9, 0.9);
                    break;
                case 'pyramid':
                    geometry = new THREE.ConeGeometry(0.45, 0.9, 4);
                    break;
                case 'ball':
                    geometry = new THREE.SphereGeometry(0.45, 32, 32);
                    break;
            }
            const material = new THREE.MeshPhongMaterial({ 
                color: type.color,
                shininess: 100 
            });
            return new THREE.Mesh(geometry, material);
        }

		function KeyDown(key)
		{
			//yOld++;
            if(key=="ArrowRight" && playerX > -1) playerX--;
            if(key=="ArrowLeft" && playerX < 1) playerX++;
            if(key=="ArrowUp" && playerY < 1) playerY++;
            if(key=="ArrowDown" && playerY > -1) playerY--;

		}

		function handleTouch(event) 
		{
			event.preventDefault();
            if (!gridVisible) {
                gridVisible = true;
                document.getElementById('touchGrid').style.display = 'block';
            }

			const touches = event.touches;
			if (touches.length > 0) {
				const touch = touches[0];
				const screenWidth = window.innerWidth;
				const screenHeight = window.innerHeight;
				
				// Calculate grid region
				const regionX = Math.floor(touch.clientX / (screenWidth / 3));
				const regionY = Math.floor(touch.clientY / (screenHeight / 3));

				// Convert to grid coordinates
				playerX = regionX === 0 ? 1 : regionX === 2 ? -1 : 0;
				playerY = regionY === 0 ? 1 : regionY === 2 ? -1 : 0;
			}
		}		

        function checkCollisions() {
			let iFinal = 0;
            walls.forEach(wall => {
                if(wall.passed || playerZ < wall.z-1) return;
                
				yOld++;
                wall.passed = true;
                if(wall.isFinal) {
                    if(wall.isTarget && Math.abs(playerX - wall.x) < 0.1 && Math.abs(playerY - wall.y) < 0.1) 
					{
                    } else 
					{
						iFinal++;
                    }
                } else {
                    if(Math.abs(playerX - wall.gapX) < 0.1 && 
                       Math.abs(playerY - wall.gapY) < 0.1) {
                        iFinal = 1;
                    } else {
                        handleCollision();
                    }
                }


            });
			if (iFinal>0)
			{
				if (iFinal == 1)
					score++;
				else if (iFinal == 9)
					handleCollision();
				else
				{
					score += 2;
					level++;
					walls.forEach(wall => scene.remove(wall.mesh));
					
					setTimeout(() => {
						generateLevel();
					}, 100);
					
				}
			}

        }

        function handleCollision() {
            lives--;
            if(lives <= 0) {
                gameActive = false;
                document.getElementById('gameOver').style.display = 'block';
                document.getElementById('finalScore').textContent = score;

				let sTopTen = "";
				topScores.forEach(o =>  {sTopTen += "<tr><td>" + o.score + "</td><td>" + o.name + "</td></tr>";});
				document.getElementById('topten').innerHTML = sTopTen;
            } else {
                generateLevel();
            }
        }

        function updateCamera() {
            camera.position.set(playerX, playerY, playerZ);
            camera.lookAt(playerX, playerY, playerZ + 5);
        }

        function updateObjects() {
            walls.forEach(wall => {
                const distance = wall.z - playerZ;
                const scale = wall.passed ? 0 : (wall.isTarget ? 0.3 : Math.min(0.9, 0.9 * (1 + 3 / Math.max(1, distance)))	);
				// 
                wall.mesh.scale.set(scale, scale, scale);
            });
        }

        function animate() {
            requestAnimationFrame(animate);
            
            if(gameActive) {
                playerZ += currentSpeed;
                updateCamera();
                updateObjects();
                checkCollisions();
				//alert(rLight.intensity);
				if (rLight.intensity < 1)	rLight.intensity += 0.005;
            }

            renderer.render(scene, camera);
            document.getElementById('hud').textContent = 
                `Score: ${score} | Lives: ${lives} | Level: ${level}`;
        }

        function saveScore() {
            const name = document.getElementById('playerName').value || 'Anonymous';
            topScores.push({ name, score });
            topScores.sort((a, b) => b.score - a.score);
            topScores = topScores.slice(0, 10);
            localStorage.setItem('topScores', JSON.stringify(topScores));
            
            gameActive = true;
            document.getElementById('gameOver').style.display = 'none';
            level = Math.max(0, level - 5);
            score = 0;
            lives = 3;
            generateLevel();
        }

        // Event listeners
        window.addEventListener('resize', () => {
            camera.aspect = window.innerWidth / window.innerHeight;
            camera.updateProjectionMatrix();
            renderer.setSize(window.innerWidth, window.innerHeight);
        });

        document.addEventListener('keydown', (e) => KeyDown(e.key));
		document.addEventListener('touchstart', handleTouch);
		document.addEventListener('touchmove', handleTouch);		

        // Update existing resize handler to maintain grid proportions
        window.addEventListener('resize', () => {
            camera.aspect = window.innerWidth / window.innerHeight;
            camera.updateProjectionMatrix();
            renderer.setSize(window.innerWidth, window.innerHeight);
        });

    </script>
</body>
</html>
```
