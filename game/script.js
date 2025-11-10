const cursor = document.querySelector('.cursor');
const holes = [...document.querySelectorAll('.hole')];
const scoreE1 = document.querySelector('.score span');
let score = 0;

let plantActive = false;
let moleActive = false;
let gamePaused = false;
let gameOver = false;

const sound = new Audio("assets/smash.mp3");

function run() {
    setHole();
}
run();

function setHole() {
    if (gamePaused || gameOver) return;

    if (plantActive || moleActive) {
        setTimeout(setHole, 500);
        return;
    }

    if (Math.random() < 0.5) {
        let availableHoles = holes.filter(h => !h.querySelector('img'));
        if (availableHoles.length === 0) {
            setTimeout(setHole, 500);
            return;
        }

        let plantHoleIndex = Math.floor(Math.random() * availableHoles.length);
        let plantHoleChosen = availableHoles[plantHoleIndex];
        setPlant(plantHoleChosen);

        let moleHoles = availableHoles.filter(h => h !== plantHoleChosen);
        if (moleHoles.length > 0) {
            let moleHoleChosen = moleHoles[Math.floor(Math.random() * moleHoles.length)];
            setMole(moleHoleChosen);
        }
    } else {
        setTimeout(setHole, 1000);
    }
}

function setMole(moleHole) {
    if (gamePaused || gameOver) return;

    moleActive = true;

    const mole = document.createElement('img');
    mole.classList.add('mole');
    mole.src = 'assets/mole.png';
    moleHole.appendChild(mole);

    const moleTimer = setTimeout(() => {
        if (moleHole.contains(mole)) moleHole.removeChild(mole);
        moleActive = false;
        setHole();
    }, 1500);

    mole.addEventListener('click', () => {
        if (gamePaused || gameOver) return;

        score += 10;
        scoreE1.textContent = score;
        sound.play();

        clearTimeout(moleTimer);
        if (moleHole.contains(mole)) moleHole.removeChild(mole);
        moleActive = false;
        setHole();
    });
}

function setPlant(plantHole) {
    if (gamePaused || gameOver) return;

    plantActive = true;

    const plant = document.createElement('img');
    plant.classList.add('mole');
    plant.src = 'assets/plant.png';
    plantHole.appendChild(plant);

    const plantTimer = setTimeout(() => {
        if (plantHole.contains(plant)) plantHole.removeChild(plant);
        plantActive = false;
        setHole();
    }, 1500);

    plant.addEventListener('click', () => {
        if (gamePaused || gameOver) return;

        clearTimeout(plantTimer);
        if (plantHole.contains(plant)) plantHole.removeChild(plant);
        plantActive = false;

        showBananaChallenge(); // 🔥 Trigger the Banana Puzzle
    });
}

// 🧠 Banana Challenge (with real verification)
function showBananaChallenge() {
    gamePaused = true;

    const overlay = document.createElement('div');
    overlay.id = 'banana-overlay';
    overlay.innerHTML = `
        <div class="banana-container">
            <h2>🍌 Banana Puzzle</h2>
            <img id="bananaImage" src="" alt="Banana Puzzle" />
            <input type="text" id="bananaAnswer" placeholder="Enter your answer..." />
            <button id="submitAnswer">Submit</button>
            <p id="bananaMessage"></p>
        </div>
    `;
    document.body.appendChild(overlay);

    const bananaImg = overlay.querySelector('#bananaImage');
    const msg = overlay.querySelector('#bananaMessage');
    const btn = overlay.querySelector('#submitAnswer');

    // Fetch puzzle image from Banana API
    fetch('https://marcconrad.com/uob/banana/api.php')
        .then(res => res.json())
        .then(data => {
            bananaImg.src = data.question;
            bananaImg.dataset.expected = data.solution; // store solution temporarily
        })
        .catch(err => {
            msg.textContent = "⚠️ Failed to load puzzle.";
            console.error(err);
        });

    btn.addEventListener('click', () => {
    const userAnswer = overlay.querySelector('#bananaAnswer').value.trim();
    const correctAnswer = bananaImg.dataset.expected;

    if (!userAnswer) {
        msg.textContent = "Please enter an answer.";
        return;
    }

    if (userAnswer === correctAnswer) {
        msg.textContent = "✅ Correct! Continue the game!";
        setTimeout(() => {
            overlay.remove();
            gamePaused = false;
            setHole();
        }, 1000);
    } else {
        msg.textContent = "❌ Wrong answer! Game Over!";
        gamePaused = true;
        gameOver = true;
        setTimeout(() => {
            overlay.remove();
            gameEnded(); // now username and score will display correctly
        }, 1000);
    }
});

}

function gameEnded() {
    gamePaused = true;
    gameOver = true;

    const existingOverlay = document.getElementById('banana-overlay');
    if (existingOverlay) existingOverlay.remove();

    const overlay = document.createElement('div');
    overlay.id = 'gameover-overlay';
    overlay.innerHTML = `
        <div class="gameover-container">
            <h2>💀 Game Over!</h2>
            <p id="playerInfo">Player: <strong>${window.username || 'Guest'}</strong></p>
            <p id="finalScore">Your Score: <strong>${score}</strong></p>
            <! -- <button id="exitButton">Exit to Menu</button> -->
            <!-- Form to submit score -->
            <form action="save_score.php" method="post">
                <input type="hidden" name="score" value="${score}">
                <button type="submit">Exit to Menu</button>
            </form>

        </div>
    `;
    document.body.appendChild(overlay);

    overlay.querySelector('#exitButton').addEventListener('click', () => {
        window.location.href = '../index.php';
    });
}




// Cursor (hammer) movement
window.addEventListener('mousemove', e => {
    cursor.style.top = e.pageY + 'px';
    cursor.style.left = e.pageX + 'px';
});
window.addEventListener('mousedown', () => {
    cursor.classList.add('active');
});
window.addEventListener('mouseup', () => {
    cursor.classList.remove('active');
});
