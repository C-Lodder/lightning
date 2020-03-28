/**
 * @copyright  Copyright (C) 2020 - JoomJunk
 * @license    MIT; see LICENSE.txt
 */

(() => {

  const toggle = document.getElementById('navbar-menu-toggle')
  if (toggle) {
    toggle.addEventListener('click', () => {
      const toggle = document.getElementById('navbar-menu-toggle')
      body = document.body.classList.toggle('menu-open');
    })
  }

})()