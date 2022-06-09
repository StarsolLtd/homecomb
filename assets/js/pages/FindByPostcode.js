import React from 'react'

import { Button, InputGroup } from 'reactstrap'
import { AvForm, AvGroup, AvInput, AvFeedback } from 'availity-reactstrap-validation'
import Constants from '../Constants'
import Address from '../components/Address'
import LoadingSpinner from '../components/LoadingSpinner'

import '../../styles/find-by-postcode.scss'
import { Redirect } from 'react-router-dom'
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome'
import { faSearch } from '@fortawesome/free-solid-svg-icons'

export default class FindByPostcode extends React.Component {
  state = {
    postcode: '',
    properties: [],
    isPendingRedirect: false,
    isLoading: false,
    loaded: false,
    redirectToUrl: null
  }

  handleChangePostcode = (event) => {
    const target = event.target
    const value = target.value
    const name = target.name

    this.setState({
      [name]: value
    })
  }

  render () {
    if (this.state.redirectToUrl) {
      return (<Redirect to={this.state.redirectToUrl} />)
    }
    if (this.state.isPendingRedirect) {
      return (<LoadingSpinner className="loading-spinner-large mt-3"/>)
    }
    return (
      <div className="find-by-postcode">
        <div className="bg-white rounded shadow-sm p-4 mb-4">
          <h1>Find an address by postcode</h1>
          <p>
            To see all property addresses for a UK postcode,
            simply enter the full postcode in the search box below,
            then click <i>Search</i>.
          </p>
          <AvForm className="find-by-postcode-form" onValidSubmit={this.handleValidSubmit}>
            <AvGroup>
              <InputGroup className="property-autocomplete-input-group">
                <AvInput
                  type="text"
                  name="postcode"
                  required
                  onChange={this.handleChangePostcode}
                  placeholder="Enter a postcode"
                  value={this.state.postcode}
                  validate={{
                    pattern: { value: Constants.UK_POSTCODE_PATTERN }
                  }}
                />
                <span className="input-group-append">
                  <button className="btn border-left-0" type="submit">
                    <FontAwesomeIcon icon={faSearch} />
                  </button>
                </span>
                <AvFeedback>Please enter a full valid UK postcode.<br />Examples: <i>CB4 3LF</i> or <i>EC1V 2NX</i></AvFeedback>
              </InputGroup>
            </AvGroup>
            <Button id="find-by-postcode-submit" color="primary" size="lg" className="mt-1">
              Search <FontAwesomeIcon icon={faSearch} />
            </Button>
          </AvForm>
        </div>
        {this.state.isLoading &&
          <LoadingSpinner className="mt-3"/>
        }
        {!this.state.isLoading && this.state.loaded &&
          <div className="find-by-postcode-results bg-white rounded shadow-sm p-3 mb-4">
            <h3 className="mt-1">{this.state.properties.length} results found in {this.state.postcode.toUpperCase()}</h3>
            {this.state.properties.map(
              ({ addressLine1, addressLine2, addressLine3, city, postcode }) => (
                <Address
                  key={addressLine1}
                  addressLine1={addressLine1}
                  addressLine2={addressLine2}
                  addressLine3={addressLine3}
                  city={city}
                  postcode={postcode}
                  handleClick={this.handleAddressClick}
                />
              )
            )}
          </div>
        }
      </div>
    )
  }

  handleValidSubmit = () => {
    this.setState({ isLoading: true })
    const payload = {
      postcode: this.state.postcode,
      captchaToken: null
    }

    const component = this
    /* eslint-disable-next-line no-undef */
    grecaptcha.ready(function () {
      /* eslint-disable-next-line no-undef */
      grecaptcha.execute(Constants.GOOGLE_RECAPTCHA_SITE_KEY, { action: 'submit' }).then(function (captchaToken) {
        payload.captchaToken = captchaToken
        fetch('/api/postcode', {
          method: 'POST',
          body: JSON.stringify(payload)
        })
          .then(
            response => {
              component.setState({ isLoading: false })
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
            component.props.fetchFlashMessages()
            component.setState({
              properties: data.vendorProperties,
              loaded: true
            })
          })
          .catch(err => console.error('Error:', err))
      })
    })
  }

  handleAddressClick = (addressLine1) => {
    this.setState({ isPendingRedirect: true })

    fetch('/api/property/lookup-slug-from-address?addressLine1=' + addressLine1 + '&postcode=' + this.state.postcode)
      .then(response => response.json())
      .then(data => {
        this.setState({ redirectToUrl: '/property/' + data.slug })
      })
  }
}
