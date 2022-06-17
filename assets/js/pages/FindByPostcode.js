import React, { useState } from 'react'

import { Button, InputGroup } from 'reactstrap'
import { AvForm, AvGroup, AvInput, AvFeedback } from 'availity-reactstrap-validation'
import Constants from '../Constants'
import Address from '../components/Address'
import LoadingSpinner from '../components/LoadingSpinner'

import '../../styles/find-by-postcode.scss'
import { Redirect } from 'react-router-dom'
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome'
import { faSearch } from '@fortawesome/free-solid-svg-icons'
import PropTypes from 'prop-types'

const FindByPostcode = (props) => {
  const [postcode, setPostcode] = useState('')
  const [properties, setProperties] = useState([])
  const [isPendingRedirect, setIsPendingRedirect] = useState(false)
  const [isLoading, setIsLoading] = useState(false)
  const [loaded, setLoaded] = useState(false)
  const [redirectToUrl, setRedirectToUrl] = useState(null)

  const handleChangePostcode = (event) => {
    setPostcode(event.target.value)
  }

  const handleAddressClick = (addressLine1) => {
    setIsPendingRedirect(true)

    fetch('/api/property/lookup-slug-from-address?addressLine1=' + addressLine1 + '&postcode=' + postcode)
      .then(response => response.json())
      .then(data => {
        setRedirectToUrl('/property/' + data.slug)
      })
  }

  const handleValidSubmit = () => {
    setIsLoading(true)
    const payload = {
      postcode,
      captchaToken: null
    }

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
              setIsLoading(false)
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
            setProperties(data.vendorProperties)
            setLoaded(true)
          })
          .catch(err => console.error('Error:', err))
      })
    })
  }

  if (redirectToUrl) {
    return (<Redirect to={redirectToUrl} />)
  }
  if (isPendingRedirect) {
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
        <AvForm className="find-by-postcode-form" onValidSubmit={handleValidSubmit}>
          <AvGroup>
            <InputGroup className="property-autocomplete-input-group">
              <AvInput
                type="text"
                name="postcode"
                required
                onChange={handleChangePostcode}
                placeholder="Enter a postcode"
                value={postcode}
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
      {isLoading &&
        <LoadingSpinner className="mt-3"/>
      }
      {!isLoading && loaded &&
        <div className="find-by-postcode-results bg-white rounded shadow-sm p-3 mb-4">
          <h3 className="mt-1">{properties.length} results found in {postcode.toUpperCase()}</h3>
          {properties.map(
            ({ addressLine1, addressLine2, addressLine3, city, postcode }) => (
              <Address
                key={addressLine1}
                addressLine1={addressLine1}
                addressLine2={addressLine2}
                addressLine3={addressLine3}
                city={city}
                postcode={postcode}
                handleClick={handleAddressClick}
              />
            )
          )}
        </div>
      }
    </div>
  )
}

FindByPostcode.propTypes = {
  addFlashMessage: PropTypes.func,
  fetchFlashMessages: PropTypes.func
}

export default FindByPostcode
