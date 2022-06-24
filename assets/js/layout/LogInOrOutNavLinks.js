import React, { useState } from 'react'
import Login from '../modals/Login'
import PropTypes from 'prop-types'

const LogInOurOutNavLinks = (props) => {
  const [loginModalVisible, setLoginModalVisible] = useState(false)

  const hideLoginModal = () => {
    setLoginModalVisible(false)
  }

  const showLoginModal = () => {
    setLoginModalVisible(true)
  }

  return (
    <>
      {loginModalVisible &&
        <Login hideLoginModal={hideLoginModal} />
      }

      {props.user &&
        <li><a href="/logout" className={props.className}>Log Out</a></li>
      }
      {!props.user &&
        <>
          <li><a onClick={showLoginModal} className={props.className}>Log In</a></li>
          <li><a href="/register" className={props.className + ' register-link'}>Register</a></li>
        </>
      }
    </>
  )
}

LogInOurOutNavLinks.propTypes = {
  className: PropTypes.string,
  user: PropTypes.object
}

export default LogInOurOutNavLinks
