import React from 'react'
import PropTypes from 'prop-types'

const TextLogo = (props) => {
  const textLogoClasses = `logo ${props.className}`
  return (
    <span className={textLogoClasses}>
      <span className="logo-first">home</span><span className="logo-second">comb</span>
    </span>
  )
}

TextLogo.propTypes = {
  className: PropTypes.string
}

export default TextLogo
