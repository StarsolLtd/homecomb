import React, { useState } from 'react'

import { Button, Modal, ModalBody, ModalFooter, ModalHeader } from 'reactstrap'
import Constants from '../Constants'
import { HashLink as Link } from 'react-router-hash-link'
import PropTypes from 'prop-types'

const LoginOrRegister = (props) => {
  const [modalVisible, setModalVisible] = useState(props.showModal)

  const toggleModal = () => {
    setModalVisible(!modalVisible)
    props.hideLoginModal()
  }

  return (
    <Modal className="login-modal" isOpen={modalVisible} toggle={toggleModal}>
      <ModalHeader toggle={toggleModal}>Please log in or register</ModalHeader>
      <ModalBody>
        To use this functionality, you need to be logged in to {Constants.SITE_NAME}.
      </ModalBody>
      <ModalFooter>
        <a href="/login"><Button color="primary" className="log-in-button">Log in</Button></a>
        <Link to="/register#"><Button color="primary" className="register-button">Register</Button></Link>
        <Button color="secondary" className="close-modal-button" onClick={toggleModal}>Never mind</Button>
      </ModalFooter>
    </Modal>
  )
}

LoginOrRegister.propTypes = {
  showModal: PropTypes.bool,
  hideLoginModal: PropTypes.func
}

export default LoginOrRegister
