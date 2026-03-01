// 404 Keypad Component
// Based on: https://codepen.io/jh3y/pen/vYwEYpv

const config = {
  theme: 'system',
  muted: false,
  exploded: false,
  one: {
    travel: 26,
    text: 'ok',
    key: 'o',
    hue: 114,
    saturation: 1.4,
    brightness: 1.2,
    buttonElement: null,
    textElement: null,
  },
  two: {
    travel: 26,
    text: 'go',
    key: 'g',
    hue: 0,
    saturation: 0,
    brightness: 1.4,
    buttonElement: null,
    textElement: null,
  },
  three: {
    travel: 18,
    text: 'create.',
    key: 'Enter',
    hue: 0,
    saturation: 0,
    brightness: 0.4,
    buttonElement: null,
    textElement: null,
  },
}

// Audio pour les clicks
const clickAudio = new Audio(
  'https://cdn.freesound.org/previews/378/378085_6260145-lq.mp3'
)
clickAudio.muted = config.muted

// Fonction pour initialiser les éléments
const initializeElements = () => {
  config.one.buttonElement = document.querySelector('#one')
  config.one.textElement = document.querySelector('#one .key__text')
  
  config.two.buttonElement = document.querySelector('#two')
  config.two.textElement = document.querySelector('#two .key__text')
  
  config.three.buttonElement = document.querySelector('#three')
  config.three.textElement = document.querySelector('#three .key__text')
}

// Fonction pour appliquer les styles des boutons
const applyButtonStyles = () => {
  const ids = ['one', 'two', 'three']
  
  for (const id of ids) {
    if (config[id].buttonElement) {
      config[id].buttonElement.style.setProperty('--travel', config[id].travel)
      config[id].buttonElement.style.setProperty('--saturate', config[id].saturation)
      config[id].buttonElement.style.setProperty('--hue', config[id].hue)
      config[id].buttonElement.style.setProperty('--brightness', config[id].brightness)
      
      // Ajouter l'événement de clic
      config[id].buttonElement.addEventListener('pointerdown', () => {
        if (!config.muted) {
          clickAudio.currentTime = 0
          clickAudio.play()
        }
      })
    }
  }
}

// Gestion des touches du clavier
window.addEventListener('keydown', (event) => {
  const ids = ['one', 'two', 'three']
  for (const id of ids) {
    if (event.key === config[id].key && config[id].buttonElement) {
      config[id].buttonElement.dataset.pressed = true
      if (!config.muted) {
        clickAudio.currentTime = 0
        clickAudio.play()
      }
    }
  }
})

window.addEventListener('keyup', (event) => {
  const ids = ['one', 'two', 'three']
  for (const id of ids) {
    if (event.key === config[id].key && config[id].buttonElement) {
      config[id].buttonElement.dataset.pressed = false
    }
  }
})

// Empêcher la soumission du formulaire
document.addEventListener('DOMContentLoaded', () => {
  initializeElements()
  applyButtonStyles()
  
  // Rendre le clavier visible
  const keypad = document.querySelector('.keypad')
  if (keypad) {
    keypad.style.setProperty('opacity', 1)
  }
  
  // Empêcher la soumission du formulaire
  const form = document.querySelector('.keypad-404-container form')
  if (form) {
    form.addEventListener('submit', (event) => {
      event.preventDefault()
    })
  }
})
