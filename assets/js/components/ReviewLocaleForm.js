import React from 'react'
import { Button, FormGroup, FormText, Label } from 'reactstrap'
import { AvForm, AvGroup, AvInput, AvFeedback, AvCheckboxGroup, AvCheckbox } from 'availity-reactstrap-validation'
import Rating from 'react-rating'
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome'
import { faStar } from '@fortawesome/free-solid-svg-icons'
import Constants from '../Constants'
import LoadingOverlay from 'react-loading-overlay'
import Loader from 'react-loaders'

import 'react-datepicker/dist/react-datepicker.css'

class ReviewLocaleForm extends React.Component {
  state = {
    localeName: this.props.localeName,
    localeSlug: this.props.localeSlug,
    reviewerEmail: this.props.reviewerEmail,
    reviewerName: this.props.reviewerName,
    reviewTitle: '',
    reviewContent: '',
    overallStars: null,
    agreeTerms: false,
    isFormSubmitting: false,
    user: null
  }

  componentDidMount () {
    this.fetchUserData()
  }

  handleChange = (event) => {
    const target = event.target
    const value = target.type === 'checkbox' ? target.checked : target.value
    const name = target.name

    this.setState({
      [name]: value
    })
  }

  handleOverallStarsChange = (value) => {
    this.setState({ overallStars: value })
  }

  render () {
    return (
      <LoadingOverlay
        active={this.state.isFormSubmitting}
        styles={Constants.LOADING_OVERLAY_STYLE}
        spinner={<Loader active type='ball-triangle-path' />}
      >
        <AvForm id="review-locale-form" onValidSubmit={this.handleValidSubmit} ref={c => (this.form = c)}>
          <AvInput type="hidden" name="localeSlug" value={this.state.localeSlug}/>
          {this.state.user &&
            <AvGroup>
              <Label for="reviewerEmail">Your email/username</Label>
              <AvInput
                type="text"
                disabled
                name="reviewerEmail"
                value={this.state.user.username}
              />
              <FormText>
                We won&apos;t publish this, and will only use it if we need to contact you.
                If you would like to add this review as a different user, please <a href="/logout">log out</a>.
              </FormText>
            </AvGroup>
          }
          {!this.state.user &&
            <AvGroup>
              <Label for="reviewerEmail">Your email</Label>
              <AvInput
                type="email"
                name="reviewerEmail"
                required
                onChange={this.handleChange}
                placeholder="Enter your email"
                value={this.state.reviewerEmail}
              />
              <AvFeedback>Please enter a valid email address, example: jane.doe@gmail.com</AvFeedback>
              <FormText>
                We won&apos;t publish this, and will only use it if we need to contact you.
              </FormText>
            </AvGroup>
          }
          <AvGroup>
            <Label for="reviewerName">Your name</Label>
            <AvInput
              type="text"
              name="reviewerName"
              required
              onChange={this.handleChange}
              placeholder="Enter your name"
              value={this.state.reviewerName}
            />
            <AvFeedback>Please enter your name here. Remember we will publish this, so it doesn&apos;t have to be your full name if you don&apos;t want.</AvFeedback>
            <FormText>
              We will publish this. It doesn&apos;t have to be your full name if you don&apos;t want.
            </FormText>
          </AvGroup>
          <AvGroup>
            <Label for="reviewTitle">Review title</Label>
            <AvInput
              type="text"
              name="reviewTitle"
              required
              onChange={this.handleChange}
              placeholder="Enter review title"
            />
            <AvFeedback>Please enter a short title for your review.</AvFeedback>
            <FormText>
              A short sentence or two summarising how you feel about {this.state.localeName}.
            </FormText>
          </AvGroup>
          <AvGroup>
            <Label for="reviewContent">Your review</Label>
            <AvInput
              type="textarea"
              name="reviewContent"
              required
              onChange={this.handleChange}
              placeholder="Enter your review"
            />
            <AvFeedback>Please your review.</AvFeedback>
            <FormText>
              Tell us all about your experience with the {this.state.localeName} area.
            </FormText>
          </AvGroup>
          <hr />
          <FormGroup>
            <Label for="overallStars">Overall Rating</Label><br />
            <Rating
              name="overallStars"
              onChange={this.handleOverallStarsChange}
              initialRating={this.state.overallStars}
              emptySymbol={
                <span className="text-rating-unchecked">
                                <FontAwesomeIcon size="lg" icon={faStar} />
                            </span>
              }
              fullSymbol={
                <span className="text-rating">
                                <FontAwesomeIcon size="lg" icon={faStar} />
                            </span>
              }
            />
            <FormText>
              Rate your overall experience living in {this.state.localeName}.
            </FormText>
          </FormGroup>
          <hr />
          <AvCheckboxGroup name="agreeTermsGroup" inline required>
            <AvCheckbox name="agreeTerms" value="yes" />
            I agree to the {Constants.SITE_NAME} <a href="/terms" target="_blank">terms and conditions</a>.
            <AvFeedback>You must agree to the terms and conditions.</AvFeedback>
          </AvCheckboxGroup>
          <Button id="review-locale-form-submit" color="primary">
            Share your review
          </Button>
        </AvForm>
      </LoadingOverlay>
    )
  }

  handleValidSubmit = () => {
    this.setState({ isFormSubmitting: true })

    const payload = {
      localeSlug: this.state.localeSlug,
      reviewerName: this.state.reviewerName,
      reviewerEmail: this.state.reviewerEmail,
      reviewTitle: this.state.reviewTitle,
      reviewContent: this.state.reviewContent,
      overallStars: this.state.overallStars,
      captchaToken: null
    }

    const component = this
    /* eslint-disable-next-line no-undef */
    grecaptcha.ready(function () {
      /* eslint-disable-next-line no-undef */
      grecaptcha.execute(Constants.GOOGLE_RECAPTCHA_SITE_KEY, { action: 'submit' }).then(function (captchaToken) {
        payload.captchaToken = captchaToken
        fetch('/api/review/locale', {
          method: 'POST',
          body: JSON.stringify(payload)
        })
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
            component.clearForm()
            component.props.completedThankYou()
            component.props.fetchFlashMessages()
          })
          .catch(err => console.error('Error:', err))
      })
    })
  }

  fetchUserData = () => {
    fetch(
      '/api/user',
      {
        headers: {
          Accept: 'application/json',
          'Content-Type': 'application/json'
        }
      }
    )
      .then((response) => {
        if (!response.ok) throw new Error(response.status)
        else return response.json()
      })
      .then(data => {
        this.setState({
          user: data
        })
      })
      .catch(err => console.error('Error:', err))
  }

  clearForm = () => {
    this.form && this.form.reset()
  }
}

export default ReviewLocaleForm
