import React, { Component } from 'react'

import { Button, Modal, ModalBody, ModalFooter, ModalHeader } from 'reactstrap'
import { HashLink as Link } from 'react-router-hash-link'
import Constants from '../Constants'

export default class Login extends Component {
  closeModal = () => {
    this.props.hideLoginModal()
  }

  render () {
    return (
      <Modal className="login-modal" isOpen={true} toggle={this.closeModal}>
        <ModalHeader toggle={this.closeModal}>Log in</ModalHeader>
        <ModalBody>
          <a href="/connect/google">
            <Button size="md" color="primary">
              Log in with Google
            </Button>
          </a>
        </ModalBody>
        <ModalFooter>
          <a href="/login">Log in with email</a>
          | <Link to="/register#" onClick={this.closeModal}>Register with {Constants.SITE_NAME}</Link>
        </ModalFooter>
      </Modal>
    )
  }
}
