// alert("Game Started");

const cursor = document.querySelector('.cursor')
const holes = [...document.querySelectorAll('.hole')]
const scoreE1 = document.querySelector('.score span')
let score = 0
let hole = prepareHoles()

const sound = new Audio("assets/smash.mp3")

function run(){

    let timer = null

    setMole()

}
run()

function prepareHoles(){
    const i = Math.floor(Math.random() * holes.length)
    const hole = holes[i]
    return hole
}

function setMole(){
    hole = prepareHoles()
    const mole = document.createElement('img')
    mole.classList.add('mole')
    mole.src = 'assets/mole.png'

    hole.appendChild(mole)
    moleFadeTimer(mole)
    moleClickTimer(mole)
}

function moleFadeTimer(mole){
    timer = setTimeout(() => {
        hole.removeChild(mole)
        run()
    }, 1500)
}

function moleClickTimer(mole){
    mole.addEventListener('click', () => {
        score += 10
        sound.play()
        scoreE1.textContent = score
        mole.src = 'assets/mole-whacked.png'
        clearTimeout(timer)
        setTimeout(() => {
            hole.removeChild(mole)
            run()
        }, 500)
    })
}

window.addEventListener('mousemove', e => {
    cursor.style.top = e.pageY + 'px'
    cursor.style.left = e.pageX + 'px'
})
window.addEventListener('mousedown', () => {
    cursor.classList.add('active') // Adding CSS class animation to the hammer
})
window.addEventListener('mouseup', () => {
    cursor.classList.remove('active')
})
