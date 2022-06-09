import React from 'react'

import { Button, Modal, ModalBody, ModalFooter, ModalHeader } from 'reactstrap'
import { HashLink as Link } from 'react-router-hash-link'
import Constants from '../Constants'
import PropTypes from 'prop-types'

const Login = (props) => {
  return (
    <Modal className="login-modal" isOpen={true} toggle={props.hideLoginModal}>
      <ModalHeader toggle={props.hideLoginModal}>Log in</ModalHeader>
      <ModalBody>
        <a href="/connect/google">
          <Button size="md" color="primary">
            Log in with Google
          </Button>
        </a>
      </ModalBody>
      <ModalFooter>
        <a href="/login">Log in with email</a>
        | <Link to="/register#" onClick={props.hideLoginModal}>Register with {Constants.SITE_NAME}</Link>
      </ModalFooter>
    </Modal>
  )
}

Login.propTypes = {
  hideLoginModal: PropTypes.func
}

export default Login
