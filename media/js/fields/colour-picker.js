/**
 * @copyright  Copyright (C) 2020 - JoomJunk
 * @license    MIT; see LICENSE.txt
 */

import iro from './iro.min.js'

class ColourPicker extends HTMLElement {
  constructor() {
    super()

    this.swatch = ''
    this.input = ''
    this.panel = ''
  }

  connectedCallback() {
    this.classList.add('has-swatch')

    // Create swatch
    this.swatch = document.createElement('colour-swatch')

    // Create panel
    this.panel = document.createElement('div')
    this.panel.classList.add('panel')

    // Append the elements
    this.appendChild(this.swatch)
    this.appendChild(this.panel)

    this.init()
  }
  
  init() {
    this.input = this.querySelector('input')

    // Get the override value, else if it's empty, get the default value
    const defaultColour = this.input.value !== '' ? this.input.value : this.input.closest('tr').querySelector('.css-default').value

    const picker = new iro.ColorPicker(this.panel, {
      width: 150,
      layoutDirection: 'horizontal',
      color: defaultColour,
    })

    picker.on(['color:change'], (color) => {
      this.input.value = color.hslString
      this.swatch.style.backgroundColor = color.hslString
      this.input.dispatchEvent(new Event('input', { bubbles: true }))
    })

    picker.on(['color:init'], (color) => {
      this.swatch.style.backgroundColor = this.input.value
    })

    this.input.addEventListener('input', ({ target }) => {
      picker.color.hslString = target.value

      const closestRow = target.closest('tr')
      if ((target.value !== closestRow.querySelector('.css-default').value
        && target.value !== target.getAttribute('data-default-value'))
        || target.value.trim() !== ''
      ) {
        closestRow.classList.add('has-changed')
      } else {
        closestRow.classList.remove('has-changed')
      }
    })

    // If the swatch is clicked, trigger a focus on the input to open the panel
    this.swatch.addEventListener('click', () => {
      this.input.focus()
    })

    this.input.addEventListener('focus', () => {
      if (!this.panel.classList.contains('is-open')) {
        this.panel.classList.add('is-open')
      }
    })

    this.input.addEventListener('blur', () => {
      this.panel.classList.remove('is-open')
    })
  }
}

// Define the new element
customElements.define('colour-picker', ColourPicker)
