import React from 'react'

import {
  Button,
  Modal,
  ModalHeader,
  ModalBody,
  ModalFooter,
  UncontrolledButtonDropdown,
  DropdownToggle, DropdownMenu, DropdownItem
} from 'reactstrap'
import { AvForm, AvGroup, AvInput, AvFeedback } from 'availity-reactstrap-validation'
import Loader from 'react-loaders'
import LoadingOverlay from 'react-loading-overlay'

import Constants from '../Constants'

export default class ReviewOptions extends React.Component {
  state = {
    reviewId: this.props.reviewId,
    flagReviewContent: '',
    flagReviewSubmitted: false,
    flagModal: false
  }

  toggleFlagModal = () => {
    this.setState({
      flagModal: !this.state.flagModal
    })
  }

  render () {
    return (
      <>
        <UncontrolledButtonDropdown className="mb-2 mr-2">
          <DropdownToggle color="light">
            &hellip;
          </DropdownToggle>
          <DropdownMenu>
            <DropdownItem onClick={this.toggleFlagModal} className="flag-review-link">Report this</DropdownItem>
          </DropdownMenu>
        </UncontrolledButtonDropdown>
        <Modal isOpen={this.state.flagModal} toggle={this.toggleFlagModal}>
          <ModalHeader toggle={this.toggleFlagModal}>Report inappropriate content</ModalHeader>
          <LoadingOverlay
            active={this.state.isFormSubmitting}
            styles={{
              overlay: (base) => ({
                ...base,
                background: '#fff',
                opacity: 0.5
              })
            }}
            spinner={<Loader active type='ball-triangle-path'/>}
          >
            <AvForm className="flag-review-form" onValidSubmit={this.handleFlagReviewSubmit}>
              <ModalBody>
                <p>
                  If this review is inappropriate, you can flag it to our moderation team.
                  Please enter an explanation below:
                </p>
                <AvGroup>
                  <AvInput
                    type="text"
                    placeholder="Enter an explanation"
                    name="flagReviewContent"
                    onChange={this.handleChange}
                    required
                  />
                  <AvFeedback>Please enter an explanation.</AvFeedback>
                </AvGroup>
              </ModalBody>
              <ModalFooter>
                <Button color="primary">
                  Send report
                </Button>
              </ModalFooter>
            </AvForm>
          </LoadingOverlay>
        </Modal>
      </>
    )
  }

  handleChange = (event) => {
    const target = event.target
    const value = target.type === 'checkbox' ? target.checked : target.value
    const name = target.name

    this.setState({
      [name]: value
    })
  }

  handleFlagReviewSubmit = () => {
    this.setState({ isFormSubmitting: true })
    const payload = {
      entityId: this.state.reviewId,
      entityName: 'Review',
      content: this.state.flagReviewContent,
      captchaToken: null
    }
    const component = this
    /* eslint-disable-next-line no-undef */
    grecaptcha.ready(function () {
      /* eslint-disable-next-line no-undef */
      grecaptcha.execute(Constants.GOOGLE_RECAPTCHA_SITE_KEY, { action: 'submit' }).then(function (captchaToken) {
        payload.captchaToken = captchaToken
        fetch('/api/flag', { method: 'POST', body: JSON.stringify(payload) })
          .then(
            response => {
              component.setState({ isFormSubmitting: false })
              if (!response.ok) {
                if (response.status === 500) {
                  component.props.addFlashMessage('error', 'Sorry, something went wrong with your request.')
                }
                component.props.fetchFlashMessages()
                /* eslint-disable-next-line prefer-promise-reject-errors */
                return Promise.reject('Error: ' + response.status)
              }
              return response.json()
            }
          )
          .then((data) => {
            component.toggleFlagModal()
            component.props.fetchFlashMessages()
          })
          .catch(err => console.error('Error:', err))
      })
    })
  }
}
