const cursor = document.querySelector('.cursor');
const holes = [...document.querySelectorAll('.hole')];
const scoreE1 = document.querySelector('.score span');
let score = 0;

let plantActive = false;
let moleActive = false;

const sound = new Audio("assets/smash.mp3");

function run() {
    setHole();
}
run();

function setHole() {
    // If both plant and mole are active, wait and retry
    if (plantActive || moleActive) {
        setTimeout(setHole, 500);
        return;
    }

    // Random 50% chance to spawn
    if (Math.random() < 0.5) {
        let availableHoles = holes.filter(h => !h.querySelector('img'));
        if (availableHoles.length === 0) {
            setTimeout(setHole, 500);
            return;
        }

        // Pick a random plant hole
        let plantHoleIndex = Math.floor(Math.random() * availableHoles.length);
        let plantHoleChosen = availableHoles[plantHoleIndex];
        setPlant(plantHoleChosen);

        // Pick a different mole hole
        let moleHoles = availableHoles.filter(h => h !== plantHoleChosen);
        if (moleHoles.length > 0) {
            let moleHoleChosen = moleHoles[Math.floor(Math.random() * moleHoles.length)];
            setMole(moleHoleChosen);
        }
    } else {
        // Try again after 1s
        setTimeout(setHole, 1000);
    }
}

function setMole(moleHole) {
    moleActive = true;

    const mole = document.createElement('img');
    mole.classList.add('mole');
    mole.src = 'assets/mole.png';
    moleHole.appendChild(mole);

    const moleTimer = setTimeout(() => {
        if (moleHole.contains(mole)) moleHole.removeChild(mole);
        moleActive = false;
        setHole(); // restart loop
    }, 1500);

    mole.addEventListener('click', () => {
        score += 10;
        scoreE1.textContent = score;
        sound.play();

        clearTimeout(moleTimer);
        if (moleHole.contains(mole)) moleHole.removeChild(mole);
        moleActive = false;
        setHole(); // restart loop
    });
}

function setPlant(plantHole) {
    plantActive = true;

    const plant = document.createElement('img');
    plant.classList.add('mole');
    plant.src = 'assets/plant.png';
    plantHole.appendChild(plant);

    const plantTimer = setTimeout(() => {
        if (plantHole.contains(plant)) plantHole.removeChild(plant);
        plantActive = false;
        setHole(); // restart loop
    }, 1500);

    plant.addEventListener('click', () => {
        score = Math.max(0, score - 5);
        scoreE1.textContent = score;
        apiCall()

        clearTimeout(plantTimer);
        if (plantHole.contains(plant)) plantHole.removeChild(plant);
        plantActive = false;
        setHole(); // restart loop
    });
}

function apiCall(){
    window.location.href = "https://marcconrad.com/uob/banana/index.php";  
    // Test 
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
