/**
 * @copyright  Copyright (C) 2020 - JoomJunk
 * @license    MIT; see LICENSE.txt
 */

(() => {
  // Theme states
  const THEME = {
    dark: 'is-dark',
    light: 'is-light',
    system: 'is-system',
  }

  // Method to change the class on the <html> element
  const applyClass = (value) => {
    html.classList.remove(THEME.dark, THEME.light, THEME.system)
    html.classList.add(value)
  }

  // Variables
  const html = document.documentElement
  const matchMedia = window.matchMedia('(prefers-color-scheme:dark)')
  const switcher = document.getElementById('color-scheme-switch')

  if (switcher) {
    const colourScheme = matchMedia.matches ? THEME.dark : THEME.light
    const storedTheme = localStorage.getItem('theme') !== null ? localStorage.getItem('theme') : THEME.system

    // Set the default input to be checked
    if (storedTheme !== THEME.system) {
      switcher.querySelector(`input[value="${storedTheme.replace(/is-/g, '')}"]`).checked = true
      applyClass(storedTheme)
    } else {
      // Set default theme
      matchMedia.matches ? applyClass(THEME.dark) : applyClass(THEME.light)
    }

    // Loop through all switcher inputs
    switcher.querySelectorAll('input').forEach((input) => {
      input.addEventListener('change', ({ target }) => {
        if (target.value === 'dark') {
          localStorage.setItem('theme', THEME.dark)
          applyClass(THEME.dark)
        } else if (target.value === 'light') {
          localStorage.setItem('theme', THEME.light)
          applyClass(THEME.light)
        } else {
          applyClass(THEME.system)
          html.classList.add(colourScheme)
          localStorage.setItem('theme', THEME.system)
        }
      })
    })
  } else {
    // Set default theme
    matchMedia.matches ? applyClass(THEME.dark) : applyClass(THEME.light)
  }

  // Listen for changes made to the system
  matchMedia.addListener((event) => {
    // If the switcher is disabled or enabled BUT set to the system preference, change the theme
    // The theme will not change if it has manually been set by the switcher 
    if (!switcher || (switcher && switcher.querySelector('input[value="system"]').checked)) {
      return event.matches ? applyClass(THEME.dark) : applyClass(THEME.light)
    }
  })

})()
