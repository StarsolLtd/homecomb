import React, { useRef, useState } from 'react'
import { Button, Label } from 'reactstrap'
import { AvForm, AvGroup, AvInput, AvFeedback } from 'availity-reactstrap-validation'
import LoadingOverlay from 'react-loading-overlay'
import Loader from 'react-loaders'
import Constants from '../Constants'

const ContactForm = (props) => {
  const [emailAddress, setEmailAddress] = useState('')
  const [name, setName] = useState('')
  const [message, setMessage] = useState('')
  const [isFormSubmitting, setIsFormSubmitting] = useState(false)

  const contactForm = useRef(null)

  const handleChange = (event) => {
    const target = event.target
    const value = target.type === 'checkbox' ? target.checked : target.value

    switch (target.name) {
      case 'emailAddress':
        setEmailAddress(value)
        break
      case 'name':
        setName(value)
        break
      case 'message':
        setMessage(value)
        break
    }
  }

  const handleValidSubmit = (res) => {
    setIsFormSubmitting(true)

    const payload = {
      emailAddress,
      name,
      message,
      captchaToken: null
    }

    /* eslint-disable-next-line no-undef */
    grecaptcha.ready(function () {
      /* eslint-disable-next-line no-undef */
      grecaptcha.execute(Constants.GOOGLE_RECAPTCHA_SITE_KEY, { action: 'submit' }).then(function (captchaToken) {
        payload.captchaToken = captchaToken
        fetch('/api/contact', {
          method: 'POST',
          body: JSON.stringify(payload)
        })
          .then(
            response => {
              setIsFormSubmitting(false)

              if (!response.ok) {
                if (response.status === 500) {
                  props.addFlashMessage('error', 'Sorry, something went wrong with your request.')
                }
                props.fetchFlashMessages()
                /* eslint-disable-next-line prefer-promise-reject-errors */
                return Promise.reject('Error: ' + response.status)
              }
              return response.json()
            }
          )
          .then((data) => {
            props.fetchFlashMessages()
            contactForm.current.reset()
          })
          .catch(err => console.error('Error:', err))
      })
    })
  }

  return (
    <LoadingOverlay
      active={isFormSubmitting}
      styles={{
        overlay: (base) => ({
          ...base,
          background: '#fff',
          opacity: 0.5
        })
      }}
      spinner={<Loader active type='ball-triangle-path' />}
    >
      <AvForm id="contact-form" onValidSubmit={handleValidSubmit} ref={contactForm}>
        <AvGroup>
          <Label for="email">Your email</Label>
          <AvInput
            type="email"
            name="emailAddress"
            required
            onChange={handleChange}
            placeholder="Enter your email address"
            value={emailAddress}
          />
          <AvFeedback>Please enter a valid email address, example: jane.doe@gmail.com</AvFeedback>
        </AvGroup>
        <AvGroup>
          <Label for="name">Your name</Label>
          <AvInput
            type="text"
            name="name"
            required
            onChange={handleChange}
            placeholder="Enter your name"
            value={name}
          />
          <AvFeedback>Please enter your name.</AvFeedback>
        </AvGroup>
        <AvGroup>
          <Label for="message">Your message</Label>
          <AvInput
            type="textarea"
            name="message"
            required
            onChange={handleChange}
            placeholder="Enter your message"
            value={message}
          />
          <AvFeedback>Please enter your message.</AvFeedback>
        </AvGroup>
        <Button id="register-form-submit" color="primary" size="lg" className="mt-4">
          Contact us
        </Button>
      </AvForm>
    </LoadingOverlay>
  )
}

export default ContactForm
