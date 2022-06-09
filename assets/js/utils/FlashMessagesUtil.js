export function addFlashMessage (context, content) {
  this.setState({ flashMessages: [...this.state.flashMessages, { key: Date.now(), context, content }] })
}

export function fetchFlashMessages (scrollTo = true) {
  fetch('/api/session/flash')
    .then(
      response => {
        this.setState({ flashMessagesFetching: false })
        if (!response.ok) {
          return Promise.reject('Error: ' + response.status)
        }
        return response.json()
      }
    )
    .then(data => {
      data.messages.forEach(message => this.addFlashMessage(message.type, message.message))
      if (scrollTo) {
        window.scrollTo({ top: 0, behavior: 'smooth' })
      }
    })
}
