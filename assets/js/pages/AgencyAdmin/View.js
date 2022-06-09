import React from 'react'
import { Container } from 'reactstrap'
import LoadingOverlay from 'react-loading-overlay'
import Loader from 'react-loaders'
import FlashMessages from '../../layout/FlashMessages'
import { Redirect } from 'react-router-dom'
import Constants from '../../Constants'

class View extends React.Component {
  constructor (props) {
    super(props)

    this.state = {
      isFormSubmitting: false,
      flashMessages: [],
      flashMessagesFetching: false,
      redirectToUrl: null
    }

    this.addFlashMessage = this.addFlashMessage.bind(this)
    this.fetchFlashMessages = this.fetchFlashMessages.bind(this)
    this.submit = this.submit.bind(this)
  }

  componentDidMount () {
    this.fetchFlashMessages()
  }

  render () {
    const Content = this.props.content

    return (
            <Container>
                <FlashMessages messages={this.state.flashMessages} />
                <LoadingOverlay
                    active={this.state.isFormSubmitting}
                    styles={{
                      overlay: (base) => ({
                        ...base,
                        background: '#fff',
                        opacity: 0.5
                      })
                    }}
                    spinner={<Loader active type='ball-triangle-path' />}
                >
                    <Content
                        addFlashMessage={this.addFlashMessage}
                        fetchFlashMessages={this.fetchFlashMessages}
                        submit={this.submit}
                        {...this.props}
                    />
                </LoadingOverlay>
                {this.state.redirectToUrl &&
                    <Redirect to={this.state.redirectToUrl} />
                }
            </Container>
    )
  }

  addFlashMessage (context, content) {
    this.setState({ flashMessages: [...this.state.flashMessages, { key: Date.now(), context, content }] })
  }

  redirectToUrl (url) {
    this.setState({ redirectToUrl: url })
  }

  submit (payload, url, method, successMessage, successRedirectUrl = '') {
    this.setState({ isFormSubmitting: true })

    const component = this
    /* eslint-disable-next-line no-undef */
    grecaptcha.ready(function () {
      /* eslint-disable-next-line no-undef */
      grecaptcha.execute(Constants.GOOGLE_RECAPTCHA_SITE_KEY, { action: 'submit' }).then(function (captchaToken) {
        payload.captchaToken = captchaToken
        fetch(url, { method, body: JSON.stringify(payload) })
          .then(
            response => {
              component.setState({ isFormSubmitting: false })
              if (!response.ok) {
                if (response.status === 500) {
                  component.addFlashMessage('error', 'Sorry, something went wrong with your request.')
                }
                component.fetchFlashMessages()
                /* eslint-disable-next-line prefer-promise-reject-errors */
                return Promise.reject('Error: ' + response.status)
              }
              return response.json()
            }
          )
          .then((data) => {
            component.fetchFlashMessages()
            if (successMessage) {
              component.addFlashMessage('success', successMessage)
            }
            if (successRedirectUrl) {
              component.redirectToUrl(successRedirectUrl)
            }
          })
          .catch(err => console.error('Error:', err))
      })
    })
  }

  fetchFlashMessages () {
    fetch('/api/session/flash')
      .then(
        response => {
          this.setState({ flashMessagesFetching: false })
          if (!response.ok) {
            /* eslint-disable-next-line prefer-promise-reject-errors */
            return Promise.reject('Error: ' + response.status)
          }
          return response.json()
        }
      )
      .then(data => {
        data.messages.forEach(message => this.addFlashMessage(message.type, message.message))
      })
  }
}

export default View
