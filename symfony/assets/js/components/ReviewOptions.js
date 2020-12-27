import React, {Fragment} from 'react';

import {
    Button,
    Modal,
    ModalHeader,
    ModalBody,
    ModalFooter,
    UncontrolledButtonDropdown,
    DropdownToggle, DropdownMenu, DropdownItem, Input, InputGroup
} from "reactstrap";
import { AvForm, AvGroup, AvInput, AvFeedback } from 'availity-reactstrap-validation';
import Loader from "react-loaders";
import LoadingOverlay from "react-loading-overlay";

import Constants from "../Constants";

class ReviewOptions extends React.Component {

    constructor(props) {
        super(props);
        this.state = {
            reviewId: this.props.reviewId,
            flagReviewContent: '',
            flagReviewSubmitted: false,
            flagModal: false
        };

        this.handleChange = this.handleChange.bind(this);
        this.toggleFlagModal = this.toggleFlagModal.bind(this);
        this.handleFlagReviewSubmit = this.handleFlagReviewSubmit.bind(this);
    }

    toggleFlagModal() {
        this.setState({
            flagModal: !this.state.flagModal
        })
    }

    render() {
        return (
            <Fragment>
                <UncontrolledButtonDropdown className="mb-2 mr-2">
                    <DropdownToggle color="light">
                        &hellip;
                    </DropdownToggle>
                    <DropdownMenu>
                        <DropdownItem onClick={this.toggleFlagModal}>Report this</DropdownItem>
                    </DropdownMenu>
                </UncontrolledButtonDropdown>
                <Modal isOpen={this.state.flagModal} toggle={this.toggleFlagModal}>
                    <ModalHeader toggle={this.toggleFlagModal}>Report inappropriate content</ModalHeader>
                    <LoadingOverlay
                        active={this.state.flagReviewSubmitted}
                        styles={{
                            overlay: (base) => ({
                                ...base,
                                background: "#fff",
                                opacity: 0.5,
                            }),
                        }}
                        spinner={<Loader active type='ball-triangle-path' />}
                    >
                        <AvForm onValidSubmit={this.handleFlagReviewSubmit}>
                            <ModalBody>
                                <p>
                                    If this review is inappropriate, you can flag it to our moderation team.
                                    Please enter an explanation below:
                                </p>
                                <AvGroup>
                                    <AvInput
                                        type="text"
                                        placeholder="Enter an explanation"
                                        name="flagReviewContent"
                                        onChange={this.handleChange}
                                        required
                                    />
                                    <AvFeedback>Please enter an explanation.</AvFeedback>
                                </AvGroup>
                            </ModalBody>
                            <ModalFooter>
                                <Button color="primary">
                                    Send report
                                </Button>
                            </ModalFooter>
                        </AvForm>
                    </LoadingOverlay>
                </Modal>
            </Fragment>
        );
    }

    handleChange(event) {
        const target = event.target;
        const value = target.type === 'checkbox' ? target.checked : target.value;
        const name = target.name;

        this.setState({
            [name]: value
        });
    }

    handleFlagReviewSubmit() {
        this.setState({flagReviewSubmitted: true});
        let payload = {
            entityId: this.state.reviewId,
            entityName: 'Review',
            content: this.state.flagReviewContent,
            captchaToken: null,
        };
        grecaptcha.ready(function() {
            grecaptcha.execute(Constants.GOOGLE_RECAPTCHA_SITE_KEY, {action: 'submit'}).then(function(captchaToken) {
                payload.captchaToken = captchaToken;
                fetch(`/api/flag`, {
                    method: 'POST',
                    body: JSON.stringify(payload),
                    headers: {
                        'Content-Type': 'application/json'
                    }
                })
                    .then((response) => {
                        if (!response.ok) throw new Error(response.status);
                        else return response.json();
                    })
                    .then((data) => {
                        location.reload()
                    })
                    .catch(err => console.error("Error:", err));
            });
        });
    }
}

export default ReviewOptions;