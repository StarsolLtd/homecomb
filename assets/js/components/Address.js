import React from 'react'
import PropTypes from 'prop-types'
import { faHome } from '@fortawesome/free-solid-svg-icons'
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome'

const Address = (props) => {
  return (
    <div className="address">
      <a href={void (0)} onClick={() => props.handleClick(props.addressLine1)}>
        <FontAwesomeIcon icon={faHome}/>{' '}
        {[props.addressLine1, props.addressLine2, props.addressLine3, props.city, props.postcode].filter(function (el) {
          return el.length > 0
        }).join(', ')}
      </a>
    </div>
  )
}

Address.propTypes = {
  handleClick: PropTypes.func,
  addressLine1: PropTypes.string,
  addressLine2: PropTypes.string,
  addressLine3: PropTypes.string,
  city: PropTypes.string,
  postcode: PropTypes.string
}

export default Address
