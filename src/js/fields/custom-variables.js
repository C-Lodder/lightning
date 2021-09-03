/**
 * @copyright  Copyright (C) 2020 - JoomJunk
 * @license    MIT; see LICENSE.txt
 */

const saveCss = () => {
  const obj = {
    css: {
      default: {},
      dark: {},
    }
  }

  const overrides = Array.from(document.querySelectorAll('.css-override'))
  for (const override of overrides) {
    const value = override.value.trim()
    if (value === '') {
      continue;
    }
    const row = override.closest('tr')
    const colourScheme = row.getAttribute('data-colour-scheme')
    const variable = row.querySelector('.css-variable').innerText

    // Update the object
    obj.css[colourScheme][variable] = value
  }

  fetch('index.php?option=com_ajax&template=lightning&method=saveCss&format=json', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json'
    },
    body: JSON.stringify(obj),
  })
  .then(response => response.json())
  .then(response => { Joomla.renderMessages({ message: [response.data] })})
  .catch(error => Joomla.renderMessages({ error: [error] }))
}

document.querySelectorAll('[data=\"save-css\"]').forEach(button => {
  button.addEventListener('click', () => {
    saveCss()
  })
})
