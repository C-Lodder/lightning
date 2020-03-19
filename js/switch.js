/**
 * @copyright  Copyright (C) 2020 - JoomJunk
 * @license    MIT; see LICENSE.txt
 */

(() => {

  // Switcher toggle
  const switcher = document.getElementById('color-scheme-switch')
  if (switcher) {
    const html = document.documentElement
    const theme = localStorage.getItem('theme') ?? 'is-light'

    switcher.checked = theme === 'is-dark' ? true : false

    switcher.addEventListener('change', () => {
      if (switcher.checked) {
        localStorage.setItem('theme', 'is-dark')
        html.classList.add('is-dark')
      } else {
        localStorage.setItem('theme', 'is-light')
        html.classList.remove('is-dark')
      }
    })
  }

})()
