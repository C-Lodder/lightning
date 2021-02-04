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

  document.querySelectorAll('.has-changed').forEach(row => {
    const colourScheme = row.getAttribute('data-colour-scheme')
    const variable = row.querySelector('.css-variable').innerText
    const value = row.querySelector('.css-override').value
    if (value.trim() !== '') {
      obj.css[colourScheme][variable] = value
    }
  })

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
