import React from 'react';

import {Button} from "reactstrap";
import { AvForm, AvGroup, AvInput, AvFeedback } from 'availity-reactstrap-validation';
import Constants from "../Constants";
import LoadingOverlay from "react-loading-overlay";
import Loader from "react-loaders";

import '../../styles/comment-form.scss';

export default class CommentForm extends React.Component {

    state = {
        content: '',
        isFormSubmitting: false,
    };

    handleChange = (event) => {
        const target = event.target;
        const value = target.type === 'checkbox' ? target.checked : target.value;
        const name = target.name;

        this.setState({
            [name]: value
        });
    }

    render() {
        return (
            <LoadingOverlay
                active={this.state.isFormSubmitting}
                styles={{
                    overlay: (base) => ({
                        ...base,
                        background: "#fff",
                        opacity: 0.5,
                    }),
                }}
                spinner={<Loader active type='ball-triangle-path' />}
            >
                <p>
                    Commenting as <span className="author">{this.props.user.firstName} {this.props.user.lastName}</span>:
                </p>
                <AvForm className="comment-form" onValidSubmit={this.handleValidSubmit} ref={c => (this.form = c)}>
                    <AvGroup>
                        <AvInput
                            type="textarea"
                            name="content"
                            value={this.state.content}
                            placeholder="Enter your comment"
                            required
                            onChange={this.handleChange}
                        />
                        <AvFeedback>Please enter your comment.</AvFeedback>
                    </AvGroup>
                    <Button className="comment-form-submit" color="primary">
                        Post comment
                    </Button>
                </AvForm>
            </LoadingOverlay>
        );
    }

    handleValidSubmit = () => {
        this.setState({isFormSubmitting: true});
        let payload = {
            entityName: this.props.entityName,
            entityId: parseInt(this.props.entityId),
            content: this.state.content,
            captchaToken: null
        };

        let component = this;
        grecaptcha.ready(function() {
            grecaptcha.execute(Constants.GOOGLE_RECAPTCHA_SITE_KEY, {action: 'submit'}).then(function(captchaToken) {
                payload.captchaToken = captchaToken;
                fetch('/api/comment', {
                    method: 'POST',
                    body: JSON.stringify(payload),
                })
                    .then(
                        response => {
                            component.setState({isFormSubmitting: false});
                            if (!response.ok) {
                                if (response.status === 500) {
                                    component.props.addFlashMessage('error', 'Sorry, something went wrong with your request.')
                                }
                                component.props.fetchFlashMessages();
                                return Promise.reject('Error: ' + response.status)
                            }
                            return response.json()
                        }
                    )
                    .then((data) => {
                        component.clearForm();
                        component.props.onSuccess();
                        component.props.fetchFlashMessages();
                    })
                    .catch(err => console.error("Error:", err));
            });
        });
    }

    clearForm = () => {
        this.form && this.form.reset();
    }
}
