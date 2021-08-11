import React from 'react';

import {Button, Modal, ModalBody, ModalFooter, ModalHeader} from "reactstrap";
import Constants from "../Constants";
import {Link} from "react-router-dom";

class LoginOrRegister extends React.Component {

    constructor(props) {
        super(props);
        this.state = {
            showModal: props.showModal
        };
        this.toggleModal = this.toggleModal.bind(this);
    }

    toggleModal() {
        this.setState({
            showModal: !this.state.showModal
        })
        this.props.hideLoginModal();
    }

    render() {
        return (
            <Modal className="login-modal" isOpen={this.state.showModal} toggle={this.toggleModal}>
                <ModalHeader toggle={this.toggleModal}>Please log in or register</ModalHeader>
                <ModalBody>
                    To use this functionality, you need to be logged in to {Constants.SITE_NAME}.
                </ModalBody>
                <ModalFooter>
                    <a href="/login"><Button color="primary" className="log-in-button">Log in</Button></a>
                    <Link to="/register"><Button color="primary" className="register-button">Register</Button></Link>
                    <Button color="secondary" className="close-modal-button" onClick={this.toggleModal}>Never mind</Button>
                </ModalFooter>
            </Modal>
        );
    }
}

export default LoginOrRegister;