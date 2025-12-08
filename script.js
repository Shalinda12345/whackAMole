const audio = document.getElementById("mySound");
const buttons = document.querySelectorAll('.btn');
const muteButton = document.querySelector('.mute-btn');

let isMuted = false;

// Unlock audio policy
document.addEventListener("click", () => {
    audio.play().then(() => audio.pause());
}, { once: true });

// Hover sound
buttons.forEach(btn => {
  btn.addEventListener("pointerenter", () => {
    if (!isMuted) {
      audio.currentTime = 0;
      audio.play();
    }
  });

  btn.addEventListener("pointerleave", () => {
    audio.pause();
    audio.currentTime = 0;
  });
});

// Mute button toggle
muteButton.addEventListener("click", (e) => {
  e.preventDefault();

  isMuted = !isMuted;

  audio.pause();
  audio.currentTime = 0;

  muteButton.textContent = isMuted ? "Unmute 🔊" : "Mute 🔇";
});
